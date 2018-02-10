var nav = userNavigator();

// ОТПРАВКА ДАННЫХ ВО ФРЕЙМ
function toajax(url)
{
	jQuery('#ajax').attr('src',url);
}

// ОТПРАВКА ДАННЫХ ЧЕРЕЗ AJAX
function inajax(script,url)
{
	jQuery.ajax({type:'GET',url:script,data:url,success:function(data){data=str_replace('<script>','',data);data=str_replace('</script>','',data);eval(data);}});
}

function loader(show)
{
	var $loader = $('#loader');
	if($loader.size())
	{
		if(show)
		{
			var bs = BodySize();
			$loader.css({
				'top'	: Math.round( (bs.height/2)-($loader.height()/2)+$('body').scrollTop() ),
				'left': Math.round( (bs.width/2)-($loader.width()/2) )
			});
			$loader.show();
		}
		else
			$loader.hide();
	}
}

// ПЕРЕЗАГРУЗИТЬ СТРАНИЦУ ПОСЛЕ РАБОТЫ ФРЕЙМА
function topReload()
{
	switch(userNavigator())
	{
		case "isOpera":
		case "isChrome":
			history.go(0);
			break;
		
		case "isGecko":
			history.back();
			setTimeout("top.location.reload(true)",500);
			break;
		
		default:
			history.back();
			history.go(0);
			break;
	}
}
// ВЫЗОВ ФУНКЦИИ history.back() ПОСЛЕ РАБОТЫ ФРЕЙМА
function topBack(post) // post - страница дергалась формой (иначе - ссылкой)
{
	showLoad(false);
	switch(userNavigator())
	{
		case "isChrome":
			if(post)
				history.back();
			break;
		
		default:
			history.back();
			break;
	}
}

// ОПРЕДЕЛЕНИЕ ТИПА БРАУЗЕРА
function userNavigator()
{
	// Получим userAgent браузера и переведем его в нижний регистр 
	var ua = navigator.userAgent.toLowerCase(); 
	// Определим Internet Explorer 
	if( (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1) )
		return "isIE";
	// Opera 
	if( (ua.indexOf("opera") != -1) )
		return "isOpera";
	// Chrome
	if( (ua.indexOf("chrome") != -1) ) 
		return "isChrome";
	// Gecko = Mozilla + Firefox + Netscape 
	if( (ua.indexOf("gecko") != -1) ) 
		return "isGecko";
	// Safari, используется в MAC OS 
	if( (ua.indexOf("safari") != -1) ) 
		return "isSafari";
	// Konqueror, используется в UNIX-системах 
	if( (ua.indexOf("konqueror") != -1) ) 
		return "isKonqueror";

	return false;
}

// ПРЕДВАРИТЕЛЬНАЯ ЗАГРУЗКА КАРТИНОК
// в аргументы передаются пути к картинкам
jQuery.fn.preloadImg = function(){
	for(var i=0; i<arguments.length; i++)
		jQuery("<img>").attr("src", arguments[i]);
};

// ФОРМАТИРУЕТ ВЫВОД ЧИСЛА, АНАЛОГ number_format() В PHP
function number_format(number, decimals, dec_point, thousands_sep) 
{
	var n = number, prec = decimals, dec = dec_point, sep = thousands_sep;
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	sep = sep == undefined ? ',' : sep;
	
	var s = n.toFixed(prec), abs = Math.abs(n).toFixed(prec), _, i;
	if (abs > 1000) {
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;
		_[0] = s.slice(0,i + (n < 0)) + _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
		s = _.join(dec || '.');
	}
	return s;
}

function end(array) 
{
	var last_elm, key;

	if (array.constructor === Array){
		last_elm = array[(array.length-1)];
	} else {
		for (key in array){
			last_elm = array[key];
		}
	}
	return last_elm;
}

// strpos('Kevin van Zonneveld', 'e', 5); -> 14
function strpos(haystack,needle,offset)
{
	var i = haystack.indexOf(needle,offset); // returns -1
	return i >= 0 ? i : false;
}
function strrev(string)
{
	var ret = '', i = 0;
	for(i=string.length-1; i>=0; i--)
	{
	   ret += string.charAt(i);
	}
	return ret;
}
function str_replace ( search, replace, subject )
{
	if(!(replace instanceof Array)){
		replace=new Array(replace);
		if(search instanceof Array){
			while(search.length>replace.length){
				replace[replace.length]=replace[0];
			}
		}
	}

	if(!(search instanceof Array))search=new Array(search);
	while(search.length>replace.length){
		replace[replace.length]='';
	}

	if(subject instanceof Array){
		for(k in subject){
			subject[k]=str_replace(search,replace,subject[k]);
		}
		return subject;
	}

	for(var k=0; k<search.length; k++){
		var i = subject.indexOf(search[k]);
		while(i>-1){
			subject = subject.replace(search[k], replace[k]);
			i = subject.indexOf(search[k],i);
		}
	}

	return subject;
}

// Размеры клиентской части окна браузера
function screenSize() 
{
	var w = (window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.offsetWidth));
	var h = (window.innerHeight ? window.innerHeight : (document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.offsetHeight));
	return {w:w, h:h};
}

