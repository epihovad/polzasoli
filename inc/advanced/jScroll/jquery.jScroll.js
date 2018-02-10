(function($){
	
	var jScroll_methods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ -------------------- */
		init : function(prm){
			$(this).each(function(){
		
				var $jScroll = $(this);
				if($jScroll.parents('.jScroll_ar:first').size())
					jScroll_methods.destroy.call($jScroll);
				
				var h = $jScroll.attr('h');
				if(!h) return true;
				if($jScroll.height()<h) return true;
				
				var delPadding = $jScroll.attr('del') ? true : false; // частный случай
				if(delPadding)
					$jScroll.find('div').css({'padding-top':0,'padding-bottom':0});
				
				$jScroll.prepend('<div class="pad clear"></div>').append('<div class="pad clear"></div>');
				
				var w = $jScroll.width();
				var H = $jScroll.get(0).scrollHeight;
				
				var tb = $jScroll.attr('tb');
						tb = tb ? ' '+tb : '';

				var $jScroll_ar = 		$('<div class="jScroll_ar"></div>'); // обёртка объекта
				var $jScroll_top = 		$('<div class="jScroll_top'+tb+'"></div>'); // затухание сверху
				var $jScroll_bot = 		$('<div class="jScroll_bot'+tb+'"></div>'); // затухание снизу
				var $jScroll_rp = 		$('<div class="jScroll_rp"></div>'); // область ролика
				var $jScroll_roller = $('<div class="jScroll_roller"><div class="ru"></div><div class="rm"></div><div class="rb"></div></div>'); // ролик
				
				$jScroll_ar.height(h);
				$jScroll.css({'height':h,'overflow':'hidden'});
				$jScroll.wrap($jScroll_ar); // оборачиваем наш объект новым блоком
				
				$jScroll_top.add($jScroll_bot).width(w); // устанавливаем ширину затуханий
				// позиционируем роллер + высота ролика
				var h_rp = h-16*2; // высота области ролика
				var rp = $jScroll.attr('rp'); // частный случай - отступ ролика
						rp = rp ? rp*1 : 0;
				$jScroll_rp.css({'margin':'16px 0 0 '+(w-10+rp)+'px'}).height(h_rp);
				var rh = (h)*h_rp/H; // высота ролика
				$jScroll_roller.height(rh);
				$jScroll_roller.find('.rm').height(rh-4); // высота центральной части ролика
				$jScroll_rp.append($jScroll_roller);
				// добавляем доп объекты
				$jScroll.before($jScroll_rp).before($jScroll_top).after($jScroll_bot);
				
				$jScroll.get(0).scrollTop = 0;
				
				var step = (H-h+32)/(h_rp-rh);
				var mng = h*5/100;
				var rollerPosTop = 0;
				var rollerStopPos = ($jScroll_rp.height() - $jScroll_roller.height())*1;
				var rollerRange = $jScroll.height()-$jScroll_roller.height();
				
				$jScroll_roller.draggable({
					containment: $jScroll_rp,
					axis: 'y',
					drag: function(event,ui){
						var top_ = ui.position.top;
						if(top_<0)
						{
							$jScroll_roller.css('top',0);
							setTimeout(function(){$jScroll.scrollTop(0)},100);
							return false;
						}
						$jScroll.scrollTop(top_*step);
						rollerPosTop = top_;
					}
				});
				
				$jScroll.mousewheel(function(event,delta){ move(delta); return false; });
				
				function move(delta,mnog)
				{				
					// delta>0?'up':'down')
					mnog = mnog ? mnog : mng;
					
					var bf = $jScroll.scrollTop(); // до
					$jScroll.scrollTop(bf-(delta*mnog));
					var af = $jScroll.scrollTop(); // после
					
					if(af<0) return false;

					// если скролл наверху
					if(!af)
					{
						rollerPosTop = 0;
						$jScroll_roller.css({'top':0});
						return false;
					}
					// если скролл внизу
					else if(bf==af)
					{
						$jScroll_roller.css({'top':rollerStopPos});
						rollerPosTop = rollerStopPos;
						return false;
					}
					
					var top_ = rollerPosTop;
					top_ = top_-(delta*mnog/step);
					
					if(top_<0) top_ = 0;	
					else if(top_ && top_<1) top_ = 1;
					else if(top_>rollerStopPos) top_ = rollerStopPos;
					
					$jScroll_roller.css({'top':top_});
					rollerPosTop = top_;					
				}
				
				//return $jScroll;
							
			});			
		},
		/* ---------------- ПЕРЕРИСОВКА -------------------- */
		redraw : function(){
			
			$jScroll = $(this); // <div class="jScroll" h="">
			
			var $jScroll_ar = $jScroll.parents('.jScroll_ar:first'); // обёртка объекта
			if(!$jScroll_ar.size())
			{
				jScroll_methods.init.call($jScroll);
				return false;
			}
			else
			{
				jScroll_methods.destroy.call($jScroll);
				jScroll_methods.init.call($jScroll);
			}						
						
		},
		/* ---------------- УНИЧТОЖАЕМ -------------------- */
		destroy : function(){
		
			$jScroll = $(this); // <div class="jScroll" h="">
			
			var $jScroll_ar = $jScroll.parents('.jScroll_ar:first'); // обёртка объекта	
			if(!$jScroll_ar.size()) return false;
			
			var $jScroll_top = 		$jScroll_ar.find('.jScroll_top'); // затухание сверху
			var $jScroll_bot = 		$jScroll_ar.find('.jScroll_bot'); // затухание снизу
			var $jScroll_rp = 		$jScroll_ar.find('.jScroll_rp'); // область ролика
			var $jScroll_roller = $jScroll_rp.find('.jScroll_roller'); // ролик
			
			$jScroll_roller.draggable('destroy');			
			$jScroll_top.add($jScroll_bot).add($jScroll_rp).remove();
			$jScroll.unwrap($jScroll_ar).css({'padding':'0','height':'auto'}).unbind('mousewheel');
			$jScroll.find('div.pad').remove();
			
		}
		
	};
	
	$.fn.jScroll = function(method){
		if(jScroll_methods[method]) {
			return jScroll_methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if (typeof method === 'object' || !method) {
			return jScroll_methods.init.apply(this,arguments);
		}/* else {
			$.error('Метод'+method+' в jQuery.jPop не существует');
		}*/
	};
	
})(jQuery);