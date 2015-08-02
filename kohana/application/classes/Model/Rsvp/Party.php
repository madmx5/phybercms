<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * RSVP Party model
 *
 * @package     Application
 * @category    RSVP
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Rsvp_Party extends ORM {

    /**
     * @var     string      Database table name
     */
    protected $_table_name = 'rsvp_parties';

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
     * Parties have zero or more guests
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'guests' => array(
                'model'       => 'Rsvp_Guest',
                'far_key'     => 'id',
                'foreign_key' => 'party_id',
                'through'     => 'rsvp_guests',
            ),
        );

    /**
     * Rules for the party model
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'slug' => array(
                array('not_empty'),
            ),

            'name' => array(
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
            'slug' => 'reservation code',
            'name' => 'party name',
        );
    }

    /**
     * Modify the invitation settings of party guests
     *
     *     $party->modify_guests(array(
     *         'guest{1}' => array('attending' =>  TRUE, 'dinner_id' => 3),
     *         'guest{4}' => array('attending' => FALSE, 'dinner_id' => NULL),
     *     ));
     *
     * @param   array       Data array containing guest information
     * @return  boolean
     */
    public function modify_guests(array $data)
    {
        $guests = $this->guests->find_all();
        $result = TRUE;

        foreach ($guests as $guest)
        {
            $rsvp = Arr::get($data, 'guest{' . $guest->id . '}', array());

            $guest->attending = Arr::get($rsvp, 'attending', FALSE);

            if ($guest->attending)
            {
                if ($guest->plus_one)
                {
                    // If guest is a "plus one" their name can be changed
                    $guest->name = Arr::get($rsvp, 'full_name', NULL);
                }

                // Don't forget to update the meal
                $guest->meal_id = Arr::get($rsvp, 'dinner_id', NULL);
            }
            else
            {
                if ($guest->plus_one)
                {
                    // Only remove guest name if they are a "plus one"
                    $guest->name = NULL;
                }

                $guest->meal_id = NULL;
            }

            $result = $guest->save() AND $result;
        }

        return $result;
    }
}
