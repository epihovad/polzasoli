<?
require('inc/common.php');

$h1 = 'Вопрос-Ответ';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'faq';
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

			if(!$question) jAlert('необходимо ввести «Вопрос»');
			if(!$answer) jAlert('необходимо ввести «Ответ»');

			$set = "question='{$question}',
							answer='{$answer}',
							status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

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
        <th>Вопрос</th>
        <td><input type="text" class="form-control input-sm" name="question" value="<?=htmlspecialchars($row['question'])?>"></td>
      </tr>
        <th></th>
        <th>Ответ</th>
        <td><?=showCK('answer',$row['answer'])?></td>
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
	$query = "SELECT * FROM {$prx}{$tbl}";

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

	show_listview_btns(($sitemap ? 'Сохранить::' : '') . 'Добавить::Удалить');
	show_filters($script);
  ?>

  <div class="clearfix"></div>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
    <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
    <table class="table-list">
      <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
	      <? if(!$_SESSION['ss']['sort']){ ?><th nowrap><?=help('параметр с помощью которого можно изменить<br>порядок вывода элементов в клиентской части сайта')?></th><? }?>
        <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Вопрос','question')?></th>
        <th width="50%">Ответ</th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
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
          <tr id="item-<?=$id?>" oid="<?=$id?>" par="0">
            <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>"></th>
            <th nowrap><?=$i++?></th>
						<? if(!$_SESSION['ss']['sort']){ ?><th nowrap align="center"><i class="fas fa-sort"></i></th><? }?>
            <td><a href="?red=<?=$id?>"><?=$row['question']?></a></td>
            <td><?=$row['answer']?></td>
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