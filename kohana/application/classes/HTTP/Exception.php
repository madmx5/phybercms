<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * HTTP Exception handler
 *
 * @package     Application
 * @category    Exceptions
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class HTTP_Exception extends Kohana_HTTP_Exception {

    /**
     * Generate a Response for all Exceptions without a more specific override
     *
     * The user should see a nice error page, however, if we are in development
     * mode we should show the normal Kohana error page.
     *
     * @return  Response
     */
    public function get_response()
    {
        if ($this->getPrevious())
        {
            // Logging the Exception that caused the error is more important!
            Kohana_Exception::log($this->getPrevious());
        }
        else
        {
            // Log the Exception, just in case it's important!
            Kohana_Exception::log($this);
        }

        if (Kohana::$environment >= Kohana::DEVELOPMENT)
        {
            // Show the normal Kohana error page.
            return parent::get_response();
        }
        else
        {
            // Generate a nice looking "Oops" page.
            $view = View::factory('errors/template')
                ->set('title', $this->getCode())
                ->set('message', $this->getMessage());

            $response = Response::factory()
                ->status($this->getCode())
                ->body($view->render());

            return $response;
        }
    }
}

