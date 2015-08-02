<?php defined('SYSPATH') OR die('No direct script access.');

return array
(
    'rsvp' => array(

        'find'    => array(
                'invalid'   => 'The invitation code entered was invalid, please try again.',
                'not_found' => 'The invitation code could not be found, please try again.',
            ),

        'verify'  => array(
                'invalid'   => 'We could not verify that you are a registered guest; please try again.',
                'not_found' => 'We could not verify any of the guests in your party; please try again.',
            ),

        'modify'  => array(
                'success'   => 'Changes have been saved, thank you for taking the time to RSVP.',
                'failure'   => 'One or more errors have occurred, please review and correct them below.',
            ),

        'session' => array(
                'timeout'   => 'Your session has timed out, please search for your RSVP again.',
            ),

        'notify'  => array(
                'subject'   => '[RSVP] :party_name',
            ),
        ),

    'Rsvp::valid_meal' => 'Please select a meal option',

    'Rsvp::valid_name' => 'Please provide a guest name',
);

