<?php

require_once __DIR__ . '/bootstrap.php';

use App\Controller\UserController;

$uc = new UserController();
$uc->updateRole();
