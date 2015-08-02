
<div class="pagination">
    <ul>
<?php if ($previous_page !== FALSE) : ?>
        <li><?php echo HTML::anchor($page->url($previous_page), '&laquo;'); ?></li>
<?php else : ?>
        <li class="disabled"><?php echo HTML::anchor($page->url($previous_page), '&laquo;'); ?></li>
<?php endif; ?>

<?php for ($i = 1; $i <= $total_pages; $i++) : ?>
<?php if ($i == $current_page) : ?>
        <li class="active"><?php echo HTML::anchor($page->url($i), $i); ?></li>
<?php else : ?>
        <li><?php echo HTML::anchor($page->url($i), $i); ?></li>
<?php endif; ?>
<?php endfor; ?>

<?php if ($next_page !== FALSE) : ?>
        <li><?php echo HTML::anchor($page->url($next_page), '&raquo;'); ?></li>
<?php else : ?>
        <li class="disabled"><?php echo HTML::anchor($page->url($next_page), '&raquo;'); ?></li>
<?php endif; ?>
    </ul>
</div>

