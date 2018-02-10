<?
@session_start();
require_once('utils.php');

$fpath = $_GET['fpath'];
$fname = $_GET['fname'];
$src = $_SERVER['DOCUMENT_ROOT'].$fpath.$fname;

if(isset($_SESSION['admin']))
{
	if(!file_exists($src))
		$src = $_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo.jpg';
	header('Content-type:image/jpeg');
	echo file_get_contents($src);
	exit;
}
else
{
	if(!file_exists($src))
	{
		$src = $_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo.jpg';
		header('Content-type:image/jpeg');
		echo file_get_contents($src);
		exit;
	}

	// проверяем есть ли заготовка с водяным знаком
	$src9999 = $_SERVER['DOCUMENT_ROOT']."{$fpath}9999x9999/{$fname}";
	if(file_exists($src9999))
	{
		header('Content-type:image/jpeg');
		echo file_get_contents($src9999);
		exit;
	}

	$size = @getimagesize($src);
	// на всякий случай
	if($size === false) die();

	$format = strtolower(substr($size['mime'],strpos($size['mime'],'/')+1));
	$func = 'imagecreatefrom'.$format;

	$im = $func($src);

	imagealphablending($im,true);
	imagesavealpha($im,true);

	$wmpath = $_SERVER['DOCUMENT_ROOT'].'/img/watermark.png';
	$wmsize = @getimagesize($wmpath);
	if($wmsize !== false)
	{
		$wm = imagecreatefrompng($wmpath);
		if($wmsize[0]>$size[0] || $wmsize[1]>$size[1])
			$nsize = getMinRatioSize(array($wmsize[0],$wmsize[1]),array($size[0],$size[1]));
		else
			$nsize = array($wmsize[0],$wmsize[1]);

		imagecopyresampled($im, $wm, round(($size[0]-$nsize[0])/2), round(($size[1]-$nsize[1])/2), 0, 0, $nsize[0], $nsize[1], $wmsize[0], $wmsize[1]);
	}

	ob_start();
	imagepng($im);
	$im = ob_get_clean();

	// -------------------- сохраняем результат ----------------------
	// проверяем есть ли папка
	// если нет такой папки создаём её
	$dir = "{$fpath}9999x9999/";
	if(!is_dir($dir))
		@mkdir($dir,0777);

	file_put_contents($src,$im);
	@chmod($src,0644);

	// ---------------- выплёвываем картинку ---------------------
	header('Content-type:image/jpeg');
	echo $im;

	imagedestroy($wm);
	imagedestroy($im);
}