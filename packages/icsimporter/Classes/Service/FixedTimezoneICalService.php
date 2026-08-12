<?php

declare(strict_types=1);

namespace Schmid\IcsImporter\Service;

use HDNET\Calendarize\Exception\UnableToGetEventsException;
use HDNET\Calendarize\Ical\ICalEvent;
use HDNET\Calendarize\Service\Ical\ICalServiceInterface;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;
use Schmid\IcsImporter\Ical\FixedTimezoneEventAdapter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom ICalService that handles non-standard ICS files with standalone TZID properties.
 *
 * Some calendar providers (like Alamos) generate ICS files where TZID is a separate
 * property in the VEVENT instead of a parameter on DTSTART/DTEND. This service
 * uses a custom adapter to handle such cases.
 */
final class FixedTimezoneICalService implements ICalServiceInterface
{
    /**
     * @return list<ICalEvent>
     * @throws UnableToGetEventsException
     */
    public function getEvents(string $filename): array
    {
        $content = $this->fetchContent($filename);
        $calendar = $this->parseCalendar($content);

        return $this->extractEvents($calendar);
    }

    /**
     * @throws UnableToGetEventsException
     */
    private function fetchContent(string $filename): string
    {
        $content = GeneralUtility::getUrl($filename);

        if ($content === false) {
            throw new UnableToGetEventsException(
                sprintf('Unable to get "%s".', $filename),
                1603307743
            );
        }

        return $content;
    }

    /**
     * @throws UnableToGetEventsException
     */
    private function parseCalendar(string $content): VCalendar
    {
        try {
            /** @var VCalendar */
            return Reader::read($content, Reader::OPTION_FORGIVING);
        } catch (ParseException $e) {
            throw new UnableToGetEventsException($e->getMessage(), 1603309056, $e);
        }
    }

    /**
     * @return list<ICalEvent>
     */
    private function extractEvents(VCalendar $calendar): array
    {
        $events = [];

        foreach ($calendar->VEVENT ?? [] as $event) {
            $events[] = FixedTimezoneEventAdapter::createFromVCalendar($event, $calendar);
        }

        return $events;
    }
}
