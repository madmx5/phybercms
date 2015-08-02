<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Command line Instagram tasks.
 *
 * The following commands are available:
 *  - recent: fetch recent media from a subscribed endpoint
 *
 * @package     Application
 * @category    Tasks
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Task_Instagram extends Minion_Task {

    /**
     * @var     string      Remove media items added before this datetime
     */
    protected static $delete_before = '2013-11-08 00:00:00';

    /**
     * @var     array       Remove media items from these slugs before [Task_Instagram::$delete_before] datetime
     */
    protected static $delete_medias = array(
            'estancia',
        );

    /**
     * @var     array       Option defaults
     */
    protected $_options = array(
            'command' => NULL,
        );

    /**
     * @var     array       Request client params
     */
    protected $_request_client_params = array(
            'options' => array(
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_NOBODY => TRUE,
                ),
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
            case 'recent':
                $this->action_recent($params);
                break;

            case 'vetted':
                $this->action_vetted($params);
                break;

            case 'delete':
                $this->action_delete($params);
                break;

            case 'manual':
                $this->action_recent($params, TRUE);
                break;

            default:
        }
    }

    /**
     * Fetch recently updated media
     *
     * @param   array       Input parameters
     * @param   boolean     TRUE to fetch all media regardless of to_fetch flag
     * @return  void
     */
    public function action_recent(array $params, $all_media = FALSE)
    {
        $subscriptions = ORM::factory('Instagram_Subscription');

        if ($all_media === FALSE)
        {
            $subscriptions = $subscriptions->where('to_fetch', '=', 1);
        }

        $subscriptions = $subscriptions
                ->order_by('fetched_at', 'ASC')
                ->find_all();

        foreach ($subscriptions as $subscription)
        {
Kohana::$log->add(Log::DEBUG, "Instagram task is fetching recent media for: :title", array(
        ':title' => $subscription->title)
    );

            $json = Instagram::fetch_recent_media($subscription);

            if ( ! is_array($json) OR ! isset($json['data']))
            {
Kohana::$log->add(Log::WARNING, "Instagram task failed to obtain valid recent media result: :result", array(
        ':result' => print_r($json, TRUE))
    );

                continue;
            }

            foreach ($json['data'] as $data)
            {
                $images = Arr::get($data, 'images', array());

                $values = array(
                    'media_id'     => Arr::path($data, 'id'),
                    'created_time' => Arr::path($data, 'created_time'),
                    'link'         => Arr::path($data, 'link'),
                    'thumbnail'    => Arr::path($data, 'images.thumbnail.url'),
                    'full_size'    => Arr::path($data, 'images.standard_resolution.url'),
                    'caption'      => Arr::path($data, 'caption.text'),
                    'username'     => Arr::path($data, 'user.username'),
                    'fullname'     => Arr::path($data, 'user.full_name'),
                );

                $media = ORM::factory('Instagram_Media')->values($values);
                $media->subscription_id = $subscription->id;

                try
                {
                    $media->save();
                }
                catch (Exception $e)
                {
                    /* Kohana::$log->add(Log::ERROR, "Failed to save Instagram media: :message", array(
                            ':message' => $e->getMessage() )
                        ); */

                    continue;
                }

                $tags = Arr::get($data, 'tags', array());

                foreach ($tags as $tag)
                {
                    $values = array('media_id' => $media->id, 'tag' => $tag);

                    $media_tag = ORM::factory('Instagram_Media_Tag')->values($values);
                    $media_tag->save();
                }
            }

            $max_id = Arr::path($json, 'pagination.next_max_' . $subscription->object . '_id');

            $subscription->fetched_at = date('Y-m-d H:i:s');
            $subscription->max_id     = $max_id;
            $subscription->to_fetch   = FALSE;
            $subscription->save();
        }
    }

    /**
     * Vet recently fetched media to make sure it's valid
     *
     * @param   array       Input parameters
     * @return  void
     */
    public function action_vetted(array $params)
    {
        $media = ORM::factory('Instagram_Media')
            ->where('vetted', '=', 0)
            ->where('created_at', '<=', DB::expr('DATE_SUB(NOW(), INTERVAL 120 SECOND)'))
            ->find_all();

        $vetted = 0;
        $delete = 0;

        foreach ($media as $item)
        {
            $request = Request::factory($item->full_size, $this->_request_client_params);

            try
            {
                $respond = $request->execute();

                if ($respond->status() == 200)
                {
                    $item->vetted = TRUE;
                    $item->vetted_at = date('Y-m-d H:i:s');
                    $item->save();

                    $vetted += 1;
                }
                else
                {
Kohana::$log->add(Log::DEBUG, "Instagram media not vetted, removing from database: :id (:status)", array(
        ':id' => $item->id,
        ':status' => $respond->status())
    );

                    $item->delete();

                    $delete += 1;
                }
            }
            catch (Exception $e)
            {
Kohana::$log->add(Log::ERROR, "Instagram vetting exception for id :id: :message", array(
        ':id' => $item->id,
        ':message' => $e->getMessage())
    );
            }
        }

Kohana::$log->add(Log::DEBUG, "Instagram has vetted :vetted media items and deleted :delete", array(
        ':vetted' => $vetted,
        ':delete' => $delete)
    );

        // All done vetting media items...
    }

    /**
     * Remove media items added before the specified datetime
     *
     * @param   array       Input params
     * @return  void
     * @uses    Task_Instagram::$delete_before
     * @uses    Task_Instagram::$delete_medias
     */
    public function action_delete(array $params)
    {
        $media = ORM::factory('Instagram_Media')
            ->with('subscription')
            ->where('subscription.slug', 'IN', Task_Instagram::$delete_medias)
            ->where('instagram_media.created_at', '<', Task_Instagram::$delete_before)
            ->find_all();

        $delete = 0;

        foreach ($media as $item)
        {
            // Only remove media items created before the specific datetime
            $item->delete();

            $delete += 1;
        }

Kohana::$log->add(Log::DEBUG, "Instagram has deleted :delete media items before :before for: :medias", array(
        ':delete' => $delete,
        ':before' => Task_Instagram::$delete_before,
        ':medias' => implode(', ', Task_Instagram::$delete_medias))
    );

        // All done deleting media items...
    }
}

