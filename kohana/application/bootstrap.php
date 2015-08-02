<?php defined('SYSPATH') OR die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
    // Application extends the core
    require APPPATH.'classes/Kohana'.EXT;
}
else
{
    // Load empty core extension
    require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/New_York');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Composer auto-loader (PSR-0 compliant).
 *
 * @link https://github.com/composer/composer
 */
require_once APPPATH . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
if (function_exists('mb_substitute_character'))
{
    mb_substitute_character('none');
}

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
    Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

if (Kohana::$environment === Kohana::PRODUCTION)
{
    // Turn off noticed and strict errors
    error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
    'base_url'   => (Kohana::$environment === Kohana::PRODUCTION ? 'http://redandfox.com/' : NULL),
    'profile'    => (Kohana::$environment !== Kohana::PRODUCTION),
    'index_file' => '',
    'caching'    => (Kohana::$environment === Kohana::PRODUCTION),
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    // 'auth'       => MODPATH.'auth',       // Basic authentication
    // 'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    // 'database'   => MODPATH.'database',   // Database access
    // 'image'      => MODPATH.'image',      // Image manipulation
    // 'minion'     => MODPATH.'minion',     // CLI Tasks
    // 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'auth'          => MODPATH.'auth',
    'cache'         => MODPATH.'cache',
    'database'      => MODPATH.'database',
    'image'         => MODPATH.'image',
    'minion'        => MODPATH.'minion',
    'orm'           => MODPATH.'orm',
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('admin', 'admin(/<action>)', array('action' => '(login|logout)'))
    ->defaults(array(
        'controller' => 'admin',
        'action'     => 'login',
    ));

Route::set('panel', 'admin(/<controller>(/<action>(/<id>)))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'dashboard',
        'action'     => 'index',
    ));

Route::set('panel_pages', 'admin/pages(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'pages',
        'action'     => 'index',
    ));

Route::set('panel_media', 'admin/media(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'media',
        'action'     => 'index',
    ));

Route::set('panel_menus', 'admin/menus(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'menus',
        'action'     => 'index',
    ));

Route::set('panel_assets', 'admin/assets(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'assets',
        'action'     => 'index',
    ));

Route::set('panel_guests', 'admin/guests(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'guests',
        'action'     => 'index',
    ));

Route::set('panel_instagram', 'admin/instagram(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'instagram',
        'action'     => 'index',
    ));

Route::set('panel_meals', 'admin/meals(/<action>(/<id>))')
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'meals',
        'action'     => 'index',
    ));

Route::set('instagram', 'instagram(/<tag>)')
    ->defaults(array(
        'controller' => 'instagram',
        'action'     => 'ingest',
    ));

Route::set('pages', 'pages(/<action>(/<id>))')
    ->defaults(array(
        'controller' => 'pages',
        'action'     => 'view',
    ));

Route::set('rsvp', 'rsvp(/<action>(/<id>))')
    ->defaults(array(
        'controller' => 'rsvp',
        'action'     => 'index',
    ));

Route::set('wedding_location', 'wedding/location')
    ->defaults(array(
        'controller' => 'wedding',
        'action'     => 'location',
    ));

Route::set('wedding_photos', 'wedding/photos')
    ->defaults(array(
        'controller' => 'pages',
        'action'     => 'view',
        'slug'       => 'wedding-photos',
    ));

Route::set('photostream', 'photostream/<action>', array(
        'action'     => '(after|before)',
    ))
    ->defaults(array(
        'controller' => 'photostream',
    ));

Route::set('default', '(<slug>)')
    ->defaults(array(
        'controller' => 'pages',
        'action'     => 'view',
        'slug'       => 'home',
    ));

