$(function () {

  var btns  = '<a href="#" class="fa fa-angle-up" id="nav-up"></a>';
      btns += '<a href="#" class="fa fa-angle-down" id="nav-down"></a>';

  $('body').append(btns);

  var special = jQuery.event.special,
    uid1 = 'D' + (+new Date()),
    uid2 = 'D' + (+new Date() + 1);

  special.scrollstart = {
    setup: function () {

      var timer,
        handler = function (evt) {

          var _self = this,
            _args = arguments;

          if (timer) {
            clearTimeout(timer);
          } else {
            evt.type = 'scrollstart';
            jQuery.event.handle.apply(_self, _args);
          }

          timer = setTimeout(function () {
            timer = null;
          }, special.scrollstop.latency);

        };

      jQuery(this).bind('scroll', handler).data(uid1, handler);

    },
    teardown: function () {
      jQuery(this).unbind('scroll', jQuery(this).data(uid1));
    }
  };

  special.scrollstop = {
    latency: 300,
    setup: function () {

      var timer,
        handler = function (evt) {

          var _self = this,
            _args = arguments;

          if (timer) {
            clearTimeout(timer);
          }

          timer = setTimeout(function () {

            timer = null;
            evt.type = 'scrollstop';
            jQuery.event.handle.apply(_self, _args);

          }, special.scrollstop.latency);

        };

      jQuery(this).bind('scroll', handler).data(uid2, handler);

    },
    teardown: function () {
      jQuery(this).unbind('scroll', jQuery(this).data(uid2));
    }
  };

  var $elem = $('body');

  $('#nav-down').click(
    function (e) {
      $('html, body').animate({scrollTop: $elem.height()}, 1000);
    }
  );
  $('#nav-up').click(
    function (e) {
      $('html, body').animate({scrollTop: '0px'}, 1000);
    }
  );

});