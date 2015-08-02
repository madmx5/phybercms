<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        $link = Route::get('panel_media')->uri(array(
            'action'     => 'delete_item',
            'id'         => $item['id']
        ));

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalDeleteForm'));
    ?>

        <p>Are you sure you wish to delete <?php

            if (empty($item['title'])) :
                echo 'the selected media item';
            else :
                echo 'the media item &quot;' . $item['title'] . '&quot;';
            endif;
        
        ?>?</p>

        <?php echo Form::hidden('confirm', 'true'); ?>

    <?php
        echo Form::close();
    ?>

