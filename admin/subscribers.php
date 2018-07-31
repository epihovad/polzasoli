<?
require('inc/common.php');

$h1 = 'Список подписчиков';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'subscribers';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			if(!$id) exit;

			foreach($_POST as $key=>$val)
				$$key = clean($val);

			$set = "note=".($note?"'{$note}'":'NULL');
			if($unsubscribe)  $set .= ",unsubscribe_date=NOW()";
			if($subscribe)    $set .= ",unsubscribe_date=NULL";
			$set .= ",black=".($black?1:0);

			if(!$id = update($tbl, $set, $id))
				jAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=sgp($HTTP_REFERER, 'id', $id, 1)?>';</script><?
			break;
		// ----------------- удаление одной записи
		case 'black':
		  $flag = isset($_GET['flag']) ? '0' : '1';
			foreach($_POST['del'] as $id => $none){
				update($tbl,"black = {$flag}",$id);
      }
			?><script>top.location.href = top.url()</script><?
			break;
		// ----------------- удаление одной записи
		case 'unsubscribe':
			$flag = isset($_GET['flag']) ? 'NULL' : 'NOW()';
			foreach($_POST['del'] as $id => $none){
				update($tbl,"unsubscribe_date = {$flag}",$id);
			}
			?><script>top.location.href = top.url()</script><?
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
	if(!$id = (int)$_GET['red']) { header("Location: {$script}"); exit; }
	if(!$row = gtv($tbl,'*',$id)) { header("Location: {$script}"); exit; }

	$title .= " :: {$row['email']} (редактирование)";
	$h = "{$row['email']} <small>(редактирование)</small>";
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . $row['email'];
			
	ob_start();
	?>  
  <form action="?action=save&id=<?=$id?>" method="post" target="ajax">
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Дата создания подписки</th>
        <td><?=date('d.m.Y H:i',strtotime($row['date']))?></td>
      </tr>
      <tr>
        <th></th>
        <th>E-mail</th>
        <td><?=$row['email']?></td>
      </tr>
      <? if(!$row['unsubscribe_date']){?>
        <tr>
          <th></th>
          <th>Завершить подписку</th>
          <td><?=dll(array('0'=>'нет','1'=>'да'),'name="unsubscribe"',0)?></td>
        </tr>
      <?} else {?>
        <tr>
          <th></th>
          <th>Дата завершения подписки</th>
          <td style="font-size:11px"><?=date('d.m.Y H:i',strtotime($row['unsubscribe_date']))?></td>
        </tr>
        <tr>
          <th></th>
          <th>Вернуть подписку</th>
          <td><?=dll(array('0'=>'нет','1'=>'да'),'name="subscribe"',0)?></td>
        </tr>
      <?}?>
      <tr>
        <th></th>
        <th>В черном списке</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="black"',isset($row['black'])?$row['black']:0)?></td>
      </tr>
      <tr>
        <th></th>
        <th>Примечание</th>
        <td><?=input('textarea','note',$row['note'])?></td>
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
		$sf = array('email','note');
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

	// проверяем текущую сортировку и формируем соответствующий запрос
	if($fl['sort']){
		foreach ($fl['sort'] as $f => $t){
			$query .= "\r\nORDER BY {$f} {$t}";
			break;
		}
	} else {
		$query .= "\r\nORDER BY `date` DESC";
	}

	$query .= "\r\nLIMIT " . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;

	ob_start();
	//pre($query);

	$btns = array(
		'Экспорт' => array('js' => "alert('в разработке')", 'class' => 'default', 'icon' => 'far fa-file-excel'),
	  'Остановить подписку' => array('js' => "SaveAll('?action=unsubscribe',1,1)", 'class' => 'warning', 'icon' => 'fas fa-stop-circle'),
		'Включить подписку' => array('js' => "SaveAll('?action=unsubscribe&flag',1,1)", 'class' => 'success', 'icon' => 'far fa-play-circle'),
		'В черный список' => array('js' => "SaveAll('?action=black',1,1)", 'class' => 'warning', 'icon' => 'fas fa-ban'),
		'Удалить из черного списка' => array('js' => "SaveAll('?action=black&flag',1,1)", 'class' => 'success', 'icon' => 'fas fa-undo-alt'),
		'Удалить' => array('js' => "SaveAll('?action=multidel',1,1)", 'class' => 'danger', 'icon' => 'far fa-trash-alt'),
	);
	show_listview_btns('', $btns);
	ActiveFilters();
	?>

  <div class="clearfix"></div>

	<? //$show_filters = $fl['catalog'] || $fl['search']; ?>
  <div id="filters" class="panel-white">
    <h4 class="heading">Фильтры
      <a href="#"<?//=$show_filters?' class="active"':''?>>
        <i class="fas fa-eye" title="показать фильтры">
        </i><i class="fas fa-eye-slash" title="скрыть фильтры"></i>
      </a>
    </h4>
    <div class="fbody<?//=$show_filters?' active':''?>">
      <div class="form-group search">
        <label>Контекстный поиск</label><br>
        <input class="form-control input-sm" type="text" value="<?=htmlspecialchars($fl['search'])?>">
        <button type="button" class="btn btn-danger btn-xs"><i class="fas fa-search"></i>найти</button>
      </div>
    </div>
  </div>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form id="ftl" action="?action=multidel" name="red_frm" method="post" target="ajax">
  <table class="table-list">
    <thead>
      <tr>
        <th><input type="checkbox" name="del" /></th>
        <th>№</th>
        <th width="50%" nowrap><?=SortColumn('E-mail','email')?></th>
        <th nowrap><?=SortColumn('Дата создания подписки','date')?></th>
        <th nowrap><?=SortColumn('Дата завершения подписки','unsubscribe_date')?></th>
        <th width="50%">Примечание</th>
        <th nowrap>В черном списке</th>
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
        <tr id="item-<?=$row['id']?>"<?=$row['unsubscribe_date']||$row['black']?' class="danger"':''?>>
          <th><input type="checkbox" name="del[<?=$id?>]" /></th>
          <th nowrap><?=$i++?></th>
          <td nowrap class="sp"><a href="?red=<?=$id?>"><?=$row['email']?></a></td>
          <th nowrap><?=date('d.m.Y',strtotime($row['date']))?> <?=date('H:i',strtotime($row['date']))?></th>
          <th nowrap><?=$row['unsubscribe_date'] ? date('d.m.Y',strtotime($row['unsubscribe_date'])).' '.date('H:i',strtotime($row['unsubscribe_date'])) : ''?></th>
          <td class="sp"><?=nl2br($row['note'])?></td>
          <th><?=$row['black']?'да':'нет'?></th>
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