<?php


namespace app\controller;

use app\core\BaseController;
use app\core\Request;
use app\model\GuideModel;
use app\model\ManageReviewsModel;
use app\model\UserModel;
use Exception;

class ManageReviewsController extends BaseController {

    private const VIEW = 'manage_reviews.twig';
    private GuideModel $guideModel;
    private UserModel $userModel;

    function __construct() {
        parent::__construct();
        $this->guideModel = new GuideModel();
        $this->userModel = new UserModel();
    }

    function render(Request $request) {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectTo404();
            return;
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectTo404();
            return;
        }

        $manageReviewsModel = new ManageReviewsModel();

        try {
            $manageReviewsModel->loadData($request->getVariables());
            $manageReviewsModel->validate();
        } catch (Exception $exception) {
            $this->redirectTo404();
            return;
        }

        $reviews = $manageReviewsModel->getGuideReviews();
        $reviewers = $this->userModel->getAllReviewers();
        $guide = $this->guideModel->getGuide($manageReviewsModel->guideId);

        if ($guide == false) {
            $this->redirectTo404();
        }

        if ($reviews == false) {
            $reviews = [];
        }
        $this->__render(self::VIEW, ['guide' => $guide, 'reviews' => $reviews, 'reviewers' => $reviewers]);
    }

    function assignReview(Request $request) {

    }
}