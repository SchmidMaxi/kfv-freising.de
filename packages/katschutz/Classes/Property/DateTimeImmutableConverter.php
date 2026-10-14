<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Property;

use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;

/**
 * Extbase registriert den DateTimeConverter nur für \DateTime. \DateTimeImmutable erbt
 * nicht davon und landete sonst beim ObjectConverter („Property $date does not exist“).
 * Delegiert an den Core-Konverter und wandelt das Ergebnis um, damit Optionen wie
 * dateFormat unverändert funktionieren.
 */
final class DateTimeImmutableConverter extends AbstractTypeConverter
{
    public function __construct(
        private readonly DateTimeConverter $dateTimeConverter,
    ) {}

    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null
    ): \DateTimeImmutable|Error|null {
        $ergebnis = $this->dateTimeConverter->convertFrom($source, \DateTime::class, $convertedChildProperties, $configuration);
        return $ergebnis instanceof \DateTime ? \DateTimeImmutable::createFromMutable($ergebnis) : $ergebnis;
    }
}
