<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin dashboard controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Controller_Admin_Dashboard extends Controller_Admin {

    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/dashboard/index');
    }
}

