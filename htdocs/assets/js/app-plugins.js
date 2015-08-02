!function ($) {

  "use strict"; // jshint ;_;


  /* SLUGGER PUBLIC CLASS DEFINITION
   * =============================== */

  var Slugger = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.slugger.defaults, options)
    this.$target = $(this.options.target)
    this.source = this.options.source 
    this.listen()
  }

  Slugger.prototype = {

    constructor: Slugger

  , listen: function () {
      this.$element
        .on('blur', $.proxy(this.blur, this))
        .on('keyup', $.proxy(this.keyup, this))
    }

  , lookup: function (force) {
      var slug
        , data = {}
        , that = this

      this.query = this.$element.val()

      if (!this.query || this.query.length < this.options.minLength) {
        return this
      }

      data[this.options.queryString] = this.query

      $.getJSON(this.source, data, function (data) {
        if (typeof data == 'object' && data) {
          that.render(data.slug, typeof force == 'undefined' ? false : true)
        }
      })

      return this
    }

  , render: function (slug, force) {
      var that = this

      this.$target.each(function () {
        var $this = $(this)

        if (!force && $this.hasClass(that.options.sluggedClass)) {
          return
        }

        $this.val(slug)
      })

      return this
    }

  , refresh: function () {
      this.lookup(true)
  }

  , blur: function (e) {
      this.lookup()
    }

  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
        case 9:  // tab
        case 13: // enter
        case 27: // escape
          break

        default:
          this.lookup()
      }

      e.stopPropagation()
      e.preventDefault()
    }

  }


 /* SLUGGER PLUGIN DEFINITION
  * ========================= */

  $.fn.slugger = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('slugger')
        , options = typeof option == 'object' && option
      if (!data) $this.data('slugger', (data = new Slugger(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.slugger.defaults = {
    minLength: 2,
    queryString: 'query',
    sluggedClass: 'slugged',
    source: ''
  }

  $.fn.slugger.Constructor = Slugger

  /* SLUGGER DATA-API
   * ================ */

  $(document).on('focus.slugger.data-api', '[data-provide="slugger"]', function (e) {
    var $this = $(this)
    if ($this.data('slugger')) return
    e.preventDefault()
    $this.slugger($this.data())
  })

}(window.jQuery);

