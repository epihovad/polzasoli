<?
require('inc/common.php');

$rubric = 'Каталог';
$tbl = 'catalog';
$id = (int)$_GET['id'];

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'saveall':
			updateSitemap();
			?><script>alert('Данные успешно сохранены');top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$value)
				$$key = clean($value);
			
			$updateLink = false;
			
			if(!$name) errorAlert('необходимо указать название !');
					
			$where = $id ? " AND id<>{$id}" : '';
			if($link)
			{
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}' AND id_parent='{$id_parent}'{$where}"))
					$updateLink = true;
			}
			else
			{
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}' AND id_parent='{$id_parent}'{$where}"))
					$updateLink = true;
			}
			
			$set = "id_parent='{$id_parent}',
							name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink)
				$set .= ",link='{$link}'";
			
			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
				
			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);

			// загружаем картинки
      if(sizeof((array)$_FILES[$tbl]['name']))
      {
        foreach($_FILES[$tbl]['name'] as $num=>$null)
        {
          if(!$_FILES[$tbl]['name'][$num]) continue;

          // формируем имя картинки
          if($new_name = get_pic_name($id, $tbl))
          {
            remove_img($new_name, $tbl);

            $path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$new_name}";
            @move_uploaded_file($_FILES[$tbl]['tmp_name'][$num],$path);
            @chmod($path,0644);
						resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$new_name}",array('45','45'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/45x45/{$new_name}",1,'');
            resizeIm($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$new_name}",array('250','250'),$_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/250x250/{$new_name}",1,'');
          }
        }
      }
			
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка вверх
		case 'moveup':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_moveup($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_movedown($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_catalog($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
      {
				remove_catalog($id);
      }
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление изображения
		case 'img_del':
			remove_img($id,$tbl);
			?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
			break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Подчинение</th>
      <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id", 'name="id_parent" style="width:100%"', $row['id_parent'], array('0'=>'без подчинения'), $id)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('ссылка формируется автоматически,<br>значение данного поля можно изменить')?></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'])?></td>
    </tr>
		<?=show_tr_images($id,'Фото','',1,'catalog','catalog')?>
    <tr>
      <th class="tab_red_th"></th>
      <th>Описание</th>
      <td><?=showFck('text',$row['text'],'medium','100%',10)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"><?=help('используется вместо названия в &lt;h1&gt;')?></th>
      <th>Заголовок</th>
      <td><?=show_pole('text','h1',htmlspecialchars($row['h1']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>title</th>
      <td><?=show_pole('text','title',htmlspecialchars($row['title']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>keywords</th>
      <td><?=show_pole('text','keywords',htmlspecialchars($row['keywords']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>description</th>
      <td><?=show_pole('textarea','description',$row['description'])?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th></th>
      <td align="center">
        <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
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
	
	$page_title .= ' :: '.$rubric; 
	
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
	
	show_filters($script);
	
	if(!$sitemap){ ?>
	<div style="padding:5px 0 0 0;">Отобразить <a href="" style="color:#F60" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
  <div class="clear"></div>
  <? } ?>
  
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=@$_GET['id']?@(int)$_GET['id']:""?>" />
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th><img src="img/image.png" title="изображение" /></th>
      <th nowrap width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name')?></th>
       <? if($sitemap){?>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
			<? }?>
      <th nowrap width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link')?></th>
			<th nowrap>Товары <?=help('кол-во товаров в данной рубрике')?></th>
      <th><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
      <?
      if(!$_SESSION['ss']['sort']) { ?><th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th><? }
      ?>
      <th style="padding:0 30px;"></th>
    </tr>
  <?
	$mas = getTree($query);
	if(sizeof($mas))
	{
		$i=1;
		foreach($mas as $vetka)
		{
			$row = $vetka['row'];
			$level = $vetka['level'];
			$id = $row['id'];
			
			$prfx = $prefix===NULL ? getPrefix($level) : str_repeat($prefix, $level);
			?>
			<tr id="row<?=$id?>">
        <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
        <th><?=$i++?></th>
        <th style="padding:3px 5px;">
          <?
          $src = '/uploads/no_photo.jpg';
          $big_src = '/uploads/no_photo.jpg';
          if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/catalog/{$id}.jpg")){
            $src = "/uploads/catalog/45x45/{$id}.jpg";
            $big_src = "/uploads/catalog/{$id}.jpg";
          }
          ?>
          <a href="<?=$big_src?>" class="highslide" onclick="return hs.expand(this)">
            <img src="<?=$src?>" align="absmiddle" height="45" />
          </a>
        </th>
        <td nowrap><?=$prfx?><a href="?red=<?=$id?>" class="link1"<?=$style?>><?=$row['name']?></a></td>
        <? if($sitemap){?>
        <th class="sitemap"><input type="text" class="datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
        <th class="sitemap"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
        <th class="sitemap"><input type="text" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" style="text-align:center; width:30px;" /></th>
        <? }?>
        <td><?=getCatUrl($row,true)?></td>
        <td align="center">
          <?
          if($count = getCountSub($id))
          {
            ?><a href="" style="color:#090" onClick="RegSessionSort('goods.php','catalog=<?=$id?>');return false;"><?=$count?></a><?
          }
          else
            echo '0';
          ?>
        </td>
        <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=')?></td>
        <?
        if(!$_SESSION['ss']['sort'])
          echo "<td nowrap align='center'>".btn_sort($id)."</td>";
        ?>
        <td nowrap align="center"><?=btn_edit($id)?></td>
			</tr>
			<?
		}	
	}
	else
	{
		?>
    <tr>
      <td colspan="100" align="center">
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