<?php

return array(
    //'viewRenderer' => 'app.components.ViewRenderer',
    
    /*'views' => array(
        'directory' => 'views',
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
