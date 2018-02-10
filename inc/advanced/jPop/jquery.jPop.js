/*
jPop - собственный плагин (v1.0).
Всплывающие окна с контентом.
*/

(function($){
	
	// ------------- глобальные объекты ------------
	var $jPop, /* окно */
			$jPop_close, /* кнопка "Выход" */
			$jPop_head, /* шапка-заголовок */
			$jPop_footer, /* шапка-заголовок */
			$jPop_cont, /* контент */
			$jPop_ind, /* индикатор загрузки */
			$jPop_shadow, /* тень окна */
			$jPop_blackout /* затемнение */
			
	// ------------- глобальные переменные ------------
	var jPop_pos; /* позиция */
	var jPop_blackout; /* затемнение */
	var jPop_blackout_params; /* параметры затемнения */
	var ie;
	
	var jPop_methods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ -------------------- */
		init : function(prm){
			
			$jPop = $('<div id="jPop"></div>'); // окно
			$jPop_head = $('<div id="jPop_head"><div></div></div>'); /* шапка-заголовок */
			$jPop_footer = $('<div id="jPop_footer"><div></div></div>'); /* шапка-заголовок */
			$jPop_close = $('<a id="jPop_close" class="inline" href=""></a>'); // кнопка "Выход"
 			$jPop_cont = $('<div id="jPop_cont"></div>'); // контент внутри окна
			$jPop_ind = $('<div id="jPop_ind"><img src="/inc/advanced/jPop/img/ind.gif" width="31" height="31"></div>'); // индикатор загрузки
			$jPop_shadow = $('<div id="jPop_shadow"></div>'); // тень от окна (только для IE)
			
			// устанавливаем размеры окна
			$jPop.prepend($jPop_footer).prepend($jPop_cont).prepend($jPop_head).prepend($jPop_close).prepend($jPop_ind);

			$('body').prepend($jPop);
			
			ie = $.browser.msie ? parseInt($.browser.version) : false;
			if(ie>9) ie = false;
			
			if(ie)
				$('body').prepend($jPop_shadow);
			
			$jPop_close.click(function(){ jPop_methods.hide.call(); return false; });
				
			$(window).resize(function(){ jPop_methods.repos.call() });
			
			$(document).mousewheel(function(event){
				if($jPop.is(':visible'))
				{
					if(!$(event.currentTarget).parents($jPop).size())
						event.preventDefault();
				}
			});
		},
		
		show : function(prm){
			
			prm = $.extend({
				title : '',
				footer : '',
				pos : 'center',
				ws : true, // с применением тени
				ws_params : {opacity:50,z:zmax?zmax:999}, // параметры тени
				z : zmax?zmax+1:1000, // z-index окна
				callback : function(){}
			}, prm);
			
			if(!$jPop.size()) jPop_methods.init.call();
			if(!prm.url && !prm.data) return false;
			
			if(prm.callback)
				$jPop_close.click(function(){ prm.callback.call() });
			
			if(prm.title) $jPop_head.find('div').html(prm.title);
			else $jPop_head.height(0);
			
			if(prm.footer)
			{
				$jPop_footer.find('div').html(prm.footer);
				$jPop_footer.addClass('act');
			}
			
			var windW, windH, windL, windT;
			
			if(!$jPop.is(':visible'))
			{
				jPop_pos = prm.pos;
				jPop_blackout = prm.ws;
				jPop_blackout_params = prm.ws_params;
				
				$jPop.css({
					'width' : (prm.w ? prm.w : Math.round($('body').width()/2)),
					'height' : (prm.h ? prm.h : Math.round($('body').height()/2)),
					'z-index' : prm.z
				});
				if(!ie)
					$jPop.css('-moz-border-radius','20px'); // иначе осёл сходит с ума
				
				windW = $jPop.width();
				windH = $jPop.height();
				windL = 0;
				windT = 0;
				switch(prm.pos)
				{
					default:
					case 'center':
						windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
						windT = Math.round( ($('body').height()/2) - (windH/2) + $(window).scrollTop() );
						break;
					case 'top':
						windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
						windT = Math.round( $(window).scrollTop() + 20 );
						break;				
				}
				
				$jPop.css({'left':windL,'top':windT});
				$jPop_cont.css({'width':windW-30,'height':windH-35-(prm.title?48:0)-(prm.footer?48:0)});
				indL = Math.round( (windW/2) - ($jPop_ind.width()/2) );
				indT = Math.round( (windH/2) - ($jPop_ind.height()/2) );
				$jPop_close.css('margin','16px 0 0 '+(windW-30)+'px');
				$jPop_ind.css('margin',indT+'px 0 0 '+indL+'px');
				
				if(jPop_blackout) $jPop_blackout = $(document).jB('show',jPop_blackout_params);
				
				$jPop.show();
				
				if(ie)
					$jPop_shadow.css({'width':windW,'height':windH,'left':windL-4,'top':windT-4,'z-index':parseInt($jPop.css('z-index'))-2}).show();
				
				if(prm.url)
				{
					$jPop_cont.load(prm.url,function(){
						$jPop_ind.hide();
					});
				}
				else if(prm.data)
				{
					$jPop_ind.hide();
					$jPop_cont.html(prm.data);
				}
				
				if(zmax) zmax += 2;
			}
			else
			{
				windW = prm.w;
				windH = prm.h;
				windL = 0;
				windT = 0;
				switch(prm.pos)
				{
					default:
					case 'center':
						windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
						windT = Math.round( ($('body').height()/2) - (windH/2) + $(window).scrollTop() );
						break;
					case 'top':
						windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
						windT = Math.round( $(window).scrollTop() + 20 );
						break;				
				}
				
				$jPop_cont.html('');
				$jPop_cont.css({'width':windW-30,'height':windH-35-(prm.title?48:0)-(prm.footer?48:0)});
				indL = Math.round( (windW/2) - ($jPop_ind.width()/2) );
				indT = Math.round( (windH/2) - ($jPop_ind.height()/2) );
				$jPop_close.css('margin','16px 0 0 '+(windW-30)+'px');
				$jPop_ind.css('margin',indT+'px 0 0 '+indL+'px');
				
				if(ie)
					$jPop_shadow.hide();
				
				$jPop.animate({
					'width' : windW,
					'height' : windH,
					'left' : windL,
					'top' : windT
				},300,function(){
					$jPop_ind.show();
					if(prm.url)
					{
						$jPop_cont.load(prm.url,function(){
							$jPop_ind.hide();
						});
					}
					else if(prm.data)
					{
						$jPop_ind.hide();
						$jPop_cont.html(prm.data);
					}
					if(ie)
						$jPop_shadow.css({'width':windW,'height':windH,'left':windL-4,'top':windT-4,'z-index':parseInt($jPop.css('z-index'))-2}).show();
				});
			}
		},
		
		hide : function(callback){
			
			$jPop.add($jPop_shadow).hide();
			if(jPop_blackout)
				$jPop_blackout.jB('hide');
			$jPop_cont.html('');
			$jPop_ind.show();
		},
		
		resize : function(prm,callback){
		
			prm = $.extend({
				w : 0,
				h : 0,
				pos : 'center'
			}, prm);
			
			windW = prm.w ? prm.w : $jPop.width();
			windH = prm.h ? prm.h : $jPop.height();
			windL = 0;
			windT = 0;
			switch(prm.pos)
			{
				default:
				case 'center':
					windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
					windT = Math.round( ($('body').height()/2) - (windH/2) + $(window).scrollTop() );
					break;
				case 'top':
					windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
					windT = Math.round( $(window).scrollTop() + 20 );
					break;				
			}
			
			if(ie)
				$jPop_shadow.hide();
					
			$jPop_cont.animate({'width':windW-30,'height':windH-35-(prm.title?48:0)-(prm.footer?48:0)},300);
			$jPop_close.hide();
			$jPop.animate({'width':windW,'height':windH,'left':windL,'top':windT},300,'linear',function(){
				$jPop_close.css('margin','16px 0 0 '+(windW-30)+'px');
				if(ie)
					$jPop_shadow.css({'width':windW,'height':windH,'left':windL-4,'top':windT-4,'z-index':parseInt($jPop.css('z-index'))-2}).show();
				if(callback) callback.call();
			});			
			
		},
		
		repos : function(){
			
			var jPopLeft = Math.round( ($('body').width()/2) - ($jPop.width()/2) + $(window).scrollLeft() );
			var jPopTop = 0;
			switch(jPop_pos)
			{
				default:
				case 'center':
					jPopTop = Math.round( ($('body').height()/2) - ($jPop.height()/2) + $(window).scrollTop() );
					break;
				case 'top':
					jPopTop = Math.round( $(window).scrollTop() + 20 );
					break;				
			}
			
			// окно
			$jPop.css({'left':jPopLeft,'top':jPopTop});
			// тень от окна
			$jPop_shadow.css({'left':jPopLeft-4,'top':jPopTop-4});
			
		}
	};
	
	/* ---------------- ПЛАГИН jPop -------------------- */
	$.fn.jPop = function(method){
		// логика вызова метода
		if(jPop_methods[method]) {
			return jPop_methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if (typeof method === 'object' || !method) {
			return jPop_methods.init.apply(this,arguments);
		}/* else {
			$.error('Метод'+method+' в jQuery.jPop не существует');
		}*/
	};
	
})(jQuery);

$(function(){ $(document).jPop() });