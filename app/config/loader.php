<?php

$loader = new \Phalcon\Loader();

$namespaces = array(
    'Softco\Controllers' => __DIR__ . '/../controllers/',
    'Phalcon\Libraries' => __DIR__ . '/../system/libraries/Phalcon',
    'System\Models' => __DIR__ . '/../system/models/',
    'System\Language' => __DIR__ . '/../system/languages',
    'System\Libraries' => __DIR__ . '/../system/libraries',
    'System\Widgets' => __DIR__ . '/../system/widgets/'
);

$loader->registerNamespaces($namespaces)->register();