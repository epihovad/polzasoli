jQuery(document).ready(function( $ ) {

});

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