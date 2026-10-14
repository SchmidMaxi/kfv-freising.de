<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Schmid\Katschutz\Domain\Enum\Buchungstyp;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Schmid\Katschutz\Domain\Repository\BuchungRepository;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

/**
 * Lieferschein (Ausgabe) bzw. Rücknahmebeleg als PDF zum Ausdrucken und Unterschreiben.
 */
final readonly class LieferscheinService
{
    private const EMBLEM = 'EXT:sitepackage/Resources/Public/Images/Emblem_KFV-Freising.png';
    private const CSS = 'EXT:katschutz/Resources/Private/Templates/Pdf/beleg.css';

    public function __construct(
        private ViewFactoryInterface $viewFactory,
        private BuchungRepository $buchungRepository,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    /**
     * @param 'lieferschein'|'ruecknahme' $art
     */
    public function erzeugeHtml(Bestellung $bestellung, string $art, ServerRequestInterface $request): string
    {
        $typen = $art === 'ruecknahme'
            ? [Buchungstyp::Ruecknahme, Buchungstyp::Verlust]
            : [Buchungstyp::Ausgabe];
        $buchungen = [];
        foreach ($this->buchungRepository->findByBestellung($bestellung) as $buchung) {
            if (in_array($buchung->getTyp(), $typen, true)) {
                $buchungen[] = $buchung;
            }
        }

        $view = $this->viewFactory->create(new ViewFactoryData(
            templatePathAndFilename: 'EXT:katschutz/Resources/Private/Templates/Pdf/Beleg.html',
            request: $request,
        ));
        $view->assignMultiple([
            'bestellung' => $bestellung,
            'buchungen' => $buchungen,
            'art' => $art,
            'titel' => $art === 'ruecknahme' ? 'Rücknahmebeleg' : 'Lieferschein',
            'datum' => new \DateTimeImmutable(),
            'emblem' => $this->emblemAlsDataUri(),
            // CSS separat, da geschweifte Klammern im Template als Fluid-Syntax gelesen würden
            'css' => (string)file_get_contents(GeneralUtility::getFileAbsFileName(self::CSS)),
        ]);
        return $view->render();
    }

    /**
     * @param 'lieferschein'|'ruecknahme' $art
     */
    public function sendePdf(Bestellung $bestellung, string $art, ServerRequestInterface $request): never
    {
        $options = new Options();
        $options->setIsRemoteEnabled(false);
        $options->setDefaultFont('Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->erzeugeHtml($bestellung, $art, $request), 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        $dateiname = sprintf('%s-%s.pdf', $art === 'ruecknahme' ? 'Ruecknahme' : 'Lieferschein', $bestellung->getNummer());
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="' . $dateiname . '"')
            ->withHeader('Cache-Control', 'private, no-store')
            ->withBody($this->streamFactory->createStream((string)$dompdf->output()));
        throw new PropagateResponseException($response, 1760439020);
    }

    private function emblemAlsDataUri(): string
    {
        $pfad = GeneralUtility::getFileAbsFileName(self::EMBLEM);
        if ($pfad === '' || !is_file($pfad)) {
            return '';
        }
        return 'data:image/png;base64,' . base64_encode((string)file_get_contents($pfad));
    }
}
