<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Media group that contains items
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Media extends ORM {

    /**
     * @var     string      Auto-update columns for creation
     */
    protected $_created_column = array(
            'column' => 'created_at',
            'format' => 'Y-m-d H:i:s',
        );

    /**
     * @var     string      Auto-update columns for updates
     */
    protected $_updated_column = array(
            'column' => 'updated_at',
            'format' => 'Y-m-d H:i:s',
        );

    /**
     * Media have zero or more media items
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'items' => array(
                'model'       => 'Media_Item',
                'foreign_key' => 'media_id',
            ),
        );

    /**
     * Rules for the media model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'title' => array(
                array('not_empty'),
            ),

            'slug'  => array(
                array('not_empty'),
            ),
        );
    }

    /**
     * Filters to run when data is set in this model
     *
     * @return  array       Filters
     */
    public function filters()
    {
        return array(
        );
    }

    /**
     * Labels for fields in this model
     *
     * @return  array       Labels
     */
    public function labels()
    {
        return array(
            'title' => 'media title',
            'slug' => 'media name',
        );
    }

    /**
     * Creates a new media group
     *
     * Example usage:
     *     $media = ORM::factory('Media')->create_media($_POST, array(
     *         'title',
     *     );
     *
     * @param   array       $values
     * @param   array       $expected
     * @throws  [ORM_Validation_Exception]
     */
    public function create_media(array $values, array $expected)
    {
        return $this->values($values, $expected)->create();
    }

    /**
     * Reorders the media items by the array
     *
     * Example usage:
     *     $media->reorder_items(array(
     *         0 => 64,  // order id => item id
     *         1 => 72,
     *         2 => 84,
     *      ));
     *
     * @param   array       $ordering
     * @return  void
     */
    public function reorder_items(array $ordering)
    {
        $items = $this->items->find_all();
        $order = array_flip($ordering);

        foreach ($items as $item)
        {
            if (isset($order[$item->id]))
            {
                $item->sort_id = $order[$item->id];
                $item->save();
            }
        }
    }
}

