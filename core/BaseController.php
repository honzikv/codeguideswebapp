<?php


namespace app\core;


class BaseController {

    protected Session $session;

    public function __construct() {
        $this->session = Application::getInstance()->getSession();
    }

    protected function __render($view, $params = []) {
        if ($this->session->isUserLoggedIn()) {
            $params['user'] = $this->session->getUserInfo();
            $params['pagename'] = $view;
        }

        echo Application::getInstance()->getTwig()->render($view, $params);
    }

}