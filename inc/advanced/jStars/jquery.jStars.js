/*
jStars - собственный плагин (v1.0).
Звёздный рейтинг
*/

jQuery.fn.jStars = function(prm){
	
	// параметры плагина
	prm = $.extend({
		path : '/inc/advanced/jStars/',
		star_on : '',
		star_off : '',
		size : {'w':'24','wm':'17','h':'22','hm':'16'},
		w : 0,
		h : 0,
		count : 5,
		active : true,
		score : 0,
		name : '',
		url : '', // для ajax'а
		callback : function(){}
	}, prm);
	
	return this.each(function()
	{
		var $obj = $(this);
		$.preloadImg(prm.star_on,prm.star_off);
		
		prm.active = $obj.attr('active') ? ($obj.attr('active')=='1'?true:false) : false;
		prm.score = $obj.attr('score') ? $obj.attr('score') : 0;
		prm.name = $obj.attr('name') ? $obj.attr('name') : '';
		
		var mini = $obj.hasClass('mini');
		prm.w = !mini ? prm.size.w : prm.size.wm;
		prm.h = !mini ? prm.size.h : prm.size.hm;
		prm.star_on = prm.path+'img/star-on'+(mini?'-mini':'')+'.png';
		prm.star_off = prm.path+'img/star-off'+(mini?'-mini':'')+'.png';
		
		var $div = $('<div style="height:'+prm.h+'px; width:'+(prm.w*prm.count)+'px; font-size:0; background-image:url('+prm.star_off+'); cursor:'+(prm.active?'pointer':'default')+';"></div>');
		var $fake = $('<div style="height:'+prm.h+'px; width:'+Math.round(prm.score*prm.w)+'px; font-size:0; background-image:url('+prm.star_on+');"></div>');
		var $starblock = $('<div style="position:relative; height:'+prm.h+'px; width:'+(prm.w*prm.count)+'px; margin-top:-'+prm.h+'px; font-size:0; visibility:hidden; font-size:0;"></div>');
		for(var i=0; i<prm.count; i++)
		{
			$starblock.append('<img src="'+prm.star_off+'" alt="" title="'+(i+1)+'" />');
		}
		var $input = $('<input type="hidden" value="'+prm.score+'" name="'+prm.name+'">');
		
		$div.append($fake).append($starblock);
		$obj.append($div);
		
		if(prm.name) $div.after($input);
		
		if(prm.active)
		{
			$starblock.find('img').mouseover(function(){ showRating($(this)) });
			$starblock.find('img').click(function(){ setRating($(this)) });
			$starblock.mouseout(function(){ $(this).css('visibility','hidden') });			
			$div.mouseover(function(){ if(prm.active) $starblock.css('visibility','visible') });	
		}
		
		function showRating($star,r)
		{
			r = $star.index();
			var i=0;
			$star.parent().find('img').each(function(){
				$(this).attr('src',i>r?prm.star_off:prm.star_on);
				i++;
			});
		}	
		function setRating($star,r)
		{
			r = $star.index()+1;
			$fake.width(r*prm.w);
			if(prm.name) $input.val(r); // записываем значение в скрытое поле
			if(prm.url) inajax(prm.url+'&score='+r); // или передаём по ссылке
		}
	});
}