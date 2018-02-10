$(function(){
	$('#tab').zebra();
	$('#check_all').click(function(){$('input[type="checkbox"]').not(this).attr('checked',$(this).attr('checked'))});
	$('input[value="добавить"]').click(function(){to_list(top.$('#'+$('#list').val()))});
	$('input[value="отмена"]').click(function(){top.hide_popup_window()});
});

function to_list(list)
{
	$('input:checked').each(function(){
		var id = $(this).attr('id');
		if(id=='check_all') return true;
		var name = $(this).val();
		
		var find_flag=0;
		list.find('option').each(function(){
			if($(this).val()==id)
			{
				find_flag++;
				return false;
			}
		});
		
		if(!find_flag)
			list.append('<option value="'+id+'">'+name+'</option>');
	});
	
	top.hide_popup_window();
}