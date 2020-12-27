<?php

namespace app\model;

use app\core\BaseModel;
use Exception;
use PDOException;

class RegistrationModel extends BaseModel {

    private const USERNAME_LIMIT = 30;
    private const EMAIL_LIMIT = 255;
    private const PASSWORD_LIMIT = 255;

    # role
    private const AUTHOR = 'author';
    private const REVIEWER = 'reviewer';
    private const PUBLISHER = 'publisher';

    var string $username;
    var string $email;
    var string $password;
    var string $role;

    function roleMatches($input): bool {
        return $input == self::AUTHOR || $input == self::REVIEWER || $input == self::PUBLISHER;
    }

    function getFormData(): array {
        return [
            'username' => $this->username ?? '',
            'email' => $this->email ?? '',
            'role' => $this->role ?? ''
        ];
    }

    function validate() {
        if (empty($this->username)) {
            throw new Exception('Username is empty');
        }

        if (strlen($this->username) > self::USERNAME_LIMIT) {
            throw new Exception('User name is too long (max ' . self::USERNAME_LIMIT . " characters).");
        }

        if (!preg_match(self::CHARACTERS_NUMBERS_REGEX, $this->username)) {
            throw new Exception('Error, invalid symbol in username');
        }

        if (empty($this->email)) {
            throw new Exception('Email is empty.');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }

        if (strlen($this->email) > self::EMAIL_LIMIT) {
            throw new Exception('Email is too long (max ' . self::EMAIL_LIMIT . " characters).");
        }

        if (empty($this->role)) {
            throw new Exception('Error, role is not selected.');
        }

        if (!$this->roleMatches($this->role)) {
            throw new Exception('Error, invalid role.');
        }

        if (empty($this->password)) {
            throw new Exception('Error, password is empty.');
        }

        if (strlen($this->password) > 255) {
            throw new Exception('Error, password is too long (max ' . self::PASSWORD_LIMIT . " characters).");
        }

        if ($this->existsInDatabase('USER','username', $this->username)) {
            throw new Exception('Error username is taken');
        }

        if ($this->existsInDatabase('USER', 'email', $this->email)) {
            throw new Exception('Error, email is taken');
        }

    }

    private function getAllUsers(): array {
        $statement = 'SELECT * FROM user';
        $query = $this->prepare($statement);
        $query->execute();
        return $query->fetchAll();
    }

    private function getRoleId($roleString) {
        $statement = 'SELECT * FROM role_lov';
        $query = $this->prepare($statement);
        $query->execute();
        foreach ($query->fetchAll() as $row) {
            $id = $row['id'];
            $role = $row['role'];
            if ($role == $roleString) {
                return $id;
            }
        }

        throw new Exception("Error, no such role in the db"); # nemelo by se stat
    }

    public function register() {
        $roleId = $this->getRoleId($this->role);

        $statement = 'INSERT INTO USER (username, password, email, role_id) VALUES (?, ?, ?, ?)';
            $query = $this->prepare($statement);
            $query->execute([$this->username, password_hash($this->password, PASSWORD_DEFAULT),
                $this->email, $roleId]);
    }
}