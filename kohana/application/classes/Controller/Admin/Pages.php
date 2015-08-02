<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Admin pages controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Controller_Admin_Pages extends Controller_Admin {

    /**
     * Action that shows all pages
     *
     * @return  void
     */
    public function action_index()
    {
        $this->template->content = $content = View::factory('admin/pages/index');

        $pages = ORM::factory('Page');

        $content->pages = $pages->with('author')->find_all()->as_array();
    }

    /**
     * Create page action
     *
     * @return  void
     */
    public function action_create()
    {
        $this->action_edit(FALSE);
    }

    /**
     * Edit an existing page
     *
     * @param   integer     Page to edit or FALSE to create a new Page
     * @return  void
     */
    public function action_edit($id = NULL)
    {
        $this->template->content = $content = View::factory('admin/pages/create');

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
                $page = ORM::factory('Page', $id);

                // Update the model properties
                $page->author  = ORM::factory('User', Arr::get($post, 'author_id'));
                $page->status  = Arr::get($post, 'status');
                $page->title   = Arr::get($post, 'title');
                $page->slug    = Arr::get($post, 'slug');
                $page->content = Arr::get($post, 'content');

                if ($page->status === 'published')
                {
                    $publish_date = Arr::get($post, 'publish_at', NULL);

                    if (empty($publish_date))
                    {
                        $publish_date = date('Y-m-d');
                    }

                    // Published pages have a date set
                    $page->publish_at = date('Y-m-d', strtotime($publish_date));
                }
                else
                {
                    // All other pages are unpublished
                    $page->publish_at = NULL;
                }

                $page->save();

                $message = ($id === FALSE ? 'page.created' : 'page.updated');

                Flash::set(Kohana::message('forms/page', $message), Flash::SUCCESS);

                $this->redirect( Route::get('panel_pages')->uri(array(
                        'action' => 'edit',
                        'id' => $page->id
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
        else if ($id !== FALSE)
        {
            $page = ORM::factory('Page', $id);

            $content->values = $page->as_array();
        }
        else
        {
            $content->values['author_id'] = Auth::instance()->get_user()->id;
            $content->values['publish']   = date('m/d/Y');
        }

        $content->status_list = Model_Page::status_list();
        $content->author_list = Model_User::select_list();
    }

    /**
     * Delete an existing Page
     *
     * @return  void
     */
    public function action_delete()
    {
    }
    
    /**
     * Determine a slug for a given title
     *
     * @return  void
     */
    public function action_slug()
    {
        $this->auto_render = FALSE;
        
        $query = $this->request->query('query');

        $this->response->body( json_encode(array('slug' => URL::title($query))) );
    }

    /**
     * Suggest a page slug based upon a query
     *
     * @return  void
     */
    public function action_suggest()
    {
        $this->auto_render = FALSE;

        $query = $this->request->query('query');

        $pages = ORM::factory('Page')
            ->where('slug', 'REGEXP', $query)
            ->or_where('title', 'REGEXP', $query)
            ->find_all();

        $result = array();

        foreach ($pages as $page)
        {
            $result[] = $page->slug;
        }

        $this->response->headers('Content-Type', 'application/json');
        $this->response->body( json_encode(array('options' => $result)) );
    }
}

