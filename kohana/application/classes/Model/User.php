<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * User object
 *
 * @package     Application
 * @category    Models
 * @author      Todd Wirth
 * @copyright   (c) 2012
 * @see         [Model_Auth_User]
 */
class Model_User extends Model_Auth_User {

    /**
     * Get a list of users
     *
     * - Suitable for use in <select> element
     * - List is sorted by username
     *
     * @param   boolean         Include empty selection
     * @return  array
     */
    public static function select_list($empty = TRUE)
    {
        $user = Model::factory('User');

        $list = $user->find_all();

        if ($list instanceof Database_Result)
        {
            $result = array();

            if ($empty == TRUE)
            {
                $result[NULL] = '';
            }

            foreach ($list as $author)
            {
                $result[$author->id] = Text::ucfirst($author->username);
            }

            asort($result);

            return $result;
        }

        return array();
    }
}

