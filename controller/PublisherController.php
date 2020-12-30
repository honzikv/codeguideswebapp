<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\AssignReviewModel;
use app\model\BanModel;
use app\model\DeleteReviewModel;
use app\model\DeleteUserModel;
use app\model\FinalizeGuideModel;
use app\model\GuideModel;
use app\model\ManageReviewsModel;
use app\model\ReviewModel;
use app\model\RoleChangeModel;
use app\model\UserModel;
use Exception;

class PublisherController extends BaseController {

    private const MANAGE_CONTENT_VIEW = 'manage_content.twig'; # view s guides
    private const MANAGE_REVIEWS_VIEW = 'manage_reviews.twig'; # view s recenzemi

    #  fragment s tabulkou recenzi pro manage reviews view
    private const MANAGE_REVIEWS_TABLE_FRAGMENT = 'fragment/manage_reviews_fragment.twig';
    private const MANAGE_USERS_VIEW = 'manage_users.twig'; # view pro spravu uzivatelu

    # fragment pro spravu recenzi - prijato
    private const GUIDE_PUBLISHED_FRAGMENT = 'fragment/guide_published_fragment.twig';

    # fragment pro spravu recenzi - zamitnuto
    private const GUIDE_REJECTED_FRAGMENT = 'fragment/guide_rejected_fragment.twig';
    private UserModel $userModel; # user model pro pristup do db
    private GuideModel $guideModel; # guide model pro pristup do db
    private ReviewModel $reviewModel; # review model pro pristup do db

