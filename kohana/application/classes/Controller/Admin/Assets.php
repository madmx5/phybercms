<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin asset controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Admin_Assets extends Controller_Admin {

    /**
     * Action that shows all asset groups
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/assets/index');

        $assets = ORM::factory('Asset');

        $content->assets = $assets->find_all()->as_array();
    }

    /**
     * Action to create a new asset group
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Action to edit an existing asset group
     *
     * @param   integer         Asset group to edit or FALSE to create a new group
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/assets/create');

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
                $asset = ORM::factory('Asset', $id);

                // Update the model properties
                $asset->values($post, array('title', 'slug', 'path'));
                $asset->save();

                $message = ($id === FALSE ? 'asset.created' : 'asset.updated');

                Flash::set(Kohana::message('forms/assets', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_assets')->uri(array(
                        'action' => 'edit',
                        'id'     => $asset->id
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

            $content->items = $asset->items->find_all()->as_array();
        }
        else if ($id !== FALSE)
        {
            $asset = ORM::factory('Asset', $id);

            $content->values = $asset->as_array();
            $content->items  = $asset->items->find_all()->as_array();
        }

        // Set other variables needed by view
    }

    /**
     * Create a new asset item that belongs to a asset group
     * @param   integer         Asset group to create the item under
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
     * Edit an existing asset item that belongs to an asset group
     *
     * @param   integer         Asset item to edit
     * @return  void
     */
    public function action_edit_item($id = NULL, $asset_id = NULL)
    {
        $session = Session::instance();

        $this->template = $content = View::factory('admin/assets/item');

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
                if ($asset_id !== NULL)
                {
                    $asset = ORM::factory('Asset', $asset_id);

                    // Create a new asset item
                    $item = ORM::factory('Asset_Item');
                    $item->asset_id = $asset->id;
                }
                else
                {
                    // Load existing asset item
                    $item = ORM::factory('Asset_Item', $id);
                    $asset_id = $item->asset_id;
                }

                if (isset($_FILES, $_FILES['asset']) AND $_FILES['asset']['error'] !== UPLOAD_ERR_NO_FILE)
                {
                    // Handle asset file upload
                    $validation = Validation::factory($_FILES)
                            ->rule('asset', 'Upload::not_empty')
                            ->rule('asset', 'Upload::valid');

                    if ($validation->check() AND Asset::save_upload($item, $validation['asset']))
                    {
                        // Asset was stored without error
                        $item->mimetype = Asset::mimetype_upload($_FILES['asset']);
                    }
                    else
                    {
                        throw new ORM_Validation_Exception(NULL, $validation);
                    }
                }

                $item->values($post, array('filename', 'editable'));
                $item->save();

                $message = ($id === FALSE ? 'item.created' : 'item.updated');

                Flash::set(Kohana::message('forms/assets', $message), Flash::SUCCESS);
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');

                $session->set('asset_item_errors', $content->errors);
                $session->set('asset_item_fields', $content->fields);
                $session->set('asset_item_failed', $this->request->uri());
                $session->set('asset_item_values', $post);

                $message = strtr(Kohana::message('forms/assets', 'item.failure'), array(
                        ':uri' => URL::site( $this->request->uri()) )
                    );

                Flash::set($message, Flash::ERROR);
            }

            $this->redirect( Route::get('panel_assets')->uri(array(
                    'action' => 'edit',
                    'id'     => $asset_id
                )) );
        }
        else if ($id !== FALSE)
        {
            $item = ORM::factory('Asset_Item', $id);

            $content->values = $item->as_array();
        }

        // Set other variables needed by view
        if ($session->get('asset_item_failed') === $this->request->uri())
        {
            $content->errors = Arr::merge($content->errors, $session->get_once('asset_item_errors', array()) );
            $content->fields = Arr::merge($content->fields, $session->get_once('asset_item_fields', array()) );
            $content->values = Arr::merge($content->values, $session->get_once('asset_item_values', array()) );

            $session->delete('asset_item_failed');
        }

        $content->asset_id = $asset_id;
    }

    /**
     * Delete an existing asset item from a asset group
     *
     * @param   integer         Asset item to delete
     * @return  void
     */
    public function action_delete_item($id = NULL)
    {
        $this->template = $content = View::factory('admin/assets/item/delete');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $item = ORM::factory('Asset_Item', $id);

            $asset_id = $item->asset_id;

            try
            {
                $item->delete();

                Flash::set(Kohana::message('forms/assets', 'item.deleted'), Flash::SUCCESS);
            }
            catch (Exception $e)
            {
                Flash::set(Kohana::message('forms/assets', 'item.not_deleted'), Flash::ERROR);

                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
            }

            $this->redirect( Route::get('panel_assets')->uri(array(
                    'action' => 'edit',
                    'id'     => $asset_id
                )) );
        }
        else if ($id !== NULL)
        {
            $item = ORM::factory('Asset_Item', $id);

            $content->item = $item->as_array();
        }

        // Set other variables needed by view
    }

    /**
     * Edit the content of an asset item
     *
     * @param   integer         Asset item to edit
     * @return  void
     */
    public function action_content_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/assets/item/content');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        // Obtain the Asset Item object
        $item = ORM::factory('Asset_Item', $id);

        $content->errors = array();
        $content->fields = array();
        $content->values = array();

        $post = $this->request->post();

        if ( ! empty($post))
        {
            try
            {
                $string = Arr::get($post, 'contents', NULL);

                if (isset($post['detect_mime']) AND $post['detect_mime'])
                {
                    $item->mimetype = Asset::mimetype_buffer($string);
                }
                else
                {
                    $item->mimetype = Arr::get($post, 'mimetype', 'text/plain');
                }

                $item->values($post, array('filename'));
                $item->save();

                Asset::save_buffer($item, $string);

                Flash::set(Kohana::message('forms/assets', 'contents.updated'), Flash::SUCCESS);

                $this->redirect( Route::get('panel_assets')->uri(array(
                        'action' => 'content_edit',
                        'id'     => $item->id
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
        }
        else if ($id !== NULL)
        {
            $content->values = $item->as_array();
        }

        // Set other variables needed by view
        $content->asset = $item->asset->as_array();
        $content->contents = Asset::contents($item);
    }

    /**
     * View the contents of an asset item
     *
     * @param   integer         Asset item to view
     * @return  void
     */
    public function action_content_view($id = NULL)
    {
        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        // Obtain the Asset Item object
        $item = ORM::factory('Asset_Item', $id);

        $this->redirect( Asset::uri($item) );
    }
}

