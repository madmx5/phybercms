<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>Recent Instagram Media</h3>
        </div>

        <div>
            <ul class="thumbnails">
<?php foreach ($media as $item) : ?>
                <li class="span2"><?php

                    $link = Route::get('panel_instagram')->uri(array(
                        'action' => 'view_item',
                        'id'     => $item->id,
                    ));

                    echo HTML::anchor($link, '<img src="' . $item->thumbnail . '">', array(
                        'class' => 'thumbnail',
                        'data-target' => '#modalViewer',
                        'data-toggle' => 'modal',
                    ));

                ?></li>
<?php endforeach; ?>
            </ul>
        </div>

    </div>

<div id="modalViewer" class="modal modal-wide hide">
    <div class="modal-body"></div>
</div>

