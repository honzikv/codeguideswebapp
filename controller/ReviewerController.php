<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\DownloadReviewModel;
use app\model\GuideModel;
use app\model\ReviewModel;
use app\model\SaveReviewModel;
use app\model\UserModel;
use Exception;

class ReviewerController extends BaseController {

    private const MY_REVIEWS_VIEW = 'my_reviews.twig'; # view pro render recenzi daneho uzivatele
    private const REVIEW_VIEW = 'review.twig'; # view pro render jednotlive recenze
    private const REVIEW_FORM_FRAGMENT = 'fragment/review_form_fragment.twig';
    private const GUIDE_NOT_REVIEWABLE = 'guide_not_reviewable.twig';

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

    /**
     * Render stranky "my reviews"
     */
    function renderMyReviews() {
        # presmerovani na hlavni stranku pokud se nejedna o reviewera nebo publishera
        $this->redirectIfNotReviewerOrPublisher();

        $userId = $this->session->getUserId(); # ziskani id uzivatele
        $reviewedId = $this->guideModel->getGuideState('reviewed')['id'];
        $reviews = $this->reviewModel->getEditableReviews($userId, $reviewedId); # ziskani recenzi uzivatele
        $this->__render(self::MY_REVIEWS_VIEW, ['reviews' => $reviews]);
    }

    /**
     * Render formulare pro recenzi
     * @param Request $request request s promennou reviewId
     */
    function renderReview(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $this->reviewModel = new ReviewModel();
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
            if ($review == null || $review == false) {
                $this->redirectTo404(); # presmerovani na 404, pokud nekdo jiny nez recenzent pristoupi ke strance
            }

            $guide = $this->guideModel->getGuide($review['guide_id']);
            $guidePublished = $this->guideModel->getGuideState('published');

            if ($guide['guide_state'] == $guidePublished['id']) {
                $this->__render(self::GUIDE_NOT_REVIEWABLE);
                return;
            }

        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        if ($error !== false) {
            $this->__render(self::REVIEW_VIEW, ['error' => $error]);
            return;
        }

        $this->__render(self::REVIEW_VIEW, ['review' => $review, 'guide' => $guide]);
    }

    /**
     * Stahnuti souboru ze serveru
     * @param Request $request
     */
    function downloadGuide(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $downloadFileModel = new DownloadReviewModel();
        $downloadFileModel->loadData($request->getVariables());

        $error = null;
        $fileName = null;
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

    /**
     * Funkce k ulozeni recenze - tzn. nastavi recenzi jako is_finished = true - publisher pote vi, ze recenzent
     * pridelenou recenzi zpracoval.
     * @param Request $request request od klienta
     */
    function saveReview(Request $request) {
        $this->redirectIfNotReviewerOrPublisher();

        $saveReviewModel = new SaveReviewModel();
        $saveReviewModel->loadData($request->getBody());

        $review = null;
        $guide = null;
        try {
            $saveReviewModel->validate();

            $review = $this->reviewModel->getReview($saveReviewModel->reviewId);
            $guide = $this->guideModel->getGuide($review['guide_id']);
            $reviewdedState = $this->guideModel->getGuideState('reviewed');

            # pokud guide neni ve stavu reviewed pak odesleme obrazovku s upozornenim, ze recenzi jiz neslo ulozit
            if ($guide['guide_state'] != $reviewdedState['id']) {
                $fragment = $this->getRenderedView(self::GUIDE_NOT_REVIEWABLE);
                $response = ['fragment' => $fragment];
                $response = json_encode($response);
                $this->sendResponse($response);
                return;
            }

            # pokud je reviewer_id jine, nez id prihlaseneho uzivatele vyhodime exception (toto by nemelo byt mozne pres
            # webovou stranku ale pouze pomoci posilani requestu primo - napr. Postman etc.)
            if ($review['reviewer_id'] != $this->session->getUserId()) {
                throw new Exception('Access denied');
            }

            $saveReviewModel->saveReview(); # jinak ulozeni recenze
            $review = $this->reviewModel->getReview($saveReviewModel->reviewId); # ziskame recenzi znovu
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        $fragment = $this->getRenderedView(self::REVIEW_FORM_FRAGMENT, ['review' => $review, 'guide' => $guide,
            'result' => 'Review was saved successfully']);
        $response = ['fragment' => $fragment];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

}
