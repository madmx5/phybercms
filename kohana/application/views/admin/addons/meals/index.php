<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>All Meals</h3>
        </div>

        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Guests</th>
            </tr>
        </thead>
        <tbody>
<?php
    $totals = 0;

    foreach ($meals as $meal) :
?>
            <tr>
                <td><?php

                    $link = Route::get('panel_meals')->uri(array(
                        'action'     => 'edit',
                        'id'         => $meal->id
                    ));

                    echo HTML::anchor($link, $meal->name);

                ?></td>
                <td><?php

                    echo HTML::entities( Text::limit_words($meal->description, 12, '...') );

                ?></td>
                <td><?php
                    $guests = $meal->guests->count_all();

                    if ($guests == 0) :
                        echo 'none';
                    else :
                        echo $guests;
                    endif;

                    $totals += $guests;
                ?></td>
            </tr>
<?php
    endforeach;
?>
            <tr>
                <th colspan="2"><div class="text-right">Total Guests</div></th>
                <td><strong><?php echo $totals; ?></strong></td>
            </tr>
        </tbody>
        </table>

        <?php
            echo HTML::anchor( Route::get('panel_meals')->uri(array(
                'action' => 'Create'
            )), 'Create New Meal', array(
                'class'  => 'btn btn-success'
            ));
        ?>

    </div>

