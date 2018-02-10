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
			if($check.size())
				$check.attr('checked',checked);
		});
	});
	//
	Search();
	//
	CKEditor();
	// тень
	$(document).jB();
	
	// календарь
	if($('.datepicker').size())
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
		if(!$input.size()) return false;
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
	$('.gimg').find('.add .i2 a').live('click',function(){
		var $block = $(this).parents('div.gimg:first');
		var count = $block.attr('count')-1;
		var name = $block.attr('name');
		
		var $glist = $block.find('.glist');
		var n = $glist.find('.add').size();

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
	if(!$field.size() || !$btn.size()) return false;

	$field.keydown(function(e){
		var code = e.keyCode || e.which;
		if(code==13) RegSessionSort($('#script').val(),'context='+$field.val());
	});
	$btn.click(function(){ RegSessionSort($('#script').val(),'context='+$field.val()); return false; });
}

function CKEditor()
{
	// CKEditor
	try {
		$('textarea[toolbar]').each(function() { // ПРИМЕР: <textarea name="text" toolbar="basic" rows="10"><?=$row['text']?></textarea>
			this.id = this.name;
			var toolbar = $(this).attr('toolbar');
			var CKEditor = CKEDITOR.replace(this.id,
			{
				toolbar: toolbar,
				width: $(this).width(),
				height: $(this).height(),
				removePlugins: toolbar == 'Full' ? '' : 'elementspath',
				resize_enabled: toolbar == 'Full',
				contentsCss: '/css/CK.css?v=20171223',
				coreStyles_bold: { element : 'b' },
				coreStyles_italic: { element : 'i' },
				skin: 'v2',
				uiColor: '#d4dff2',
				toolbar_full:[
					['Source','-','Maximize',/*'Save',*/'Preview','-','Templates'],
					['Cut','Copy','Paste','PasteText','PasteFromWord'],
					['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
					['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],'/',
					['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
					['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
					['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
					['BidiLtr','BidiRtl'],['Link','Unlink','Anchor'],
					['Image','Flash','Table','HorizontalRule','SpecialChar'],'/',
					[/*'Styles',*/'Format',/*'Font',*/'FontSize'],['TextColor','BGColor'],['ShowBlocks'] 
				],
				toolbar_medium:[
					['Source','-',/*'Save',*/'Preview','-','Templates'],['Cut','Copy','Paste','PasteText','PasteFromWord'],
					['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],'/',
					['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
					['NumberedList','BulletedList'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
					['Link','Unlink'],['Image','Flash','Table','HorizontalRule','SpecialChar'],'/',
					['Format',/*'Font',*/'FontSize'],['TextColor','BGColor'],['ShowBlocks'] 
				],
				toolbar_basic:[
					['Source','-','Bold','Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','-','TextColor','-','Link','Unlink','-','Image']
				]
			});
			CKFinder.setupCKEditor(CKEditor, '/inc/advanced/ckfinder/');			
		});
	} catch(e) {}
}