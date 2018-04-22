<?
/*
<iframe width="560" height="315" src="https://www.youtube.com/embed/Z_m0Ip7XmNg" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
 */
require('inc/common.php');

$rubric = 'Отзывы';
$tbl = 'reviews';

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

			if(!$name) errorAlert('Введите имя');
			if(!$text && !$youtube) errorAlert('Введите текст отзыва или добавьте код видео');

			$set = "name='{$name}',
			        text=".($text ? "'{$text}'" : 'NULL').",
			        youtube=".($youtube ? "'{$youtube}'" : 'NULL').",
			        status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			// загружаем картинку
			if($_FILES['img']['name'])
			{
				remove_img($id);
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
		// ----------------- удаление банера
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				remove_object($id);
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
    <tr>
      <th class="tab_red_th"></th>
      <th>Имя</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
    </tr>
		<?=show_tr_img('img',"/uploads/{$tbl}/","{$id}.jpg",$script."?action=pic_del&id={$id}",'Фото','Для корректного отображения фото,<br>рекомендуется загружать квадратное изображение размером 100x100 пикселей')?>
		<tr>
      <th class="tab_red_th"></th>
      <th>Текст отзыва</th>
      <td><?=show_pole('textarea','text',$row['text'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('Для корректного отображения плеера,<br>рекомендуется использовать размер видео 560x315 (ширина x высота)')?></th>
      <th>Код видео</th>
      <td><?=show_pole('textarea','youtube',$row['youtube'])?></td>
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
      <th><img src="img/image.png" title="изображение" /></th>
      <th>Имя</th>
      <th width="50%">Текст отзыва</th>
      <th width="50%">Код видео</th>
      <th nowrap>Статус</th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = sql("SELECT * FROM {$prx}{$tbl} ORDER BY id");
	if(mysqli_num_rows($res))
	{
		$i=1;
		while($row = mysqli_fetch_assoc($res))
		{
			$id = $row['id'];
			?>
			<tr id="row<?=$row['id']?>">
			  <th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
			  <th nowrap><?=$i++?></th>
        <th style="padding:3px 5px;">
					<?
					$src = '/uploads/no_photo.jpg';
					$big_src = '/uploads/no_photo.jpg';
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
						$src = "/uploads/{$tbl}/50x-/{$id}.jpg";
						$big_src = "/uploads/{$tbl}/{$id}.jpg";
					}
					?>
          <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)" style="display:block;">
            <img src="<?=$src?>" align="absmiddle" style="max-height:50px; border-radius:100px;" />
          </a>
        </th>
        <td nowrap><?=$row['name']?></td>
        <td><?=$row['text']?></td>
        <td align="center"><?=$row['youtube']?></td>
        <td nowrap align="center"><?=btn_flag($row['status'],$row['id'],'action=status&id=')?></td>
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
	$content = panel($navigate, $subcontent.ob_get_clean());
}

require('tpl/template.php');