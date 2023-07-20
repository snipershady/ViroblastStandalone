<?php

namespace App\Service;

/**
 * Description of PasswordHasher
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
final class PasswordHasher {

    public function hashPassword(string $plainPassword): string {
        $salt = "";
        return hash('sha3-512', $plainPassword . $salt);
    }
}
