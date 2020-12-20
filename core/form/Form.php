<?php

namespace app\core\form;

use app\core\BaseModel;

class Form {

    static function begin($action, $method) {
        echo sprinf('<form action="%s" method="%s" >',$action, $method);
        return new Form();
    }

    static function end() {
        return '</form>';
    }

    function field(BaseModel $model, $attribute) {
        return new Field($model, $attribute);
    }
}