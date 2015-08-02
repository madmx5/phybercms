<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Asset Group';
                else :
                    echo 'Create New Asset Group';
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
            <div class="control-group <?php echo Arr::get($fields, 'path'); ?>">
                <label class="control-label" for="inputPath">Path</label>
                <div class="controls">
                    <?php
                        $path = Arr::get($values, 'path');

                        echo Form::input('path', $path, array(
                            'class' => 'span3',
                            'placeholder' => 'Path',
                            'id' => 'inputPath',
                        ));
                    ?>

                </div>
            </div>
<?php if (isset($values['id']) AND $values['id']) : ?>
            <div class="control-group">
                <label class="control-label">Items</label>
                <div class="controls">
                    <?php
                        $link = Route::get('panel_assets')->uri(array(
                            'controller' => 'asset',
                            'action'     => 'create_item',
                            'id'         => $values['id']
                        ));

                        echo HTML::anchor($link, 'Create Asset Item', array(
                            'class'       => 'btn btn-small btn-success',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalEditor'
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">

                    <table id="sortable" class="table table-condensed table-hover <?php
                        if ( ! isset($items) OR empty($items)) :
                            echo 'hide';
                        endif;
                    ?>" style="margin-bottom: 7px;">
                    <thead>
                        <tr>
                            <th scope="col">Filename</th>
                            <th scope="col">Content Type</th>
                            <th scope="col">Edit / View</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>

<?php foreach ($items as $index => $item) : ?>
                        <tr>
                            <td><?php
                                    $edit = Route::get('panel_assets')->uri(array(
                                        'action' => 'edit_item',
                                        'id'     => $item->id
                                    ));

                                    echo HTML::anchor($edit, $item->filename, array(
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalEditor'
                                    ));
                            ?></td>
                            <td><?php
                                    $mime = $item->mimetype ? : 'application/octet-stream';
                                    
                                    echo HTML::entities($mime);
                            ?></td>
                            <td>
                                <span class="btn-group">
                                    <?php
                                        $content_edit = Route::get('panel_assets')->uri(array(
                                            'action' => 'content_edit',
                                            'id'     => $item->id
                                        ));

                                        $content_view = Route::get('panel_assets')->uri(array(
                                            'action' => 'content_view',
                                            'id'     => $item->id
                                        ));

                                        echo HTML::anchor($content_edit, '<i class="icon-edit"></i>', array(
                                            'class'     => 'btn btn-small'
                                        ));

                                        echo HTML::anchor($content_view, '<i class="icon-search"></i>', array(
                                            'class'     => 'btn btn-small',
                                            'target'    => '_blank'
                                        ));
                                    ?>

                                </span>
                            </td>
                            <td>
                                <span class="btn-group">

                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalEditor"
                                     data-remote="<?php echo URL::site($edit); ?>"><i class="icon-pencil"></i></button>

                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalDelete"
                                     data-remote="<?php
                                        echo URL::site(Route::get('panel_assets')->uri(array(
                                            'action' => 'delete_item',
                                            'id'     => $item->id
                                        )) );
                                    ?>"><i class="icon-trash"></i></button>

                                </span>
                            </td>
                        </tr>
<?php endforeach; ?>

                    </tbody>
                    </table>
                </div>
            </div>
<?php endif; ?>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <?php echo HTML::anchor( Route::get('panel_assets')->uri(), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>

<!-- Start of Asset Item Modal -->
<div class="modal hide asset-editor" id="modalEditor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Edit Existing Asset Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="$('#modalEditorForm').submit()">Save changes</button>
    </div>
</div>
<!-- End of Asset Item Modal -->

<!-- Start of Asset Item Delete -->
<div class="modal hide asset-delete" id="modalDelete">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Delete Asset Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="$('#modalDeleteForm').submit()">Delete</button>
    </div>
</div>
<!-- End of Asset Item Delete -->

