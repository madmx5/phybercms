<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Flash message helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Flash {

    // Flash message levels
    const BLOCK   = 0;
    const SUCCESS = 1;
    const ERROR   = 2;
    const INFO    = 3;

    /**
     * @var     string      Name of session key to use for flashes
     */
    public static $session_key = 'flash';

    /**
     * Set the flash message and level
     *
     * @param   string      Flash message
     * @param   integer     Message level
     * @return  void
     */
    public static function set($message, $level = Flash::BLOCK)
    {
        $flash = Flash::factory($message, $level);

        Session::instance()->set(Flash::$session_key, $flash);
    }

    /**
     * Get the flash message as a rendered view
     *
     * @param   string      View to render with or NULL for default
     * @return  string
     */
    public static function get($view = NULL)
    {
        $flash = Session::instance()->get_once(Flash::$session_key);

        if ($flash instanceof Flash)
        {
            if ($view !== NULL)
            {
                $flash->view($view);
            }

            return $flash->render();
        }
        else
        {
            return '';
        }
    }

    /**
     * Create a new Flash object
     *
     * @param   string      Flash message
     * @param   integer     Message level
     * @return  Flash
     */
    public static function factory($message, $level = Flash::BLOCK)
    {
        return new Flash($message, $level);
    }

    /**
     * @var     string      Flash message
     */
    protected $message = NULL;

    /**
     * @var     integer     Message level
     */
    protected $level = Flash::BLOCK;

    /**
     * @var     string      View filename
     */
    protected $view = 'flash/template';

    /**
     * Constructor
     *
     * @param   string      Flash message
     * @param   integer     Message level
     * @return  void
     */
    public function __construct($message, $level = Flash::BLOCK)
    {
        $this->message = $message;

        $this->level = $level;
    }

    /**
     * Get or set the flash message
     *
     * @param   string      Flash message
     * @return  mixed
     */
    public function message($message = NULL)
    {
        if ($message === NULL)
        {
            return $this->message;
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Get or set the flash message
     *
     * @param   integer     Message level
     * @return  mixed
     */
    public function level($level = NULL)
    {
        if ($level === NULL)
        {
            // Act as a getter
            return $this->level;
        }

        // Act as a setter
        $this->level = $level;

        return $this;
    }

    /**
     * Get or set the flash view
     *
     * @param   string      View filename
     * @return  mixed
     */
    public function view($view = NULL)
    {
        if ($view === NULL)
        {
            // Act as a getter
            return $this->view;
        }

        // Act as a setter
        $this->view = $view;

        return $this;
    }

    /**
     * Render the flash message
     *
     * @param   mixed       View object or filename
     * @return  string
     */
    public function render($view = NULL)
    {
        if ($view === NULL)
        {
            $view = $this->view;
        }

        if ( ! $view instanceof Kohana_View)
        {
            $view = View::factory($view);
        }

        return $view->set(get_object_vars($this))->render();
    }

    /**
     * Render the flash message
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->render();
    }
}

