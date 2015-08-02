<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Instagram controller class
 *
 * @package     Application
 * @category    Controller
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Controller_Instagram extends Controller {

    /**
     * Ingest new media from Instagram API
     *
     * [http://instagram.com/developer/realtime/]
     *
     * @return  void
     */
    public function action_ingest()
    {
        $query = $this->request->query();
        $param = $this->request->param('tag');

        if ( ! empty($query))
        {
            $response = Instagram::verify_subscription($param, $query);

            if ($response === FALSE)
            {
                $this->response->status(401);
                $this->response->body('Token verification failed.');
            }
            else
            {
                $this->response->body($response);
            }

            Kohana::$log->add(Log::DEBUG, "Instagram Ingest(:tag) subscribe: :body", array(
                    ':tag' => $param, ':body' => $this->response->body())
                );

            return;
        }

        $subscription = Instagram::subscription($param);

        if ( ! $subscription->loaded())
        {
            $this->response->status('404');
            $this->response->body('Tag not found.');
            return;
        }

        $msg = $this->request->body();
        $sig = $this->request->headers('X-Hub-Signature');

        if (Instagram::valid_signature($msg, $sig, $subscription->client_secret))
        {
            $subscription->to_fetch = TRUE;
            $subscription->save();

            Kohana::$log->add(Log::DEBUG, "Instagram Ingest(:tag) \":sig\" :body", array(
                    ':tag' => $param, ':sig' => $sig, ':body' => $msg)
                );
        }
        else
        {
            Kohana::$log->add(Log::ERROR, "Instagram Ingest(:tag) \":sig\" Verification failed.", array(
                    ':tag' => $param, ':sig' => $sig)
                );
        }

        $this->response->body('OK');
    }
}

