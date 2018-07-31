<?
function menu()
{
  global $prx, $tbl;

  if(strpos($tbl,'.php') === false){
    $tbl .= '.php';
  }
	$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

  $tree = getTree("SELECT * FROM {$prx}am ORDER BY sort,id");
	if (!sizeof($tree)) {
	  return;
	}
	?><div id="menu"><?
  $i = 0;
  $old_level = null;
  foreach ($tree as $vetka) {
    $item = $vetka['row'];
    $level = $vetka['level'];
    $id = $item['id'];

    //$parents = getArrParents("SELECT id,id_parent FROM {$prx}am WHERE id='%s'", $id);
    $childs = getIdChilds("SELECT * FROM {$prx}am", $id);
    $siblings = getArr("SELECT id FROM {$prx}am WHERE id_parent = '{$item['id_parent']}'");

    $has_childs = sizeof($childs) > 1;

    if(!$i || $level > $old_level) {
      $display = !$level ? 'block' : (in_array($menu['id'], $siblings) !== false ? 'block' : 'none');
      ?><ul style="display:<?=$display?>"><?
    }
    if($old_level !== null && $level < $old_level){ ?></li></ul><? }

    if(!$level){
      $class = '';
      if($has_childs) $class .= ' has-sub';
      if(in_array($menu['id'], $childs) !== false) $class .= ' highlight active';

      ?><li class="<?=$class?>"><a href="<?=$has_childs?'#':$item['link']?>"><i class="<?=$item['im']?>"></i><span><?=$item['name']?></span></a><?
    } else {
      $class = '';
      if($id == $menu['id']) $class .= ' select';

      ?><li><a class="<?=$class?>" href="<?=$item['link']?>"><span><?=$item['name']?></span></a><?
    }

    $old_level = $level;
    $i++;
	}
	?></li></ul></div><?
}

function arr($head, $body, $custom = null)
{
  ob_start();
  ?>
  <div class="arr">
    <div class="arr-head">
      <h4><?=$head?></h4>
      <i><?=$custom?></i>
    </div>
    <div class="arr-body"><?=$body?></div>
  </div>
  <?
  return ob_get_clean();
}

function updateSitemap()
{
	global $prx, $tbl;
	foreach($_POST['lastmod'] as $id_obj=>$lastmod)
	{
		$lastmod = $lastmod ? date('Y-m-d',strtotime($lastmod)) : date('Y-m-d');
		$changefreq = $_POST['changefreq'][$id_obj];
		$priority = trim($_POST['priority'][$id_obj]);
			$priority = number_format(str_replace(',','.',$priority),1,'.','');
		$pr = (int)$priority;
		if($pr<0 || $pr>1) $priority = '0.5';
		
		$q = "INSERT INTO {$prx}sitemap (`id_obj`,`type`,`lastmod`,`changefreq`,`priority`)
					VALUES ('{$id_obj}','{$tbl}','{$lastmod}','{$changefreq}','{$priority}')
					ON DUPLICATE KEY UPDATE `lastmod`=VALUES(`lastmod`),`changefreq`=VALUES(`changefreq`),`priority`=VALUES(`priority`)";
		sql($q);
	}
}

