<?php

namespace App\Service\Database;

use PDO;
use function getenv;

/**
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class DatabaseConnection implements DatabasePdo {

    private ?string $dbhost = null;
    private ?string $dbuser = null;
    private ?string $dbpass = null;
    private ?string $dbname = null;
    private static ?PDO $pdo = null;

    public function __construct() {
        $this->dbhost = getenv("dbhost");
        $this->dbuser = getenv("dbuser");
        $this->dbpass = getenv("dbpass");
        $this->dbname = getenv("dbname");
    }

    private function createconnection(): PDO {
        $dsn = "mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname . ";charset=utf8mb4";
        $pdo = new PDO($dsn, $this->dbuser, $this->dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }

    /**
     * 
     * {@InheritDoc}
     */
    public function getConnection(): PDO {
        if (self::$pdo === null) {
            self::$pdo = $this->createconnection();
        }
        return self::$pdo;
    }
}
