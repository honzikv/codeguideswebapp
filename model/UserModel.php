<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class UserModel extends BaseModel {

    const TABLE_NAME = 'user';

    # sloupce
    const ID = 'id';
    const USERNAME = 'username';
    const EMAIL = 'email';
    const RANK = 'rank';
    const PREFERRED_ROLE = 'preferred_role';

    function validate() {
        throw new Exception("Invalid validate call");
    }

    function getUserFromUsername($username) {
        $statement = 'SELECT id, username, password, email, role_id, banned FROM USER WHERE username = (?)';
        $query = $this->prepare($statement);
        $query->execute([$username]);
        return $query->fetch();
    }

    function getUserFromId($userId) {
        $statement = 'SELECT id, username, email, role_id, banned FROM USER WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetch();
    }

    function getRole($roleId) {
        $statement = 'SELECT role FROM ROLE_LOV where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleId]);
        return $query->fetch();
    }

    function getRoleId($roleString) {
        $statement = 'SELECT id FROM role_lov where role = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleString]);
        return $query->fetch()['id'];
    }

    function getAllUsersWithRoles() {
        $statement = 'SELECT user.id, username, role, email, banned FROM user INNER JOIN 
                        role_lov role_lov ON user.role_id = role_lov.id';
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
        $statement = 'SELECT * FROM guide INNER JOIN guide_state_lov guide_state_lov on 
                        guide.guide_state = guide_state_lov.id WHERE user_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    /**
     * Ziska vsechny reviewers
     */
    function getAllReviewers(): array {
        $reviewerRoleId = $this->getRoleId('reviewer');
        $statement = 'SELECT id, username, role_id FROM user where role_id = (?) and banned = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewerRoleId, false]);
        return $query->fetchAll();
    }

    function getChangeableRoles(): array {
        $statement = 'SELECT * FROM role_lov WHERE role = (?) OR role = (?)';
        $query = $this->prepare($statement);
        $query->execute(['author', 'reviewer']);
        return $query->fetchAll();
    }
}