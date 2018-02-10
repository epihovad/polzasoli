<?
require('inc/common.php');

$rubric = 'Комментарии';
$tbl = 'comments';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':			
			if(!$text = clean($_POST['text'])) errorAlert('необходимо ввестит текст !');
			if($id = update($tbl,"text='{$text}'",$id))
			{
				?><script>top.location.href = "<?=$script?>?id=<?=$id?>";</script><?
			}
			else
				errorAlert('Во время сохранения данных произошла ошибка.');			
		break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
		break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'',$id);
			?><script>top.location.href = "<?=$script?>";</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $k=>$v)
				update($tbl,'',$k);
			?><script>top.location.href = "<?=$script?>";</script><?
		break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];	
	if(!$row = gtv($tbl,'*',$id)) { header("Location: {$script}"); exit; }
	
	$rubric .= ' &raquo; Редактирование';
	$page_title .= ' :: '.$rubric;
	
	ob_start();
	?>
	<form action="?action=save&id=<?=$id?>" method="post" target="ajax" enctype="multipart/form-data">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
		<tr>
    	<th class="tab_red_th"></th>
      <th>Дата</th>
      <td style="font-size:11px"><?=date('d.m.Y H:i',strtotime($row['date']))?></td>
    </tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Автор</th>
      <td>
      	<?
				if($row['id_users'] && $user=gtv('users','name',$row['id_users']))
				{
					?><a href="users.php?red=<?=$row['id_users']?>"><?=$user?></a><?
				}
				elseif(!$row['id_users'] && !$row['name'])
					echo 'Администратор';
				else
					echo $row['name'];
				?>
      </td>
    </tr>
    <tr>
			<th class="tab_red_th"></th>
			<th>Оценка</th>
			<td><?=$row['score']?></td>
		</tr>
    <tr>
			<th class="tab_red_th"></th>
			<th>Текст</th>
			<td><?=show_pole('textarea','text',htmlspecialchars($row['text']))?></td>
		</tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
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
	$f_goods = (int)$_SESSION['ss']['goods'];
	$f_context = stripslashes($_SESSION['ss']['context']);
	
	$where = '';
	if($f_goods) 		$where .= " AND A.id_goods='{$f_goods}'";
	if($f_context) 	$where .= " AND (	A.name LIKE '%{$f_context}%' OR
																		A.text LIKE '%{$f_context}%' OR
																		B.articul LIKE '%{$f_context}%' OR
																		B.name LIKE '%{$f_context}%')";
										
	$page_title .= ' :: '.$rubric; 
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel = array("Удалить"=>"javascript:multidel(document.red_frm,'check_del_');");
	$subcontent = show_subcontent($razdel);
	
	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";
	
	$query = "SELECT A.*,B.articul,B.name AS gname,B.link,C.name as uname
						FROM {$prx}{$tbl} A
						LEFT JOIN {$prx}goods B ON A.id_goods=B.id
						LEFT JOIN {$prx}users C ON A.id_users=C.id
						WHERE 1{$where}
						GROUP BY A.id";
						
	$r = sql($query);
	$count_obj = (int)@mysql_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 50; // кол-во объектов на странице
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
		$query .= ' ORDER BY A.`date` DESC';
	$query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
	//-----------------------------
	//echo $query;
	
	show_filters($script);
	show_navigate_pages($kol_str,$cur_page,$script);
	
	?>
  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
			<td>контекстный поиск <?=help('поиск осуществляется по автору, тексту отзыва,<br />артукулу и наименованию товара')?></td>
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
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Дата','A.`date`');?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Автор','A.name');?></th>
      <th width="100%">Отзыв</th>
      <th>Товар</th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Оценка','A.score');?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','A.status');?></th>
      <th style="padding:0 30px;"></th>
    </tr>
    <?
	$res = mysql_query($query);
	if(@mysql_num_rows($res))
	{
		$i=1;
		while($row = mysql_fetch_array($res))
		{
			$id = $row['id'];
			?>
			<tr id="<?=$id?>">
				<th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
        <th><?=$i++?></th>
        <td nowrap style="font-size:11px"><?=date('d.m.Y H:i',strtotime($row['date']))?></td>
        <td class="sp" nowrap align="center">
					<?
					if($row['uname'])
					{
						?><a href="users.php?red=<?=$row['id_users']?>" style="color:#090"><?=$row['uname']?></a><?
					}
					elseif(!$row['id_users'] && !$row['name'])
						echo 'Администратор';
					else
						echo $row['name'];
					?>
        </td>
        <td class="sp"><?=nl2br($row['text'])?></td>
        <td class="sp" nowrap>
        	<?
					if($row['gname'])
					{
						?><a href="/goods/<?=$row['link']?>.htm" style="color:#090" target="_blank"><?=$row['gname']?></a><?
					}
					?>
        </td>
        <td align="center"><?=$row['score']?></td>
        <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=')?></td>
				<td nowrap align="center"><?=btn_edit($id)?></td>
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