<?php
// EXT:feuerwehren/Classes/Controller/Api/SearchController.php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Schmid\Feuerwehren\Service\Api\SearchService;
use Schmid\Feuerwehren\Service\Api\SettingsReader;
use TYPO3\CMS\Core\Http\JsonResponse;

final class SearchController
{
    public function __construct(
        private readonly SearchService $search,
        private readonly SettingsReader $settings
    ) {}

    public function ping(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['ok' => true, 'time' => time()], 200);
    }

    public function search(ServerRequestInterface $request): ResponseInterface
    {
        $q = $request->getQueryParams();

        // Beispiel: Settings nutzen (wenn nötig)
        $tileSource = $this->settings->get($request, 'feuerwehren.tileSource', 'mbtiles');

        $data = $this->search->search($q);
        $res  = new JsonResponse([
            'items' => $data['items'],
            'meta'  => array_merge($data['meta'], ['tileSource' => $tileSource]),
        ], 200);

        return $res->withHeader('Cache-Control', 'public, max-age=300');
    }
}
