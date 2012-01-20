<?php

error_reporting(E_ALL);

require_once __DIR__ . '/pinturicchio/Loader.php';

use pinturicchio\Loader,
    pinturicchio\Registry,
    pinturicchio\FrontController;

$loader = new Loader();
$loader->setPath(__DIR__)
       ->registerAutoload();

Registry::set('rootPath', __DIR__);
Registry::set('appPath', Registry::get('rootPath') . '/app');

$frontController = new FrontController();
$frontController->dispatch();
