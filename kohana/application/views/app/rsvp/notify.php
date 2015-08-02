<?php defined('SYSPATH') OR die('No direct script access.');

    foreach ($party->guests->find_all() as $guest) :
?>
* <?php

    if ($guest->name == '') :
        echo 'Additional Guest';
    else :
        echo $guest->name;
    endif;

    if ($guest->attending) :
        echo ' will attend and have: ' . $guest->meal->name;
    else :
        echo ' will not attend';
    endif;

?>

<?php endforeach; ?>
---
Remote Address: <?php echo Arr::get($_SERVER, 'REMOTE_ADDR', 'localhost'); ?>

