<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\BanModel;
use app\model\UserModel;
use Exception;

class ManageUsersController extends BaseController {

    private const VIEW = 'manage_users.twig';

    private UserModel $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    function processBan(Request $request) {

        if (!$this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
            return;
        }

        $user = $this->session->getUserInfo();
        if ($user['role'] != 'publisher') {
            $this->redirectToIndex();
            return;
        }

        $banModel = new BanModel();
        $banModel->loadData($request->getBody());
        try {
            $banModel->validate();
        }
        catch (Exception $exception) {
            $this->redirectToIndex();
            return; # toto by se nikdy nemelo stat, pokud nebudeme posilat post rucne (tzn. mimo stranku)
        }

        $user = $this->userModel->getUserFromId($banModel->userId);
        if ($user['username'] == $this->session->getUsername()) {
            return; # toto take nedava smysl aby slo zabanovat sebe
        }

        $banStatus = $user['banned'] == 1 ? 0 : 1;
        $banModel->banUser($banStatus);

        $this->sendResponse();
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