function input($type, $name, $value = null, $property = null, $class = null)
{
	ob_start();
	switch($type)
	{
		case 'text':
			?><input name="<?=$name?>" value="<?=htmlspecialchars($value)?>" type="text" class="form-control input-sm <?=$class?>" <?=$property?>><?
			break;

		case 'textarea':
			?><textarea name="<?=$name?>" class="form-control input-sm <?=$class?>" <?=$property?>><?=$value?></textarea><?
			break;

		case 'date':
			?><input name="<?=$name?>" value="<?=$value?>" type="text" class="form-control input-sm datepicker <?=$class?>" <?=$property?>><?
			break;

		case "checkbox":
			/*?>
      <input type="hidden" name="<?=$name?>" id="ch_<?=$name?>"  value="<?=$value?>">
      <input type="checkbox" <?=($value=="true" ? "checked" : "")?> onClick="$('#ch_<?=$name?>').val(this.checked);" style="width:auto;"<?=($locked?" readonly":"")?>>
			<?*/
			break;

		case "color":
			//echo aInput("color", "name='{$name}'", $value);
			break;

		case "file":
			/*?>
      <table class="tab_no_borders" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input type="file" name="<?=$name?>"></td>
					<?
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/settings/{$name}.jpg"))
					{
						?>
            <td style="padding:0 10px 0 10px;">
              <a href="/uploads/settings/<?=$name?>.jpg" class="highslide" onclick="return hs.expand(this)">
                <img src="/img_spec/20x20/<?=$name?>.jpg" width="20" height="20" style="border:1px solid #333;" />
              </a>
            </td>
            <td>
              <a href="?action=pic_del&id=<?=$name?>" target="ajax">
                <img src="img/del_pic.png" title="удалить текущую картинку" width="20" height="20" style="border:1px solid #333;" class="alpha_png" />
              </a>
            </td>
						<?
					}
					?>
        </tr>
      </table>
			<?*/
			break;
	}
	return ob_get_clean();
}

function btn_flag($flag,$id,$link,$locked=0)
{
	global $script;
	
	if($locked) return;
	
	if($flag)
	{
		?><i class="flag active fab fa-font-awesome-flag" alt="активно" title="заблокировать"></i><?
		?><input type="hidden" value="<?=$script?>" /><?
		?><input type="hidden" value="<?=$link.$id?>" /><?
	}
	else
	{
		?><i class="flag fab fa-font-awesome-flag" alt="заблокировано" title="активировать"></i><?
		?><input type="hidden" value="<?=$script?>" /><?
		?><input type="hidden" value="<?=$link.$id?>" /><?
	}
}

function btn_edit($id,$locked=0,$properties='')
{
	ob_start();
	?>
  <div class="edit">
    <button type="button" class="btn btn-info btn-xs" alt="редактировать" title="редактировать"
      onclick="location.href = '?red=<?=$id?><?=$properties?>'">
      <i class="far fa-edit"></i>
    </button>
		<? if(!$locked) { ?>
      <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить"
        onclick="$(document).jAlert('show', 'confirm', 'Уверены?', function () { toajax(url('file') + '?action=del&id=<?=$id?><?=$properties?>'); })">
        <i class="far fa-trash-alt"></i>
      </button>
		<? }?>
  </div>
	<?
	return ob_get_clean();
}

function update_flag($tab,$pole,$id)
{
	global $prx;
	
	$res = getField("SELECT {$pole} FROM {$prx}{$tab} WHERE id={$id}");
	sql("UPDATE {$prx}{$tab} SET {$pole}=".($res?"0":"1")." WHERE id='{$id}'");
}

function get_criteria($tab)
{
	global $prx;
	
	$mas = array();
	
	$res = sql("SELECT * FROM {$prx}criteria WHERE tab_name='{$tab}'");
	while($row = @mysqli_fetch_assoc($res))
		if($row['show_flag'])
			$mas[] = $row['field_name'];
	
	return $mas;
}