function mousePageXY(e)
{
	var x = 0, y = 0;
	if (!e) e = window.event;
	if (e.pageX || e.pageY)
	{
		x = e.pageX;
		y = e.pageY;
	}
	else if (e.clientX || e.clientY)
	{
		x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
		y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
	}
	
	return {"x":x, "y":y};
}

// ОПРЕДЕЛЕНИЕ КООРДИНАТ ЭЛЕМЕНТА
function absPosition(obj) 
{ 
	var x = y = 0;
	while(obj) 
	{
		x += obj.offsetLeft;
		y += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return {x:x, y:y};
	// Пример:
	// "x = " + absPosition(obj).x;
	// "y = " + absPosition(obj).y;
}

function getMinRatioSize(size,sizeto)
{
	width = sizeto[0];
	height = sizeto[1];
	
	if(!width || width > size[0])
		width = size[0];
	if(!height || height > size[1])
		height = size[1];
		
	x_ratio = width / size[0];
	y_ratio = height / size[1];
	
	ratio = Math.min(x_ratio, y_ratio);
	use_x_ratio = (x_ratio == ratio);
	
	width   = use_x_ratio  ? width  : Math.floor(size[0] * ratio);
	height  = !use_x_ratio ? height : Math.floor(size[1] * ratio);
	
	return {w:width, h:height};
}

// Размеры клиентской части окна браузера
function screenSize() 
{
	var w = (window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.offsetWidth));
	var h = (window.innerHeight ? window.innerHeight : (document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.offsetHeight));
	return {w:w, h:h};
}

function getElementPosition(obj)
{
    var w = obj.offsetWidth;
    var h = obj.offsetHeight;
	
    var l = 0;
    var t = 0;
	
    while (obj)
    {
        l += obj.offsetLeft;
        t += obj.offsetTop;
        obj = obj.offsetParent;
    }

    return {"left":l, "top":t, "width": w, "height":h};
}

function BodySize()
{
	return {"width":$('body').width(), "height":$('body').height()};
}

function add_photo()
{
	var sch = $('#sch_photo').val()*1 + 1;
	var tab = $('#tab_add_photo').get(0);
	
	var _tr = tab.insertRow(-1);
	_tr.id = 'tr_photo'+sch;
	
    var cell_1 = top.document.createElement('TD');
	var cell_2 = top.document.createElement('TH');
	
	// убираем + - у предыдущей строки
	var pred_tr = $('#tr_photo'+(sch-1)).get(0);
	if(pred_tr)
	{
		_th = pred_tr.getElementsByTagName('TH');
		_th[0].innerHTML = '';
	}	
	
	// поле файл 
	_tr.appendChild(cell_1);
    cell_1.innerHTML = '<input type="file" name="user_photo['+sch+']" />';
	// + -	
	_tr.appendChild(cell_2);
	cell_2.innerHTML = '<a href="" target="ajax" class="link2" onclick="add_photo();return false;">ещё</a>';
	
	// увеличиваем счетчик
	$('#sch_photo').val(sch);
	
	// убираем + у 5-й строки
	if(sch+1==4) 
	{
		var cur_tr = $('#tr_photo'+sch).get(0);
		if(cur_tr)
		{
			_th = cur_tr.getElementsByTagName('TH');
			_th[0].innerHTML = '';
		}
	}
}

function clear_select(obj,flag)
{
	while(obj.options.length) 
		obj.options[0] = null;
	
	if(flag)
		obj.options[0] = new Option('', '');
}

function rgb2hex(r, g, b) 
{
	return (((r & 255) << 16) + ((g & 255) << 8) + b).toString(16);
}

function hex2rgb(hex) 
{
	return (function (v) {
		return [v >> 16 & 255, v >> 8 & 255, v & 255];
	})(parseInt(hex, 16));
}

function update_captcha()
{
	tmp = new Date();
	$('#captcha').attr('src','/captcha/'+tmp+'/');
}

jQuery.fn.input_fb = function(prm){
	
	// Settings
	prm = $.extend({
		text : 'Ваш текст',
		color_focus : '#007fff',
		color_blur : '#ff0000',
		wIn : 0,
		wOut : 0
	}, prm);
	
	var $obj = $(this);
	prm.wOut = $obj.width();
	b();
	$(this).focus(function(){f()});
	$(this).blur(function(){b()});
	
	function f() { if($obj.val()==prm.text) $obj.val('').css('color',prm.color_focus).width(prm.wIn?prm.wIn:prm.wOut); }
	function b() { if($obj.val()=='') $obj.css('color',prm.color_blur).val(prm.text).width(prm.wOut); }

	return jQuery;
}

