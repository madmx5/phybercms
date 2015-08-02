<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        if (isset($values['id'])) :
            $link = Route::get('panel_media')->uri(array(
                'action' => 'edit_item',
                'id'         => $values['id']
            ));
        else :
            $link = Route::get('panel_media')->uri(array(
                'action' => 'create_item',
                'id'     => $media_id
            ));
        endif;

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalEditorForm', 'enctype' => 'multipart/form-data'));
    ?>


        <div class="form-horizontal">
            <div class="control-group <?php echo Arr::get($fields, 'media'); ?>">
                <label class="control-label" for="inputMediaShim">Upload media</label>
                <div class="controls">
                    <?php
                        echo Form::input('media', NULL, array(
                                'type' => 'file',
                                'id' => 'inputMediaFile',
                                'onchange' => "$('#inputMediaShim').val( $(this).val().replace('C:\\\\fakepath\\\\', '') )",
                            ));
                    ?>

                    <div class="input-append">
                        <?php
                            echo Form::input('mediaShim', NULL, array(
                                    'class' => 'input-xlarge',
                                    'id' => 'inputMediaShim',
                                    'onclick' => "$('#inputMediaFile').click()",
                                ));
                        ?>

                        <button class="btn" type="button" onclick="$('#inputMediaShim').focus().click()"><i class="icon-folder-open"></i></button>
                    </div>
                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'url'); ?>">
                <label class="control-label" for="inputMediaUrl"><b>... or</b> link to media</label>
                <div class="controls">
                    <div class="input-append">
                        <?php
                            $url = Arr::get($values, 'url', '');

                            echo Form::input('url', $url, array(
                                    'class' => 'input-xlarge',
                                    'id' => 'inputMediaUrl',
                                ));
                        ?>

                        <span class="add-on"><i class="icon-globe"></i></span>
                    </div>
                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'title'); ?>">
                <label class="control-label" for="inputMediaTitle">Title</label>
                <div class="controls">
                    <?php
                        $title = Arr::get($values, 'title', '');

                        echo Form::input('title', $title, array(
                                'class' => 'input-xlarge',
                                'id' => 'inputMediaTitle',
                            ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'caption'); ?>">
                <label class="control-label" for="inputCaption">Caption</label>
                <div class="controls">
                    <?php
                        $caption = Arr::get($values, 'caption', '');

                        echo Form::textarea('caption', $caption, array(
                                'class' => 'input-xlarge',
                                'rows' => 4,
                                'id' => 'inputCaption',
                            ));
                    ?>

                </div>
            </div>
        </div>

    <?php echo Form::close(); ?>

