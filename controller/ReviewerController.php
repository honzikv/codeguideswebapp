<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\DownloadFileModel;
use app\model\GuideModel;
use app\model\ReviewModel;
use app\model\SaveReviewModel;
use app\model\UserModel;
use Exception;

class ReviewerController extends BaseController {

    private const MY_REVIEWS_VIEW = 'my_reviews.twig'; # view pro render recenzi daneho uzivatele
    private const REVIEW_VIEW = 'review.twig'; # view pro render jednotlive recenze
    private const REVIEW_COMPLETED = 'fragment/review_completed_fragment.twig';
    private const REVIEW_ALREADY_COMPLETED = 'review_already_completed.twig';

    private UserModel $userModel;
    private ReviewModel $reviewModel;
    private GuideModel $guideModel;

    function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->guideModel = new GuideModel();
    }

    /**
     * Presmerovani na hlavni stranku podle role
     */
    private function redirectIfNotReviewerOrPublisher() {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] == 'author') {
            $this->redirectToIndex();
        }
    }

    function renderMyReviews() {
        # presmerovani na hlavni stranku pokud se nejedna o reviewera nebo publishera
        $this->redirectIfNotReviewerOrPublisher();

        $userId = $this->session->getUserId(); # ziskani id uzivatele
        $reviews = $this->reviewModel->getPendingReviews($userId); # ziskani nedodelanych recenzi uzivatele
        $this->__render(self::MY_REVIEWS_VIEW, ['reviews' => $reviews]);
    }

    function renderReview(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $this->reviewModel = new ReviewModel(); # pro jistotu vytvorime novy "cisty objekt", ktery neobsahuje zadna predchozi data
        $this->reviewModel->loadData($request->getVariables()); # nacteme promenne z url

        $error = false;
        try {
            $this->reviewModel->validate(); # validace
        } catch (Exception $exception) {
            $error = $exception->getMessage(); # pokud hodi exception zobrazime exception
            $this->__render(self::REVIEW_VIEW, ['error' => $error]);
            return;
        }

        $review = null;
        $guide = null;
        try {
            $review = $this->reviewModel->getReviewFromUserId($this->session->getUserId());

            if ($review['is_finished'] == '1') {
                $this->__render(self::REVIEW_ALREADY_COMPLETED);
                return;
            }

            $guide = $this->guideModel->getGuide($review['guide_id']);
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        if ($error !== false) {
            $this->__render(self::REVIEW_VIEW, ['error' => $error]);
            return;
        }

        $this->__render(self::REVIEW_VIEW, ['review' => $review, 'guide' => $guide]);
    }

    function downloadGuide(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $downloadFileModel = new DownloadFileModel();
        $downloadFileModel->loadData($request->getVariables());

        $error = false;
        $fileName = false;
        try {
            $downloadFileModel->validate();
            $review = $this->reviewModel->getReview($downloadFileModel->reviewId);
            if ($review['reviewer_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }

            $guide = $this->guideModel->getGuide($review['guide_id']);
            $fileName = $guide['filename'];
        } catch (Exception $exception) {
            $this->__render(self::REVIEW_VIEW, ['error' => $exception->getMessage()]);
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
            $this->__render(self::REVIEW_VIEW, ['error' => 'File not found']);
        }

    }

    function saveReview(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $saveReviewModel = new SaveReviewModel();
        $saveReviewModel->loadData($request->getBody());

        $review = null;
        $guide = null;
        try {
            $saveReviewModel->validate();

            $review = $this->reviewModel->getReview($saveReviewModel->reviewId);
            if ($review['reviewer_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }

            $saveReviewModel->saveReview();
            $guide = $this->guideModel->getGuide($review['guide_id']);
            $review = $this->reviewModel->getReview($saveReviewModel->reviewId); # refresh, protoze jsme review upravili
        } catch (Exception $exception) {
            $this->__render(self::REVIEW_VIEW, ['error' => $exception->getMessage()]);
            return;
        }

        $fragment = $this->getRenderedView(self::REVIEW_VIEW, ['review' => $review, 'guide' => $guide,
            'result' => 'Review was successfully saved']);
        $response = ['fragment' => $fragment];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function completeReview(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $saveReviewModel = new SaveReviewModel();
        $saveReviewModel->loadData($request->getBody());

        $review = null;
        $guide = null;
        try {
            $saveReviewModel->validate();

            $review = $this->reviewModel->getReview($saveReviewModel->reviewId);
            if ($review['is_finished'] == '1') {
                $fragment = $this->getRenderedView(self::REVIEW_ALREADY_COMPLETED);
                $response = ['fragment' => $fragment];
                $response = json_encode($response);
                $this->sendResponse($response);
                return;
            }
            if ($review['reviewer_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }
            $saveReviewModel->completeReview();
        } catch (Exception $exception) {
            $this->__render(self::REVIEW_VIEW, ['error' => $exception->getMessage()]);
            return;
        }

        $fragment = $this->getRenderedView(self::REVIEW_COMPLETED);
        $response = ['fragment' => $fragment];
        $response = json_encode($response);
        $this->sendResponse($response);
    }
}