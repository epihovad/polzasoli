/*jQuery(document).ready(function( $ ) {

});*/

$(function () {

  Ch2btn();
  iReviews();
  iGallery();

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