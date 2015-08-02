<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Acts as an object wrapper for Twig templates called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 * @package     Application
 * @category    Twig
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Twig_View {

    /**
     * @var     array           Array of global variables
     */
    protected static $_global_data = array();

    /**
     * Returns a new Twig_View object. If you do not define the "environment" parameter
     * you must call [Twig_View::set_environment].
     *
     *     $view = Twig_View::factory(new Twig_Environment());
     *
     * @param   object          Twig_Environment object
     * @param   string          Source of view
     * @param   array           Array of values
     * @return  Twig_View
     */
    public static function factory(Twig_Environment $twig, $source = NULL, array $data = NULL)
    {
        return new Twig_View($twig, $source, $data);
    }

    /**
     * Sets a global variable, similar to [Twig_View::set], except that
     * the variable will be accessible to all views.
     *
     *     Twig_View::set_global($name, $value);
     *
     * @param   string          Variable name or an array of variables
     * @param   mixed           Value
     * @return  void
     */
    public static function set_global($key, $value = NULL)
    {
        if (is_array($key))
        {
            foreach ($key as $key2 => $value)
            {
                Twig_View::$_global_data[$key2] = $value;
            }
        }
        else
        {
            Twig_View::$_global_data[$key] = $value;
        }
    }

    /**
     * Assigns a global variable by reference, similar to [Twig_View::bind], except
     * that the variable will be accessible to all views.
     *
     *     Twig_View::bind_global($key, $value);
     *
     * @param   string          Variable name
     * @param   mixed           References variable
     * @return  void
     */
    public static function bind_global($key, & $value)
    {
        Twig_View::$_global_data[$key] =& $value;
    }

    /**
     * @var     object          Twig_Environment object
     */
    protected $_twig = NULL;

    /**
     * @var     array           Array of local variables
     */
    protected $_data = array();

    /**
     * @var     string          Source of the view
     */
    protected $_source = NULL;

    /**
     * Sets the inital view environment and local data. View should almost
     * always be created using [Twig_View::factory].
     *
     *     $view = new Twig_View(new Twig_Environment());
     *
     * @param   object          Twig_Environment object
     * @param   string          Source for view
     * @param   array           Array of values
     * @return  void
     */
    public function __construct(Twig_Environment $environment = NULL, $source = NULL, array $data = NULL)
    {
        if ($environment !== NULL)
        {
            $this->set_environment($environment);
        }

        if ($source !== NULL)
        {
            $this->set_source($source);
        }

        if ($data !== NULL)
        {
            // Add the values to the current data
            $this->_data = $data + $this->_data;
        }
    }

    /**
     * Magic method, searches for the given variable and returns its value.
     * Local variables will be returned before global variables.
     *
     *     $value = $view->foo;
     *
     * [!!] If the variable has not yet been set, an exception will be thrown.
     *
     * @param   string          Variable name
     * @return  mixed
     * @throws  Kohana_Exception
     */
    public function & __get($key)
    {
        if (array_key_exists($key, $this->_data))
        {
            return $this->_data[$key];
        }
        elseif (array_key_exists($key, View::$_global_data))
        {
            return Twig_View::$_global_data[$key];
        }
        else
        {
            throw new Kohana_Exception('Twig_View variable is not set: :var',
                array(':var' => $key));
        }
    }

    /**
     * Magic method, calls [Twig_View::set] with the same parameters.
     *
     *     $view->foo = 'something';
     *
     * @param   string          Variable name
     * @param   mixed           Value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magic method, determines if a variable is set.
     *
     *     isset($view->foo);
     *
     * [!!] `NULL` variables are not considered to be set by [isset](http://php.net/isset).
     *
     * @param   string          Variable name
     * @return  boolean
     */
    public function __isset($key)
    {
        return (isset($this->_data[$key]) OR isset(Twig_View::$_global_data[$key]));
    }

    /**
     * Magic method, unsets a given variable.
     *
     *     unset($view->foo);
     *
     * @param   string          Variable name
     * @return  void
     */
    public function __unset($key)
    {
        unset($this->_data[$key], Twig_View::$_global_data[$key]);
    }

    /**
     * Magic method, returns the output of [Twig_View::render].
     *
     * @return  string
     * @uses    Twig_View::render
     */
    public function __toString()
    {
        try
        {
            return $this->render();
        }
        catch (Exception $e)
        {
            /**
             * Display the exception message.
             *
             * We use this method here because it's impossible to throw and
             * exception from __toString().
             */
            $error_response = Kohana_exception::_handler($e);

            return $error_response->body();
        }
    }

    /**
     * Sets the view environment.
     *
     *     $view->set_environment(new Twig_Environment());
     *
     * @param   object          Twig_Environment object
     * @return  Twig_View
     */
    public function set_environment(Twig_Environment $environment)
    {
        $this->_twig = $environment;

        return $this;
    }

    /**
     * Sets the view source.
     *
     *     $view->set_source('Layout/main');
     *
     * @param   string          Source of view
     * @return  Twig_View
     */
    public function set_source($source)
    {
        $this->_source = $source;

        return $this;
    }

    /**
     * Assigns a variable by name. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This value can be accessed as $foo within the view
     *     $view->set('foo', 'my value');
     *
     * You can also use an array to set several values at once:
     *
     *     // Create the values $food and $beverage in the view
     *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * @param   string          Variable name or an array of variables
     * @param   mixed           Value
     * @return  $this
     */
    public function set($key, $value = NULL)
    {
        if (is_array($key))
        {
            foreach ($key as $name => $value)
            {
                $this->_data[$name] = $value;
            }
        }
        else
        {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Assigns a value by reference. The benefit of binding is that values can
     * be altered without re-setting them. It is also possible to bind variables
     * before they have values. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This reference can be accessed as $ref within the view
     *     $view->bind('ref', $bar);
     *
     * @param   string          Variable name
     * @param   mixed           Referenced variable
     * @return  $this
     */
    public function bind($key, & $value)
    {
        $this->_data[$key] =& $value;

        return $this;
    }

    /**
     * Renders the view object to a string. Global and local data are merged
     * and extracted to create local variables within the view file.
     *
     *     $output = $view->render();
     *
     * @return  string
     */
    public function render($source = NULL)
    {
        if ($source !== NULL)
        {
            $this->set_source($source);
        }

        $data = Twig_View::$_global_data + $this->_data;

        return $this->_twig->render($this->_source, $data);
    }
}

