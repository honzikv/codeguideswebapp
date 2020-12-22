<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\BanModel;
use app\model\DeleteUserModel;
use app\model\RoleChangeModel;
use app\model\UserModel;
use Exception;

class ManageUsersController extends BaseController {

    private const VIEW = 'manage_users.twig';

    private UserModel $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

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

    function render() {
        # spravovat uzivatele smi pouze publisher, tzn ostatni dostanou presmerovani na hlavni stranku
        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
            return;
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectToIndex(); # presmerovani, pokud prihlaseny uzivatel neni publisher
            return;
        }

        $users = $this->userModel->getAllUsersWithRoles();
        # filter uzivatele aby se nemohl sam upravovat
        $filterBy = $user['username'];
        $users = array_filter($users, fn($var) => ($var['username'] != $filterBy));

        # ziska role, ktere lze zmenit - tzn autor a recenzent
        $changeableRoles = $this->userModel->getChangeableRoles();

        $this->__render(self::VIEW, ['users' => $users, 'roles' => $changeableRoles]);
    }
}