<?php
declare(strict_types=1);

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Config;
use App\Core\Installer;

require_once __DIR__ . '/../app/Core/helpers.php';
require_once __DIR__ . '/../app/Core/Autoload.php';

App\Core\Autoload::register();

Session::start();
Config::load(__DIR__ . '/../config/config.php');

$request = Request::capture();
$response = new Response();
$router = new Router($request, $response);

Installer::guard($request, $response);

require_once __DIR__ . '/../app/routes.php';

$router->dispatch();
