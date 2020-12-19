<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controllers\HomeController;
use app\core\Application;

$application = new Application(dirname(__DIR__));
$application->router->setGetMethod('/', 'home');
$application->router->setGetMethod('/login','login');
$application->router->setGetMethod('/register','register');
$application->router->setGetMethod('/tournaments', 'tournaments');

# nastaveni callbacku pro
$application->router->setPostMethod('/register', [HomeController::class, 'processRegistration']);


$application->run();
