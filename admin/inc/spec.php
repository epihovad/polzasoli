<?
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
// горячая статистика - на самом верху
function show_hot_statistic()
{
	global $prx;
	
	// пользователи
	$str_res = "пользователей онлайн: ".users_online();
	
	$count = getField("select count(*) from {$prx}orders where status='новый'");
	if($count)
	{
		if($str_res) $str_res .= "&nbsp;|&nbsp;";
		ob_start();
		?><a href="" target="ajax" onclick="RegSessionSort('orders.php','filters=remove');return false;"><?=$count?></a>&nbsp;<?
		$str_res .= "новых заказов: ".ob_get_clean();
	}
	
	return $str_res;
}
// главное меню - навигация
function ShowNavigate()
{
	global $prx,$script,$script_fake;
	$sc = $script_fake ? $script_fake : $script;
	
	?>
  <div class="nav">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
  <?
	$mas = getTree("SELECT * FROM {$prx}am WHERE id_parent='%s' ORDER BY sort,id");
	if(sizeof($mas))
	{
		foreach($mas as $vetka)
		{
			$row = $vetka['row'];
			$level = (string)$vetka["level"];
			
			if($level=='0')
			{
				?>
				<tr>
					<td width="20" align="left"><img src="img/navigate/<?=$row['link']?>.png" width="25" height="22"/></td>
					<td><a href="" target="_blank" onclick="RegSessionSort('<?=$row['link']?>.php','filters=remove');return false;" class="<?=$sc==$row['link'].'.php'?"nav_link2":"nav_link1"?>"><?=$row['name']?></a></td>
				</tr>
				<?
			}
			else
			{
				?>
				<tr>
					<td width="20" align="left"></td>
					<td><a href="" target="_blank" onclick="RegSessionSort('<?=$row['link']?>.php','filters=remove');return false;" class="<?=$sc==$row['link'].'.php'?"nav_link2":"nav_link1"?>"><?=$row['name']?></a></td>
				</tr>
				<?
			}
		}
	}
	?>
	</table>
	</div>
	<?
}

// выводим субконтент раздела меню (редактировать, удалить, добавить...)
function show_subcontent($razdel)
{
	$script = basename($_SERVER['SCRIPT_FILENAME']);
	ob_start();
	?><table class="razdel"><tr><td><?
		$i = 0;
		foreach($razdel as $k=>$v)
		{
			$cur = mb_strpos($v,$script)!==false;
			?><?=($i++>0?'<span>|</span>':'')?><a href="<?=$v?>"<?=$cur?' class="cur"':''?>><?=$k?></a><?
		}
	?></td></tr></table><?
	return ob_get_clean();
}

// Страницы навигации
// show_navigate_pages(количество страниц,текущая,'ссылка = ?topic=news&page=')
function show_navigate_pages($x,$p,$link)
{
	if($x<2)
		return '';
	
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="str_page">
  	<tr>
    <td align="left" valign="middle">&nbsp;
	<?
	if($x<4)
	{
		for($i=1;$i<=$x;$i++)
		{
			if($i==$p)
				echo "[".$i."]&nbsp;";
			else
				echo get_href($link,$i)."&nbsp;";
		}
	}
	if($x==4)
	{
		if($p==1) // 1
			echo "[".$p."]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
		if($p==2) // 2
			echo get_href($link,1)."&nbsp;[".$p."]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
		if(($p-1)==2) // 3
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$p-1)."&nbsp;[".$p."]&nbsp;".get_href($link,$x);
		if($p==$x) // 4
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$x-1)."&nbsp;[".$p."]";
	}
	if($x>4)
	{
		if($p==1) // 1
			echo "[1]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
		elseif($p==2) // 2
			echo get_href($link,1)."&nbsp;[".$p."]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
		elseif(($p-1)==2) // 3
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$p-1)."&nbsp;[".$p."]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
		elseif(($x-$p)==1) // 4
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$p-1)."&nbsp;[".$p."]&nbsp;".get_href($link,$x);
		elseif($p==$x) // 5
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$x-1)."&nbsp;[".$p."]";
		else
			echo get_href($link,1)."&nbsp;...&nbsp;".get_href($link,$p-1)."&nbsp;[".$p."]&nbsp;".get_href($link,$p+1)."&nbsp;...&nbsp;".get_href($link,$x);
	}
	?>
    </td>
    <td align="right" valign="middle">
    <span>перейти к странице&nbsp;</span>
    <select onchange="RegSessionSort('<?=$link?>','page='+this.value);">
    <?
	for($i=1;$i<=$x;$i++)
	{
    	?><option value="<?=$i?>" <?=($p==$i?"selected":"")?>><?=$i?></option><?
	}
	?>
    </select>&nbsp;
    </td>
    </tr>
	</table>
    </div>
    <?	
}
function get_href($link,$name)
{
	ob_start();
	?>
    <a href="" target="_blank" onclick="RegSessionSort('<?=$link?>','page=<?=$name?>');return false;"><?=$name?></a>
    <?
	return ob_get_clean();
}

