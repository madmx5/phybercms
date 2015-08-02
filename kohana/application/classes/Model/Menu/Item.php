<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Menu item
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Model_Menu_Item extends ORM {

    /**
     * @var     string      Name of the database table
     */
    protected $_table_name = 'menu_items';

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
     * A menu item belongs to a menu
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'menu' => array(
                'model'       => 'Menu',
                'foreign_key' => 'menu_id',
            ),

            'page' => array(
                'model'       => 'Page',
                'foreign_key' => 'page_id',
            ),
        );

    /**
     * Rules for the menu item model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'menu_id' => array(
                array('not_empty'),
            ),
        );
    }

    /**
     * Rules for a menu item of type 'page'
     *
     * @return  array       Rules
     */
    public function rules_page()
    {
        return array(
            'page_id' => array(
                array('not_empty'),
            ),
        );
    }

    /**
     * Rules for a menu item of type 'custom'
     *
     * @return  array       Rules
     */
    public function rules_custom()
    {
        return array(
            'title' => array(
                array('not_empty'),
            ),

            'url' => array(
                array('not_empty'),
                array('url'),
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
                array(array($this, 'filter_title'), array(':value'))
            ),
        );
    }

    /**
     * Filter the menu item title
     *
     * @param   string      Title to filter
     * @return  mixed
     */
    public function filter_title($title)
    {
        if (empty($title))
        {
            return NULL;
        }

        if ($this->page_id !== NULL)
        {
            $page_title = ORM::factory('Page', $this->page_id)->title;

            if ($page_title === $title)
            {
                return NULL;
            }
        }

        return $title;
    }

    /**
     * Labels for fields in this model
     *
     * @return  array       Labels
     */
    public function labels()
    {
        return array(
            'menu_id' => 'menu',
            'page_id' => 'page',
        );
    }

    /**
     * Creates a new menu item
     *
     * Example usage:
     *     $item = ORM::factory('Menu_Item')->create_item($_POST, array(
     *         'menu',
     *         'page',
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
     * Deletes a menu item maintaining sort ordering
     *
     * @return  Model_Menu_Item
     */
    public function delete()
    {
        $sort_id = $this->sort_id;

        DB::update($this->_table_name)
            ->set(array(
                'sort_id' => DB::expr('sort_id-1'),
            ))
            ->where('menu_id', '=', $this->menu_id)
            ->and_where('sort_id', '>', $sort_id)
            ->execute($this->_db);

        return parent::delete();
    }

    /**
     * Handles getting of column
     *
     * @param   string      $column         Column name
     * @return  mixed
     * @throws  [Kohana_Exception]
     */
    public function get($column)
    {
        if ($column === 'title' AND parent::get('title') === NULL AND parent::get('page_id') !== NULL)
        {
            return $this->page->title;
        }

        if ($column === 'url' AND parent::get('url') === NULL AND parent::get('page_id') !== NULL)
        {
            return $this->page->slug;
        }

        return parent::get($column);
    }
}

