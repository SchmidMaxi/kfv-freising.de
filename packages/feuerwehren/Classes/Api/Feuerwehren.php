<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Api;

use Nng\Nnrestapi\Api\AbstractApi;
use Nng\Nnrestapi\Annotations as Api;
use Schmid\Feuerwehren\Service\Api\SearchService;
use Schmid\Feuerwehren\Service\Api\OverlaysService;

/**
 * @Api\Endpoint()
 */
final class Feuerwehren extends AbstractApi
{
    public function __construct(
        private readonly SearchService $search,
        private readonly OverlaysService $overlays
    ) {}

    /**
     * GET /api/feuerwehren/search
     * @Api\Access("public")
     */
    public function getSearchAction(): array
    {
        $q = $this->request->getArguments();
        return $this->search->search($q);
    }

    /**
     * GET /api/feuerwehren/overlays
     * @Api\Access("public")
     */
    public function getOverlaysAction(): array
    {
        return $this->overlays->overlays();
    }
}
