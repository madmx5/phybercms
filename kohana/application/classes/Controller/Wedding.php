<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Wedding frontend controller
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Wedding extends Controller_Twig {

    /**
     * @var     string      Regular expression that matches valid code words
     */
    protected $valid_codes = '/^(arrazola|arrazola\-hurt|hurt|wirth)$/i';

    /**
     * Location action displays driving directions
     *
     * @return  void
     */
    public function action_location()
    {
        $code = $this->request->post('code');

        if ( ! empty($code))
        {
            $code = trim(preg_replace('/\s{2,}/', ' ', $code));
        }

        $rsvp = Rsvp::visit();

        if (preg_match($this->valid_codes, $code) OR $rsvp > 0)
        {
            // Visitor has entered a valid code or has already RSVP'd
            $this->template = 'Page/wedding-location-valid/content';
        }
        else
        {
            // Visitor is invalid or is visiting for the first time
            $this->template = 'Page/wedding-location-index/content';
        }

        parent::_initialize();
    }
}

