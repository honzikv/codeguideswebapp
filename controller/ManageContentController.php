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

        $this->__render(self::VIEW);
        $reviewers = $this->userModel->getAllReviewers();

        $content = $this->guideModel->getAllReviewableGuides();
        $content = $this->getAllReviews();
        var_dump($content);
    }
}