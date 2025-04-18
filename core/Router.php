<?php

use core\http\Next;
use core\http\Request;
use core\http\Response;

require_once './core/http/Next.php';
require_once './core/http/Request.php';
require_once './core/http/Response.php';

class Router {
    private static array $routes = [];
    private static array $middleware = [];
    private static array $groupStack = [];

    private static string $viewsFolder = '';
    private static string $errorViews = 'Errors';
    private static int $COMPONENT_RENDER_DEPTH = 2;

    // === CONFIGURATION ===
    public static function setViewsFolder(string $folder): void {
        self::$viewsFolder = $folder;
    }

    public static function setErrorViews(string $folder): void {
        self::$errorViews = $folder;
    }

    public static function setComponentRenderDepth(int $depth): void {
        self::$COMPONENT_RENDER_DEPTH = $depth;
    }

    public static function getViewsFolder(): string {
        return self::$viewsFolder;
    }

    public static function getErrorViews(): string {
        return self::$errorViews;
    }

    public static function getComponentRenderDepth(): int {
        return self::$COMPONENT_RENDER_DEPTH;
    }

    public static function getRoutes(): array {
        return array_map(fn($r) => ['route' => $r['route'], 'method' => $r['method']], self::$routes);
    }

    // === MIDDLEWARE ===
    public static function use(callable $middleware): void {
        self::$middleware[] = $middleware;
    }

    // === ROUTE GROUPING ===
    public static function group(array $options, callable $callback): void {
        self::$groupStack[] = $options;
        $callback();
        array_pop(self::$groupStack);
    }

    // === HTTP METHODS ===
    public static function get(string $route, callable|string $callback, ?callable $middleware = null): void {
        self::registerRoute('GET', $route, $callback, $middleware);
    }

    public static function post(string $route, callable|string $callback, ?callable $middleware = null): void {
        self::registerRoute('POST', $route, $callback, $middleware);
    }

    public static function put(string $route, callable|string $callback, ?callable $middleware = null): void {
        self::registerRoute('PUT', $route, $callback, $middleware);
    }

    public static function delete(string $route, callable|string $callback, ?callable $middleware = null): void {
        self::registerRoute('DELETE', $route, $callback, $middleware);
    }

    private static function registerRoute(string $method, string $route, callable|string $callback, ?callable $middleware): void {
        $prefix = '';
        $groupMiddleware = [];

        foreach (self::$groupStack as $group) {
            $prefix .= rtrim($group['prefix'] ?? '', '/');
            if (isset($group['middleware'])) {
                $groupMiddleware[] = $group['middleware'];
            }
        }

        // Normalize full route
        $normalized = rtrim($prefix . '/' . ltrim($route, '/'), '/');
        $fullRoute = $normalized === '' ? '/' : $normalized;

        self::$routes[] = [
            'method' => $method,
            'route' => $fullRoute,
            'callback' => $callback,
            'middleware' => $middleware,
            'groupMiddleware' => $groupMiddleware
        ];
    }

    // === ROUTE RESOLUTION ===
    public static function resolve(): void {
        $method = Request::getHTTPmethod();
        $path = self::normalizePath(Request::getURIpath());

        foreach (self::$routes as $route) {
            $routePath = self::normalizePath($route['route']);
            $params = self::matchRoute($routePath, $path);

            if ($params === null || $method !== $route['method']) {
                continue;
            }

            // Run middleware
            $params = self::runMiddleware(self::$middleware, $params);
            $params = self::runMiddleware($route['groupMiddleware'], $params);
            if (is_callable($route['middleware'])) {
                $params = self::runMiddleware([$route['middleware']], $params);
            }

            // Run handler
            if (is_callable($route['callback'])) {
                $route['callback'](new Request($params), new Response());
            } elseif (is_string($route['callback'])) {
                Response::render($route['callback']);
            }

            return;
        }

        // No route matched
        Response::notFound();
    }

    // === REGEX MATCHING ===
    private static function matchRoute(string $pattern, string $path): ?array {
        // Replace :param with named regex group
        $regex = preg_replace_callback('/:([a-zA-Z0-9_]+)/', fn($m) => '(?P<' . $m[1] . '>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    private static function normalizePath(string $path): string {
        $clean = '/' . trim($path, '/');
        return $clean === '/' ? $clean : rtrim($clean, '/');
    }

    // === MIDDLEWARE RUNNER ===
    private static function runMiddleware(array $middlewares, array $params): array {
        foreach ($middlewares as $middleware) {
            $next = new Next($params);
            $next = $middleware(new Request($params), $next);
            $params = $next->getModifiedData() ?? $params;
        }
        return $params;
    }
}
