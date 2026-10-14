<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-katschutz-extension' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:katschutz/Resources/Public/Icons/Extension.svg',
    ],
    'ext-katschutz-plugin-bestellung' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:katschutz/Resources/Public/Icons/inventar.svg',
    ],
    'ext-katschutz-plugin-verwaltung' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:katschutz/Resources/Public/Icons/dashboard.svg',
    ],
    'tx-katschutz-record' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:katschutz/Resources/Public/Icons/inventar.svg',
    ],
];
