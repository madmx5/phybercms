<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        if (isset($values['id'])) :
            $link = Route::get('panel_assets')->uri(array(
                'action' => 'edit_item',
                'id'     => $values['id']
            ));
        else :
            $link = Route::get('panel_assets')->uri(array(
                'action' => 'create_item',
                'id'     => $asset_id
            ));
        endif;

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalEditorForm', 'enctype' => 'multipart/form-data'));
    ?>


        <div class="form-horizontal">
            <div class="control-group <?php echo Arr::get($fields, 'filename'); ?>">
                <label class="control-label" for="inputAssetFilename">Asset filename</label>
                <div class="controls">
                    <div class="input-append">
                        <?php
                            $filename = Arr::get($values, 'filename');

                            echo Form::input('filename', $filename, array(
                                    'class' => 'input-xlarge',
                                    'id' => 'inputAssetFilename',
                                    'placeholder' => 'ex: stylesheet.css, application.js',
                                ));
                        ?>

                        <button class="btn btn-file" type="button"><i class="icon-file"></i></button>
                    </div>
                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'asset'); ?>">
                <label class="control-label" for="inputAssetShim">Upload asset</label>
                <div class="controls">
                    <?php
                        echo Form::input('asset', NULL, array(
                                'type' => 'file',
                                'id' => 'inputAssetFile',
                                'onchange' => "$('#inputAssetShim').val( $(this).val().replace('C:\\\\fakepath\\\\', '') )",
                            ));
                    ?>

                    <div class="input-append">
                        <?php
                            echo Form::input('assetShim', NULL, array(
                                    'class' => 'input-xlarge',
                                    'id' => 'inputAssetShim',
                                    'onclick' => "$('#inputAssetFile').click()",
                                ));
                        ?>

                        <button class="btn" type="button" onclick="$('#inputAssetShim').focus().click()"><i class="icon-folder-open"></i></button>
                    </div>
                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'editable'); ?>">
                <label class="control-label" for="inputAssetEditable">Editable as text</label>
                <div class="controls">
                    <?php
                        $editable = Arr::get($values, 'editable', NULL);

                        echo Form::select('editable', array(NULL => '', 0 => 'No', 1 => 'Yes'), $editable, array(
                                'class' => 'input-small',
                                'id' => 'inputAssetEditable',
                            ));
                    ?>

                </div>
            </div>
        </div>

    <?php echo Form::close(); ?>

