<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use TYPO3\CMS\Core\Context\Context;

final readonly class ZugriffService
{
    public function __construct(
        private Context $context,
    ) {}

    public function benutzerId(): int
    {
        return (int)$this->context->getPropertyFromAspect('frontend.user', 'id', 0);
    }

    public function istVerwalter(KatschutzSettings $settings): bool
    {
        return $this->inGruppe($settings->gruppeVerwalter);
    }

    /** Verwalter dürfen ebenfalls bestellen (z.B. Schnellausgabe in einer Lage). */
    public function darfBestellen(KatschutzSettings $settings): bool
    {
        return $this->inGruppe($settings->gruppeInspektion) || $this->istVerwalter($settings);
    }

    /**
     * Gruppe als Titel (portabel zwischen Umgebungen) oder als UID.
     */
    private function inGruppe(string $gruppe): bool
    {
        if ($gruppe === '' || $this->benutzerId() <= 0) {
            return false;
        }
        if (ctype_digit($gruppe)) {
            $ids = array_map('intval', (array)$this->context->getPropertyFromAspect('frontend.user', 'groupIds', []));
            return in_array((int)$gruppe, $ids, true);
        }
        $namen = (array)$this->context->getPropertyFromAspect('frontend.user', 'groupNames', []);
        return in_array($gruppe, $namen, true);
    }
}
