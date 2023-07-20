<?php

require_once __DIR__ . '/bootstrap.php';

use App\Controller\LoginController;

$logincontroller = new LoginController();
$logincontroller->logout();

