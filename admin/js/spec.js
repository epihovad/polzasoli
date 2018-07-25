$(function(){

  MainMenu();
  MarkChangeRow();
  Filters();
  Search();
  CKEditor();
  Blueimp();
  FlagStatus();
  TableListSortable();
  Gimg();
  TransformInputs();

  //
  $('thead :checkbox[name=del]').click(function(){
    $('tbody :checkbox[name^=del]').prop('checked',$(this).prop('checked'));
  });

  // обновление значения в списке
  $('span.lval').dblclick(function(){
    var $input = $(this).next('input');
    if(!$input.length) return false;
    $(this).hide();
    $input.show().focus().select();
  });
  $('input.lpole').blur(function(){
    var $this = $(this);
    var $span = $this.prev('span');
    var v = $this.val();
    $.getJSON('inc/actions.php?action=update&id='+$this.attr('oid')+'&tbl='+$this.attr('tbl')+'&field='+$this.attr('name')+'&val='+v,function(data){
      $.each(data,function(key,val){
        if(key=='invalidVal') { alert(val); $this.focus(); }
        else
        {
          $span.html(val);
          $this.hide();
          $span.show();
        }
      });
    });
  });

  //
  $.scrollUp({
    scrollName: 'scrollUp', // Element ID
    scrollDistance: 180, // Distance from top/bottom before showing element (px)
    scrollFrom: 'top', // 'top' or 'bottom'
    scrollSpeed: 300, // Speed back to top (ms)
    easingType: 'linear', // Scroll to top easing (see http://easings.net/)
    animation: 'fade', // Fade, slide, none
    animationSpeed: 200, // Animation in speed (ms)
    scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
    //scrollTarget: false, // Set a custom target element for scrolling to the top
    scrollText: '<i class="fa fa-chevron-up"></i>', // Text for element, can contain HTML // Text for element, can contain HTML
    scrollTitle: false, // Set a custom <a> title if required.
    scrollImg: false, // Set true to use image
    activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
    zIndex: 2147483647 // Z-Index for the overlay
  });
});

function MainMenu() {
  //
  $('#menu > ul > li > a').click(function() {
    $('#menu li').removeClass('active');
    $(this).closest('li').addClass('active');
    var checkElement = $(this).next();
    if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
      $(this).closest('li').removeClass('active');
      checkElement.slideUp('normal');
    }
    if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
      $('#menu ul ul:visible').slideUp('normal');
      checkElement.slideDown('normal');
    }
    if($(this).closest('li').find('ul').children().length == 0) {
      return true;
    } else {
      return false;
    }
  });
  //
  var s = 0;
  $('.menu-toggle').click(function() {
    if (s == 0) {
      s = 1;
      $( "#sidebar" ).animate({left: "-230px"}, 100 );
      $('.dashboard-wrapper').animate({'margin-left': "0px"}, 100);
    } else {
      s = 0;
      $('#sidebar').animate({left: "0px"}, 100);
      $('.dashboard-wrapper').animate({'margin-left': "230px"}, 100);
    }
    return false;
  });
}

function MarkChangeRow() {
  var parsedUrl = new URL(window.location.href);
  var id = parsedUrl.searchParams.get('id');
  if(id == undefined){
    return;
  }
  $('tr#item-'+id).addClass('active');
}

function Filters() {

  var $filters = $('#filters .fbody');
  var $sh = $('#filters h4 a');

  $sh.click(function () {
    if($sh.hasClass('active')){
      $sh.removeClass('active');
      $filters.slideUp('normal');
    } else {
      $sh.addClass('active');
      $filters.slideDown('normal');
    }
    return false;
  });
}

function Search(){
  var $sblock = $('#filters .search');
  var $field = $sblock.find('input');
  var $btn = $sblock.find('button');
  if(!$field.length || !$btn.length) return false;

  $field.keydown(function(e){
    var code = e.keyCode || e.which;
    if(code==13) {
      changeURI({'fl[search]': $field.val()});
    }
  });
  $btn.click(function(){ changeURI({'fl[search]': $field.val()}); return false; });
}

