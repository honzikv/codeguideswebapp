<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\AssignReviewModel;
use app\model\BanModel;
use app\model\DeleteUserModel;
use app\model\GuideModel;
use app\model\ManageReviewsModel;
use app\model\RoleChangeModel;
use app\model\UserModel;
use Exception;

class PublisherController extends BaseController {

    private const MANAGE_CONTENT_VIEW = 'manage_content.twig';
    private const MANAGE_REVIEWS_VIEW = 'manage_reviews.twig';
    private const MANAGE_REVIEWS_TABLE_FRAGMENT = 'fragment/manage_reviews_fragment.twig';
    private const MANAGE_USERS_VIEW = 'manage_users.twig';

    private UserModel $userModel;
    private GuideModel $guideModel;

    private function redirectIfNotPublisher(): bool {
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
            return true;
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectToIndex();
            return true;
        }

        return false;
    }

    function __construct() {
        parent::__construct();

        $this->userModel = new UserModel();
        $this->guideModel = new GuideModel();
    }

    function renderManageContent() {
        if ($this->redirectIfNotPublisher()) {
            return;
        }

        $allReviewableGuides = $this->guideModel->getAllReviewableGuides();
        foreach ($allReviewableGuides as $guide) {
            $reviews = $this->guideModel->getGuideReviews($guide['guide_id']);
            $guide['reviews'] = $reviews;
        }

        $this->__render(self::MANAGE_CONTENT_VIEW, ['guides' => $allReviewableGuides]);
    }

    function renderManageReviews(Request $request) {
        if ($this->redirectIfNotPublisher()) {
            return;
        }
        $manageReviewsModel = new ManageReviewsModel();

        try {
            $manageReviewsModel->loadData($request->getVariables());
            $manageReviewsModel->validate();
        } catch (Exception $exception) {
            $this->redirectTo404();
            return;
        }

        $reviews = $manageReviewsModel->getGuideReviews();
        $reviewers = $this->userModel->getAllReviewers();
        $guide = $this->guideModel->getGuide($manageReviewsModel->guideId);

        if ($guide == false) {
            $this->redirectTo404();
        }

        if ($reviews == false) {
            $reviews = [];
        }
        $this->__render(self::MANAGE_REVIEWS_VIEW, ['guide' => $guide, 'reviews' => $reviews, 'reviewers' => $reviewers]);
    }

    function assignReview(Request $request) {
        if ($this->redirectIfNotPublisher()) {
            return;
        }
        $assignReviewModel = new AssignReviewModel();
        $assignReviewModel->loadData($request->getBody());

        $error = '';
        try {
            $assignReviewModel->validate();
            $assignReviewModel->assignReview();
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        $reviews = $this->guideModel->getGuideReviews($assignReviewModel->guideId);
        $reviewers = $this->userModel->getAllReviewers();
        $guide = $this->guideModel->getGuide($assignReviewModel->guideId);
        $response = ['fragment' => $this->getRenderedView(
            self::MANAGE_REVIEWS_TABLE_FRAGMENT, [
            'error' => $error,
            'guide' => $guide,
            'reviews' => $reviews,
            'reviewers' => $reviewers
        ])];

        $response = json_encode($response);
        $this->sendResponse($response);

    }

    function processBan(Request $request) {
        if ($this->redirectIfNotPublisher()) {
            return;
        }

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
        if ($this->redirectIfNotPublisher()) {
            return;
        }

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
        if ($this->redirectIfNotPublisher()) {
            return;
        }

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
        if ($this->redirectIfNotPublisher()) {
            return;
        }

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