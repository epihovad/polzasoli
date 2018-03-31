<?
require('inc/common.php');

$rubric = 'Мини баннеры';
$tbl = 'banner_mini';
$id = (int)$_GET['id'];

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];

	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$value)
				$$key = clean($value);

			if(!$name) errorAlert('необходимо указать заголовок !');

			$set = "`name`='{$name}',
			        `text`='{$text}',
			        `link`='{$link}',
			        `is_through`='{$is_through}',
							`status`='{$status}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			// загружаем картинку
			if(sizeof((array)$_FILES[$tbl]['name']))
			{
				foreach($_FILES[$tbl]['name'] as $num=>$null)
				{
					if(!$_FILES[$tbl]['name'][$num]) continue;

					remove_img($id, $tbl);
					$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg";
					@move_uploaded_file($_FILES[$tbl]['tmp_name'][$num],$path);
					@chmod($path,0644);

					break;
				}
			}

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'is_through':
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id, array($tbl));
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v){
				remove_object($id);
			}
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление изображения
		case 'img_del':
			remove_img($id,$tbl);
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
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
      <tr>
        <th class="tab_red_th"></th>
        <th>Название</th>
        <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Текст</th>
        <td><?=show_pole('text','text',htmlspecialchars($row['text']))?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Ссылка</th>
        <td><?=show_pole('text','link',htmlspecialchars($row['link']))?></td>
      </tr>
			<?=show_tr_images($id,'Изображение','рекомендуется загружать изображение не более 125 пикселей',1,$tbl,$tbl)?>
      <tr>
        <th class="tab_red_th"></th>
        <th>Сквозной</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_through"',isset($row['is_through'])?$row['is_through']:0)?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th></th>
        <td align="center">
          <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
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
	$f_context = stripslashes($_SESSION['ss']['context']);

	$where = '';
	if($f_context!='')	$where .= " AND (name LIKE '%{$f_context}%' OR `text` LIKE '%{$f_context}%')";

	$page_title .= ' :: '.$rubric;
	$rubric .= ' &raquo; Общий список';

	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);

	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";

	$r = sql($query);
	$count_obj = (int)@mysql_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$count_pages = ceil($count_obj/$count_obj_on_page); // количество страниц

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
		$query .= ' ORDER BY name';
	//-----------------------------
	//echo $query;

	show_filters($script);
	show_navigate_pages($count_pages,$cur_page,$script);
	?>

  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
      <td>контекстный поиск</td>
      <td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
      <td><a id="searchBtn" href="" class="link">найти</a></td>
    </tr>
  </table>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
    <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
    <table width="100%" cellspacing="0" cellpadding="0" class="tab1">
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
        <th><img src="img/image.png" title="изображение" /></th>
        <th nowrap style="width:50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Заголовок','name')?></th>
        <th nowrap style="width:50%">Ссылка</th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Сквозной','is_through')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
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
            <th nowrap><?=$i++?></th>
            <th style="padding:3px 5px;">
							<?
							$src = '/uploads/no_photo.jpg';
							$big_src = '/uploads/no_photo.jpg';
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
								$src = "/uploads/{$tbl}/100x-/{$id}.jpg";
								$big_src = "/uploads/{$tbl}/{$id}.jpg";
							}
							?>
              <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)" style="background-color:#444351; display:block;">
                <img src="<?=$src?>" align="absmiddle" style="max-height:75px" />
              </a>
            </th>
            <td class="sp"><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a><br><?=$row['text']?></td>
            <td><a href="<?=$row['link']?>" target="_blank" style="color:#090"><?=$row['link']?></a></td>
            <td align="center"><?=btn_flag($row['is_through'],$id,'action=is_through&id=')?></td>
            <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=')?></td>
            <td nowrap align="center"><?=btn_edit($id)?></td>
          </tr>
					<?
				}
			} else {
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