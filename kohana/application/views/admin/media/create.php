<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Media Group';
                else :
                    echo 'Create New Media Group';
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
                    <?php
                        $link = Route::get('panel_media')->uri(array(
                            'action' => 'create_item',
                            'id'     => $values['id']
                        ));

                        echo HTML::anchor($link, 'Create Media Item', array(
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
                            <th scope="col">Preview</th>
                            <th scope="col">Title / Caption</th>
                            <th scope="col">URL</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($items as $index => $item) : ?>
                        <tr class="item">
                            <td>
                                <?php
                                    $edit = Route::get('panel_media')->uri(array(
                                        'controller' => 'media',
                                        'action'     => 'edit_item',
                                        'id'         => $item->id
                                    ));

                                    echo HTML::anchor($edit, '<img class="media-object" src="' . Media::url($item->url, '75x75', 'crop') . '">', array(
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalEditor'
                                    ));
                                ?>

                                <?php
                                    echo Form::hidden('sort[]', $item->id);
                                ?>

                            </td>
                            <td class="media-body">
                                <div class="media-heading"><?php echo $item->title; ?></div>
                                <i><?php echo $item->caption; ?></i>
                            </td>
                            <td>
                                <a href="<?php echo Media::url($item->url); ?>" target="_blank"><?php echo Media::short_url($item->url); ?></a>
                            </td>
                            <td>
                                <span class="btn-group">
                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalEditor"
                                     data-remote="<?php echo URL::site($edit); ?>"><i class="icon-pencil"></i></button>

                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalDelete"
                                     data-remote="<?php
                                        echo URL::site(Route::get('panel_media')->uri(array(
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
                <?php echo HTML::anchor( Route::get('panel_media')->uri(), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>

<!-- Start of Media Item Modal -->
<div class="modal hide media-editor" id="modalEditor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Edit Existing Media Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="$('#modalEditorForm').submit()">Save changes</button>
    </div>
</div>
<!-- End of Media Item Modal -->

<!-- Start of Media Item Delete -->
<div class="modal hide media-delete" id="modalDelete">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Delete Media Item</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="$('#modalDeleteForm').submit()">Delete</button>
    </div>
</div>
<!-- End of Media Item Delete -->

