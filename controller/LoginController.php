<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;

class LoginController extends BaseController {

    private $view = 'login';

    function show() {
         $this->render('login');
    }

    function processLogin(Request $request) {
        print "Processing";
    }
}