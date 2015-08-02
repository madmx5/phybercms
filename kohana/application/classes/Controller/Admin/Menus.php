<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin menus controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Controller_Admin_Menus extends Controller_Admin {

    /**
     * Action that shows all menus
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/menus/index');

        $menus = ORM::factory('Menu');

        $content->menus = $menus->find_all()->as_array();
    }

    /**
     * Action to create a new menu
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Action to edit an existing menu
     *
     * @param   integer         Menu to edit or FALSE to create a new Menu
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/menus/create');

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
                $menu = ORM::factory('Menu', $id);

                // Update the model properties
                $menu->values($post, array('title', 'slug'));
                $menu->save();

                // Apply new sort ordering if given
                $sort = Arr::get($post, 'sort');

                if (is_array($sort) AND ! empty($sort))
                {
                    $menu->reorder_items($sort);
                }

                $message = ($id === FALSE ? 'menu.created' : 'menu.updated');

                Flash::set(Kohana::message('forms/menu', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_menus')->uri(array(
                        'action' => 'edit',
                        'id'     => $menu->id
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

            $content->items = $menu->items->find_all()->as_array();
        }
        else if ($id !== FALSE)
        {
            $menu = ORM::factory('Menu', $id);

            $content->values = $menu->as_array();
            $content->items  = $menu->items->find_all()->as_array();
        }

        // Set other variables needed by view
    }

    /**
     * Create a new menu item that belongs to a menu
     *
     * @param   integer         Menu to create the item under
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
     * Edit an existing menu item that belongs to a menu
     *
     * @param   integer         Menu item to edit
     * @return  void
     */
    public function action_edit_item($id = NULL, $menu_id = NULL)
    {
        $session = Session::instance();

        $this->template = $content = View::factory('admin/menus/item');

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
                if ($menu_id !== NULL)
                {
                    $menu = ORM::factory('Menu', $menu_id);

                    // Create a new menu item
                    $item = ORM::factory('Menu_Item');
                    $item->menu_id = $menu->id;
                    $item->sort_id = $menu->items->count_all();
                }
                else
                {
                    // Load existing menu item
                    $item = ORM::factory('Menu_Item', $id);
                    $menu_id = $item->menu_id;
                }

                $type = Arr::get($post, 'type');

                // Update the model properties
                if ($type === 'page')
                {
                    $page = ORM::factory('Page')
                        ->where('slug', '=', $post['page'])
                        ->find();

                    $item->page_id = $page->id;
                    $item->title   = $post['page_title'];
                    $item->url     = NULL;

                    $rules = $item->rules_page();
                }
                else
                {
                    $item->page_id = NULL;
                    $item->title   = $post['custom_title'];
                    $item->url     = $post['url'];

                    $rules = $item->rules_custom();
                }

                $validation = Validation::factory($item->as_array());

                foreach ($rules as $field => $rule)
                {
                    $validation->rules($field, $rule);
                }

                $item->save($validation);

                $message = ($id === FALSE ? 'item.created' : 'item.updated');

                Flash::set(Kohana::message('forms/menu', $message), Flash::SUCCESS);
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $external_errors = Arr::get($content->errors, '_external', array());

                $content->errors = $content->errors + $external_errors;
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');

                $session->set('menu_item_errors', $content->errors);
                $session->set('menu_item_fields', $content->fields);
                $session->set('menu_item_failed', $this->request->uri());
                $session->set('menu_item_values', $post);

                $message = strtr(Kohana::message('forms/menu', 'item.failure'), array(
                        ':uri' => URL::site( $this->request->uri()) )
                    );

                Flash::set($message, Flash::ERROR);
            }

            $this->redirect( Route::get('panel_menus')->uri(array(
                    'action' => 'edit',
                    'id'     => $menu_id
                )) );
        }
        else if ($id !== FALSE)
        {
            $item = ORM::factory('Menu_Item', $id);

            $content->values = $item->as_array();
        }

        // Set other variables needed by view
        if ($session->get('menu_item_failed') === $this->request->uri())
        {
            $content->errors = Arr::merge($content->errors, $session->get_once('menu_item_errors', array()) );
            $content->fields = Arr::merge($content->fields, $session->get_once('menu_item_fields', array()) );
            $content->values = Arr::merge($content->values, $session->get_once('menu_item_values', array()) );

            $session->delete('menu_item_failed');
        }

        $content->menu_id = $menu_id;
    }

    /**
     * Delete an existing menu item from a menu
     *
     * @param   integer         Menu item to delete
     * @return  void
     */
    public function action_delete_item($id = NULL)
    {
        $this->template = $content = View::factory('admin/menus/item/delete');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $item = ORM::factory('Menu_Item', $id);

            $menu_id = $item->menu_id;

            try
            {
                $item->delete();

                Flash::set(Kohana::message('forms/menu', 'item.deleted'), Flash::SUCCESS);
            }
            catch (Exception $e)
            {
                Flash::set(Kohana::message('forms/menu', 'item.not_deleted'), Flash::ERROR);

                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
            }

            $this->redirect( Route::get('panel_menus')->uri(array(
                    'action' => 'edit',
                    'id'     => $menu_id
                )) );
        }
        else if ($id !== FALSE)
        {
            $item = ORM::factory('Menu_Item', $id);

            $content->item = $item->as_array();
        }

        // Set other variables needed by view
    }
}

