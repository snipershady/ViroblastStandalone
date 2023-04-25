<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/path.php';

use App\Component\Request;
use App\Service\SessionService;
use TypeIdentifier\Service\EffectivePrimitiveTypeIdentifierService;

$request = new Request();
$epti = new EffectivePrimitiveTypeIdentifierService();
$session = SessionService::getInstance();

if (!$session->isLoggedIn()) {
    header("location: login.php");
}

