<?
require('inc/common.php');

$h1 = 'Абонементы';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'tickets';

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	$id = (int)@$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'saveall':
			updateSitemap();
      jAlert('Данные успешно сохранены');
			break;
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$name) jAlert('Укажите название');

			$updateLink = false;
			$where = $id ? " and id<>{$id}" : "";

			if($link){
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			} else {
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			}

			$set = "name = '{$name}',
			        text = ".($text ? "'{$text}'" : 'NULL').",
			        price = '{$price}',
			        old_price = '{$old_price}',
			        validity = ".($validity ? "'{$validity}'" : 'NULL').",
			        age = '{$age}',
			        seance = ".($seance ? "'{$seance}'" : 'NULL').",
			        ids_type = ".(sizeof($_POST['ids_type']) > 0 ? "'".implode(',', $_POST['ids_type'])."'" : 'NULL').",
			        ids_who = ".(sizeof($_POST['ids_who']) > 0 ? "'".implode(',', $_POST['ids_who'])."'" : 'NULL').",
			        ids_disease = ".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
			        status = '{$status}',
							h1 = " . ($h1 ? "'{$h1}'" : "NULL") . ",
							title = " . ($title ? "'{$title}'" : "NULL") . ",
							keywords = " . ($keywords ? "'{$keywords}'" : "NULL") . ",
							description = " . ($description ? "'{$description}'" : "NULL");
			if(!$updateLink) $set .= ",link='{$link}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

			if($updateLink)
				update($tbl,"link='".($link.'_'.$id)."'",$id);

			// загружаем картинку
			if(sizeof((array)$_FILES[$tbl]['name']))
			{
				foreach($_FILES[$tbl]['name'] as $num=>$null)
				{
					if(!$_FILES[$tbl]['name'][$num]) continue;

					remove_img($id, $tbl);
					$path = $_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg";
					@move_uploaded_file($_FILES[$tbl]['tmp_name'][$num],$path);
					@chmod($path,0644);

					break;
				}
			}

			?><script>top.location.href = '<?=sgp($HTTP_REFERER, 'id', $id, 1)?>';</script><?
			break;
		// ----------------- обновление статуса
		case 'status':
			update_flag($tbl,'status',$id);
		break;
		// ----------------- удаление банера
		case 'del':
			remove_object($id);
			?><script>top.location.href = top.url()</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v)
				remove_object($id);
			?><script>top.location.href = top.url()</script><?
		break;
		// ----------------- удаление изображения
		case 'img_del':
			remove_img($id,$tbl);
			?><script>top.location.href = '<?=$script?>?red=<?=$id?>'</script><?
			break;
	}
	exit;
}
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];

	$title .= ' :: ' . ($id ? $row['name'] . ' (редактирование)' : 'Добавление');
	$h = $id ? $row['name'] . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $row['name'] : 'Добавление');
	
	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Название</th>
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
      <tr>
        <th><?=help('ссылка формируется автоматически,<br>значение данного поля можно изменить')?></th>
        <th>Ссылка</th>
        <td><?=input('text', 'link', $row['link'])?></td>
      </tr>
      <?=show_tr_images($id,'Фото','Для корректного отображения,<br>рекомендуется загружать квадратное изображение размером 320x320 пискелей',1,$tbl,$tbl)?>
      <tr>
        <th></th>
        <th>Описание</th>
        <td><?=showCK('text', $row['text'], 'basic')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Цена</th>
        <td><?=input('text', 'price', $row['price'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Старая цена</th>
        <td><?=input('text', 'old_price', $row['old_price'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Срок действия</th>
        <td><?=input('text', 'validity', $row['validity'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Возрастные ограничения</th>
        <td><?=dllEnum($tbl,'age','name="age" class="form-control input-sm"',$row['age'])?></td>
      </tr>
      <tr>
        <th><?=help('например «около 30 сеансов»')?></th>
        <th>Кол-во сеансов</th>
        <td><?=input('text', 'seance', $row['seance'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Тип абонемента</th>
        <td><?=dll("SELECT * FROM {$prx}tickets_type ORDER BY name",'name="ids_type[]" multiple data-placeholder="Укажите тип абонемента" style="width:100%"',explode(',',$row['ids_type']),null,'chosen')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Тип посетителей</th>
        <td><?=dll("SELECT * FROM {$prx}tickets_who ORDER BY name",'name="ids_who[]" multiple data-placeholder="Укажите тип посетителей" style="width:100%"',explode(',',$row['ids_who']),null,'chosen')?></td>
      </tr>
      <tr>
        <th><?=help('Привязка к объектам из спр-ка болезней<br>для вывода на сайте (в нижней части) соответствующих статей')?></th>
        <th>Спр-к болезней</th>
        <td><?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="ids_disease[]" multiple data-placeholder="Укажите болезни" style="width:100%"',explode(',',$row['ids_disease']),null,'chosen')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
      <tr>
        <th><?=help('используется вместо названия в &lt;h1&gt;')?></th>
        <th>Заголовок</th>
        <td><?=input('text', 'h1', $row['h1'])?></td>
      </tr>
      <? foreach (array('title','keywords','description') as $v){?>
        <tr>
          <th></th>
          <th><?=$v?></th>
          <td><?=input('text', $v, $row[$v])?></td>
        </tr>
      <?}?>
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
	$fl['sitemap'] = isset($_GET['fl']['sitemap']);
	$fl['type'] = $_GET['fl']['type'];
	$fl['who'] = $_GET['fl']['who'];
	$fl['disease'] = $_GET['fl']['disease'];
	$fl['sort'] = $_GET['fl']['sort'];
	$fl['search'] = stripslashes($_GET['fl']['search']);

	$filters['type'] = "выбор абонементов по типу";
	$filters['who'] = "выбор абонементов по типу посетителей";
	$filters['disease'] = 'выбор объектов по спр-ку болезней';

	$where = '';
	if($fl['type']){
	  $where .= "\r\nAND CONCAT(',',ids_type,',') LIKE '%,{$fl['type']},%'";
	}
	if($fl['who']){
	  $where .= "\r\nAND CONCAT(',',ids_who,',') LIKE '%,{$fl['who']},%'";
	}
	if($fl['disease']){
		$where .= "\r\nAND CONCAT(',',ids_disease,',') LIKE '%,{$fl['disease']},%'";
	}
	if($fl['search'] != ''){
	  $sf = array('name','link','text','price','old_price','validity','age','h1','title','keywords','description');
		$w = '';
		foreach ($sf as $field){
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($fl['sitemap']){
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= "\r\nLEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else {
	  $query  = sprintf($query,'');
	}

	$query .= "\r\nWHERE 1{$where}";

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
		$query .= "\r\nORDER BY A.name";
	}

	$query .= "\r\nLIMIT " . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;

	ob_start();
  //pre($query);

	show_listview_btns(($fl['sitemap'] ? 'Сохранить::' : '') . 'Добавить::Удалить');
	ActiveFilters();

	if(!$fl['sitemap']){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="changeURI({'fl[sitemap]':''});return false;">Sitemap поля</a></div>
	<? } ?>

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
      <div class="item">
        <label>Тип абонемента</label>
				<?=dll("SELECT * FROM {$prx}tickets_type ORDER BY name",'name="fl[type]" multiple data-placeholder="-- неважно --"',$fl['type']?explode(',',$fl['type']):null,null,'chosen')?>
      </div>
      <div class="item">
        <label>Тип посетителей</label>
				<?=dll("SELECT * FROM {$prx}tickets_who ORDER BY name",'name="fl[who]" multiple data-placeholder="-- неважно --"',$fl['who']?explode(',',$fl['who']):null,null,'chosen')?>
      </div>
      <div class="item">
        <label>Спр-к болезней</label>
				<?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="fl[disease]" multiple data-placeholder="-- неважно --"',$fl['disease']?explode(',',$fl['disease']):null,null,'chosen')?>
      </div>
      <div class="item search">
        <label>Контекстный поиск</label><br>
        <div><?=input('text', 'fl[search]', $fl['search'])?></div>
      </div>
      <button class="btn btn-danger" onclick="setFilters()"><i class="fas fa-search"></i>Поиск</button>
    </div>
  </div>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form id="ftl" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
      <tr>
        <th width="1%"><input type="checkbox" name="check_del" id="check_del" /></th>
        <th width="1%">№</th>
        <th width="1%" style="text-align:center;"><img src="img/image.png" title="изображение" /></th>
        <th nowrap width="30%"><?=SortColumn('Название','A.name')?></th>
        <th nowrap>Ссылка</th>
        <? if($fl['sitemap']){?>
          <th nowrap><?=SortColumn('lastmod','S.lastmod')?></th>
          <th nowrap><?=SortColumn('changefreq','S.changefreq')?></th>
          <th nowrap><?=SortColumn('priority','S.priority')?></th>
        <? }?>
        <th nowrap><?=SortColumn('Цена, руб.','A.price')?></th>
        <th>Срок действия</th>
        <th>Возраст</th>
        <th>Типы абонемента</th>
        <th>Типы посетителей</th>
        <th width="1%">Статус</th>
        <th width="1%" style="padding:0 30px;"></th>
      </tr>
    </thead>
    <tbody>
    <?
    $res = sql($query);
    if(mysqli_num_rows($res)){
      $i=1;
      while($row = mysqli_fetch_assoc($res)){
        $id = $row['id'];
        ?>
        <tr id="item-<?=$row['id']?>">
          <th><input type="checkbox" name="del[<?=$id?>]"></th>
          <th nowrap><?=$i++?></th>
          <th>
            <?
            $src = '/uploads/no_photo.jpg';
            $big_src = '/uploads/no_photo.jpg';
            if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
              $src = "/{$tbl}/60x60/{$id}.jpg";
              $big_src = "/{$tbl}/{$id}.jpg";
            }
            ?>
            <a href="<?=$big_src?>" class="blueimp" title="<?=htmlspecialchars($row['name'])?>">
              <img src="<?=$src?>" align="absmiddle" style="max-height:60px; max-width:60px;" class="img-rounded">
            </a>
          </th>
          <td class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
          <td style="text-align:center"><a href="/tickets/<?=$row['link']?>.htm" class="clr-green im-lnk" target="_blank"><i class="fas fa-link"></i></a></td>
          <? if($fl['sitemap']){?>
            <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
            <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
            <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
          <? }?>
          <th class="sp" nowrap><?=$row['price'] . ($row['old_price'] ? ' (<s>'.$row['old_price'].'</s>)' : '')?></th>
          <th class="sp" nowrap><?=$row['validity']?></th>
          <th class="sp" nowrap><?=$row['age']?></th>
          <th><?
            $q = sql("SELECT * FROM {$prx}tickets_type where id IN (" . ($row['ids_type'] ?: '0' ) . ")");
            while ($arr = @mysqli_fetch_assoc($q)){
              ?><div><?=$arr['name']?>;</div><?
            }
          ?></th>
          <th><?
            $q = sql("SELECT * FROM {$prx}tickets_who where id IN (" . ($row['ids_who'] ?: '0' ) . ")");
            while ($arr = @mysqli_fetch_assoc($q)){
              ?><div><?=$arr['name']?>;</div><?
            }
          ?></th>
          <th><?=btn_flag($row['status'],$id,'action=status&id=')?></th>
          <th nowrap><?=btn_edit($id)?></th>
        </tr>
        <?
      }
    } else {
      ?>
      <tr class="nofind">
        <td colspan="15">
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