<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Katschutz\Service\KatschutzSettings;
use Schmid\Katschutz\Service\LageService;
use Schmid\Katschutz\Service\ZugriffService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

/**
 * Gemeinsame Basis: Zugriffsprüfung VOR dem Argument-Mapping (keine Datensätze
 * für Unberechtigte laden), POST-Zwang für schreibende Actions, gemeinsame
 * View-Variablen (aktive Lage, Rolle).
 */
abstract class AbstractKatschutzController extends ActionController
{
    /**
     * Actions, die Daten verändern und nur per POST aufgerufen werden dürfen.
     *
     * @var list<string>
     */
    protected const SCHREIBENDE_ACTIONS = [];

    protected KatschutzSettings $katschutz;

    public function __construct(
        protected readonly ZugriffService $zugriff,
        protected readonly LageService $lageService,
    ) {}

    abstract protected function hatZugriff(): bool;

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $this->katschutz = KatschutzSettings::fromRequest($request)->mitStoragePid($this->storagePid());
        if ($this->zugriff->benutzerId() <= 0) {
            return $this->hinweis('Bitte melden Sie sich an, um das KatS-Inventar zu nutzen.', 'info');
        }
        if (!$this->hatZugriff()) {
            return $this->hinweis('Sie haben keine Berechtigung für diesen Bereich.', 'danger');
        }
        if (in_array($request->getControllerActionName(), static::SCHREIBENDE_ACTIONS, true)
            && $request->getMethod() !== 'POST'
        ) {
            return $this->hinweis('Diese Aktion ist nur über das Formular möglich.', 'warning');
        }
        return parent::processRequest($request);
    }

    /**
     * Erste PID der Extbase-Persistenz (Datensatzsammlung des Plugins, sonst Set-Setting).
     */
    private function storagePid(): int
    {
        $framework = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        return (int)explode(',', (string)($framework['persistence']['storagePid'] ?? '0'))[0];
    }

    protected function initializeView(ViewInterface $view): void
    {
        $view->assignMultiple([
            'lage' => $this->lageService->aktiveLage(),
            'istVerwalter' => $this->zugriff->istVerwalter($this->katschutz),
            'freigabeNoetig' => $this->lageService->freigabeNoetig($this->katschutz),
        ]);
    }

    protected function meldung(string $text, ContextualFeedbackSeverity $stufe = ContextualFeedbackSeverity::OK): void
    {
        $this->addFlashMessage($text, '', $stufe);
    }

    protected function fehler(string $text): void
    {
        $this->addFlashMessage($text, '', ContextualFeedbackSeverity::ERROR);
    }

    private function hinweis(string $text, string $farbe): ResponseInterface
    {
        return $this->htmlResponse(sprintf(
            '<div class="container my-4"><div class="alert alert-%s" role="alert">%s</div></div>',
            $farbe,
            htmlspecialchars($text),
        ));
    }
}
