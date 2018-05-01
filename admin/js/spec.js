function changeURI(object, URI, returnNewURI)
{
	if(URI == undefined) {
    URI = url();
  }
	var NewURI = url('path', URI);
  var QueryURI = url('?', URI);

  if(QueryURI != undefined){

    // замена
    $.each(object, function(key, val) {
      QueryURI[key] = val;
    });
    // новый URL
    var i=0;
    $.each(QueryURI, function(key, val) {
      if(val == null){
        delete QueryURI[key];
        return true;
      }
      NewURI += (!i ? '?' : '&') + key + (val ? '=' + val : '');
      i++;
    });
	} else {
    var i=0;
    $.each(object, function(key, val) {
      NewURI += (!i ? '?' : '&') + key + (val ? '=' + val : '');
      i++;
    });
	}

  //console.log(newURI);

  if(returnNewURI == undefined){
  	top.document.location = NewURI;
	} else {
  	return NewURI;
	}
}

function RegSessionSort(url,filter)
{
	toajax('inc/session_sort.php?'+filter+'&location='+url);
}

function saveall($frm)
{
	if($frm==undefined) $frm = $('form[name="red_frm"]');
	if(!$frm.length) return false;
	$frm.attr('action','?action=saveall').submit();
}

function multidel(form,name,href)
{
	var num = 0;
	var size = name.length;
	var elements = document.getElementsByTagName('input');
	
	for(var i=0; i<elements.length; i++){
		if ((elements[i].type=='checkbox')&&(elements[i].id.substr(0,size)==name)&&(elements[i].checked==true)){
			num++;
		}
	}
	
	if(num==0) {
    $(document).jAlert('show', 'alert', 'Для удаления выберите хотя бы один объект');
  } else {
    $(document).jAlert('show', 'confirm', 'Уверены?', function () {
      if(href) {
        form.action = href + '&action=multidel';
      } else {
        form.action = '?action=multidel';
      }
      form.submit();
    });
	}
}

function multimail(form,name)
{
	var num = 0;
	var size = name.length;
	var elements = document.getElementsByTagName('input');
	
	for(var i=0; i<elements.length; i++){
		if ((elements[i].type=='checkbox')&&(elements[i].id.substr(0,size)==name)&&(elements[i].checked==true)){
			num++;
		}
	}
	
	if(num==0){
    $(document).jAlert('show','alert','Для рассылки выберите хотя бы один объект');
	} else {
		if(confirm('Уверены?')){
			form.action = '?action=multimail';
			form.submit();
		}
	}
}

function check_settings_frm()
{
	var elements = document.frm.getElementsByTagName('input');
	var flag=0;
	for(var i=0; i<elements.length; i++)
	{
		if ((elements[i].type=='text')&&(elements[i].name.substr(0,6)=='count_'))
			if(isNaN(elements[i].value)) /* если не число */
				flag++;
			else
				if(elements[i].value*1<1)
					flag++;
	}
	if(flag==0)
		document.frm.submit();
	else
		alert('Ошибка ввода! Поля, указывающие количество элементов отображаемых на странице, должны иметь числовое значение больше 0');
}

function save_red_detail_frm()
{
	var obj = document.red_detail_frm.id_cat;
	var cur_color = obj.options[obj.selectedIndex].style.color.toString();
	
	if(cur_color=='rgb(255, 51, 0)' || cur_color=='#ff3300')
		alert('невено выбрана категория товара ! (см. подсказку)');
	else
	{
		$('#red_detail_frm').attr('action','goods.php?part=details&action=save&id='+$('#cur_id').val());
		$('#red_detail_frm').submit();
	}	
}

function del_options(list,color)
{
	list.find('option:selected').each(function(){
		if(color)
		{
			cc = this.style.color;
			if(cc!='rgb(153, 153, 153)' && cc!='999999')
				$(this).remove();
		}
		else
			$(this).remove();
	})
}

var $popup_window;
var $jB;

function show_popup_window(title,href)
{
	$popup_window = $('#popup_window'); // окно
	var $popup_frame = $popup_window.find('#popup_frame'); // frame окна
	var $popup_window_title = $popup_window.find('#popup_window_title'); // заголовок окна
	var $popup_loader = $popup_window.find('#popup_loader'); // индикатор загрузки
		
	var $body = $('body');
	var bs = BodySize();
	
	// затемнение
	$jB = $(document).jB('show',{opacity:50});
	// загружаем контент окна
	$popup_frame.attr('src',href);
	// когда контент загружен
	$popup_frame.bind('load',function(){ $popup_loader.hide(); $popup_frame.customFadeIn(300); });
	// заголовок окна
	$popup_window_title.html(title); 
	// отображаем окно
	$popup_window.show();
	// отображаем loader
	$popup_loader.show();
	// позиция окна
	$popup_window.css('height',Math.round(bs.height*0.7));
	rs_popup_window();
	//$popup_window.css("top",Math.round((bs.height/2) - ($popup_window.height()/2) + $body.scrollTop()));
	//$popup_window.css("left",Math.round((bs.width/2) - ($popup_window.width()/2)));
	// позиция loader'а
	$popup_loader.css({
		'top'		: Math.round( ($popup_window.height()/2)-($popup_loader.height()/2) ),
		'left'	: Math.round( ($popup_window.width()/2)-($popup_loader.width()/2) )
	});
	
	$(window).resize(function(){ rs_popup_window() });
	
	$(document).mousewheel(function(event){
		if($popup_window.is(':visible'))
		{
			if(!$(event.currentTarget).parents($popup_window).length)
				event.preventDefault();
		}
	});
}
function rs_popup_window()
{
	ss = screenSize();
	posLeft = Math.round( (ss.w/2) - ($popup_window.width()/2) );
	posTop = Math.round( (ss.h/2) - ($popup_window.height()/2) + $(window).scrollTop() );
	
	$popup_window.css({
		'left' : posLeft,
		'top' : posTop
	});
}
function hide_popup_window()
{
	var $popup_window = $('#popup_window'); // окно
	var $popup_frame = $popup_window.find('#popup_frame'); // frame окна
	
	$jB.jB('hide');
	$popup_window.hide();
	$popup_frame.unbind("load");
	$popup_frame.hide();
	$popup_frame.attr('src','');
}