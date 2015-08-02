<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Instagram Media Tag model
 *
 * @package     Application
 * @category    Instagram
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Model_Instagram_Media_Tag extends ORM {

    /**
     * @var     string      Database table name
     */
    protected $_table_name = 'instagram_media_tags';

    /**
     * A media item belongs to a subscription
     *
     * @var     array       Relationships
     */
    protected $_belongs_to = array(
            'media' => array(
                'model'       => 'instagram_media',
                'foreign_key' => 'media_id',
            ),
        );
}


