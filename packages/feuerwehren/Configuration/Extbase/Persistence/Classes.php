<?php
declare(strict_types=1);

use Schmid\Feuerwehren\Domain\Model\FrontendUser;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'username' => ['fieldName' => 'username'],
            'name'     => ['fieldName' => 'name'],
            'first_name'     => ['fieldName' => 'first_name'],
            'last_name'     => ['fieldName' => 'last_name'],
            'email'    => ['fieldName' => 'email'],
        ],
    ],
];
