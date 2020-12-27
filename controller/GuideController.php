<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\InternalError;
use app\core\Request;
use app\model\DeleteGuideModel;
use app\model\DownloadGuideModel;
use app\model\GuideDetailModel;
use app\model\GuideModel;
use app\model\UserModel;
use Exception;

class GuideController extends BaseController {

    private const GUIDE_LIST_VIEW = 'guide_list.twig'; # view se seznamem guides
    private const CREATE_GUIDE_VIEW = 'create_guide.twig'; #
    private const VIEW_GUIDE_CREATED = 'success_guide_upload.html';
    private const MY_GUIDES_VIEW = 'my_guides.twig';
    private const MY_GUIDES_TABLE_FRAGMENT = 'fragment/my_guides_table_fragment.twig';
    private const GUIDE_DETAIL_VIEW = 'guide_detail.twig';

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
        } catch (InternalError $error) {
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

    function renderGuideDetail(Request $request) {
        try {
            $guideDetailModel = new GuideDetailModel();
            $guideDetailModel->loadData($request->getVariables());
            $guideDetailModel->validate();
            $guide = $guideDetailModel->getGuideWithAuthor();
            $this->__render(self::GUIDE_DETAIL_VIEW, ['guide' => $guide]);
        }
        catch (Exception|InternalError $error) {
            $this->redirectTo418();
        }
    }

    function downloadGuide(Request $request) {
        $downloadGuideModel = new DownloadGuideModel();

        $fileName = null;
        try {
            $downloadGuideModel->loadData($request->getVariables());
            $downloadGuideModel->validate();
            $guide = $this->guideModel->getGuide($downloadGuideModel->guideId);

            $guidePublished = $this->guideModel->getGuideState('published');
            if ($guide['guide_state'] != $guidePublished['id']) {
                $this->redirectTo418();
            }

            $fileName = $guide['filename'];
        }
        catch (Exception $exception) {
            $this->__render(self::GUIDE_DETAIL_VIEW, ['error' => $exception->getMessage()]);
            return;
        }

        $filePath = Application::$FILES_PATH . $fileName;
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($filePath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        }
        else {
            $this->__render(self::GUIDE_DETAIL_VIEW, ['error' => 'File not found, please contact support']);
        }
    }

    function removeGuide(Request $request) {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $deleteGuideModel = new DeleteGuideModel();
        $deleteGuideModel->loadData($request->getBody());

        try {
            $deleteGuideModel->validate();

            $guide = $this->guideModel->getGuide($deleteGuideModel->guideId);
            if ($guide['user_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }
            $deleteGuideModel->delete();
        }
        catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        $userId = $this->userModel->getUserId($this->session->getUsername());
        $userGuides = $this->userModel->getUserGuidesWithStates($userId);
        $fragment = $this->getRenderedView(self::MY_GUIDES_TABLE_FRAGMENT, ['guides' => $userGuides]);

        $response = ['fragment' => $fragment];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

}