function btn_flag($flag,$id,$link,$locked=0)
{
	global $script;
	
	if($locked) return;
	
	if($flag)
	{
		?><img class="flag" src="img/green-flag.png" alt="активно" title="заблокировать" width="16" height="16"><?
		?><input type="hidden" value="<?=$script?>" /><?
		?><input type="hidden" value="<?=$link.$id?>" /><?
	}
	else
	{
		?><img class="flag" src="img/red-flag.png" alt="заблокировано" title="активировать" width="16" height="16"><?
		?><input type="hidden" value="<?=$script?>" /><?
		?><input type="hidden" value="<?=$link.$id?>" /><?
	}
}

function btn_edit($id,$locked=0,$properties='')
{
	ob_start();
	?>
  <a href="?red=<?=$id?><?=$properties?>"><img src="img/edit.png" width="16" height="16" alt="редактировать" title="редактировать" /></a>
	<?
	if(!$locked)
	{
		?><a href="javascript:if(confirm('Уверены?')) location.href='?action=del&id=<?=$id?><?=$properties?>'" target="ajax"><?
		?><img src="img/del.png" width="16" height="16" alt="удалить" title="удалить" /><?
    ?></a><?
	}
	return ob_get_clean();
}

function btn_sort($id,$param='')
{
	ob_start();
	?>
  <a href="" target="ajax" onclick="toajax('?action=moveup&id=<?=$id?><?=$param?>');return false;"><img src="img/up.png" width="16" height="16" class="alpha_png" alt="вверх" title="вверх" border="0" /></a>
  <a href="" target="ajax" onclick="toajax('?action=movedown&id=<?=$id?><?=$param?>');return false;"><img src="img/down.png" width="16" height="16" class="alpha_png" alt="вниз" title="вниз" border="0" /></a>
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
	
	$res = mysql_query("SELECT * FROM {$prx}criteria WHERE tab_name='{$tab}'");
	while($row = @mysql_fetch_assoc($res))
		if($row['show_flag'])
			$mas[] = $row['field_name'];
	
	return $mas;
}

function show_tr_img($input_name,$path,$fname='',$href,$name='Изображение',$help='',$tr='',$url=false,$img=true)
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
              	<?
								if($img)
								{
									?>
                  <a href="<?=$path.$fname?>" class="highslide" onclick="return hs.expand(this)">
                  <img src="img/image20x20.png" width="20" height="20" title="показать изображение" />
                  </a>
                  <?
								}
								else
								{
									?><a href="<?=$path.$fname?>" target="_blank">файл</a><?
								}
								?>
              </td>
              <td style="padding:0 0 0 5px; border:none;">
                <a href="<?=$href?>" target="ajax" style="border:none;">
                <img src="img/del_pic.png" width="20" height="20" title="удалить изображение" />
                </a>
              </td>
              </tr>
            </table>
            <?
          }
          ?>
          </td>
          <?
					if($url)
					{
						?><th style="border:none; padding-left:20px;">Url: <input type="text" name="<?=$input_name?>_url" style="width:300px;" /></th><?
					}
					?>
        </tr>
      </table>
    </td>
  <?=$tr?$tr:'</tr>'?>
  <?
	return ob_get_clean();
}

