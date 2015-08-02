<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>Instagram Subscriptions</h3>
        </div>

        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Name</th>
                <th scope="col">Kind</th>
                <th scope="col">Items</th>
                <th scope="col">Updated</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($subscriptions as $subscription) : ?>
            <tr>
                <td><?php
                    $edit = Route::get('panel_instagram')->uri(array(
                        'action' => 'edit_item',
                        'id'     => $subscription->id
                    ));

                    echo HTML::anchor($edit, $subscription->title);
                ?></td>
                <td><?php echo $subscription->slug; ?></td>
                <td><?php
                    echo $subscription->object . ' / ' . $subscription->aspect;
                ?></td>
                <td><?php echo $subscription->media->count_all(); ?></td>
                <td><?php
                    if ($subscription->fetched_at === NULL) :
                        echo 'never';
                    else :
                        $fetched_at = strtotime($subscription->fetched_at);
                        echo Date::fuzzy_span($fetched_at);
                    endif;
                ?></td>
            </tr>
<?php endforeach; ?>
        </tbody>
        </table>

        <?php
            $link = Route::get('panel_instagram')->uri(array(
                'action' => 'create_item'
            ));

            echo HTML::anchor($link, 'Create New Subscription', array(
                'class'  => 'btn btn-small btn-success',
            ));
        ?>

    </div>

