<?php

namespace App\Service\Database;

use PDO;
use PDOException;

/**
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
interface DatabasePdo {

    /**
     * @return PDO;
     * @thorws PDOException
     */
    function getConnection(): PDO;
}
