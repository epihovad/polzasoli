$(function(){
	
	/* ---------------------- arrnum ---------------------------- */
	$(".arrnum input").keypress(function(e){
		if(e.which!=8 && e.which!=0 && $(this).attr('value').length>2)
			return false;
		if(e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
			return false; 
			
		disOform();   
	});
			
	$.preloadImg('/inc/advanced/img/more_.gif','/inc/advanced/img/less_.gif');
	$('.more').hover(
		function(){ $(this).css("background","url(/inc/advanced/img/more_.gif) no-repeat") },
		function(){ $(this).css("background","url(/inc/advanced/img/more.gif) no-repeat") }
	);
	$('.less').hover(
		function(){ $(this).css("background","url(/inc/advanced/img/less_.gif) no-repeat") },
		function(){ $(this).css("background","url(/inc/advanced/img/less.gif) no-repeat") }
	);
	
	$('.more,.less').click(function(){
		var $input = $(this).parents('td:first').find('input');
		var cur_val = $input.val();
		if(!cur_val || isNaN(cur_val))
			$input.val(1);
		
		cur_val = parseInt($input.val());
		
		if($(this).hasClass('more'))
		{
			if(cur_val<99)
			{
				$input.val(cur_val+1);
				disOform();
			}
		}
		else
		{
			if(cur_val>1)
			{
				$input.val(cur_val-1);
				disOform();
			}
		}
	});
	
	/* -------------------------- / arrnum ---------------------------- */
});