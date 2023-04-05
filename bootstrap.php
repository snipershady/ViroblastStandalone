<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/path.php';

use App\Component\Request;
use App\Service\SessionService;

$request = new Request();

$session = SessionService::getInstance();

if (!$session->isLoggedIn()) {
    header("location: login.php");
}

