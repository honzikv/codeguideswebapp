<?php


namespace app\core;


class Request {

    var $path;
    var $variables;

    public function __construct() {
        $url = $_SERVER['REQUEST_URI'] ?? '/'; # path ziskame z REQUEST URI, pokud neexistuje pak je path root
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $parts = parse_url($url); # pro parsing URL muzeme pouzit funkci parse_url, ze ktere lze ziskat
        if ($parts === false) {
            $this->path = false;
            return;
        }

        $this->path = $parts['path']; # zjisteni path
        if (!preg_match('[/+]', $this->path)) {
            $this->path = rtrim($this->path, '/');
        }

        if (!empty($parts['query'])) {
            $this->variables = $parts['query'];
        }
    }

    /**
     * Ziskani cesty z $_SERVER[REQUEST_URI]
     */
    function getPath() {
        return $this->path;
    }

    /**
     * Ziskani METHOD z $_SERVER
     */
    function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Ziska body, pouzito pouze pro POST request
     * @return array
     */
    function getBody() {
        $result = [];

        if ($this->getMethod() === 'get') {
            $input = $_GET; # odstraneni nevalidnich znaku pro get metodu
            foreach ($input as $key => $value) {
                $result[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        else if ($this->getMethod() === 'post') {
            $input = $_POST; # odstraneni nevalidnich znaku pro post metodu
            foreach ($input as $key => $value) {
               $result[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $result;
    }

    /**
     * Ziska multipart pro ulozeni
     * @param $name
     * @return mixed
     */
    function getMultipart($name) {
        return $_FILES[$name];
    }

    /**
     * Vrati promenne z query url.
     * Napr. localhost/test?myVar1=2 vrati [myVar1 => 2]
     * @return array vraci asociativni pole
     */
    function getVariables() {
        $result = [];
        parse_str($this->variables, $result);
        foreach ($result as $key => $value) {
            $result[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $result;
    }


}