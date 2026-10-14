<?php

declare(strict_types=1);

namespace PostCard\Core;

// Knows the addresses and who handles each one.
class Router
{
    private array $routes = [];

    // It remembers what page belongs to this address e.g. 'GET', '/' → [PageController::class, 'landing']
    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    // Find the page for an address: 'GET', '/' → [PageController::class, 'landing']
    public function find(string $method, string $path): ?array
    {
        return $this->routes[$method][$path] ?? null;
    }
}
