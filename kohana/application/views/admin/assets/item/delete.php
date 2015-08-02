<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        $link = Route::get('panel_assets')->uri(array(
            'action' => 'delete_item',
            'id'     => $item['id']
        ));

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalDeleteForm'));
    ?>

        <p>Are you sure you wish to delete <?php

            if (empty($item['filename'])) :
                echo 'the selected asset item';
            else :
                echo 'the asset item &quot;' . $item['filename'] . '&quot;';
            endif;
        
        ?>?</p>

        <?php echo Form::hidden('confirm', 'true'); ?>

    <?php
        echo Form::close();
    ?>

