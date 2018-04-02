$(function(){
	// выделение сохраненного объекта
	mark_change_tr();
	// полосатая таблица
	$('.tab1').zebra();
	//
	$('#check_del').click(function(){
		$("input[id^='check_del_']").not(this).attr('checked',$(this).attr('checked'));
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
				$check.attr('checked',checked);
		});
	});
	//
	Search();
	//
	CKEditor();
	// календарь
	if($('.datepicker').length)
	{
		$.datepicker.setDefaults($.datepicker.regional['ru']);
		$('.datepicker').css({'width':'80px','text-align':'center'});
		$('.datepicker').datepicker();
	}
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
					$obj.parents('tr:first').find('td').effect("highlight", {}, 1000);
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
});

function Search()
{
	var $field = $('#searchTxt');
	var $btn = $('#searchBtn');
	if(!$field.length || !$btn.length) return false;

	$field.keydown(function(e){
		var code = e.keyCode || e.which;
		if(code==13) RegSessionSort($('#script').val(),'context='+$field.val());
	});
	$btn.click(function(){ RegSessionSort($('#script').val(),'context='+$field.val()); return false; });
}

function CKEditor() {
  // CKEditor
  $('textarea[toolbar]').each(function () { // ПРИМЕР: <textarea name="text" toolbar="basic" rows="10"><?=$row['text']?></textarea>
    this.id = this.name;
    var toolbar = $(this).attr('toolbar');
    var CKEditor = CKEDITOR.replace(
    	this.id,
      {
        toolbarGroups : [
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
    );
    CKFinder.setupCKEditor(CKEditor, '/js/ckfinder/');
  });
}