<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset item
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Model_Asset_Item extends ORM {

    /**
     * @var     string      Name of the database table
     */
    protected $_table_name = 'asset_items';

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
     * A asset item belongs to a asset group
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'asset' => array(
                'model'       => 'Asset',
                'foreign_key' => 'asset_id',
            ),
        );

    /**
     * Rules for the asset item model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'asset_id' => array(
                array('not_empty'),
            ),

            'filename' => array(
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
            'asset_id' => 'asset',
        );
    }

    /**
     * Creates a new asset item
     *
     * Example usage:
     *     $item = ORM::factory('Asset_Item')->create_item($_POST, array(
     *         'asset',
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
}

