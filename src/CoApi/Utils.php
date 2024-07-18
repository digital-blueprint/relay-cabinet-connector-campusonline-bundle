<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

class Utils
{
    /**
     * Converts a sync timestamp to a DateTimeInterface with a UTC timezone. $timezone needs to be the timezone
     * of the sync timestamp.
     */
    public static function syncTimestampToDateTimeUTC(string $timestamp, \DateTimeZone $timezone): \DateTimeInterface
    {
        $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y\TH:i:s', $timestamp, $timezone);
        if ($dateTime === false) {
            throw new \RuntimeException('Invalid timestamp format');
        }

        return $dateTime->setTimezone(new \DateTimeZone('UTC'));
    }

    public static function getTranslatedText(array $translations, mixed $value, string $locale): string
    {
        $fallbackLocale = 'de';
        $fallback = [
            'de' => 'Unbekannter Wert',
            'en' => 'unknown value',
        ];

        if (isset($translations[$value])) {
            if (isset($translations[$value][$locale])) {
                return $translations[$value][$locale];
            } else {
                return $translations[$value][$fallbackLocale];
            }
        } else {
            if (isset($fallback[$locale])) {
                return $fallback[$locale].' ('.$value.')';
            } else {
                return $fallback[$fallbackLocale].' ('.$value.')';
            }
        }
    }
}
