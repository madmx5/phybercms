<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Pages frontend controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Pages extends Controller_Twig {

    /**
     * Pages view action
     *
     * @return  void
     */
    public function action_view()
    {
        $slug = $this->request->param('slug');

        $page = Page::get($slug, 'published', 'publish_at');

        if ($page === FALSE)
        {
            throw HTTP_Exception::factory(404, 'File not found');
        }

        $this->template = 'Page/' . $page->slug . '/content';

        parent::_initialize();
    }
}

