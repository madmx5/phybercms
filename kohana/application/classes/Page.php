<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Page helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Page {

    /**
     * Return a page by slug
     *
     * @param   string          Page slug name
     * @param   string          Page status restriction
     * @param   string          Page date restriction column
     * @param   string          Page date restriction value
     * @return  Model_Page      Page object if found
     * @return  FALSE           Page could not be found
     */
    public static function get($slug, $status = NULL, $atdate = NULL, $date = NULL)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('-', func_get_args());

        if ($page = $cache->get($c_key))
        {
            return $page;
        }

        $page = ORM::factory('Page')->where('slug', '=', $slug);

        if ($status !== NULL)
        {
            $page->and_where('status', '=', $status);
        }

        if ($atdate !== NULL)
        {
            if ($date === NULL)
            {
                $date = date('Y-m-d H:i:s');
            }

            $page->and_where($atdate, '<=', $date);
        }

        $page->find();

        if ( ! $page->loaded())
        {
            $page = FALSE;
        }

        $cache->set($c_key, $page);

        return $page;
    }
}

