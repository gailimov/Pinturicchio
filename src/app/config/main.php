<?php

return array(
    'basePath' => __DIR__ . '/..',
    //'controllersDirectory' => 'controllers',
    //'viewRenderer' => 'app.components.ViewRenderer',
    
    /*'views' => array(
        'directory' => 'app.views',
        'layoutDirectory' => 'layouts',
        'layout' => 'main',
        'fileExtension' => '.php',
        'contentKey' => 'content'
    ),*/
    
    'urlScheme' => array(
        'home' => array('^$', 'Site::index'),
        'greeting' => array('^hello/(?P<name>[-_a-z0-9]+)$', 'Site::greet')
    )
);
