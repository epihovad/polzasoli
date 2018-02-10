<?
require('common.php');

// страницы
if($_GET['page'])
{
	if($_GET['page']=='remove')
		unset($_SESSION['ss']['page']);
	elseif((int)$_GET['page'])
		$_SESSION['ss']['page'] = $_GET['page'];
}
// сортировка в колонках
if($_GET['sort'])
{
	if($_GET['sort']=='remove')
		unset($_SESSION['ss']['sort']);
	else
		$_SESSION['ss']['sort'] = $_GET['sort'];
}
// буква
if(isset($_GET['letter']) && $_GET['letter']!='')	
{	
	if($_GET['letter']=='remove')
		unset($_SESSION['ss']['letter']);
	else
	{
		$_SESSION['ss']['letter'] = $_GET['letter'];
		unset($_SESSION['ss']['page']);
	}
}
// контекстный поиск
if($context = trim(preg_replace("/\s+/u",' ',$_GET['context'])))
{
	if($context=='remove')
		unset($_SESSION['ss']['context']);
	else
	{
		$_SESSION['ss']['context'] = $context;
		unset($_SESSION['ss']['page']);
	}
}
// sitemap поля
if(isset($_GET['sitemap']))
{
	if($_GET['sitemap']=='remove') unset($_SESSION['ss']['sitemap']);
	else $_SESSION['ss']['sitemap'] = 1;
}

// переменная $filters определена в spec.php
$n=1;
foreach($filters as $prm=>$txt)
{
	if($n++<4) continue;
	if($_GET[$prm])
	{
		if($_GET[$prm]=='remove')
			unset($_SESSION['ss'][$prm]);
		else
		{
			$_SESSION['ss'][$prm] = $_GET[$prm];
			unset($_SESSION['ss']['page']);
			unset($_SESSION['ss']['sort']);
		}
	}
}

remove_filters();

preg_match("/&location=(.*)/",$_SERVER['REQUEST_URI'],$mathces);
$location = $mathces[1];
?><script>top.location.href = '../<?=$location?>'</script><?