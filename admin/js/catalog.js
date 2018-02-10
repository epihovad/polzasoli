$(function(){
	//
	$.preloadImg('../img/cat_folder_open1.gif','../img/cat_opened.gif','../img/cat_folder_close1.gif','../img/cat_closed.gif');
	//
	function sh_child($this,checked)
	{
		var id = $($this).attr('id');
		var $child_block = $('#cat_'+id);
		if($child_block.size())
		{
			$child_block.find('input[type="checkbox"]').attr('checked',checked);
			if(checked)
			{
				if(!$child_block.is(':visible'))
				{
					$child_block.show();
					$('#znak_'+id+' img').attr('src','../img/cat_opened.gif');
					$('#folder_'+id+' img').attr('src','../img/cat_folder_open1.gif');
				}
				$child_block.find('.ar_block').each(function(){
					if(!$(this).is(':visible'))
					{
						var mas = this.id.split('_');
						var id = mas[1];
						$(this).show();
						$('#znak_'+id+' img').attr('src','../img/cat_opened.gif');
						$('#folder_'+id+' img').attr('src','../img/cat_folder_open1.gif');
					}
				});
			}
		}
	}
	//
	$("span[id^='znak_']").click(function(){
		var mas = this.id.split('_');
		var id = mas[1];
		
		var block = $('#cat_'+id);
		
		if(!block.size())
			block = $('#goods_'+id);
		
		if(!block.is(':visible'))
		{
			block.show();
			$(this).find('img').attr('src','../img/cat_opened.gif');
			$('#folder_'+id).find('img').attr('src','../img/cat_folder_open1.gif');
		}
		else
		{
			block.hide();
			$(this).find('img').attr('src','../img/cat_closed.gif');
			$('#folder_'+id).find('img').attr('src','../img/cat_folder_close1.gif');
		}
	});
	//
	$(".cat_open").click(function(){
		$("div[id^='cat_']").show();
		$("span[id^='znak_']").find('img').attr('src','../img/cat_opened.gif');
		$("span[id^='folder_']").find('img').attr('src','../img/cat_folder_open1.gif');
		
		$("div[id^='goods_']").show();
		return false;
	});
	$(".cat_close").click(function(){
		$("div[id^='cat_']").hide();
		$("span[id^='znak_']").find('img').attr('src','../img/cat_closed.gif');
		$("span[id^='folder_']").find('img').attr('src','../img/cat_folder_close1.gif');
		
		$("div[id^='goods_']").hide();
		return false;
	});	
});