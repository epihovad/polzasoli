<?
function price($price)
{
	//return number_format($price,is_int($price*1)?0:2,',',' ');
	return number_format($price,0,',',' ');
}
//
function dateAdd($date='',$ads=array(),$mask='d.m.Y H:i:s')
{
	$date = $date ? strtotime($date) : mktime();
	$time = mktime(date('H',$date)+$ads['H'],date('i',$date)+$ads['i'],date('s',$date)+$ads['s'],date('n',$date)+$ads['m'],date('j',$date)+$ads['d'],date('Y',$date)+$ads['Y']);
	return date($mask,$time);
}
//
function online() 
{
	global $prx;
	
	$ip = getenv("REMOTE_ADDR");
	$sid = session_id();

	$find_sid = getField("SELECT sid FROM {$prx}users_online WHERE sid='{$sid}'");

	$cur_time = time();

	if($find_sid)
		sql("UPDATE {$prx}users_online SET time='{$cur_time}' WHERE sid='{$find_sid}'");
	else
		sql("INSERT INTO {$prx}users_online (ip, time, sid) VALUES ('{$ip}', '{$cur_time}', '{$sid}')");
}
//
function users_online() 
{
	global $prx;
	
	$past = time()-180;

	sql("DELETE FROM {$prx}users_online WHERE time < {$past}");

	return (int)getField("SELECT COUNT(*) FROM {$prx}users_online");;
}
//
function cleanReserve()
{
	global $prx;
	
	$res = mysql_query("SELECT * FROM {$prx}reserve WHERE `date` < DATE_SUB(NOW(),INTERVAL 1 DAY)");
	while($row = @mysql_fetch_assoc($res))
	{
		update('goods',"nalich=nalich+1",$row['id_goods']);
		update('reserve','',$row['id']);
	}
}
// ПОЛУЧАЕТ МАССИВ ВСЕГО ЗАПРОСА
function getArr($sql, $simple=true) // $simple=true - возвратит "простой" массив (без привязки к полям запроса)
{
	$res = sql($sql);
	
	if($simple)
	{
		while($row = mysql_fetch_row($res))
			if(mysql_num_fields($res)>1)
				$arr[$row[0]] = $row[1];
			else
				$arr[] = $row[0];
	}
	else
		while($row = mysql_fetch_assoc($res))
			$arr[] = $row;

	return (array)$arr;
}
// ВЫПАДАЮЩИЙ СПИСОК/МУЛЬТИСПИСОК для таблицы/массива
function dll($obj, $properties, $value='', $default=NULL) // запрос/массив, св-ва списка, значение (может быть массивом), "пустое" значение(может быть массивом)
{ 
	ob_start();
	
	?><select <?=$properties?>><?	
	if($default !== NULL)
	{
		if(is_array($default)) 
		{
			foreach($default as $k=>$v)
			{
				?><option value="<?=htmlspecialchars($k)?>"<?=$k==$value?' selected':''?>><?=$v?></option><?
			}
		} 
		else 
		{ 
			?><option value=""<?=!$value?' selected':''?>><?=$default?></option><?	
		}
	}
	$arr = is_array($obj) ? $obj : getArr($obj);
	foreach($arr as $key=>$val)
	{
		$selected = is_array($value) ? in_array($key, $value) : $key==$value;
		?><option value="<?=htmlspecialchars($key)?>"<?=($selected?' selected':'')?>><?=$val?></option><? 
	}
	?></select><? 
	
	return ob_get_clean();
}
// ПЕРЕВОД ДАТЫ В ФОРМАТ БАЗЫ ДАННЫХ
function formatDateTime($datetime='00.00.0000 00:00:00') // можно передавать только дату и вообще формат не строгий!
{
	if(!($time = strtotime($datetime)))
		return '';
	$format = date('H:i:s', $time) == '00:00:00' ? 'Y-m-d' : 'Y-m-d H:i:s';
	return date($format, $time);
}
function trans($title, $rtl_standard = 'gost')
{
    static $gost, $iso;
 
    switch ($rtl_standard)
    {
      case 'off':
        return $title;
 
      case 'gost':
        if (empty($gost))
        {
            $gost = array(
                'Є'=>'EH','І'=>'I','і'=>'i','№'=>'#','є'=>'eh',
                'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D',
                'Е'=>'E','Ё'=>'JO','Ж'=>'ZH',
                'З'=>'Z','И'=>'I','Й'=>'JJ','К'=>'K','Л'=>'L',
                'М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R',
                'С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'KH',
                'Ц'=>'C','Ч'=>'CH','Ш'=>'SH','Щ'=>'SHH','Ъ'=>'\'',
                'Ы'=>'Y','Ь'=>'','Э'=>'EH','Ю'=>'YU','Я'=>'YA',
                'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
                'е'=>'e','ё'=>'jo','ж'=>'zh',
                'з'=>'z','и'=>'i','й'=>'jj','к'=>'k','л'=>'l',
                'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
                'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh',
                'ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shh','ъ'=>'',
                'ы'=>'y','ь'=>'','э'=>'eh','ю'=>'yu','я'=>'ya',
                '«'=>'"','»'=>'"','"'=>'"','"'=>'"','—'=>'-');
        }
        return strtr($title, $gost);
 
      default:
        if (empty($iso))
        {
            $iso = array(
                'Є'=>'YE','І'=>'I','Ѓ'=>'G','і'=>'i','№'=>'#','є'=>'ye','ѓ'=>'g',
                'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D',
                'Е'=>'E','Ё'=>'YO','Ж'=>'ZH',
                'З'=>'Z','И'=>'I','Й'=>'J','К'=>'K','Л'=>'L',
                'М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R',
                'С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'X',
                'Ц'=>'C','Ч'=>'CH','Ш'=>'SH','Щ'=>'SHH','Ъ'=>'\'',
                'Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'YU','Я'=>'YA',
                'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
                'е'=>'e','ё'=>'yo','ж'=>'zh',
                'з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l',
                'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
                'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'x',
                'ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shh','ъ'=>'',
                'ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
                '«'=>'"','»'=>'"','"'=>'"','"'=>'"','—'=>'-'
            );
        }
        return strtr($title, $iso);
    }
}
// ПРИВОДИМ ТЕКСТ К ПРИГОДНОМУ ДЛЯ ССЫЛКИ
function makeUrl($str)
{
	$str = trans($str);
	$str = str_replace(array(" ",".",","), "_", $str);
	$str = mb_strtolower($str);
	$str = preg_replace('#[^a-z0-9_\-]#isU','',$str); // оставляем только буквы, цифры, - и _
	return $str; 
}
// ПОЛУЧАЕТ ОДНО ЗНАЧЕНИЕ ИЗ ТАБЛИЦЫ
function getField($sql)
{
	$res = mysql_query($sql); 
	$field = @mysql_result($res,0,0);
	return $field;
}	
// ПОЛУЧАЕТ МАССИВ ПЕРВОЙ СТРОКИ ТАБЛИЦЫ
function getRow($sql)
{
	$res = mysql_query($sql); 
	$row = @mysql_fetch_assoc($res);
	return $row;
}
// ЗНАЧЕНИЕ ПОЛЯ В ОПРЕДЕЛЁННОЙ СТРОКЕ ТАБЛИЦЫ
function gtv($tab,$pole,$id)
{
	global $prx;
	if($pole=='*' || mb_strpos($pole,',')!==false)
		return getRow("SELECT {$pole} FROM {$prx}{$tab} WHERE id='{$id}'");
	else
		return getField("SELECT {$pole} FROM {$prx}{$tab} WHERE id='{$id}'");
}
// ВЫПАДАЮЩИЙ СПИСОК ДЛЯ ПОЛЯ enum
function dllEnum($tab,$field,$properties,$value="",$default=NULL)
{
	global $prx;
	
	ob_start();
	?>
	<select <?=$properties?>><? 
	if($default!==NULL)
	{
		if(is_array($default)) 
		{
			foreach($default as $k=>$v)
			{
				?><option value="<?=htmlspecialchars($k)?>"><?=$v?></option><?
			}
		} 
		else 
		{ 
			?><option value=""><?=$default?></option><?	
		}
	}
	$res = sql("SHOW COLUMNS FROM {$prx}{$tab} LIKE '{$field}'");
	$val = mysql_result($res,0,1);
	$val = str_replace(array("enum(",")","'"), "", $val);
	$arr = explode(",",$val);
	foreach($arr as $val) 
	{
	  if(!$val) continue;
		?><option value="<?=$val?>" <?=($val==$value ? "selected" : "")?>><?=$val?></option><? 
	} 
	?></select><? 	
	return ob_get_clean();
}
// ВОЗВРАЩАЕТ ЗНАЧЕНИЕ ПЕРЕМЕННОЙ ИЗ ТАБЛИЦЫ settings
function set($name, $tbl='settings')
{
	global $prx;

	if(!$_SESSION['cache'][$tbl])
		$_SESSION['cache'][$tbl] = getArr("SELECT id,`value` FROM {$prx}{$tbl}");
	
	$val = $_SESSION['cache'][$tbl][$name];

	return $val=='true' ? true : ($val=='false' ? false : $val);
}
// ОБНОВЛЕНИЕ / ДОБАВЛЕНИЕ / УДАЛЕНИЕ ЗАПИСИ В ТАБЛИЦЕ
function update($tbl, $set="", $id=0) // таблица, обновляемые поля, id (для удаления id может быть массивом, строкой через ',' или NULL)
{
	global $prx;
	if(!$set)
	{
		if(is_null($id))
			sql("TRUNCATE TABLE {$prx}{$tbl}");
		if(is_array($id))
			sql("DELETE FROM {$prx}{$tbl} WHERE id IN ('".implode("','",$id)."')");
		elseif(strpos(",",$id))
			sql("DELETE FROM {$prx}{$tbl} WHERE id IN (".trim($id,",").")");
		else
			sql("DELETE FROM {$prx}{$tbl} WHERE id='{$id}'");
		return;
	}
	if($id)
		sql("UPDATE {$prx}{$tbl} SET {$set} WHERE id='{$id}'");
	else
	{
		sql("INSERT INTO {$prx}{$tbl} SET {$set}");
		$id = mysql_insert_id();
	}
	return $id;
}
//ОЧИСТКА СТРОКИ ДЛЯ ВИДА ПРИГОДНОГО К ПЕРЕДАЧИ В JAVASCRIPT
function cleanJS($str) 
{
	$str = preg_replace('#[\n\r]+#', '\\n', $str); // убираем переносы строк
	$str = str_replace(array("\'", '\"'), array("'", '"'), $str); //  убираем экранирование
	$str = str_replace("'", "\'", $str); //  экранируем только '
	$str = str_replace('script>', "scr'+'ipt>",$str); // чтобы можно было вставить скрипт
   return $str;
}
// ВЫВОД ALERT ОБ ОШИБКЕ (и прерывание выполнения)
function errorAlert($msg,$exit=true)
{
	?><script>alert("<?=$msg?>");top.loader(false);</script><?
	if($exit) exit;
}
//
function pre($data)
{
	?><pre><?
	if(is_array($data)) print_r($data);
	else echo $data;
	?></pre><?
}
// ЗАМЕНА mysql_query - ВЫВОДИТ ТЕКСТ ЗАПРОСА В СЛУЧАИ НЕУДАЧИ
function sql($sql, $debug=false)
{
	global $debugSql;
	$res = mysql_query($sql);
	if((!$res && @$_SESSION['admin']) || $debugSql || $debug)
	{
		$text = $sql."\r\n".mysql_error()."\r\n";
		echo nl2br($text);
		$text = cleanJS($text);
		?>
		<script>
		if(top.window !== window && <?=(!$debugSql && !$debug ? "true" : "false")?>) // если мы во фрейме, то выводим алерт и прерываем фрейм
		{
			alert('<?=$text?>');
			location.href = "/inc/none.html";
		}
		else
			alert('<?=$text?>');
		top.loader(false);
		</script><?
	}
	return $res;
}
// ПОДГОТОВКА СТРОКИ К СОХРАНЕНИЮ В ТАБЛИЦЕ
function clean($str, $strong=false) 
{
	$str = @trim($str);
	$str = stripslashes($str);
	if($strong)
	{
		$str = preg_replace('/\s\s+/',' ',$str); // убираем повторяющиеся пробелы
		$str = htmlspecialchars($str); //преобразоваваем теги html
		$str = strtr($str, array("'"=>"&#0039;", "'"=>"&#0039;"));
	}
	else
		$str = addslashes($str);
	return $str;
}

