<?php

//require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

use App\Component\ConfigurationHandler;
use App\Controller\LoginController;

$logincontroller = new LoginController();
$logincontroller->logout();

