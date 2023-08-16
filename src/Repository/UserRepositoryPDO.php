<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Database\DatabaseConnection;
use App\Service\PasswordHasher;
use Exception;

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

        $stm = $pdo->prepare('SELECT id, username, email, password, roles FROM app_user WHERE id = :id');
        $stm->bindValue(":id", $id);

        $res = $stm->execute();
        if ($res === false) {
            return null;
        }
        $row = $stm->fetch();
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

        try {
            $stm = $pdo->prepare('SELECT id, username, email, password, roles FROM app_user WHERE username = :username AND password = :password');
            $stm->bindValue(":username", $username);
            $stm->bindValue(":password", $password);

            $res = $stm->execute();
            $row = $stm->fetch();
        } catch (Exception $e) {
            return null;
        }
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
    public function save(User $user): ?User {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        $sql = "INSERT INTO app_user VALUES(NULL,:username, :email, :password, :roles )";
        try {
            $stm = $pdo->prepare($sql);
            $stm->bindValue(":username", $user->getUsername());
            $stm->bindValue(":email", $user->getEmail());
            $stm->bindValue(":password", $user->getPassword());
            $stm->bindValue(":roles", json_encode($user->getRoles()));
            $stm->execute();
        } catch (Exception $e) {
            return null;
        }
        $user->setId($pdo->lastInsertId());
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function update(User $user): bool {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        $sql = "UPDATE app_user set 
            username = :username,
            email= :email,
            password= :password,
            roles= :roles
            WHERE id = :id
            ";

        $stm = $pdo->prepare($sql);
        $stm->bindValue(":username", $user->getUsername());
        $stm->bindValue(":email", $user->getEmail());
        $stm->bindValue(":password", $user->getPassword());
        $stm->bindValue(":roles", json_encode($user->getRoles()));
        $stm->bindValue(":id", $user->getId());

        return $stm->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function initDb(string $username, string $password, string $email): bool {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();
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

        $stm = $pdo->prepare($sqlcreate);
        $stm->execute();
        $ph = new PasswordHasher();
        $user = $this->findOneUsernameAndPassword($username, $ph->hashPassword($password));

        if ($user === null) {
            $roles = ["ROLE_ADMIN", "ROLE_USER"];
            $user = new User();
            $user
                    ->setEmail($email)
                    ->setPassword($ph->hashPassword($password))
                    ->setRoles($roles)
                    ->setUsername($username);
            $this->save($user);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        try {
            $stm = $pdo->prepare('SELECT id, username, email, password, roles FROM app_user');

            $res = $stm->execute();
            $resultSet = $stm->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
        if (empty($resultSet)) {
            return [];
        }
        $result = [];
        foreach ($resultSet as $row) {
            $user = new User();
            $user
                    ->setId($row["id"])
                    ->setUsername($row["username"])
                    ->setEmail($row["email"])
                    ->setRoles(json_decode($row["roles"]));
            $result[$user->getUsername()] = $user;
        }

        return $result;
    }
}
