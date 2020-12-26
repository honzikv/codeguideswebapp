<?php


namespace app\model;


use app\core\Application;
use app\core\BaseModel;
use app\core\TeapotError;
use app\core\Request;
use Exception;
use PDO;

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

    function getGuideState($guideState) {
        $statement = 'SELECT * FROM guide_state_lov WHERE state = (?)';
        $query = $this->prepare($statement);
        $query->execute([$guideState]);
        return $query->fetch();
    }

    function getGuideStateFromId($stateId) {
        $statement = 'SELECT * FROM guide_state_lov WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$stateId]);
        return $query->fetch();
    }

    function getUserGuides($userId) {
        $statement = 'SELECT * FROM guide WHERE user_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    function getAllGuideStates() {
        $statement = 'SELECT * FROM guide_state_lov ORDER BY id';
        $query = $this->prepare($statement);
        $query->execute();
        return $query->fetchAll();
    }

    function validate() {
        if (empty($this->guideName)) {
            throw new Exception('Error, name of the guide is empty');
        }

        if (strlen($this->guideName) > self::NAME_LIMIT) {
            throw new Exception('Name of the guide is too long, max ' . self::NAME_LIMIT . ' characters allowed');
        }

        if ($this->existsInDatabase('guide', 'name', $this->guideName)) {
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

    function getAllReviewableGuides() {
        $reviewedId = $this->getGuideState('reviewed')['id']; # id pro reviewed stav
        $statement = 'SELECT name, username, guide.id as guide_id, user_id as user_id FROM guide INNER JOIN user 
    user ON guide.user_id = user.id WHERE guide_state = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewedId]);
        return $query->fetchAll();
    }

    function getGuideReviews($guideId) {
        $statement = 'SELECT * FROM review WHERE guide_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$guideId]);
        return $query->fetchAll();
    }

    function getGuideReviewsWithReviewers($guideId) {
        $statement = 'SELECT review.id,
                           info_score, efficiency_score, complexity_score, quality_score, overall_score,
                           notes,
                           is_finished,
                           username,
                           role_id,
       reviewer.id as reviewer_id FROM review INNER JOIN user as reviewer 
           on reviewer.id = review.reviewer_id WHERE guide_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$guideId]);
        return $query->fetchAll();
    }

    function getGuide(string $guideId) {
        $statement = 'SELECT * FROM guide WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$guideId]);
        return $query->fetch();
    }

    function getPublishedGuides(int $count) {
        $statement = 'SELECT * FROM guide ORDER BY RAND() LIMIT 1, :count';
        $query = $this->prepare($statement);
        $query->bindParam(':count', $count, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    function getAllPublishedGuides() {
        try {
            $guideStatePublished = $this->getGuideState('published');
            $statement = 'SELECT * FROM guide WHERE guide_state = (?)';
            $query = $this->prepare($statement);
            $query->execute([$guideStatePublished['id']]);
            return $query->fetchAll();
        }
        catch (Exception $exception) {
            throw new TeapotError();
        }
    }

}