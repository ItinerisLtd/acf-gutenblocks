<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

class Util
{
    // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound -- public API; renaming breaks named arguments.
    public static function camelToKebab(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    public static function sanitizeHtmlClasses(array $classes): string
    {
        return implode(
            ' ',
            array_map(fn ($class): string => sanitize_html_class((string) $class), $classes),
        );
    }
}
