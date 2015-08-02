<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin Instagram controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Admin_Instagram extends Controller_Admin {

    /**
     * Action that shows all Instagram subscriptions
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/addons/instagram/index');

        $subscriptions = ORM::factory('Instagram_Subscription');

        $content->subscriptions = $subscriptions->find_all()->as_array();
    }

    /**
     * Action to create a new Instagram subscription
     *
     * @return  void
     */
    public function action_create_item()
    {
        $this->action_edit_item(FALSE);
    }

    /**
     * Edit an existing Instagram subscription
     *
     * @param   integer         Subscription to edit
     * @return  void
     */
    public function action_edit_item($id = NULL)
    {
        $session = Session::instance();

        $this->template->content = $content = View::factory('admin/addons/instagram/create');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $content->errors = array();
        $content->fields = array();
        $content->values = array();

        $post = $this->request->post();

        if ( ! empty($post))
        {
            try
            {
                $subscription = ORM::factory('Instagram_Subscription', $id);

                $subscription->values($post, array(
                        'slug', 'title', 'client_id', 'client_secret', 'token', 'object', 'aspect', 'obj_id', 'params',
                    ));

                $res = $subscription->save();

                // Preform API operations needed to subscribe to real-time updates
                if ( Instagram::create_subscription($subscription) )
                {
                    $subscription->save();
                }

                $message = ($id === FALSE ? 'subscription.created' : 'subscription.updated');

                Flash::set(Kohana::message('forms/addons/instagram', $message), Flash::SUCCESS);
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');

                $session->set('instagram_subscription_errors', $content->errors);
                $session->set('instagram_subscription_fields', $content->fields);
                $session->set('instagram_subscription_failed', $this->request->uri());
                $session->set('instagram_subscription_values', $post);

                $message = strtr(Kohana::message('forms/addons/instagram', 'subscription.failure'), array(
                        ':uri' => URL::site( $this->request->uri()) )
                    );

                Flash::set($message, Flash::ERROR);
            }

            $this->redirect( Route::get('panel_instagram')->uri() );
        }
        else if ($id !== FALSE)
        {
            $subscription = ORM::factory('Instagram_Subscription', $id);

            $content->values = $subscription->as_array();

            $content->recent = View::factory('admin/addons/instagram/recent', array(
                'media' => $subscription
                    ->media
                    ->order_by('created_time', 'DESC')
                    ->limit(48)
                    ->find_all()
                ));
        }

        // Set other variables needed by view
        if ($session->get('instagram_subscription_failed') === $this->request->uri())
        {
            $content->errors = Arr::merge($content->errors, $session->get_once('instagram_subscription_errors', array()) );
            $content->fields = Arr::merge($content->fields, $session->get_once('instagram_subscription_fields', array()) );
            $content->values = Arr::merge($content->values, $session->get_once('instagram_subscription_values', array()) );

            $session->delete('instagram_subscription_failed');
        }
    }

    /**
     *
     */
    public function action_view_item($id = NULL)
    {
        $this->template = $content = View::factory('admin/addons/instagram/item');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $item = ORM::factory('Instagram_Media', $id);

        $content->item = $item;
    }
}

