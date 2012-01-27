<?php

$root = __DIR__ . '/../../..';

return array(
    'system' => array(
        'pinturicchio\system\Exception' => $root . '/pinturicchio/system/Exception.php',
        'pinturicchio\system\NotFoundException' => $root . '/pinturicchio/system/NotFoundException.php',
        'pinturicchio\system\FrontController' => $root . '/pinturicchio/system/FrontController.php',
        'pinturicchio\system\Controller' => $root . '/pinturicchio/system/Controller.php',
        'pinturicchio\system\http\Request' => $root . '/pinturicchio/system/http/Request.php',
        'pinturicchio\system\view\helpers\Url' => $root . '/pinturicchio/system/view/helpers/Url.php'
    ),
    'components' => array(
        'pinturicchio\components\Config' => $root . '/pinturicchio/components/Config.php',
        'pinturicchio\components\Router' => $root . '/pinturicchio/components/Router.php',
        'pinturicchio\components\view\Exception' => $root . '/pinturicchio/components/view/Exception.php',
        'pinturicchio\components\view\Renderer' => $root . '/pinturicchio/components/view/Renderer.php',
        'pinturicchio\components\view\PhpRenderer' => $root . '/pinturicchio/components/view/PhpRenderer.php'
    )
);
