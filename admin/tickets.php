<?
require('inc/common.php');

$h1 = 'Абонементы';
$tbl = 'tickets';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

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

			if(!$name) errorAlert('Укажите название');

			$set = "name = '{$name}',
			        text = ".($text ? "'{$text}'" : 'NULL').",
			        price = '{$price}',
			        old_price = '{$old_price}',
			        validity = ".($validity ? "'{$validity}'" : 'NULL').",
			        age = '{$age}',
			        ids_type = ".(sizeof($_POST['ids_type']) > 0 ? "'".implode(',', $_POST['ids_type'])."'" : 'NULL').",
			        ids_who = ".(sizeof($_POST['ids_who']) > 0 ? "'".implode(',', $_POST['ids_who'])."'" : 'NULL').",
			        ids_disease = ".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
			        status = '{$status}',
							title = " . ($title ? "'{$title}'" : "NULL") . ",
							keywords = " . ($keywords ? "'{$keywords}'" : "NULL") . ",
							description = " . ($description ? "'{$description}'" : "NULL");

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

	$navigate .= $id ? 'Редактирование' : 'Добавление';
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	?>
  <form id="frm_edit" action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
    </tr>
		<?=show_tr_img('img',"/uploads/{$tbl}/","{$id}.jpg",$script."?action=pic_del&id={$id}",'Фото','Для корректного отображения,<br>рекомендуется загружать квадратное изображение размером 282x282 пискелей')?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Описание</th>
      <td><?=show_pole('textarea','text',$row['text'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Цена</th>
      <td><?=show_pole('text','price',$row['price'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Старая цена</th>
      <td><?=show_pole('text','old_price',$row['old_price'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Срок действия</th>
      <td><?=show_pole('text','validity',$row['validity'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Возрастные ограничения</th>
      <td><?=dllEnum($tbl,'age',"name='age' style='width:auto;'",$row['age'])?></td>
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
	$cur_page = $_SESSION['ss']['page'] ? $_SESSION['ss']['page'] : 1;
	$sitemap = isset($_SESSION['ss']['sitemap']);
	$f_context = stripslashes($_SESSION['ss']['context']);

	$where = '';
	if($f_context!='')	$where .= " AND (name LIKE '%{$f_context}%')";

	$page_title .= " :: ".$rubric;
	$navigate .= 'Общий список';

	if($sitemap) $razdel['Сохранить'] = "javascript:saveall();";
	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);

	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($sitemap)
	{
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else $query  = sprintf($query,'');

	$query .= " WHERE 1{$where}";

	$r = sql($query);
	$count_obj = @mysql_num_rows($r); // кол-во объектов в базе
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

		$query .= " ORDER BY {$cur_pole} ".($cur_sort=='up'?'DESC':'ASC');
	}
	else
		$query .= ' ORDER BY name';
	$query .= ' LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
	//-----------------------------
	//echo $query;

	show_filters($script);
	show_navigate_pages($kol_str,$cur_page,$script);

	?>
  <table class="filter_tab" style="margin:5px 0 0 0;">
    <tr>
      <td>контекстный поиск</td>
      <td><input type="text" id="searchTxt" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
      <td><a id="searchBtn" href="" class="link">найти</a></td>
    </tr>
  </table>

	<? if(!$sitemap){ ?>
  <div style="padding:5px 0 0 0;">Отобразить <a href="" style="color:#F60" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
  <div class="clear"></div>
  <? } ?>

  <form action="?action=multidel" name="red_frm" method="post" enctype="multipart/form-data" style="margin:0;" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" onclick="check_uncheck('check_del')" /></th>
      <th>№</th>
      <th><img src="img/image.png" title="изображение" /></th>
      <th>Название</th>
			<? if($sitemap){?>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
			<? }?>
      <th>Цена, руб.</th>
      <th>Срок действия</th>
      <th>Возраст</th>
      <th>Типы абонемента</th>
      <th>Типы посетителей</th>
      <th>Заболевания</th>
      <th nowrap>Статус</th>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$res = sql($query);
	if(mysql_num_rows($res)){
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
					if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
						$src = "/uploads/{$tbl}/100x-/{$id}.jpg";
						$big_src = "/uploads/{$tbl}/{$id}.jpg";
					}
					?>
          <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)" style="display:block;">
            <img src="<?=$src?>" align="absmiddle" style="max-height:100px;" />
          </a>
        </th>
        <td class="sp" nowrap><?=$row['name']?></td>
				<? if($sitemap){?>
          <th class="sitemap"><input type="text" class="datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
          <th class="sitemap"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
          <th class="sitemap"><input type="text" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" style="text-align:center; width:30px;" /></th>
				<? }?>
        <td nowrap><?=$row['price'] . ($row['old_price'] ? ' (<u>'.$row['old_price'].'</u>)' : '')?></td>
        <td><?=$row['validity']?></td>
        <td><?=$row['age']?></td>
        <td></td>
        <td></td>
        <td></td>
        <td nowrap align="center"><?=btn_flag($row['status'],$row['id'],'action=status&id=')?></td>
			  <td nowrap align="center"><?=btn_edit($row['id'])?></td>
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
  </table>
  </form>
  <?
	$content = $subcontent.ob_get_clean();
}

require('tpl/template.php');