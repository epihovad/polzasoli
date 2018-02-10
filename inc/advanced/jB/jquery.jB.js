/*
jBlackout - собственный плагин (v1.0).
Затемнение
*/

var jB_num = 1;

(function($){
	
	var jB_methods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ + SHOW -------------------- */
		show : function(prm,callback){
			
			if($('#jB'+jB_num).size())
				jB_methods.destroy.call($('#jB'+jB_num));
			
			// Settings
			prm = $.extend({
				bg : '/inc/advanced/jB/jB.png',
				color : '',
				opacity : 100,
				z : zmax?zmax:1000
			}, prm);
			
			if(prm.opacity>1)
				prm.opacity = parseInt(prm.opacity)*0.01;
			
			var $jB = $('<div id="jB'+(jB_num)+'"></div>');
			$jB.css({
				'position' : 'absolute',
				'left' : '0',
				'top' : '0',
				'padding' : '0',
				'display' : 'none',
				'background' : prm.bg ? 'url('+prm.bg+')' : prm.color,
				'z-index' : prm.z
			}).animate({opacity:prm.opacity},0);
			
			$(window).resize(function(){ jB_methods.repos.call($jB) });
			
			$('body').prepend($jB);
			
			jB_methods.repos.call($jB);
			$jB.fadeIn();
			
			if(callback) callback.call();
			
			jB_num++;
			
			return $jB;
		},
		
		repos : function(){
			
			var $jB = $(this);
			
			$jB.css({
				'width' : $.browser.msie ? $('body').width() : $(document).width(),
				'height' : $(document).height()-($.browser.msie?4:0)
			});
		
		},
		
		hide : function(callback){
			
			var $jB = $(this);
			
			$jB.hide();
			if(callback) callback.call();
			jB_methods.destroy.call($jB);
			
		},
		
		destroy : function(){
		
			$(this).remove();
			
		}
	};
	
	/* ---------------- ПЛАГИН jB -------------------- */
	$.fn.jB = function(method){
		// логика вызова метода
		if(jB_methods[method]) {
			return jB_methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		}/* else if (typeof method === 'object' || !method) {
			return jB_methods.init.apply(this,arguments);
		} else {
			$.error('Метод'+method+' в jQuery.jPop не существует');
		}*/
	};
	
})(jQuery);