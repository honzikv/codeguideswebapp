<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controller\ReviewerController;
use app\core\Application;
use app\controller\ErrorController;
use app\controller\GuideController;
use app\controller\MainPageController;
use app\controller\PublisherController;
use app\controller\AuthenticationController;

$application = new Application(dirname(__DIR__));
$router = $application->router;

# nastaveni routeru pro metody, pri triggeru jedne z metod zavola dany controller a obslouzi request

# hlavni stranka
$router->setGetMethod('/', [MainPageController::class, 'render']);

# error stranka kam se presune uzivatel pri spatnem requestu
$router->setGetMethod('/error', [ErrorController::class, 'render404']);
$router->setPostMethod('/error', [ErrorController::class, 'render404']);
$router->setGetMethod('/teapot', [ErrorController::class, 'render418']);
$router->setPostMethod('/teapot', [ErrorController::class, 'render418']);

# autentizace
$router->setGetMethod('/login', [AuthenticationController::class, 'renderLoginPage']);
$router->setGetMethod('/register', [AuthenticationController::class, 'renderRegistrationPage']);
$router->setPostMethod('/register', [AuthenticationController::class, 'processRegistration']);
$router->setPostMethod('/login', [AuthenticationController::class, 'processLogin']);
$router->setGetMethod('/logout', [AuthenticationController::class, 'processLogout']);
$router->setGetMethod('/banned', [AuthenticationController::class, 'renderBanned']);

# akce pro manipulaci s user guides
$router->setGetMethod('/createguide', [GuideController::class, 'renderCreateGuide']);
$router->setPostMethod('/createguide', [GuideController::class, 'processGuideUpload']);
$router->setGetMethod('/guidelist', [GuideController::class, 'renderGuideList']);
$router->setGetMethod('/myguides', [GuideController::class, 'renderMyGuides']);
$router->setGetMethod('/guide', [GuideController::class, 'renderGuideDetail']);
$router->setGetMethod('/guide/download', [GuideController::class, 'downloadGuide']);
$router->setPostMethod('/removeguide',[GuideController::class, 'removeGuide']);

# akce pro publishera (admina) stranky
$router->setGetMethod('/manageusers', [PublisherController::class, 'renderManageUsers']);
$router->setGetMethod('/managecontent', [PublisherController::class, 'renderManageContent']);
$router->setGetMethod('/managereviews', [PublisherController::class, 'renderManageReviews']);
$router->setPostMethod('/ban', [PublisherController::class, 'processBan']);
$router->setPostMethod('/changerole', [PublisherController::class, 'processRoleChange']);
$router->setPostMethod('/deleteuser', [PublisherController::class, 'processDeleteUser']);
$router->setPostMethod('/assignreview', [PublisherController::class, 'assignReview']);
$router->setPostMethod('/deletereview', [PublisherController::class, 'deleteReview']);
$router->setPostMethod('/releaseguide', [PublisherController::class, 'releaseGuide']);
$router->setPostMethod('/rejectguide', [PublisherController::class, 'rejectGuide']);

$router->setGetMethod('/myreviews', [ReviewerController::class, 'renderMyReviews']);
$router->setGetMethod('/review', [ReviewerController::class, 'renderReview']);
$router->setGetMethod('/review/download', [ReviewerController::class, 'downloadGuide']);
$router->setPostMethod('/savereview', [ReviewerController::class, 'saveReview']);


$application->run();
