<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Instagram Media model
 *
 * @package     Application
 * @category    Instagram
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Instagram_Media extends ORM {

    /**
     * @var     string      Database table name
     */
    protected $_table_name = 'instagram_media';

    /**
     * @var     string      Auto-update columns for creation
     */
    protected $_created_column = array(
            'column' => 'created_at',
            'format' => 'Y-m-d H:i:s',
        );

    /**
     * A media item belongs to a subscription
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'subscription' => array(
                'model'       => 'Instagram_Subscription',
                'foreign_key' => 'subscription_id',
            ),
        );

    /**
     * A media item has many tags
     *
     * @var     array       Relationships
     */
    protected $_has_many = array(
            'tags' => array(
                'model'       => 'Instagram_Media_Tag',
                'foreign_key' => 'media_id',
            ),
        );

    /**
     * @var     array       Default sorting for object
     */
    protected $_sorting = array(
            'id' => 'DESC',
        );
}

