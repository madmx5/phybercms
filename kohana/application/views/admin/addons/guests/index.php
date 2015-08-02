<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="pull-right">
<?php echo $pagination->render(); ?>
        </div>

        <div class="page-header">
            <h3>All Guests</h3>
        </div>


        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Party</th>
                <th scope="col">Code</th>
                <th scope="col">Invited</th>
                <th scope="col">Attending</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($parties as $party) : ?>
            <tr>
                <td><?php

                    $link = Route::get('panel_guests')->uri(array(
                        'action' => 'edit',
                        'id'     => $party->id
                    ));

                    echo HTML::anchor($link, $party->name);

                ?></td>
                <td><?php echo $party->slug; ?></td>
                <td><?php
                    
                    $guests = $party->guests->count_all();
                    $attend = 0;

                    if ($guests > 0) :

                        $adults = 0;
                        $kinder = 0;

                        foreach ($party->guests->find_all() as $guest) :
                            if ($guest->adult) :
                                $adults += 1;
                            else :
                                $kinder += 1;
                            endif;

                            if ($guest->attending) :
                                $attend += 1;
                            endif;
                        endforeach;

                        $content = array();

                        if ($adults > 0) :
                            $content[] = '<i class="icon-extra-glass"></i> ' . $adults;
                        endif;

                        if ($kinder > 0) :
                            $content[] = '<i class="icon-extra-stroller"></i> ' . $kinder;
                        endif;

                        echo HTML::anchor($link, $guests, array(
                            'data-html'    => 'true',
                            'data-trigger' => 'hover',
                            'data-content' => implode(' &nbsp; ', $content),
                            'class'        => 'admin-popover',
                        ));
                    else :
                        echo 'none';
                    endif;

                ?></td>
                <td><?php

                    if ($attend == 0) :
                        echo 'none';
                    else :
                        echo $attend;
                    endif;

                ?></td>
            </tr>
<?php endforeach; ?>
        </tbody>
        </table>

        <?php echo HTML::anchor( Route::get('panel_guests')->uri(array('action' => 'create')), 'Create New Party', array('class' => 'btn btn-success')); ?>

    </div>

