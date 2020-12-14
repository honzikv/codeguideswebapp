<?php


namespace app\core;


class Request {

    /**
     * Ziskani cesty z $_SERVER[REQUEST_URI]
     */
    function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/'; # path ziskame z REQUEST URI, pokud neexistuje pak je path root
        $position = strpos($path, '?'); # zjisteni pozice otazniku pro query
        return $position === false ? $path : substr($path, 0, $position); # pokud neni vratime path, jinak substring
    }

    /**
     * Ziskani METHOD z $_SERVER
     */
    function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

}