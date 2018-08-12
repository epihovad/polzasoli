<?
require('inc/common.php');

$page = array();
if($link = clean($_GET['link'])){
	if(!$page = getRow("SELECT * FROM {$prx}disease WHERE link = '{$link}' AND status = 1")){
		header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit;
	}
}

$navigate = '<a href="/diseases/">Справочник болезней</a>';

ob_start();

// ---------------------------- Страница болезни
if($page) {

	$title = $page['name'];
	foreach (array('title', 'keywords', 'description') as $val)
		if ($page[$val])
			$$val = $page[$val];

	$h1 = $page['h1'] ? $page['h1'] : $page['name'];

	echo $page['text'];
}
// ---------------------------- Справочник болезней
else {



}

$data = ob_get_clean();

ob_start();
?>
<div class="container-fluid" style="padding-bottom:40px">
  <?=navigate()?>
  <h1><?=$h1?></h1>
  <?=$data?>
  <a href="" class="back" rel="nofollow">назад</a>
</div>
<?
$content = ob_get_clean();
require('tpl/template.php');