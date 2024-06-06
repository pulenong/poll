<?php

declare(strict_types=1);

namespace User\Service;

class UrlService
{
    /**
     * @param string $url
     * @return array|string
     */
    public static function encode(string $url): array|string
    {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($url));
    }

    /**
     * @param string $url
     * @return false|string
     */
    public static function decode(string $url): false|string
    {
        return base64_decode(str_replace(['-','_'], ['+','/'], $url));
    }
}
