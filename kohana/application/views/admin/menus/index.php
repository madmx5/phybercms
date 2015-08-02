<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>All Menus</h3>
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
<?php foreach ($menus as $menu) : ?>
            <tr>
                <td><?php
                    $link = Route::get('panel_menus')->uri(array(
                        'action' => 'edit',
                        'id'     => $menu->id
                    ));
                    
                    echo HTML::anchor($link, $menu->title);
                ?></td>
                <td><?php
                    echo HTML::entities($menu->slug);
                ?></td>
                <td><?php
                    $item = $menu->items->count_all();

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

        <?php echo HTML::anchor( Route::get('panel_menus')->uri(array('action' => 'Create')), 'Create New Menu', array('class' => 'btn btn-success')); ?>

    </div>

