<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        $link = Route::get('panel_menus')->uri(array(
            'action' => 'delete_item',
            'id'     => $item['id']
        ));

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalDeleteForm'));
    ?>

        <p>Are you sure you wish to delete the menu item &quot;<?php echo $item['title']; ?>&quot;?</p>

        <?php echo Form::hidden('confirm', 'true'); ?>

    <?php
        echo Form::close();
    ?>

