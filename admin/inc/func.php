<?
/*
 * $childs = array(
 * 		array('catalog' => 'id_catalog'),
 * 		array('pages' => 'id_parent'),
 * 		array('goods' => 'id_catalog'),
 * )
 */
function remove_object($id, $childs = array())
{
	global $prx, $tbl;

	// обнуляем связки с текущей записью
	if(sizeof($childs)){
		foreach ($childs as $ch_tbl => $ch_filed) {
			sql("UPDATE {$prx}{$ch_tbl} SET {$ch_filed} = 0 WHERE {$ch_filed} = '{$id}'");
		}
	}
	// чистим sitemap
	sql("DELETE FROM {$prx}sitemap WHERE id_obj = '{$id}' AND `type` = '{$tbl}'");
	// мочим картинки
	remove_img($id, $tbl);
	// удаляем текущую рубрику
	update($tbl,'',$id);
}

//
function resizeIm($path,$imsize=array('100','100'),$newpath='',$method=1,$wmpath='')
{
	if(!sizeof($imsize)) return;
	if(!file_exists($path)) return;
	$size = @getimagesize($path);
	if($size === false) return;
	
	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	$icfunc = 'imagecreatefrom'.$format;
	
	if(!function_exists($icfunc)) return;
	
	$width = (int)$imsize[0];
	$height = (int)$imsize[1];
	if(!$width && !$height) return;
	
	if($method==1)
	{
		// 1-й вариант:
		$mas = getMinRatioSize(array($size[0],$size[1]),array($width,$height));
		$new_width  = $mas[0];
		$new_height = $mas[1];
		
		$isrc = $icfunc($path);
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
		
		$isrc = $icfunc($path);
		$idest = imagecreatetruecolor($width, $height);
		
		$alpha = imagecolorallocatealpha($idest,0,0,0,127); // цвет который должен быть прозрачным 
		imagefill($idest, 0, 0, $alpha); // закрашиваем все изображение 
		imagecolortransparent($idest,$alpha); // делаем цвет $alpha прозрачным 
		
		imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
		
		imagealphablending($idest,true);
		imagesavealpha($idest,true);
	}
	
	if($wmpath)
	{
		//$wmpath = $_SERVER['DOCUMENT_ROOT'].'/img/watermark.png';
		$wmsize = @getimagesize($wmpath);
		if($wmsize!==false)
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
	
	if($newpath)
	{
		// существование сопутствующих директорий
		$arr = explode('/',$newpath);
		$count = sizeof($arr);
		$dir = '';
		for($i=0; $i<$count-1; $i++)
		{
			$dir .= ($i?'/':'').$arr[$i];
			if(!is_dir($dir)) @mkdir($dir,0755);
		}
	}
	else
	{
		$arr = explode('/',$path);
		$a = $arr;
		unset($a[sizeof($a)-1]);
		$dir = implode('/',$a)."/{$imsize[0]}x{$imsize[1]}";
		if(!is_dir($dir)) @mkdir($dir,0755);
		$newpath = $dir.'/'.end($arr);
	}
	
	return file_put_contents($newpath,$im);
}

function remove_img($fname,$dir='')
{
	global $tbl;
	
	if(!$fname) return;
	$dir = $dir ? $dir : $tbl;
	
	if(!mb_strpos($fname,'.'))
	{
		$fe = getFileFormat($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$fname}.*");
		$fname = $fname.'.'.$fe;
	}
		
	// мочим большую картинку
	@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$fname}");
	// мочим уменьшенные копии
	$mas_dir = get_dir_list($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/");
	if(sizeof($mas_dir))
		foreach($mas_dir as $dir)
			@unlink($dir.$fname);
}