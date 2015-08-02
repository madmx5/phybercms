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

    <div class="container">

        <div class="page-header">
            <h1><?php echo $_page_title; ?></h1>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="control-group<?php
                if (isset($errors, $errors['username'])) :
                    echo ' error';
                endif;
            ?>">
                <label class="control-label" for="inputUsername">Email</label>
                <div class="controls">
                    <?php
                        echo Form::input('username', $username, array(
                            'id' => 'inputUsername',
                            'placeholder' => 'Email address'
                        ));

                        if (isset($errors, $errors['username'])) :
                            echo '<span class="help-inline">' . HTML::entities($errors['username']) . '</span>';
                        endif;
                    ?>
                </div>
            </div>
            <div class="control-group<?php
                if (isset($errors, $errors['password'])) :
                    echo ' error';
                endif;
            ?>">
                <label class="control-label" for="inputUsername">Password</label>
                <div class="controls">
                    <?php
                        echo Form::input('password', $password, array(
                            'id' => 'inputPassword',
                            'placeholder' => 'Password',
                            'type' => 'password'
                        ));

                        if (isset($errors, $errors['password'])) :
                            echo '<span class="help-inline">' . HTML::entities($errors['password']) . '</span>';
                        endif;
                    ?>
                </div>
            </div>
            <div class="control-group warning">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Sign in</button>
                    <span class="help-inline"><?php

                        if ($message = Session::instance()->get_once('flash')) :
                            echo HTML::entities($message);
                        endif;

                    ?></span>
                </div>
            </div>
        </form>

    </div>

<?php
    $_ = str_repeat(' ', 4);

    // Le javascript
    foreach ($_javascripts as $_javascript) :
        echo $_, HTML::script($_javascript), PHP_EOL;
    endforeach;
?>

</body>
</html>

