<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>All Media Groups</h3>
        </div>

        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Name</th>
                <th scope="col">Items</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($media as $group) : ?>
            <tr>
                <td><?php
                    $link = Route::get('panel_media')->uri(array(
                        'action' => 'edit',
                        'id'     => $group->id
                    ));
                    
                    echo HTML::anchor($link, $group->title);
                ?></td>
                <td><?php
                    echo HTML::entities($group->slug);
                ?></td>
                <td><?php
                    $item = $group->items->count_all();

                    if ($item > 0) :
                        echo $item;
                    else :
                        echo 'none';
                    endif;
                ?></td>
            </tr>
<?php endforeach; ?>

        </tbody>
        </table>

        <?php echo HTML::anchor( Route::get('panel_media')->uri(array('action' => 'create')), 'Create New Group', array('class' => 'btn btn-success')); ?>

    </div>

