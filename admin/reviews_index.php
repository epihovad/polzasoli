<?
require('inc/common.php');

$rubric = 'Отзывы на главной';
$tbl = 'reviews_index';

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
			if(!$prof) errorAlert('Введите профессию');
			if(!$text) errorAlert('Введите текст отзыва');

			$set = "name='{$name}',
			        prof='{$prof}',
			        text='{$text}',
			        status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			$dir = 'reviews';
			if(sizeof((array)$_FILES['img']['name']))
			{
				foreach($_FILES['img']['name'] as $num=>$null)
				{
					if(!$_FILES['img']['name'][$num]) continue;

					// формируем имя картинки
					if($new_name = get_pic_name($id, $dir))
					{
						remove_img($new_name, $dir);

						$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}";
						@move_uploaded_file($_FILES['img']['tmp_name'][$num],$path);
						@chmod($path,0644);
						resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/{$new_name}",array('47','47'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$dir}/47x47/{$new_name}",1,'');
					}
				}
			}

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,'status',$id);
		break;
		// ----------------- удаление банера
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
		<?=show_tr_images($id,'Фото','Для корректного отображения фото,<br>рекомендуется загружать квадратное небольшое изображение',1,'img','reviews','47x47')?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Профессия</th>
      <td><?=show_pole('text','prof',htmlspecialchars($row['prof']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Отзыв</th>
      <td><?=show_pole('textarea','text',$row['text'])?></td>
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
      <th>Профессия</th>
      <th width="100%">Отзыв</th>
      <th nowrap>Статус</th>
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
        <th style="padding:3px 5px;">
					<?
					$src = '/uploads/no_photo.jpg';
					$big_src = '/uploads/no_photo.jpg';
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/reviews/{$id}.jpg")){
						$src = "/uploads/reviews/47x47/{$id}.jpg";
						$big_src = "/uploads/reviews/{$id}.jpg";
					}
					?>
          <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)">
            <img src="<?=$src?>" align="absmiddle" height="45" />
          </a>
        </th>
        <td nowrap><?=$row['name']?></td>
        <td nowrap><?=$row['prof']?></td>
        <td><?=$row['text']?></td>
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
	$content = $subcontent.ob_get_clean();
}

require('tpl/tpl.php');