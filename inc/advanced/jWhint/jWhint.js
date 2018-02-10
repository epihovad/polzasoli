var zWhint = 10;

jQuery.fn.jWhint = function(){
	
	$(this).each(function(index,element){$(this).attr('hnum',index)});
	
	$(this).click(function(){
		var $h = $(this);
		var $p = $('div.whint[hnum="'+$h.attr('hnum')+'"]');
		if(!$p.size())
		{
			$p = $('<div class="whint" hnum="'+$h.attr('hnum')+'"></div>');
			$('body').prepend($p);
			$p.load('/inc/advanced/jWhint/jWhint.php','w='+$h.html(),function(){
				var $close = $('<div class="whint-close"></div>');
				$p.prepend($close);
				$close.css('margin','-5px 0 0 '+($p.width()-3)+'px')
				$close.click(function(){whint_sh($p,$h)});
				whint_sh($p,$h);
			});
		}
		else whint_sh($p,$h);
		return false;
	});
	
	function whint_sh($p,$h)
	{
		if($p.is(':visible')) $p.fadeOut();
		else
		{
			if(!$p.html()) return false;
			var pos = $h.offset();
			var left = pos.left < $('body').width()/2 ? false : true;
			var w = $p.width();
			var h = $p.height();
			var t = pos.top-h-20;
			var l = pos.left+(left ? -(w+25) : 50);
			$('div.whint').not($p).each(function(){if($(this).is(':visible')) $(this).fadeOut()});
			$p.css({'top':t,'left':l,'z-index':zWhint++}).fadeIn();
		}
	}
	
	$(document).click(function(event){
		var $o = $(event.target);
		$('div.whint').each(function(){
      var $p = $(this);
			if(!$p.is(':visible')) return true;
			if($o.hasClass('whint')) return true;
			if(!$o.parents('.whint').size()) whint_sh($p);
    });
	});
};