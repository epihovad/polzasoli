(function($){
	
	// ------------- глобальные объекты ------------
	var $jImbox = {
			objs : '', // объекты (все <a class="jImbox">)
			o : '', // текуший объект
			box : '', // окно
			im : '', // изображение
			bshadow : '', // тень для блока (IE)
			prev : '', // кнопка "предыдущее фото"
			next : '', // кнопка "следующее фото"
			info : '', // описание фото
			mark : '',
			shadow : '', // тень (jB)
			loader : '' // лоадер
	}
	
	var $jImboxVars = {
		path : '/inc/advanced/jImbox/',
		GLimages : [], // url'ы к картнкам
		GLwidth : [], // ширина картинок
		GLheight : [], // высота картинок
		prmInit : [], // начальные параметры
		imkol : 1, // кол-во картинок на странице
		num : 0, // порядковый номер объекта (<a>)
		index : 0, // индекс текущей картинки 
		visible : false, // флаг
		ie : ($.browser.msie && $.browser.version<'9.0'),
		W : 0, H : 0, L : 0, T : 0,
		startW : 0, startH : 0, startL : 0, startT : 0,
		imW : 0, imH : 0
	}
	
	var jImboxMethods = {
		/* ---------------- ИНИЦИАЛИЗАЦИЯ -------------------- */
		init : function(prm){
			
			// параметры плагина
			prm = $.extend({
				z : 1000, // z-index окна
				ws : true, // с применением тени
				ws_params : {opacity:50,z:998} // параметры тени
			}, prm);
			
			$jImboxVars.prmInit = prm;
			
			if($jImbox['box'])
			{
				// метод init уже вызывался
				var $objs = $(this);
				$objs.each(function(){
					var $this = this;
					var f = false;
					$jImbox['objs'].each(function(){
						if(this==$this) { f = true; return false;	}
					});
					if(!f)
					{
						$($this).attr('num',$jImboxVars.num++).click(function(){
							$jImbox['o'] = $(this);
							$jImboxVars['index'] = $jImbox['o'].attr('num');
							jImboxMethods.run.call();
							return false;
						});
					}
				});
				return false;
			}
						
			$jImbox['objs'] = $(this);
			$jImbox['objs'].each(function(){
				$(this).attr('num',$jImboxVars.num++);
			});
			$jImboxVars['imkol'] = $jImbox['objs'].size();

			$jImbox['box'] = $('<div id="jImbox" align="center"></div>');
			$jImbox['im'] = $('<img id="jImbox_im" alt="">');
			$jImbox['bshadow'] = $('<div id="jImbox_bshadow"></div>');
			$jImbox['prev'] = $('<a id="jImbox_prev" class="inline" href=""></a>');
			$jImbox['next'] = $('<a id="jImbox_next" class="inline" href=""></a>');
			$jImbox['info'] = $('<div id="jImbox_info"><div></div></div>');
			$jImbox['mark'] = $('<div id="jImbox_mark"></div>');
			$jImbox['loader'] = $('<div id="jImbox_loader"><img src="'+$jImboxVars.path+'img/loader.gif" alt=""></div>');
			
			var $btns = $('<div id="jImbox_btns" align="left"></div>');
			$btns.append($jImbox['prev']).append($jImbox['next']);
			
			$jImbox['box'].prepend($jImbox['im']).prepend($jImbox['info']).prepend($jImbox['mark']).prepend($btns).prepend($jImbox['loader']);
			$('body').prepend($jImbox['box']);
			
			$jImbox['objs'].click(function(){
				$jImbox['o'] = $(this);
				$jImboxVars['index'] = $jImbox['o'].attr('num');
				jImboxMethods.run.call();
				return false;
			});
						
			$(document).add('body, #jImbox, #jImbox *').bind('keyup',function(e){
				if(!$jImboxVars['visible']) return false;
				var code = e.keyCode || e.which;
				if(e.keyCode==37) $jImbox['prev'].click();
				if(e.keyCode==39) $jImbox['next'].click();
				if(e.keyCode==27) jImboxMethods.hide.call();
			});
			
			$jImbox['im'].click(function(){ jImboxMethods.hide.call() });
			
			$(document).click(function(event){
				if($jImbox['box'].is(':visible'))
				{
					if(!$(event.target).parents('#jImbox').size())
						jImboxMethods.hide.call();
				}
			});
			
			$(window).resize(function(){ jImboxMethods.repos.call() });
			
			$(document).mousewheel(function(event){
				if($jImbox['im'].is(':visible'))
					event.preventDefault();
			});
			
		},
		
		run : function(){
			
			if(typeof(zmax)!='undefined')
			{
				$jImboxVars.prmInit.z = zmax+3;
				$jImboxVars.prmInit.ws_params.z = $jImboxVars.prmInit.z-2;
				zmax += 3;
			}
			
			var $o 	= $jImbox['o'].find(':first');
			var pos = $o.offset();
			
			$jImboxVars.startW 	= $o.width();
			$jImboxVars.startH 	= $o.height();
			$jImboxVars.startL 	= pos.left;
			$jImboxVars.startT 	= pos.top;
			
			$jImbox['box'].css({
				'width' : $jImboxVars.startW,
				'height' : $jImboxVars.startH,
				'left' : $jImboxVars.startL,
				'top' : $jImboxVars.startT,
				'z-index' : $jImboxVars.prmInit.z
			});
			if($jImboxVars.ie) $jImbox['bshadow'].css({'z-index':$jImboxVars.prmInit.z-1});
			
			jImboxMethods.setGL.call();
		
		},
		
		setGL : function(){
			
			var resize = typeof(this.resize)!='undefined' ? this.resize : false;
			
			if($jImboxVars.GLimages[$jImboxVars.index]){ setSize(); return false; }

      var im = new Image();
      im.onload = function(){
        var imUrl = $jImbox['o'].attr('href');
        var imW = im.width;
        var imH = im.height;
        if(imUrl && imW && imH)
        {
          $jImboxVars.GLimages[$jImboxVars.index] = imUrl;
          $jImboxVars.GLwidth[$jImboxVars.index] = imW;
          $jImboxVars.GLheight[$jImboxVars.index] = imH;
          setSize();
        }
      }
      $(im).attr('src',$jImbox['o'].attr('href'));
		
			function setSize()
			{
				var dopuskW = $('body').width() - 50;
				var dopuskH = $('body').height() - 50;
				var size = getMinRatioSize([$jImboxVars.GLwidth[$jImboxVars.index],$jImboxVars.GLheight[$jImboxVars.index]],[dopuskW,dopuskH-40]);
				$jImboxVars.imW = size.w;
				$jImboxVars.W = size.w;
				$jImboxVars.imH = size.h;
				$jImboxVars.H = size.h + 40; // +40 - для блока $box_info
				$jImboxVars.L = Math.round( ($('body').width()/2) - ($jImboxVars.W/2) + $(window).scrollLeft() );
				$jImboxVars.T = Math.round( ($('body').height()/2) - ($jImboxVars.H/2) + $(window).scrollTop() );
				
				jImboxMethods.show.call({resize:resize});
			}
			
		},
		
		show : function(){
			
			var prm = $jImboxVars.prmInit;
			var resize = typeof(this.resize)!='undefined' ? this.resize : false;
			
			if(!resize)
			{
				$jImboxVars['visible'] = true;
				
				$jImbox['box'].show();
				if(prm.ws)
					$jImbox['shadow'] = $(document).jB('show',prm.ws_params);
			}
			else
			{
				if($jImboxVars.ie) $jImbox['bshadow'].hide();
				
				var $o 	= $jImbox['o'].find(':first');
				var pos = $o.offset();
				
				$jImboxVars.startW 	= $o.width();
				$jImboxVars.startH 	= $o.height();
				$jImboxVars.startL 	= pos.left;
				$jImboxVars.startT 	= pos.top;
			}
			
			updateInfo();
			
			if($jImboxVars.W < $jImbox['info'].width())
			{
				$jImboxVars.W = $jImbox['info'].width();
				$jImboxVars.L = Math.round( ($('body').width()/2) - ($jImboxVars.W/2) + $(window).scrollLeft() );
				$jImbox['im'].removeClass('rad');
			}
			else
			{
				$jImbox['info'].width($jImboxVars.W);
				$jImbox['im'].addClass('rad');
			}
			
			$jImbox['mark'].css({
				'margin-top' : $jImboxVars.H-$jImbox['mark'].height(),
				'margin-left' : $jImboxVars.W/2-$jImbox['mark'].width()/2
			});
			
			$jImbox['box'].animate({
				'width' : $jImboxVars.W,
				'height' : $jImboxVars.H,
				'left' : $jImboxVars.L,
				'top' : $jImboxVars.T
			},300,function(){
				imLoad();
				if($jImboxVars.ie)
				{
					$jImbox['bshadow'].css({
						'width' : $jImboxVars.W,
						'height' : $jImboxVars.H,
						'left' : $jImboxVars.L-7,
						'top' : $jImboxVars.T-7
					}).show();
				}
			});
			
			function imLoad()
			{
				$jImbox['loader'].css({
					'left' : Math.round( $jImboxVars.W/2 - 31/2 ),
					'top' : Math.round( $jImboxVars.H/2 - 31/2 )
				}).show();
				
				var img = new Image();
				img.onload = function(){
					$jImbox['im'].width($jImboxVars.imW).height($jImboxVars.imH).attr('src',$jImboxVars.GLimages[$jImboxVars['index']]);
					$jImbox['loader'].hide();
					$jImbox['info'].show();
					if($jImbox['mark'].html()!='') $jImbox['mark'].show();
					$jImbox['im'].fadeIn('slow',function(){jImboxMethods.btn.call({flag:true})});
				}
				$(img).attr('src',$jImboxVars.GLimages[$jImboxVars['index']]);
			}
			
			function updateInfo()
			{
				var info = $jImbox['o'].next('div.jImbox').html();
				$jImbox['info'].find('div').html(info);
				var mark = $jImbox['o'].attr('mark');
				$jImbox['mark'].html(mark);
			}
			
		},
		
		repos : function(){
			
			if(!$jImbox['box'].size() || !$jImbox['box'].is(':visible')) return false;
			
			var Left = Math.round( ($('body').width()/2) - ($jImbox['box'].width()/2) + $(window).scrollLeft() );
			var Top = Math.round( ($('body').height()/2) - ($jImbox['box'].height()/2) + $(window).scrollTop() );
			
			// блок
			$jImbox['box'].css({'left':Left,'top':Top});
			// тень блока
			if($jImboxVars.ie) 
				$jImbox['bshadow'].css({'left':boxposLeft-7,'top':boxposTop-7});
							
		},
		
		btn : function(){
			
			if(this.flag)
			{	
				var gr = $jImbox['o'].attr('gr');
						gr = typeof(gr)=='undefined' ? '' : gr;
				var n = 0;
				var curN = 0;
				var group = [];
				$jImbox['objs'].each(function(){
					if($(this).attr('gr')!=gr) return true;
					group[n] = $(this);
					if(this==$jImbox['o'][0]) curN = n;
					n++;
				});
				
				if($jImboxVars['imkol']==1 || !gr || n<2) return false;		
					
				var T = Math.round( $jImboxVars.H/2 - $jImbox['prev'].height()/2 );
				var L = Math.round( $jImboxVars.W - $jImbox['prev'].width() );
				$jImbox['prev'].css('margin',T+'px 0 0 0');
				$jImbox['next'].css('margin',T+'px 0 0 '+L+'px');
				
				var $prev = group[curN-1];
				var $next = group[curN+1];				
				
				if(curN==0) $jImbox['prev'].addClass('na').unbind('click').bind('click',function(){ return false });
				else $jImbox['prev'].removeClass('na').bind('click',function(){ $(this).hide(); jImboxMethods.imslide.call({obj:$prev}); return false; });
				
				if(curN==group.length-1) $jImbox['next'].addClass('na').unbind('click').bind('click',function(){ return false });
				else $jImbox['next'].removeClass('na').bind('click',function(){ $(this).hide(); jImboxMethods.imslide.call({obj:$next}); return false; });
				
				$jImbox['prev'].add($jImbox['next']).show();
				$jImbox['box'].mouseover(function(){$jImbox['prev'].add($jImbox['next']).show()});
				$jImbox['box'].mouseout(function(){$jImbox['prev'].add($jImbox['next']).hide()});
			}
			else
			{
				$jImbox['prev'].add($jImbox['next']).hide().removeClass('na').unbind('click').bind('click',function(){ return false });
				$jImbox['box'].unbind('mouseover').unbind('mouseout');
			}
			
		},
		
		imslide : function(){
			
			jImboxMethods.btn.call({flag:false});
			$jImbox['info'].width('auto').hide();
			$jImbox['mark'].hide();
			$jImbox['im'].hide();
			
			$jImbox['o'] = this.obj;
			$jImboxVars['index'] = $jImbox['o'].attr('num');
			jImboxMethods.setGL.call({resize:true});
			
		},
		
		hide : function(){
		
			if(!$jImbox['box'] || !$jImboxVars['visible']) return false;
			
			if($jImboxVars.ie) $jImbox['bshadow'].hide();
			jImboxMethods.btn.call({flag:false});
			$jImbox['box'].animate({
				'width' : $jImboxVars.startW,
				'height' : $jImboxVars.startH,
				'left' : $jImboxVars.startL,
				'top' : $jImboxVars.startT
			},300,function(){
				$(this).hide();
				$jImbox['im'].hide();
				$jImbox['shadow'].jB('hide');
				$jImbox['info'].width('auto').hide();
				$jImbox['mark'].hide();
				$jImboxVars['visible'] = false;
			});
			
		}
	}
	
	$.fn.jImbox = function(method){
		if(jImboxMethods[method])
			return jImboxMethods[method].apply(this,Array.prototype.slice.call(arguments,1));
		else if(typeof method === 'object' || !method)
			return jImboxMethods.init.apply(this,arguments);
	};
	
})(jQuery);

$(function(){ $('a.jImbox').jImbox() });