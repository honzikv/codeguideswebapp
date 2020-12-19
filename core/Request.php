<?php


namespace app\core;


class Request {

    /**
     * Ziskani cesty z $_SERVER[REQUEST_URI]
     */
    function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/'; # path ziskame z REQUEST URI, pokud neexistuje pak je path root
        $position = strpos($path, '?'); # zjisteni pozice otazniku pro query

        # pokud neni vratime path, jinak substring
        return $position === false ? $path : substr($path, 0, $position);
    }

    /**
     * Ziskani METHOD z $_SERVER
     */
    function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    function getBody() {
        $result = [];

        if ($this->getMethod() === 'get') {
            $input = $_GET; # odstraneni nevalidnich znaku pro get metodu
            foreach ($input as $key => $value) {
                $result[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        else if ($this->getMethod() === 'post') {
            $input = $_POST; # odstraneni nevalidnich znaku pro post metodu
            foreach ($input as $key => $value) {
                $result[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $result;
    }



}