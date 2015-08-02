<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Asset {

    /**
     * @const   string          Directory where assets are stored
     */
    const PATH_NAME = 'assets';

    /**
     * Return the filename of an asset
     *
     * @param   string          Asset filename
     * @return  string
     */
    public static function filename($filename)
    {
        return $filename;
    }

    /**
     * Return the mimetype of a buffer (string)
     *
     * @param   string          Asset buffer
     * @return  string
     */
    public static function mimetype_buffer($contents)
    {
        if ( ! class_exists('finfo'))
        {
            return 'application/octet-stream';
        }

        $finfo = new finfo(FILEINFO_MIME);

        if ( ($mime = $finfo->buffer($contents)) === FALSE)
        {
            return FALSE;
        }

        $type = explode(';', $mime);

        return Arr::get($type, 0, 'application/octet-stream');
    }

    /**
     * Return the mimetype of an uploaded file
     *
     * @param   array           Asset upload ($_FILES item)
     * @return  string
     */
    public static function mimetype_upload(array $upload)
    {
        $filename = Arr::get($upload, 'tmp_name', FALSE);

        if ( ! file_exists($filename) OR ! is_readable($filename))
        {
            return FALSE;
        }

        return Asset::mimetype_buffer( file_get_contents($filename) );
    }

    /**
     * Return the full filesystem path an asset directory
     *
     * @param   string          Asset directory
     * @return  string
     */
    public static function pathname($directory = NULL)
    {
        $pathname = DOCROOT . Asset::PATH_NAME . DIRECTORY_SEPARATOR;

        if ($directory !== NULL)
        {
            $pathname .= $directory . DIRECTORY_SEPARATOR;
        }

        return $pathname;
    }

    /**
     * Return assets by slug
     *
     * @param   string          Asset slug name
     * @return  array
     */
    public static function get($slug)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('-', func_get_args());

        if ($asset = $cache->get($c_key))
        {
            return $asset;
        }

        $asset = ORM::factory('Asset')
            ->where('slug', '=', $slug)
            ->with('Asset')
            ->find();

        $items = array();

        foreach ($asset->items->find_all() as $item)
        {
            $items[] = $item->as_array();
        }

        $cache->set($c_key, $items);

        return $asset;
    }

    /**
     * Generate a uri to an asset item
     *
     * @param   Asset_Item      Asset item
     * @return  string
     */
    public static function uri(Model_Asset_Item $item)
    {
        if (strpos($item->filename, '://') !== FALSE)
        {
            return $item->filename;
        }

        $pathname = $item->asset->path;
        $filename = Asset::filename($item->filename);

        return Asset::PATH_NAME . '/' . $pathname . '/' . $filename;
    }

    /**
     * Generate a url to an asset item
     *
     * @param   Asset_Item          Asset item
     * @return  string
     */
    public static function url(Model_Asset_Item $item)
    {
        return URL::site( Asset::uri($item) );
    }

    /**
     * Load asset contents from file
     *
     * @param   Model_Asset_Item    Asset item to load
     * @return  string              Contents of asset
     * @return  NULL                On failure
     */
    public static function contents(Model_Asset_Item $item)
    {
        $filename = Asset::filename($item->filename);
        $pathname = Asset::pathname($item->asset->path);

        try
        {
            return file_get_contents($pathname . $filename);
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, "Unable to load asset from file: :filename (:message)", array(
                    ':filename' => $pathname . $filename, ':message' => Kohana_Exception::text($e) ));
        }

        return NULL;
    }

    /**
     * Save an asset from a string buffer (usually text)
     *
     * @param   Model_Asset_Item    Asset item to save
     * @param   string              Contents to save
     * @return  boolean
     */
    public static function save_buffer(Model_Asset_Item $item, $contents)
    {
        $filename = Asset::filename($item->filename);
        $pathname = Asset::pathname($item->asset->path);

        try
        {
            Asset::_init_path($pathname);

            file_put_contents($pathname . $filename, $contents);
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, "Unable to save asset to file: :filename (:message)", array(
                    ':filename' => $pathname . $filename, ':message' => Kohana_Exception::text($e) ));

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Save an asset from upload
     *
     * @param   Model_Asset_Item    Asset item to save
     * @param   array               Upload array from $_FILES
     * @return  boolean
     */
    public static function save_upload(Model_Asset_Item $item, array $upload)
    {
        $filename = Asset::filename($item->filename);
        $pathname = Asset::pathname($item->asset->path);
    
        Asset::_init_path($pathname);

        return Upload::save($upload, $filename, $pathname);
    }

    /**
     * Initialize an asset path by creating the directory if needed
     *
     * @param   string          Asset directory
     * @return  void
     */
    protected static function _init_path($directory)
    {
        if ( ! is_dir($directory))
        {
            // Create the directory
            mkdir($directory, 02775);

            // Set permissions (must be manually set to fix umask issues)
            chmod($directory, 02775);
        }
    }
}

