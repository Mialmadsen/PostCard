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
use PostCard\Controllers\PostController;
use PostCard\Controllers\PostImageController;
use PostCard\Controllers\RequestController;
use PostCard\Controllers\GroupController;
use PostCard\Controllers\GroupPageController;
use PostCard\Controllers\HomeController;
use PostCard\Controllers\MemberController;
use PostCard\Controllers\CommentController;
use PostCard\Controllers\LikeController;
use PostCard\Controllers\ProfileController;
use PostCard\Controllers\SettingsController;
use PostCard\Controllers\ProfileImageController;
use PostCard\Controllers\AdminPostController;

require __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/Helpers/escape.php';
require_once __DIR__ . '/../app/Helpers/url.php';
require_once __DIR__ . '/../app/Helpers/asset.php';
require_once __DIR__ . '/../app/Helpers/input.php';
require_once __DIR__ . '/../app/Helpers/redirect.php';
require_once __DIR__ . '/../app/Helpers/date.php';
require_once __DIR__ . '/../app/Helpers/initials.php';
require_once __DIR__ . '/../app/Helpers/abort.php';

// Show errors on my own PC, hide them on the live site (both still write them to the log)
$isLocal = Config::get('APP_ENV') === 'local';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

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

// From here on, a crash nobody catches shows the error page (and is logged) instead of an empty page.
// Set after BASE_PATH and the session, because the error page needs both.
set_exception_handler('handleUncaughtException');

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
$router->add('GET', '/join', [GroupController::class, 'join']);
$router->add('POST', '/join', [GroupController::class, 'acceptInvitation']);
$router->add('GET', '/group-settings', [GroupController::class, 'settings']);
$router->add('POST', '/group-settings', [GroupController::class, 'updateSettings']);
$router->add('GET', '/profile', [ProfileController::class, 'show']);
$router->add('GET', '/profile-image', [ProfileImageController::class, 'show']);
$router->add('GET', '/settings', [SettingsController::class, 'show']);
$router->add('POST', '/settings/details', [SettingsController::class, 'updateDetails']);
$router->add('POST', '/settings/password', [SettingsController::class, 'updatePassword']);
$router->add('POST', '/settings/picture', [SettingsController::class, 'updatePicture']);
$router->add('POST', '/settings/remove-picture', [SettingsController::class, 'removePicture']);
$router->add('GET', '/members', [MemberController::class, 'index']);
$router->add('POST', '/make-admin', [MemberController::class, 'makeAdmin']);
$router->add('POST', '/remove-member', [MemberController::class, 'removeMember']);
$router->add('POST', '/leave-group', [MemberController::class, 'leaveGroup']);
$router->add('GET', '/group', [GroupPageController::class, 'show']);
$router->add('GET', '/experience', [ExperienceController::class, 'show']);
$router->add('GET', '/create-experience', [ExperienceController::class, 'create']);
$router->add('POST', '/create-experience', [ExperienceController::class, 'store']);
$router->add('GET', '/create-post', [PostController::class, 'create']);
$router->add('POST', '/create-post', [PostController::class, 'store']);
$router->add('GET', '/post', [PostController::class, 'show']);
$router->add('GET', '/edit-post', [PostController::class, 'edit']);
$router->add('POST', '/edit-post', [PostController::class, 'update']);
$router->add('POST', '/delete-post', [PostController::class, 'delete']);
$router->add('POST', '/post-comment', [CommentController::class, 'store']);
$router->add('POST', '/update-comment', [CommentController::class, 'update']);
$router->add('POST', '/delete-comment', [CommentController::class, 'delete']);
$router->add('POST', '/post-like', [LikeController::class, 'toggle']);
$router->add('GET', '/post-likes', [LikeController::class, 'users']);
$router->add('GET', '/post-image', [PostImageController::class, 'show']);
$router->add('GET', '/requests', [RequestController::class, 'index']);
$router->add('POST', '/requests', [RequestController::class, 'update']);
$router->add('GET', '/admin/posts', [AdminPostController::class, 'index']);
$router->add('GET', '/admin/posts/show', [AdminPostController::class, 'show']);
$router->add('POST', '/admin/posts/delete', [AdminPostController::class, 'delete']);
$router->add('POST', '/admin/posts/comments/delete', [AdminPostController::class, 'deleteComment']);

// Find who handles this address, e.g. [PageController::class, 'landing'], or null if unknown.
$handler = $router->find($requestMethod, $path);

// Unknown address: we tell the browser (404) and the visitor, then stop.
if ($handler === null) {
    abort(404, 'Siden findes ikke');
}

// Read the two words off the note: 'PostCard\Controllers\PageController' and 'landing'.
[$controllerClass, $methodName] = $handler;

// Call that controller and method: the same as (new PageController())->landing().
$controller = new $controllerClass();
$controller->$methodName();
