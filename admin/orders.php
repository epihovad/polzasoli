<?
require('inc/common.php');

$rubric = 'Заказы';
$tbl = 'orders';

$arr_status = array('новый','обработан','выполнен','закрыт');
$arr_status_str = array('новый'=>'ожидает проверки','обработан'=>'принят','выполнен'=>'выполнен','закрыт'=>'закрыт');

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			if(!$order = gtv($tbl,'*',$id)){
				exit;
			}

			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$id = update($tbl,"status='{$status}',notes='{$notes}'",$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $k=>$v)
				update($tbl,'',$k);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		//
		case 'status':
			$status = $_GET['status'];

			if(!$order = gtv($tbl,'*',$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			// проверка и запрет установки предыдущего статуса
			$old_status = array_search($order['status'],$arr_status);
			$new_status = array_search($status,$arr_status);

			if(!update($tbl,"status='".clean($status)."'",$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			?>
			<script>
				var $tr = top.$('tr#row<?=$id?>');;
				$tr.removeClass('status<?=$old_status?>').addClass('status<?=$new_status?>');
			</script>
			<?
			errorAlert('Статус заказа успешно изменён!');
			break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];
	
	$rubric .= ' &raquo; Просмотр';
	$page_title .= ' :: '.$rubric;
	
	if(!$order = gtv($tbl,'*',$id)) { header("Location: {$script}"); exit; }

	ob_start();
	?>
	<form action="?action=save&id=<?=$id?>" method="post" target="ajax" enctype="multipart/form-data">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Номер</th>
      <td><b style="margin-right:50px;"><?=$order['number']?></b></td>
		</tr>
		<tr>
    	<th class="tab_red_th"></th>
      <th>Дата</th>
      <td><?=date('d.m.Y, H:i:s',strtotime($order['date']))?></td>
		</tr>
		<tr>
    	<th class="tab_red_th"></th>
      <th>Клиент</th>
      <td><?=$order['user_info']?></td>
		</tr>
		<tr>
    	<th class="tab_red_th"></th>
      <th>Заказ</th>
      <td><?=$order['order_info']?></td>
		</tr>
		<tr>
			<th class="tab_red_th"><?=help('Уведомлять клиента об изменении статуса заказа')?></th>
			<th>E-mail информирование</th>
			<td><?=$order['sendmail'] ? '<span style="color:green">ВКЛЮЧЕНО</span>' : '<span style="color:red">ВЫКЛЮЧЕНО</span>'?></td>
		</tr>
    <tr>
      <th class="tab_red_th"><?=help('Перезвонить клиенту для уточнения заказа')?></th>
      <th>Перезвонить клиенту</th>
      <td><?=$order['sendmail'] ? '<span style="color:green">ДА</span>' : '<span style="color:red">НЕТ</span>'?></td>
    </tr>
		<tr>
    	<th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dllEnum($tbl,'status',"name='status' style='width:auto;'",$order['status'])?></td>
		</tr>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Примечание</th>
      <td><?=show_pole('textarea','notes',$order['notes'])?></td>
		</tr>
		<tr>
    	<th class="tab_red_th"></th>
      <th></th>
      <td align="center">
      	<input type="submit" value="Сохранить" class="but1" name="submit" onclick="loader(true)" />&nbsp;
        <input type="button" value="Отмена" class="but1" onclick="location.href='<?=$script?>'" />
      </th>
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
	$f_status = stripslashes($_SESSION['ss']['order_status']);
	$f_date = stripslashes($_SESSION['ss']['date']);
	$f_context = stripslashes($_SESSION['ss']['context']);

	$where = '';
	if($f_status) $where .= " AND status = '{$f_status}'";
	if($f_date) $where .= " AND DATE_FORMAT(`date`,'%d.%m.%Y') = '{$f_date}'";
	if($f_context) $where .= " AND (`number` LIKE '%{$f_context}%' OR
																	`order_info` LIKE '%{$f_context}%' OR
																	`user_info` LIKE '%{$f_context}%')";
	
	$page_title .= ' :: '.$rubric; 
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel = array("Удалить"=>"javascript:multidel(document.red_frm,'check_del_','?action=multidel');");
	$subcontent = show_subcontent($razdel);
	
	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";
	
	$count_obj = getField(str_replace('*','COUNT(*)',$query)); // кол-во объектов в базе
	$count_obj_on_page = 15; // кол-во объектов на странице
	$kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц

	ob_start();
	
	// проверяем текущую сортировку
	if($_SESSION['ss']['sort']) 
	{
		$sort = explode(':',$_SESSION['ss']['sort']);
		$cur_pole = $sort[0];
		$cur_sort = $sort[1];

		$query .= " ORDER BY {$cur_pole}".($cur_sort=='up'?' DESC':' ASC');
	}
	else
		$query .= " ORDER BY FIELD(`status`,'новый','обработан','выполнен','закрыт'), `date` DESC";
	$query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
	//-----------------------------
	//echo $query;
	
	show_filters($script);
	show_navigate_pages($kol_str,$cur_page,$script);

	?>
	<style>
		.zstatus { float:left; padding:0 0 0 20px;}
		.zstatus div { padding-bottom:5px; }
		.zstatus th { width:40px;}
		.zstatus td { padding:0 0 0 5px; font-size:11px;}
		.subtab .ocode { width:90px;}
		.subtab .oname { width:400px; text-align:left; font-weight:bold; color:#385a89;}
		.subtab .obrand { width:150px;}
		.subtab .oprice { width:100px; text-align:right;}
		.subtab .okol { width:60px;}
		.subtab .ocost { width:100px; text-align:right;}
		.status0 th, .status0 td, .zstatus th.status0 { background-color:#E7FEE4;}
		.status1 th, .status1 td, .zstatus th.status1 { background-color:#FFF3D3;}
		.status2 th, .status2 td, .zstatus th.status2 { background-color:#D5F2FF;}
    .status3 th, .status3 td, .zstatus th.status3 { background-color:#E4E4E4;}
	</style>

	<div style="float:left">
		<table class="filter_tab" style="margin:5px 0 0 0;">
			<tr>
				<td align="left">Статус заказа</td>
				<td colspan="2"><?=dllEnum($tbl,'status','style="width:100%" onChange="RegSessionSort(\''.$script.'\',\'order_status=\'+this.value);return false;"',$f_status,array('remove'=>'-- все --'))?></td>
			</tr>
			<tr>
				<td align="left">Дата заказа</td>
				<td colspan="2"><input type="text" class="datepicker" value="<?=$f_date ? date("d.m.Y",strtotime($f_date)) : ''?>" onChange="RegSessionSort('<?=$script?>','date='+this.value);return false;" /></td>
			</tr>
			<tr>
				<td>контекстный поиск</td>
				<td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
				<td><a id="searchBtn" href="" class="link">найти</a></td>
			</tr>
		</table>
	</div>

	<div class="zstatus">
		<div>Статусы заказов по цветам:</div>
		<table>
			<?
			$i=0;
			foreach($arr_status_str as $s=>$n){
				?><tr><th class="status<?=$i++?>"></th><td>- <?=$s?></td></tr><?
			}
			?>
		</table>
	</div>

	<div style="clear:both"></div>

  <script>
    $(function(){
      $('select.change_status').change(function() {
        var selected = $(this).val();
        var id = $(this).attr('order');
        toajax('?action=status&id='+id+'&status='+selected)
      });
    });
  </script>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
    	<th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'№','number')?></th>
      <th width="100%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Заказ','order_info')?></th>
      <th nowrap>Перезвонить <?=help('Перезвонить клиенту для уточнения заказа')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = mysql_query($query);
	if(@mysql_num_rows($res))
	{
		$arr_status = array('новый','обработан','выполнен','закрыт');

		while($order = mysql_fetch_array($res))
		{
			$id = $order['id'];
			?>
			<tr id="row<?=$id?>" class="status<?=array_search($order['status'],$arr_status)?>" audio="0">
        <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
        <th class="sp" nowrap><?=$order['number']?><br><span style="font-weight:normal; font-size:11px;">от <?=date('d.m.Y', strtotime($order['date']))?><br><?=date('H:i:s', strtotime($order['date']))?></span></th>
        <td class="sp" valign="top"><?=$order['order_info']?></td>
        <td nowrap align="center"><?=$order['sendmail'] ? '<span style="color:green">ДА</span>' : '<span style="color:red">НЕТ</span>'?></td>
        <td nowrap align="center"><?=dllEnum($tbl,'status',"order=\"{$id}\" class=\"change_status\"",$order['status'])?></td>
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