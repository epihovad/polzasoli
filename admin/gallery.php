<?
require('inc/common.php');

$rubric = 'Фотогелерея';
$tbl = 'gallery';
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

			if(!$name) errorAlert('необходимо указать название !');

			$where = $id ? " AND id <> '{$id}'" : '';
			if(getField("SELECT id FROM {$prx}{$tbl} WHERE name='{$name}'{$where}")){
			  errorAlert('Объект с таким названием уже существует');
      }

			$set = "id_catalog='{$id_catalog}',
							name='{$name}',
							status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			// загружаем картинки
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
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка вверх
		case 'moveup':
			$id_catalog = gtv($tbl,'id_catalog',$id);
			sort_moveup($tbl,$id,"id_catalog = '{$id_catalog}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			$id_catalog = gtv($tbl,'id_catalog',$id);
			sort_movedown($tbl,$id,"id_catalog = '{$id_catalog}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
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
        <th>Рубрика</th>
        <td><?=dllTree("SELECT * FROM {$prx}{$tbl}_catalog ORDER BY sort,id", 'name="id_catalog" style="width:100%"', $row['id_catalog'], array('0'=>'без рубрики'), $id)?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Название</th>
        <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
      </tr>
			<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
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
	$f_catalog = (int)@$_SESSION['ss']['gallery_catalog'];
	$f_context = stripslashes($_SESSION['ss']['context']);

	$where = '';
	if($f_catalog)
	{
		$ids = getIdChilds("SELECT * FROM {$prx}{$tbl}_catalog",$f_catalog,false);
		$where .= " AND id_catalog IN ({$ids})";
	}
	if($f_context!='')	$where .= " AND name LIKE '%{$f_context}%'";

	$page_title .= ' :: '.$rubric;
	$rubric .= ' &raquo; Общий список';

	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);

	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";

	$r = sql($query);
	$count_obj = (int)@mysqli_num_rows($r); // кол-во объектов в базе
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
		$query .= ' ORDER BY sort,id';
	//-----------------------------
	//echo $query;

	show_filters($script);
	show_navigate_pages($count_pages,$cur_page,$script);
	?>

  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
      <td align="left">Рубрика <?=help('отображаются объекты выбранной рубрики<br>(вместе с объектами подчинённых рубрик)')?></td>
      <td colspan="2"><?=dllTree("SELECT * FROM {$prx}{$tbl}_catalog ORDER BY sort,id",'style="width:100%" onChange="RegSessionSort(\''.$script.'\',\'gallery_catalog=\'+this.value);return false;"',$f_catalog,array('remove'=>'-- все --'))?></td>
    </tr>
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
        <th nowrap style="width:<?=$f_catalog?'100':'50'?>%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Наименование','name')?></th>
				<? if(!$f_catalog){ ?><th nowrap style="width:100%">Рубрика</th><? }?>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
				<? if(!$_SESSION['ss']['sort']) { ?><th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th><? }?>
        <th style="padding:0 30px;"></th>
      </tr>
			<?
			$res = sql($query);
			if(@mysqli_num_rows($res))
			{
				$i=1;
				while($row = mysqli_fetch_assoc($res))
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
              <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)">
                <img src="<?=$src?>" align="absmiddle" style="max-height:75px" />
              </a>
            </th>
            <td><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
						<? if(!$f_catalog){ ?>
              <td nowrap><?
							$tree = '';
							if($row['id_catalog']){
								$ids_catalog = getArrParents("SELECT id,id_parent FROM {$prx}{$tbl}_catalog WHERE id='%s'",$row['id_catalog']);
								$tree = '';
								foreach($ids_catalog as $id_catalog)
								{
									ob_start();
									?><a href="/<?=$tbl?>_catalog.php?red=<?=$id_catalog?>" style="color:#090"><?=gtv($tbl.'_catalog','name',$id_catalog)?></a><?
									$tree .= ($tree?' &raquo; ':'').ob_get_clean();
								}
								echo $tree;
							} else {
							  ?>без рубрики<?
              }
							?></td><?
						}?>
            <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=')?></td>
						<?
						if(!$_SESSION['ss']['sort'])
							echo "<td nowrap align='center'>".btn_sort($id)."</td>";
						?>
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