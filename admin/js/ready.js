$(function(){
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
	$('#check_del').click(function(){
	  $('input[id^="check_del_"]').not(this).prop('checked',$(this).prop('checked'));
	});
	// check_all
	$('.check_all').click(function(){
		$table = jQuery(this).parents('table:first');
		$tr = jQuery(this).parents('tr:first');
		$th = jQuery(this).parents('th:first');
		tr_index = $tr.index();
		th_index = $th.index();
		checked = jQuery(this).is(':checked');

		$table.find('tr').slice(tr_index+1).each(function(){
			$check = jQuery(this).find('td').eq(th_index).find('input');
			if($check.length)
				$check.prop('checked',checked);
		});
	});
	//
  var s = 0;
  $('.menu-toggle').click(function() {
    if (s == 0) {
      s = 1;
      $( "#sidebar" ).animate({left: "-210px"}, 100 );
      $('.dashboard-wrapper').animate({'margin-left': "0px"}, 100);
    } else {
      s = 0;
      $('#sidebar').animate({left: "0px"}, 100);
      $('.dashboard-wrapper').animate({'margin-left': "210px"}, 100);
    }
  });
  //
  $('select.chosen').each(function () {
    $(this).chosen({
      no_results_text: "Нет данных по запросу",
    });
  });
  //
  markChangeRow();
  //
  Filters();
	//
	Search();
	//
	CKEditor();
	// календарь
	if($('.datepicker').length)
	{
		$.datepicker.setDefaults($.datepicker.regional['ru']);
		$('.datepicker').datepicker();
	}
	//
	Blueimp();
	// флажки
	//$.preloadImg('img/loader.gif');
	$('.flag').click(function(){
		loader(true);
		
		var $obj = $(this);
		var new_src,new_alt,new_title;
		
		if(strpos($obj.attr('src'),'red'))
		{
			new_src = 'img/green-flag.png';
			new_alt = 'активно';
			new_title = 'заблокировать';
		}
		else
		{
			new_src = 'img/red-flag.png';
			new_alt = 'заблокировано';
			new_title = 'активировать';
		}
		
		var $script = $obj.next('input');
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
					$obj.attr({'src':new_src,'alt':new_alt,'title':new_title});
					$obj.parents('tr:first').find('td,th').effect("highlight", {}, 1000);
				}
				loader(false);		
			}
		});
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
	// загрузка изображений
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
	// сортировка
  $(function () {
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
          url: window.location.pathname + '?action=sort',
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
  });
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

function markChangeRow() {
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

function Search()
{
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