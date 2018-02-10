jQuery.fn.jTip = function(){
	
	return this.each(function(){
		
		var $obj = $(this);
		var $jTip = $obj.find('span');
		
		if(!$jTip.size())
		{
			var txt = $obj.attr('title');
			if(!txt) return true;
		
			$jTip = $('<span><i>'+txt+'</i></span>');
			$obj.prepend($jTip);
			$obj.attr('title','');
		}
		
		var W = $obj.width();
		var H = $obj.height();
		var w = $jTip.width();
		var h = $jTip.height();
		
		$jTip.css({
			'top':-(h+1)+'px',
			'margin':'0 0 0 '+(Math.abs((w-W)/2)*(w-W<0?1:-1))+'px'
		});
		
		$obj.hover(function(){$jTip.show()},function(){$jTip.hide()});
		
	});
};

$(function(){ $('.jTip').jTip(); });