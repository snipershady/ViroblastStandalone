<?php

if (PHP_SAPI !== 'cli' || empty($argc) || isset($_SERVER['HTTP_USER_AGENT'])) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Repository\UserRepository;
use App\Repository\UserRepositoryPDO;
use TypeIdentifier\Service\EffectivePrimitiveTypeIdentifierService;

$epti = new EffectivePrimitiveTypeIdentifierService();

if ($argc < 5) {
    echo "-----------------------------------------------------------" . PHP_EOL;
    echo "Wrong syntax. Please run setup with right arguments" . PHP_EOL;
    echo "-----------------------------------------------------------" . PHP_EOL;
    echo "php setup.php admin_username admin_password admin_email mysql" . PHP_EOL;
    echo "-----------------------------------------------------------" . PHP_EOL;
    exit;
}

$username = $epti->getTypedValueFromArray(1, $argv, true, true);
$password = $epti->getTypedValueFromArray(2, $argv, true, true);
$email = $epti->getTypedValueFromArray(3, $argv, true, true);
$dbType = $epti->getTypedValueFromArray(4, $argv, true, true);
if ($dbType === "mysql") {
    $repo = new UserRepositoryPDO();
    $repo->initDb($username, $password, $email);
} else if ($dbType === "sqlite") {
    $repo = new UserRepository();
    $repo->initDb($username, $password, $email);
} else {
    echo "-----------------------------------------------------------" . PHP_EOL;
    echo "Database type can be 'mysql' or 'sqlite'" . PHP_EOL;
    echo "-----------------------------------------------------------" . PHP_EOL;
    exit;
}

echo "-----------------------------------------------------------" . PHP_EOL;
echo "SETUP COMPLETE, DELETE setup.php from your host" . PHP_EOL;
echo "-----------------------------------------------------------" . PHP_EOL;
