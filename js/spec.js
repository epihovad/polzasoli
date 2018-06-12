/*jQuery(document).ready(function( $ ) {

});*/

$(function () {

  Ch2btn();
  iReviews();

});

function Ch2btn() {
  $('.ch2btn button').click(function () {
    if($(this).hasClass('active')){
      return true;
    }
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
  });
}

function iReviews() {
  var $ireviews = $('#ireviews');
  var $ireviews_story = $('#ireviews-story');
  var $ireviews_video = $('#ireviews-video');
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
  //
  $ireviews.find('.ch2btn button').click(function () {
    if($(this).attr('for') == 'ireviews-video'){
      $ireviews_story.hide();
      $ireviews_video.show();
    } else {
      $ireviews_video.hide();
      $ireviews_story.show();
    }
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