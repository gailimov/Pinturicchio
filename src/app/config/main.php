<?php

return array(
    //'controllersDirectory' => 'controllers',
    //'viewRenderer' => 'app.components.ViewRenderer',
    
    /*'views' => array(
        'directory' => 'app.views',
        'layoutDirectory' => 'layouts',
        'layout' => 'main',
        'fileExtension' => '.php',
        'contentKey' => 'content',
        'helpersOptions' => array(
            'directory' => 'app.views.helpers',
            'namespace' => 'app\views\helpers'
        )
    ),*/
    
    'routes' => array(
        'home' => array('^$', 'Site::index'),
        'greeting' => array('^hello/(?P<name>[-_a-z0-9]+)$', 'Site::greet')
    )
);
