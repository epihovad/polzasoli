<?
require('inc/common.php');

$h1 = 'Статьи';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$tbl = 'articles';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];

	switch($_GET['action'])
	{
	  // ----------------- сохранение
		case 'saveall':
			updateSitemap();
			jAlert('Данные успешно сохранены');
			break;
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$name) jAlert('Укажите название');

			$updateLink = false;
			$where = $id ? " and id<>{$id}" : "";

			if($link){
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			} else {
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			}

			$set = "name='{$name}',
							preview='{$preview}',
							text='{$text}',
							ids_disease=".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
							status='{$status}',
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);

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
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
			<tr>
        <th><?=help('ссылка формируется автоматически,<br>значение данного поля можно изменить')?></th>
        <th>Ссылка</th>
        <td><?=input('text', 'link', $row['link'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Краткое<br />описание</th>
        <td><?=showCK('preview',$row['preview'],'basic')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Текст</th>
        <td><?=showCK('text',$row['text'])?></td>
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
        <td><?=input('text', 'h1', $row['h1'])?></td>
      </tr>
			<? foreach (array('title','keywords','description') as $v){?>
        <tr>
          <th></th>
          <th><?=$v?></th>
          <td><?=input('text', $v, $row[$v])?></td>
        </tr>
			<?}?>
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
	$count_obj_on_page = 30; // кол-во объектов на странице
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
		$query .= ' ORDER BY A.id DESC';
	}
	$query .= ' LIMIT ' . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;
	//-----------------------------
	//echo $query;

	show_listview_btns(($sitemap ? 'Сохранить::' : '') . 'Добавить::Удалить');
	ActiveFilters();

	if(!$sitemap){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="RegSessionSort(REQUEST_URI,'sitemap');return false;">Sitemap поля</a></div>
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
        <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','A.name')?></th>
				<? if($sitemap){?>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
          <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
				<? }?>
        <th nowrap width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link');?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status');?></th>
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
            <td class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
						<? if($sitemap){?>
              <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
              <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
              <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
						<? }?>
            <td>/articles/<a href="/articles/<?=$row['link']?>.htm" class="clr-green" target="_blank"><?=$row['link']?></a>.htm</td>
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