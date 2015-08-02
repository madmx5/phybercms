<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Media item
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Model_Media_Item extends ORM {

    /**
     * @var     string      Name of the database table
     */
    protected $_table_name = 'media_items';

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
     * @var     array       Default sorting for object
     */
    protected $_sorting = array(
            'sort_id' => 'ASC',
        );

    /**
     * A media item belongs to a media group
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'media' => array(
                'model'       => 'Media',
                'foreign_key' => 'media_id',
            ),
        );

    /**
     * Rules for the media item model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'media_id' => array(
                array('not_empty'),
            ),

            'url' => array(
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
            'title' => array(
                array('trim'),
            ),
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
            'media_id' => 'media',
        );
    }

    /**
     * Creates a new media item
     *
     * Example usage:
     *     $item = ORM::factory('Media_Item')->create_item($_POST, array(
     *         'media',
     *     );
     *
     * @param   array       $values
     * @param   array       $expected
     * @throws  [ORM_Validation_Exception]
     */
    public function create_item(array $values, array $expected)
    {
        return $this->values($values, $expected)->create();
    }

    /**
     * Deletes a media item maintaining sort ordering
     *
     * @return  Model_Media_Item
     */
    public function delete()
    {
        $sort_id = $this->sort_id;

        DB::update($this->_table_name)
            ->set(array(
                'sort_id' => DB::expr('sort_id-1'),
            ))
            ->where('media_id', '=', $this->media_id)
            ->and_where('sort_id', '>', $sort_id)
            ->execute($this->_db);

        return parent::delete();
    }

    /**
     * Moves a media item below the next item in the media group
     *
     * @return  Model_Media_item
     */
    public function move_down()
    {
        $next_item = ORM::factory('Media_Item')
            ->where('media_id', '=', $this->media_id)
            ->and_where('sort_id', '>', $this->sort_id)
            ->order_by('sort_id', 'ASC')
            ->limit(1)
            ->find();

        if ($next_item->loaded())
        {
            $sort_id = $next_item->sort_id;

            $next_item->sort_id = $this->sort_id;
            $next_item->save();

            $this->sort_id = $sort_id;
            $this->save();
        }

        return $this;
    }

    /**
     * Moves a media item above the previous item in the media group
     *
     * @return  Model_Media_Item
     */
    public function move_up()
    {
        $prev_item = ORM::factory('Media_Item')
            ->where('media_id', '=', $this->media_id)
            ->and_where('sort_id', '<', $this->sort_id)
            ->order_by('sort_id', 'DESC')
            ->limit(1)
            ->find();

        if ($prev_item->loaded())
        {
            $sort_id = $prev_item->sort_id;

            $prev_item->sort_id = $this->sort_id;
            $prev_item->save();

            $this->sort_id = $sort_id;
            $this->save();
        }

        return $this;
    }
}

