<?php


namespace app\model;


use app\core\Application;
use app\core\BaseModel;
use Exception;

class DeleteUserModel extends BaseModel {

    var string $userId;

    function validate() {
        if (empty($this->userId)) {
            throw new Exception('Error, user id is empty');
        }

        if (!is_numeric($this->userId)) {
            throw new Exception('Error user id is not a number');
        }
    }

    private function deleteUserGuides($userGuides) {
        foreach ($userGuides as $userGuide) {
            $path = Application::$FILES_PATH + $userGuide['filename'];
            unlink($path);
        }
    }

    function removeUser() {
        $guideModel = new GuideModel();
        $userGuides = $guideModel->getUserGuides($this->userId);

        if ($userGuides !== false) {
            $this->deleteUserGuides($userGuides);
        }

        $statement = 'DELETE FROM user where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->userId]);
    }
}