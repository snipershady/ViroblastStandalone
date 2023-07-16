<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Database\DatabaseConnection;
use SQLite3;

/**
 * Description of UserRepository
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class UserRepositoryPDO implements UserRepositoryInterface {

    /**
     * 
     * {@inheritDoc}
     */
    public function findOneById(int $id): ?User {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        $stm = $pdo->prepare('SELECT id, username, email, password, roles FROM user WHERE id = :id');
        $stm->bindValue(":id", $id);

        $res = $stm->execute();

        $row = $res->fetchAll();
        if (empty($row)) {
            return null;
        }
        $user = new User();
        $user
                ->setId($row["id"])
                ->setUsername($row["username"])
                ->setEmail($row["email"])
                ->setRoles(json_decode($row["roles"]));
        return $user;
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function findOneUsernameAndPassword(string $username, string $password): ?User {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        $stm = $pdo->prepare('SELECT id, username, email, password, roles FROM app_user WHERE username = :username AND password = :password');
        $stm->bindValue(":username", $username);
        $stm->bindValue(":password", $password);

        $res = $stm->execute();

        $row = $res->fetchAll();
        if (empty($row)) {
            return null;
        }
        $user = new User();
        $user
                ->setId($row["id"])
                ->setUsername($row["username"])
                ->setEmail($row["email"])
                ->setRoles(json_decode($row["roles"]));
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function save(User $user): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function update(User $user): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function initDb(string $username, string $password, string $email): bool {

        $sqlcreate = "CREATE TABLE IF NOT EXISTS app_user(
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                roles VARCHAR(255) NOT NULL,
                INDEX `IDX_app_user_username` (`username`) USING BTREE,
                INDEX `IDX_app_user_email` (`email`) USING BTREE
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
            ";
        $sqlInitAdmin = "INSERT INTO user(username, email, password, roles) VALUES(:username, :email, :password, '[\"ROLE_ADMIN\", \"ROLE_USER\"]')";

        $db = new DatabaseConnection();
        $pdo = $db->getConnection();
        try {
            $stm = $pdo->prepare($sqlcreate);
            $stm->execute();

            $stminitadmin = $pdo->prepare($sqlInitAdmin);
            $stminitadmin->bindValue(":username", $username);
            $stminitadmin->bindValue(":password", $password);
            $stminitadmin->bindValue(":email", $email);
            $stminitadmin->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
