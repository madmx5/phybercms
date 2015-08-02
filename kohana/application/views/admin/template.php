<?php defined('SYSPATH') OR die('No direct script access.'); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title><?php echo $_page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
    $_ = str_repeat(' ', 4);

    // Le styles
    foreach ($_stylesheets as $_stylesheet) :
        echo $_, HTML::style($_stylesheet), PHP_EOL;
    endforeach;
?>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <?php
                    echo HTML::anchor(Route::get('panel')->uri(), 'CMS Admin', array('class' => 'brand'));
                ?>

                <div class="nav-collapse collapse">
                    <p class="navbar-text pull-right">
                        Logged in as <?php echo HTML::entities($_user['username']); ?> (<a href="#">logout</a>)
                    </p>
                    <ul class="nav">
<?php
    $route = Request::current()->uri();

    foreach ($_navigation_items as $_name => $_item) :
        if (is_array($_item)) :
?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_name; ?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
<?php
            foreach ($_item as $_name => $_sub_item) :
?>
                                <li><?php echo HTML::anchor($_name, $_sub_item); ?></li>
<?php
            endforeach;
?>
                            </ul>
                        </li>
<?php
        else :
            if (strpos($route, $_name) !== FALSE) :
?>
                        <li class="active"><?php echo HTML::anchor($_name, $_item); ?></li>
<?php       else : ?>
                        <li><?php echo HTML::anchor($_name, $_item); ?></li>
<?php
            endif;
        endif;
    endforeach;
?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Flash Message -->
<?php echo Flash::get(); ?>
<!-- End Flash -->

<?php echo $content; ?>

<?php
    $_ = str_repeat(' ', 4);

    // Le javascript
    foreach ($_javascripts as $_javascript) :
        echo $_, HTML::script($_javascript), PHP_EOL;
    endforeach;
?>

</body>
</html>

