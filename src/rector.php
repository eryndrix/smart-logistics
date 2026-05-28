<?php declare(strict_types=1);

/**
 * Rector configuration for the project.
 *
 * This file registers paths, sets of rules and additional options for Rector.
 * - Limits Rector scope to the "app" directory.
 * - Enables Laravel and PHP rule sets (including PHP 8.5 and type declaration sets).
 * - Adds code quality, dead code, naming and coding style improvements.
 * - Loads phpstan config, enables parallel processing and automatic import of names.
 *
 * Usage:
 *   1. Check what will be changed (safe mode, no files modified):
 *      docker compose exec app php libs/vendor/bin/rector process --config rector.php --dry-run
 *
 *   2. Apply actual fixes after reviewing the diff:
 *      docker compose exec app php libs/vendor/bin/rector process --config rector.php
 *
 * @package Project\Rector
 */

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    /**
     * Configure directories to be processed by Rector.
     *
     * @see https://github.com/rectorphp/rector
     */
    $rectorConfig->paths([__DIR__ . '/app']);

    /**
     * Enable Rector sets.
     *
     * Order is not strictly important, but grouping related sets improves readability:
     * - Laravel-specific sets
     * - PHP version and type declaration sets
     * - General quality, style and static checks
     */
    $rectorConfig->sets([
        // Laravel framework upgrades and checks
        LaravelSetList::LARAVEL_130,

        // PHP version specific rules (PHP 8.5)
        SetList::PHP_85,

        // Type declaration helpers and docblock-based type declaration
        SetList::TYPE_DECLARATION_DOCBLOCKS,
        SetList::TYPE_DECLARATION,

        // General improvements
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::NAMING,
        SetList::CODING_STYLE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::ASSERT,

        // Laravel-specific extra rules
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_CODE_QUALITY,
    ]);

    // Use project's phpstan config for type analysis
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');

    // Run in parallel when possible
    $rectorConfig->parallel();

    // Import fully-qualified names automatically
    $rectorConfig->importNames();

    // Keep short class names unimported when false
    $rectorConfig->importShortClasses(false);
};
