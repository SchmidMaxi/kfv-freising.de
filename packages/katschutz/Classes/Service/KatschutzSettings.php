<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Site-Settings des Sets schmid/katschutz (settings.definitions.yaml),
 * überschreibbar in config/sites/<site>/settings.yaml.
 */
final readonly class KatschutzSettings
{
    /**
     * @param list<string> $mailVerwalter
     */
    public function __construct(
        public bool $freigabeErforderlich = true,
        public int $storagePid = 0,
        public string $gruppeInspektion = 'Inspektion',
        public string $gruppeVerwalter = 'KatS-Verwalter',
        public string $mailAbsender = '',
        public array $mailVerwalter = [],
        public int $pruefungVorlaufTage = 30,
    ) {}

    /** Storage-PID aus der Plugin-Konfiguration (Datensatzsammlung bzw. TypoScript) übernehmen. */
    public function mitStoragePid(int $storagePid): self
    {
        return new self(
            $this->freigabeErforderlich,
            $storagePid,
            $this->gruppeInspektion,
            $this->gruppeVerwalter,
            $this->mailAbsender,
            $this->mailVerwalter,
            $this->pruefungVorlaufTage,
        );
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return new self();
        }
        $s = $site->getSettings();
        $empfaenger = array_values(array_filter(array_map(
            'trim',
            explode(',', (string)$s->get('katschutz.mailVerwalter', '')),
        )));
        return new self(
            freigabeErforderlich: (bool)$s->get('katschutz.freigabeErforderlich', true),
            storagePid: (int)$s->get('katschutz.storagePid', 0),
            gruppeInspektion: trim((string)$s->get('katschutz.gruppeInspektion', 'Inspektion')),
            gruppeVerwalter: trim((string)$s->get('katschutz.gruppeVerwalter', 'KatS-Verwalter')),
            mailAbsender: trim((string)$s->get('katschutz.mailAbsender', '')),
            mailVerwalter: $empfaenger,
            pruefungVorlaufTage: (int)$s->get('katschutz.pruefungVorlaufTage', 30),
        );
    }
}
