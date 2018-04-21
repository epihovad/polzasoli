<?
require('inc/common.php');

$h1 = 'Тип абонемента';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$tbl = 'tickets_type';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];

	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$name) errorAlert('необходимо указать название !');

			if($locked)
			{
				$set = "preview=".($preview?"'{$preview}'":"NULL").",
				        text=".($text?"'{$text}'":"NULL").",
								h1=".($h1?"'{$h1}'":"NULL").",
								title=".($title?"'{$title}'":"NULL").",
								keywords=".($keywords?"'{$keywords}'":"NULL").",
								description=".($description?"'{$description}'":"NULL");
				update($tbl,$set,$id);
				?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
				exit;
			}

			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';

			if($type=='page')
			{
				if($link)
				{
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
				else
				{
					$link = makeUrl($name);
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
			}

			$set = "id_parent='{$id_parent}',
							name='{$name}',
							preview=".($preview?"'{$preview}'":"NULL").",
				      text=".($text?"'{$text}'":"NULL").",
							ids_disease=".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
							type='{$type}',
							is_main='{$is_main}',
							is_slider='{$is_slider}',
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);

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
		// ----------------- обновление в меню
		case 'is_main':
		case 'is_slider':
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка
		case 'sort':
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");

			$i = 1;
			$errors = 0;
			foreach ($_POST['item'] as $id){
				if(!update($tbl, "`sort`={$i}", $id)){
					$errors++;
					continue;
				}
				$i++;
			}
			if(!$errors){
				echo json_encode(array('status' => 'ok', 'message' => 'success update ' . sizeof($_POST['item']) . ' items'));
			} else {
				echo json_encode(array('status' => 'error', 'message' => 'произошла ошибка'));
			}
			break;
		// ----------------- сортировка вверх
		case 'moveup':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_moveup($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_movedown($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			if(gtv($tbl,'locked',$id))
				errorAlert("данная страница защищена от удаления!");
			else
				remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				if(!gtv($tbl,'locked',$id))
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
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];
	$locked = $row['locked'];
	$readonly = $locked ? ' readonly' : '';

	$title .= ' :: ' . ($id ? $row['name'] . ' (редактирование)' : 'Добавление страницы');
	$h = $id ? $row['name'] . ' <small>(редактирование)</small>' : 'Добавление страницы';
	$navigate = '<span></span><a href="' . $script . '">Страницы</a><span></span>' . ($id ? $row['name'] : 'Добавление страницы');

	ob_start();
	?>
  <link rel="stylesheet" href="/js/chosen/chosen.min.css">
  <style>
    .for-page { display:<?=$row['type']=='link'?'none':'table-row'?>;}
    .chosen-search-input { width:auto !important;}
  </style>
  <script src="/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
  <script>
    $(function () {
      //
      $('select[name="type"]').change(function () {
        var val = $(this).find('option:selected').val();
        if(val=='link'){ $('.for-page').hide(); }
        else { $('.for-page').show(); }
      });
      //
      $('select[name="ids_disease\[\]"]').chosen({
        no_results_text: "Нет данных по запросу",
      });
    })
  </script>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <input type="hidden" name="locked" value="<?=$locked?>" />
    <table class="table-edit">
			<? if(!$locked){ ?>
        <tr>
          <th></th>
          <th>Подчинение</th>
          <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id",'name="id_parent"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
        </tr>
			<? } ?>
      <tr>
        <th></th>
        <th>Название</th>
        <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"<?=$readonly?>></td>
      </tr>
      <tr>
        <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
        <th>Ссылка</th>
        <td><input type="text" class="form-control input-sm" name="link" value="<?=htmlspecialchars($row['link'])?>"<?=$readonly?>></td>
      </tr>
			<?/*<tr>
      <th></th>
      <th>Дата</th>
      <td><input type="text" class="form-control input-sm datepicker"></td>
    </tr>*/?>
			<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
      <tr>
        <th></th>
        <th>Краткое<br />описание</th>
        <td><?=showCK('preview',$row['preview'],'basic','100%',20)?></td>
      </tr>
      <tr>
        <th></th>
        <th>Текст</th>
        <td><?=showCK('text',$row['text'])?></td>
      </tr>
      <tr class="for-page">
        <th><?=help('Привязка к объектам из спр-ка болезней<br>для вывода на сайте (в нижней части) соответствующих статей')?></th>
        <th>Спр-к болезней</th>
        <td><?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="ids_disease[]" multiple data-placeholder="Укажите болезни" style="width:100%"',explode(',',$row['ids_disease']))?></td>
      </tr>
			<?
			if(!$locked)
			{
				?>
        <tr>
          <th></th>
          <th>Тип</th>
          <td><?=dll(array('page'=>'страница','link'=>'ссылка'),' name="type"',$row['type'])?></td>
        </tr>
        <tr>
          <th><?=help('отображать объект в главном меню')?></th>
          <th>Главное меню</th>
          <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_main"',$row['is_main'])?></td>
        </tr>
        <tr>
          <th></th>
          <th>Статус</th>
          <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
        </tr>
				<?
			}
			?>
      <tr class="for-page">
        <th><?=help('отображать объект в слайдере<br>на главной странице')?></th>
        <th>В слайдер</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_slider"',$row['is_slider'])?></td>
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
	$navigate = '<span></span>Общий список';

	$query = "SELECT * FROM {$prx}{$tbl} ORDER BY name";

	ob_start();

	show_listview_btns('Добавить::Удалить');

	?>

  <div class="clearfix"></div>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
        <th width="100%">Тип</th>
        <th nowrap>Статус</th>
        <th style="padding:0 30px;"></th>
      </tr>
    </thead>
    <tbody>
    <?
    $res = sql($query);
    if(@mysqli_num_rows($res))
    {
      ?><?
      $i=1;
      while($row = mysqli_fetch_array($res))
      {
        $id = $row['id'];
				?>
        <tr id="item-<?=$id?>">
          <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>"></th>
          <th nowrap><?=$i++?></th>
          <td><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
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
	<?
	$content = arr($h, ob_get_clean());
}

require('tpl/template.php');