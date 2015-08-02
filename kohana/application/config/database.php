<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
    'default' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
            'hostname'   => 'localhost',
            'database'   => '',
            'username'   => '',
            'password'   => '',
            'persistent' => TRUE,
        ),
        'table_prefix'  => '',
        'charset'       => 'utf8',
        'caching'       => FALSE,
        'profile'       => FALSE,
    ),
);
