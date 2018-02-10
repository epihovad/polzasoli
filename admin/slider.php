<?
require('inc/common.php');

$rubric = 'Слайдер';
$tbl = 'slider';

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);
			
			if(!$id = update($tbl,"link='{$link}',status='{$status}'",$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
							
			// загружаем картинку для рубрики
			if($_FILES['img']['name'])
			{
				$info = @getimagesize($_FILES['img']['tmp_name']);
				if($info===false) errorAlert('Ошибка загрузки файла');
					
				// проверка размеров
				if($info[0]!=1280 && $info[1]!=450)
					errorAlert('Нарушение требований к изображения!\n(см. примечание)');
				
				remove_img($id); // удаляем старую картинку
				
				$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg";
				@move_uploaded_file($_FILES['img']['tmp_name'],$path);
				@chmod($path,0644);
			}
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,'status',$id);
		break;
		// ----------------- сортировка вниз
		case 'moveup':
			sort_moveup($tbl,$id);
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			sort_movedown($tbl,$id);
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление банера
		case 'del':
			update($tbl,'',$id);
			@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg");
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
			{
				update($tbl,'',$id);
				@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg");
			}
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление изображения
		case 'pic_del':
			remove_img($id);
			?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
			break;
	}
	exit;
}
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
	$id = (int)@$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	?>
  <form id="frm_edit" action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <?=show_tr_img('img',"/uploads/{$tbl}/","{$id}.jpg",$script."?action=pic_del&id={$id}",'Изображение','требуемый размер изображения 1280x450 пикселей')?>
    <tr>
    	<th class="tab_red_th"></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'])?></td>
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
        <input type="submit" value="<?=($id?'Сохранить':'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
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
	ob_start();
	
	$page_title .= ' :: '.$rubric; 
	$rubric .= ' &raquo; Общий список'; 
	
	$razdel = array('Добавить'=>'?red=0','Удалить'=>"javascript:multidel(document.red_frm,'check_del_','');");
	$subcontent = show_subcontent($razdel);
	
	?>
  <form action="?action=multidel" name="red_frm" method="post" enctype="multipart/form-data" style="margin:0;" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" onclick="check_uncheck('check_del')" /></th>
      <th>№</th>
      <th>Баннер</th>
      <th width="100%">Ссылка</th>
      <th nowrap>Статус</th>
      <th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = sql("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id");
	if(mysql_num_rows($res))
	{
		$i=1;
		while($row = mysql_fetch_array($res))
		{
			$id = $row['id'];
			?>
			<tr id="row<?=$row['id']?>">
			  <th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
			  <th nowrap><?=$i++?></th>
			  <td align="center">
			  	<?
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg"))
					{
						?><img src="/<?=$tbl?>/<?=$id?>.jpg" height="100"><?
					}
					?>
        </td>
        <td nowrap><a href="<?=$row['link']?>" class="green_link" target="_blank"><?=$row['link']?></a></td>
        <td nowrap align="center"><?=btn_flag($row['status'],$row['id'],'action=status&id=')?></td>
        <td nowrap align='center'><?=btn_sort($id)?></td>
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

require('tpl/tpl.php');
?>