function ToDouble($val)
{
	if($val == '') return 0;
	$val = str_replace(',','.',$val);
		$val = str_replace(' ','',$val);
			$val = number_format($val,0,'.','');
	return $val;
}
// ОТПРАВКА ПИСЬМА ЧЕРЕЗ SMTP PHPMailer
function mailTo($to, $subject, $body)
{
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/inc/Mailer.php');
	$Mailer = new Mailer();
	return $Mailer->mailTo($to, $subject, $body);
}
/*// ОТПРАВКА HTML ПИСЬМА
function mailTo($to, $subject, $message, $from='', $charset='utf-8')
{
	$subject = "=?{$charset}?B?".base64_encode($subject)."?=";
	$headers  = "Content-type: text/html; charset={$charset} \r\n";
	if($from)
		$headers .= "From: {$from}\r\nReply-To: {$from}\r\n";
	return @mail($to, $subject, $message, $headers);
}*/
// ОТПРАВКА HTML ПИСЬМА С ВЛОЖЕНИЯМИ
function mailToFiles($to, $subject, $message, $from='', $files=array(), $charset='utf-8')
{
	$subject = "=?{$charset}?B?".base64_encode($subject)."?=";
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/nomad_mimemail.php');								
	$mimemail = new nomad_mimemail();
	$mimemail->set_charset($charset);
	$mimemail->set_to($to);
	if($from)	$mimemail->set_from($from);
	$mimemail->set_subject($subject);
	$mimemail->set_html("<HTML><HEAD></HEAD><BODY>{$message}</BODY></HTML>");
	foreach($files as $file)
	{
		if(file_exists($file))
			$mimemail->add_attachment($file,basename($file));
	}

	return $mimemail->send();
}
// проверка mail адреса
function check_mail($mail)
{
	$shablon = "/[0-9a-z_\-]+@[0-9a-z_\-^\.]+\.[a-z]{2,3}/i";
	return preg_match($shablon,$mail);
}
// проверка времени
function check_time($time,$mask='чч:мм')
{
	$shablon = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
	
	return preg_match($shablon,$time);
}
// ГЕНЕРАТОР ПАРОЛЯ
// $pass_length - длина пароля
function get_new_pass($pass_length=6)
{
	$simvols = array ('0','1','2','3','4','5','6','7','8','9',
										'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
										'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	
	for ($i = 0; $i < $pass_length; $i++)
	{
		shuffle($simvols);
		$string = $string.$simvols[1];
	}
		
	return $string;
}
// ВСТАВКА FLASH
// пусть к флехе, свойства (ширина, высота...)
function flash($src, $properties="") 
{
	ob_start();
	?><embed src='<?=$src?>' <?=$properties?> quality='high' wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'></embed><?
	return ob_get_clean();
}
// ВОЗВРАЩАЕТ РАЗМЕРЫ В СООТВЕТСТВИИ С ПРОПОРЦИЯМИ
function getMinRatioSize($size=array("320","240"), $sizeto=array("160","120"))
{
	list($width, $height) = $sizeto;
	if(!$width || $width > $size[0])
		$width = $size[0];
	if(!$height || $height > $size[1])
		$height = $size[1];
	
	$x_ratio = $width / $size[0];
	$y_ratio = $height / $size[1];
	
	$ratio = min($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio);
	
	$width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
	$height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
	
	return array($width, $height);
}
//
function getImSize($path,$w,$h)
{
	if(!file_exists($path)) return $w.'x'.$h;
	
	$size = getimagesize($path);
	$size = getMinRatioSize(array($size[0],$size[1]),array($w,$h));
	if($size[0]<$w) return $w.'x-';
	elseif($size[1]<$h) return '-x'.$h;
	else return $w.'x'.$h;
}
// РАСШИРЕНИЕ ФАЙЛА
function getFileExtension($filename) 
{
	return end(explode('.',$filename));
}

function getFileFormat($mask,$array=false)
{
	// $mask = $_SERVER['DOCUMENT_ROOT']."/uploads/news/10.*";
	$images = glob($mask, GLOB_NOSORT);
	if($images)
	{
		if($array)
		{
			$res = array();
			foreach($images as $val)
				if(!is_dir($val))
					$res[] = $val;
					
			return (array)$res;
		}
		else
			return end(explode(".",$images[0]));
	}
	else
		return false;
}
// Возвращает массив всех директорий,
// находящихся в $path
function get_dir_list($path)
{
	$res = array();
	
	if(is_dir($path))
	{
		$dh = opendir($path);
	  	while (false !== ($dir = readdir($dh))) 
	  	{
		 	if(is_dir($path.$dir) && $dir!=='.' && $dir!=='..') 
		 	{
			 	$subdir = $path.$dir.'/';
			 	$res[] = $subdir;
			 	get_dir_list($subdir);
		 	} 
		 	else
				next;
	  	}
	  	closedir($dh);
	} 
	/*else
	   print "Директорий не найдено";*/
   
   	return $res;
}
// Возвращает массив всех файлов,
// находящихся в $dir
function get_file_list($dir)
{
	$res = array();
	
   	if($dh=opendir($dir))
   	{
    	while(($file=readdir($dh))!==false)
      	{
        	if($file!=='.' && $file!=='..')
          	{
             	$current_file = "{$dir}/{$file}";
             	if(is_file($current_file))
                	$res[] = $file;
          	}
       	}
		closedir($dh);
   	}
	
	return $res;
}
// УДАЛЕНИЕ ДИРЕКТОРИИ
function delete_dir($dirname,$files_only=false)
{
	if(is_dir($dirname))
		$dir_handle = opendir($dirname);
	if(!$dir_handle)
		return false;
	
	while($file = readdir($dir_handle))
	{
		if($file!="." && $file!="..")
		{
			if(!is_dir($dirname."/".$file))
				@unlink($dirname."/".$file);
			else
				delete_dir($dirname.'/'.$file);
		}
	}
	closedir($dir_handle);
	
	if(!$files_only)
		rmdir($dirname);
	
	return true;
}

function getImages($dir,$mask)
{
	$images = array();
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$mask}.jpg"))
		$images[] = $mask.'.jpg';
	if($arr = getFileFormat($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$mask}_*",true))
		foreach($arr as $fname)
			$images[] = basename($fname);
	return $images;
}

function getStructureTable($tableName,$pole='')
{
	global $prx;
	/*
	Параметры: $tableName-имя таблицы БД
	Возвращает: ассоциативный массив:
		 [<имя поля>] => Array(
			  [Field] => id                                       //-имя поля (соответствует ключу массива)
			  [Type] => int(5)                                    //-тип поля
			  [Collation] => cp1251_general_ci                    //-кодировка
			  [Null] => NO
			  [Key] => PRI
			  [Default] => 1
			  [Extra] => auto_increment
			  [Privileges] => select,insert,update,references
			  [Comment] => 
			  [number] => 0
		 )
	*/
	$res = mysql_query("SHOW FULL FIELDS FROM {$prx}{$tableName}");
	if(!$res)
		return false;
	else
	{
		$mas = array();
		$i=0;
		while($row = mysql_fetch_assoc($res))
		{
			if($pole && $pole==$row['Field'])
				return 	$row['Comment'];
				
			$mas[$row['Field']] = $row;
			$mas[$row['Field']]['number'] = $i;
			$i++;
		 }
	}
	
	return count($mas)?$mas:false;
}

// ОПРЕДЕЛЕНИЕ ЧТО КОДИРОВКА ТЕКСТА В UTF8
function detectUTF8($str)
{
	return preg_match('//u', $str);
}
// PHP конвертер из Windows-1251 в UTF-8
function win2utf($text, $iconv=true) // текст (в кодировке Windows-1251), флаг что сначала попытаться использовать функцию iconv
{
	if(detectUTF8($text))
		return $text;
		
	if(!$iconv || !function_exists('iconv'))
	{
		for($i=0, $m=strlen($text); $i<$m; $i++)
		{
			$c=ord($text[$i]);
			if($c<=127) {
				@$t.=chr($c); continue; 
			}
			if($c>=192 && $c<=207) {
				@$t.=chr(208).chr($c-48); continue; 
			}
			if($c>=208 && $c<=239) {
				@$t.=chr(208).chr($c-48); continue; 
			}
			if($c>=240 && $c<=255) {
				@$t.=chr(209).chr($c-112); continue; 
			}
			if($c==184) { 
				@$t.=chr(209).chr(209);	continue; 
			}
			if($c==168) { 
				@$t.=chr(208).chr(129);	continue; 
			}
		}
		return $t;
	}
	else
		return iconv('windows-1251', 'utf-8', $text);
}
// PHP конвертер из UTF-8 в Windows-1251
function utf2win($text, $iconv=true) // текст (в кодировке UTF-8), флаг что сначала попытаться использовать функцию iconv
{
	if(!$iconv || !function_exists('iconv'))
	{
		$out = $c1 = '';
		$byte2 = false;
		for($c=0; $c<strlen($text); $c++)
		{
			$i = ord($text[$c]);
			if ($i <= 127)
				$out .= $text[$c];
	
			if($byte2) 
			{
				$new_c2 = ($c1 & 3) * 64 + ($i & 63);
				$new_c1 = ($c1 >> 2) & 5;
				$new_i = $new_c1 * 256 + $new_c2;
				$out_i = $new_i == 1025
					? 168
					: ($new_i==1105 ? 184 : $new_i-848);
				$out .= chr($out_i);
				$byte2 = false;
			}
			if(($i >> 5) == 6) 
			{
				$c1 = $i;
				$byte2 = true;
			}
		}
		return $out;
	}
	else
		return iconv('utf-8', 'windows-1251', $text);
}

// $mask - маска:
// d - день месяца
// m - месяц
// y - год
// w - день недели
function getRusDate($mask,$date='')
{
	$date = $date ? $date : date('d.m.Y');
	
	$mas = explode('.',date('D.d.m.Y',strtotime($date)));	
	$dayofweek = mb_strtolower($mas[0]);
	$day = $mas[1];
	$month = $mas[2];
	$year = $mas[3];
	
	$masEng = array('mon','tue','wed','thu','fri','sat','sun');
	$masRus = array('понедельник','вторник','среда','четверг','пятница','суббота','воскресение');
	$dayofweek = $masRus[array_search($dayofweek,$masEng)];
	
	$masM 			= array('01','02','03','04','05','06','07','08','09','10','11','12');
	$masM_small 	= array('янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек');
	$masM_big 		= array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	$month_small 	= $masM_small[array_search($month,$masM)];
	$month_big 		= $masM_big[array_search($month,$masM)];
	
	return strtr($mask,array(	'd'=>$day,
								'm'=>$month_small,
								'M'=>$month_big,
								'y'=>$year,
								'w'=>$dayofweek
								));
}

function get_nasled_classifer($id_catalog,$tab='classifer')
{
	global $prx, $mas_nasled;
	
	$res = mysql_query("SELECT A.* FROM {$prx}{$tab} A INNER JOIN {$prx}{$tab}_catalog B ON A.id = B.id_{$tab} WHERE B.id_catalog='{$id_catalog}' ORDER BY A.sort");
	while($row = @mysql_fetch_array($res))
	{
		if(!in_array($row['id'],(array)$mas_nasled))
			$mas_nasled[$row['id']] = $row;
	}
	
	if($id_parent = gtv('catalog','id_parent',$id_catalog))
		get_nasled_classifer($id_parent,$tab);	
}

// отправка СМС
function smsTo($to, $msg)
{
	if(set('sms')!='true') return false;
	$to = preg_replace("/\D/",'',$to);
	if(mb_strlen($to)!=11 || !$msg) return false;
	$login = set('sms_login');
	$password = set('sms_pass');
	$msg = utf2win($msg);
	if(!$login || !$password || !$msg) return false;
	 
	$u = 'http://www.websms.ru/http_in5.asp';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'Http_username='.urlencode($login).'&Http_password='.urlencode($password).'&fromPhone=x9r.ru&Phone_list='.$to.'&Message='.urlencode($msg));
	curl_setopt($ch, CURLOPT_URL, $u);
	$u = trim(curl_exec($ch));
	curl_close($ch);
	preg_match("/message_id\s*=\s*[0-9]+/i", $u, $arr_id);
	$id = preg_replace("/message_id\s*=\s*/i", "", @strval($arr_id[0]));
	return $id;
}
?>