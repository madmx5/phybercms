<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Menu that contains menu items
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Model_Menu extends ORM {

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
     * A menu has zero or more menu items
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'items' => array(
                'model'       => 'Menu_Item',
                'foreign_key' => 'menu_id',
            ),
        );

    /**
     * Rules for the menu model
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
            'title' => 'menu title',
            'slug' => 'menu name',
        );
    }

    /**
     * Creates a new menu
     *
     * Example usage:
     *     $menu = ORM::factory('Menu')->create_menu($_POST, array(
     *         'title',
     *     );
     *
     * @param   array       $values
     * @param   array       $expected
     * @throws  [ORM_Validation_Exception]
     */
    public function create_menu(array $values, array $expected)
    {
        return $this->values($values, $expected)->create();
    }

    /**
     * Reorders the menu items by the array
     *
     * Example usage:
     *     $menu->reorder_items(array(
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

