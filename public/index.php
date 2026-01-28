<?php
declare(strict_types=1);

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Config;
use App\Core\Installer;

$basePath = is_dir(__DIR__ . '/../app') ? realpath(__DIR__ . '/..') : realpath(__DIR__);

require_once $basePath . '/app/Core/helpers.php';
require_once $basePath . '/app/Core/Autoload.php';

App\Core\Autoload::register();

Session::start();
Config::load($basePath . '/config/config.php');

$request = Request::capture();
$response = new Response();
$router = new Router($request, $response);

Installer::guard($request, $response);

require_once $basePath . '/app/routes.php';

$router->dispatch();
