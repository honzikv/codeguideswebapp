<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\ReviewModel;
use app\model\UserModel;
use Exception;

class MyReviewsController extends BaseController {

    private const VIEW = 'my_reviews.twig';

    private UserModel $userModel;

    function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    function render() {
        $myReviewsModel = new ReviewModel();
        $userId = $this->session->getUserId();
        $reviews = $myReviewsModel->getPendingReviews($userId);
        $this->__render(self::VIEW, ['reviews' => $reviews]);
    }


}