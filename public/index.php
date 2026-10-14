<?php

/**
 * Front controller: every request comes through this file
 * (public/.htaccess sends all addresses that are not real files here).
 */

declare(strict_types=1);

use PostCard\Core\Router;

require __DIR__ . '/../app/autoload.php';
require __DIR__ . '/../app/Helpers/escape.php';
require __DIR__ . '/../app/Helpers/url.php';

// The address the browser asked for, without the "?id=3" part.
// e.g. "/PostCard/public/group"
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$requestMethod = $_SERVER['REQUEST_METHOD'];

// The folder this file lives in, without a slash at the end.
// Locally "/PostCard/public", on a host often "" (the top of the site).
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Remember the folder for the whole app, so url() can build links: BASE_PATH = '/PostCard/public'
define('BASE_PATH', $basePath);

// Cut the folder off the front, so only the page is left: "/group".
// If nothing is left, it is the front page: "/".
$path = substr($requestPath, strlen($basePath));
if ($path === '') {
    $path = '/';
}

// A list of every address the site knows, and which page it shows
$router = new Router();
$router->add('GET', '/', 'landing');
$router->add('GET', '/login', 'login');
$router->add('GET', '/group', 'group');

// Find the page for this address, or null if the address is unknown.
$page = $router->find($requestMethod, $path);

// Unknown address: we tell the browser (404) and the visitor, then stop.
if ($page === null) {
    http_response_code(404);
    echo 'Siden findes ikke';
    exit;
}

// Now we show the required page
$viewFile = __DIR__ . '/../views/pages/' . $page . '.php';
require $viewFile;
