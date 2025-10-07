<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Api;

use Nng\Nnrestapi\Api\AbstractApi;
use Nng\Nnrestapi\Annotations as Api;

/**
 * @Api\Endpoint()
 */
final class Health extends AbstractApi
{
    /**
     * GET /api/health
     * @Api\Access("public")
     */
    public function getIndexAction(): array
    {
        return ['ok'=>true, 'time'=>time()];
    }
}
