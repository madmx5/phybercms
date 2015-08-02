<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        if (isset($values['id'])) :
            $link = Route::get('panel_guests')->uri(array(
                'action' => 'edit_item',
                'id'     => $values['id']
            ));
        else :
            $link = Route::get('panel_guests')->uri(array(
                'action' => 'create_item',
                'id'     => $party_id
            ));
        endif;

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalEditorForm'));
    ?>


        <div class="form-horizontal">
            <div class="control-group <?php echo Arr::get($fields, 'first_name'); ?>">
                <label class="control-label" for="inputFirstName">First name</label>
                <div class="controls">
                    <?php
                        $first_name = Arr::get($values, 'first_name');

                        echo Form::input('first_name', $first_name, array(
                                'class' => 'input-large',
                                'id' => 'inputFirstName',
                            ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'last_name'); ?>">
                <label class="control-label" for="inputLastName">Last name</label>
                <div class="controls">
                    <?php
                        $last_name = Arr::get($values, 'last_name');

                        echo Form::input('last_name', $last_name, array(
                                'class' => 'input-large',
                                'id' => 'inputLastName',
                            ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'adult'); ?>">
                <label class="control-label" for="inputAdult">Adult</label>
                <div class="controls">
                    <?php
                        $adult = Arr::get($values, 'adult');

                        $options = array(
                            '1' => 'Yes',
                            '0' => 'No',
                        );

                        echo Form::select('adult', $options, $adult, array(
                            'class' => 'input-large',
                            'id' => 'inputAdult',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'plus_one'); ?>">
                <label class="control-label" for="inputPlusOne">Plus One</label>
                <div class="controls">
                    <?php
                        $plus_one = Arr::get($values, 'plus_one');

                        $options = array(
                            '0' => 'No',
                            '1' => 'Yes',
                        );

                        echo Form::select('plus_one', $options, $plus_one, array(
                            'class' => 'input-large',
                            'id' => 'inputPlusOne',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'gender'); ?>">
                <label class="control-label" for="inputGender">Gender</label>
                <div class="controls">
                    <?php
                        $gender = Arr::get($values, 'gender');

                        $options = array(
                            NULL => '',
                            'M' => 'Male',
                            'F' => 'Female',
                        );

                        echo Form::select('gender', $options, $gender, array(
                            'class' => 'input-large',
                            'id' => 'inputGender',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'attending'); ?>">
                <label class="control-label" for="inputAttending">Attending</label>
                <div class="controls">
                    <?php
                        $attending = Arr::get($values, 'attending');

                        $options = array(
                            NULL => '',
                            '1' => 'Attending',
                            '0' => 'Not attending',
                        );

                        echo Form::select('attending', $options, $attending, array(
                            'class' => 'input-large',
                            'id' => 'inputAttending',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'meal_id'); ?>">
                <label class="control-label" for="inputMeal">Meal</label>
                <div class="controls">
                    <?php
                        $meal_id = Arr::get($values, 'meal_id');

                        echo Form::select('meal_id', $meals, $meal_id, array(
                            'class' => 'input-large',
                            'id' => 'inputMeal',
                        ));
                    ?>

                </div>
            </div>
        </div>

    <?php echo Form::close(); ?>

