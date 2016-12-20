/* Thanks to ... https://github.com/tamarasaurus/focalpoint */

var focalPoint = function (element, options) {
  var focalpoint = this;
  this.$element = $(element);
  this.options = $.extend({
    point: $('.focus-point', this.$element)
  }, options);


  // Click on container
  $(this.$element).on('click', $('img', this.$element), function (e) {
    // Calculate focal point
    var pos = focalpoint.calculate($(focalpoint.$element).offset().top, $(focalpoint.$element).offset().left, e.clientY, e.clientX);

    // Relative positioning
    $(options.point).css({
      'left': pos.x * 100 + '%',
      'top': pos.y * 100 + '%'
    });

    //Callback for position
    if (typeof options.callback !== 'undefined') {
      options.callback.call(null, pos);
    }
  })
};

focalPoint.prototype = {
  // Get percentage values from pixels (doesn't account for body margin/padding)
  calculate: function (t_set, l_set, t_pos, l_pos) {
    var image = $(this.$element);
    var offset_t = image.offset().top - $(window).scrollTop();
    var offset_l = image.offset().left - $(window).scrollLeft();
    var width = image.height();
    var height = image.width();
    var top = Math.round((t_pos - offset_t));
    var left = Math.round((l_pos - offset_l));
    percentx = Math.round((left / height) * 100) / 100;
    percenty = Math.round((top / width) * 100) / 100;
    return {
      x: percentx,
      y: percenty,
      pos_x: (l_pos + $(window).scrollLeft() - ($(this.options.point).width() / 2)),
      pos_y: (t_pos + $(window).scrollTop() - ($(this.options.point).width() / 2))
    };
  },
  // Get pixel values from percentages
  reverse_calculate: function (el) {
    // Pass along percentage values in focal_x & focal_y
    var default_x = this.options.focal_x.val();
    var default_y = this.options.focal_y.val();
    var image_x = $(el).offset().left;
    var image_y = $(el).offset().top;
    var image_width = $(el).width();
    var image_height = $(el).height();
    var final_x = (image_width * default_x) + image_x;
    var final_y = (image_height * default_y) + image_y;
    //$(options.point).offset({top:(final_y-40), left: (final_x-40) }).show();
  }
};

(function (factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else {
    factory(window.jQuery);
  }
}(function ($) {
  $.fn.focalpoint = function (option) {
    var args = [].splice.call(arguments, 1);

    return this.each(function () {
      var $this = $(this),
      data = $this.data('focalpoint'),
      options = typeof option === 'object' && option;
      if (!data) {
        $this.data('focalpoint', (data = new focalPoint(this, options)));
      } else if (typeof option === 'string') {
        data[option].apply(data, args);
      }
    });
  };
}));
