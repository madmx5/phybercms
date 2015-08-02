<?php defined('SYSPATH') OR die('No direct script access.'); ?>

    <?php
        if (isset($values['id'])) :
            $link = Route::get('panel_menus')->uri(array(
                'action' => 'edit_item',
                'id'     => $values['id']
            ));
        else :
            $link = Route::get('panel_menus')->uri(array(
                'action' => 'create_item',
                'id'     => $menu_id
            ));
        endif;

        echo Form::open($link, array('method' => HTTP_Request::POST, 'id' => 'modalEditorForm'));
    ?>


        <p><label><?php
            if (Arr::get($values, 'type') === 'page') :
                $checked = TRUE;
            elseif (isset($values['id']) AND array_key_exists('page_id', $values) AND $values['page_id'] !== NULL AND ! isset($values['type'])) :
                $checked = TRUE;
            else :
                $checked = FALSE;
            endif;

            echo Form::radio('type', 'page', $checked);

        ?> <b>Link to an existing page...</b></label></p>

        <fieldset>
            <div class="form-horizontal">
                <div class="control-group <?php echo Arr::get($fields, 'page_id'); ?>">
                    <label class="control-label" for="inputPageSearch">Search for page</label>
                    <div class="controls">
                        <div class="input-append">
                            <?php
                                if ($checked) :
                                    $page = Arr::get($values, 'page', array());
                                    $slug = Arr::get($page,   'slug', '');
                                else :
                                    $slug = '';
                                endif;

                                echo Form::input('page', $slug, array( 
                                        'class' => 'input-large',
                                        'id' => 'inputPageSearch',
                                        'autocomplete' => 'off',
                                    ));
                            ?>

                            <span class="add-on"><i class="icon-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="control-group <?php
                    if ($checked) :
                        echo Arr::get($fields, 'title');
                    endif;
                ?>">
                    <label class="control-label" for="inputPageTitle">Title of item</label>
                    <div class="controls">
                        <?php
                            if ($checked) :
                                $title = Arr::get($values, 'title', Arr::get($values, 'page_title'));
                            else :
                                $title = '';
                            endif;

                            echo Form::input('page_title', $title, array(
                                    'class' => 'input-large',
                                    'id' => 'inputPageTitle',
                                ));
                        ?>

                    </div>
                </div>
            </div>
        </fieldset>

        <p><label><?php 
            if (Arr::get($values, 'type') === 'custom') :
                $checked = TRUE;
            elseif (isset($values['id']) AND array_key_exists('page_id', $values) AND $values['page_id'] === NULL AND ! isset($values['type'])) :
                $checked = TRUE;
            else :
                $checked = FALSE;
            endif;

            echo Form::radio('type', 'custom', $checked);
        ?> <b>Or create a custom link...</b></label></p>

        <fieldset>
            <div class="form-horizontal">
                <div class="control-group <?php echo Arr::get($fields, 'url'); ?>">
                    <label class="control-label" for="inputCustomUrl">URL</label>
                    <div class="controls">
                        <div class="input-append">
                            <?php
                                if ($checked) :
                                    $url = Arr::get($values, 'url', '');
                                else :
                                    $url = '';
                                endif;

                                echo Form::input('url', $url, array(
                                        'class' => 'input-large',
                                        'id' => 'inputCustomUrl',
                                    ));
                            ?>

                            <span class="add-on"><i class="icon-globe"></i></span>
                        </div>
                    </div>
                </div>
                <div class="control-group <?php
                    if ($checked) :
                        echo Arr::get($fields, 'title');
                    endif;
                ?>">
                    <label class="control-label" for="inputCustomTitle">Title of item</label>
                    <div class="controls">
                        <?php
                            if ($checked) :
                                $title = Arr::get($values, 'title', Arr::get($values, 'custom_title'));
                            else :
                                $title = '';
                            endif;

                            echo Form::input('custom_title', $title, array(
                                    'class' => 'input-large',
                                    'id' => 'inputCustomTitle',
                                ));
                        ?>

                    </div>
                </div>
            </div>
        </fieldset>

    <?php echo Form::close(); ?>

<?php
    // Get the route to the typeahead suggest results
    $typeahead_url = Route::url('panel_pages', array('action' => 'suggest'));
?>

<script type="text/javascript">
  $('#inputPageSearch').typeahead({
    source: function (query, process) {
      return $.get('<?php echo $typeahead_url; ?>', { query: query }, function (data) {
        return process(data.options)
      });
    }
  });
</script>

