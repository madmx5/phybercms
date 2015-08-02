<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3>Edit Asset Content</h3>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="control-group">
                <label class="control-label">Group</label>
                <div class="controls" style="padding-top: 5px;">
                    <i class="icon-folder-close"></i> <?php

                        $edit = Route::get('panel_assets')->uri(array(
                            'action' => 'edit',
                            'id'     => $asset['id']
                        ));

                        echo HTML::anchor($edit, $asset['title']);
                    ?>

                </div>
            </div>

            <div class="control-group <?php echo Arr::get($fields, 'filename'); ?>">
                <label class="control-label" for="inputFilename">Filename</label>
                <div class="controls">
                    <?php
                        echo Form::input('filename', $values['filename'], array(
                            'class' => 'input-large',
                            'placeholder' => 'stylesheet.css',
                            'id' => 'inputFilename'
                        ));
                    ?>

                </div>
            </div>

            <div class="control-group <?php echo Arr::get($fields, 'mimetype'); ?>">
                <label class="control-label" for="inputMimetype">Content type</label>
                <div class="controls">
                    <?php
                        echo Form::input('mimetype', $values['mimetype'], array(
                            'class' => 'input-large',
                            'placeholder' => 'text/plain',
                            'id' => 'inputMimetype'
                        ));
                    ?>

                    <?php
                        $options = array(
                            '0' => 'Do not change type',
                            '1' => 'Detect automatically',
                        );

                        $selected = ($values['mimetype'] ? 0 : 1);

                        echo Form::select('detect_mime', $options, $selected);
                    ?>

                </div>
            </div>

            <div class="control-group <?php echo Arr::get($fields, 'contents'); ?>">
                <label class="control-label" for="inputContent">Contents</label>
                <div class="controls">
                    <div id="assetEditor" class="ace-editor" data-name="contents" data-mode="<?php
                        switch ($values['mimetype']) :

                            case 'text/stylesheet':
                                echo 'ace/mode/css';
                                break;

                            case 'text/javascript':
                                echo 'ace/mode/javascript';
                                break;

                            default:
                                echo 'ace/mode/text';

                        endswitch;

                    ?>"><?php

                        echo HTML::entities($contents);

                    ?></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <?php echo HTML::anchor( Route::get('panel_assets')->uri(array('action' => 'edit', 'id' => $asset['id'])), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>


