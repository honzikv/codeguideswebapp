<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\controller\HomeController;
use app\controller\LoginController;
use app\controller\RegisterController;
use app\controller\TournamentsController;

$application = new Application(dirname(__DIR__));
$router = $application->router;

$router->setGetMethod('/', [HomeController::class, 'show']);
$router->setGetMethod('/login', [LoginController::class, 'show']);
$router->setGetMethod('/register', [RegisterController::class, 'show']);
$router->setGetMethod('/tournaments', [TournamentsController::class, 'show']);

$application->router->setPostMethod('/register', [RegisterController::class, 'processRegistration']);
$application->router->setPostMethod('/login', [LoginController::class, 'processLogin']);


$application->run();
