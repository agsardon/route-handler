<?php

namespace Core;

/**
 * Class Request
 *
 * Provides static helpers to work with HTTP requests.
 * Used by RouteHandler to extract method and URL parts.
 *
 * @package Core
 */
class Request
{
    /**
     * Returns the HTTP method of the current request.
     *
     * @return string
     */
    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Returns the parts of the current URL path as an array.
     *
     * Example:
     *   URL: http://example.com/api/users/123
     *   getURLParts() => ['api', 'users', '123']
     *
     * @return array<int, string>
     */
    public static function getURLParts(): array
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));

        return array_values(array_filter($parts)); // remove empty values
    }

    /**
     * Returns the URL segment by index (zero-based).
     *
     * Example:
     *   URL: http://example.com/api/users/123
     *   getURLKey(1) => 'users'
     *
     * @param int $key Index of the URL segment.
     * @return string
     */
    public static function getURLKey(int $key): string
    {
        $parts = self::getURLParts();
        return $parts[$key] ?? '';
    }
}