function show_tr_images($mask,$title='Изображения',$help='',$count=3,$name='gimg',$dir='',$size_mini='45x45')
{
	global $prx, $tbl, $row;
	$dir = $dir ? $dir : $tbl;
	?>
	<tr>
		<th class="tab_red_th"><?=$help?help($help):''?></th>
		<th><?=$title?></th>
		<td>
			<div class="gimg" count="<?=$count?>" name="<?=$name?>">
				<div class="glist" style="padding-left:0">
					<div class="add">
						<div class="i1"><input type="file" name="<?=$name?>[]"></div>
						<? if($count>1){ ?><div class="i2"><a href="" title="добавить">ещё</a></div><? }?>
					</div>
          <div class="clear" style="padding-top:5px;"></div>
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
						foreach($images as $fname)
						{
							?>
							<div class="im">
								<div class="i0"><?=$i++?>.</div>
								<div class="i1"><a href="/uploads/<?=$dir?>/<?=$fname?>" class="highslide" onclick="return hs.expand(this)"><img src="/uploads/<?=$dir?>/<?=$size_mini?>/<?=$fname?>" width="16"></a></div>
								<div class="i2"><a href="?action=img_del&id=<?=$mask?>&dir=<?=$dir?>&fname=<?=$fname?>" target="ajax" title="удалить текущее изображение"><img src="img/del.png"></a></div>
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

// ------------------------ ФУНКЦИИ СОРТИРОВКИ -----------------------------
// ПЕРЕСОРТИРОВКА
function resort($tab,$where='')
{
	global $prx;
	
	$where = $where ? ' AND '.str_replace(',',' AND ',$where) : '';
	
	$res = mysql_query("SELECT id FROM {$prx}{$tab} WHERE sort>0{$where} ORDER BY sort,id");
	$i=0;
	while($row = @mysql_fetch_assoc($res))
		update($tab,"sort=".(++$i),$row['id']);
}
// В САМЫЙ ВЕРХ
function sort_movetop($tab,$id,$where='')
{
	global $prx;
	
	if(!getField("SELECT id FROM {$prx}{$tab} WHERE id='{$id}'"))
		exit;
	
	// пересортировка
	resort($tab,$where);
	
	$where = $where ? str_replace(',',' and ',$where) : '';
	
	// определим новое значение поля sort
	$res = getRow("SELECT id,sort FROM {$prx}{$tab} WHERE sort>0{$where} ORDER BY sort,id LIMIT 1");
	if($res['id']==$id)
		errorAlert('Выше некуда!');
	else
	{
		$sort = (int)$res['sort']-1;
		update($tab,"sort={$sort}",$id);
	}
}
// В САМЫЙ НИЗ
function sort_movebottom($tab,$id,$where='')
{
	global $prx;
	
	if(!getField("SELECT id FROM {$prx}{$tab} WHERE id='{$id}'"))
		exit;
	
	// пересортировка
	resort($tab,$where);
	
	$where = $where ? str_replace(',',' and ',$where) : '';
	
	// определим новое значение поля sort
	$res = getRow("SELECT id,sort FROM {$prx}{$tab} WHERE sort>0{$where} ORDER BY sort DESC, id DESC LIMIT 1");
	if($res['id']==$id)
		errorAlert('И так уже внизу!');
	else
	{
		$sort = (int)$res['sort']+1;
		update($tab,"sort={$sort}",$id);
	}
}
// НА ОДНУ ПОЗИЦИЮ ВЕРХ
function sort_moveup($tab,$id,$where='')
{
	global $prx;
	
	if(!getField("SELECT id FROM {$prx}{$tab} WHERE id='{$id}'"))
		exit;
	
	// пересортировка
	resort($tab,$where);
	
	$where = $where ? ' and '.str_replace(',',' and ',$where) : '';
	
	// текущая позиция
	$cur_sort = getField("SELECT sort FROM {$prx}{$tab} WHERE id='{$id}'");
	// верхняя позиция
	$res = getRow("SELECT id,sort FROM {$prx}{$tab} WHERE sort>0 and sort<{$cur_sort}{$where} ORDER BY sort DESC, id DESC LIMIT 1");
	if($res)
	{
		$pre_id = $res['id'];
		$pre_sort = $res['sort'];				
	}
	if($pre_id)
	{
		// меняем позицию предыдущей записи
		update($tab,"sort={$cur_sort}",$pre_id);
		// меняем позицию текущей записи
		update($tab,"sort={$pre_sort}",$id);
	}
	else
		errorAlert('Выше некуда!');
}
// НА ОДНУ ПОЗИЦИЮ ВНИЗ
function sort_movedown($tab,$id,$where='')
{
	global $prx;
	
	if(!getField("SELECT id FROM {$prx}{$tab} WHERE id='{$id}'"))
		exit;
	
	// пересортировка
	resort($tab,$where);
	
	$where = $where ? ' and '.str_replace(',',' and ',$where) : '';
	
	// текущая позиция
	$cur_sort = getField("SELECT sort FROM {$prx}{$tab} WHERE id='{$id}'");
	// нижняя позиция
	$res = getRow("SELECT id,sort FROM {$prx}{$tab} WHERE sort>0 and sort>{$cur_sort}{$where} ORDER BY sort,id LIMIT 1");
	if($res)
	{	
		$sled_id = $res['id'];
		$sled_sort = $res['sort'];
	}
	if($sled_id)
	{
		// меняем позицию предыдущей записи
		update($tab,"sort={$cur_sort}",$sled_id);
		// меняем позицию текущей записи
		update($tab,"sort={$sled_sort}",$id);
	}
	else
		errorAlert('И так уже внизу!');
}
// ShowSortPole('страница = news.php','Имя столбца = Название','текущая сортировка = up/down/0','имя поля в БД = name');
function ShowSortPole($page,$cur_pole,$cur_sort,$name,$pole)
{
	ob_start();
	
	if(!$cur_pole) // если сессии нет
	{
		?><a href="" target="_blank" onclick="RegSessionSort('<?=$page?>','sort=<?=$pole?>:down');return false;"><?=$name?></a><?
	}
	else
	{
		if($pole==$cur_pole)
		{
			?>
			<a href="" target="_blank" onclick="RegSessionSort('<?=$page?>','sort=<?=$pole?>:<?=($cur_sort=="up"?"down":"up")?>');return false;"><?=$name?></a>
			<img src='img/sort_<?=$cur_sort?>.gif' border='0' width='9' height='9' title='сортировка <?=($cur_sort=="up"?"по убыванию (Я-А)":"по возрастанию (А-Я)")?>' align="absmiddle" />
			<?
		}
		else
		{
			?><a href="" target="_blank" onclick="RegSessionSort('<?=$page?>','sort=<?=$pole?>:down');return false;"><?=$name?></a><?
		}
	}
		
	return ob_get_clean();
}

function ins_div($tek_lvl,$old_lvl,$id_parent)
{
	// текущий уровень больше предыдущего
	if($tek_lvl>$old_lvl)
	{
		?><div id="cat_<?=$id_parent?>" style="display:none;"><?
	}
	// текущий уровень меньше предыдущего
	if($tek_lvl<$old_lvl)
	{
		$delta = ($old_lvl-$tek_lvl);
		while($delta>0)
		{
			echo "</div>";
			$delta--;
		}
	}
}

function show_pole($type,$name,$value='',$locked=0,$rows=3)
{
	ob_start();
	switch($type)
	{
		case 'text':
			?><input type="<?=$type?>" class="form-control input-sm" name="<?=$name?>" value="<?=$value?>" style="width:100%;"<?=($locked?" readonly":"")?>><?
			break;
		
		case 'textarea':
			?><textarea name="<?=$name?>" class="form-control" style="width:100%;" rows="<?=$rows?>"<?=($locked?" readonly":"")?>><?=$value?></textarea><?
			break;
		
		case "checkbox":
			?>
			<input type="hidden" name="<?=$name?>" id="ch_<?=$name?>"  value="<?=$value?>">
			<input type="checkbox" <?=($value=="true" ? "checked" : "")?> onClick="$('#ch_<?=$name?>').val(this.checked);" style="width:auto;"<?=($locked?" readonly":"")?>>
			<?
			break;

		case "datetime":
		case "date":
			echo aInput($type, "name='{$name}'", $value);	
			break;

		case "color":
			echo aInput("color", "name='{$name}'", $value);	
			break;
		
		case "file":
			?>
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
            <?
			
			break;
	}
	return ob_get_clean();
}
function help($text)
{
	ob_start();
	?><a class="help" title="<?=htmlspecialchars($text)?>" href="" onClick="return false"><span class="glyphicon glyphicon-info-sign"></span></a><?
	/*?><a class="help" title="<?=htmlspecialchars($text)?>" href="" onClick="return false"><img src="img/help.png" width="16" height="16" align="absmiddle" /></a><?*/
	return ob_get_clean();
}
// передача через массив (как $defaults) или строкой, с перечислением требуемых кнопок ('Сохранить::Добавить::Удалить')
function show_listview_btns($btns = ''){

  global $script;

  $defaults = array(
      'Сохранить' => array('js' => "saveall()", 'class' => 'warning', 'icon' => 'far fa-save'),
      'Добавить' => array('link' => '?red=0', 'class' => 'success', 'icon' => 'fa fa-plus'),
      'Удалить' => array('js' => "multidel(document.red_frm,'check_del_','')", 'class' => 'danger', 'icon' => 'far fa-trash-alt'),
    );

  if($btns){
    if(is_array($btns) === false){
      $arr = explode('::',$btns);
      foreach ($defaults as $k => $none){
        if(array_search($k, $arr) === false){
          unset($defaults[$k]);
        }
      }
      $btns = $defaults;
    }
  } else {
    $btns = $defaults;
  }

  ?><div id="listview_btns"><?
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
	
	$res = mysql_query("SELECT DISTINCT SUBSTRING({$pole},1,1) as symbol from {$prx}{$tab} WHERE 1=1 {$where} ORDER BY {$pole}");
	while($row = @mysql_fetch_assoc($res))
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

$filters = array(	'page'=>'постраничный вывод объектов',
									'sort'=>'сортировка по колонкам',
									'letter'=>'выбор объектов по букве',
									'context'=>'выбор объектов по контекстному поиску',
									'sitemap'=>'отображать sitemap поля',
									'catalog'=>'выбор объектов по рубрике каталога',
									'gallery_catalog'=>'выбор объектов по рубрике каталога',
									'order_status'=>'выбор заказов по статусу',
									'msg'=>'выбор сообщений по типу',
									'reviews'=>'выбор отзывов по объекту');

function show_filters($link)
{
	global $filters;
	
	$mas = array();	
	foreach($filters as $prm=>$txt)
	{
		if($prm=='page') continue;
		if($_SESSION['ss'][$prm])
			$mas[$prm] = 'сбросить фильтр "'.$txt.'"';
	}
	
	if(!sizeof($mas)) return;

	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:3px 0 3px 0;">
		<tr><td><?=help('здесь вы можете сбросить фильтры,<br>примененные ранее к текущему списку объектов')?></td></tr>
		<?
		foreach($mas as $k=>$v)
		{
			?><tr><td align="left"><a href="" onclick="RegSessionSort('<?=$link?>','<?=$k?>=remove');return false;" style="color:#697079;"><?=$v?></a></td></tr><?
		}		
		?>
		<tr><td align="left"><a href="" onclick="RegSessionSort('<?=$link?>','filters=remove');return false;" style="color:#090;">сбросить все фильтры</a></td></tr>
	</table>
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

function get_rubric_tab($id_modul)
{
	$tab = 'rubrics';
	switch($id_modul)
	{
		case 3: $tab = 'spr'; break;
		case 5: $tab = 'art_rubrics'; break;
		case 8: $tab = 'srubrics'; break;
		case 17: $tab = 'gal_rubrics'; break;
		case 18: $tab = 'not_rubrics'; break;
	}
	return $tab;
}

function popup_modul()
{
	ob_start();
	?>    
  <div id="popup_window" style="display:none;">
  <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="16" width="100%" style="background-color:#d4dff2; border-bottom:1px solid #FFF;">
          <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center"><span id="popup_window_title" class="window_title"></span></td>
            </tr>
          </table>
      </td>
      <td width="16" style="padding:3px; background-color:#d4dff2; border-bottom:1px solid #FFF;">
          <img src="img/exit.png" border="0" align="absmiddle" style="cursor:pointer;" onclick="hide_popup_window()" />
      </td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF">
      <div id="popup_loader"><img src="img/loader.gif"></div>
      <iframe id="popup_frame"></iframe>
      </td>
    </tr>
  </table>
  </div>
  <script>$(function(){$.preloadImg("img/popup_loader.gif")})</script>
  <?	
	return ob_get_clean();
}

function stat_around($block)
{
	ob_start();
	?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab_subcontent">
      <tr>
        <td width="50" height="50"><img src="img/stat_left_up.jpg" border="0" alt="" /></td>
        <td style="background-image:url(img/stat_up.jpg); background-repeat:repeat-x;"></td>
        <td width="50"><img src="img/stat_right_up.jpg" border="0" alt="" /></td>
      </tr>
      <tr>
        <td style="background-image:url(img/stat_left.jpg); background-repeat:repeat-y;"></td>
        <th style="background-color:#ecf0fb;" valign="top"><?=$block?></th>
        <td style="background-image:url(img/stat_right.jpg); background-repeat:repeat-y;"></td>
      </tr>
      <tr>
        <td height="50"><img src="img/stat_left_down.jpg" border="0" alt="" /></td>
        <td style="background-image:url(img/stat_down.jpg); background-repeat:repeat-x;"></td>
        <td><img src="img/stat_right_down.jpg" border="0" alt="" /></td>
      </tr>
    </table>
    <?
	return ob_get_clean();
}

function show_stat_visit()
{
	global $prx;
	
	// за сегодня
	$date = date("Y-m-d");
	$day = getField("SELECT count(*) FROM {$prx}users_visit WHERE date='{$date}'");
	
	// за вчера
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
	$yesterday = getField("SELECT count(*) FROM {$prx}users_visit WHERE date='{$date}'");
	
	// за неделю
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-7,date("Y")));
	$week = getField("SELECT count(*) FROM {$prx}users_visit WHERE date>='{$date}'");
	
	// за месяц
	$date = date("Y-m-d", mktime(0,0,0,date("m")-1,date("d"),date("Y")));
	$month = getField("SELECT count(*) FROM {$prx}users_visit WHERE date>='{$date}'");
	
	// за год
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-1));
	$year = getField("SELECT count(*) FROM {$prx}users_visit WHERE date>='{$date}'");
	
	// всего
	$all = getField("SELECT count(*) FROM {$prx}users_visit");
	
	ob_start();
	?>
    <table width="100%" border="0" height="100%">
      <tr>
        <th style="color:#697079;font:normal 14px Tahoma, Geneva, sans-serif;" align="left">
          <? $date_start = getField("SELECT MIN(date) FROM {$prx}users_visit"); ?>
          Статистика посещений сайта c <?=date("d.m.Y", $date_start ? strtotime($date_start) : time())?>
        </th>
      </tr>
      <tr>
        <td colspan="2" valign="middle">
        
          <table class="tab_stat" cellpadding="5" style="margin:10px 0 10px 0;">
            <tr>
                <th>Сегодня</th>
                <th>Вчера</th>
                <th>Неделя</th>
                <th>Месяц</th>
                <th>Год</th>
                <th>Всего</th>
            </tr>
            <tr>
                <td style="color:#3e6aaa;"><b><?=(int)$day?></b></td>
                <td><?=(int)$yesterday?></td>
                <td><?=(int)$week?></td>
                <td><?=(int)$month?></td>
                <td><?=(int)$year?></td>
                <td style="color:#3e6aaa;"><b><?=(int)$all?></b></td>
            </tr>
          </table>

        </td>
      </tr>
      <tr>
      	<th style="text-align:right; font-weight:normal;">
        	<a href="" onclick="if(confirm('Вы действительно хотите удалить всю статистику?')) toajax('visit.php?action=del');return false;">удалить статистику</a>
        </th>
      </tr>
  	</table>
    <?
	return ob_get_clean();
}

