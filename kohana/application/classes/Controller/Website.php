<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Abstract website controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
abstract class Controller_Website extends Controller_Template {

    /**
     * @var     string      Page title
     */
    public $page_title = 'My Site';

    /**
     * @var     array       List of stylesheets
     */
    public $stylesheets = array();

    /**
     * @var     array       List of javascripts
     */
    public $javascripts = array();

    /**
     * @var     array       List of navigation items
     */
    public $navigation_items = array();

    /**
     * Called before controller action
     *
     * @see     [Kohana_Controller_Template::before]
     * @return  void
     */
    public function before()
    {
        parent::before();

        View::bind_global('_stylesheets', $this->stylesheets);
        View::bind_global('_javascripts', $this->javascripts);

        View::bind_global('_navigation_items', $this->navigation_items);

        View::set_global('_page_title', $this->page_title);
    }
}

