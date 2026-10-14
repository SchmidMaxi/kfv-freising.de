<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Repository\ArtikelRepository;
use Schmid\Katschutz\Domain\Repository\KategorieRepository;
use Schmid\Katschutz\Domain\Repository\ModulRepository;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\UebersichtService;
use Schmid\Katschutz\Service\ZugriffService;

final class KatalogController extends AbstractKatschutzController
{
    public function __construct(
        ZugriffService $zugriff,
        LageService $lageService,
        private readonly ArtikelRepository $artikelRepository,
        private readonly KategorieRepository $kategorieRepository,
        private readonly ModulRepository $modulRepository,
        private readonly UebersichtService $uebersicht,
    ) {
        parent::__construct($zugriff, $lageService);
    }

    protected function hatZugriff(): bool
    {
        return $this->zugriff->darfBestellen($this->katschutz);
    }

    public function listAction(int $kategorie = 0, string $suche = ''): ResponseInterface
    {
        $aktiveKategorie = $kategorie > 0 ? $this->kategorieRepository->findByUid($kategorie) : null;
        $this->view->assignMultiple([
            'gruppen' => $this->uebersicht->zeilenNachKategorie(
                $this->artikelRepository->findeFuerKatalog($aktiveKategorie, $suche),
            ),
            'kategorien' => $this->kategorieRepository->findAll(),
            'module' => $this->modulRepository->findAll(),
            'kategorie' => $kategorie,
            'suche' => $suche,
        ]);
        return $this->htmlResponse();
    }

    public function showAction(Artikel $artikel): ResponseInterface
    {
        $this->view->assign('zeile', $this->uebersicht->zeilen([$artikel])[0]);
        return $this->htmlResponse();
    }
}
