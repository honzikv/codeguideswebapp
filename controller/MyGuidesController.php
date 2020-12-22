<?php


namespace app\controller;


use app\core\BaseController;
use app\model\GuideModel;
use app\model\UserModel;

class MyGuidesController extends BaseController {

    private const VIEW = 'my_guides.twig';

    private UserModel $userModel;
    private GuideModel $guideModel;

    function __construct() {
        parent::__construct();

        $this->userModel = new UserModel();
        $this->guideModel = new GuideModel();
    }

    function render() {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $userId = $this->userModel->getUserId($this->session->getUsername());

        $userGuides = $this->userModel->getUserGuidesWithStates($userId);

        $this->__render(self::VIEW, ['guides' => $userGuides]);
    }

}