function CKEditor() {
  // CKEditor
  var CKtoolbar = {
    basic : {
      toolbarGroups: [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
        {name: 'forms', groups: ['forms']},
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'styles', groups: ['styles']},
        {name: 'links', groups: ['links']},
        {name: 'insert', groups: ['insert']},
        '/',
        {name: 'colors', groups: ['colors']},
        {name: 'tools', groups: ['tools']},
        {name: 'others', groups: ['others']},
        {name: 'about', groups: ['about']}
      ],
      removeButtons : 'Save,NewPage,Preview,Print,Templates,SelectAll,Find,Replace,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,RemoveFormat,CopyFormatting,Flash,HorizontalRule,Smiley,PageBreak,Iframe,SpecialChar,Table,Image,Anchor,Unlink,Link,TextColor,BGColor,Maximize,ShowBlocks,About,Blockquote,CreateDiv,BidiLtr,BidiRtl,JustifyCenter,Language,Font,Styles,Source',
    },
    full : {
      toolbarGroups: [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
        {name: 'forms', groups: ['forms']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'links', groups: ['links']},
        {name: 'insert', groups: ['insert']},
        '/',
        {name: 'styles', groups: ['styles']},
        {name: 'colors', groups: ['colors']},
        {name: 'tools', groups: ['tools']},
        {name: 'others', groups: ['others']},
        {name: 'about', groups: ['about']}
      ],
      removeButtons : 'Save,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,About',
    }
  };
  // ПРИМЕР: <textarea name="text" toolbar="basic" rows="10"><?=$row['text']?></textarea>
  $('textarea[toolbar]').each(function () {
    this.id = this.name;
    var toolbar = $(this).attr('toolbar');
    var CKEditor = CKEDITOR.replace(
      this.id,
      {
        toolbar: toolbar,
        //contentsCss: '/css/CK.css?v=20180414',
        coreStyles_bold: { element : 'b' },
        coreStyles_italic: { element : 'i' },
        toolbarGroups : CKtoolbar[toolbar].toolbarGroups,
        removeButtons : CKtoolbar[toolbar].removeButtons,
      }
    );
    CKFinder.setupCKEditor(CKEditor, '/js/ckfinder/');
  });
}

function Blueimp() {
  $('a.blueimp').click(function () {
    var $a = $(this);
    blueimp.Gallery($a, {});
    return false;
  });
}

// флажки-статусы
function FlagStatus() {
  //$.preloadImg('img/loader.gif');
  $('.flag').click(function(){
    loader(true);

    var $flag = $(this);
    var active = $flag.hasClass('active');
    var new_alt = !active ? 'активно' : 'заблокировано';
    var new_title = !active ? 'заблокировать' : 'активировать';

    var $script = $flag.next('input');
    var _script = $script.val();
    var _link = $script.next('input').val();

    $.ajax({
      type: "GET",
      url: _script,
      data: _link,
      success: function(data){
        if(data)
        {
          if(data=='reload')
            top.topReload();
          else
            alert(data);
        }
        else
        {
          $flag.attr({'alt':new_alt,'title':new_title});
          if(!active){
            $flag.addClass('active');
          } else {
            $flag.removeClass('active');
          }
          $flag.parents('tr:first').find('td,th').effect("highlight", {}, 1000);
        }
        loader(false);
      }
    });
  });
}

// сортировка строк
function TableListSortable() {
  $('table.table-list tbody').sortable({
    helper: fixWidthHelper,
    axis: 'y',
    /*containment: 'parent',*/
    cursor: 'move',
    handle: '.fa-sort',
    start: function(event, ui){
      /*var id = ui.item.attr('oid');
      var $dragged = $(this).find('tr[par="'+id+'"]');
      $dragged.appendTo(ui.item);*/
    },
    stop: function(event, ui){

    },
    update: function (event, ui) {

      var $sortable = $(this);
      var tbl = $sortable.parents('table.table-list').attr('tbl');

      var cur = { 'id' : ui.item[0].attributes.oid.value, 'par' : ui.item[0].attributes.par.value, };
      var prev = { 'id' : 0, 'par' : 0, 'has_childs' : false, };
      var next = { 'id' : 0, 'par' : 0, 'has_childs' : false, };
      try {
        prev = {
          'id' : ui.item[0].previousElementSibling.attributes.oid.value,
          'par' : ui.item[0].previousElementSibling.attributes.par.value,
          'has_childs' : strpos(ui.item[0].previousElementSibling.className, 'has-childs') !== false,
        };
      } catch (e){}
      try {
        next = {
          'id' : ui.item[0].nextElementSibling.attributes.oid.value,
          'par' : ui.item[0].nextElementSibling.attributes.par.value,
          'has_childs' : strpos(ui.item[0].nextElementSibling.className, 'has-childs') !== false,
        };
      } catch (e){}

      // допускается ли перемещение
      if(cur.par != prev.par && cur.par != next.par){
        $sortable.sortable('cancel');
        //$(document).jAlert('show','alert','сортировать строки возможно лишь в рамках одного уровня');
        return ui;
      }
      if(cur.par == prev.par && prev.has_childs){
        $sortable.sortable('cancel');
        //$(document).jAlert('show','alert','сортировать строки возможно лишь в рамках одного уровня');
        return ui;
      }

      var childs = [];
      getTree(cur.id, childs);
      var $tree = ui.item;
      var $last = ui.item;
      childs.forEach(function($e) {
        $tree = $tree.add($e);
        $e.detach().insertAfter($last);
        $last = $e;
      });

      $tree.effect("highlight", {}, 1000);

      var data = $sortable.sortable('serialize');
      $.ajax({
        data: data,
        type: 'POST',
        //url: window.location.pathname + '?action=sort',
        url: '/admin/inc/actions.php?action=sort&tbl=' + tbl,
        complete: function(data,status){
          if(data.responseJSON.status != 'ok'){
            $sortable.sortable('cancel');
            $(document).jAlert('show', 'alert', data.responseJSON.message);
            return;
          }
          var i=1;
          $('table.table-list tbody tr').each(function () {
            $(this).find('th').eq(1).html(i++);
          });
        }
      });
    }
  });
}
function fixWidthHelper(event, ui) {
  ui.children().each(function() {
    $(this).width($(this).width());
  });
  return ui;
}
function getTree (id, arr) {
  var $ch = $('tr[par="' + id + '"');
  $ch.each(function () {
    arr.push($(this));
    getTree($(this).attr('oid'), arr);
  });
  return arr;
}

