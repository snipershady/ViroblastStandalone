<?php

/**
 * chgrp -R www-data database
 * chown -R USERNAME database
 * ./vendor/phpunit/phpunit/phpunit  src/tests/RepoTestCase.php -v
 */


namespace App\tests;

use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use SQLite3;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Description of MyTestCase
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class RepoTestCase extends TestCase {
    
    public function setUp(): void {
        parent::setUp();
        $this->initDb();
    }
    
    private function initDb():void {
        $db = new SQLite3('database/bestiabase.db');
        $db->exec("DROP TABLE user");
        $db->exec("CREATE TABLE user(id INTEGER PRIMARY KEY, username TEXT, email TEXT, password TEXT, roles TEXT)");
        $db->exec("INSERT INTO user(username, email, password, roles) VALUES('shady', 'perrini.stefano@gmail.com', 'forzanapoli', '[\"ROLE_ADMIN\", \"ROLE_USER\"]')");
    }

    public function testFindOneById(): void {
        $username = "shady";
        $repo = new UserRepository();
        $user = $repo->findOneById(1);
        //var_dump($user);
        self::assertTrue(!empty($user));
        self::assertEquals(1, $user->getId());
        self::assertEquals($username, $user->getUsername());
    }
    
    public function testFindByUsernameAndPassword(): void {
        $username = "shady";
        $password = "forzanapoli";
        $repo = new UserRepository();
        $user = $repo->findOneUsernameAndPassword($username, $password);
        //var_dump($user);
        self::assertTrue(!empty($user));
        self::assertEquals(1, $user->getId());
        self::assertEquals("shady", $user->getUsername());
    }
    
    public function testFindByUsernameAndPasswordNull(): void {
        $username = "shady";
        $password = "forzanapoli1926";
        $repo = new UserRepository();
        $user = $repo->findOneUsernameAndPassword($username, $password);
        var_dump($user);
        self::assertTrue(true);
    }

}
