<?
require('inc/common.php');

$h1 = 'Абонементы';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$tbl = 'tickets';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

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

			$set = "name = '{$name}',
			        text = ".($text ? "'{$text}'" : 'NULL').",
			        price = '{$price}',
			        old_price = '{$old_price}',
			        validity = ".($validity ? "'{$validity}'" : 'NULL').",
			        age = '{$age}',
			        ids_type = ".(sizeof($_POST['ids_type']) > 0 ? "'".implode(',', $_POST['ids_type'])."'" : 'NULL').",
			        ids_who = ".(sizeof($_POST['ids_who']) > 0 ? "'".implode(',', $_POST['ids_who'])."'" : 'NULL').",
			        ids_disease = ".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
			        status = '{$status}',
							h1 = " . ($h1 ? "'{$h1}'" : "NULL") . ",
							title = " . ($title ? "'{$title}'" : "NULL") . ",
							keywords = " . ($keywords ? "'{$keywords}'" : "NULL") . ",
							description = " . ($description ? "'{$description}'" : "NULL");

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
			foreach($_POST['check_del_'] as $id=>$v)
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
        <th>Название</th>
        <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"></td>
      </tr>
      <?=show_tr_images($id,'Фото','Для корректного отображения,<br>рекомендуется загружать квадратное изображение размером 320x320 пискелей',1,$tbl,$tbl)?>
      <tr>
        <th></th>
        <th>Описание</th>
        <td><textarea class="form-control input-sm" name="text"><?=$row['text']?></textarea></td>
      </tr>
      <tr>
        <th></th>
        <th>Цена</th>
        <td><input type="text" class="form-control input-sm" name="price" value="<?=$row['price']?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>Старая цена</th>
        <td><input type="text" class="form-control input-sm" name="old_price" value="<?=$row['old_price']?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>Срок действия</th>
        <td><input type="text" class="form-control input-sm" name="validity" value="<?=$row['validity']?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>Возрастные ограничения</th>
        <td><?=dllEnum($tbl,'age','name="age" class="form-control input-sm"',$row['age'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Тип абонемента</th>
        <td><?=dll("SELECT * FROM {$prx}tickets_type ORDER BY name",'name="ids_type[]" multiple data-placeholder="Укажите тип абонемента" style="width:100%"',explode(',',$row['ids_type']),null,'chosen')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Тип посетителей</th>
        <td><?=dll("SELECT * FROM {$prx}tickets_who ORDER BY name",'name="ids_who[]" multiple data-placeholder="Укажите тип посетителей" style="width:100%"',explode(',',$row['ids_who']),null,'chosen')?></td>
      </tr>
      <tr>
        <th><?=help('Привязка к объектам из спр-ка болезней<br>для вывода на сайте (в нижней части) соответствующих статей')?></th>
        <th>Спр-к болезней</th>
        <td><?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="ids_disease[]" multiple data-placeholder="Укажите болезни" style="width:100%"',explode(',',$row['ids_disease']),null,'chosen')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
      <tr>
        <th><?=help('используется вместо названия в &lt;h1&gt;')?></th>
        <th>Заголовок</th>
        <td><input type="text" class="form-control input-sm" name="h1" value="<?=htmlspecialchars($row['h1'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>title</th>
        <td><input type="text" class="form-control input-sm" name="title" value="<?=htmlspecialchars($row['title'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>keywords</th>
        <td><input type="text" class="form-control input-sm" name="keywords" value="<?=htmlspecialchars($row['keywords'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>description</th>
        <td><textarea class="form-control input-sm" name="description"><?=$row['description']?></textarea></td>
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
	$sitemap = isset($_SESSION['ss']['sitemap']);
	$f_context = stripslashes($_SESSION['ss']['context']);

	$where = '';
	if($f_context!='')	$where .= " AND (	name LIKE '%{$f_context}%' OR
																				text LIKE '%{$f_context}%' )";

	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($sitemap)
	{
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else $query  = sprintf($query,'');

	$query .= " WHERE 1{$where}";

	$r = sql($query);
	$count_obj = @mysqli_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 3; // кол-во объектов на странице
	$count_page = ceil($count_obj/$count_obj_on_page); // количество страниц

	ob_start();
	// проверяем текущую сортировку
	// и формируем соответствующий запрос
	if($_SESSION['ss']['sort']) {
		$sort = explode(':',$_SESSION['ss']['sort']);
		$cur_pole = $sort[0];
		$cur_sort = $sort[1];
		$query .= " ORDER BY {$cur_pole} ".($cur_sort=='up'?'DESC':'ASC');
	} else {
		$query .= ' ORDER BY A.name';
	}
	$query .= ' LIMIT ' . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;
	//-----------------------------
	//echo $query;

	show_listview_btns(($sitemap ? 'Сохранить::' : '') . 'Добавить::Удалить');
	show_filters($script);

	if(!$sitemap){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
	<? } ?>

  <div class="clearfix"></div>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
    <tr>
      <th style="width:1%"><input type="checkbox" name="check_del" id="check_del" /></th>
      <th style="width:1%">№</th>
      <th style="width:1%; text-align:center;"><img src="img/image.png" title="изображение" /></th>
      <th width="40%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','A.name')?></th>
			<? if($sitemap){?>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
			<? }?>
      <th>Цена, руб.</th>
      <th>Срок действия</th>
      <th>Возраст</th>
      <th>Типы абонемента</th>
      <th>Типы посетителей</th>
      <th>Заболевания</th>
      <th nowrap>Статус</th>
      <th style="padding:0 30px;"></th>
    </tr>
    </thead>
    <tbody>
  <?
	$res = sql($query);
	if(mysqli_num_rows($res)){
		$i=1;
		while($row = mysqli_fetch_assoc($res))
		{
			$id = $row['id'];
			?>
			<tr id="item-<?=$row['id']?>">
			  <th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
			  <th nowrap><?=$i++?></th>
        <th>
					<?
					$src = '/uploads/no_photo.jpg';
					$big_src = '/uploads/no_photo.jpg';
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
						$src = "/{$tbl}/60x60/{$id}.jpg";
						$big_src = "/{$tbl}/{$id}.jpg";
					}
					?>
          <a href="<?=$big_src?>" class="blueimp" title="<?=htmlspecialchars($row['name'])?>">
            <img src="<?=$src?>" align="absmiddle" style="max-height:60px; max-width:60px;" class="img-rounded">
          </a>
        </th>
        <td class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
				<? if($sitemap){?>
          <th class="sitemap"><input type="text" class="datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
          <th class="sitemap"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
          <th class="sitemap"><input type="text" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" style="text-align:center; width:30px;" /></th>
				<? }?>
        <th nowrap><?=$row['price'] . ($row['old_price'] ? ' (<s>'.$row['old_price'].'</s>)' : '')?></th>
        <th nowrap><?=$row['validity']?></th>
        <th nowrap><?=$row['age']?></th>
        <td><?
          $q = sql("SELECT * FROM {$prx}tickets_type where id IN (" . ($row['ids_type'] ?: '0' ) . ")");
          while ($arr = @mysqli_fetch_assoc($q)){
            ?><small><?=$arr['name']?></small><?
          }
        ?></td>
        <td></td>
        <td></td>
        <th><?=btn_flag($row['status'],$id,'action=status&id=')?></th>
        <th nowrap><?=btn_edit($id)?></th>
			</tr>
			<?
		}
	} else {
		?>
    <tr class="nofind">
      <td colspan="15">
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