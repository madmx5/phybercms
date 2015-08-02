<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Party';
                else :
                    echo 'Create New Party';
                endif;
            ?></h3>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="control-group <?php echo Arr::get($fields, 'name'); ?>">
                <label class="control-label" for="inputName">Name</label>
                <div class="controls">
                    <?php
                        $name = Arr::get($values, 'name');

                        echo Form::input('name', $name, array(
                            'class' => 'span3',
                            'placeholder' => 'Name',
                            'id' => 'inputName',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'slug'); ?>">
                <label class="control-label" for="inputSlug">Code</label>
                <div class="controls">
                    <?php
                        $slug = Arr::get($values, 'slug');

                        echo Form::input('slug', $slug, array(
                            'class' => 'span3',
                            'placeholder' => 'Code',
                            'id' => 'inputSlug',
                        ));
                    ?>

                </div>
            </div>
<?php if (isset($values['id']) AND $values['id']) : ?>
            <div class="control-group">
                <label class="control-label">Guests</label>
                <div class="controls">
                    <?php
                        $link = Route::get('panel_guests')->uri(array(
                            'action' => 'create_item',
                            'id'     => $values['id']
                        ));

                        echo HTML::anchor($link, 'Create Party Guest', array(
                            'class'       => 'btn btn-small btn-success',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalEditor',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">

                    <table class="table table-condensed table-hover <?php
                        if ( ! isset($guests) OR empty($guests)) :
                            echo 'hide';
                        endif;
                    ?>" style="margin-bottom: 7px;">
                    <thead>
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">Name</th>
                            <th scope="col">Attending</th>
                            <th scope="col">Meal</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>

<?php foreach ($guests as $index => $guest) : ?>
                        <tr>
                            <td><i class="icon-extra-<?php
                                if (strcasecmp($guest->gender, "F") === 0) :
                                    echo 'woman';
                                else :
                                    echo 'old-man';
                                endif;
                            ?>"></i> <i class="icon-extra-<?php
                                if ($guest->adult) :
                                    echo 'glass';
                                else :
                                    echo 'stroller';
                                endif;
                            ?>"></i></td>
                            <td><?php
                                $edit = Route::get('panel_guests')->uri(array(
                                    'action' => 'edit_item',
                                    'id'     => $guest->id
                                ));

                                $name = $guest->name;

                                if ($guest->plus_one AND empty($name))
                                {
                                    $name = '-';
                                }

                                echo HTML::anchor($edit, $name, array(
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalEditor'
                                ));
                            ?></td>
                            <td><?php
                                if ($guest->attending === NULL) :
                                    echo '-';
                                else :
                                    echo '<i class="icon-' . ($guest->attending ? 'ok' : 'remove') . '"></i>';
                                endif;
                            ?></td>
                            <td><?php
                                if ( ! $guest->meal->loaded()) :
                                    echo '-';
                                else :
                                    echo $guest->meal->name;
                                endif;
                            ?></td>
                            <td>
                                <span class="btn-group">
                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalEditor"
                                     data-remote="<?php echo URL::site($edit); ?>"><i class="icon-pencil"></i></button>

                                    <button type="button" class="btn btn-small" data-toggle="modal" data-target="#modalDelete"
                                     data-remote="<?php
                                        echo URL::site(Route::get('panel_guests')->uri(array(
                                            'action' => 'delete_item',
                                            'id'     => $guest->id
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
                <?php
                    echo HTML::anchor( Route::get('panel_guests')->uri(), 'Cancel', array(
                        'class' => 'btn',
                    ));
                ?>

            </div>
        </form>

    </div>

<!-- Start of Party Guest Modal -->
<div class="modal hide guest-editor" id="modalEditor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Edit Existing Party Guest</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="$('#modalEditorForm').submit()">Save changes</button>
    </div>
</div>
<!-- End of Party Guest Modal -->

<!-- Start of Party Guest Delete -->
<div class="modal hide guest-delete" id="modalDelete">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Delete Party Guest</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="$('#modalDeleteForm').submit()">Delete</button>
    </div>
</div>
<!-- End of Party Guest Delete -->

