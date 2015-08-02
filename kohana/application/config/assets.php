<?php defined('SYSPATH') OR die('No direct script access.');

return array
(
    'css' => array
        (
            'admin' => array
                (
                    'files' => array
                        (
                            DOCROOT.'assets/css/bootstrap.css',
                            DOCROOT.'assets/css/app-admin.css',
                            DOCROOT.'assets/css/bootstrap-responsive.css',
                            DOCROOT.'assets/css/datepicker.css',
                            DOCROOT.'assets/css/icon-extra.css',
                        ),
                    'slugs' => array(),
                ),

            'app'   => array
                (
                    'files' => array
                        (
                            DOCROOT.'assets/css/bootstrap.css',
                            DOCROOT.'assets/css/jquery.fancybox.css',
                            DOCROOT.'assets/css/joyride-2.1.css',
                            DOCROOT.'assets/css/bootstrap-responsive.css',
                        ),
                    'slugs' => array
                        (
                            'stylesheets',
                        ),
                ),
        ),

    'js'  => array
        (
            'admin' => array
                (
                    'files' => array
                        (
                            DOCROOT.'assets/js/bootstrap.js',
                            DOCROOT.'assets/js/app-plugins.js',
                            DOCROOT.'assets/js/app-admin.js',
                            DOCROOT.'assets/js/holder.js',
                            DOCROOT.'assets/js/jquery-ui-1.10.0.custom.js',
                            DOCROOT.'assets/js/bootstrap-datepicker.js',
                        ),
                    'slugs' => array(),
                ),

            'app'   => array
                (
                    'files' => array
                        (
                            DOCROOT.'assets/js/bootstrap.js',
                            DOCROOT.'assets/js/jquery.fancybox.js',
                            DOCROOT.'assets/js/jquery.joyride-2.1.js',
                            DOCROOT.'assets/js/jquery.countdown.min.js',
                            DOCROOT.'assets/js/handlebars.runtime.js',
                        ),
                    'slugs' => array
                        (
                            'javascripts',
                        ),
                ),
        ),
);

