<?php

error_reporting(E_ALL | E_STRICT);

require_once __DIR__ . '/../pinturicchio/components/loader/Loader.php';

use pinturicchio\components\loader\Loader,
    pinturicchio\components\Registry,
    pinturicchio\system\FrontController;

$loader = new Loader();
$loader->setPath(__DIR__ . '/..')
       ->registerAutoload();

Registry::set('rootPath', __DIR__ . '/..');
Registry::set('appPath', Registry::get('rootPath') . '/app');

// For enable debugging set value to true, otherwise set to false
Registry::set('debug', true);

FrontController::getInstance()->dispatch();
