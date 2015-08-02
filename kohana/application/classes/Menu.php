<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Menu helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Menu {

    /**
     * Return an array of menu items
     *
     * @param   string          Menu slug name
     * @return  array           Menu items
     */
    public static function get($slug)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('-', func_get_args());

        if ($items = $cache->get($c_key))
        {
            return $items;
        }

        $menu = ORM::factory('Menu')
            ->where('slug', '=', $slug)
            ->find();

        $items = array();

        foreach ($menu->items->find_all() as $item)
        {
            $items[] = $item->as_array();
        }

        $cache->set($c_key, $items);

        return $items;
    }

    /**
     * Determine the url to a given menu item
     *
     * @param   string          Menu item url
     * @return  string          Absolute url
     */
    public static function url($url)
    {
        if (strpos($url, '://') !== FALSE)
        {
            return $url;
        }

        return URL::site($url);
    }

    /**
     * Determine if a menu item is active (selected)
     *
     * @param   string          Menu item slug name
     * @return  boolean
     */
    public static function active($slug)
    {
        $request = Request::current();

        if ($request->controller() !== 'Pages' OR $request->action() !== 'view')
        {
            return FALSE;
        }

        if ($request->param('slug') === 'home' AND $slug === Kohana::$base_url)
        {
            return TRUE;
        }

        return $request->param('slug') == $slug;
    }
}

