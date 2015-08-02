$(function() {

  $('.date-picker').each(function () {
    $(this).datepicker({ format: 'yyyy-mm-dd' })
  });

  $('.admin-popover').popover();
  
  $('.admin-tooltop').tooltip();

  $('.ace-editor').each(function () {
      var $this = $(this)

      var editor = ace.edit(this)

      editor.setTheme('ace/theme/tomorrow')
      editor.getSession().setUseWorker(false)

      if ($this.data('mode')) {
          editor.getSession().setMode( $this.data('mode') )
      } else {
          editor.getSession().setMode('ace/mode/php')
      }

      editor.getSession().setTabSize(4)
      editor.getSession().setUseSoftTabs(true)
      editor.getSession().setUseWrapMode(true)

      if ($this.data('name')) {
        var textarea = $('<textarea class="hide" name="' + $this.data('name') + '"></textarea>').insertAfter($this)

        editor.getSession().on('change', function () {
            textarea.val(editor.getSession().getValue())
        });

        textarea.val(editor.getSession().getValue())
      }
  });

  $('#inputStatus').on('change', function (e) {
    var $this = $(this)

    if ($this.val() == 'published') {
      $('input[name=publish_at]').removeClass('hide')
    }
    else {
      $('input[name=publish_at]').addClass('hide')
    }
  });

  $('#btnSlugEdit').on('click', function (e) {
    var $hide = $('#inputSlugHidden')
    var $that = $('#inputSlug')

    if ($that.hasClass('slugged')) {
      $that.attr('disabled', 'disabled').removeClass('slugged')
      $hide.attr('name', 'slug')
    }
    else {
      $that.removeAttr('disabled').addClass('slugged')
      $hide.attr('name', 'slug_hidden')
    }
  });

  $('#btnSlugSync').on('click', function (e) {
    var $that = $('#inputTitle')

    $that.slugger('refresh')
  });

  $('#modalDelete, #modalEditor').on('show', function () {
    var $this = $(this)
      , options = $this.data('modal').options
      , textVal = $this.find('h3').text()

    if (options.remote && options.remote.match(/create_item/)) {
      $this.find('h3').text(textVal.replace('Edit Existing', 'Create New'))
    }
    else {
      $this.find('h3').text(textVal.replace('Create New', 'Edit Existing'))
    }
  });

  $('#modalEditor, #modalDelete, #modalViewer').on('hidden', function () {
    $(this).removeData('modal')
  });

  $('#sortable tbody').each(function() {
    $(this).sortable({ helper: function (e, ui) {
        ui.children().each(function() {
          $(this).width($(this).width())
        });
        return ui
      }
    }).disableSelection()
  });

});

