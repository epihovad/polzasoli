/*
	ÏĞÈÌÅĞ: 
	<div ramka="/img/ramka1.png" style="padding:10px"></div>
		ramka - ïóòü ê ğàìêå
		padding - øèğèíà ğàìêè
*/

$(function(){
	
	for(var i=0; i<2; i++) // öèêë íà ñëó÷èé ğàìîê â ğàìêàõ
		makeRamka();	
	
});

// ÑÎÇÄÀÅÌ ĞÀÌÊÓ ÂÍÓÒĞÈ ÄÈÂÀ
function makeRamka()
{
	$('div[ramka]').each(function(){
		$(this).html(withRamka($(this))).css('padding',0).removeAttr('ramka');
	});
}

// ĞÀÌÊÀ (ÑÊĞÓÃËÅÍÍÛÅ ÓÃËÛ) ÂÍÓÒĞÈ İËÅÌÅÍÒÀ
function withRamka(obj) // ïóòü ê êàğòèíêå ğàìêè, òîëùèíà ğàìêè, ğàäèóñ ğàìêè
{
	var url = obj.attr('ramka');
	var r = obj.css('padding-top') || 10;
	var dot = '/inc/advanced/ramka/dot.php';
	var height100 = obj.hasClass('height100') ? ' class="height100"' : '';
	var width100 = obj.css('position') == 'absolute' ? '' : ' style="width:100%;"';
	var ramka;
	ramka = 	'<style>td.tdRamka { padding:0; border:none; width:auto; background:none; }</style>';
	ramka += '<table ' + width100 + height100 + '>';
	ramka += 	'<tr>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+url+') top left" height="'+r+'"><img src="/img/none.gif" width="'+r+'" height="'+r+'"></td>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+dot+'?src='+url+'&r='+r+'&line=1) top repeat-x">&nbsp;</td>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+url+') top right"><img src="/img/none.gif" width="'+r+'" height="'+r+'"></td>';
	ramka += 	'</tr>';
	ramka += 	'<tr>';
	ramka += 		'<td class="tdRamka" style="background:url('+dot+'?src='+url+'&r='+r+'&line=4) left repeat-y"></td>';
	ramka += 		'<td class="tdRamka"'+width100+'>'+obj.html()+'</td>';
	ramka += 		'<td class="tdRamka" style="background:url('+dot+'?src='+url+'&r='+r+'&line=2) right repeat-y"></td>';
	ramka += 	'</tr>';
	ramka += 	'<tr>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+url+') bottom left" height="'+r+'"><img src="/img/none.gif" width="'+r+'" height="'+r+'"></td>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+dot+'?src='+url+'&r='+r+'&line=3) bottom repeat-x">&nbsp;</td>';
	ramka += 		'<td class="tdRamka" style="font-size:0; background:url('+url+') bottom right"><img src="/img/none.gif" width="'+r+'" height="'+r+'"></td>';
	ramka += 	'</tr>';
	ramka += '</table>';
	return ramka;
}