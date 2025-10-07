<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Feuerwehren\Domain\Model\Feuerwehr;
use Schmid\Feuerwehren\Domain\Model\Jubilaeum;
use Schmid\Feuerwehren\Domain\Repository\FeuerwehrRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class JubilaeumController extends ActionController
{
    public function __construct(
        protected readonly FeuerwehrRepository $feuerwehrRepository
    ) {}

    public function listAction(): ResponseInterface
    {
        $feuerwehren = $this->feuerwehrRepository->findAll();
        $alleJubilaeumsTitel = [];
        $feuerwehrenData = [];

        foreach ($feuerwehren as $feuerwehr) {
            $jubilaeenProFeuerwehr = [];
            foreach ($feuerwehr->getJubilaeen() as $jubilaeum) {
                if (!in_array($jubilaeum->getTitel(), $alleJubilaeumsTitel)) {
                    $alleJubilaeumsTitel[] = $jubilaeum->getTitel();
                }
                $jubilaeenProFeuerwehr[$jubilaeum->getTitel()] = $jubilaeum;
            }
            $feuerwehrenData[] = [
                'feuerwehr' => $feuerwehr,
                'jubilaeen' => $jubilaeenProFeuerwehr
            ];
        }
        sort($alleJubilaeumsTitel);

        // NEU: Logik zum Filtern basierend auf dem Feier-Datum
        $showPast = (bool)($this->settings['showPastJubilaeen'] ?? false);
        if (!$showPast) {
            $today = new \DateTime('today');
            $titelZuFiltern = [];

            foreach ($alleJubilaeumsTitel as $titel) {
                $isPastColumn = true;
                foreach ($feuerwehrenData as $data) {
                    if (isset($data['jubilaeen'][$titel])) {
                        /** @var Jubilaeum $jubilaeum */
                        $jubilaeum = $data['jubilaeen'][$titel];
                        // Prüfen, ob das Feierdatum in der Zukunft liegt
                        if ($jubilaeum->getDatum() && $jubilaeum->getDatum() >= $today) {
                            $isPastColumn = false; // Ein Datum in dieser Spalte ist noch nicht vorbei
                            break;
                        }
                    }
                }
                if ($isPastColumn) {
                    $titelZuFiltern[] = $titel;
                }
            }
            $alleJubilaeumsTitel = array_diff($alleJubilaeumsTitel, $titelZuFiltern);
        }

        $this->view->assignMultiple([
            'feuerwehrenData' => $feuerwehrenData,
            'jubilaeumsTitel' => $alleJubilaeumsTitel // Umbenannt für Klarheit
        ]);

        return $this->htmlResponse();
    }
}