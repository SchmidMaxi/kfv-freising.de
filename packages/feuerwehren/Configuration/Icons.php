<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-feuerwehren-extension' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/Extension.svg',
    ],
    'ext-feuerwehren-plugin-karte' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/feuerwehr.svg',
    ],
    'ext-feuerwehren-plugin-organigramm' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/area.svg',
    ],
    'ext-feuerwehren-plugin-jubilaeen' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/jubilaeum.svg',
    ],
    'tx-feuerwehren-area' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/area.svg',
    ],
    'tx-feuerwehren-fahrzeugkategorie' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/fahrzeug.svg',
    ],
    'tx-feuerwehren-feuerwehr' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/feuerwehr.svg',
    ],
    'tx-feuerwehren-gemeinde' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/gemeinde.svg',
    ],
    'tx-feuerwehren-jubilaeum' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/jubilaeum.svg',
    ],
    'tx-feuerwehren-person' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:feuerwehren/Resources/Public/Icons/person.svg',
    ],
];
