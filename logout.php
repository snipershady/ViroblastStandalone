<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\LoginController;

$logincontroller = new LoginController();
$logincontroller->logout();

