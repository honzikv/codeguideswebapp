<?php


namespace app\controllers;


use app\core\Controller;

class RegistrationController extends Controller {

    function show() {
        return $this->render('login');
    }

    function processRegistration() {

    }
}