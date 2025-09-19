<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;

final class VectorTileProxyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath() ?? '';
        if (!\str_starts_with($path, '/_vt/')) {
            return $handler->handle($request);
        }

        $parts = explode('/', trim($path, '/'));
        if (count($parts) !== 4 || $parts[0] !== '_vt' || !preg_match('~^(\d+)\.(pbf)$~', $parts[3], $m)) {
            return $this->notFound();
        }

        $z = $parts[1]; $x = $parts[2]; $y = $m[1];

        if (!ctype_digit($z) || !ctype_digit($x) || !ctype_digit($y)) {
            return $this->notFound();
        }

        $conf    = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['feuerwehren']['vtProxy'] ?? [];
        $mode    = (string)($conf['mode'] ?? 'file');               // 'file' | 'http'
        $storage = (string)($conf['storage'] ?? 'var');             // 'var' | 'public'
        $fileBase= (string)($conf['fileBase'] ?? 'tiles');          // Unterordner
        $baseUrl = (string)($conf['baseUrl'] ?? '');

        try {
            if ($mode === 'file') {
                $root = $storage === 'public'
                    ? Environment::getPublicPath()
                    : Environment::getVarPath();

                $abs = $root . '/' . trim($fileBase, '/') . '/' . $z . '/' . $x . '/' . $y . '.pbf';
                if (!is_file($abs)) {
                    return $this->notFound();
                }
                $stream = new Stream($abs, 'r');

                return (new Response())
                    ->withStatus(200)
                    ->withHeader('Content-Type', 'application/x-protobuf')
                    ->withHeader('Content-Encoding', 'gzip') // üblich bei MVT
                    ->withHeader('Cache-Control', 'public, max-age=604800, s-maxage=604800')
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withBody($stream);
            }

            // Fallback: HTTP Proxy
            if ($baseUrl === '') {
                return $this->notFound();
            }
            $url = strtr($baseUrl, ['{z}' => $z, '{x}' => $x, '{y}' => $y]);

            /** @var RequestFactory $rf */
            $rf = GeneralUtility::makeInstance(RequestFactory::class);
            $up = $rf->request($url, 'GET', [
                'headers' => [
                    'Accept' => 'application/x-protobuf, */*',
                ],
                'http_errors' => false,
                'timeout' => 10,
                'verify' => false,
            ]);

            if ($up->getStatusCode() !== 200) {
                return $this->notFound();
            }

            $bodyStream = new Stream('php://temp', 'w+');
            $bodyStream->write((string)$up->getBody());
            $bodyStream->rewind();

            $resp = (new Response())
                ->withStatus(200)
                ->withHeader('Content-Type', $up->getHeaderLine('Content-Type') ?: 'application/x-protobuf')
                ->withHeader('Cache-Control', $up->getHeaderLine('Cache-Control') ?: 'public, max-age=86400, s-maxage=86400')
                ->withHeader('Access-Control-Allow-Origin', '*');

            $enc = $up->getHeaderLine('Content-Encoding');
            if ($enc) {
                $resp = $resp->withHeader('Content-Encoding', $enc);
            } else {
                $resp = $resp->withHeader('Content-Encoding', 'gzip');
            }

            return $resp->withBody($bodyStream);

        } catch (\Throwable $e) {
            return $this->notFound();
        }
    }

    private function notFound(): ResponseInterface
    {
        return (new Response())
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withBody($this->s('Not Found'));
    }

    private function s(string $c): Stream
    {
        $s = new Stream('php://temp', 'w+');
        $s->write($c);
        $s->rewind();
        return $s;
    }
}
