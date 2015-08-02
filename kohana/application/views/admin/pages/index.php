<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>All Pages</h3>
        </div>

        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Author</th>
                <th scope="col">Permalink</th>
                <th scope="col">Status</th>
                <th scope="col">Date</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($pages as $page) :
?>
            <tr>
                <td><?php
                    $link = Route::get('panel_pages')->uri(array(
                        'controller' => 'pages',
                        'action'     => 'edit',
                        'id'         => $page->id
                    ));

                    echo HTML::anchor($link, $page->title);
                ?></td>
                <td><?php echo $page->author->username; ?></td>
                <td><?php
                    $link = Route::get('pages')->uri(array(
                        'id' => $page->slug
                    ));

                    echo HTML::anchor($link, $page->slug);
                ?></td>
                <td><?php echo $page->status; ?></td>
                <td><?php
                    switch ($page->status) :
                        default:
                        case 'drafted':
                        case 'hidden':
                            echo $page->updated_at ? : $page->created_at;
                            break;

                        case 'published':
                            echo $page->publish_at;
                            break;

                        case 'deleted':
                            echo $page->deleted_at;
                            break;
                    endswitch;
                ?></td>
            </tr>
<?php
    endforeach;
?>
        </tbody>
        </table>

        <?php echo HTML::anchor( Route::get('panel_pages')->uri(array('action' => 'create')), 'Create New Page', array('class' => 'btn btn-success')); ?>

    </div>

