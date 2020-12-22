<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\GuideModel;
use app\model\ManageReviewsModel;
use Exception;

class ManageReviewsController extends BaseController {

    private const VIEW = 'manage_reviews.twig';
    private GuideModel $guideModel;

    function __construct() {
        parent::__construct();
        $this->guideModel = new GuideModel();
    }

    function render(Request $request) {
        $manageReviewsModel = new ManageReviewsModel();

        try {
            $manageReviewsModel->loadData($request->getVariables());
            $manageReviewsModel->validate();
        } catch (Exception $exception) {
            $this->redirectTo404();
            return;
        }

        $reviews = $manageReviewsModel->getGuideReviews();
        $guide = $this->guideModel->getGuide($manageReviewsModel->guideId);

        if ($guide == false) {
            $this->redirectTo404();
        }

        if ($reviews == false) {
            $reviews = [];
        }
        $this->__render(self::VIEW, ['guide' => $guide, 'reviews' => $reviews]);
    }
}