jQuery.fn.numer = function(settings){
	
	// Settings
	settings = $.extend({
		nul : true
	}, settings);
	
	return this.each(function() {
		$(this).keypress(function(e){
			if(e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
				return false;    
		});
		
		$(this).change(function(){
			var value = $(this).val();
			if(!settings.nul)
			{
				if(value*1<1)
					$(this).val('1');
				$(this).val(value.replace(/^[0]+/,''));					
			}
			/*else if(value=='') $(this).val('0');*/
		});
	});
}

jQuery.fn.PopWin = function(prm){
	
	// Settings
	prm = $.extend({
		text : '',
		url : '',
		type : 'ok',
		pos : 'center',
		callback: function(){}
	}, prm);
	
	prm.type = prm.type=='' ? 'ok' : prm.type;
	prm.pos = prm.pos=='' ? 'center' : prm.pos;
	prm.callback = prm.callback=='' ? function(){} : prm.callback;
	
	$popwin = $(this);
	$ok = $popwin.find('.btn_ok');
	$close = $popwin.find('.close div');
	
	if(!prm.text)
		initPopWin();
	
	if( (prm.text||prm.url) && prm.type )
	{
		showPopWin();
		$ok.click(prm.callback);
		$close.click(prm.callback);
	}
	
	function initPopWin()
	{
		$ok.hover(function(){$(this).addClass('hover')},function(){$(this).removeClass('hover')});
		$close.hover(function(){$(this).addClass('cur')},function(){$(this).removeClass('cur')});
		$ok.click(function(){hidePopWin()});
		$close.click(function(){hidePopWin()});
	}
	
	function posPopWin()
	{
		if(prm.pos=='center')
		{
			var bs = BodySize();
			$popwin.css('top',Math.round((bs.height/2) - ($popwin.height()/2) + $('body').scrollTop()));
			$popwin.css('left',Math.round((bs.width/2) - ($popwin.width()/2)));
		}
	}
	
	function showPopWin()
	{
		$block = $popwin.find('.type_'+prm.type);
		if($block.size())
		{
			$content = $block.find('.content');
			// добавляем в окно контент
			if(prm.text)
			{
				$content.html(prm.text);
				$block.show();
				posPopWin(); // позиционируем окно
				$popwin.show(); // отображаем окно
			}
			else
			{
				$.ajax({
					type: "GET",
					url: prm.url,
					success: function(data){
						if(data)
						{
							$content.html(data);
							$block.show();
							posPopWin(); // позиционируем окно
							$popwin.show(); // отображаем окно
						}
					}
				});
			}
		}
	}
	
	function hidePopWin()
	{
		$popwin.hide();
		$popwin.find('div[class^="type_"]').hide();
		$popwin.find('div.content').html('');
	}
};

// ОТКРЫВАЕТ СТРАНИЦУ В ОТДЕЛЬНОМ ОКНЕ
function openWindow(width,height,url,target)
{
	/*
	width	размер в пикселах	ширина нового окна
	height	размер в пикселах	высота нового окна
	left	размер в пикселах	абсцисса левого верхнего угла нового окна
	top	размер в пикселах	ордината левого верхнего угла нового окна
	toolbar	1 / 0 / yes / no	вывод панели инструменов
	location	1 / 0 / yes / no	вывод адресной строки
	directories	1 / 0 / yes / no	вывод панели ссылок
	menubar	1 / 0 / yes / no	вывод строки меню
	scrollbars	1 / 0 / yes / no	вывод полос прокрутки
	resizable	1 / 0 / yes / no	возможность изменения размеров окна
	status	1 / 0 / yes / no	вывод строки статуса
	fullscreen	1 / 0 / yes / no	вывод на полный экран
	*/ 
	if(!target) target = 'my';
	var left = Math.round((screen.width-width)/2);
	var top = Math.round((screen.height-height)/2)-40;
	var win = window.open(url, target, 'resizable=yes,width='+width+',height='+height+',scrollbars=1,top='+top+',left='+left);
	win.focus();
	// Пример:
	// <a href="page.htm" target="my" onClick="openWindow(570,700)">открыть</a>
}

// ОТКРЫВАЕТ СТРАНИЦУ В ОТДЕЛЬНОМ ОКНЕ
function openWindow(width,height,url,target)
{
	/*
	width	размер в пикселах	ширина нового окна
	height	размер в пикселах	высота нового окна
	left	размер в пикселах	абсцисса левого верхнего угла нового окна
	top	размер в пикселах	ордината левого верхнего угла нового окна
	toolbar	1 / 0 / yes / no	вывод панели инструменов
	location	1 / 0 / yes / no	вывод адресной строки
	directories	1 / 0 / yes / no	вывод панели ссылок
	menubar	1 / 0 / yes / no	вывод строки меню
	scrollbars	1 / 0 / yes / no	вывод полос прокрутки
	resizable	1 / 0 / yes / no	возможность изменения размеров окна
	status	1 / 0 / yes / no	вывод строки статуса
	fullscreen	1 / 0 / yes / no	вывод на полный экран
	*/ 
	if(!target) target = 'my';
	var left = Math.round((screen.width-width)/2);
	var top = Math.round((screen.height-height)/2)-40;
	var win = window.open(url, target, 'resizable=yes,width='+width+',height='+height+',scrollbars=1,top='+top+',left='+left);
	win.focus();
	// Пример:
	// <a href="page.htm" target="my" onClick="openWindow(570,700)">открыть</a>
}