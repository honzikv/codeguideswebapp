<?php


namespace app\core;


/**
 * Trida, ktera slouzi jako wrapper pro response - umi pouze nastavit status code
 * Class Response
 * @package app\core
 */
class Response {

    public function setStatusCode(int $statusCode) {
        http_response_code($statusCode);
    }
}