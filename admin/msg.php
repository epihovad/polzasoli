<?
require('inc/common.php');

$rubric = 'Обратная связь';
$tbl = 'msg';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			if(!$id) exit;
			$notes = clean($_POST['notes']);
			if(!$id = update($tbl,"notes=".($notes?"'{$notes}'":"NULL"),$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
			
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				update($tbl,'',$id);
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
      <th>Дата</th>
      <td style="font-size:12px"><?=date('d.m.Y H:i',strtotime($row['date']))?></td>
    </tr>
		<tr>
			<th class="tab_red_th"></th>
			<th>Тип</th>
			<td style="font-size:12px"><?=$row['type']?></td>
		</tr>
    <? if($row['type'] == 'Оптовики'){?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Организация</th>
      <td style="font-size:12px"><?=$row['firma']?></td>
    </tr>
    <?}?>
    <tr>
      <th class="tab_red_th"></th>
      <th><?=$row['type'] == 'Оптовики' ? 'Контактное лицо' : 'Имя'?></th>
      <td style="font-size:12px"><?=$row['name']?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>E-mail</th>
      <td style="font-size:12px"><?=$row['email']?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Телефон</th>
      <td style="font-size:12px"><?=$row['phone']?'+7'.$row['phone']:''?></td>
    </tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Сообщение</th>
			<td style="font-size:12px"><?=nl2br($row['text'])?></td>
    </tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Примечание</th>
      <td><?=show_pole('textarea','notes',$row['notes'])?></td>
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
	$f_msg = stripslashes($_SESSION['ss']['msg']);
	$f_context = stripslashes($_SESSION['ss']['context']);
	
	$where = '';
	if($f_msg) $where .= " AND type = '{$f_msg}'";
	if($f_context) $where .= " AND (firma LIKE '%{$f_context}%' OR
	                                name LIKE '%{$f_context}%' OR
																	email LIKE '%{$f_context}%' OR
																	phone LIKE '%{$f_context}%' OR
																	text LIKE '%{$f_context}%' OR
																	notes LIKE '%{$f_context}%')";
										
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
	<table class="filter_tab" style="margin:5px 0 0 0;">
		<tr>
			<td align="left">Тип</td>
			<td colspan="2"><?=dllEnum($tbl,'type','name="msg" onChange="RegSessionSort(\''.$script.'\',\'msg=\'+this.value);return false;"',$f_msg?$f_msg:'-- все --',array('remove'=>'-- все --'))?></tr>
		<tr>
			<td>контекстный поиск</td>
			<td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
			<td><a id="searchBtn" href="" class="link">найти</a></td>
		</tr>
	</table>
  
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th width="1%"><input type="checkbox" name="check_del" id="check_del" /></th>
      <th width="1%">№</th>
			<th nowrap>Тип</th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Дата','date')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Организация','firma')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Имя','name')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'E-mail','email')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Телефон','phone')?></th>
      <th width="30%">Сообщение</th>
			<th width="30%">Примечание</th>
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
			<tr id="row<?=$row['id']?>">
				<th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
        <th><?=$i++?></th>
				<td nowrap align="center"><?=$row['type']?></td>
        <td nowrap style="font-size:11px; text-align:center;"><?=date('d.m.Y',strtotime($row['date']))?><br><?=date('H:i',strtotime($row['date']))?></td>
        <td nowrap align="center" class="sp"><?=$row['firma']?></td>
        <td nowrap align="center" class="sp"><?=$row['name']?></td>
        <td nowrap align="center" class="sp"><?=$row['email']?></td>
        <td nowrap align="center" class="sp"><?=$row['phone']?'+7'.$row['phone']:''?></td>
        <td class="sp"><?=nl2br($row['text'])?></td>
				<td class="sp"><?=nl2br($row['notes'])?></td>
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