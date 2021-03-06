<?
require('inc/common.php');

$link = clean($_GET['link']);
$page = getRow("SELECT * FROM {$prx}pages WHERE status=1 AND link='{$link}'");
if(!$page) { header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit; }

$mainID = $page['id'];

$navigate = $page['name'];

$title = $page['name'];
foreach(array('title','keywords','description') as $val)
	if($page[$val]) $$val = $page[$val];

$h1 = $page['h1'] ? $page['h1'] : $page['name'];

ob_start();
?>
<div class="container-fluid" style="padding-bottom:40px">
  <?=navigate()?>
  <h1><?=$h1?></h1>
  <?=$page['text']?>
  <a href="" class="back" rel="nofollow"><i class="fas fa-arrow-left"></i>назад</a>
</div>
<?
$content = ob_get_clean();
require('tpl/template.php');