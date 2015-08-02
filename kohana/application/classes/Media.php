<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Media helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Media {

    /**
     * @const   string          Directory where media is stored
     */
    const PATH_NAME = 'media';

    /**
     * @var     array           An array mapping mime-types to file extensions
     */
    public static $extension_map = array(
            IMAGETYPE_GIF  => 'gif',
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
        );

    /**
     * @var     array           An list of thumbnail sizes to create
     */
    public static $thumbnail_sizes = array(
            'crop' => array(
                array( 75,  75, Image::INVERSE),
                array(100, 100, Image::INVERSE),
                array(160, 160, Image::INVERSE),
                array(240, 240, Image::INVERSE),
                array(320, 320, Image::INVERSE),
            ),

            'size' => array(
                array( 75,  75, Image::INVERSE),
                array(100, 100, Image::INVERSE),
                array(160, 160, Image::INVERSE),
                array(240, 240, Image::INVERSE),
                array(320, 320, Image::INVERSE),
            ),
        );

    /**
     * Determine the extension a media item would use if saved
     *
     * @param   string          Filename to obtain extension for
     * @return  string
     */
    public static function extension($filename)
    {
        $size = getimagesize($filename);

        if ( ! is_array($size) OR count($size) < 3)
        {
            return FALSE;
        }

        return Arr::get(Media::$extension_map, $size[2], 'jpg');
    }

    /**
     * Generate a unique media filename
     *
     * @param   string          Extension to use for file
     * @return  string
     */
    public static function filename($extension)
    {
        $hash = substr(md5( uniqid() . rand(0, 99)), 0, 24);
        $path = substr($hash, 0, 2);

        return $path . DIRECTORY_SEPARATOR . $hash . '.' . $extension;
    }

    /**
     * Generate the full filesystem path to a media item
     *
     * @param   string          Media item name
     * @return  string
     */
    public static function pathname($media = NULL)
    {
        return DOCROOT . Media::PATH_NAME . DIRECTORY_SEPARATOR . (string) $media;
    }

    /**
     * Return an array of media items
     *
     * @param   string          Media item slug name
     * @return  array           Media items
     */
    public static function get($slug)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('-', func_get_args());

        if ($items = $cache->get($c_key))
        {
            return $items;
        }

        $media = ORM::factory('Media')
            ->where('slug', '=', $slug)
            ->find();

        $items = array();

        foreach ($media->items->find_all() as $item)
        {
            $items[] = $item->as_array();
        }

        $cache->set($c_key, $items);

        return $items;
    }

    /**
     * Generate a url to a media item
     *
     * @param   string          Media item name
     * @param   string          Media size (WxH)
     * @param   string          Media type (crop|size)
     * @return  string
     */
    public static function url($media = NULL, $size = NULL, $type = 'size')
    {
        if (strpos($media, '://') !== FALSE)
        {
            return $media;
        }

        if ( ! empty($size) AND ! empty($type))
        {
            $exten = pathinfo($media, PATHINFO_EXTENSION);
            $media = preg_replace("/$exten$/", "{$type}_{$size}.{$exten}", $media);
        }

        return URL::site( Media::PATH_NAME . '/' . $media );
    }

    /**
     * Retrieve a shortened url for a given media path
     *
     * @param   string          Media path
     * @return  string
     */
    public static function short_url($media)
    {
        if (strpos($media, '://') !== FALSE)
        {
            return preg_replace('@^.+://@', '', $media);
        }

        return $media;
    }

    /**
     * Save a remote media item to the media folder
     *
     * @param   string          Remote media url
     * @param   string          Media item filename
     * @return  string          Path to new media file
     */
    public static function save_remote($url, $filename = NULL)
    {
        $tempname = tempnam(sys_get_temp_dir(), 'media');

        if ($tempname === FALSE)
        {
            return FALSE;
        }

        try
        {
            file_put_contents($tempname, file_get_contents($url));
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, "Unable to save remote media ':url' to file: :filename (:message)", array(
                    ':url' => $url, ':filename' => $tempname, ':message' => Kohana_Exception::text($e) ));

            return FALSE;
        }

        if ($filename === NULL)
        {
            $filename = Media::_init_new_media($tempname);
        }

        $result = Media::pathname() . $filename;

        if (rename($tempname, $result) === FALSE)
        {
            return FALSE;
        }

        Media::create_thumbnails($result);

        return str_replace(Media::pathname(), '', $result);
    }

    /**
     * Save an uploaded media item to the media folder
     *
     * @param   array           Media upload item
     * @param   string          Media item filename
     * @return  string          Path to new media file
     */
    public static function save_upload(array $upload, $filename = NULL)
    {
        if ($filename === NULL)
        {
            $filename = Media::_init_new_media($upload['tmp_name']);
        }

        $result = Upload::save($upload, $filename, Media::pathname());

        if ($result === FALSE)
        {
            return FALSE;
        }

        Media::create_thumbnails($result);

        return str_replace(Media::pathname(), '', $result);
    }

    /**
     * Create thumbnails from a given source image. Thumnails
     * will be placed in the same output directory with the extension:
     *
     *     filename.(crop|size)_(w)x(h).ext
     *
     * @param   string          Source filename
     * @return  array           List of thumbnails created
     */
    public static function create_thumbnails($filename)
    {
        if ( ! file_exists($filename) OR ! is_readable($filename))
        {
            return FALSE;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $base_name = preg_replace("/{$extension}$/", '', $filename);

        $result = array();

        foreach (Media::$thumbnail_sizes as $type => $sizes)
        {
            foreach ($sizes as $size)
            {
                $image = Image::factory($filename);

                call_user_func_array(array($image, 'resize'), $size);

                if ($type === 'crop')
                {
                    $image->crop($size[0], $size[1]);
                }

                $image->sharpen(20);

                $human_size = $size[0] . 'x' . $size[1];
                $thumb_file = $base_name . $type . '_' . $human_size . '.' . $extension;

                try
                {
                    $image->save($thumb_file);

                    $result[$human_size] = $thumb_file;
                }
                catch (Exception $e)
                {
                    Kohana::$log->add(Log::ERROR, "Unable to create thumbnail for media ':filename' of size: :size (:message)", array(
                            ':filename' => $filename, ':size' => $human_size, ':message', Kohana_Exception::text($e) ));
                }
            }
        }

        return $result;
    }

    /**
     * Initialize a new media item from a temporary file
     *
     * @param   string          Temporary filename
     * @return  string          New media filename
     */
    protected static function _init_new_media($tempname)
    {
        // Filename not specified, get it from the upload
        $filename = Media::filename( Media::extension($tempname) );
        $filename = Media::pathname( $filename );

        // Determine the directory where the file is saved
        $directory = dirname($filename);

        if ( ! is_dir($directory))
        {
            // Create the directory
            mkdir($directory, 02775);

            // Set permissions (must be manually set to fix umask issues)
            chmod($directory, 02775);
        }

        return str_replace(Media::pathname(), '', $filename);
    }
}

