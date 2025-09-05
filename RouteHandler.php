<?php

namespace Helpers;

use Core\Request;

/**
 * Class RouteHandler
 *
 * Handles routing logic for mapping route names to controllers and views.
 * It provides methods to register controllers and views for specific routes,
 * match incoming requests to the correct controller or view, and validate routes.
 *
 * @package Helpers
 */
class RouteHandler
{
    /**
     * Stores available route names mapped to their route keys.
     *
     * @var array<string, string>
     */
    public array $routeNames = [];

    /**
     * Stores registered controllers for each route and HTTP method.
     *
     * @var array<string, array<string, string>>
     */
    private array $controllers = [];

    /**
     * Stores registered views for each route.
     *
     * @var array<string, string>
     */
    private array $views = [];

    /**
     * URL prefix for this route handler.
     *
     * @var string
     */
    public string $prefix = '';

    /**
     * RouteHandler constructor.
     *
     * @param string $prefix Optional URL prefix for the route handler.
     */
    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Registers one controllers for a given route and on or multiple HTTP method(s).
     *
     * @param string              $routeName  The route name identifier.
     * @param string|array        $method     HTTP method(s), e.g. 'GET' or ['GET', 'POST'].
     * @param string|null         $controller Optional controller file path.
     *
     * @return void
     */
    public function addController(string $routeName, string|array $method, ?string $controller = null): void
    {
        $route = $this->routeNames[$routeName];

        // Normalize controller path if provided
        $controller = $controller ? $this->normalizePhpPath($controller) : null;
        $controllerPath = $controller ?? "{$route}-controller.php";

        // Normalize $method to array
        $methods = is_array($method) ? $method : [$method];

        foreach ($methods as $m) {
            $m = strtolower($m);
            $this->controllers[$route][$m] = $controllerPath;
        }
    }

    /**
     * Registers a view for a given route.
     *
     * @param string      $routeName The route name identifier.
     * @param string|null $view      Optional view file path.
     *
     * @return void
     */
    public function addView(string $routeName, ?string $view = null): void
    {
        $route = $this->routeNames[$routeName];
        $view = $view ? $this->normalizePhpPath($view) : $view;
        $this->views[$route] = $view ?? "{$route}.php";
    }

    /**
     * Returns all registered controllers.
     *
     * @return array<string, array<string, string>>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * Returns all registered views.
     *
     * @return array<string, string>
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * Matches the current request to a registered controller.
     *
     * @return string|false Controller file path or false if not found.
     */
    public function matchController(): string|false
    {
        $routeName = $this->getRouteName();
        $method = Request::getMethod();

        $route = $this->routeNames[$routeName] ?? false;
        if (!$route) return false;

        $method = strtolower($method);
        $controller = $this->controllers[$route][$method] ?? false;
        return $controller;
    }

    /**
     * Matches the current request to a registered view.
     *
     * @return string|false View file path or false if not found.
     */
    public function matchView(): string|false
    {
        $routeName = $this->getRouteName();
        $route = $this->routeNames[$routeName] ?? false;
        if (! $route) return false;
        return $this->views[$route] ?? false;
    }

    /**
     * Ensures a given PHP file path ends with ".php".
     *
     * @param string $path File path to normalize.
     * @return string Normalized PHP file path.
     */
    private function normalizePhpPath(string $path): string
    {
        $pathWithoutExtension = preg_replace('/\.php$/i', '', $path);
        return $pathWithoutExtension . '.php';
    }

    /**
     * Splits the prefix into parts for route comparison.
     *
     * @return array<int, string>
     */
    private function getPrefixParts(): array
    {
        if (empty($this->prefix)) {
            return [];
        }
        return explode('/', trim($this->prefix, '/'));
    }

    /**
     * Returns the route key based on prefix parts count.
     *
     * @return int
     */
    private function getRouteKey(): int
    {
        return count($this->getPrefixParts());
    }

    /**
     * Returns the current route name from the request.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        $key = $this->getRouteKey();
        return Request::getURLKey($key);
    }

    /**
     * Validates if the current request matches the defined prefix.
     *
     * @return bool True if route is valid, false otherwise.
     */
    public function isValidRoute(): bool
    {
        $prefixParts = $this->getPrefixParts();
        $urlParts = Request::getURLParts();
        return $this->matchCommonKeys($prefixParts, $urlParts);
    }

    /**
     * Compares two arrays and ensures that common keys match.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return bool True if all common keys match, false otherwise.
     */
    private function matchCommonKeys($array1, $array2): bool
    {
        $commonKeys = array_intersect(array_keys($array1), array_keys($array2));

        if (empty($commonKeys)) {
            return false;
        }

        foreach ($commonKeys as $key) {
            if ($array1[$key] !== $array2[$key]) {
                return false;
            }
        }

        return true;
    }
}

