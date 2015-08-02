<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Command line user tasks.
 *
 * The following commands are available:
 *  - add: this command will add a user
 *  - del: this command will delete a user
 *
 * @package     Application
 * @category    Tasks
 * @author      Todd Wirth
 * @copyright   (c) 2012
 */
class Task_User extends Minion_Task {

    /**
     * @var     array       Option defaults
     */
    protected $_options = array(
        'command'  => NULL,
        'username' => NULL,
        'email'    => NULL,
        'admin'    => NULL,
    );

    /**
     * Execute the task
     *
     * @return  void
     */
    protected function _execute(array $params)
    {
        switch ($params['command'])
        {
            case 'add':
                $this->action_add($params);
                break;

            case 'del':
                $this->action_del($params);
                break;

            default:
        }
    }

    /**
     * Create a new user
     *
     * @param   array       Input parameters
     * @return  void
     */
    public function action_add(array $params)
    {
        if ( ! isset($params['email']) OR $params['email'] === NULL)
        {
            $params['email'] = Minion_CLI::read('   Email');
        }

        $params['password'] = Minion_CLI::password('Password');
        $params['password_confirm'] = Minion_CLI::password(' Confirm');

        try
        {
            $user = ORM::factory('User')->create_user($params, array(
                'username',
                'password',
                'email' ));

            $user->add('roles', ORM::factory('Role', array('name' => 'login')));

            if (isset($params['admin']) AND $params['admin'] !== NULL)
            {
                $user->add('roles', ORM::factory('Role', array('name' => 'admin')));
            }

            Minion_CLI::write('Created user "' . $user->username . '" (' . $user->email . ') with ID ' . $user->id);
        }
        catch (ORM_Validation_Exception $e)
        {
            Minion_CLI::write('Failed to add user, the following errors have occurred:');
            Minion_CLI::write($e->errors('orm-validation'));
        }
    }

    /**
     * Delete a user
     *
     * @param   array       Input parameters
     * @return  void
     */
    public function action_del(array $params)
    {
        $user = ORM::factory('User', array('username' => $params['username']));

        if ($user->loaded())
        {
            $confirm = Minion_CLI::read('Are you sure?', array('y', 'n'));

            if (strtolower($confirm) === 'y')
            {
                Minion_CLI::write('Deleted user "' . $user->username . '" (' . $user->email . ') with ID ' . $user->id);

                $user->delete();
            }
        }
        else
        {
            Minion_CLI::write('Could not find user "' . $params['username'] . '"');
        }
    }

    /**
     * Build command validation
     *
     * @param   Validation  Validation object
     * @return  Validation
     */
    public function build_validation(Validation $validation)
    {
        return parent::build_validation($validation)
            ->rule('command', 'not_empty')
            ->rule('username', 'not_empty');
    }
}

