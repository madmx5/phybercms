<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin meals controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Admin_Meals extends Controller_Admin {

    /**
     * Action that shows all meals
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/addons/meals/index');

        $meals = ORM::factory('Rsvp_Meal');

        $content->meals = $meals->find_all()->as_array();
    }

    /**
     * Action that creates a new meal
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Action that edits an existing meal
     *
     * @param   integer         id of meal to edit or FALSE to create a new meal
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/addons/meals/create');

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
                $meal = ORM::factory('Rsvp_Meal', $id);

                // Update the model properties
                $meal->values($post, array('name', 'description', 'adult'));
                $meal->save();

                $message = ($id === FALSE ? 'meal.created' : 'meal.updated');

                Flash::set(Kohana::message('forms/addons/meals', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_meals')->uri(array(
                        'action' => 'edit',
                        'id'     => $meal->id
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

            $content->guests = $meal->guests->find_all()->as_array();
        }
        else if ($id !== FALSE)
        {
            $meal = ORM::factory('Rsvp_Meal', $id);

            $content->values = $meal->as_array();
            $content->guests = $meal->guests->find_all()->as_array();
        }

        // Set other variables needed by view
    }
}

