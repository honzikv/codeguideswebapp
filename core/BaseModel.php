<?php


namespace app\core;

/**
 * Reprezentuje zakladni model, ktereho extenduji vsechny specificke modely.
 * Model v teto aplikaci validuje uzivatelsky vstup a ma pristup do databaze pomoci tohoto BaseModelu
 * Class BaseModel
 * @package app\core
 */
abstract class BaseModel {

    const CHARACTERS_NUMBERS_REGEX = '/^[a-zA-Z0-9]+$/'; # regex pro uzivatelska jmena
    const CHARACTERS_FILE_REGEX = '/^[a-zA-Z0-9.\-_\[\+\]\(\)]+.pdf$/'; # regex pro nazev souboru
    const GUIDE_NAME_REGEX = '/^[a-z\sA-z0-9]+$/';

    /**
     * Nacte data z asociativniho pole $data. Data se ulozi do jednotlivych promennych ve tride podle nazvu.
     * @param $data
     */
    function loadData($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract function validate(); # funkce pro validaci

    /**
     * Funkce pro prepared statement - vyuziva prepare funkci z databaze
     * @param $statement statement, ktery se bude provadet
     * @return false|\PDOStatement - vraci query nebo false
     */
    function prepare($statement) {
        return Application::getInstance()->getDatabase()->prepare($statement);
    }

    /**
     * Funkce pro zjisteni existence v databazi - vhodne pro osetreni zakladnich chyb
     * @param $table - tabulka v db
     * @param $column - sloupec v tabulce
     * @param $value - hodnota ve sloupci
     * @return bool - true pokud existuje, jinak false
     */
    protected function existsInDatabase($table, $column, $value): bool {
        $statement = "SELECT 1 FROM $table WHERE $column = ?";
        $query = $this->prepare($statement);
        $query->execute([$value]);
        $result = $query->fetchAll();
        return count($result) > 0;
    }

}