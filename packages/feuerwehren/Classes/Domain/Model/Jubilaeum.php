<?php

declare(strict_types=1);

class Jubilaeum extends AbstractEntity
{
    protected ?Feuerwehr $feuerwehr = null;
    protected int $jahr = 0;
    protected string $titel = '';
    protected string $beschreibung = '';

    // Getter / Setter...
}