function show_tr_images($mask,$title='Изображения',$help='',$count=3,$name='gimg',$dir='',$size_mini='45x45')
{
	global $prx, $tbl, $row;
	$dir = $dir ? $dir : $tbl;
	?>
	<tr>
		<th><?=$help?help($help):''?></th>
		<th><?=$title?></th>
		<td>
			<div class="gimg" count="<?=$count?>" name="<?=$name?>">
				<div class="glist" style="padding-left:0">
					<div class="add">
						<div class="i1"><input type="file" name="<?=$name?>[]"></div>
						<? if($count>1){ ?><div class="i2"><a href="" title="добавить">ещё</a></div><? }?>
					</div>
					<?
					$images = array();
			
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$mask}.jpg"))
						$images[] = "{$mask}.jpg";
						
					if($arr = getFileFormat($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$mask}_*",true))
					{
						$ArNum = array();
						foreach($arr as $fname)
						{
							$fname = basename($fname);
							preg_match('/_(.*)\.(?:.*)$/isU',$fname,$m);
						 	$ArNum[$m[1]] = $fname;
						}						
						ksort($ArNum);
						
						foreach($ArNum as $num=>$fname)
							$images[] = $fname;
					}
					
					if($images)
					{
						$i=1;
						?><div class="clear" style="padding-top:5px;"></div><?
						foreach($images as $fname)
						{
							?>
							<div class="im">
								<div class="i0"><?=$i++?>.</div>
								<div class="i1"><a href="/uploads/<?=$dir?>/<?=$fname?>" class="blueimp" title=""><img src="/uploads/<?=$dir?>/<?=$size_mini?>/<?=$fname?>" width="16"></a></div>
								<div class="i2"><a href="?action=img_del&id=<?=$mask?>&dir=<?=$dir?>&fname=<?=$fname?>" target="ajax" title="удалить текущее изображение"><i class="far fa-trash-alt"></i></a></div>
							</div>
							<?
						}
					}
					?>
				</div>
			</div>
		</td>
	</tr>
  <?
}

function show_tr_file($input_name,$path,$fname,$href,$name,$help='',$tr='')
{
	ob_start();
	?>
    <?=$tr?$tr:'<tr>'?>
      <th class="tab_red_th"><?=$help?help($help):''?></th>
      <th><?=$name?></th>
      <td align="left">
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td style="border:none;"><input type="file" size="30" name="<?=$input_name?>" /></td>
            <td align="left" style="border:none;">
            <?
						if($fname && file_exists($_SERVER['DOCUMENT_ROOT'].$path.$fname))
						{
							?>
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
								<td style="padding:0 5px 0 20px; border:none;">
									<a href="<?=$path.$fname?>" target="_blank">файл</a>
								</td>
								<td style="padding:0 0 0 5px; border:none;">
									<a href="<?=$href?>" target="ajax" style="border:none;">
									<img src="img/del_pic.png" width="20" height="20" />
									</a>
								</td>
								</tr>
							</table>
							<?
						}
						?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <?
	return ob_get_clean();
}

