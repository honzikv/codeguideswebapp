<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controller\CreateTournamentController;
use app\controller\MainPageController;
use app\core\Application;
use app\controller\LoginController;
use app\controller\RegisterController;
use app\controller\TournamentsController;

$application = new Application(dirname(__DIR__));
$router = $application->router;

# nastaveni routeru pro metody, pri triggeru jedne z metod zavola dany controller a obslouzi request

$router->setGetMethod('/', [MainPageController::class, 'render']);
$router->setGetMethod('/login', [LoginController::class, 'render']);
$router->setGetMethod('/register', [RegisterController::class, 'render']);
$router->setGetMethod('/tournaments', [TournamentsController::class, 'render']);
$router->setGetMethod('/create-tournament', [CreateTournamentController::class, 'render']);

$application->router->setPostMethod('/register', [RegisterController::class, 'processRegistration']);
$application->router->setPostMethod('/login', [LoginController::class, 'processLogin']);


$application->run();
