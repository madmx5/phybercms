<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Rsvp helper
 *
 * @package     Application
 * @category    Helpers
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Rsvp {

    /**
     * @var     array       Request client params
     */
    protected static $_request_client_params = array(
            'options' => array(
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_TIMEOUT => 20,
                ),
            );

    /**
     * Validation rules for finding an Rsvp
     *
     * @return  array
     */
    public static function find_validation_rules()
    {
        return array(
            'code' => array(
                array('not_empty'),
                array('min_length', array(':value', 3)),
                array('max_length', array(':value', 16)),
                array('alpha_numeric'),
            ),
        );
    }

    /**
     * Validation rules for verifying an Rsvp
     *
     * @return  array
     */
    public static function verify_validation_rules()
    {
        return array(
            'search' => array(
                array('not_empty'),
                array('min_length', array(':value', 2)),
                array('max_length', array(':value', 45)),
                array('alpha'),
            ),
        );
    }

    /**
     * Validation rules for modifying an Rsvp
     *
     * @return  array
     */
    public static function modify_validation(array $fields, Model_Rsvp_Party $party)
    {
        $validation = new Validation( $fields );

        foreach ($party->guests->find_all() as $guest)
        {
            $rules = array();

            if ($guest->plus_one)
            {
                $rules[] = array('Rsvp::valid_name', array($guest->id, ':value', ':validation'));
            }

            $rules[] = array('Rsvp::valid_meal', array($guest->id, $guest->adult, ':value', ':validation'));

            $validation->rules('guest{' . $guest->id . '}', $rules);
        }

        return $validation;
    }

    /**
     * Determine if a guest meal choice is valid or not
     *
     * @param   integer     Primary key value of guest (id)
     * @param   boolean     TRUE if guest is an adult, FALSE otherwise
     * @param   mixed       Array if rsvp is completed, NULL otherwise
     * @param   Validation  Validation object used to record errors
     * @return  boolean
     */
    public static function valid_meal($guest_id, $guest_adult, $rsvp, Validation $validation)
    {
        if ( ! is_array($rsvp) OR empty($rsvp))
        {
            // Assume guest is not attending and ignore
            return TRUE;
        }

        $attending = Arr::get($rsvp, 'attending', FALSE);
        $dinner_id = Arr::get($rsvp, 'dinner_id',  NULL);

        if ($attending AND empty($dinner_id))
        {
            return FALSE;
        }
        else if ($attending AND ! Model_Rsvp_Meal::valid($dinner_id, $guest_adult))
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Determine if a guest name is valid or not
     *
     * @param   integer     Primary key value of guest (id)
     * @param   mixed       Array if rsvp is completed, NULL otherwise
     * @param   Validation  Validation object used to record errors
     * @return  boolean
     */
    public static function valid_name($guest_id, $rsvp, Validation $validation)
    {
        if ( ! is_array($rsvp) OR empty($rsvp))
        {
            // Assume guest is not attending and ignore
            return TRUE;
        }

        $attending = Arr::get($rsvp, 'attending', FALSE);
        $full_name = Arr::get($rsvp, 'full_name',  NULL);

        if ($attending AND empty($full_name))
        {
            return FALSE;
        }
        else if ($attending AND ! Valid::regex($full_name, '/^[\pL\pN\p{Zs}\.\-]++$/uD'))
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Return the current RSVP party
     *
     * @return  array
     */
    public static function party()
    {
        $party = Session::instance()->get('rsvp_party');

        if ($party instanceof ORM)
        {
            return $party->as_array();
        }

        return $party;
    }

    /**
     * Return the current RSVP party guest list merged with
     * any changes being made within the active session
     *
     * @return  array
     */
    public static function guest()
    {
        $guests = Session::instance()->get('rsvp_guest', array());
        $arrays = Session::instance()->get('rsvp_array', array());
        $result = array();

        foreach ($guests as $guest)
        {
            if ($guest instanceof ORM)
            {
                $guest_arr = $guest->as_array();
                $guest_key = 'guest{' . $guest->id . '}';

                if (isset($arrays[$guest_key]))
                {
                    if (isset($arrays[$guest_key]['attending']))
                        $guest_arr['attending'] = $arrays[$guest_key]['attending'];

                    if (isset($arrays[$guest_key]['dinner_id']))
                        $guest_arr['meal_id']   = $arrays[$guest_key]['dinner_id'];

                    if (isset($arrays[$guest_key]['full_name']))
                        $guest_arr['name']      = $arrays[$guest_key]['full_name'];
                }

                $result[] = $guest_arr;
            }
        }

        return $result;
    }

    /**
     * Return the list of current RSVP session errors
     *
     * @param   integer     Guest primary key (id)
     * @return  array
     */
    public static function error($guest_id = NULL)
    {
        $error = Session::instance()->get('rsvp_error', array());

        if ($guest_id !== NULL)
        {
            return Arr::get($error, 'guest{' . $guest_id . '}', NULL);
        }

        return $error;
    }

    /**
     * Get a list of meals for a specific guest
     *
     * @param   array       Guest data array
     * @param   boolean     Include blank entry
     * @return  array
     */
    public static function meals(array $guest = NULL, $blank = TRUE)
    {
        $adult = is_array($guest) ? Arr::get($guest, 'adult') : NULL;

        return Model_Rsvp_Meal::select_list($blank, $adult);
    }

    /**
     * Return the number of visits to the RSVP guests page
     *
     * @return  integer
     */
    public static function visit()
    {
        return (int) Session::instance()->get('rsvp_visit', 0);
    }

    /**
     * Notify the admin of changes to a RSVP
     *
     * @param   ORM         Model_Rsvp_Party object
     * @return  void
     */
    public static function notify_changes(Model_Rsvp_Party $party)
    {
        $configs = Kohana::$config->load('app.rsvp.notify');

        if ( ! isset($configs['apiurl'], $configs['apikey']))
        {
            return FALSE;
        }

        $request = Request::factory($configs['apiurl'], Rsvp::$_request_client_params, TRUE);
        $request->method(Request::POST);

        $subject = Kohana::message('app', 'rsvp.notify.subject');
        $subject = strtr($subject, array(
                ':party_id'   => $party->id,
                ':party_slug' => $party->slug,
                ':party_name' => $party->name,
            ));

        $message = View::factory('app/rsvp/notify');
        $message->set('party', $party);

        $request->post(array(
                'apikey'      => $configs['apikey'],
                'providerkey' => $configs['providerkey'],
                'application' => 'Red and Fox',
                'event'       => $subject,
                'description' => $message->render(),
            ));

        try
        {
            return $request->execute();
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, 'Unable to send RSVP notification: ' . $e->getMessage());
        }

        return FALSE;
    }
}

