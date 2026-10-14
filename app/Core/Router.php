<?php

declare(strict_types=1);

namespace PostCard\Core;

// Know the addresses and find the page
class Router
{
    private array $routes = [];

    // It remembers what page belongs to this address e.g. 'GET','/login' → 'login'
    public function add(string $method, string $path, string $page): void
    {
        $this->routes[$method][$path] = $page;
    }

    // Find the page for an address: 'GET','/login' → 'login', unknown → null.
    public function find(string $method, string $path): ?string
    {
        return $this->routes[$method][$path] ?? null;
    }
}
