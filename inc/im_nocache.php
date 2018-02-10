<?
@session_start();
require_once('utils.php');

$fpath = $_GET['fpath'];
$fname = $_GET['fname'];

$width = (int)$_GET['width'];
$height = (int)$_GET['height'];

$src = $_SERVER['DOCUMENT_ROOT'].$fpath.($width ? $width.'x'.$height.'/' : '').$fname;

if(!file_exists($src)){
	$src = $_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo'.($width ? $width.'x'.$height : '').'.jpg';
}

header('Content-type:image/jpeg');
echo file_get_contents($src);