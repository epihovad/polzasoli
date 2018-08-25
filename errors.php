<?
require_once('inc/common.php');

$code = @$code ? $code : @$_GET['code'];
if(!$code) $code = 404;

ob_start();
switch($code)
{
	case 403:
	  $h1 = 'Доступ к данной странице закрыт';
		?><div class="nofind">Доступ к данной странице запрещен<div>перейти на <a href="/">главную страницу</a></div></div><?
		break;
	case 404:
		$h1 = 'Страница не найдена';
		?><div class="nofind">Запрашиваемая страница не найдена<div>перейти на <a href="/">главную страницу</a></div><div class="code">404</div></div><?
		break;
}
$data = ob_get_clean();

ob_start();
?>
<style>
  h1 { padding-top:40px;}
  .nofind { text-align:center; padding-top:20px; }
  .nofind .code { font-weight:normal; font-size:210px; margin:0 auto; text-align: center;}
</style>
<div class="container-fluid" style="padding-bottom:40px">
  <h1><?=$h1?></h1>
  <?=$data?>
  <a href="" class="back" rel="nofollow"><i class="fas fa-arrow-left"></i>назад</a>
</div>
<?
$content = ob_get_clean();
require('tpl/template.php');