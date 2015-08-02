<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Pagination links generator.
 *
 * @package     Application
 * @category    Helpers
 * @author      Kohana Team
 * @author      Todd Wirth
 * @copyright   (c) 2008-2009 Kohana Team
 * @license     http://kohanaphp.com/license.html
 */
class Pagination {

    // Merged configuration settings
    protected $config = array(
            'current_page'      => array('source' => 'query_string', 'key' => 'page', 'request' => FALSE),
            'total_items'       => 0,
            'items_per_page'    => 10,
            'view'              => 'pagination/basic',
            'auto_hide'         => TRUE,
            'first_page_in_url' => FALSE,
        );

    // Current page number
    protected $current_page;

    // Total item count
    protected $total_items;

    // How many items to show per page
    protected $items_per_page;

    // Total page count
    protected $total_pages;

    // Item offset for the first item displayed on the current page
    protected $current_first_item;

    // Item offer for the last item displayed on the current page
    protected $current_last_item;

    // Previous page number; FALSE if the current page is the first one
    protected $previous_page;

    // Next page number; FALSE if the current page is the last one
    protected $next_page;

    // First page number; FALSE if the current page is the first one
    protected $first_page;

    // Last page number; FALSE if the current page is the last one
    protected $last_page;

    // Query offset
    protected $offset;

    /**
     * Creates a new Pagination object.
     *
     * @param   array       Configuration
     * @return  Pagination
     */
    public static function factory(array $config = array())
    {
        return new Pagination($config);
    }

    /**
     * Creates a new Pagination object.
     *
     * @param   array       Configuation
     * @return  void
     */
    public function __construct(array $config = array())
    {
        // Pagination setup
        $this->setup($config);
    }

    /**
     * Loads configuration settings into the object and (re)calculates pagination if needed.
     * Allows you to update config settings after a Pagination object has been constructed.
     *
     * @param   array       Configuration
     * @return  Pagination
     */
    public function setup(array $config = array())
    {
        // Overwrite the current config settings
        $this->config = Arr::merge($this->config, $config);

        // Only (re)calculate pagination when needed
        if ($this->current_page === NULL
                OR isset($config['current_page'])
                OR isset($config['total_items'])
                OR isset($config['items_per_page']))
        {
            // Retrieve the current page number
            if ( ! empty($this->config['current_page']['page']))
            {
                // The current page number has been set manually
                $this->current_page = (int) $this->config['current_page']['page'];
            }
            else
            {
                $request = Arr::get($this->config['current_page'], 'request');
                $pagekey = Arr::get($this->config['current_page'], 'key');

                switch ($this->config['current_page']['source'])
                {
                    case 'query_string':
                        $this->current_page = $request->query($pagekey) ? (int) $request->query($pagekey) : 1;
                        break;

                    case 'route':
                        $this->current_page = $request->param($pagekey) ? (int) $request->param($pagekey) : 1;
                        break;
                }
            }

            // Calculate and clean all pagination values
            $this->total_items        = (int) max(0, $this->config['total_items']);
            $this->items_per_page     = (int) max(1, $this->config['items_per_page']);
            $this->total_pages        = (int) ceil($this->total_items / $this->items_per_page);
            $this->current_page       = (int) min(max(1, $this->current_page), max(1, $this->total_pages));
            $this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
            $this->current_last_item  = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);
            $this->previous_page      = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
            $this->next_page          = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;
            $this->first_page         = ($this->current_page === 1) ? FALSE : 1;
            $this->last_page          = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
            $this->offset             = (int) (($this->current_page - 1) * $this->items_per_page);
        }

        // Chainable method
        return $this;
    }

    /**
     * Generates the full URL for a certain page.
     *
     * @param   integer     Page number
     * @return  string      Page URL
     */
    public function url($page = 1)
    {
        // Clean the page number
        $page = max(1, (int) $page);

        // No page number in URLs to first page
        if ($page === 1 AND ! $this->config['first_page_in_url'])
        {
            $page = NULL;
        }

        $request = Arr::get($this->config['current_page'], 'request');
        $pagekey = Arr::get($this->config['current_page'], 'key');

        switch ($this->config['current_page']['source'])
        {
            case 'query_string':
                $query = $request->query();

                if ($page === NULL)
                {
                    unset($query[$pagekey]);
                }
                else
                {
                    $query[$pagekey] = $page;
                }

                list($uri,) = explode('?', $request->uri(), 2);

                return $uri . URL::query($query, FALSE);

            case 'route':
                throw new Exception('Code not yet implemented');
        }

        return '#';
    }

    /**
     * Checks whether the given page number exists.
     *
     * @param   integer     Page number
     * @return  boolean
     */
    public function valid_page($page)
    {
        // Page number has to be a clean integer
        if ( ! Valid::digit($page))
            return FALSE;

        return $page > 0 AND $page <= $this->total_pages;
    }

    /**
     * Renders the pagination links.
     *
     * @param   mixed       String of the view to use, or Kohana_View object
     * @return  string      Pagination output (HTML)
     */
    public function render($view = NULL)
    {
        // Automatically hide pagination whenever it is superflous
        if ($this->config['auto_hide'] === TRUE AND $this->total_pages <= 1)
            return '';

        if ($view === NULL)
        {
            // Use the view from config
            $view = $this->config['view'];
        }

        if ( ! $view instanceof Kohana_View)
        {
            // Load the view file
            $view = View::factory($view);
        }

        // Pass on the whole Pagination object
        return $view->set(get_object_vars($this))->set('page', $this)->render();
    }

    /**
     * Renders the pagination links.
     *
     * @return  string      Pagination output (HTML)
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Returns a Pagination property.
     *
     * @param   string      Property to return
     * @return  mixed       Pagination property; NULL if not found
     */
    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : NULL;
    }

    /**
     * Updates a single config setting, and recalculates pagination if needed.
     *
     * @param   string      Config key
     * @param   mixed       Config value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->setup(array($key => $value));
    }
}

