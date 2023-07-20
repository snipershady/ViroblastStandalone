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

    /**
     * 
     * @param string $username
     * @param string $password
     * @return User|null
     */
    function findOneUsernameAndPassword(string $username, string $password): ?User;

    /**
     * 
     * @param User $user
     * @return User|null
     */
    function save(User $user): ?User;

    /**
     * @param User $user
     * @return bool
     */
    function update(User $user): bool;

    /**
     * 
     * @return bool
     */
    function initDb(string $username, string $password, string $email): bool;
}
