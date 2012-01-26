<?php

error_reporting(E_ALL | E_STRICT);

// Change the following paths if necessary
$frontController = __DIR__ . '/../pinturicchio/system/FrontController.php';
$config = __DIR__ . '/../app/config/main.php';

require_once $frontController;

use pinturicchio\system\FrontController;

FrontController::getInstance()->run($config);
