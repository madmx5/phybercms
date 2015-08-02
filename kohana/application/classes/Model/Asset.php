<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset group that contains items
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Asset extends ORM {

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
     * Assets have zero or more asset items
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'items' => array(
                'model'       => 'Asset_Item',
                'foreign_key' => 'asset_id',
            ),
        );

    /**
     * Rules for the asset model
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

            'path' => array(
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
            'title' => 'asset title',
            'slug' => 'asset name',
            'path' => 'asset pathname',
        );
    }

    /**
     * Creates a new asset group
     *
     * Example usage:
     *     $asset = ORM::factory('Asset')->create_asset($_POST, array(
     *         'title',
     *     );
     *
     * @param   array       $values
     * @param   array       $expected
     * @throws  [ORM_Validation_Exception]
     */
    public function create_asset(array $values, array $expected)
    {
        return $this->values($values, $expected)->create();
    }
}

