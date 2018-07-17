$(function () {

  Ch2btn();
  iReviews();
  iGallery();
  chQuant();
  iFAQ();
  //
  $('header .hb2 button').click(function(){
    scrollTo($('#bron'), 0, 700);
  });
  //
  Inputmask({mask: '+7 (999) 999-99-99', showMaskOnHover: false}).mask($('#fbron input[name="phone"]'));
  //
  $('header .hb3 button').click(function(){
    jPop('/inc/actions.php?show=seance');
  });
  //
  $('#subscribe .frm i').click(function(){
    $(this).addClass('disabled');
    inajax('/inc/actions.php?action=subscribe','email='+$(this).prev().val());
  });
});

function Ch2btn() {
  $('.ch2btn button').click(function () {
    if($(this).hasClass('active')){
      return true;
    }
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    var $cur = $('#' + $(this).attr('for'));
    var $sib = $('#' + $(this).siblings(':first').attr('for'));
    $sib.hide();
    $cur.show();
  });
}

function iReviews() {
  var $ireviews = $('#ireviews');
  //
  $ireviews.find('.author img').click(function () {
    var $item = $(this).parent('.item:first');
    if($item.hasClass('active')){
      return false;
    }
    var ind = $item.index();
    var $par = $item.parents('#ireviews-story').length ? $item.parents('#ireviews-story') : $item.parents('#ireviews-video');
    var $cont = $item.parents('#ireviews-story').length ? $par.find('.txt') : $par.find('.video');
    $item.siblings().removeClass('active');
    $item.addClass('active');
    $cont.find('.item').hide();
    $cont.find('.item').eq(ind).show();
  });
}

function iGallery(){
  $('#igallery .item a').click(function () {
    var $par = $(this).parents('#igallery-photo').length ? $(this).parents('#igallery-photo') : $(this).parents('#igallery-video');
    var ind = parseInt($(this).attr('ind'));
    ind = isNaN(ind) ? 0 : ind;
    var $im = $par.find('.item a[ind=' + ind + ']');
    var link = $im.attr('href'),
      options = {index: link, index: ind},
      links = $par.find('.item a');
    blueimp.Gallery(links, options);
    return false;
  });
}

function chQuant($area){
  //
  if($area == undefined){
    $area = $(document);
  }
  //
  $area.find('.btn-number').click(function(e){
    e.preventDefault();
    var $block    = $(this).parents('.input-group:first');
    var $input    = $block.find('input[type="text"]');
    var type      = $(this).attr('data-type');
    var currentVal = parseInt($input.val());
    if (!isNaN(currentVal)) {
      if(type == 'minus') {
        if(currentVal > $input.attr('min')) {
          $input.val(currentVal - 1).change();
        }
        if(parseInt($input.val()) == $input.attr('min')) {
          $(this).attr('disabled', true);
        }
      } else if(type == 'plus') {
        if(currentVal < $input.attr('max')) {
          $input.val(currentVal + 1).change();
        }
        if(parseInt($input.val()) == $input.attr('max')) {
          $(this).attr('disabled', true);
        }
      }
    } else {
      $input.val(0);
    }
  });
  $area.find('.input-number').focusin(function(){
    $(this).data('oldValue', $(this).val());
  });
  $area.find('.input-number').change(function() {
    var $block = $(this).parents('.input-group:first');
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    if(valueCurrent >= minValue) {
      $block.find('.btn-number[data-type="minus"]').removeAttr('disabled')
    } else {
      alert('Sorry, the minimum value was reached');
      $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
      $block.find('.btn-number[data-type="plus"]').removeAttr('disabled')
    } else {
      alert('Sorry, the maximum value was reached');
      $(this).val($(this).data('oldValue'));
    }
  });
  $area.find('.input-number').keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
      // Allow: Ctrl+A
      (e.keyCode == 65 && e.ctrlKey === true) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });
}

function iFAQ() {
  $('#ifaq .ifaq-q').click(function () {
    $('#ifaq .ifaq-q.active').removeClass('active');
    $('#ifaq .ifaq-a.active').removeClass('active');
    $(this).addClass('active');
    $(this).next(':first').addClass('active');
    return false;
  });
}

function jPop(url) {
  jQuery.arcticmodal({
    type: 'ajax',
    url: url,
    ajax: {
      type:'GET',
      cache: false,
      success:function(data, el, responce){
        data.body.html(jQuery('<div class="box-modal"><div class="box-modal_close arcticmodal-close glyphicon glyphicon-remove"></div>' + responce + '</div>'));
      }
    }
  });
}

function toCart(mod,quant,dopURL)
{
  inajax('/cart.php','action=add&mod='+mod+'&quant='+(quant*1<1?1:quant)+dopURL);
}

function update_captcha()
{
	var tmp = new Date();
	$('#captcha').attr('src','/captcha/'+tmp+'/');
}

function isiPhone(){
  return (
    (navigator.platform.indexOf("iPhone") != -1) ||
    (navigator.platform.indexOf("iPod") != -1)
  );
}