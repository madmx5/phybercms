<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin media controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Admin_Media extends Controller_Admin {

    /**
     * Action that shows all media groups
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/media/index');

        $media = ORM::factory('Media');

        $content->media = $media->find_all()->as_array();
    }

    /**
     * Action to create a new media group
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Action to edit an existing media group
     *
     * @param   integer         Media group to edit or FALSE to create a new group
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/media/create');

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
                $media = ORM::factory('Media', $id);

                // Update the model properties
                $media->values($post, array('title', 'slug'));
                $media->save();

                // Apply new sort ordering if given
                $sort = Arr::get($post, 'sort');

                if (is_array($sort) AND ! empty($sort))
                {
                    $media->reorder_items($sort);
                }

                $message = ($id === FALSE ? 'media.created' : 'media.updated');

                Flash::set(Kohana::message('forms/media', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_media')->uri(array(
                        'action' => 'edit',
                        'id'     => $media->id
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

            $content->items = $media->items->find_all()->as_array();
        }
        else if ($id !== FALSE)
        {
            $media = ORM::factory('Media', $id);

            $content->values = $media->as_array();
            $content->items  = $media->items->find_all()->as_array();
        }

        // Set other variables needed by view
    }

    /**
     * Create a new media item that belongs to a media group
     *
     * @param   integer         Media group to create the item under
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
     * Edit an existing media item that belongs to a media group
     *
     * @param   integer         Media item to edit
     * @param   integer         Media group item belongs to
     * @return  void
     */
    public function action_edit_item($id = NULL, $media_id = NULL)
    {
        $session = Session::instance();

        $this->template = $content = View::factory('admin/media/item');

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
                if ($media_id !== NULL)
                {
                    $media = ORM::factory('Media', $media_id);

                    // Create a new media item
                    $item = ORM::factory('Media_Item');
                    $item->media_id = $media->id;
                    $item->sort_id  = $media->items->count_all();
                }
                else
                {
                    // Load existing media item
                    $item = ORM::factory('Media_Item', $id);
                    $media_id = $item->media_id;
                }

                if ( ! empty($post['url']) AND strpos($post['url'], '://') !== FALSE)
                {
                    // Save the remote filename to media
                    $post['url'] = Media::save_remote($post['url']);
                }

                if (isset($_FILES, $_FILES['media']) AND $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE)
                {
                    // Handle media file upload
                    $validation = Validation::factory($_FILES)
                            ->rule('media', 'Upload::not_empty')
                            ->rule('media', 'Upload::image');

                    if ($validation->check())
                    {
                        // Filename used to store media locally
                        $filename = NULL;

                        // Use the existing filename if it has not been modified and isn't external
                        if (Arr::get($post, 'url') === $item->url AND strpos($item->url, '://') === FALSE)
                        {
                            $filename = $item->url;
                        }

                        // Upload is valid, save it
                        $post['url'] = Media::save_upload($validation['media'], $filename);
                    }
                    else
                    {
                        throw new ORM_Validation_Exception(NULL, $validation);
                    }
                }

                $item->values($post, array('url', 'title', 'caption'));
                $item->save();

                $message = ($id === FALSE ? 'item.created' : 'item.updated');

                Flash::set(Kohana::message('forms/media', $message), Flash::SUCCESS);
            }
            catch (ORM_Validation_Exception $e)
            {
                $content->errors = $e->errors('forms');
                $content->fields = array_fill_keys(array_keys($content->errors), 'error');

                $session->set('media_item_errors', $content->errors);
                $session->set('media_item_fields', $content->fields);
                $session->set('media_item_failed', $this->request->uri());
                $session->set('media_item_values', $post);

                $message = strtr(Kohana::message('forms/media', 'item.failure'), array(
                        ':uri' => URL::site( $this->request->uri()) )
                    );

                Flash::set($message, Flash::ERROR);
            }

            $this->redirect( Route::get('panel_media')->uri(array(
                    'action' => 'edit',
                    'id'     => $media_id
                )) );
        }
        else if ($id !== FALSE)
        {
            $item = ORM::factory('Media_Item', $id);

            $content->values = $item->as_array();
        }

        // Set other variables needed by view
        if ($session->get('media_item_failed') === $this->request->uri())
        {
            $content->errors = Arr::merge($content->errors, $session->get_once('media_item_errors', array()) );
            $content->fields = Arr::merge($content->fields, $session->get_once('media_item_fields', array()) );
            $content->values = Arr::merge($content->values, $session->get_once('media_item_values', array()) );

            $session->delete('media_item_failed');
        }

        $content->media_id = $media_id;
    }

    /**
     * Delete an existing media item from a media group
     *
     * @param   integer         Media item to delete
     * @return  void
     */
    public function action_delete_item($id = NULL)
    {
        $this->template = $content = View::factory('admin/media/item/delete');

        if ($id === NULL)
        {
            $id = $this->request->param('id');
        }

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $item = ORM::factory('Media_Item', $id);

            $media_id = $item->media_id;

            try
            {
                $item->delete();

                Flash::set(Kohana::message('forms/media', 'item.deleted'), Flash::SUCCESS);
            }
            catch (Exception $e)
            {
                Flash::set(Kohana::message('forms/media', 'item.not_deleted'), Flash::ERROR);

                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
            }

            $this->redirect( Route::get('panel_media')->uri(array(
                    'action' => 'edit',
                    'id'     => $media_id
                )) );
        }
        else if ($id !== NULL)
        {
            $item = ORM::factory('Media_Item', $id);

            $content->item = $item->as_array();
        }

        // Set other variables needed by view
    }
}

