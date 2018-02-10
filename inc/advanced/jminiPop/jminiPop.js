(function($){
	
	// ------------- глобальные объекты ------------
	var $jminiPop, 			/* окно */
			$jminiPop_corn,	/* уголок */
			$jminiPop_ind, 	/* индикатор загрузки */
			$jminiPop_cont; /* контент */
	
	var timer;
	
	var jminiPop_methods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ -------------------- */
		init : function(prm){
			
			$jminiPop = $('<div id="jminiPop"></div>'); // окно
			$jminiPop_corn = $('<div id="jminiPop_corn"></div>'); // уголок
			$jminiPop_ind = $('<div id="jminiPop_ind"><img src="/inc/advanced/jminiPop/ind.gif" width="16" height="11"></div>'); // индикатор загрузки
			$jminiPop_cont = $('<div id="jminiPop_cont"></div>'); // контент внутри окна
			
			$jminiPop.prepend($jminiPop_cont).prepend($jminiPop_ind).prepend($jminiPop_corn);

			$('body').prepend($jminiPop);

			$(document).click(function(event){
				if($jminiPop.is(':visible'))
				{
					if(!$(event.target).closest('#jminiPop').length)
						jminiPop_methods.hide.call();
				}
			});
		},
		
		show : function(prm){

			prm = $.extend({
				w : 100, // ширина
				h : 60, // высота
				Top: 0, // корректировка высоты
				timer : 0, // таймер на закрытие
				url : '', // ссылка на загружаемый контент
				data : '', // загружаемые данные
				z : zmax?zmax+1:1000 // z-index окна
			}, prm);
			
			var $obj = $(this);
		
			if(!$obj.size()) return false;
			if(!$jminiPop.size()) jminiPop_methods.init.call();
			
			var windW, windH, windL, windT;
			var indL, indT;
			var cornL, cornT;
			var cornclass = '';
			
			if($jminiPop.is(':visible'))
				jminiPop_methods.hide.call();
			
			windW = prm.w;
			windH = prm.h;
			var pos = $obj.offset();
			if(pos.left>(windW+20))
			{
				windL = pos.left-windW-10;
				cornclass = 'onright';
			}
			else
			{
				windL = pos.left+$obj.width()+10;
				cornclass = 'onleft';
			}
			windT = pos.top + 2 + prm.Top;
			
			$jminiPop.css({'width':windW,'height':windH,'left':windL,'top':windT,'z-index':prm.z});
			
			indL = Math.round( (windW/2) - ($jminiPop_ind.width()/2) );
			indT = Math.round( (windH/2) - ($jminiPop_ind.height()/2) );
			$jminiPop_ind.css('margin',indT+'px 0 0 '+indL+'px');
			
			cornL = cornclass=='onright' ? windW : - $jminiPop_corn.width();
			cornT = 8;
			$jminiPop_corn.css({'left':cornL,'top':cornT}).addClass(cornclass);

			$jminiPop.show();
			
			if(prm.url)
			{
				$jminiPop_cont.load(prm.url,function(){
					$jminiPop_ind.hide();
				});
			}
			else if(prm.data)
			{
				$jminiPop_ind.hide();
				$jminiPop_cont.html(prm.data);
			}
			
			if(zmax) zmax += 2;
			
			if(prm.timer)
				timer = setTimeout(function(){jminiPop_methods.hide.call()},prm.timer);
		},
		
		hide : function(callback){
			
			$jminiPop.hide();
			$jminiPop_cont.html('');
			$jminiPop_ind.show();
			if(timer) clearTimeout(timer);
		}
	};
	
	/* ---------------- ПЛАГИН jminiPop -------------------- */
	$.fn.jminiPop = function(method){
		// логика вызова метода
		if(jminiPop_methods[method]) {
			return jminiPop_methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if (typeof method === 'object' || !method) {
			return jminiPop_methods.init.apply(this,arguments);
		}/* else {
			$.error('Метод'+method+' в jQuery.jPop не существует');
		}*/
	};
	
})(jQuery);

$(function(){ $(document).jminiPop() });