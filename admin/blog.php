<?
require('inc/common.php');

$rubric = 'Блог';
$tbl = 'blog';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET["action"]))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'saveall':
			updateSitemap();
			?><script>alert('Данные успешно сохранены');top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$value)
				$$key = clean($value);
			
			if(!$name) errorAlert('необходимо указать название !');
			
			$where = $id ? " and id<>{$id}" : "";	
			if($link)
			{
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					errorAlert('объект с данной ссылкой уже существует !');
			}
			else
			{
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					errorAlert('ссылка автоматически сформированна - '.$link.',\nно объект с данной ссылкой уже существует!');
			}
			
			$date_change = false;
			// если дата поста изменилась
			if($date!=$old_date)
			{
				$date = $date ? formatDateTime($date.' '.date("H:i:s")) : date("Y-m-d H:i:s");
				$date_change = true;
			}
			
			$set = "name='{$name}',
							preview='{$preview}',
							text='{$text}',
							status='{$status}',
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			$set .= $date_change ? ",`date`='{$date}'" : '';
			if(!$updateLink) $set .= ",link='{$link}'";
			
			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);

			// загружаем картинку
			if($_FILES['img']['name'])
			{
				$fname = "{$id}.jpg";
				remove_img($id); // удаляем старую картинку
				@move_uploaded_file($_FILES['img']['tmp_name'],$_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$fname}");
				@chmod($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$fname}",0644);
			}
			
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
    case 'to_index':
    case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_img($id);
			sql("DELETE FROM {$prx}sitemap WHERE id_obj='{$id}' AND `type`='{$tbl}'");
			update($tbl,'',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
			{
				remove_img($id);
				sql("DELETE FROM {$prx}sitemap WHERE id_obj='{$id}' AND `type`='{$tbl}'");
				update($tbl,'',$id);
			}
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление изображения
		case 'pic_del':
			remove_img($id);
			?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
			break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <input type="hidden" name="old_date" value="<?=date("d.m.Y",strtotime($row['date']))?>">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('ссылка формируется автоматически,<br>значение данного поля можно изменить')?></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'])?></td>
    </tr>
    <tr>
			<th class="tab_red_th"></th>
			<th>Дата</th>
			<td><input type="text" class="datepicker" name="date" value="<?=(isset($row['date']) ? date("d.m.Y",strtotime($row['date'])) : date("d.m.Y"))?>" /></td>
		</tr>
    <?=show_tr_img('img',"/uploads/{$tbl}/","{$id}.jpg",$script."?action=pic_del&id={$id}")?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Краткое<br />описание</th>
      <td><?=showFck('preview',$row['preview'],'medium','100%',20)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Текст</th>
      <td><?=showFck('text',$row['text'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>title</th>
      <td><?=show_pole('text','title',htmlspecialchars($row['title']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>keywords</th>
      <td><?=show_pole('text','keywords',htmlspecialchars($row['keywords']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>description</th>
      <td><?=show_pole('textarea','description',$row['description'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th></th>
      <td align="center">
        <input type="submit" value="<?=($id?'Сохранить':'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
        <input type="button" value="Отмена" class="but1" onclick="location.href='<?=$script?>'" />
      </td>
    </tr>
  </table>
  </form>
  <?	
	$content = ob_get_clean();
}
// -----------------ПРОСМОТР-------------------
else
{
	$cur_page = $_SESSION['ss']['page'] ? $_SESSION['ss']['page'] : 1;
	$sitemap = isset($_SESSION['ss']['sitemap']);
	$f_context = stripslashes($_SESSION['ss']['context']);
	
	$where = '';
	if($f_context!='')	$where .= " AND (	name LIKE '%{$f_context}%' OR
																				preview LIKE '%{$f_context}%' OR
																				text LIKE '%{$f_context}%' )";
	
	$page_title .= " :: ".$rubric; 
	$rubric .= " &raquo; Общий список"; 
	
	if($sitemap) $razdel['Сохранить'] = "javascript:saveall();";
	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);
	
	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($sitemap)
	{
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else $query  = sprintf($query,'');
	
	$query .= " WHERE 1{$where}";
	
	$r = sql($query);
	$count_obj = @mysql_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц

	ob_start();
	// проверяем текущую сортировку
	// и формируем соответствующий запрос
	if($_SESSION['ss']['sort']) 
	{
		$sort = explode(':',$_SESSION['ss']['sort']);
		$cur_pole = $sort[0];
		$cur_sort = $sort[1];

		$query .= " ORDER BY {$cur_pole} ".($cur_sort=='up'?'DESC':'ASC');
	}
	else
		$query .= ' ORDER BY date DESC,id DESC';
	$query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
	//-----------------------------
	//echo $query;
	
	show_filters($script);
	show_navigate_pages($kol_str,$cur_page,$script);
	
	?>
  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
			<td>контекстный поиск</td>
			<td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
			<td><a id="searchBtn" href="" class="link">найти</a></td>
		</tr>
	</table>
  
  <? if(!$sitemap){ ?>
	<div style="padding:5px 0 0 0;">Отобразить <a href="" style="color:#F60" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
  <div class="clear"></div>
  <? } ?>
  
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table width="100%" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th><img src="img/image.png" title="изображение" /></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Дата','date');?></th>
      <th nowrap width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name');?></th>
      <? if($sitemap){?>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
			<? }?>
      <th nowrap width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link');?></th>
      <th nowrap>На главную</th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status');?></th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = mysql_query($query);
	if(@mysql_num_rows($res))
	{
		$i=1;
		while($row = mysql_fetch_array($res))
		{
			$id = $row['id'];
			?>
			<tr id="row<?=$id?>">
			<th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
			<th><?=$i?></th>
      <th style="padding:3px 5px;">
        <?
        $src = '/uploads/no_photo.jpg';
        $big_src = '/uploads/no_photo.jpg';
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
          $src = "/uploads/{$tbl}/45x45/{$id}.jpg";
          $big_src = "/uploads/{$tbl}/{$id}.jpg";
        }
        ?>
        <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)">
          <img src="<?=$src?>" align="absmiddle" style="max-height:45px" />
        </a>
      </th>
      <td style="font-size:11px"><?=date('d.m.Y',strtotime($row['date']))?></td>
			<td class="sp"><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
      <? if($sitemap){?>
      <th class="sitemap"><input type="text" class="datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
      <th class="sitemap"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
      <th class="sitemap"><input type="text" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" style="text-align:center; width:30px;" /></th>
			<? }?>
      <td>/<?=$tbl?>/<a href="/<?=$tbl?>/<?=$row['link']?>.htm" style="color:#090" target="_blank"><?=$row['link']?></a>.htm</td>
      <td align="center"><?=btn_flag($row['to_index'],$id,'action=to_index&id=')?></td>
      <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=')?></td>
			<td nowrap align="center"><?=btn_edit($id)?></td>
			</tr>
			<?
			$i++;
		}		
	}
	else
	{
		?>
    <tr>
      <td colspan="10" align="center">
      по вашему запросу ничего не найдено. <?=help('нет ни одной записи отвечающей критериям вашего запроса,<br>возможно вы установили неверные фильтры')?>
      </td>
    </tr>
    <?
	}
	?>
  </table>
  </form>
  <?	
	$content = $subcontent.ob_get_clean();
}

require('tpl/tpl.php');