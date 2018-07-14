<?
require('inc/common.php');

$h1 = 'Страницы';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'pages';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
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

			if(!$name) jAlert('необходимо указать название !');
				
			if($locked){
				$set = "preview=".($preview?"'{$preview}'":"NULL").",
				        text=".($text?"'{$text}'":"NULL").",
								h1=".($h1?"'{$h1}'":"NULL").",
								title=".($title?"'{$title}'":"NULL").",
								keywords=".($keywords?"'{$keywords}'":"NULL").",
								description=".($description?"'{$description}'":"NULL");
				update($tbl,$set,$id);
				?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
				exit;
			}

			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';

			if($type=='page'){
				if($link){
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				} else {
					$link = makeUrl($name);
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
			}

			$set = "id_parent='{$id_parent}',
							name='{$name}',
							preview=".($preview?"'{$preview}'":"NULL").",
				      text=".($text?"'{$text}'":"NULL").",
							ids_disease=".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
							type='{$type}',
							is_main='{$is_main}',
							is_bmain='{$is_bmain}',
							is_slider='{$is_slider}',
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
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
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?		
			break;
		// ----------------- обновление в меню
		case 'is_main':
		case 'is_bmain':
		case 'is_slider':
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			if(gtv($tbl,'locked',$id))
				jAlert("данная страница защищена от удаления!");
			else
				remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v)
				if(!gtv($tbl,'locked',$id))
					remove_object($id);
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
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];
	$locked = $row['locked'];
	$readonly = $locked ? ' readonly' : '';

	$title .= ' :: ' . ($id ? $row['name'] . ' (редактирование)' : 'Добавление');
	$h = $id ? $row['name'] . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $row['name'] : 'Добавление');
	
	ob_start();
	?>
  <style>
    .for-page { display:<?=$row['type']=='link'?'none':'table-row'?>;}
  </style>
  <script>
    $(function () {
      //
      $('select[name="type"]').change(function () {
        var val = $(this).find('option:selected').val();
        if(val=='link'){ $('.for-page').hide(); }
        else { $('.for-page').show(); }
      });
    })
  </script>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <input type="hidden" name="locked" value="<?=$locked?>" />
  <table class="table-edit">
    <? if(!$locked){ ?>
    <tr>
      <th></th>
      <th>Подчинение</th>
      <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id",'name="id_parent"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
    </tr>
    <? } ?>
    <tr>
      <th></th>
      <th>Название</th>
      <td><?=input('text', 'name', $row['name'], $readonly)?></td>
    </tr>
    <tr>
      <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
      <th>Ссылка</th>
      <td><?=input('text', 'link', $row['link'], $readonly)?></td>
    </tr>
		<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
    <tr>
      <th></th>
      <th>Краткое<br />описание</th>
      <td><?=showCK('preview',$row['preview'],'basic')?></td>
    </tr>
    <tr>
      <th></th>
      <th>Текст</th>
      <td><?=showCK('text',$row['text'])?></td>
    </tr>
    <tr class="for-page">
      <th><?=help('Привязка к объектам из спр-ка болезней<br>для вывода на сайте (в нижней части) соответствующих статей')?></th>
      <th>Спр-к болезней</th>
      <td><?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="ids_disease[]" multiple data-placeholder="Укажите болезни" style="width:100%"',explode(',',$row['ids_disease']),null,'chosen')?></td>
    </tr>
    <?
    if(!$locked)
    {
      ?>
      <tr>
        <th></th>
        <th>Тип</th>
        <td><?=dll(array('page'=>'страница','link'=>'ссылка'),' name="type"',$row['type'])?></td>
      </tr>
      <tr>
        <th><?=help('отображать объект в главном меню')?></th>
        <th>Главное меню</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_main"',$row['is_main'])?></td>
      </tr>
      <tr>
        <th><?=help('отображать объект в футере сайта')?></th>
        <th>В футер</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_bmain"',$row['is_bmain'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
      <?
    }
    ?>
    <tr class="for-page">
      <th><?=help('отображать объект в слайдере<br>на главной странице')?></th>
      <th>В слайдер</th>
      <td><?=dll(array('0'=>'нет','1'=>'да'),'name="is_slider"',$row['is_slider'])?></td>
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
	$fl['sitemap'] = isset($_GET['fl']['sitemap']);
	$fl['sort'] = $_GET['fl']['sort'];

	$query = "SELECT A.*%s FROM {$prx}{$tbl} A";
	if($fl['sitemap'])
	{
		$query  = sprintf($query,',S.lastmod,S.changefreq,S.priority');
		$query .= " LEFT JOIN (SELECT * FROM {$prx}sitemap WHERE `type`='{$tbl}') S ON A.id=S.id_obj";
	}	else $query  = sprintf($query,'');

	ob_start();
	// проверяем текущую сортировку и формируем соответствующий запрос
	if($fl['sort']){
		foreach ($fl['sort'] as $f => $t){
			$query .= " ORDER BY {$f} {$t}";
		  break;
    }
  } else {
		$query .= ' ORDER BY A.sort,A.id';
  }

  show_listview_btns(($fl['sitemap'] ? 'Сохранить::' : '') . 'Добавить::Удалить');
	ActiveFilters();

	if(!$fl['sitemap']){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="changeURI({'fl[sitemap]':''});return false;">Sitemap поля</a></div>
  <? } ?>

  <div class="clearfix"></div>

  <form id="ftl" method="post" target="ajax">
  <table class="table-list" tbl="<?=$tbl?>">
    <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
				<? if(!$fl['sort']) { ?><th nowrap><?=help('параметр с помощью которого можно изменить<br>порядок вывода объектов в клиентской части сайта')?></th><? }?>
        <th><img src="img/image.png" title="изображение" /></th>
        <th width="50%">Название<?//=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name')?></th>
        <? if($fl['sitemap']){?>
        <th nowrap><?=SortColumn('lastmod','S.lastmod')?></th>
        <th nowrap><?=SortColumn('changefreq','S.changefreq')?></th>
        <th nowrap><?=SortColumn('priority','S.priority')?></th>
        <? }?>
        <th width="50%"><?=SortColumn('Ссылка','link')?></th>
        <th nowrap><?=SortColumn('Тип','type')?></th>
        <th nowrap><?=SortColumn('Глав. меню','is_main')?> <?=help('отображать объект в главном меню')?></th>
        <th nowrap><?=SortColumn('Футер','is_bmain')?> <?=help('отображать объект в футере сайта')?></th>
        <th nowrap><?=SortColumn('В слайдер','is_slider')?> <?=help('отображать объект в слайдере<br>на главной странице')?></th>
        <th nowrap><?=SortColumn('Статус','status')?></th>
        <th style="padding:0 30px;"></th>
      </tr>
    </thead>
    <tbody>
    <?
    $mas = getTree($query);
    if(sizeof($mas))
    {
      $i=1;
      ?><?
      foreach($mas as $vetka)
      {
        $row = $vetka['row'];
        $level = $vetka['level'];

        $id = $row['id'];
        $locked = $row['locked'];
        $link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
        $prfx = $prefix===NULL ? getPrefix($level) : str_repeat($prefix, $level);
        $childs = getIdChilds("SELECT * FROM {$prx}{$tbl}", $id);
        $has_childs = sizeof($childs) > 1;

        ?>
        <tr id="item-<?=$id?>" oid="<?=$id?>" par="<?=$row['id_parent']?>" class="<?=$has_childs?' has-childs':''?>">
          <th><? if(!$locked){ ?><input type="checkbox" name="del[<?=$id?>]"><? }?></th>
          <th nowrap><?=$i++?></th>
          <? if(!$fl['sort']){ ?><th nowrap align="center"><i class="fas fa-sort"></i></th><? }?>
          <th style="padding:3px 5px;">
            <?
            $src = '/uploads/no_photo.jpg';
            $big_src = '/uploads/no_photo.jpg';
            if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){
              $src = "/{$tbl}/20x20/{$id}.jpg";
              $big_src = "/{$tbl}/{$id}.jpg";
            }
            ?>
            <a href="<?=$big_src?>" class="blueimp" title="<?=htmlspecialchars($row['name'])?>">
              <img src="<?=$src?>" align="absmiddle" style="max-height:20px" />
            </a>
          </th>
          <td><?=$prfx?><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
          <? if($fl['sitemap']){?>
            <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
            <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
            <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
          <? }?>
          <td><?=$row['type']=='page'?'/':''?><a href="<?=$link?>" class="clr-green" target="_blank"><?=$row['link']?></a><?=$row['type']=='page'?'.htm':''?></td>
          <th><?=$row['type']=='page'?'страница':'ссылка'?></th>
          <th><?=btn_flag($row['is_main'],$id,'action=is_main&id=',$locked)?></th>
          <th><?=btn_flag($row['is_bmain'],$id,'action=is_bmain&id=',$locked)?></th>
          <th><?=btn_flag($row['is_slider'],$id,'action=is_slider&id=',$locked)?></th>
          <th><?=btn_flag($row['status'],$id,'action=status&id=',$locked)?></th>
          <th nowrap><?=btn_edit($id,$locked)?></th>
        </tr>
        <?
      }
    } else {
      ?>
      <tr class="nofind">
        <td colspan="10">
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
  <?
	$content = arr($h, ob_get_clean());
}

require('tpl/template.php');