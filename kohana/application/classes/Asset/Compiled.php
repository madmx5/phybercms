<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Compiled assets helper
 *
 * @package     Application
 * @category    Helper
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Asset_Compiled {

    /**
     * @const   string      Directory where assets are stored
     */
    const PATH_NAME = 'compiled';

    /**
     * Return the site url to a compiled asset
     *
     * @param   string      Compiled asset type
     * @param   string      Compiled asset name
     * @return  string
     */
    public static function url($type, $name)
    {
        $config = Kohana::$config->load('assets/compiled');

        $assets = $config->get($type);

        if ($assets === NULL)
        {
            return FALSE;
        }

        $pathname = implode(DIRECTORY_SEPARATOR, array(Asset::PATH_NAME, Asset_Compiled::PATH_NAME, $type)) . DIRECTORY_SEPARATOR;

        return $pathname . Arr::get($assets, $name);
    }

    /**
     * @var     string      Asset collection type (css, js)
     */
    protected $_type;

    /**
     * @var     string      Asset collection name
     */
    protected $_name;

    /**
     * @var     array       Filenames that belong in the collection
     */
    protected $_files = array();

    /**
     * Constructor
     *
     *     // Reads APPPATH.'config/assets.php'
     *     $asset = new Asset_Compiled('js', 'app');
     *
     * @param   string      Compiled asset type
     * @param   string      Compiled asset name
     * @return  void
     */
    public function __construct($type, $name)
    {
        $this->_type = $type;
        $this->_name = $name;

        $config = Kohana::$config->load('assets')->as_array();

        $config = Arr::path($config, $type . '.' . $name, array());

        $this->_init($config);
    }

    /**
     * Add a list of files to this collection
     *
     *     $asset->add_slugs(array(
     *          DOCROOT.'assets/js/app.js',
     *          DOCROOT.'assets/js/admin.js'
     *     ));
     *
     * @param   array       List of file paths
     * @return  Asset_Compiled
     * @chainable
     */
    public function add_files(array $files)
    {
        foreach ($files as $file)
        {
            $this->_files[] = $file;
        }

        return $this;
    }

    /**
     * Expand a list of Asset slugs from the database, adding
     * references files to this collection
     *
     *     $asset->add_slugs(array(
     *         'my-app-assets',
     *         'my-admin-assets'
     *     ));
     *
     * @param   array       List of asset slugs
     * @return  Asset_Compiled
     * @chainable
     */
    public function add_slugs(array $slugs)
    {
        foreach ($slugs as $slug)
        {
            $asset = ORM::factory('Asset', array('slug' => $slug));

            if ( ! $asset->loaded())
                continue;

            foreach ($asset->items->find_all() as $item)
            {
                $pathname = Asset::pathname($asset->path);
                $filename = Asset::filename($item->filename);

                $this->_files[] = $pathname . $filename;
            }
        }

        return $this; 
    }

    /**
     * Return the asset collection namespace
     *
     * @return  string
     */
    public function name_space()
    {
        return sha1($this->_type . ':' . $this->_name);
    }

    /**
     * Return a hash of all files within the collection
     *
     * @return  string
     */
    public function files_hash()
    {
        $input = array();

        foreach ($this->_files as $file)
        {
            $input[] = filemtime($file);
        }

        return sha1(implode(':', $input));
    }

    /**
     * Compile the asset collection
     *
     * @return  string
     */
    public function compile()
    {
        $content = '';

        foreach ($this->_files as $file)
        {
            $content .= file_get_contents($file);
        }

        $method = array($this, '_compile_' . $this->_type);

        return call_user_func($method, $content);
    }

    /**
     * Compile assets associated with this collection and update the
     * compiled assets config (APPPATH.'config/assets/compiled.php) with
     * the path to this compiled collection.
     *
     * @return  boolean
     */
    public function update()
    {
        $this->_remove_files();

        $pathname = $this->pathname();
        $filename = $this->filename();

        try
        {
            file_put_contents($pathname . $filename, $this->compile());
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, __METHOD__ . "(): Unable to write compiled assets to file: :message", array(
                    ':message' => $e->getMessage()) );

            return FALSE;
        }

        $config = Kohana::$config->load('assets/compiled');

        $assets = $config->get($this->_type, array());

        // Place the new filename in config array
        $assets[$this->_name] = $this->filename();

        $config->set($this->_type, $assets);

        // Generate the updated config file content
        $content = Kohana::FILE_SECURITY.PHP_EOL.PHP_EOL.'return '.var_export($config->as_array(), TRUE).';'.PHP_EOL;

        try
        {
            file_put_contents(APPPATH.'config/assets/compiled'.EXT, $content);
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, __METHOD__ . "(): Unable to write compiled assets config: :message", array(
                    ':message' => $e->getMessage()) );

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Return the real filename of a compiled asset collection
     *
     *     $asset->filename(); // <namespace>_<fileshash>.<css|js>
     *
     * @return  string
     */
    public function filename()
    {
        return $this->name_space() . '_' . $this->files_hash() . '.' . $this->_type;
    }

    /**
     * Return the filesystem path to compiled asset
     *
     *     $asset->pathname(); // Asset::pathname(Asset_Compiled::PATH_NAME . '/<type:css|js>');
     *
     * @return  string
     */
    public function pathname()
    {
        return Asset::pathname(Asset_Compiled::PATH_NAME . DIRECTORY_SEPARATOR . $this->_type);
    }

    /**
     * Initialize the Asset collection from a configuration array
     *
     *     $config = array(
     *         'files' => array(DOCROOT.'assets/js/app.js'),
     *         'slugs' => array('my-app-asset'),
     *      );
     *
     *      $asset = new Asset_Compiled();
     *      $asset->init($config);
     *
     * @param   array       Configuration array
     * @return  Asset_Compiled
     * @chainable
     */
    protected function _init(array $config)
    {
        $files = Arr::get($config, 'files', array());
        $this->add_files($files);

        $slugs = Arr::get($config, 'slugs', array());
        $this->add_slugs($slugs);

        return $this;
    }

    /**
     * Compile the collection as CSS files
     *
     * @param   string      Content to compile
     * @return  string
     */
    protected function _compile_css($content)
    {
        return CssMin::minify($content);
    }

    /**
     * Compile the collection as JS files
     *
     * @param   string      Content to compile
     * @return  string
     */
    protected function _compile_js($content)
    {
        return \JsMin\Minify::Minify($content);
    }

    /**
     * Remove existing compiled assets within the same namespace
     *
     * @return  Asset_Compiled
     * @chainable
     */
    protected function _remove_files()
    {
        $pathname = $this->pathname();

        $files = glob($pathname . $this->name_space() . '_*.' . $this->_type);

        foreach ($files as $file)
        {
            unlink($file);
        }

        return $this;
    }
}

