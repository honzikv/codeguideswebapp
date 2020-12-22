<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\ManageReviewsModel;
use Exception;

class ManageReviewsController extends BaseController {

    private const VIEW = 'manage_reviews.twig';

    function render(Request $request) {
        $manageReviewsModel = new ManageReviewsModel();

        try {
            $manageReviewsModel->loadData($request->getVariables());
            $manageReviewsModel->validate();
        }
        catch (Exception $exception) {
            //todo
        }


    }
}