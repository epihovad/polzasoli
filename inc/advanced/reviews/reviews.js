$(function(){
	reviews();
	$('#reviews input[name="kod"]').numer();
});

function reviews()
{
	var $reviews = $('#reviews');
	if(!$reviews.size()) return false;
	
	var $frm = $reviews.find('form');
	var $who = $frm.find('.who');
	//
	$reviews.find('.rvw').hover(function(){$(this).find('.rvw-ans').show()},function(){$(this).find('.rvw-ans').hide()});
	//
	$reviews.find('.rvw-ans').click(function(){
		var $item = $(this).parents('.rvw:first');
		var id_parent = $item.attr('cid');
		$who.find('span').html($item.find('.rvw-name').html()+'<b>'+$item.find('.rvw-date').html()+'</b>');
		$who.find('input[name="id_parent"]').val(id_parent);
		$who.show();
		$frm.find('textarea').focus();
		return false;
	});
	$who.find('a.remove').click(function(){ $who.hide(); $who.find('input[name="id_parent"]').val('0'); return false; });
	//
	$reviews.find('.btn').click(function(){ $frm.submit() });
	//
	$frm.submit(function(){ $reviews.find('input').removeClass('error') });
}