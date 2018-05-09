<?
require('inc/common.php');

$h1 = 'Тип абонемента';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
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

			if(!$name) jAlert('необходимо указать название !');

			$set = "name='{$name}', status='{$status}'";

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
      <th>Название</th>
      <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"></td>
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
	$query = "SELECT * FROM {$prx}{$tbl} ORDER BY name";

	ob_start();

	show_listview_btns('Добавить::Удалить');
	?>

  <div class="clearfix"></div>

  <form id="ftl" method="post" target="ajax">
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
    if(mysqli_num_rows($res))
    {
      ?><?
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
	<?
	$content = arr($h, ob_get_clean());
}

require('tpl/template.php');