//
function SortColumn($column, $field)
{
  $sort = $_GET['fl']['sort'][$field];
  $title = 'сортировка ' . ($sort == 'asc' ? 'по возрастанию (А-Я)' : 'по убыванию (Я-А)');

  if(!$sort){
		?><a href="" target="_blank" onclick="changeURI({'fl[sort][<?=$field?>]':'asc'});return false;"><?=$column?></a><?
  } else {
    ?>
    <a href="" target="_blank" onclick="changeURI({'fl[sort][<?=$field?>]':'<?=$sort=='asc'?'desc':'asc'?>'});return false;"><?=$column?></a>
    <img src='img/sort_<?=$sort?>.gif' border='0' width='9' height='9' title='<?=$title?>' align="absmiddle" />
    <?
  }

}
//
function help($text)
{
	ob_start();
	?><a class="help" title="<?=htmlspecialchars($text)?>" href="" onClick="return false"><span class="glyphicon glyphicon-info-sign"></span></a><?
	return ob_get_clean();
}
// передача через массив (как $defaults) или строкой, с перечислением требуемых кнопок ('Сохранить::Добавить::Удалить')
function show_listview_btns($btns = '', $individual_btns = array()){

  global $script;

  $defaults = array(
    'Сохранить' => array('js' => "SaveAll('?action=saveall')", 'class' => 'warning', 'icon' => 'far fa-save'),
    'Добавить' => array('link' => '?red=0', 'class' => 'success', 'icon' => 'fa fa-plus'),
    'Удалить' => array('js' => "SaveAll('?action=multidel',1,1)", 'class' => 'danger', 'icon' => 'far fa-trash-alt'),
  );

  // собственный набор кнопок
  if(sizeof($individual_btns)){
    $btns = $individual_btns;
  } else {
    // стандартный набор
		if ($btns) {
			if (is_array($btns) === false) {
				$arr = explode('::', $btns);
				foreach ($defaults as $k => $none) {
					if (array_search($k, $arr) === false) {
						unset($defaults[$k]);
					}
				}
				$btns = $defaults;
			}
		} else {
			$btns = $defaults;
		}
	}

  ?><div id="listview-btns"><?
  foreach ($btns as $name => $prm){
    $onclick = $prm['link'] ? "location.href = '".$prm['link']."'" : $prm['js'];
    ?><button type="button" class="btn btn-<?=$prm['class']?> btn-xs" onclick="<?=$onclick?>"><?
    if($prm['icon']){ ?><i class="<?=$prm['icon']?>"></i><? }?>
    <span><?=$name?></span></button><?
  }
  ?></div><?
}
//
function show_letter_navigate($link,$tab,$pole,$where='')
{
	global $prx;
	
	$mas_en = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$mas_ru = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
	$mas_num = array('0','1','2','3','4','5','6','7','8','9');
	
	$mas_en_in_base = array();
	$mas_ru_in_base = array();
	$mas_num_in_base = array();
	
	$res = sql("SELECT DISTINCT SUBSTRING({$pole},1,1) as symbol from {$prx}{$tab} WHERE 1=1 {$where} ORDER BY {$pole}");
	while($row = @mysqli_fetch_assoc($res))
	{
		//$symbol = strto($row['symbol'],'upper');
		$symbol = mb_strtoupper($row['symbol']);
		
		if(in_array($symbol, $mas_en))
			$mas_en_in_base[] = $symbol;
		elseif(in_array($symbol, $mas_ru))
			$mas_ru_in_base[] = $symbol;
		elseif(in_array($symbol, $mas_num))
			$mas_num_in_base[] = $symbol;
	}	
	
	?>
  <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0 5px 0;">
  <tr>
  <td align="center" style="padding:0;">
    <?
    $i=1;
    $size = sizeof($mas_en);
    foreach($mas_en as $k=>$v)
    {
      if(in_array($v, $mas_en_in_base))
      {
        ?><a href="" target="ajax" class="green_link" onclick="RegSessionSort('<?=$link?>','letter=<?=$v?>');return false;"<?=($_SESSION['ss']['letter']==$v?" style='color:#ff3300;'":"")?> title="вывести объекты на букву '<?=$v?>'"><?=$v?></a><?=($i!=$size?"&nbsp;":"")?><?
      }
      else
      {
        ?><span style="color:#CCC;"><?=$v?></span><?=($i!=$size?"&nbsp;":"")?><?
      }
      $i++;
    }
    ?>
  </td>
  </tr>  
  <tr>
  <td align="center" style="padding:3px 0 3px 0;">
		<?
    $i=1;
    $size = sizeof($mas_ru);
    foreach($mas_ru as $k=>$v)
    {
      if(in_array($v, $mas_ru_in_base))
      {
        ?><a href="" target="ajax" class="green_link" onclick="RegSessionSort('<?=$link?>','letter=<?=$v?>');return false;"<?=($_SESSION['ss']['letter']==$v?" style='color:#ff3300;'":"")?> title="вывести объекты на букву '<?=$v?>'"><?=$v?></a><?=($i!=$size?"&nbsp;":"")?><?
      }
      else
      {
        ?><span style="color:#CCC;"><?=$v?></span><?=($i!=$size?"&nbsp;":"")?><?
      }
      $i++;
    }
    ?>
  </td>
  </tr>
  <tr>
  <td align="center" style="padding:0 0 3px 0;">
		<?
    $i=1;
    $size = sizeof($mas_num);
    foreach($mas_num as $k=>$v)
    {
      if(in_array($v, $mas_num_in_base))
      {
        ?><a href="" target="ajax" class="green_link" onclick="RegSessionSort('<?=$link?>','letter=<?=$v?>');return false;"<?=($_SESSION['ss']['letter']==$v?" style='color:#ff3300;'":"")?> title="вывести объекты, назване которых начинаются с цифры '<?=$v?>'"><?=$v?></a><?=($i!=$size?"&nbsp;":"")?><?
      }
      else
      {
        ?><span style="color:#CCC;"><?=$v?></span><?=($i!=$size?"&nbsp;":"")?><?
      }
      $i++;
    }
    ?>
  </td>
  </tr>
  </table>
	<?
}
//
$filters = array(
  'page' => 'постраничный вывод объектов',
  'sort' => 'сортировка по колонкам',
  'letter' => 'выбор объектов по букве',
  'search' => 'выбор объектов по контекстному поиску',
  'sitemap' => 'отображать sitemap поля',
  'catalog' => 'выбор объектов по рубрике каталога',
  'gallery_catalog' => 'выбор объектов по рубрике каталога',
  'order_status' => 'выбор заказов по статусу',
  'msg' => 'выбор сообщений по типу',
  'reviews' => 'выбор отзывов по объекту',
);

