<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * CMS Twig extension
 *
 * @package     Application
 * @category    Twig
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Twig_Extension_Cms extends Twig_Extension {

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return  array       An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('nonl', 'twig_nonl_filter', array('is_safe' => array('all'))),
        );
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return  array       An array of global functions
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('asset_url', array('Asset_Compiled', 'url')),
            new Twig_SimpleFunction('media', array('Media', 'get')),
            new Twig_SimpleFunction('media_url', array('Media', 'url')),
            new Twig_SimpleFunction('menu', array('Menu', 'get')),
            new Twig_SimpleFunction('menu_active', array('Menu', 'active')),
            new Twig_SimpleFunction('menu_url', array('Menu', 'url')),
            new Twig_SimpleFunction('site_url', array('URL', 'site')),
            new Twig_SimpleFunction('rsvp_party', array('Rsvp', 'party')),
            new Twig_SimpleFunction('rsvp_guest', array('Rsvp', 'guest')),
            new Twig_SimpleFunction('rsvp_error', array('Rsvp', 'error')),
            new Twig_SimpleFunction('rsvp_meals', array('Rsvp', 'meals')),
            new Twig_SimpleFunction('rsvp_visit', array('Rsvp', 'visit')),

            new Twig_SimpleFunction('instagram_subscription', array('Instagram', 'subscription')),
            new Twig_SimpleFunction('instagram_media', array('Instagram', 'get')),
            new Twig_SimpleFunction('instagram_fetch', array('Instagram', 'more')),

            // Functions that output HTML
            new Twig_SimpleFunction('flash', array('Flash', 'get'), array('is_safe' => array('all'))),
            new Twig_SimpleFunction('form_checkbox', array('Form', 'checkbox'), array('is_safe' => array('all'))),
            new Twig_SimpleFunction('form_input', array('Form', 'input'), array('is_safe' => array('all'))),
            new Twig_SimpleFunction('form_select', array('Form', 'select'), array('is_safe' => array('all'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return  string      The extension name
     */
    public function getName()
    {
        return 'cms';
    }
}

/**
 * Removes all new line characters from string.
 *
 * @param   string      String to remove new lines
 * @return  string
 */
function twig_nonl_filter($string)
{
    return preg_replace('/\R/', '', $string);
}

