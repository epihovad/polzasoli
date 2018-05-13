<?
require('inc/common.php');
ob_start();
$index = true;
?>
	<div class="container-fluid">
		<h1>Привет!</h1>
	</div>
<?
$content = ob_get_clean();
require('tpl/template.php');