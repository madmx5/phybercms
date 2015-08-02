<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
    'file'  => array
        (
            'driver'           => 'file',
            'cache_dir'        => APPPATH.'cache',
            'default_expire'   => 3600,
            'ignore_on_delete' => array
                (
                    '.gitignore',
                    '.git',
                ),
        ),
);

