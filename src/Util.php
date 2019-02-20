<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

class Util
{
    public static function camelToKebab(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    public static function sanitizeHtmlClasses(array $classes): string
    {
        return implode(' ', array_map(function (string $class) : string {
            return sanitize_html_class($class);
        }, $classes));
    }
}
