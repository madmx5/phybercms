<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Menu';
                else :
                    echo 'Create New Menu';
                endif;
            ?></h3>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="control-group <?php echo Arr::get($fields, 'title'); ?>">
                <label class="control-label" for="inputTitle">Title</label>
                <div class="controls">
                    <?php
                        $title = Arr::get($values, 'title');

                        echo Form::input('title', $title, array(
                            'class' => 'span3',
                            'placeholder' => 'Title',
                            'id' => 'inputTitle',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'slug'); ?>">
                <label class="control-label" for="inputSlug">Name</label>
                <div class="controls">
                    <?php
                        $slug = Arr::get($values, 'slug');

                        echo Form::input('slug', $slug, array(
                            'class' => 'span3',
                            'placeholder' => 'Name',
                            'id' => 'inputSlug',
                        ));
                    ?>

                </div>
            </div>
<?php if (isset($values['id']) AND $values['id']) : ?>
            <div class="control-group">
                <label class="control-label">Items</label>
                <div class="controls">

                    <table id="sortable" class="table table-condensed table-hover <?php
                        if ( ! isset($items) OR empty($items)) :
                            echo 'hide';
                        endif;
                    ?>" style="margin-bottom: 7px;">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">URL</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($items as $index => $item) : ?>
                        <tr class="item">
                            <td>
                                <?php
                                    $edit = Route::get('panel_menus')->uri(array(
                                        'action' => 'edit_item',
                                        'id'     => $item->id
                                    ));

                                    echo HTML::anchor($edit, $item->title, array(
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalEditor'
                                    ));
                                ?>

                                <?php
                                    echo Form::hidden('sort[]', $item->id);
                                ?>

                            </td>
                            <td><?php
                                echo HTML::anchor($item->url, $item->url);
                            ?></td>
                            <td>
                                <span class="btn-group">
                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalEditor"
                                     data-remote="<?php echo URL::site($edit); ?>"><i class="icon-pencil"></i></button>

                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalDelete"
                                     data-remote="<?php
                                        echo URL::site(Route::get('panel_menus')->uri(array(
                                            'action' => 'delete_item',
                                            'id'     => $item->id
                                        )) );
                                    ?>"><i class="icon-trash "></i></button>
                                </span>
                            </td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                    </table>

                    <?php
                        $link = Route::get('panel_menus')->uri(array(
                            'action' => 'create_item',
                            'id'     => $values['id']
                        ));

                        echo HTML::anchor($link, 'Create New Item', array(
                            'class'       => 'btn btn-small btn-success',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalEditor'
                        ));
                    ?>

                </div>
            </div>
<?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <?php echo HTML::anchor( Route::get('panel_menus')->uri(), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>

<!-- Start of Menu Item Modal -->
<div class="modal hide menu-editor" id="modalEditor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Create New Menu Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="$('#modalEditorForm').submit()">Save changes</button>
    </div>
</div>
<!-- End of Menu Item Modal -->

<!-- Start of Menu Item Delete -->
<div class="modal hide menu-delete" id="modalDelete">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Delete Menu Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="$('#modalDeleteForm').submit()">Delete</button>
    </div>
</div>
<!-- End of Menu Item Delete -->

