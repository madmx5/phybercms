<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin guests controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Admin_Guests extends Controller_Admin {

    /**
     * Action that shows all guests
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/addons/guests/index');

        $parties = ORM::factory('Rsvp_Party');

        $pagination = Pagination::factory(array(
                'current_page' => array('request' => $this->request),
                'total_items'  => $parties->reset(FALSE)->count_all(),
            ));

        $content->parties = $parties
            ->offset($pagination->offset)
            ->limit($pagination->items_per_page)
            ->find_all()
            ->as_array();

        $content->pagination = $pagination;
    }

    /**
     * Action that creates a new party
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Action that edits an existing party
     *
     * @param   integer         id of party to edit or FALSE to create a new party
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/addons/guests/create');

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
                $party = ORM::factory('Rsvp_Party', $id);

                // Update the model properties
                $party->values($post, array('name', 'slug'));
                $party->save();

                $message = ($id === FALSE ? 'party.created' : 'party.updated');

                Flash::set(Kohana::message('forms/addons/guests', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_guests')->uri(array(
                        'action' => 'edit',
                        'id'     => $party->id
                    )) );
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');
            }

            // Include the posted values
            $content->values = $post;
            $content->values['id'] = $id;

            $content->guests = $party->guests->find_all()->as_array();
        }
        else if ($id !== FALSE)
        {
            $party = ORM::factory('Rsvp_Party', $id);

            $content->values = $party->as_array();
            $content->guests = $party->guests->find_all()->as_array();
        }

        // Set other variables needed by view
    }

    /**
     * Create a new guest that belongs to a party
     *
     * @param   integer         Party to create the guest under
     * @return  void
     */
    public function action_create_item($id = NULL)
    {
        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $this->action_edit_item(FALSE, $id);
    }

    /**
     * Edit an existing guest that belongs to a party
     *
     * @param   integer         Guest to edit
     * @param   integer         Party guest belongs to
     * @return  void
     */
    public function action_edit_item($id = NULL, $party_id = NULL)
    {
        $session = Session::instance();

        $this->template = $content = View::factory('admin/addons/guests/item');

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
                if ($party_id !== NULL)
                {
                    $party = ORM::factory('Rsvp_Party', $party_id);

                    // Create a new party guest
                    $guest = ORM::factory('Rsvp_Guest');
                    $guest->party_id = $party->id;
                }
                else
                {
                    // Load existing party guest
                    $guest = ORM::factory('Rsvp_Guest', $id);

                    $party_id = $guest->party_id;
                }

                $guest->values($post, array('first_name', 'last_name', 'adult', 'plus_one', 'gender', 'attending', 'meal_id'));
                $guest->save();

                $message = ($id === FALSE ? 'item.created' : 'item.updated');

                Flash::set(Kohana::message('forms/addons/guests', $message), Flash::SUCCESS);
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');

                $session->set('party_guest_errors', $content->errors);
                $session->set('party_guest_fields', $content->fields);
                $session->set('party_guest_failed', $this->request->uri());
                $session->set('party_guest_values', $post);

                $message = strtr(Kohana::message('forms/addons/guests', 'item.failure'), array(
                        ':uri' => URL::site( $this->request->uri()) )
                    );

                Flash::set($message, Flash::ERROR);
            }

            $this->redirect( Route::get('panel_guests')->uri(array(
                    'action' => 'edit',
                    'id'     => $party_id
                )) );
        }
        else if ($id !== FALSE)
        {
            $guest = ORM::factory('Rsvp_Guest', $id);

            $content->values = $guest->as_array();
        }

        // Set other variables needed by view
        if ($session->get('party_guest_failed') === $this->request->uri())
        {
            $content->errors = Arr::merge($content->errors, $session->get_once('party_guest_errors', array()) );
            $content->fields = Arr::merge($content->fields, $session->get_once('party_guest_fields', array()) );
            $content->values = Arr::merge($content->values, $session->get_once('party_guest_errors', array()) );

            $session->delete('party_guest_failed');
        }

        $content->party_id = $party_id;
        $content->meals = Model_Rsvp_Meal::select_list();
    }

    /**
     * Delete an existing guest that belongs to a party
     *
     * @param   integer         Guest to delete
     * @return  void
     */
    public function action_delete_item($id = NULL)
    {
        $this->template = $content = View::factory('admin/addons/guests/item/delete');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $item = ORM::factory('Rsvp_Guest', $id);

            $party_id = $item->party_id;

            try
            {
                $item->delete();

                Flash::set(Kohana::message('forms/addons/guests', 'item.deleted'), Flash::SUCCESS);
            }
            catch (Exception $e)
            {
                Flash::set(Kohana::message('forms/addons/guests', 'item.not_deleted'), Flash::ERROR);

                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
            }

            $this->redirect( Route::get('panel_guests')->uri(array(
                    'action'     => 'edit',
                    'id'         => $party_id
                )) );
        }
        else if ($id !== NULL)
        {
            $item = ORM::factory('Rsvp_Guest', $id);

            $content->item = $item->as_array();
        }

        // Set other variables needed by view
    }
}

