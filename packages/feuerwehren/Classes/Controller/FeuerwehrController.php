<?php

declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Feuerwehren\Domain\Repository\FahrzeugkategorieRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for the fire department map plugin.
 *
 * Renders the interactive map view with vehicle category filters.
 */
final class FeuerwehrController extends ActionController
{
    public function __construct(
        private readonly FahrzeugkategorieRepository $fahrzeugkategorieRepository,
    ) {}

    /**
     * Displays the fire department map with filter options.
     */
    public function listAction(): ResponseInterface
    {
        $kategorien = $this->fahrzeugkategorieRepository->findAll();

        $mapConfig = [
            'vtUrlTemplate' => (string) ($this->settings['vtUrlTemplate'] ?? '/_vt/{z}/{x}/{y}.pbf'),
            'apiSearchUrl' => (string) ($this->settings['apiSearchUrl'] ?? '/api/feuerwehren/search'),
            'overlaysUrl' => (string) ($this->settings['overlaysUrl'] ?? '/api/feuerwehren/overlays'),
        ];

        $this->view->assignMultiple([
            'fahrzeugkategorien' => $kategorien,
            'mapConfig' => $mapConfig,
            'compact' => (bool) ($this->settings['compact'] ?? false),
        ]);

        return $this->htmlResponse();
    }

    /**
     * Displays details for a single fire department (address, founding year,
     * vehicle categories, jubilees). Only real, DB-backed fields are shown —
     * no fabricated membership counts, statistics, contact details or gallery,
     * since the Feuerwehr model does not carry that data yet.
     */
    public function showAction(Feuerwehr $feuerwehr): ResponseInterface
    {
        $this->view->assign('feuerwehr', $feuerwehr);

        return $this->htmlResponse();
    }
}
