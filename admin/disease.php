<?
require('inc/common.php');

$h1 = 'Справочник болезней';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>Общий список';
$tbl = 'disease';
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
			
			$set = "name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";
				
			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?		
			break;
		// ----------------- обновление в меню
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v){
				remove_object($id);
      }
			?><script>top.location.href = '<?=$script?>'</script><?
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
      <th>Название</th>
      <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"></td>
    </tr>
    <tr>
      <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
      <th>Ссылка</th>
      <td><input type="text" class="form-control input-sm" name="link" value="<?=htmlspecialchars($row['link'])?>"></td>
    </tr>
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
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th width="100%">Название</th>
      <th nowrap>Статус</th>
      <th style="padding:0 30px;"></th>
    </tr>
    </thead>
    <tbody>
    <?
    $res = sql($query);
    if(mysqli_num_rows($res))
    {
      $i=1;
      while($row = mysqli_fetch_assoc($res))
      {
        $id = $row['id'];
        $active = $id == $_GET['id'] ? ' active' : '';
        ?>
        <tr id="item-<?=$id?>" class="<?=$active?>">
          <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>"></th>
          <th nowrap><?=$i++?></th>
          <td><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
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