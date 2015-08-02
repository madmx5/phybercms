<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Meal';
                else :
                    echo 'Create New Meal';
                endif;
            ?></h3>
        </div>

        <form class="form-horizontal" method="POST">

        <div class="row">
            <div class="span5">

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
                <div class="control-group <?php echo Arr::get($fields, 'description'); ?>">
                    <label class="control-label" for="inputDescription">Description</label>
                    <div class="controls">
                        <?php
                            $description = Arr::get($values, 'description');

                            echo Form::textarea('description', $description, array(
                                'class' => 'span3',
                                'placeholder' => 'Description',
                                'id' => 'inputDescription',
                            ));
                        ?>

                    </div>
                </div>
                <div class="control-group <?php echo Arr::get($fields, 'adult'); ?>">
                    <label class="control-label" for="inputAdult">Adult Option</label>
                    <div class="controls">
                        <?php
                            $adult = Arr::get($values, 'adult');

                            $options = array(
                                    NULL  => '',
                                    TRUE  => 'Yes',
                                    FALSE => 'No',
                                );

                            echo Form::select('adult', $options, $adult, array(
                                'class' => 'span3',
                                'id' => 'inputAdult',
                            ));
                        ?>

                    </div>
                </div>

            </div><!-- <div class="span5"> -->
            <div class="span7">
<?php if (isset($values['id']) AND $values['id'] AND isset($guests) AND ! empty($guests)) : ?>

                <div class="span6 pull-right well">
                    <h4 style="margin-top: 0;">Guests</h4>

                    <table class="table table-condensed table-hover <?php
                        if ( ! isset($guests) OR empty($guests)) :
                            echo 'hide';
                        endif;
                    ?>" style="margin-bottom: 7px;">
                    <thead>
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">Name</th>
                            <th scope="col">Party</th>
                            <th scope="col">Attending</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($guests as $guest) : ?>
                        <tr>
                            <td><i class="icon-user"></i></td>
                            <td><i class="icon-asterisk"></i></td>
                            <td><?php echo $guest->name; ?></td>
                            <td><?php echo $guest->party->name; ?></td>
                            <td><?php
                                if ($guest->attending === NULL) :
                                    echo '-';
                                else :
                                    echo '<i class="icon-' . ($guest->attending ? 'ok' : 'remove') . '"></i>';
                                endif;
                            ?></td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                    </table>
                </div><!-- <div class="span6 pull-right well"> -->

<?php endif; ?>
            </div><!-- <div class="span7"> -->
        </div><!-- <div class="row"> -->

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <?php
                echo HTML::anchor( Route::get('panel_meals')->uri(), 'Cancel', array(
                    'class' => 'btn',
                ));
            ?>

        </div>

        </form>

    </div>

<!-- Start of Meal Modal -->
<div class="modal hide meal-editor" id="modalEditor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Edit Existing Meal</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="$('#modalEditorForm').submit()">Save changes</button>
    </div>
</div>
<!-- End of Meal Modal -->

<!-- Start of Meal Delete -->
<div class="modal hide meal-delete" id="modalDelete">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Delete Meal</h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="$('#modalDeleteForm').submit()">Delete</button>
    </div>
</div>
<!-- End of Meal Delete -->

