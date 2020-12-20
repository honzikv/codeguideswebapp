<?php


namespace app\core;


abstract class BaseModel {

    function loadData($data) {

        var_dump($data);
        # Data muzeme nacist tak, ze iterujeme pres dany objekt a ukladame hodnoty
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