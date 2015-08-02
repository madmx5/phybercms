<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * RSVP Meal model
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Rsvp_Meal extends ORM {

    /**
     * Get a list of meals
     *
     * - Suitable for use in <select> element
     * - List is sorted by name
     *
     * @param   boolean     Include empty selection
     * @param   boolean     Filter for "adult" meals
     * @return  array
     */
    public static function select_list($empty = TRUE, $adult = NULL)
    {
        $meal = Model::factory('Rsvp_Meal');

        if ($adult !== NULL)
        {
            $meal = $meal->where('adult', '=', $adult);
        }

        $list = $meal->find_all();

        if ($list instanceof Database_Result)
        {
            $result = array();

            if ($empty == TRUE)
            {
                $result[NULL] = '';
            }

            foreach ($list as $item)
            {
                $result[$item->id] = $item->name;
            }

            asort($result);

            return $result;
        }

        return array();
    }

    /**
     * Determine if a meal is valid
     *
     * @param   integer     Meal primary key value
     * @param   boolean     Check adult flag or NULL to skip
     * @return  boolean
     */
    public static function valid($value, $adult = NULL)
    {
        if (empty($value))
        {
            return FALSE;
        }

        $meal = ORM::factory('Rsvp_Meal', $value);

        if ( ! $meal->loaded())
        {
            return FALSE;
        }

        if ($adult !== NULL)
        {
            return ($adult == $meal->adult);
        }

        return TRUE;
    }

    /**
     * @var     string      Database table name
     */
    protected $_table_name = 'rsvp_meals';

    /**
     * A meal is eaten by many guests
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'guests' => array(
                    'model'       => 'Rsvp_Guest',
                    'foreign_key' => 'meal_id'
                ),
        );

    /**
     * Rules for the meal model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
            ),

            'description' => array(
                array('not_empty'),
            ),
        );
    }
}

