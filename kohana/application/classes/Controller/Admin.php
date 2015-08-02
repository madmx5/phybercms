<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin panel controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Controller_Admin extends Controller_Website {

    /**
     * @var     string      View template
     */
    public $template = 'admin/template';

    /**
     * @var     array       List of stylesheets
     */
    public $stylesheets = array(
        );

    /**
     * @var     array       List of javascripts
     */
    public $javascripts = array(
            'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js',
            '/assets/ace/ace.js',
        );

    /**
     * @var     array       List of navigation items
     */
    public $navigation_items = array(
            'admin/dashboard'  => 'Dashboard',
            'admin/pages'      => 'Pages',
            'admin/media'      => 'Media',
            'admin/menus'      => 'Menus',
            'admin/assets'     => 'Assets',
            // 'admin/components' => 'Components',
            'Add Ons'          => array(
                    'admin/guests'    => 'Guests',
                    'admin/instagram' => 'Instagram',
                    'admin/meals'     => 'Meals',
                ),
            'admin/settings'   => 'Settings',
        );

    /**
     * Before controller action
     *
     * @return  void
     */
    public function before()
    {
        $this->stylesheets[] = Asset_Compiled::url('css', 'admin');
        $this->javascripts[] = Asset_Compiled::url( 'js', 'admin');

        if ( ! Auth::instance()->logged_in('admin') AND $this->request->action() !== 'login')
        {
            $this->redirect('admin/login', 302);
        }

        if (Auth::instance()->logged_in())
        {
            View::set_global('_user', Auth::instance()->get_user()->as_array());
        }
        else
        {
            View::set_global('_user', array());
        }

        View::set_global('_site', Kohana::$config->load('site')->as_array());

        parent::before();
    }

    /**
     * Show login form and authenticate user
     *
     * @return  void
     */
    public function action_login()
    {
        if (Auth::instance()->logged_in('admin'))
        {
            return $this->redirect('admin/dashboard', 302);
        }

        $this->template = $content = View::factory('admin/login');

        $content->set('username', NULL);
        $content->set('password', NULL);

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $validation = Validation::factory($post)
                ->rule('username', 'not_empty')
                ->rule('password', 'not_empty');

            if ($validation->check())
            {
                $success = Auth::instance()->login($validation['username'], $validation['password']);

                if ($success)
                {
                    // Login successful, send to app
                    $this->redirect('admin/dashboard', 302);
                }
                else
                {
                    // Login failed, send back to form with error message
                    $content->set('username', $post['username']);
                    $content->set('password', '');

                    $content->set('errors', array('username' => Kohana::message('admin', 'login.invalid')));
                }
            }
            else
            {
                // Login form is invalid, show errors
                $content->set('errors', $validation->errors('admin'));
            }
        }
    }

    /**
     * Destory any active authentication session
     *
     * @return  void
     */
    public function action_logout()
    {
        Session::instance()->set('flash', Kohana::message('admin', 'login.logout'));

        Auth::instance()->logout();

        $this->redirect('admin/login', 302);
    }
}

