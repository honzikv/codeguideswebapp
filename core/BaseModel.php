<?php


namespace app\core;


abstract class BaseModel {

    const CHARACTERS_NUMBERS_REGEX = 'w+';

    function loadData($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract function validate();

    function prepare($statement) {
        return Application::getInstance()->getDatabase()->prepare($statement);
    }

}