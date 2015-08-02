<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Instagram Subscription model
 *
 * @package     Application
 * @category    Instagram
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Instagram_Subscription extends ORM {

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
     * A subscription has many media items 
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'media' => array(
                'model'       => 'Instagram_Media',
                'foreign_key' => 'subscription_id',
            ),
        );


    /**
     * Rules for Instagram subscriptions
     *
     * @return  array       Rules
     */
    public function rules()
    {
        return array(
            'title' => array(
                array('not_empty'),
            ),

            'slug' => array(
                array('not_empty'),
            ),

            'client_id' => array(
                array('not_empty'),
            ),

            'client_secret' => array(
                array('not_empty'),
            ),

            'token' => array(
                array('not_empty'),
            ),

            'object' => array(
                array('not_empty'),
            ),

            'aspect' => array(
                array('not_empty'),
            ),
        );
    }
}

