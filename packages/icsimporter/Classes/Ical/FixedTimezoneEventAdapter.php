<?php

declare(strict_types=1);

namespace Schmid\IcsImporter\Ical;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use HDNET\Calendarize\Ical\VObjectEventAdapter;
use HDNET\Calendarize\Utility\DateTimeUtility;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Property\ICalendar\DateTime as ICalDateTime;

/**
 * Extended VObjectEventAdapter that handles non-standard ICS files
 * where TZID is specified as a separate property instead of a parameter on DTSTART/DTEND.
 *
 * Some calendar providers (like Alamos) generate ICS files with:
 *   DTSTART:20260120T190000
 *   TZID:Europe/Berlin
 *
 * Instead of the correct RFC 5545 format:
 *   DTSTART;TZID=Europe/Berlin:20260120T190000
 */
final class FixedTimezoneEventAdapter extends VObjectEventAdapter
{
    private const DATETIME_PATTERN = '/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})$/';

    public function __construct(
        VEvent $event,
        private readonly ?DateTimeZone $fallbackTimezone = null,
    ) {
        parent::__construct($event);
    }

    public static function createFromVCalendar(VEvent $event, ?VCalendar $calendar = null): self
    {
        $fallbackTimezone = self::extractCalendarTimezone($calendar);

        return new self($event, $fallbackTimezone);
    }

    private static function extractCalendarTimezone(?VCalendar $calendar): ?DateTimeZone
    {
        if ($calendar === null || !isset($calendar->{'X-WR-TIMEZONE'})) {
            return null;
        }

        $tzid = (string)$calendar->{'X-WR-TIMEZONE'}->getValue();

        return self::createTimezone($tzid);
    }

    private static function createTimezone(string $tzid): ?DateTimeZone
    {
        if ($tzid === '') {
            return null;
        }

        try {
            return new DateTimeZone($tzid);
        } catch (\Exception) {
            return null;
        }
    }

    public function getStartDate(): ?DateTime
    {
        $start = $this->getStartDateTime();

        if ($start === null) {
            return null;
        }

        if ($this->isAllDay()) {
            $start = $start->format('Y-m-d');
        }

        return DateTimeUtility::getDayStart($start);
    }

    public function getEndDate(): ?DateTime
    {
        $end = $this->getCorrectedEndDateTime();

        if ($end === null) {
            return null;
        }

        if ($this->isAllDay()) {
            $end = $end->sub(new DateInterval('P1D'));
            $end = $end->format('Y-m-d');
        }

        return DateTimeUtility::getDayStart($end);
    }

    /**
     * Returns the start datetime, handling both standard and non-standard TZID formats.
     */
    private function getStartDateTime(): ?DateTimeImmutable
    {
        $event = $this->getEvent();

        if (!isset($event->DTSTART)) {
            return null;
        }

        return $this->getDateTimeFromProperty($event->DTSTART);
    }

    /**
     * Returns the corrected end datetime, ensuring it's never before start datetime.
     * This is the central method that applies all necessary fixes for invalid ICS data.
     */
    private function getCorrectedEndDateTime(): ?DateTimeImmutable
    {
        $end = $this->getRawEndDateTime();
        $start = $this->getStartDateTime();

        if ($end === null) {
            return null;
        }

        // Fix: If end is before start, use start datetime as end datetime
        if ($start !== null && $end < $start) {
            return $start;
        }

        // Fix: If same date but end time is 00:00 (midnight) or before start time,
        // use start datetime as end datetime (point-in-time event)
        if ($start !== null) {
            $startDate = $start->format('Y-m-d');
            $endDate = $end->format('Y-m-d');
            $startTime = DateTimeUtility::getNormalizedDaySecondsOfDateTime($start);
            $endTime = DateTimeUtility::getNormalizedDaySecondsOfDateTime($end);

            if ($startDate === $endDate && ($endTime === 0 || $endTime < $startTime)) {
                return $start;
            }
        }

        return $end;
    }

    public function getStartTime(): int
    {
        if ($this->isAllDay()) {
            return self::ALLDAY_START_TIME;
        }

        $start = $this->getStartDateTime();

        if ($start === null) {
            return self::ALLDAY_START_TIME;
        }

        return DateTimeUtility::getNormalizedDaySecondsOfDateTime($start);
    }

    public function getEndTime(): int
    {
        if ($this->isAllDay()) {
            return self::ALLDAY_END_TIME;
        }

        $end = $this->getCorrectedEndDateTime();

        if ($end === null) {
            return self::ALLDAY_END_TIME;
        }

        return DateTimeUtility::getNormalizedDaySecondsOfDateTime($end);
    }

    /**
     * Returns the raw (uncorrected) end datetime from the event.
     */
    private function getRawEndDateTime(): ?DateTimeImmutable
    {
        $event = $this->getEvent();

        if (isset($event->DTEND)) {
            return $this->getDateTimeFromProperty($event->DTEND);
        }

        if (isset($event->DURATION, $event->DTSTART)) {
            $start = $this->getDateTimeFromProperty($event->DTSTART);
            if ($start === null) {
                return null;
            }
            $duration = $event->DURATION->getDateInterval();

            return DateTimeImmutable::createFromMutable(
                DateTime::createFromImmutable($start)->add($duration)
            );
        }

        return null;
    }

    /**
     * Extracts datetime from a property, handling both standard TZID parameter
     * and non-standard standalone TZID property formats.
     */
    private function getDateTimeFromProperty(ICalDateTime $property): ?DateTimeImmutable
    {
        $value = $property->getValue();

        // UTC times (ending with Z) - use native parsing
        if (str_ends_with($value, 'Z')) {
            return $property->getDateTime();
        }

        // Standard format with TZID parameter - use native parsing
        if ($this->hasStandardTimezone($property)) {
            return $property->getDateTime();
        }

        // Non-standard format or missing timezone - parse with fallback timezone
        if (!preg_match(self::DATETIME_PATTERN, $value, $matches)) {
            return $property->getDateTime();
        }

        $dateString = sprintf(
            '%s-%s-%s %s:%s:%s',
            $matches[1],
            $matches[2],
            $matches[3],
            $matches[4],
            $matches[5],
            $matches[6]
        );

        $timezone = $this->getEventTimezone() ?? new DateTimeZone(date_default_timezone_get());

        return new DateTimeImmutable($dateString, $timezone);
    }

    private function hasStandardTimezone(ICalDateTime $property): bool
    {
        return isset($property['TZID']);
    }

    private function getEventTimezone(): ?DateTimeZone
    {
        $event = $this->getEvent();

        if (isset($event->TZID)) {
            $tzid = (string)$event->TZID->getValue();
            $timezone = self::createTimezone($tzid);

            if ($timezone !== null) {
                return $timezone;
            }
        }

        return $this->fallbackTimezone;
    }

}
