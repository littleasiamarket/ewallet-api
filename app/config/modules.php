<?php
$application->registerModules(array(
    'user' => array(
        'className' => 'Softco\User\Module',
        'path' => __DIR__ . '/../modules/user/Module.php'
    ),
));
