<?php defined('SYSPATH') OR die('No direct script access.'); ?>

<!-- <h3><?php echo $item->caption; ?></h3> -->

<div class="row">
    <div class="span4">
        <img alt="" src="<?php echo $item->full_size; ?>">
    </div>
    <div class="span2" style="overflow: auto; max-height: 370px;">
        <p><b>username</b><br><?php echo HTML::entities($item->username); ?></p>

        <p><b>fullname</b><br><?php echo HTML::entities($item->fullname); ?></p>

        <p><b>taken at</b><br><?php echo date('Y-m-d H:i:s', $item->created_time); ?></p>

        <p><b> caption</b><br><?php echo HTML::entities($item->caption); ?></p>
    </div>
</div>

