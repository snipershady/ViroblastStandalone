<?php

/**
 * ./vendor/phpunit/phpunit/phpunit  src/tests/UserRepoTestCase.php
 */

namespace App\tests;

use App\Component\ConfigurationHandler;
use App\Entity\User;
use App\Repository\UserRepositoryPDO;
use App\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Description of MyTestCase
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class UserRepoTestCase extends TestCase {

    public function setUp(): void {
        parent::setUp();
        $configurationHandler = new ConfigurationHandler();
        $configurationHandler->setEnviromentDataFromConfig();
        $repo = new UserRepositoryPDO();
        $username = "shady";
        $plainPassword = "forzanapoli";
        $email = "snipershady@gmail.com";
        $repo->initDb($username, $plainPassword, $email);
    }

    public function testLogin(): void {
        $ph = new PasswordHasher();
        $repo = new UserRepositoryPDO();
        $username = "shady";
        $plainPassword = "forzanapoli";
        $user = $repo->findOneUsernameAndPassword($username, $ph->hashPassword($plainPassword));
        $this->assertNotNull($user);
        $this->assertEquals($user::class, User::class);
    }

    public function testFindAll(): void {
        $repo = new UserRepositoryPDO();

        $allUser = $repo->findAll();
        $this->assertNotNull($allUser);
        $this->assertIsArray($allUser);
    }
}
