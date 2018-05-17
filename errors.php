<?
require_once('inc/common.php');

$code = @$code ? $code : @$_GET['code'];
if(!$code) $code = 404;

ob_start();
switch($code)
{
	case 403:
		?><div class="nofind">Доступ к данной странице запрещен<div>перейти на <a href="/">главную страницу</a></div></div><?
		break;
	case 404:
		?><div class="nofind">Запрашиваемая страница не найдена<div>перейти на <a href="/">главную страницу</a></div><div class="code">404</div></div><?
		break;
}
$content = ob_get_clean();
require('tpl/template.php');