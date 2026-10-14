<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Controller;

/**
 * Basis aller Actions des Verwaltungs-Plugins: nur für die Verwalter-Gruppe.
 */
abstract class AbstractVerwaltungController extends AbstractKatschutzController
{
    protected function hatZugriff(): bool
    {
        return $this->zugriff->istVerwalter($this->katschutz);
    }

    /** @return array<string, mixed> */
    protected function formularDaten(string $name): array
    {
        return $this->request->hasArgument($name) ? (array)$this->request->getArgument($name) : [];
    }
}
