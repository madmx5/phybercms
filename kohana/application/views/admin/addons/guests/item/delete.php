<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        $link = Route::get('panel_guests')->uri(array(
            'action'     => 'delete_item',
            'id'         => $item['id']
        ));

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalDeleteForm'));
    ?>

        <p>Are you sure you wish to delete <?php

            if (empty($item['name'])) :
                echo 'the selected guest';
            else :
                echo 'the guest &quot;' . $item['name'] . '&quot;';
            endif;
        
        ?>?</p>

        <?php echo Form::hidden('confirm', 'true'); ?>

    <?php
        echo Form::close();
    ?>

