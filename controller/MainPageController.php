<?php


namespace app\controller;


use app\core\BaseController;
use app\model\GuideModel;

class MainPageController extends BaseController {

    private const VIEW = 'index.twig';

    private GuideModel $guideModel;

    public function __construct() {
        parent::__construct();
        $this->guideModel = new GuideModel();
    }

    function render() {
        $guides = $this->guideModel->getPublishedGuides(10);
        $this->__render(self::VIEW, ['guides' => $guides]);
    }
}