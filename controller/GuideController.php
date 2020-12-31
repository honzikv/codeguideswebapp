<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\DeleteGuideModel;
use app\model\DownloadGuideModel;
use app\model\GuideDetailModel;
use app\model\GuideModel;
use app\model\UserModel;
use Exception;

/**
 * Controller pro operace se zpravami
 * Class GuideController
 * @package app\controller
 */
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

    /**
     * Render stranky se senznamem vsech verejne dostupnych navodu
     */
    function renderGuideList() {
        $guides = $this->guideModel->getAllPublishedGuides();
        $this->__render(self::GUIDE_LIST_VIEW, ['guides' => $guides]);
    }

    /**
     * Render stranky s formularem pro vytvoreni guide
     */
    function renderCreateGuide() {
        $this->__render(self::CREATE_GUIDE_VIEW);
    }

    /**
     * Obslouzeni POST pozadavku pro nahrani nove guide
     * @param Request $request POST request s informacemi o guide
     */
    function processGuideUpload(Request $request) {
        if (!$this->session->isUserLoggedIn()) { # pokud neni uzivatel prihlaseny request ignorujeme
            return;
        }
        $guideModel = new GuideModel();
        $guideModel->loadData($request->getBody());

        try {
            $guideModel->validate(); # validace dat
            $fileName = $guideModel->uploadFile($request); # upload souboru
            $userId = $this->userModel->getUserId($this->session->getUsername()); # ziskani id uzivatele
            $guideModel->createGuide($fileName, $userId); # vytvoreni guide v databazi
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()]; # pri chybe posleme klientovi zpravu s chybou
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        # jinak posleme html stranku, kterou uzivatel vyrenderuje
        $response = ['html' => $this->getRenderedView(self::VIEW_GUIDE_CREATED)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    /**
     * Render stranky s guides uzivatele
     */
    function renderMyGuides() {
        if (!$this->session->isUserLoggedIn()) { # pokud neni uzivatel prihlaseny presmerujeme na vychozi stranku
            $this->redirectToIndex();
        }

        $userId = $this->userModel->getUserId($this->session->getUsername()); # id uzivatele
        $userGuides = $this->userModel->getUserGuidesWithStates($userId); # guides uzivatele
        $reviewScores = $this->guideModel->getReviewMeanScores($userGuides);
        $this->__render(self::MY_GUIDES_VIEW, ['guides' => $userGuides,
            'reviewScores' => $reviewScores]); # posleme stranku s guides
    }

    /**
     * Render stranky s detaily o tutorialu
     * @param Request $request GET request s id guide
     */
    function renderGuideDetail(Request $request) {
        $guideDetailModel = new GuideDetailModel();
        $guideDetailModel->loadData($request->getVariables());

        $guide = null;
        try {
            $guideDetailModel->validate();
            $guide = $guideDetailModel->getGuideWithAuthor();
            $statePublished = $this->guideModel->getGuideState('published');

            # pokud by nekdo pristupoval k necemu co neni jeho a neni zverejnene vyhodime chybu
            if ($guide['guide_state'] != $statePublished['id'] and $this->session->getUserId() != $guide['user_id']
                and $this->session->getUserInfo()['role'] != 'publisher') {
                throw new Exception('Access denied');
            }
        } catch (Exception $exception) { # render chyby
            $this->__render(self::GUIDE_DETAIL_VIEW, ['error' => $exception->getMessage()]);
            return;
        }

        # pokud vse probehne spravne, vyrenderujeme view
        $this->__render(self::GUIDE_DETAIL_VIEW, ['guide' => $guide]);
    }

    /**
     * Obslouzeni GET pozadavku pro stazeni souboru
     * @param Request $request
     */
    function downloadGuide(Request $request) {
        $downloadGuideModel = new DownloadGuideModel();

        $fileName = null;
        try {
            $downloadGuideModel->loadData($request->getVariables());
            $downloadGuideModel->validate();
            $guide = $this->guideModel->getGuide($downloadGuideModel->guideId);

            $guidePublished = $this->guideModel->getGuideState('published');
            if ($guide['guide_state'] != $guidePublished['id'] and $guide['user_id'] != $this->session->getUserId()
                and $this->session->getUserInfo()['role'] != 'publisher') {
                throw new Exception('Access denied');
            }

            $fileName = $guide['filename'];
        } catch (Exception $exception) {
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
        } else {
            $this->__render(self::GUIDE_DETAIL_VIEW, ['error' => 'File not found, please contact support']);
        }
    }

    /**
     * Odstrani guide daneho uzivatele
     * @param Request $request POST request s id guide
     */
    function removeGuide(Request $request) {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $deleteGuideModel = new DeleteGuideModel();
        $deleteGuideModel->loadData($request->getBody()); # nacteni dat z requestu

        try {
            $deleteGuideModel->validate(); # validace id
            $guide = $this->guideModel->getGuide($deleteGuideModel->guideId); # ziskani guide

            # pokud neni prihlaseny uzivatel autorem nelze smazat
            if ($guide['user_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }
            $deleteGuideModel->delete(); # jinak smazeme
        } catch (Exception $exception) { # pri chybe vratime response s chybou
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        # pokud vse probehlo spravne vratime fragment s novou tabulkou bez smazaneho zaznamu
        $userId = $this->userModel->getUserId($this->session->getUsername());
        $userGuides = $this->userModel->getUserGuidesWithStates($userId);
        $fragment = $this->getRenderedView(self::MY_GUIDES_TABLE_FRAGMENT, ['guides' => $userGuides]);

        $response = ['fragment' => $fragment];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

}