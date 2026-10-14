<?php

declare(strict_types=1);

use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Model\Buchung;
use Schmid\Katschutz\Domain\Model\FrontendUser;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    Bestellung::class => [
        'properties' => [
            'crdate' => ['fieldName' => 'crdate'],
        ],
    ],
    Buchung::class => [
        'properties' => [
            'crdate' => ['fieldName' => 'crdate'],
        ],
    ],
];
