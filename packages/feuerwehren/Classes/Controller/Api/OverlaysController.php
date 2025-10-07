<?php
// EXT:feuerwehren/Classes/Controller/Api/OverlaysController.php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Schmid\Feuerwehren\Service\Api\OverlaysService;
use TYPO3\CMS\Core\Http\JsonResponse;

final class OverlaysController
{
    public function __construct(private readonly OverlaysService $overlays) {}

    public function overlays(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->overlays->overlays();
        return (new JsonResponse($data, 200))
            ->withHeader('Cache-Control', 'public, max-age=300');
    }
}
