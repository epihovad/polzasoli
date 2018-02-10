<?
session_start();

$a = rand(1,998);
$b = 1;
$_SESSION['number_test'] = $a+$b;

$string = $a.'+'.$b.'=';

// фоновое изображение
$back = "back.gif";
$im = imagecreatefromgif($back);
// Создаем в палитре новый цвет
$color = imagecolorallocate($im,190,190,205);
// Вычисляем размеры текста, который будет выведен
$px = (imageSX($im)-11.5*strlen($string))/2;
//подгружаем полученный шрифт
$font = imageloadfont("Pointy.gdf");
// Выводим строку поверх того, что было в загруженном изображении
imagestring($im, $font, $px, 1, $string, $color);
// Сообщаем о том, что далее следует рисунок GIF
header("Content-type: image/gif");
// Теперь - самое главное: отправляем данные картинки в
// стандартный выходной поток, т. е. в браузер
imagegif($im);
// В конце освобождаем память, занятую картинкой
imagedestroy($im);
?>