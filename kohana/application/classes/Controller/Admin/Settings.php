<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin CMS settings controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Controller_Admin_Settings extends Controller_Admin {

    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/settings/index');
    }
}

