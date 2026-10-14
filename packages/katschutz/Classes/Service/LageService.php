<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Schmid\Katschutz\Domain\Model\Lage;
use Schmid\Katschutz\Domain\Repository\LageRepository;

final class LageService
{
    private Lage|false|null $aktiv = false;

    public function __construct(
        private readonly LageRepository $lageRepository,
    ) {}

    /** Aktive Großschadenslage oder null. */
    public function aktiveLage(): ?Lage
    {
        if ($this->aktiv === false) {
            $this->aktiv = $this->lageRepository->findAktive();
        }
        return $this->aktiv;
    }

    public function aktiveLageUid(): int
    {
        return $this->aktiveLage()?->getUid() ?? 0;
    }

    /** Freigabe entfällt bei abgeschaltetem Setting oder aktiver Lage. */
    public function freigabeNoetig(KatschutzSettings $settings): bool
    {
        return $settings->freigabeErforderlich && $this->aktiveLage() === null;
    }
}
