$(function(){
	//
	$('table.mods thead .mod-add img').click(function () {
		var n = $('table.mods tbody tr').size()+1;
    var tr  = '	<tr>';
				tr += '		<th class="mod-number"><span>'+n+'</span><input type="hidden" name="mods[id][]" value=""></th>';
				tr += '		<td class="mod-price"><input type="text" value="" name="mods[name][]"></td>';
    		tr += '		<td class="mod-price"><input type="text" value="" name="mods[sections][]"></td>';
				tr += '		<td class="mod-price"><input type="text" value="" name="mods[price][]"></td>';
				tr += '		<td class="mod-price"><input type="text" value="" name="mods[price_shelf][]"></td>';
				tr += '		<td class="mod-price"><input type="text" value="" name="mods[price_opt][]"></td>';
				tr += '		<td class="mod-price"><input type="text" value="" name="mods[price_shelf_opt][]"></td>';
				tr += '		<td class="mod-sort"><input type="text" value="" name="mods[sort][]"></td>';
				tr += '		<td class="mod-status"><input type="checkbox" value="1" name="mods[status][]"></td>';
				tr += '		<td class="mod-del"><img src="img/del.png" title="удалить модель"></td>';
				tr += '	</tr>';
		$('table.mods tbody').append(tr);
	});
	//
	$('table.mods tbody .mod-del img').live('click',function () {
		$(this).parents('tr:first').remove();
    var n = 1;
		$('table.mods tbody tr').each(function () {
			$(this).find('.mod-number span').html(n++);
    });
  });
});