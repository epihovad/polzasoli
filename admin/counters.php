<?
require('inc/common.php');

$h1 = 'Счетчики';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'counters';

// ------------------- СОХРАНЕНИЕ ------------------------
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
			        html = ".($html ? "'{$html}'" : 'NULL').",
			        note = ".($note ? "'{$note}'" : 'NULL').",
			        status = '{$status}'";

			if(!$id = update($tbl, $set, $id))
				jAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'',$id);
			?><script>top.location.href = top.url()</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id => $none)
				update($tbl, '', $id);
			?><script>top.location.href = top.url()</script><?
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
  <form action="?action=save&id=<?=$id?>" method="post" target="ajax">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Название</th>
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Счетчик</th>
        <td><?=input('textarea', 'html', $row['html'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Примечание</th>
        <td><?=input('textarea', 'note', $row['note'])?></td>
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
	$query = "SELECT * FROM {$prx}{$tbl}\r\nWHERE 1{$where}\r\nORDER BY sort,id";

	ob_start();
	//pre($query);

	show_listview_btns('Добавить::Удалить');
	?>

  <div class="clearfix"></div>

  <form id="ftl" action="?action=multidel" name="red_frm" method="post" target="ajax">
    <table class="table-list" tbl="<?=$tbl?>">
      <thead>
      <tr>
        <th><input type="checkbox" name="del" /></th>
        <th>№</th>
				<th nowrap><?=help('параметр с помощью которого можно изменить<br>порядок вывода объектов в клиентской части сайта')?></th>
        <th width="33%" nowrap>Название</th>
        <th width="33%" nowrap>Счетчик</th>
        <th width="33%" nowrap>Примечание</th>
        <th>Статус</th>
        <th style="padding:0 30px;"></th>
      </tr>
      </thead>
      <tbody>
			<?
			$res = sql($query);
			if(@mysqli_num_rows($res)){
				$i=1;
				while($row = mysqli_fetch_assoc($res)){
					$id = $row['id'];
					?>
          <tr id="item-<?=$row['id']?>" oid="<?=$id?>" par="0">
            <th><input type="checkbox" name="del[<?=$id?>]" /></th>
            <th nowrap><?=$i++?></th>
            <th nowrap align="center"><i class="fas fa-sort"></i></th>
            <td><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
            <td><?=$row['html']?></td>
            <td><?=nl2br($row['note'])?></td>
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