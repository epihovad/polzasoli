<?
require('inc/common.php');

$h1 = 'Фото-рубрикатор';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>Общий список';
$tbl = 'gallery_catalog';
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

			if(!$name) jAlert('необходимо указать название !');

			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';

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

			$set = "id_parent='{$id_parent}',
							name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

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
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v) {
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
        <th>Подчинение</th>
        <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id",'name="id_parent"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
      </tr>
      <tr>
        <th></th>
        <th>Название</th>
        <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"></td>
      </tr>
      <tr>
        <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
        <th>Ссылка</th>
        <td><input type="text" class="form-control input-sm" name="link" value="<?=htmlspecialchars($row['link'])?>"></td>
      </tr>
			<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
      <tr>
        <th></th>
        <th>Текст</th>
        <td><?=showCK('text',$row['text'])?></td>
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
	$sitemap = isset($_SESSION['ss']['sitemap']);

	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($sitemap)
	{
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else $query  = sprintf($query,'');

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
		$query .= ' ORDER BY A.sort,A.id';
	//-----------------------------
	//echo $query;

	show_listview_btns(($sitemap ? 'Сохранить::' : '') . 'Добавить::Удалить');
	show_filters($script);

	if(!$sitemap){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
	<? } ?>

  <div class="clearfix"></div>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
    <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
    <table class="table-list">
      <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
				<? if(!$_SESSION['ss']['sort']) { ?><th nowrap><?=help('параметр с помощью которого можно изменить<br>порядок вывода элементов в клиентской части сайта')?></th><? }?>
        <th><img src="img/image.png" title="изображение" /></th>
        <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name')?></th>
				<? if($sitemap){?>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
				<? }?>
        <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
        <th style="padding:0 30px;"></th>
      </tr>
      </thead>
      <tbody>
			<?
			$mas = getTree($query);
			if(sizeof($mas))
			{
				$i=1;
				?><?
				foreach($mas as $vetka)
				{
					$row = $vetka['row'];
					$level = $vetka['level'];

					$id = $row['id'];
					$link = '/gallery/' . $row['link'] . '/';
					$prfx = $prefix===NULL ? getPrefix($level) : str_repeat($prefix, $level);
					$childs = getIdChilds("SELECT * FROM {$prx}{$tbl}", $id);
					$has_childs = sizeof($childs) > 1;

					?>
          <tr id="item-<?=$id?>" oid="<?=$id?>" par="<?=$row['id_parent']?>" class="<?=$has_childs?' has-childs':''?>">
            <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>"></th>
            <th nowrap><?=$i++?></th>
						<? if(!$_SESSION['ss']['sort']){ ?><th nowrap align="center"><i class="fas fa-sort"></i></th><? }?>
            <th style="padding:3px 5px;">
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
            <td><?=$prfx?><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
						<? if($sitemap){?>
              <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
              <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
              <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
						<? }?>
            <td>/gallery/<a href="<?=$link?>" class="clr-green" target="_blank"><?=$row['link']?></a>/</td>
            <th><?=btn_flag($row['status'],$id,'action=status&id=')?></th>
            <th nowrap><?=btn_edit($id)?></th>
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
      </tbody>
    </table>
  </form>
	<?
	$content = arr($h, ob_get_clean());
}
require('tpl/template.php');