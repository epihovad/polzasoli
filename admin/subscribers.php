<?
require('inc/common.php');

$rubric = 'Список подписчиков';
$tbl = 'subscribers';

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

			if(!$id) exit;

			$set = "note=".($note?"'{$note}'":'NULL');
			if($unsubscribe)  $set .= ",unsubscribe_date=NOW()";
			if($subscribe)    $set .= ",unsubscribe_date=NULL";
			$set .= ",black=".($black?1:0);

			if(!$id = update($tbl, $set, $id))
				errorAlert('Во время сохранения данных произошла ошибка.');
			
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'black=1',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				update($tbl,'unsubscribe_date=NOW()',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	if(!$id = (int)$_GET['red']) { header("Location: {$script}"); exit; }
	if(!$row = gtv($tbl,'*',$id)) { header("Location: {$script}"); exit; }
	
	$rubric .= ' &raquo; Просмотр';
	$page_title .= ' :: '.$rubric;
			
	ob_start();
	?>  
  <form action="?action=save&id=<?=$id?>" method="post" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
  	<tr>
    	<th class="tab_red_th"></th>
      <th>Дата создания подписки</th>
      <td style="font-size:11px"><?=date('d.m.Y H:i',strtotime($row['date']))?></td>
    </tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>E-mail</th>
			<td style="font-size:14px"><?=$row['email']?></td>
		</tr>
    <? if(!$row['unsubscribe_date']){?>
      <tr>
        <th class="tab_red_th"></th>
        <th>Завершить подписку</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="unsubscribe"',0)?></td>
      </tr>
    <?} else {?>
      <tr>
        <th class="tab_red_th"></th>
        <th>Дата завершения подписки</th>
        <td style="font-size:11px"><?=date('d.m.Y H:i',strtotime($row['unsubscribe_date']))?></td>
      </tr>
      <tr>
        <th class="tab_red_th"></th>
        <th>Вернуть подписку</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="subscribe"',0)?></td>
      </tr>
    <?}?>
    <tr>
      <th class="tab_red_th"></th>
      <th>В черном списке</th>
      <td><?=dll(array('0'=>'нет','1'=>'да'),'name="black"',isset($row['black'])?$row['black']:0)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Примечание</th>
      <td><?=show_pole('text','note',htmlspecialchars($row['note']))?></td>
    </tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th></th>
      <td align="center">
      	<input type="submit" value="Сохранить" class="but1" onclick="loader(true)" />&nbsp;
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
	$f_context = stripslashes($_SESSION['ss']['context']);
	
	$where = '';
	if($f_context) $where .= " AND (email LIKE '%{$f_context}%' OR
	                                note LIKE '%{$f_context}%'
	                                )";

	$page_title .= ' :: '.$rubric; 
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel = array("Удалить"=>"javascript:multidel(document.red_frm,'check_del_');");
	$subcontent = show_subcontent($razdel);
	
	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";
	
	$count_obj = getField(str_replace('*','COUNT(*)',$query)); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц

	ob_start();
	// проверяем текущую сортировку
	// и формируем соответствующий запрос	
	if($_SESSION['ss']['sort']) 
	{
		$sort = explode(':',$_SESSION['ss']['sort']);
		$cur_pole = $sort[0];
		$cur_sort = $sort[1];

		$query .= " ORDER BY {$cur_pole}".($cur_sort=='up'?' DESC':' ASC');
	}
	else
		$query .= ' ORDER BY `date` DESC';
	$query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
	//-----------------------------
	//echo $query;
	
	show_filters($script);
	show_navigate_pages($kol_str,$cur_page,$script);

	?>
  <style>
    tr.unactive th, tr.unactive td { background-color:#fcd9cb;}
  </style>

	<table class="filter_tab" style="margin:5px 0 0 0;">
			<td>контекстный поиск</td>
			<td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
			<td><a id="searchBtn" href="" class="link">найти</a></td>
		</tr>
	</table>
  
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
			<th width="20%" nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'E-mail','email')?></th>
      <th width="20%" nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Дата создания подписки','date')?></th>
      <th width="20%" nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Дата завершения подписки','unsubscribe_date')?></th>
			<th width="40%">Примечание</th>
      <th nowrap>В черном списке</th>
      <th style="padding:0 30px;"></th>
    </tr>
    <?
	$res = mysql_query($query);
	if(@mysql_num_rows($res))
	{
		$i=1;
		while($row = mysql_fetch_array($res))
		{
			?>
			<tr id="row<?=$row['id']?>"<?=$row['unsubscribe_date']||$row['black']?' class="unactive"':''?>>
				<th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
        <th><?=$i++?></th>
				<td nowrap class="sp"><?=$row['email']?></td>
        <td nowrap style="font-size:11px; text-align:center;"><?=date('d.m.Y',strtotime($row['date']))?> <?=date('H:i',strtotime($row['date']))?></td>
        <td nowrap style="font-size:11px; text-align:center;"><?=$row['unsubscribe_date'] ? date('d.m.Y',strtotime($row['unsubscribe_date'])).' '.date('H:i',strtotime($row['unsubscribe_date'])) : ''?></td>
				<td class="sp"><?=$row['note']?></td>
        <td align="center"><?=$row['black']?'да':'нет'?></td>
				<td nowrap align="center"><?=btn_edit($row['id'])?></td>
			</tr>
			<?
		}	
	}
	else
	{
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

require("tpl/tpl.php");