    /**
     * Presmeruje uzivatele na hlavni stranku pokud neni publisher (admin)
     */
    private function redirectIfNotPublisher() {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectToIndex();
        }
    }

    function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->guideModel = new GuideModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Render stranky pro spravu guides (ktere lze recenzovat)
     */
    function renderManageContent() {
        $this->redirectIfNotPublisher();

        # ziskame vsechny recenzovatelne guides a pro kazde najdeme recenze
        $allReviewableGuides = $this->guideModel->getAllReviewableGuides();
        foreach ($allReviewableGuides as $guide) {
            $reviews = $this->guideModel->getGuideReviews($guide['guide_id']);
            $guide['reviews'] = $reviews;
        }

        $this->__render(self::MANAGE_CONTENT_VIEW, ['guides' => $allReviewableGuides]);
    }

    function renderManageReviews(Request $request) {
        $this->redirectIfNotPublisher();
        $manageReviewsModel = new ManageReviewsModel();

        try {
            $manageReviewsModel->loadData($request->getVariables());
            $manageReviewsModel->validate();
        } catch (Exception $exception) {
            $this->redirectTo404();
            return;
        }

        $reviews = $this->guideModel->getGuideReviewsWithReviewers($manageReviewsModel->guideId);
        $usableReviewers = $this->getAllUsableReviewers($reviews, $this->session->getUserId());
        $guide = $this->guideModel->getGuide($manageReviewsModel->guideId);


        $publishedId = $this->guideModel->getGuideState('published')['id'];
        if ($guide == false || $guide['guide_state'] == $publishedId) {
            $this->redirectTo404();
        }

        if ($reviews == false) {
            $reviews = [];
        }
        $this->__render(self::MANAGE_REVIEWS_VIEW, ['guide' => $guide,
            'reviews' => $reviews, 'reviewers' => $usableReviewers]);
    }

    private function getAllUsableReviewers($reviews, $authorId): array {
        $allReviewers = $this->userModel->getAllReviewers();

        # filtr pro pouzitelne recenzenty aby nemohl uzivatel vybrat nekoho vicekrat
        $usableReviewers = [];
        foreach ($allReviewers as $reviewer) {
            $used = false;
            foreach ($reviews as $alreadyUsed) {
                if ($reviewer['id'] == $alreadyUsed['reviewer_id'] || $reviewer['id'] == $authorId) {
                    $used = true;
                    break;
                }
            }

            if (!$used) {
                array_push($usableReviewers, $reviewer);
            }
        }
        return $usableReviewers;
    }

    function assignReview(Request $request) {
        $this->redirectIfNotPublisher();
        $assignReviewModel = new AssignReviewModel();
        $assignReviewModel->loadData($request->getBody());

        $error = '';
        try {
            $assignReviewModel->validate();
            $assignReviewModel->assignReview();
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        $guideReviews = $this->guideModel->getGuideReviewsWithReviewers($assignReviewModel->guideId);
        $guide = $this->guideModel->getGuide($assignReviewModel->guideId);
        $usableReviewers = $this->getAllUsableReviewers($guideReviews, $guide['user_id']);
        $response = ['fragment' => $this->getRenderedView(
            self::MANAGE_REVIEWS_TABLE_FRAGMENT, [
            'error' => $error,
            'guide' => $guide,
            'reviews' => $guideReviews,
            'reviewers' => $usableReviewers
        ])];

        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function deleteReview(Request $request) {
        $this->redirectIfNotPublisher();

        $deleteReviewModel = new DeleteReviewModel();
        $deleteReviewModel->loadData($request->getBody());

        $error = '';
        try {
            $deleteReviewModel->validate();
            $deleteReviewModel->deleteReview();
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        $guideReviews = $this->guideModel->getGuideReviewsWithReviewers($deleteReviewModel->guideId);
        $usableReviewers = $this->getAllUsableReviewers($guideReviews, $this->session->getUserId());
        $guide = $this->guideModel->getGuide($deleteReviewModel->guideId);
        $response = ['fragment' => $this->getRenderedView(
            self::MANAGE_REVIEWS_TABLE_FRAGMENT, [
            'error' => $error,
            'guide' => $guide,
            'reviews' => $guideReviews,
            'reviewers' => $usableReviewers
        ])];

        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function releaseGuide(Request $request) {
        $this->redirectIfNotPublisher();

        $finalizeGuideModel = new FinalizeGuideModel();

        try {
            $finalizeGuideModel->loadData($request->getBody());
            $finalizeGuideModel->validate();

            # pokud guide neobsahuje 3 dokoncene recenze nelze ji zverejnit
            $reviews = $this->reviewModel->getFinishedReviewsForGuide($finalizeGuideModel->guideId);
            if (empty($reviews) || count($reviews) < 3) {
                throw new Exception('Error, 3 complete reviews required');
            } else {
                $statePublished = $this->guideModel->getGuideState('published')['id'];
                $finalizeGuideModel->acceptGuide($statePublished);
            }
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        $response = ['fragment' => $this->getRenderedView(self::GUIDE_PUBLISHED_FRAGMENT)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function rejectGuide(Request $request) {
        $this->redirectIfNotPublisher();

        $finalizeGuideModel = new FinalizeGuideModel();
        try {
            $finalizeGuideModel->loadData($request->getBody());
            $finalizeGuideModel->validate();
            $finalizeGuideModel->rejectGuide();
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
        }

        $response = ['fragment' => $this->getRenderedView(self::GUIDE_REJECTED_FRAGMENT)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function processBan(Request $request) {
        $this->redirectIfNotPublisher();

        $banModel = new BanModel();
        $banModel->loadData($request->getBody());
        try {
            $banModel->validate();
            $user = $this->userModel->getUserFromId($banModel->userId);
            if ($user['username'] == $this->session->getUsername()) {
                return; # toto take nedava smysl aby slo zabanovat sebe
            }

            $banStatus = $user['banned'] == 1 ? 0 : 1;
            $banModel->banUser($banStatus);
            $user['banned'] = strval($banStatus); # jeste musime zmenit model

            $this->sendResponse(json_encode($user));
        } catch (Exception $exception) {
            $this->redirectToIndex();
            return; # toto by se nikdy nemelo stat, pokud nebudeme posilat post rucne (tzn. mimo stranku)
        }
    }

    function processDeleteUser(Request $request) {
        $this->redirectIfNotPublisher();

        $deleteUserModel = new DeleteUserModel();
        $deleteUserModel->loadData($request->getBody());
        try {
            $deleteUserModel->validate();
            $deleteUserModel->removeUser();
        } catch (Exception  $exception) {
            $this->redirectToIndex();
            return;
        }

        $response = ['message' => 'User was successfully removed'];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function processRoleChange(Request $request) {
        $this->redirectIfNotPublisher();

        $roleChangeModel = new RoleChangeModel();
        $roleChangeModel->loadData($request->getBody());

        try {
            $roleChangeModel->validate();
            $roleChangeModel->changeRole();
            $result = $roleChangeModel->getResult();
            $this->sendResponse(json_encode($result));
        } catch (Exception $exception) {
            $this->redirectToIndex();
            return;
        }
    }

    function renderManageUsers() {
        # spravovat uzivatele smi pouze publisher, tzn ostatni dostanou presmerovani na hlavni stranku
        $this->redirectIfNotPublisher();

        $user = $this->session->getUserInfo();
        $users = $this->userModel->getAllUsersWithRoles();
        # filter uzivatele aby se nemohl sam upravovat
        $filterBy = $user['username'];
        $users = array_filter($users, fn($var) => ($var['username'] != $filterBy));

        # ziska role, ktere lze zmenit - tzn autor a recenzent
        $changeableRoles = $this->userModel->getChangeableRoles();

        $this->__render(self::MANAGE_USERS_VIEW, ['users' => $users, 'roles' => $changeableRoles]);
    }

}