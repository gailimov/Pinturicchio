<?php

error_reporting(E_ALL);

// Change the following paths if necessary
$pinturicchio = __DIR__ . '/../pinturicchio/FrontController.php';
$config = __DIR__ . '/../app/config/main.php';

require_once $pinturicchio;
\pinturicchio\FrontController::getInstance($config)->dispatch();
