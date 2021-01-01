<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class SaveReviewModel extends BaseModel {
    var string $reviewId;
    var string $infoScore;
    var string $complexityScore;
    var string $efficiencyScore;
    var string $qualityScore;
    var string $overallScore;
    var string $notes;


    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, review id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error, review id is not a number');
        }

        if (empty($this->infoScore)) {
            throw new Exception('Error, info score is empty');
        }

        if (empty($this->complexityScore)) {
            throw new Exception('Error, complexity score is empty');
        }

        if (empty($this->efficiencyScore)) {
            throw new Exception('Error, efficiency score is empty');
        }

        if (empty($this->qualityScore)) {
            throw new Exception('Error, quality score is empty');
        }

        if (empty($this->overallScore)) {
            throw new Exception('Error, overall score is empty');
        }

        if (!is_numeric($this->infoScore)) {
            throw new Exception('Error, info score is not a number');
        }

        if (!is_numeric($this->complexityScore)) {
            throw new Exception('Error, complexity score is not a number');
        }

        if (!is_numeric($this->efficiencyScore)) {
            throw new Exception('Error, efficiency score is not a number');
        }

        if (!is_numeric($this->qualityScore)) {
            throw new Exception('Error, quality score is not a number');
        }

        if (!is_numeric($this->overallScore)) {
            throw new Exception('Error, overall score is not a number');
        }

        $scores = [$this->infoScore, $this->complexityScore, $this->efficiencyScore, $this->qualityScore, $this->overallScore];

        foreach ($scores as $score) {
            $asNumber = (int)$score;
            if ($asNumber < 1 || $asNumber > 10) {
                throw new Exception('Error, invalid score value');
            }
        }

        if (!$this->existsInDatabase('review', 'id', $this->reviewId)) {
            throw new Exception('Error, this review id does not exist, please refresh the page');
        }
    }

    function saveReview() {
        $statement = 'UPDATE review SET info_score = (?), efficiency_score = (?), complexity_score = (?),
                      quality_score = (?), overall_score = (?), notes = (?), is_finished = true WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->infoScore, $this->efficiencyScore,
            $this->complexityScore, $this->qualityScore, $this->overallScore, $this->notes, $this->reviewId]);
    }
}