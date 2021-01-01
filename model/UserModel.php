<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class UserModel extends BaseModel {

    function validate() { # tento model se nepouziva pro zadnou validaci
        throw new Exception("Invalid validate call");
    }

    function getUserFromUsername($username) {
        $statement = 'SELECT id, username, password, email, role_id, banned FROM USER WHERE username = (?)';
        $query = $this->prepare($statement);
        $query->execute([$username]);
        return $query->fetch();
    }

    /**
     * Ziskani informaci o uzivateli z jeho id
     * @param $userId : id uzivatele
     * @return mixed: zaznam uzivatele bez jeho hesla
     */
    function getUserFromId($userId) {
        $statement = 'SELECT id, username, email, role_id, banned FROM USER WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetch();
    }

    /**
     * Funkce ziska roli z daneho id
     * @param $roleId id role
     * @return mixed : vrati radku z tabulky role pro dane id
     */
    function getRole($roleId) {
        $statement = 'SELECT role FROM ROLE_LOV where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleId]);
        return $query->fetch();
    }

    /**
     * Funkce ziska nazev role z id role
     * @param $roleString: nazev role (sloupec role)
     * @return mixed: vrati id role (pouze id)
     */
    function getRoleId($roleString) {
        $statement = 'SELECT id FROM role_lov where role = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleString]);
        return $query->fetch()['id'];
    }

    /**
     * Ziska vsechny uzivatele s rolemi
     * @return array: uzivatele s rolemi
     */
    function getAllUsersWithRoles() {
        $statement = 'SELECT user.id, username, role, email, banned FROM user INNER JOIN 
                        role_lov role_lov ON user.role_id = role_lov.id ORDER BY user.id';
        $query = $this->prepare($statement);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Ziska vsechny role
     */
    function getAllRoles() {
        $statement = 'SELECT * FROM ROLE_LOV';
        $query = $this->prepare($statement);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Ziska uzivatelske id
     */
    function getUserId($username) {
        $statement = 'SELECT id, username, email, role_id, banned FROM USER where username = (?)';
        $query = $this->prepare($statement);
        $query->execute([$username]);
        return $query->fetch()['id'];
    }

    /**
     * Ziska vsechny guides i s danymi stavy jako stringy - pres inner join
     */
    function getUserGuidesWithStates($userId) {
        $statement = 'SELECT guide.id, name, abstract, filename, guide_state, state FROM guide INNER JOIN guide_state_lov guide_state on 
                        guide.guide_state = guide_state.id WHERE user_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    /**
     * Ziska vsechny recenzenty
     */
    function getAllReviewers(): array {
        $reviewerRoleId = $this->getRoleId('reviewer');
        $statement = 'SELECT id, username, role_id FROM user where role_id = (?) and banned = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewerRoleId, false]);
        return $query->fetchAll();
    }

    /**
     * Ziska vsechny role, ktere lze zmenit - tzn. autor a recenzent
     * @return array pole s radky
     */
    function getChangeableRoles(): array {
        $statement = 'SELECT * FROM role_lov WHERE role = (?) OR role = (?)';
        $query = $this->prepare($statement);
        $query->execute(['author', 'reviewer']);
        return $query->fetchAll();
    }
}