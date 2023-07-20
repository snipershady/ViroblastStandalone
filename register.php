<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Component\ConfigurationHandler;
use App\Controller\LoginController;
$configurationHandler = new ConfigurationHandler();
$configurationHandler->setEnviromentDataFromConfig();

$logincontroller = new LoginController();
$logincontroller->register();

