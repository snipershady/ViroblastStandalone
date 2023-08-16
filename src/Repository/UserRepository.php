<?php

namespace App\Repository;

use App\Entity\User;
use SQLite3;

/**
 * Description of UserRepository
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class UserRepository implements UserRepositoryInterface {

    /**
     * 
     * {@inheritDoc}
     */
    public function findOneById(int $id): ?User {
        $db = new SQLite3('database/bestiabase.db');

        $stm = $db->prepare('SELECT id, username, email, password, roles FROM user WHERE id = :id');
        $stm->bindValue(":id", $id, SQLITE3_INTEGER);

        $res = $stm->execute();

        $row = $res->fetchArray(SQLITE3_ASSOC);
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
        $db = new SQLite3('database/bestiabase.db');

        $stm = $db->prepare('SELECT id, username, email, password, roles FROM user WHERE username = :username AND password = :password');
        $stm->bindValue(":username", $username, SQLITE3_TEXT);
        $stm->bindValue(":password", $password, SQLITE3_TEXT);

        $res = $stm->execute();

        $row = $res->fetchArray(SQLITE3_ASSOC);
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
        $db = new SQLite3('database/bestiabase.db');
        $db->exec("DROP TABLE user");
        $db->exec("CREATE TABLE user(id INTEGER PRIMARY KEY, username TEXT, email TEXT, password TEXT, roles TEXT)");
        $db->exec("INSERT INTO user(username, email, password, roles) VALUES($username, $email, $password, '[\"ROLE_ADMIN\", \"ROLE_USER\"]')");
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array {
        return [];
    }
}
