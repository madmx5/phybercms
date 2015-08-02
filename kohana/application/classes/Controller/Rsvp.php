<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Rsvp frontend controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Rsvp extends Controller_Twig {

    /**
     * @var     Model_Rsvp_Party        Invitation party object
     */
    protected $rsvp_party = NULL;

    /**
     * @var     Model_Rsvp_Guest        Invitation guest object
     */
    protected $rsvp_guest = NULL;

    /**
     * @var     array                   Invitation data sent by the client
     */
    protected $rsvp_array = NULL;

    /**
     * @var     array                   List of errors occuring on modify
     */
    protected $rsvp_error = NULL;

    /**
     * @var     integer                 The number of visits to the guest page
     */
    protected $rsvp_visit = 0;

    /**
     * Initializes the Rsvp controller
     *
     * @return  void
     */
    public function before()
    {
        parent::before();

        $this->_get_state();
    }

    /**
     * Restores the state of the Rsvp controller from Session instance
     *
     * @return  void
     */
    protected function _get_state()
    {
        $this->rsvp_party = Session::instance()->get('rsvp_party');
        $this->rsvp_guest = Session::instance()->get('rsvp_guest');
        $this->rsvp_array = Session::instance()->get('rsvp_array');
        $this->rsvp_error = Session::instance()->get('rsvp_error');
        $this->rsvp_visit = Session::instance()->get('rsvp_visit');
    }

    /**
     * Saves the state of the Rsvp controller to Session instance
     *
     * @return  void
     */
    protected function _set_state()
    {
        Session::instance()->set('rsvp_party', $this->rsvp_party);
        Session::instance()->set('rsvp_guest', $this->rsvp_guest);
        Session::instance()->set('rsvp_array', $this->rsvp_array);
        Session::instance()->set('rsvp_error', $this->rsvp_error);
        Session::instance()->set('rsvp_visit', $this->rsvp_visit);
    }

    /**
     * Default controller action
     *
     * @return  void
     */
    public function action_index()
    {
        if ($this->rsvp_party !== NULL AND $this->rsvp_guest != NULL)
        {
            $this->redirect( Route::get('rsvp')->uri(array('action' => 'guests')) );
        }

        $this->template = 'Page/rsvp-index/content';

        parent::_initialize();
    }

    /**
     * Action to reset RSVP state
     *
     * @return  void
     */
    public function action_clear()
    {
        $this->rsvp_party = NULL;
        $this->rsvp_guest = NULL;
        $this->rsvp_array = NULL;
        $this->rsvp_error = NULL;
        $this->rsvp_visit = 0;

        $this->_set_state();

        $this->redirect( Route::get('rsvp')->uri() );
    }

    /**
     * Find invitation action
     *
     * @return  void
     */
    public function action_find()
    {
        $this->template = 'Page/rsvp-find/content';

        parent::_initialize();

        $post = $this->request->post();

        if ( ! empty($post))
        {
            $validation = new Validation( $post );
            $validation->fields_rules( Rsvp::find_validation_rules() );

            if ( ! $validation->check())
            {
                $message = Kohana::message('app', 'rsvp.find.invalid');

                Flash::set($message, Flash::ERROR);

                $this->redirect( Route::get('rsvp')->uri() );
            }

            $rsvp_party = ORM::factory('Rsvp_Party', array('slug' => $validation['code']));

            if ( ! $rsvp_party->loaded())
            {
                $message = Kohana::message('app', 'rsvp.find.not_found');

                Flash::set($message, Flash::ERROR);

                $this->redirect( Route::get('rsvp')->uri() );
            }

            // Save the Rsvp_Party object as state
            $this->rsvp_party = $rsvp_party;

            $this->_set_state();
        }
    }

    /**
     * Verify invitation validity
     *
     * @return  void
     */
    public function action_verify()
    {
        $post = $this->request->post();

        $validation = new Validation( $post );
        $validation->fields_rules( Rsvp::verify_validation_rules() );

        if ( ! $validation->check())
        {
            $message = Kohana::message('app', 'rsvp.verify.invalid');

            Flash::set($message, Flash::ERROR);

            $route = Route::get('rsvp')->uri(array(
                'action' => 'find',
            ));

            $this->redirect($route);
        }

        $rsvp_guest = $this->rsvp_party
            ->guests
            ->where('rsvp_guest.last_name', '=', $validation['search'])
            ->find_all();

        if ( ! $rsvp_guest->count())
        {
            $message = Kohana::message('app', 'rsvp.verify.not_found');

            Flash::set($message, Flash::ERROR);

            $route = Route::get('rsvp')->uri(array(
                'action' => 'find',
            ));

            $this->redirect($route);
        }

        // Get the complete party guest list
        $rsvp_guest = $this->rsvp_party
            ->guests
            ->find_all();

        // Save the Rsvp_Guest object as state
        $this->rsvp_guest = $rsvp_guest->as_array();

        $this->_set_state();

        $route = Route::get('rsvp')->uri(array(
            'action' => 'guests',
        ));

        $this->redirect($route);
    }

    /**
     * Action to show and edit guest list
     *
     * @return  void
     */
    public function action_guests()
    {
        if ($this->rsvp_party === NULL OR $this->rsvp_guest === NULL)
        {
            $message = Kohana::message('app', 'rsvp.session.timeout');

            Flash::set($message, Flash::ERROR);

            $this->redirect( Route::get('rsvp')->uri() );
        }

        $this->template = 'Page/rsvp-guests/content';

        // Increment the number of visits
        $this->rsvp_visit += 1;

        $this->_set_state();

        parent::_initialize();
    }

    /**
     * Action to save changed made to reservation
     *
     * @return  void
     */
    public function action_modify()
    {
        if ($this->rsvp_party === NULL OR $this->rsvp_guest === NULL)
        {
            $message = Kohana::message('app', 'rsvp.session.timeout');

            Flash::set($message, Flash::ERROR);

            $this->redirect( Route::get('rsvp')->uri() );
        }

        $post = $this->request->post();

        $validation = Rsvp::modify_validation($post, $this->rsvp_party);

        if ( ! $validation->check())
        {
            $this->rsvp_array = $post;
            $this->rsvp_error = $validation->errors('app');

            $message = Kohana::message('app', 'rsvp.modify.failure');

            Flash::set($message, Flash::ERROR);

            $this->_set_state();

            $route = Route::get('rsvp')->uri(array(
                'action' => 'guests',
            ));

            $this->redirect($route);
        }

        $this->rsvp_party->modify_guests($this->request->post());

        Rsvp::notify_changes($this->rsvp_party);

        $this->rsvp_array = NULL;
        $this->rsvp_error = NULL;

        $this->_set_state();

        $message = Kohana::message('app', 'rsvp.modify.success');

        Flash::set($message, Flash::SUCCESS);

        $route = Route::get('rsvp')->uri(array(
            'action' => 'guests',
        ));

        $this->redirect($route);
    }
}

