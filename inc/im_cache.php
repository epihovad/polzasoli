<?
@session_start();
require_once('utils.php');

$fpath = $_GET['fpath'];
$fname = $_GET['fname'];

$width = (int)$_GET['width'];
$height = (int)$_GET['height'];

$method = (int)$_GET['method'];
if(!in_array($method,array(1,2))) $method = 1;

// директория запрашиваемого изображения
$dir = $_SERVER['DOCUMENT_ROOT'].$fpath.$width.'x'.$height.'/';

// проверяем есть ли запрашиваемое изображение (маленькое)
if($fe = getFileFormat($dir.$fname.'.*'))
{
	$src = $dir.$fname.'.'.$fe;
	header("Content-type:image/jpeg");
	echo file_get_contents($src);
	exit;
}

// -------------------- формируем картинку ----------------------
$big_src = '';
$wm_show = isset($_GET['nowm']) ? false : true;
// проверяем на месте ли большая картинка из которой собираемся делать копию
$fe = getFileFormat($_SERVER['DOCUMENT_ROOT'].$fpath.$fname.'.*');
// если есть
if($fe)
{
	$src = $dir.$fname.'.'.$fe;
	$big_src = $_SERVER['DOCUMENT_ROOT'].$fpath.$fname.'.'.$fe;
}
// если нет
else
{
	// вместо реального (большого) изображения подсовываем no_image.jpg
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo.jpg'))
	{
		$src = $_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo'.$width.'x'.$height.'.jpg';
		// если уже есть маленькая картинка no_image
		if(file_exists($src))
		{
			header("Content-type:image/jpeg");
			echo file_get_contents($src);
			exit;
		}
		$big_src = $_SERVER['DOCUMENT_ROOT'].'/uploads/no_photo.jpg';
		$wm_show = false;
	}
}

$size = @getimagesize($big_src);
// на всякий случай
if($size === false) die();

$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
$icfunc = 'imagecreatefrom'.$format;

if(!function_exists($icfunc)) die();

if($method==1)
{
	// 1-й вариант:
	$mas = getMinRatioSize(array($size[0],$size[1]),array($width,$height));
	$new_width  = $mas[0];
	$new_height = $mas[1];
	
	$isrc = $icfunc($big_src);
	$idest = imagecreatetruecolor($new_width, $new_height);
	
	$alpha = imagecolorallocatealpha($idest,0,0,0,127); // цвет который должен быть прозрачным 
	imagefill($idest, 0, 0, $alpha); // закрашиваем все изображение 
	imagecolortransparent($idest,$alpha); // делаем цвет $alpha прозрачным 
	
	imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
	
	imagealphablending($idest,true);
	imagesavealpha($idest,true);
}
else
{
	// 2-й вариант: картинка по центру
	if(!$width) $width = $height;
	if(!$height) $height = $width;
	
	$x_ratio = $width / $size[0];
	$y_ratio = $height / $size[1];
	
	$ratio       = min($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio);
	
	if($size[0]>$width || $size[1]>$height)
	{
		$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
		$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
		$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
		$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
	}
	else
	{
		$new_width   = $size[0];
		$new_height  = $size[1];
		$new_left    = floor(($width - $size[0]) / 2);
		$new_top     = floor(($height - $size[1]) / 2);
	}
	
	$isrc = $icfunc($big_src);
	$idest = imagecreatetruecolor($width, $height);
	
	$alpha = imagecolorallocatealpha($idest,0,0,0,127); // цвет который должен быть прозрачным 
	imagefill($idest, 0, 0, $alpha); // закрашиваем все изображение 
	imagecolortransparent($idest,$alpha); // делаем цвет $alpha прозрачным 
	
	imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
	
	imagealphablending($idest,true);
	imagesavealpha($idest,true);
}

if($wm_show)
{
	$wmpath = $_SERVER['DOCUMENT_ROOT'].'/img/watermark.png';
	$wmsize = @getimagesize($wmpath);
	if($wmsize !== false)
	{		
		$wm = imagecreatefrompng($wmpath);
		if($wmsize[0]>$new_width || $wmsize[1]>$new_height)
			$nsize = getMinRatioSize(array($wmsize[0],$wmsize[1]),array($new_width,$new_height));
		else
			$nsize = array($wmsize[0],$wmsize[1]);
			
		imagecopyresampled($idest, $wm, round(($new_width-$nsize[0])/2), round(($new_height-$nsize[1])/2), 0, 0, $nsize[0], $nsize[1], $wmsize[0], $wmsize[1]);
	}
}

ob_start();
	imagepng($idest);
$im = ob_get_clean();

// -------------------- сохраняем результат ----------------------
// проверяем есть ли папка, если нет такой папки создаём её
if(!is_dir($dir))
	@mkdir($dir,0777);

// сохраняем файл
file_put_contents($src,$im);
@chmod($src,0644);

// ---------------- выплёвываем картинку ---------------------
header('Content-type:image/jpeg');
echo $im;

imagedestroy($isrc);
imagedestroy($idest);