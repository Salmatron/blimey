<?php

namespace Salmatron\Blimey;

class Util
{
    public static function startsWith(string $haystack, string $needle): bool
    {
        $length = \strlen($needle);

        return (\substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = \strlen($needle);

        if ($length == 0) {
            return true;
        }

        return (\substr($haystack, -$length) === $needle);
    }
}
