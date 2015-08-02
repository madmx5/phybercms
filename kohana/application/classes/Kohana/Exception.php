<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Kohana Exception class. Translates exceptions using the [I18n] class.
 *
 * @package     Application
 * @category    Exceptions
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Kohana_Exception extends Kohana_Kohana_Exception {

    /**
     * Exception handler, logs the exception and generates a Response object
     * for display.
     *
     * @param   Exception       Exception to be handled
     * @return  void            When Kohana::$environment >= Kohana::DEVELOPMENT
     * @return  Response        When Kohana::$environment  < Kohana::DEVELOPMENT
     */
    public static function _handler(Exception $e)
    {
        if (Kohana::$environment < Kohana::DEVELOPMENT)
        {
            $response = HTTP_Exception::factory(500, 'Application error', NULL, $e)->get_response();

            // Send the response to the browser
            echo $response->send_headers()->body();

            exit(1);
        }

        return parent::_handler($e);
    }
}

