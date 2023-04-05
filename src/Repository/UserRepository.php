<?php

namespace App\Repository;

use App\Entity\User;

/**
 * Description of UserRepository
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class UserRepository implements UserRepositoryInterface {

    public function findOneById(int $id): ?User {
        $user = new User();
        $user
                ->setId($id)
                ->setRoles(["ROLE_USER"]);
        return $user;
    }

}
