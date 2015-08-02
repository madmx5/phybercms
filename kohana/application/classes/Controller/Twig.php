<?php defined('SYSPATH') OR die('No direct script access.');

use \JSMin;

/**
 * Abstract class for automatic Twig templating.
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
abstract class Controller_Twig extends Controller {

    /**
     * @var     string              Twig template path and Twig_View object
     */
    public $template = '';

    /**
     * @var     boolean             Auto render template
     */
    public $auto_render = TRUE;

    /**
     * @var     Twig_Loader         Twig template loader
     */
    protected $_load;

    /**
     * @var     Twig_Environment    Twig configuration environment
     */
    protected $_twig;

    /**
     * Initializes the Twig template engine
     *
     * @return  void
     */
    public function before()
    {
        parent::before();

        if ( ! empty($this->template))
        {
            $this->_initialize();
        }
    }

    /**
     * Assigns the Twig template as the request response
     *
     * @return  void
     */
    public function after()
    {
        if ($this->auto_render === TRUE)
        {
            $this->response->body($this->template->render());
        }

        parent::after();
    }

    /**
     * Initialize the Twig environment
     *
     * @return  void
     */
    protected function _initialize()
    {
        $this->_load = new Twig_DatabaseLoader();

        $this->_twig = new Twig_Environment($this->_load, array(
                'cache' => APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'twig',
                'debug' => TRUE,
            ) );

        $this->template = Twig_View::factory($this->_twig, $this->template);

        $this->_twig->addExtension( new Twig_Extension_Cms() );

        if (Kohana::$environment !== Kohana::PRODUCTION)
        {
            $this->_twig->addExtension( new Twig_Extension_Debug() );
        }
    }
}