// загрузка изображений
function Gimg() {

  $('.gimg').find('.add .i2 a').on('click',function(){
    var $block = $(this).parents('div.gimg:first');
    var count = $block.attr('count')-1;
    var name = $block.attr('name');

    var $glist = $block.find('.glist');
    var n = $glist.find('.add').length;

    // убираем у предыдущего поля "ещё"
    var $last = $glist.find('.add').eq(n-1);
    $last.find('.i2').remove();

    var data  = '<div class="add">';
    data += '	<div class="i1"><input type="file" name="'+name+'[]"></div>';
    if(n<count)
      data += '	<div class="i2"><a href="" title="добавить">ещё</a></div>';
    data += '</div>';
    $last.after(data);

    return false;
  });
}

function TransformInputs() {
  //
  $('select.chosen').each(function () {
    $(this).chosen({
      no_results_text: "Нет данных по запросу",
    });
  });

  // календарь
  if($('.datepicker').length)
  {
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    $('.datepicker').datepicker();
  }
}

function setFilters(){
  var $filters = $('#filters');
  var URI = url();

  $filters.find('select').each(function () {
    var name = this.name;
    var obj = {};
    var vals = '';
    $(this).find(':selected').each(function () {
      if(this.value && this.value != 'null'){
        vals += (vals ? ',' : '') + this.value;
      }
    });
    obj[name] = vals ? vals : 'null';
    URI = changeURI(obj, URI, 1);
  });

  $filters.find('input[type=text]').each(function () {
    var name = this.name;
    var obj = {};
    var val = this.value;
    obj[name] = val ? val : 'null';
    URI = changeURI(obj, URI, 1);
  });

  top.location.href = URI;
}

// changeURI({'fl[sort][link]':'asc'});
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
      // индивидуальная обработка для сортировки
      if(key == 'fl[sort]'){
        // удаляем из QueryURI все сортировки
        $.each(QueryURI, function(k, v) {
          if(strpos(k, 'fl[sort]') !== false){
            delete QueryURI[k];
          }
        });
        return true;
      }
      var m = key.match(/fl\[sort\]\[([^\]]*)/);
      if(m){
        // удаляем из QueryURI все сортировки
        $.each(QueryURI, function(k, v) {
          if(strpos(k, 'fl[sort]') !== false){
            delete QueryURI[k];
          }
        });
        key = 'fl[sort][' + m[1] + ']';
      }
      QueryURI[key] = val;
    });
    // новый URL
    var i=0;
    $.each(QueryURI, function(key, val) {
      if(val == null || val == 'null'){
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

  //console.log(NewURI);
  //return false;

  if(returnNewURI == undefined){
    top.document.location = NewURI;
  } else {
    return NewURI;
  }
}

function SaveAll(href, confirm, check_checked) {
  var $frm = $('#ftl');
  // отмечены ли чекбоксы
  if(check_checked != undefined){
    if($frm.find('tbody :checkbox[name^=del]:checked').length == 0){
      $(document).jAlert('show', 'alert', 'Для действия выберите хотя бы один объект');
      return false;
    }
  }
  // Уверены?
  if(confirm != undefined){
    $(document).jAlert('show', 'confirm', 'Уверены?', function () {
      $frm.attr('action',href);
      $frm.submit();
    });
  } else {
    $frm.attr('action',href);
    $frm.submit();
  }
}

function check_settings_frm(){
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

function jPop(url) {
  jQuery.arcticmodal({
    type: 'ajax',
    url: url,
    ajax: {
      type:'GET',
      cache: false,
      success:function(data, el, responce){
        data.body.html(jQuery('<div class="box-modal"><div class="box-modal_close arcticmodal-close glyphicon glyphicon-remove"></div>' + responce + '</div>'));
      }
    }
  });
}