<?
require('inc/common.php');

$h1 = 'Отзывы';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'reviews';

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];

	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$name) jAlert('Укажите название');
			if(!$text && !$youtube) jAlert('Введите текст отзыва или добавьте код видео');

			$set = "name='{$name}',
			        text=".($text ? "'{$text}'" : 'NULL').",
			        youtube=".($youtube ? "'{$youtube}'" : 'NULL').",
			        status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

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
		case 'status':
			update_flag($tbl,'status',$id);
			break;
		// ----------------- удаление банера
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v)
				remove_object($id);
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
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];

	$title .= ' :: ' . ($id ? $row['name'] . ' (редактирование)' : 'Добавление');
	$h = $id ? $row['name'] . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $row['name'] : 'Добавление');

	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Имя</th>
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
			<?=show_tr_images($id,'Фото','Для корректного отображения,<br>рекомендуется загружать квадратное изображение размером 320x320 пискелей',1,$tbl,$tbl)?>
      <tr>
        <th></th>
        <th>Текст отзыва</th>
        <td><?=showCK('text',$row['text'],'basic')?></td>
      </tr>
      <tr>
        <th class="tab_red_th"><?=help('Для корректного отображения плеера,<br>рекомендуется использовать размер видео 560x315 (ширина x высота)')?></th>
        <th>Код видео</th>
        <td><?=input('textarea', 'youtube', $row['youtube'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
    </table>
    <div class="frm-btns">
      <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="btn btn-success btn-sm" onclick="loader(true)" />&nbsp;
      <input type="button" value="Отмена" class="btn btn-default btn-sm" onclick="location.href='<?=$script?>'" />
    </div>
  </form>
	<?
	$content = arr($h, ob_get_clean());
}
// -----------------ПРОСМОТР-------------------
else
{
	$cur_page = (int)$_GET['page'] ?: 1;
	$fl['search'] = stripslashes($_GET['fl']['search']);

	$where = '';
	if($fl['search'] != ''){
		$sf = array('name','text');
		$w = '';
		foreach ($sf as $field){
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT * FROM {$prx}{$tbl}\r\nWHERE 1{$where}";

	$r = sql($query);
	$count_obj = @mysqli_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$count_page = ceil($count_obj/$count_obj_on_page); // количество страниц

	ob_start();
	// проверяем текущую сортировку и формируем соответствующий запрос
	if($fl['sort']){
		foreach ($fl['sort'] as $f => $t){
			$query .= " ORDER BY {$f} {$t}";
			break;
		}
	} else {
		$query .= ' ORDER BY name';
	}

	show_listview_btns('Добавить::Удалить');
	ActiveFilters();
  ?>
  <div class="clearfix"></div>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form name="red_frm" method="post" target="ajax">
    <table class="table-list">
      <thead>
      <tr>
        <th style="width:1%"><input type="checkbox" name="check_del" id="check_del" /></th>
        <th style="width:1%">№</th>
        <th style="width:1%; text-align:center;"><img src="img/image.png" title="Фото" /></th>
        <th><?=SortColumn('Имя','name')?></th>
        <th width="50%">Отзыв</th>
        <th width="50%">Видео</th>
        <th nowrap>Статус</th>
        <th style="padding:0 30px;"></th>
      </tr>
      </thead>
      <tbody>
			<?
			$res = sql($query);
			if(mysqli_num_rows($res)){
				$i=1;
				while($row = mysqli_fetch_assoc($res)){
					$id = $row['id'];
					?>
          <tr id="item-<?=$row['id']?>">
            <th><input type="checkbox" name="del[<?=$id?>]"></th>
            <th nowrap><?=$i++?></th>
            <th>
							<?
							$src = '/uploads/no_photo.jpg';
							$big_src = '/uploads/no_photo.jpg';
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
								$src = "/{$tbl}/65x65/{$id}.jpg";
								$big_src = "/{$tbl}/{$id}.jpg";
							}
							?>
              <a href="<?=$big_src?>" class="blueimp" title="<?=htmlspecialchars($row['name'])?>">
                <img src="<?=$src?>" align="absmiddle" style="max-height:65px; max-width:65px;" class="img-circle">
              </a>
            </th>
            <td class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
            <td><?=$row['text']?></td>
            <th><?
              $ycode = preg_replace('/width=\"[0-9]+\"/', 'width="100%"', $row['youtube']);
							$ycode = preg_replace('/height=\"[0-9]+\"/', '', $ycode);
              echo $ycode;
            ?></th>
            <th><?=btn_flag($row['status'],$id,'action=status&id=')?></th>
            <th nowrap><?=btn_edit($id)?></th>
          </tr>
					<?
				}
			} else {
				?>
        <tr class="nofind">
          <td colspan="10">
            <div class="bg-warning">
              по вашему запросу ничего не найдено.
							<?=help('нет ни одной записи отвечающей критериям вашего запроса,<br>возможно вы установили неверные фильтры')?>
            </div>
          </td>
        </tr>
				<?
			}
			?>
      </tbody>
    </table>
  </form>
	<?=pagination($count_page, $cur_page, true, 'padding:10px 0 0;')?>
	<?
	$content = arr($h, ob_get_clean());
}
require('tpl/template.php');