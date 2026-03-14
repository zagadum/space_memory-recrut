<?php

namespace App\Services;

/**
 * LocaleService
 *
 * Centralized service for handling locale/language transformations
 * across the application. Provides consistent mapping between
 * database storage format and display format.
 */
class LocaleService
{
    /**
     * Supported locales in database format
     */
    public const SUPPORTED_LOCALES = ['uk', 'pl', 'en', 'ru'];

    /**
     * Supported locales in display format
     */
    public const DISPLAY_LOCALES = ['UA', 'PL', 'EN', 'RU'];

    /**
     * Mapping for display to database format
     */
    private const DISPLAY_TO_DB_MAP = [
        'UA' => 'uk',
        'PL' => 'pl',
        'EN' => 'en',
        'RU' => 'ru',
    ];

    /**
     * Mapping for database to display format
     */
    private const DB_TO_DISPLAY_MAP = [
        'uk' => 'UA',
        'pl' => 'PL',
        'en' => 'EN',
        'ru' => 'RU',
    ];

    /**
     * Convert display format to database format
     * Example: UA -> uk, PL -> pl, EN -> en
     *
     * @param string|null $displayLocale
     * @return string|null
     */
    public static function toDatabase(?string $displayLocale): ?string
    {
        if ($displayLocale === null) {
            return null;
        }

        $upper = strtoupper($displayLocale);

        return self::DISPLAY_TO_DB_MAP[$upper] ?? strtolower($displayLocale);
    }

    /**
     * Convert database format to display format
     * Example: uk -> UA, pl -> PL, en -> EN
     *
     * @param string|null $dbLocale
     * @return string|null
     */
    public static function toDisplay(?string $dbLocale): ?string
    {
        if ($dbLocale === null) {
            return null;
        }

        $lower = strtolower($dbLocale);

        return self::DB_TO_DISPLAY_MAP[$lower] ?? strtoupper($dbLocale);
    }

    /**
     * Normalize locale to database format and validate
     *
     * @param string|null $locale
     * @return string|null Normalized locale or null if invalid
     */
    public static function normalize(?string $locale): ?string
    {
        if ($locale === null) {
            return null;
        }

        $normalized = self::toDatabase($locale);

        return self::isValid($normalized) ? $normalized : null;
    }

    /**
     * Check if locale is valid (database format)
     *
     * @param string|null $locale
     * @return bool
     */
    public static function isValid(?string $locale): bool
    {
        if ($locale === null) {
            return false;
        }

        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }

    /**
     * Get default locale (database format)
     *
     * @return string
     */
    public static function getDefault(): string
    {
        return 'uk';
    }

    /**
     * Get all supported locales in database format
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Get all supported locales in display format
     *
     * @return array
     */
    public static function getDisplayLocales(): array
    {
        return self::DISPLAY_LOCALES;
    }
}
