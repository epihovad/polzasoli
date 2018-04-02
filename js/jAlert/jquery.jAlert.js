/*
jAlert - собственный плагин (v1.0).
Создан с целью замены стандартных диалоговых окон alert и confirm
(в последствии возможно и promt).
*/

(function($){
	
	/* ----------------------- ОБЪЕКТЫ ----------------------- */
	var $jA_wind = {}, $jA_wind_shadow = {}, $jA_btn_ok = {}, $jA_btn_yes = {}, $jA_btn_no = {}, $jA_blackout = {};
	
	/* ----------------------- ПЕРЕМЕННЫЕ ----------------------- */
	var jA_blackout; /* затемнение */
	var jA_blackout_params; /* параметры затемнения */
	var ie;
	
	var jA_methods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ -------------------- */
		init : function(){

			jQuery.fn.preloadImg('/inc/advanced/jAlert/jAlert.png');
			
			$jA_wind = $('<div id="jAlert"></div>'); // окно
			$jA_wind_shadow = $('<div id="jAlert_shadow"></div>'); // тень от окна (только для IE)
			$jA_btn_ok = $('<a href="" class="ok inline"></a>'); // кнопка OK
			$jA_btn_yes = $('<a href="" class="yes inline"></a>'); // кнопка YES
			$jA_btn_no = $('<a href="" class="no inline"></a>'); // кнопка NO
			
			$jA_wind.prepend('<div class="info_place"></div><div class="btn_place" align="center"></div>');

			$('body').prepend($jA_wind);
			
			ie = $.browser.msie ? parseInt($.browser.version) : false;
			if(ie>9) ie = false;
			
			if(ie)
				$('body').prepend($jA_wind_shadow);

			$(window).add('body, #jAlert, #jAlert *').bind('keyup',function(e){
				if(!$jA_wind.is(':visible')) return false;
				if(!$jA_wind.find('a.ok').length) return false;
				var code = e.keyCode || e.which;
				if(e.keyCode==27)
					jA_methods.hide.call();
			});
				
			$(window).resize(function(){ reposjAlert() });

			$(document).mousewheel(function(event){
				if($jA_wind.is(':visible'))
					event.preventDefault();
			});
			
			function reposjAlert()
			{
				var jAlertLeft = Math.round( ($('body').width()/2) - ($jA_wind.width()/2) + $(window).scrollLeft() );
				var jAlertTop = Math.round( ($('body').height()/2) - ($jA_wind.height()/2) + $(window).scrollTop() );
				
				// окно
				$jA_wind.css({
					'left' : jAlertLeft,
					'top' : jAlertTop
				});
				// тень от окна
				$jA_wind_shadow.css({
					'left' : jAlertLeft-4,
					'top' : jAlertTop-4
				});
			}
		},
		
		show : function(type,text,func,prm){
			if(!$jA_wind.length) jA_methods.init.call();
			if($jA_wind.is(':visible')) jA_methods.hide.call();
			
			prm = $.extend({
				ws : true, // с применением тени
				ws_params : {bg:'',color:'#000',opacity:70,z:zmax?zmax:999}, // параметры тени
				z : zmax?zmax+1:1000, // z-index окна
				b_alert : 'Ok',
				b_confirm : {b1:'Да',b2:'Нет'},
				callbackNo : function(){}
			}, prm);
			
			jA_blackout = prm.ws;
			jA_blackout_params = prm.ws_params;
			
			var windW, windH, windL, windT;
			
			$jA_wind.find('.info_place').html(text);
			
			// добавляем кнопки
			if(type=='alert')
			{
				$jA_btn_ok.html(prm.b_alert);
				$jA_wind.find('.btn_place').prepend($jA_btn_ok);
				$jA_btn_ok.click(function(){ if(func) func.call(); jA_methods.hide.call(); return false; });
			}
			else if (type=='confirm')
			{
				$jA_btn_yes.html(prm.b_confirm.b1);
				$jA_btn_no.html(prm.b_confirm.b2);
				$jA_wind.find('.btn_place').prepend($jA_btn_no).prepend($jA_btn_yes);
				$jA_btn_yes.click(function(){ if(func) func.call(); jA_methods.hide.call(); return false; });
				$jA_btn_no.click(function(){ if(prm.callbackNo) prm.callbackNo.call(); jA_methods.hide.call(); return false; });
			}
			
			if(ie)
			{
				if($.browser.version<'9.0')
					$jA_wind.find('.btn_place').width($jA_wind.width());
			}
			
			windW = $jA_wind.width();
			windH = $jA_wind.height();
			windL = Math.round( ($('body').width()/2) - (windW/2) + $(window).scrollLeft() );
			windT = Math.round( ($('body').height()/2) - (windH/2) + $(window).scrollTop() );
			
			$jA_wind.css({
				'left' : windL,
				'top' : windT,
				'z-index': zmax?(zmax+1):prm.z
			});
			
			if(jA_blackout)
				$jA_blackout = $(document).jB('show',jA_blackout_params);
			$jA_wind.fadeIn();
			if(ie && $.browser.version<'9.0')
			{
				$jA_wind_shadow.css({
					'width' : windW,
					'height' : windH,
					'left' : windL-4,
					'top' : windT-4,
					'z-index'  :prm.z-1
				}).fadeIn();
			}
			$jA_wind.find('.btn_place a:first').focus();
			if(zmax) zmax += 2;
		},
		
		hide : function(){
			$jA_wind.add($jA_wind_shadow).hide();
			$jA_wind.find('.btn_place').html('');
			if(jA_blackout)
				$jA_blackout.jB('hide');
		}
	};
	
	/* ---------------- ПЛАГИН jAlert -------------------- */
	$.fn.jAlert = function(method){
		// логика вызова метода
		if(jA_methods[method]) {
			return jA_methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if (typeof method === 'object' || !method) {
			return jA_methods.init.apply(this,arguments);
		}/* else {
			$.error('Метод'+method+' в jQuery.jAlert не существует');
		}*/
	};
	
})(jQuery);