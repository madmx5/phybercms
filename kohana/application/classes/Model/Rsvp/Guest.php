<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * RSVP Guest model
 *
 * @package     Application
 * @category    RSVP
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Rsvp_Guest extends ORM {

    /**
     * @var     string      Database table name
     */
    protected $_table_name = 'rsvp_guests';

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
     * Guests belong to a party
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'party' => array(
                'model'       => 'Rsvp_Party',
                'foreign_key' => 'party_id',
            ),

            'meal' => array(
                'model'       => 'Rsvp_Meal',
                'foreign_key' => 'meal_id',
            ),
        );

    /**
     * Rules for the guest model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'adult' => array(
                array('numeric'),
            ),

            'plus_one' => array(
                array('numeric'),
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
            'first_name' => array(
                array('Filter::proper_noun'),
                array('Filter::null_empty'),
            ),

            'last_name'  => array(
                array('Filter::proper_noun'),
                array('Filter::null_empty'),
            ),

            'gender'     => array(
                array('Filter::null_empty'),
            ),

            'attending'  => array(
                array('Filter::null_empty'),
            ),

            'meal_id'    => array(
                array('Filter::null_empty'),
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
            'first_name' => 'first name',
            'last_name'  => 'last name',
            'meal_id'    => 'meal',
        );
    }

    /**
     * Return the full name of a guest
     *
     * @return  string
     */
    public function get_name()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Set the first_name and last_name properties from a single value
     *
     * @param   string      Full name value
     * @return  ORM
     */
    public function set_name($value)
    {
        if ($value === NULL)
        {
            $this->first_name = $this->last_name = NULL;

            return $this;
        }

        if (preg_match('/\p{Zs}++/u', $value))
        {
            list($first_name, $last_name) = preg_split('/\p{Zs}++/u', $value, 2);
        }
        else
        {
            $first_name = $value;
            $last_name  = NULL;
        }

        $this->first_name = $first_name;
        $this->last_name  = $last_name;

        return $this;
    }

    /**
     * Return the object as an associative array
     *
     * @return  array
     */
    public function as_array()
    {
        return array_merge(parent::as_array(), array(
            'name' => $this->get_name(),
        ));
    }
}

