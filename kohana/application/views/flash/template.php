<?php defined('SYSPATH') OR die('No direct script access.');

switch ($level) :
    case Flash::SUCCESS :
        $class = 'alert-success';
        $alert = 'Well done!';
        break;

    case Flash::ERROR :
        $class = 'alert-error';
        $alert = 'Oh snap!';
        break;

    case Flash::INFO :
        $class = 'alert-info';
        $alert = 'Heads up!';
        break;

    default:
        $class = '';
        $alert = 'Warning!';
        break;
endswitch;

?>

<div class="container flash">
    <div class="alert <?php echo $class; ?>">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong><?php echo $alert; ?></strong> <?php echo $message; ?>
    </div>
</div>

