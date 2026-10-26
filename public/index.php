<?php

/**
 * Front controller: every request comes through this file
 * (public/.htaccess sends all addresses that are not real files here).
 */

declare(strict_types=1);

use PostCard\Core\Router;
use PostCard\Core\Session;
use PostCard\Core\Csrf;
use PostCard\Core\Config;
use PostCard\Controllers\PageController;
use PostCard\Controllers\AuthController;
use PostCard\Controllers\ExperienceController;
use PostCard\Controllers\RequestController;
use PostCard\Controllers\GroupController;
use PostCard\Controllers\GroupPageController;
use PostCard\Controllers\HomeController;

require __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/Helpers/escape.php';
require_once __DIR__ . '/../app/Helpers/url.php';
require_once __DIR__ . '/../app/Helpers/asset.php';
require_once __DIR__ . '/../app/Helpers/input.php';
require_once __DIR__ . '/../app/Helpers/redirect.php';
require_once __DIR__ . '/../app/Helpers/date.php';

date_default_timezone_set(Config::get('APP_TIMEZONE'));

// The address the browser asked for, without the "?id=3" part.
// e.g. "/PostCard/public/group"
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$requestMethod = $_SERVER['REQUEST_METHOD'];

// The folder this file lives in, without a slash at the end.
// Locally "/PostCard/public", on a host often "" (the top of the site).
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Remember the folder for the whole app, so url() can build links: BASE_PATH = '/PostCard/public'
define('BASE_PATH', $basePath);

// Start the session, so $_SESSION remember who is logged in between pages
Session::start();

// Every POST must carry the session's CSRF token, or it stops here with 403.
if ($requestMethod === 'POST') {
    Csrf::verify();
}

// Cut the folder off the front, so only the page is left: "/group".
// If nothing is left, it is the front page: "/".
$path = substr($requestPath, strlen($basePath));
if ($path === '') {
    $path = '/';
}

// Every address the site knows, and who handles it.
$router = new Router();
$router->add('GET', '/', [PageController::class, 'landing']);
$router->add('GET', '/home', [HomeController::class, 'index']);
$router->add('GET', '/login', [AuthController::class, 'showLogin']);
$router->add('GET', '/register', [AuthController::class, 'showRegister']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('POST', '/register', [AuthController::class, 'register']);
$router->add('POST', '/logout', [AuthController::class, 'logout']);
$router->add('GET', '/contact', [PageController::class, 'contact']);
$router->add('GET', '/rules', [PageController::class, 'rules']);
$router->add('GET', '/create-group', [GroupController::class, 'create']);
$router->add('POST', '/create-group', [GroupController::class, 'store']);
$router->add('GET', '/invite', [GroupController::class, 'invite']);
$router->add('POST', '/invite', [GroupController::class, 'createInvitation']);
$router->add('POST', '/cancel-invitation', [GroupController::class, 'cancelInvitation']);
$router->add('GET', '/group', [GroupPageController::class, 'show']);
$router->add('GET', '/experience', [ExperienceController::class, 'show']);
$router->add('GET', '/create-experience', [ExperienceController::class, 'create']);
$router->add('POST', '/create-experience', [ExperienceController::class, 'store']);
$router->add('GET', '/requests', [RequestController::class, 'index']);
$router->add('POST', '/requests', [RequestController::class, 'update']);

// Find who handles this address, e.g. [PageController::class, 'landing'], or null if unknown.
$handler = $router->find($requestMethod, $path);

// Unknown address: we tell the browser (404) and the visitor, then stop.
if ($handler === null) {
    http_response_code(404);
    echo 'Siden findes ikke';
    exit;
}

// Read the two words off the note: 'PostCard\Controllers\PageController' and 'landing'.
[$controllerClass, $methodName] = $handler;

// Call that controller and method: the same as (new PageController())->landing().
$controller = new $controllerClass();
$controller->$methodName();