function show_stat_order()
{
	global $prx;
	
	// за сегодня
	$date = date("Y-m-d");
	$day = getField("SELECT count(*) FROM {$prx}orders WHERE DATE_FORMAT(date,'%Y-%m-%d')='{$date}'");
	
	// за вчера
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
	$yesterday = getField("SELECT count(*) FROM {$prx}orders WHERE DATE_FORMAT(date,'%Y-%m-%d')='{$date}'");
	
	// за неделю
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-7,date("Y")));
	$week = getField("SELECT count(*) FROM {$prx}orders WHERE DATE_FORMAT(date,'%Y-%m-%d')>='{$date}'");
	
	// за месяц
	$date = date("Y-m-d", mktime(0,0,0,date("m")-1,date("d"),date("Y")));
	$month = getField("SELECT count(*) FROM {$prx}orders WHERE DATE_FORMAT(date,'%Y-%m-%d')>='{$date}'");
	
	// за год
	$date = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-1));
	$year = getField("SELECT count(*) FROM {$prx}orders WHERE DATE_FORMAT(date,'%Y-%m-%d')>='{$date}'");
	
	// всего
	$all = getField("SELECT count(*) FROM {$prx}orders");
	
	ob_start();
	?>
    <table width="100%" border="0">
      <tr>
        <th align="left">
          <? $date_start = getField("SELECT MIN(date) FROM {$prx}orders"); ?>
          <span class="rubric">Статистика заказов c <?=date("d.m.Y", $date_start ? strtotime($date_start) : time())?></span>
        </th>
      </tr>
      <tr>
        <td valign="middle">
        
          <table width="100%" class="tab_stat" cellpadding="5" style="margin:10px 0 0 0;">
            <tr>
                <th>Сегодня</th>
                <th>Вчера</th>
                <th>Неделя</th>
                <th>Месяц</th>
                <th>Год</th>
                <th>Всего</th>
            </tr>
            <tr>
                <td style="color:#3e6aaa;"><b><?=(int)$day?></b></td>
                <td><?=(int)$yesterday?></td>
                <td><?=(int)$week?></td>
                <td><?=(int)$month?></td>
                <td><?=(int)$year?></td>
                <td style="color:#3e6aaa;"><b><?=(int)$all?></b></td>
            </tr>
          </table>

        </td>
      </tr>
  	</table>
    <?
	return ob_get_clean();
}

