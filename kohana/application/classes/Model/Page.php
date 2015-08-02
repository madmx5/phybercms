<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Page that contains content
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Model_Page extends ORM {

    /**
     * Get a list of available page status
     *
     * - List is suiteable for use in <select> elements
     * - List is not sorted
     *
     * @param   boolean     Include empty option
     * @return  array
     */
    public static function status_list($empty = TRUE)
    {
        $result = array();

        if ($empty === TRUE)
        {
            $result[NULL] = '';
        }

        return array_merge($result, array(
            'draft'     => 'Draft',
            'published' => 'Published',
            'deleted'   => 'Deleted',
            'hidden'    => 'Hidden',
        ));
    }

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
     * A page belongs to an author
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
        'author' => array(
            'model'       => 'User',
            'foreign_key' => 'author_id',
        ),
    );

    /**
     * Rules for the page model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'author_id' => array(
                array('not_empty'),
                array('numeric'),
            ),
            'status' => array(
                array('not_empty'),
            ),
            'title' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 140)),
            ),
            'slug' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 140)),
            ),
            'content' => array(
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
            'author_id' => 'author',
            'status'    => 'status',
            'title'     => 'page title',
            'slug'      => 'permalink',
            'content'   => 'content',
        );
    }

    /**
     * Create a new page
     *
     * Example usage:
     *     $page = ORM::factory('Page')->create_page($_POST, array(
     *         'author_id',
     *         'status',
     *         'title',
     *     );
     *
     * @param   array       $values
     * @param   array       $expected
     * @throws  [ORM_Validation_Exception]
     */
    public function create_page(array $values, array $expected)
    {
        return $this->values($values, $expected)->create();
    }
}

