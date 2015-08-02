<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <div class="container">

        <div class="page-header">
            <h3><?php
                if (isset($values['id'])) :
                    echo 'Edit Existing Page';
                else :
                    echo 'Create New Page';
                endif;
            ?></h3>
        </div>

        <form class="form-horizontal" method="POST">
            <div class="control-group <?php echo Arr::get($fields, 'author_id'); ?>">
                <label class="control-label" for="inputAuthorId">Author</label>
                <div class="controls">
                    <?php
                        $author = Arr::get($values, 'author_id');

                        echo Form::select('author_id', $author_list, $author, array('id' => 'inputAuthorId'));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'status'); ?>">
                <label class="control-label" for="inputStatus">Status</label>
                <div class="controls">
                    <?php
                        $status = Arr::get($values, 'status');

                        echo Form::select('status', $status_list, $status, array('id' => 'inputStatus'));
                    ?>

                    <?php
                        $class = 'input-small date-picker';

                        $publish = Arr::get($values, 'publish_at');

                        if ($status !== 'published')
                        {
                            $class .= ' hide';
                        }

                        if ($publish !== NULL)
                        {
                            $publish = strstr($publish, ' ', TRUE);
                        }

                        echo Form::input('publish_at', $publish, array(
                            'class' => $class,
                            'placeholder' => 'Publish on...',
                        ));
                    ?>

                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'title'); ?>">
                <label class="control-label" for="inputTitle">Page Title</label>
                <div class="controls">
                    <?php
                        $title = Arr::get($values, 'title');

                        echo Form::input('title', $title, array(
                            'class' => 'input-xlarge',
                            'id' => 'inputTitle',
                            'data-provide' => 'slugger',
                            'data-target' => 'form input[name=slug]',
                            'data-source' => Route::url('panel_pages', array('action' => 'slug')),
                        ));
                    ?>

                    <button type="button" class="btn" id="btnSlugSync"><i class="icon-arrow-down"></i></button>
                </div>
            </div>
            <div class="control-group <?php echo Arr::get($fields, 'slug'); ?>">
                <label class="control-label" for="inputSlug">Permalink</label>
                <div class="controls">
                    <?php
                        $slug = Arr::get($values, 'slug');

                        echo Form::input('slug', $slug, array(
                            'type' => 'hidden',
                            'id' => 'inputSlugHidden',
                        ));

                        echo Form::input('slug', $slug, array(
                            'class' => 'input-xlarge',
                            'disabled' => 'disabled',
                            'id' => 'inputSlug',
                        ));
                    ?>

                    <button type="button" class="btn" id="btnSlugEdit"><i class="icon-edit"></i></button>
                </div>
            </div>
            <div class="tabbable span9 offset1">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#content_edit" data-toggle="tab">Edit</a>
                    </li>
                    <li><a href="#content_view" data-toggle="tab">Preview</a></li>
                </ul>
                <div class="tab-content control-group <?php echo Arr::get($fields, 'content'); ?>">
                    <div id="content_edit" class="tab-pane active">
                        <div id="pageEditor" class="ace-editor" data-name="content"><?php
                            $content = Arr::get($values, 'content');

                            echo HTML::entities($content);
                        ?></div>
                    </div>
                    <div id="content_view" class="tab-pane">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="offset1">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <?php echo HTML::anchor( Route::get('panel_pages')->uri(), 'Cancel', array('class' => 'btn')); ?>

            </div>
        </form>

    </div>

