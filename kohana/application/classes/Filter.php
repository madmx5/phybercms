<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Filter helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Filter {

    /**
     * Filter empty() values by returning NULL
     *
     * @param   mixed       Value to filter
     * @return  NULL        if empty($value) is TRUE
     * @return  mixed
     */
    public static function null_empty($value)
    {
        return empty($value) ? NULL : $value;
    }

    /**
     * Filter string to proper noun form
     *
     * @param   string      Value to filter
     * @return  string
     */
    public static function proper_noun($value)
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/\p{Zs}{2,}/', ' ', $value);

        return ucwords($value);
    }
}

