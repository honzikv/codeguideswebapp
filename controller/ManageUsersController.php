<?php


namespace app\controller;


use app\core\BaseController;
use app\model\UserModel;

class ManageUsersController extends BaseController {

    private const VIEW = 'manage_users.twig';

    private UserModel $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
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

        $users = $this->userModel->getAllUsers();
        # filter uzivatele aby se nemohl sam upravovat
        $filterBy = $user['username'];
        $users = array_filter($users, fn($var) => ($var['username'] != $filterBy));
        $mapping = $this->userModel->getAllRoles();

        for ($i = 0; $i < count($users); $i += 1) {
            $users[$i]['role'] = $mapping[((int)$users[$i]['role_id'])]['role'];
        }

        $this->__render(self::VIEW, ['users' => $users]);
    }

}