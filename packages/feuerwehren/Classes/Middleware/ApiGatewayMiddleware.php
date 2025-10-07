<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schmid\Feuerwehren\Service\Api\OverlaysService;
use Schmid\Feuerwehren\Service\Api\SearchService;
use Schmid\Feuerwehren\Service\Api\SettingsReader;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\JsonResponse;

final class ApiGatewayMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SearchService   $search,
        private readonly OverlaysService $overlays,
        private readonly SettingsReader  $settings,
        private readonly CacheManager    $cacheManager,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // KORREKTUR: Pfad vom Query String trennen für robustes Matching
        $path = explode('?', $request->getUri()->getPath(), 2)[0];

        // Nur unsere API übernehmen
        if (!str_starts_with($path, '/api/feuerwehren')) {
            return $handler->handle($request);
        }

        // --- CORS / Preflight ---
        $origin = $request->getHeaderLine('Origin');
        $allowed = $this->allowedOrigin($request, $origin);

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $this->withCors(new JsonResponse(null, 204), $allowed);
        }

        // --- Rate Limiting (60 req / 60s je IP + Pfad) ---
        if ($resp = $this->rateLimit($request, $path)) {
            return $this->withCors($resp, $allowed);
        }

        // --- Dispatch ---
        $response = match (true) {
            $path === '/api/feuerwehren/search'   && $request->getMethod() === 'GET' => $this->handleSearch($request),
            $path === '/api/feuerwehren/overlays' && $request->getMethod() === 'GET' => $this->handleOverlays($request),
            default => new JsonResponse(['error' => 'not_found'], 404),
        };

        // Caching-Hinweis für FE-Karten; bei Bedarf ETag/Last-Modified ergänzen
        if ($response->getStatusCode() === 200) {
            $response = $response->withHeader('Cache-Control', 'public, max-age=300');
        }

        return $this->withCors($response, $allowed);
    }

    private function handleSearch(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->search->search($request->getQueryParams());
        return new JsonResponse($data, 200);
    }

    private function handleOverlays(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->overlays->overlays();
        return new JsonResponse($data, 200);
    }

    /** Einfache Origin-Whitelist aus Site-Settings (settings.feuerwehren.api.allowedOrigins: [https://…]) */
    private function allowedOrigin(ServerRequestInterface $request, string $origin): string
    {
        /** @var array<int,string>|null $list */
        $list = $this->settings->get($request, 'feuerwehren.api.allowedOrigins', null);
        if (is_array($list) && $origin !== '' && in_array($origin, $list, true)) {
            return $origin;
        }
        // Dev-Fallback: gleiche Origin spiegeln, sonst '*'
        return $origin !== '' ? $origin : '*';
    }

    private function withCors(Psr7Response $response, string $origin): Psr7Response
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Vary', 'Origin')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '600');
    }

    private function rateLimit(ServerRequestInterface $request, string $path): ?ResponseInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rl:' . md5($ip . '|' . $path);

        $cache = $this->cacheManager->getCache('feuerwehren_rate');
        $entry = $cache->get($key);
        $now = time();
        $window = 60;
        $limit  = 60;

        if (!$entry) {
            $cache->set($key, ['count' => 1, 'start' => $now], [], $window);
            return null;
        }

        if ($now - ($entry['start'] ?? 0) >= $window) {
            $cache->set($key, ['count' => 1, 'start' => $now], [], $window);
            return null;
        }

        $count = (int) (($entry['count'] ?? 0) + 1);
        $remaining = max(1, $window - ($now - (int)($entry['start'] ?? $now)));
        $cache->set($key, ['count' => $count, 'start' => $entry['start']], [], $remaining);

        if ($count > $limit) {
            return (new JsonResponse(['error' => 'rate_limited'], 429))
                ->withHeader('Retry-After', (string) $remaining);
        }
        return null;
    }
}