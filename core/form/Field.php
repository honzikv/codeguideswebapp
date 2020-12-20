<?php


namespace app\core\form;


use app\core\BaseModel;

class Field {

    private BaseModel $model;
    public string $attribute;

    /**
     * @param BaseModel $model
     * @param string $attribute
     */
    public function __construct(BaseModel $model, string $attribute) {
        $this->model = $model;
        $this->attribute = $attribute;
    }


}