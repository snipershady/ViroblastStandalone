<?php

if (PHP_SAPI !== 'cli' || empty($argc) || isset($_SERVER['HTTP_USER_AGENT'])) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Component\ConfigurationHandler;
use App\Repository\UserRepositoryPDO;
use TypeIdentifier\Service\EffectivePrimitiveTypeIdentifierService;
$configurationHandler = new ConfigurationHandler();
$configurationHandler->setEnviromentDataFromConfig();
$epti = new EffectivePrimitiveTypeIdentifierService();

if ($argc < 4) {
    echo "-----------------------------------------------------------" . PHP_EOL;
    echo "Wrong syntax. Please run setup with right arguments" . PHP_EOL;
    echo "-----------------------------------------------------------" . PHP_EOL;
    echo "php setup.php admin_username admin_password admin_email" . PHP_EOL;
    echo "-----------------------------------------------------------" . PHP_EOL;
    exit;
}

$username = $epti->getTypedValueFromArray(1, $argv, true, true);
$password = $epti->getTypedValueFromArray(2, $argv, true, true);
$email = $epti->getTypedValueFromArray(3, $argv, true, true);

$repo = new UserRepositoryPDO();
$repo->initDb($username, $password, $email);

echo "-----------------------------------------------------------" . PHP_EOL;
echo "SETUP COMPLETE, DELETE setup.php from your host" . PHP_EOL;
echo "-----------------------------------------------------------" . PHP_EOL;
