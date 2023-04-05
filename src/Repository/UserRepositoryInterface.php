<?php

namespace App\Repository;

use App\Entity\User;

/**
 * Description of UserRepositoryInterface
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
interface UserRepositoryInterface {

    /**
     * 
     * @param int $id
     * @return User|null
     */
    function findOneById(int $id): ?User;
}
