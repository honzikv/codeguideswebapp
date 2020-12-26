<?php


namespace app\controller;


use app\core\BaseController;
use app\core\TeapotError;
use app\core\Request;
use app\model\GuideModel;
use app\model\UserModel;
use Exception;

class GuideController extends BaseController {

    private const GUIDE_LIST_VIEW = 'guide_list.twig';
    private const CREATE_GUIDE_VIEW = 'create_guide.twig';
    private const VIEW_GUIDE_CREATED = 'success_guide_upload.html';
    private const MY_GUIDES_VIEW = 'my_guides.twig';

    private UserModel $userModel;
    private GuideModel $guideModel;

    function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->guideModel = new GuideModel();
    }

    function renderGuideList() {
        try {
            $guides = $this->guideModel->getAllPublishedGuides();
        } catch (TeapotError $error) {
            $this->redirectTo418();
        }
        $this->__render(self::GUIDE_LIST_VIEW, ['guides' => $guides]);
    }

    function renderCreateGuide() {
        $this->__render(self::CREATE_GUIDE_VIEW);
    }

    function processGuideUpload(Request $request) {
        $guideModel = new GuideModel();
        $guideModel->loadData($request->getBody());

        try {
            $guideModel->validate();
            $fileName = $guideModel->uploadFile($request);
            $userId = $this->userModel->getUserId($this->session->getUsername());
            $guideModel->addGuideToDatabase($fileName, $userId);
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        $response = ['html' => $this->getRenderedView(self::VIEW_GUIDE_CREATED)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function renderMyGuides() {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $userId = $this->userModel->getUserId($this->session->getUsername());

        $userGuides = $this->userModel->getUserGuidesWithStates($userId);

        $this->__render(self::MY_GUIDES_VIEW, ['guides' => $userGuides]);
    }
}