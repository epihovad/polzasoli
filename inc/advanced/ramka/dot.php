<?
// ToDo: по-хорошему, точку надо-бы кэшировать... наверно


// ПЕРЕВОД ЦВЕТА ИЗ HTML В RGB
function html2rgb($color='FFFFFF')
{
	list($r, $g, $b) = strlen($color)==6
		? array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5])
		: array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
	
	return array(hexdec($r), hexdec($g), hexdec($b));
}


$src = mysql_escape_string(@$_GET['src']); // путь к картинке из которой будем вырезать

if($src)
{
	// ВЫРЕЗАЕМ ТОЧКУ (линию) ИЗ КАРТИНКИ
	$r = @$_GET['r'] ? (int)$_GET['r'] : 1; // длина точки
	$line = @$_GET['line'] ? (int)$_GET['line'] : 1; // индекс линии (1=>'верх', 'право', 'низ', 'лево')
	
	$src = $_SERVER['DOCUMENT_ROOT'].$src;
	$size = @getimagesize($src);
	
	switch($line)
	{
		case '1': $w = 1;		$h = $r;		$top = 0;						$left = (int)$size[0]/2;	break;
		case '2': $w = $r;	$h = 1;		$top = (int)$size[1]/2;		$left = $size[0]-$r;			break;
		case '3': $w = 1;		$h = $r;		$top = $size[1]-$r;			$left = (int)$size[0]/2;	break;
		case '4': $w = $r;	$h = 1;		$top = (int)$size[1]/2;		$left = 0;						break;
	}
	$type = $size['mime'];
	$format = strtolower(substr($type, strpos($type, '/')+1));
	$icfunc = 'imagecreatefrom'.$format;
	$isrc = $icfunc($src);
	$idest = imagecreate($w, $h);
	//imagecolortransparent($idest, imagecolorallocate($idest, 0, 0, 0));
	imagecopy($idest, $isrc, 0, 0, $left, $top, $w, $h);
}
else
{
	// СОЗДАЕМ КАРТИНКУ 
	$w = @$_GET['w'] ? (int)$_GET['w'] : 1; // ширина точки
	$c = @$_GET['c'] ? mysql_escape_string($_GET['c']) : '000000'; // цвет точки

	list($r, $g, $b) = html2rgb($c);
	
	$idest = ImageCreate($w, $w);
	imagecolorallocate($idest, $r, $g, $b); 
}

header('Content-type: image/png');
imagepng($idest); 
?>