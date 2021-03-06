<?php


namespace app\controller;


use app\core\BaseController;
use app\model\GuideModel;

/**
 * Controller pro hlavni stranku
 * Class MainPageController
 * @package app\controller
 */
class MainPageController extends BaseController {

    private const VIEW = 'index.twig';

    private GuideModel $guideModel;

    public function __construct() {
        parent::__construct();
        $this->guideModel = new GuideModel();
    }

    function render() {
        $guides = $this->guideModel->getPublishedGuidesRandom(3);
        $this->__render(self::VIEW, ['guides' => $guides]);
    }
}