function ActiveFilters()
{
	global $filters;

	if(!sizeof($_GET['fl']))
	  return;

	$mas = array();
	foreach($_GET['fl'] as $prm => $none){
		$mas[$prm] = 'сбросить фильтр «<b>' . $filters[$prm] . '</b>»';
	}

	?>
	<div id="listview-clear-filters" class="pull-left bg-warning">
	  <?=help('здесь вы можете сбросить фильтры,<br>примененные ранее к текущему списку объектов')?>
	  <? foreach($mas as $k=>$v){
			?><div class="fltr"><a href="" class="clr-orange" onclick="changeURI({'fl[<?=$k?>]':null});return false;"><?=$v?></a></div><?
		}?>
		<hr>
		<div><a href="" class="clr-orange" onclick="document.location = url('path');return false;"><b>сбросить все фильтры</b></a></div>
  </div>
  <div class="clearfix"></div>
	<?
}

function remove_filters()
{
	global $filters;
	// удаление всех фильтров
	if(@$_GET['filters']=='remove')
	{
		foreach($filters as $prm=>$txt)
			unset($_SESSION['ss'][$prm]);
	}
}

function change_user_info($info,$user)
{
	preg_match_all("|ФИО</b>: ([^<]*)|i",$info,$mas);
	if($mas)
	{
		$fio = $mas[1][0];
		ob_start();
		?><a href="" target="_blank" onclick="RegSessionSort('users.php?red=<?=$user?>','filters=remove');return false;"><?=$fio?></a><?
		$new_fio = ob_get_clean();
		$info = str_replace($fio, $new_fio, $info);
	}
	
	return $info;
}

function get_pic_name($prefix,$dir='')
{
	global $tbl;

	$dir = $dir ? $dir : $tbl;
		
	if(!file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$prefix}.jpg"))
		return "{$prefix}.jpg"; 
	
	$num = array();
	$images = getFileFormat($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$prefix}_*",true);
	if($images)
	{
		foreach($images as $fname)
		{
			// $fname имеет формат
			// C:/www/sites/s-dom.local/uploads/goods/049489381774_2.jpg
			// нужна лишь 049489381774_2.jpg
			$fname = end(explode('/',$fname));
			preg_match("/^".$prefix."_([0-9]+).jpg$/isU",$fname,$mas);
			if($mas[1])
				$num[] = $mas[1];
		}
	}
	
	$new_fname = '';
	
	if($size = sizeof($num))
	{
		asort($num);
		$i=1;
		foreach($num as $v)
		{
			if($v!=$i)
			{
				$new_fname = "{$prefix}_{$i}.jpg";
				break;
			}
			$i++;
		}		
		if(!$new_fname)
		{		
			$n = end($num)+1;
			$new_fname = "{$prefix}_{$n}.jpg";
		}
	}
	else
		$new_fname = "{$prefix}_1.jpg";
	
	return $new_fname;
}

function showCK($name,$text,$toolBar='full',$width="100%",$rows=20)
{
	ob_start();
	?><textarea name="<?=$name?>" toolbar="<?=$toolBar?>" rows="<?=$rows?>" style="width:<?=$width?>"><?=$text?></textarea><?
  return ob_get_clean();
}