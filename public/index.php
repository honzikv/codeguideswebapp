<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;

$application = new Application(dirname(__DIR__));

$application->router->setGetMethod('/','home');
$application->router->setGetMethod('tournaments','tournaments');


$application->run();
