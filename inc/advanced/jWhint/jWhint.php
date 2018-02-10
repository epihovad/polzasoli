<?
@session_start();
require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php');

if(isset($_GET['w']))
{
	if(!$w = clean(stripslashes(trim(preg_replace("/\s+/u",' ',$_GET['w']))))) exit;
	echo getField("SELECT text FROM {$prx}hints WHERE name='{$w}'");
}
?>