<?php


namespace app\controller;


use app\core\BaseController;
use app\model\GuideModel;
use app\model\UserModel;

class ManageContentController extends BaseController {

    private const VIEW = 'manage_content.twig';
    private UserModel $userModel;
    private GuideModel $guideModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->guideModel = new GuideModel();
    }

    function render() {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectToIndex();
        }

        $reviewers = $this->userModel->getAllReviewers();
        $allReviewableGuides = $this->guideModel->getAllReviewableGuides();
        foreach ($allReviewableGuides as $guide) {
            $reviews = $this->guideModel->getGuideReviews($guide['guide_id']);
            $guide['reviews'] = $reviews;
        }

        $this->__render(self::VIEW, ['guides' => $allReviewableGuides, 'reviewers' => $reviewers]);
    }
}