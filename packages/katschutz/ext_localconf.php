<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Schmid\Katschutz\Controller\ArtikelController;
use Schmid\Katschutz\Controller\BestellungController;
use Schmid\Katschutz\Controller\BestellverwaltungController;
use Schmid\Katschutz\Controller\BuchungController;
use Schmid\Katschutz\Controller\DashboardController;
use Schmid\Katschutz\Controller\EinzelstueckController;
use Schmid\Katschutz\Controller\KatalogController;
use Schmid\Katschutz\Controller\KategorieController;
use Schmid\Katschutz\Controller\LageController;
use Schmid\Katschutz\Controller\LagerortController;
use Schmid\Katschutz\Controller\ModulController;
use Schmid\Katschutz\Controller\PruefungController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Alle Actions sind personen- bzw. bestandsbezogen → komplett nicht cachebar.
$bestellungActions = [
    KatalogController::class => 'list,show',
    BestellungController::class => 'new,create,list,show,cancel,lieferschein',
];
ExtensionUtility::configurePlugin('Katschutz', 'Bestellung', $bestellungActions, $bestellungActions);

$verwaltungActions = [
    DashboardController::class => 'index',
    BestellverwaltungController::class => 'list,new,create,show,approve,reject,ausgabe,ausgabeBuchen,ruecknahme,ruecknahmeBuchen,umbuchung,umbuchungBuchen,close,lieferschein',
    ArtikelController::class => 'list,show,new,create,edit,update,archive',
    EinzelstueckController::class => 'new,create,edit,update,archive',
    KategorieController::class => 'list,new,create,edit,update,archive',
    LagerortController::class => 'list,new,create,edit,update,archive',
    ModulController::class => 'list,new,create,edit,update,archive',
    BuchungController::class => 'list,new,create',
    PruefungController::class => 'list,new,create',
    LageController::class => 'list,show,new,create,end',
];
ExtensionUtility::configurePlugin('Katschutz', 'Verwaltung', $verwaltungActions, $verwaltungActions);

// Fluid-Mailtemplates (FluidEmail::setTemplate('KatschutzNeueBestellung') …)
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][1760439] = 'EXT:katschutz/Resources/Private/Templates/Email/';
