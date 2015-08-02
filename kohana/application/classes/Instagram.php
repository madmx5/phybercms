<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Instagram helper
 *
 * @package     Application
 * @category    Instagram
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Instagram {

    /**
     * @const   integer             Cache lifetime of Instagram media items
     */
    const ITEM_CACHE_LIFETIME = 10;

    /**
     * @var     array               URLs to the Instagram endpoints APIs
     */
    public static $endpoints_url = array(
            'recent' => array(
                'geography' => 'https://api.instagram.com/v1/:object/:obj_id/:aspect/recent',
                'location'  => 'https://api.instagram.com/v1/:object/:obj_id/:aspect/recent',
                'tag'       => 'https://api.instagram.com/v1/:object/:obj_id/:aspect/recent',
            ),
        );

    /**
     * @var     string              URL to the Instagram subscriptions API
     */
    public static $subscriptions_url = 'https://api.instagram.com/v1/subscriptions';

    /**
     * Create a new real-time Instagram subscription
     *
     * [http://instagram.com/developer/realtime/]
     *
     * @param   object              Model_Instagram_Subscription
     * @return  boolean
     */
    public static function create_subscription(Model_Instagram_Subscription $subscription)
    {
        if ($subscription->api_id !== NULL)
        {
            Instagram::delete_subscription($subscription);
        }

        $route = Route::get('instagram')->uri(array('tag' => $subscription->slug));

        $params = array(
                'client_id'     => $subscription->client_id,
                'client_secret' => $subscription->client_secret,
                'object'        => $subscription->object,
                'aspect'        => $subscription->aspect,
                'verify_token'  => $subscription->token,
                'callback_url'  => URL::base('http', TRUE) . $route,
            );

        $custom = array();
        parse_str($subscription->params, $custom);

        // Merge custom parameters with API requirements
        $params = array_merge($params, $custom);

        // Prepare the API request
        $request = Request::factory(Instagram::$subscriptions_url)
                ->method(Request::POST)
                ->post($params);

        $response = Instagram::_preform_api_call($request);

        if ( ! is_array($response) OR ! isset($response['data']))
        {
            Kohana::$log->add(Log::ERROR, "Instagram subscription failed (:code): :message", array(
                    ':code'    => Arr::path($response, 'meta.code'),
                    ':message' => Arr::path($response, 'meta.error_message')
                ) );

            return FALSE;
        }

        $subscription->api_id = Arr::path($response, 'data.id');
        $subscription->obj_id = Arr::path($response, 'data.object_id');

        return TRUE;
    }

    /**
     * Delete an existing Instagram real-time subscription
     *
     * [http://instagram.com/developer/realtime/]
     *
     * @param   object              Model_Instagram_Subscription
     * @return  boolean
     */
    public static function delete_subscription(Model_Instagram_Subscription $subscription)
    {
        $params = array(
                'client_id'     => $subscription->client_id,
                'client_secret' => $subscription->client_secret,
                'id'            => $subscription->api_id,
            );

        $request = Request::factory(Instagram::$subscriptions_url)
                ->method(Request::DELETE)
                ->query($params);

        $response = Instagram::_preform_api_call($request);

        if ($response !== NULL)
        {
            $subscription->api_id = NULL;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Handle a subscription challenge from Instagram API
     *
     * [http://instagram.com/developer/realtime/]
     *
     * @param   array           Input parameters
     * @return  boolean         FALSE if the challenge is rejected
     * @return  string          response of challenge is accepted
     */
    public static function verify_subscription($slug, array $params)
    {
        if ( ! isset($params['hub_mode'], $params['hub_challenge'], $params['hub_verify_token']))
        {
            return FALSE;
        }

        if (strcasecmp($params['hub_mode'], 'subscribe') !== 0)
        {
            return FALSE;
        }

        $token = Arr::get($params, 'hub_verify_token');

        $subscription = Instagram::subscription($slug);

        if ( ! $subscription->loaded())
        {
            return FALSE;
        }

        if (strcasecmp($token, $subscription->token) === 0)
        {
            return Arr::get($params, 'hub_challenge');
        }

        return FALSE;
    }

    /**
     * Return an Instagram subscription and associated media
     *
     * @param   mixed           Instagram subscription slug name or array of names
     * @param   integer         Offset of result set
     * @param   integer         Limit the number of results returned
     * @param   boolean         TRUE for vetted items, FALSE for non-vetted, NULL for both
     * @return  array           Subscription data and media items
     */
    public static function get($slugs, $offset = NULL, $limit = NULL, $vetted = TRUE)
    {
        if ( ! is_array($slugs))
        {
            $slugs = array($slugs);
        }

        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('|', $slugs) . '-' . implode('-', array_slice(func_get_args(), 1));

        if ($data = $cache->get($c_key))
        {
            return $data;
        }

        $list = Instagram::subscription_ids($slugs);
        $data = array();

        $find = ORM::factory('Instagram_Media')
            ->where('subscription_id', 'IN', $list);

        if ($vetted !== NULL)
        {
            $find->where('vetted', '=', $vetted);
        }

        if ($offset !== NULL)
        {
            $find->offset($offset);
        }

        if ($limit !== NULL)
        {
            $find->limit($limit);
        }

        $find = $find->find_all();

        foreach ($find as $item)
        {
            $data[] = $item->as_array();
        }

        $cache->set($c_key, $data, Instagram::ITEM_CACHE_LIFETIME);

        return $data;
    }

    /**
     * Return a Instagram media items starting with an internal offset id
     *
     *     $offset_id = 42;
     *     $limit = 10;
     *     $items = Instagram::more('mazda', '>', $offset_id, $limit);
     *
     * @param   mixed           Instagram subscription slug name or array of names
     * @param   string          Operator for comparing offset IDs
     * @param   integer         Internal offset ID (primary key of instagram_media table)
     * @param   integer         Limit the number of results returned
     * @param   boolean         TRUE for vetted items, FALSE for non-vetted, NULL for both
     * @return  array           Media items
     */
    public static function more($slugs, $operator, $offset_id, $limit = NULL, $vetted = TRUE)
    {
        if ( ! is_array($slugs))
        {
            $slugs = array($slugs);
        }

        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('|', $slugs) . '-' . implode('-', array_slice(func_get_args(), 1));

        if ($data = $cache->get($c_key))
        {
            return $data;
        }

        $list = Instagram::subscription_ids($slugs);
        $data = array();

        $find = ORM::factory('Instagram_Media')
            ->where('subscription_id', 'IN', $list)
            ->where('id', $operator, $offset_id);

        if ($vetted !== NULL)
        {
            $find->where('vetted', '=', $vetted);
        }

        if ($limit !== NULL)
        {
            $find->limit($limit);
        }

        $find = $find->find_all();

        foreach ($find as $item)
        {
            $data[] = $item->as_array();
        }

        $cache->set($c_key, $data, Instagram::ITEM_CACHE_LIFETIME);

        return $data;
    }

    /**
     * Return an Instagram subscription by name
     *
     * @param   string          Subscription slug
     * @return  object          Model_Instagram_Subscription
     */
    public static function subscription($slug)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('-', func_get_args());

        if ($subscription = $cache->get($c_key))
        {
            return $subscription;
        }

        $subscription = ORM::factory('Instagram_Subscription', array('slug' => $slug));

        $cache->set($c_key, $subscription);

        return $subscription;
    }

    /**
     * Return a list of Instagram subscription ids from a list of slugs
     *
     * @param   array           List of subscription slugs
     * @return  array
     */
    public static function subscription_ids(array $slugs)
    {
        $cache = Cache::instance();
        $c_key = __METHOD__ . '-' . implode('|', $slugs);

        if ($list = $cache->get($c_key))
        {
            return $list;
        }

        $list = array();

        $subs = ORM::factory('Instagram_Subscription')
            ->where('slug', 'IN', $slugs)
            ->find_all();

        foreach ($subs as $sub)
        {
            $list[] = $sub->id;
        }

        $cache->set($c_key, $list);

        return $list;
    }

    /**
     * Verify that a message is valid and has not been tampered with
     *
     * @param   string          Message to validate
     * @param   string          Message digest
     * @param   string          Shared secret used to generate digest
     * @return  boolean
     */
    public static function valid_signature($message, $signature, $secret)
    {
        return hash_hmac('sha1', $message, $secret) === $signature;
    }

    /**
     * Fetch recent media from the Instagram Endpoints API
     *
     * [http://instagram.com/developer/endpoints/]
     *
     * @param   object          Model_Instagram_Subscription
     * @param   boolean         TRUE to return result as array, FALSE as JSON object
     * @return  mixed
     */
    public static function fetch_recent_media(Model_Instagram_Subscription $subscription, $as_array = TRUE)
    {
        $api_url = Arr::get( Instagram::$endpoints_url['recent'], $subscription->object );

        $api_url = strtr($api_url, array(
                ':object' => Inflector::plural($subscription->object),
                ':aspect' => $subscription->aspect,
                ':api_id' => $subscription->api_id,
                ':obj_id' => $subscription->obj_id,
            ));

        $params = array('client_id' => $subscription->client_id);

        if ($subscription->max_id !== NULL)
        {
            $params['max_' . $subscription->object . '_id'] = $subscription->max_id;
        }

        $request = Request::factory($api_url)->query($params);

        return Instagram::_preform_api_call($request, $as_array);
    }

    /**
     * Preform an Instagram API call
     *
     * [http://instagram.com/developer/]
     *
     * @param   object          Request object to execute
     * @param   boolean         TRUE to return result as array, FALSE as JSON object
     * @return  mixed
     */
    protected static function _preform_api_call(Request $request, $as_array = TRUE)
    {
        try
        {
            $response = $request->execute();

            if ($response->status() != 200)
            {
                Kohana::$log->add(Log::ERROR, "Unsuccessful result from Instagram API (:code): :body", array(
                        ':code' => $response->status(), ':body' => $response->body() )
                    );

                return FALSE;
            }

            $data = $response->body();

            return json_decode($data, $as_array);
        }
        catch (Exception $e)
        {
            Kohana::$log->add(Log::ERROR, "Unexpected result from Instagram API: :message", array(
                    ':message' => $e->getMessage() )
                );
        }

        return FALSE;
    }
}

