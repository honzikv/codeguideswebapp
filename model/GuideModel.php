<?php


namespace app\model;


use app\core\Application;
use app\core\BaseModel;
use app\core\Request;
use Exception;

class GuideModel extends BaseModel {

    var string $guideName;
    var string $guideAbstract;

    const NAME_LIMIT = 60;
    const MEDIUM_TEXT_LIMIT_CHARACTERS = 30000;
    const FILE_MAX_SIZE = 10 * 1024 * 1024 * 1024; # max 10 MB


    function uploadFile(Request $request) {
        $file = $request->getMultipart('pdfFile');

        if (empty($file)) {
            throw new Exception('Error file is empty');
        }

        if (strlen($file['name']) > self::NAME_LIMIT) {
            throw new Exception('File name is too long (max ' . self::NAME_LIMIT . ' characters)');
        }

        if (!preg_match(parent::CHARACTERS_NUMBERS_REGEX, $file['name'])) {
            throw new Exception('Invalid file name (only letters, numbers and _ are allowed');
        }

        if ($file['size'] > self::FILE_MAX_SIZE) {
            throw new Exception('Error, file is too large (max 10 MB)');
        }

        if (pathinfo($file['name'], PATHINFO_EXTENSION) != 'pdf') {
            throw new Exception('Error, specified file was not PDF');
        }

        $filename = $file['name'];
        $path = Application::$FILES_PATH . "$filename";

        if (file_exists($path)) {
            throw new Exception('Error, specified file already exists, please rename it');
        }

        move_uploaded_file($file['tmp_name'], Application::$FILES_PATH . "$filename");
        return $filename;
    }

    function addGuideToDatabase($fileName, $userId) {
        $guideStateReviewed = $this->getGuideState('reviewed');
        $statement = 'INSERT INTO guide (name, abstract, filename, user_id, guide_state) VALUES (?, ?, ?, ?, ?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideName, $this->guideAbstract, $fileName, $userId, $guideStateReviewed['id']]);
    }

    function getGuideState($guideState): array {
        $statement = 'SELECT * FROM guide_state_lov WHERE state = (?)';
        $query = $this->prepare($statement);
        $query->execute([$guideState]);
        return $query->fetch();
    }

    function validate() {
        if (empty($this->guideName)) {
            throw new Exception('Error, name of the guide is empty');
        }

        if (strlen($this->guideName) > self::NAME_LIMIT) {
            throw new Exception('Name of the guide is too long, max ' . self::NAME_LIMIT . ' characters allowed');
        }

        if ($this->existsInDatabase('guide','name',$this->guideName)) {
            throw new Exception('Error, this guide name already exists in the database');
        }

        if (!preg_match(parent::CHARACTERS_NUMBERS_REGEX, $this->guideName)) {
            throw new Exception('Invalid characters in the name of the guide');
        }

        if (strlen($this->guideAbstract) > self::MEDIUM_TEXT_LIMIT_CHARACTERS) {
            throw new Exception('Error, abstract is too long, max');
        }

        if (!preg_match(parent::CHARACTERS_NUMBERS_REGEX, $this->guideAbstract)) {
            throw new Exception('Error, invalid characters in the abstract');
        }
    }

    function getFormData() {
        return [
            'guideName' => $this->guideName,
            'guideAbstract' => $this->guideAbstract
        ];
    }

}