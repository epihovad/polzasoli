var $jHints = '';

jQuery.fn.jHint = function(){
	
	$.preloadImg('/inc/advanced/jHint/jHint.png');
	
	return this.each(function(){
		
		var $jHint = $(this);
		var $jHint_text = $jHint.next(':first');
	
		if(!$jHint_text.hasClass('jHint_text') || $jHint_text.html()=='') return true;
		
		if($jHints)
		{
			var f = false;
			$jHints.each(function(){
				if(this==$jHint.get(0)) { f = true; return false;	}
			});
			if(f) return true;
		}
		
		var $clone = $jHint_text.clone();
		$jHint_text.remove();
		$jHint_text = $clone;
		var $corner = $('<div class="jHint_corn"></div>');
		$jHint_text.prepend($corner);
		$('body').append($jHint_text);
		
		var w = $jHint_text.width();
		var h = $jHint_text.height();
		var padding = {
			l : $jHint_text.css('padding-left').replace('px','')*1,
			r : $jHint_text.css('padding-right').replace('px','')*1,
			u : $jHint_text.css('padding-top').replace('px','')*1,
			b : $jHint_text.css('padding-bottom').replace('px','')*1
		}
		
		if($jHint.attr('once'))
		{
			jHintPos();
			var t = $jHint.attr('time')*1;
					t = t ? t*1000 : 7000;
			setTimeout(function(){$jHint_text.hide()},t);
		}
		else
		{
			$jHint.click(function(){return false}).hover(
				function(){ jHintPos() },
				function(){ $jHint_text.hide() }
			);
		}
		
		function jHintPos()
		{
			var pos = $jHint.offset();
			var left = pos.left < $('body').width()/2 ? false : true;
			// позиционируем уголок
			$corner.addClass(left?'r':'l').css('margin',(h/2-3)+'px 0 0 '+(left?(w+padding.l):(-4-padding.r))+'px');
			// позиционируем подсказку
			var t = pos.top+3-(h/2);
			var l = pos.left+(left ? -(w+25) : $jHint.width()+10);
			$jHint_text.css({'top':t,'left':l}).show();
		}
		
	});
	
	if(!$jHints) $jHints = this;
};