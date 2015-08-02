<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Instagram Subscription';
                else :
                    echo 'Create New Instagram Subscription';
                endif;
            ?></h3>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="row">
                <div class="span5">
                    <div class="control-group <?php echo Arr::get($fields, 'slug'); ?>">
                        <label class="control-label" for="inputSlug">Name</label>
                        <div class="controls">
                            <?php
                                $slug = Arr::get($values, 'slug');

                                echo Form::input('slug', $slug, array(
                                    'class' => 'input-large',
                                    'placeholder' => 'Name',
                                    'id' => 'inputSlug'
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'title'); ?>">
                        <label class="control-label" for="inputTitle">Title</label>
                        <div class="controls">
                            <?php
                                $title = Arr::get($values, 'title');

                                echo Form::input('title', $title, array(
                                    'class' => 'input-large',
                                    'placeholder' => 'Title',
                                    'id' => 'inputTitle'
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'object'); ?>">
                        <label class="control-label" for="inputObject">Object</label>
                        <div class="controls">
                            <?php
                                $object = Arr::get($values, 'object');

                                echo Form::input('object', $object, array(
                                    'class' => 'input-large',
                                    'placeholder' => 'Object',
                                    'id' => 'inputObject',
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'obj_id'); ?>">
                        <label class="control-label" for="inputObjId">Object Id</label>
                        <div class="controls">
                            <?php
                                $obj_id = Arr::get($values, 'obj_id');

                                echo Form::input('obj_id', $obj_id, array(
                                    'class' => 'input-large',
                                    'placeholder' => 'Id',
                                    'id' => 'inputObjId',
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'aspect'); ?>">
                        <label class="control-label" for="inputAspect">Aspect</label>
                        <div class="controls">
                            <?php
                                $aspect = Arr::get($values, 'aspect');

                                echo Form::input('aspect', $aspect, array(
                                    'class' => 'input-large',
                                    'placeholder' => 'Aspect',
                                    'id' => 'inputAspect',
                                ));
                            ?>

                        </div>
                    </div>
                </div>

                <div class="span4">
                    <div class="control-group <?php echo Arr::get($fields, 'client_id'); ?>">
                        <label class="control-label" for="inputClientId">Client Id</label>
                        <div class="controls">
                            <?php
                                $client_id = Arr::get($values, 'client_id');

                                echo Form::input('client_id', $client_id, array(
                                    'class' => 'input-xlarge',
                                    'placeholder' => 'Client id',
                                    'id' => 'inputClientId'
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'client_secret'); ?>">
                        <label class="control-label" for="inputClientSecret">Client secret</label>
                        <div class="controls">
                            <?php
                                $client_secret = Arr::get($values, 'client_secret');

                                echo Form::input('client_secret', $client_secret, array(
                                    'class' => 'input-xlarge',
                                    'placeholder' => 'Client secret',
                                    'id' => 'inputClientSecret'
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'token'); ?>">
                        <label class="control-label" for="inputToken">Verify token</label>
                        <div class="controls">
                            <?php
                                $token = Arr::get($values, 'token');

                                echo Form::input('token', $token, array(
                                    'class' => 'input-xlarge',
                                    'placeholder' => 'Verify token',
                                    'id' => 'inputToken'
                                ));
                            ?>

                        </div>
                    </div>
                    <div class="control-group <?php echo Arr::get($fields, 'params'); ?>">
                        <label class="control-label" for="inputParams">API Parameters</label>
                        <div class="controls">
                            <?php
                                $params = Arr::get($values, 'params');

                                echo Form::textarea('params', $params, array(
                                    'class' => 'span5',
                                    'placeholder' => 'Parameters',
                                    'rows' => '3',
                                    'id' => 'inputParams',
                                ));
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <?php echo HTML::anchor( Route::get('panel_instagram')->uri(), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>

<?php
    if (isset($values['id'], $recent)) :
        echo $recent;
    endif;
?>


