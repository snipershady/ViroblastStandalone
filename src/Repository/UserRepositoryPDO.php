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

        $stm = $db->prepare('SELECT id, username, email, password, roles FROM user WHERE id = :id');
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

        $stm = $db->prepare('SELECT id, username, email, password, roles FROM user WHERE username = :username AND password = :password');
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
     * 
     * {@inheritDoc}
     */
    public function save(User $user): bool {
        
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function update(User $user): bool {
        
    }
}
