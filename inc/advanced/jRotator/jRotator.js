jQuery.fn.jRotator = function(prm){
	
	// параметры плагина
	prm = $.extend({
		pAlign : 'center',
		thumbW : '120',
		thumbH : '64',
		nothumb : false,
		timeLineColor : '#ff0000'
	}, prm);
	
	var $obj = $(this);
	var $images = $obj.find('img');
	var count = $images.size();
	if(!count) return false;
	var move = true;
	var t1;
	
	$images.hide();
	
	var $jR = $('<div class="jR"></div>');
	var $jR_images	= $('<div class="jR-images"></div>');
	var $jR_timeLine = $('<div class="jR-timeline"></div>');
	if(!prm.nothumb)
		var $jR_thumbs = $('<div class="jR-thumbs"></div>');
	$images.each(function(){
		var src = $(this).attr('src');
		var thumb = $(this).attr('thumb');
				thumb = thumb ? thumb : src;
		var href = '';
		var $a = $(this).parents(':first');
		if($a.is('a')) href = $a.attr('href');
		$jR_images.append('<div bg="'+src+'" href="'+href+'"></div>');
		if(!prm.nothumb)
			$jR_thumbs.append('<img src="'+thumb+'" width="'+prm.thumbW+'" height="'+prm.thumbH+'">');
	});
	var $L = $('<div class="jR-prev"></div>');
	var $R = $('<div class="jR-next"></div>');
	var cnt = count;
	var $jR_points = $('<div class="jR-points">'+(new Array(++cnt).join('<div></div>'))+'</div>');

  $jR.prepend($jR_images);
  if(count>1)
    $jR.prepend($jR_points).prepend(!prm.nothumb?$jR_thumbs:'').prepend($L).prepend($R).prepend($jR_timeLine.css('background-color',prm.timeLineColor));
	
	var T = 7000;
	var t1;
	var w = $obj.width();
	var h = $obj.height();
	var pW = 18;
	var pH = 18;
	var aW = 35;
	var aH = 35;
	var pM = 7; // point margin-left
	var pL; // points отступ слева
	
	$jR_points.find('div').each(function(index,element){
    var mL = (pW+pM)*index;
		$(element).css('margin-left',mL);
		if(!prm.nothumb)
			$jR_thumbs.find('img').eq(index).css('margin-left',mL);
  });
	
	$jR.add($jR_images).add($jR_images.find('div')).css({'width':w,'height':h});
	
	switch(prm.pAlign)
	{
		case 'left': 		pL = pH; break;
		case 'center':	pL = w/2-(pW*count+pM*(count-1))/2; break;
		case 'right':		pL = w-(pW*count+pM*(count-1))-pH; break;
	}
	$jR_points.css({'margin':(h-15-pH)+'px 0 0 '+pL+'px','width':'auto'});
	$L.css('margin',(h/2-aH/2)+'px 0 0 10px');
	$R.css('margin',(h/2-aH/2)+'px 0 0 '+(w-aW-10)+'px');
	if(!prm.nothumb)
	{
		var tL;
		switch(prm.pAlign)
		{
			case 'left':		tL = pL*1.5; break;
			case 'center':	tL = pL-prm.thumbW/2; break;
			case 'right':		tL = pL-prm.thumbW; break;
		}
		$jR_thumbs.css('margin',(parseInt($jR_points.css('margin-top').replace('px',''))+pH+10)+'px 0 0 '+tL+'px');
	}
	var $im = $jR_images.find(':first');
	var img = new Image();
	img.onload = function(){
		$im.css('background-image','url('+$im.attr('bg')+')');
		$im.fadeIn('slow','',function(){
			$jR_points.find(':first').addClass('cur');
			move = true;
      if(count>1)
      {
        $jR_timeLine.animate({'width':w},T,'linear');
        t1 = setInterval(function(){slide(1)},T);
      }
		});
	}
	$(img).attr('src',$im.attr('bg'));
	
	$obj.prepend($jR);
	
	$jR_images.find('div').click(function(){
		var href = $(this).attr('href');
		if(!href) return false;
		location.href = href;
	});
	
	$obj.hover(function(){$L.add($R).show()},function(){$L.add($R).hide()});
	
	$jR_points.find('div')
	.click(function(){
		if($(this).hasClass('cur')) return false;
		$jR_points.find('div').removeClass('cur');
		$(this).addClass('cur');
		slide(0,$(this).index()+1);
	});
	if(!prm.nothumb)
		$jR_points.find('div').hover(
			function(){$jR_thumbs.find('img').eq($(this).index()).fadeIn(200)},
			function(){$jR_thumbs.find('img').hide()}
		);
	
	$L.click(function(){ if(move) slide(); return false; });
	$R.click(function(){ if(move) slide(1); return false; });
	
	function slide(right,ind)
	{
    if(count<2) return false;

		move = false;
		
		clearInterval(t1);
		$jR_timeLine.stop().width(0);

		var $cur = $jR_images.find('div:visible');
		var cur_ind = $cur.index();

		if(ind) ind--;
		else 		ind = right ? (cur_ind+1<count?cur_ind+1:0) : (cur_ind-1<0?count-1:cur_ind-1);

		$jR_images.find('div').hide();
		
		var $im = $jR_images.find('div').eq(ind);

		$jR_points.find('div').removeClass('cur');
		$jR_points.find('div').eq(ind).addClass('cur');
		
		if($im.css('background-image')=='none')
		{
			var img = new Image();
			img.onload = function(){
				$im.css('background-image','url('+$im.attr('bg')+')');
				$cur.fadeOut('slow');
				$im.fadeIn('slow','',function(){
					move = true;
					$jR_timeLine.animate({'width':w},T,'linear');
					t1 = setInterval(function(){slide(1)},T);
				});
			}
			$(img).attr('src',$im.attr('bg'));
		}
		else
		{
			$cur.fadeOut('slow');
			$im.fadeIn('slow','',function(){
				move = true;
				$jR_timeLine.animate({'width':w},T,'linear');
				t1 = setInterval(function(){slide(1)},T);
			});
		}
	}
};