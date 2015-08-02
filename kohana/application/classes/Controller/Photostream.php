<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Photostream controller
 * 
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Photostream extends Controller {

    /**
     * Called before the controller action to set the Content-type
     * of the controller response
     *
     * @return  void
     */
    public function before()
    {
        $this->response->headers('Content-type', 'application/json');

        parent::before();
    }

    /**
     * Action to retrieve Instagram media items that follow a
     * specified internal database ID
     *
     *     /photo-stream/mazda/after/42  // Return media items with ID > 42
     *
     * @return  void
     */
    public function action_after()
    {
        $this->_execute();
    }

    /**
     * Action to retrieve Instagram media items that preceed a
     * specified internal database ID
     *
     *     /photo-stream/mazda/before/42  // Return media items with ID < 42
     *
     * @return  void
     */
    public function action_before()
    {
        $this->_execute();
    }

    /**
     * Execute a request to find media items before or after
     * the offset provided in the URI
     *
     * @return  void
     */
    protected function _execute()
    {
        $action = $this->request->action();
        $inputs = $this->request->query();

        $offset = Arr::get($inputs, 'o');
        $limits = Arr::get($inputs, 'l');

        if ($offset === NULL)
        {
            $this->response->status('400');
            return;
        }

        switch ($action)
        {
            case 'after':
                $operator = '>';
                break;

            case 'before':
                $operator = '<';
                break;

            default:
                throw new Kohana_Exception('Unsupported photostream action: :action', array(
                        'action' => $action,
                    ));
        }

        $list = Arr::get($inputs, 's');

        if (is_scalar($list))
        {
            $list = preg_split('/[,:\|]/', $list);
        }

        if ( ! is_array($list) OR empty($list))
        {
            throw new Kohana_Exception('Streams not provided or invalid list of streams');
        }

        $more = Instagram::more($list, $operator, $offset, $limits);

        if ( ! is_array($more) OR empty($more))
        {
            $this->response->status('304');
            return;
        }

        $more = array_filter($more, array($this, '_strip_item_data'));

        $max_id = NULL;
        $min_id = NULL;

        foreach ($more as $item)
        {
            if ($item['id'] > $max_id OR $max_id === NULL)
                $max_id = $item['id'];

            if ($item['id'] < $min_id OR $min_id === NULL)
                $min_id = $item['id'];
        }

        switch ($action)
        {
            case 'after':
                $new_id = $max_id;
                break;

            case 'before':
                $new_id = $min_id;
                break;
        }

        $provider = Route::url('photostream', array(
                'action' => $this->request->action(),
            )) . '?' . http_build_query(array(
                'o'      => $new_id,
                's'      => $list,
            ));

        $this->response->body( json_encode(array(
                'items'    => $more,
                'offset'   => $new_id,
                'provider' => $provider,
                'slugs'    => $list,
            ), JSON_PRETTY_PRINT) );
    }

    /**
     * Remove unwanted meta data from a media item result array
     *
     * @param   array&      Media item array
     * @return  boolean
     */
    protected function _strip_item_data(array &$item)
    {
        unset($item['subscription_id'], $item['media_id'], $item['vetted'], $item['created_at'], $item['vetted_at']);

        return TRUE;
    }
}

