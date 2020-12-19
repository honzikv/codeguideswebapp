<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\controller\HomeBaseController;
use app\controller\LoginBaseController;
use app\controller\RegisterBaseController;
use app\controller\TournamentsController;

$application = new Application(dirname(__DIR__));
$router = $application->router;

$router->setGetMethod('/', [HomeBaseController::class, 'show']);
$router->setGetMethod('/login', [LoginBaseController::class, 'show']);
$router->setGetMethod('/register', [RegisterBaseController::class, 'show']);
$router->setGetMethod('/tournaments', [TournamentsController::class, 'show']);

# Register stranka
$application->router->setPostMethod('/register', [LoginBaseController::class, 'processRegistration']);
$application->router->setPostMethod('/login', [LoginBaseController::class, 'processLogin']);


$application->run();