function show_stat_count()
{
	global $prx;
	
	ob_start();
	?>
    <style type="text/css">
	.stat_count td
	{
		color:#15428b;
		font:normal 12px Tahoma, Geneva, sans-serif;
		padding:5px 0 0 0;
	}
	.comment
	{
		font:normal 12px Tahoma, Geneva, sans-serif;
		color:#697079;
	}
	.stat_count b
	{
		color:#069;
	}
    </style>    
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="stat_count">
   	  <tr><th align="left" style="padding-bottom:15px;"><span class="rubric">Общая статистика</span></th></tr>
      <tr><td>Кол-во страниц: <b><?=(int)getField("select count(*) from {$prx}pages");?></b> (<span class="comment">заблокированных:</span> <b><?=(int)getField("select count(*) from {$prx}pages where status=0")?></b>)</td></tr>
      <? /*<tr><td>Кол-во производителей: <b><?=(int)getField("select count(*) from {$prx}makers");?></b></td></tr>
      <tr><td>Кол-во рубрик в каталоге: <b><?=(int)getField("select count(*) from {$prx}rubrics");?></b></td></tr>
      <tr><td>Кол-во товаров: <b><?=(int)getField("select count(*) from {$prx}goods");?></b> (<span class="comment">заблокированных:</span> <b><?=(int)getField("select count(*) from {$prx}goods where status=0")?></b>)</td></tr>
      <tr><td>Кол-во заказов: <b><?=(int)getField("select count(*) from {$prx}orders");?></b> (<span class="comment">действующих:</span> <b><?=(int)getField("select count(*) from {$prx}orders where status=1")?></b>; <span class="comment">завершенных:</span> <b><?=(int)getField("select count(*) from {$prx}orders where status=2")?></b>)</td></tr>
      <tr><td>Кол-во новостей: <b><?=(int)getField("select count(*) from {$prx}news");?></b> (<span class="comment">заблокированных:</span> <b><?=(int)getField("select count(*) from {$prx}news where status=0")?></b>)</td></tr>
      <tr><td>Кол-во статей: <b><?=(int)getField("select count(*) from {$prx}articles");?></b> (<span class="comment">заблокированных:</span> <b><?=(int)getField("select count(*) from {$prx}articles where status=0")?></b>)</td></tr>
      <tr><td>Кол-во сообщений: <b><?=(int)getField("select count(*) from {$prx}messages");?></b></td></tr>
      <tr><td>Кол-во менеджеров: <b><?=(int)getField("select count(*) from {$prx}managers");?></b></td></tr>*/?>
      <tr><td>Кол-во пользователей: <b><?=(int)getField("select count(*) from {$prx}users");?></b> (<span class="comment">заблокированных:</span> <b><?=(int)getField("select count(*) from {$prx}users where status=0")?></b>)</td></tr>
    </table>
	<?
	return ob_get_clean();
}

function showCK($name,$text,$toolBar='full',$width="100%",$rows=20)
{
	?><textarea name="<?=$name?>" toolbar="<?=$toolBar?>" rows="<?=$rows?>" style="width:<?=$width?>"><?=$text?></textarea><?
}