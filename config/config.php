<?php

$config = array();

/**
 * Enable showing detailed error messages on application exceptions. Useful for developing.
 */
$config['debug'] = false;

$config['db'] = array(
    'host' => 'localhost',
    'username' => 'username',
    'password' => 'password',
    'db' => 'database',
);

/**
 * Google Analytics config
 */
$config['google_analytics'] = array(
    'account' => '', // UA-xxxxxxxx-x
    'site' => '', // ecdb.net
);

/**
 * Directory where Smarty will store compiled templates
 */
$config['smarty_compile_dir'] = sys_get_temp_dir();
