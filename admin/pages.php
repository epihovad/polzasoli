<?
require('inc/common.php');

$h1 = 'Страницы';
$tbl = 'pages';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);

			if(!$name) errorAlert('необходимо указать название !');
				
			if($locked)
			{
				$set = "text=".($text?"'{$text}'":"NULL").",
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
			
			if($type=='page')
			{
				if($link)
				{
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
				else
				{
					$link = makeUrl($name);
					if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
						$updateLink = true;
				}
			}
			
			$set = "id_parent='{$id_parent}',
							name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
							ids_disease=".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
							type='{$type}',
							is_main='{$is_main}',
							is_slider='{$is_slider}',
							status='{$status}',
							h1=".($h1?"'{$h1}'":"NULL").",
							title=".($title?"'{$title}'":"NULL").",
							keywords=".($keywords?"'{$keywords}'":"NULL").",
							description=".($description?"'{$description}'":"NULL");
			if(!$updateLink) $set .= ",link='{$link}'";
				
			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');
			
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
		case 'is_slider':
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
			if(gtv($tbl,'locked',$id))
				errorAlert("данная страница защищена от удаления!");
			else
				remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
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
	$id = (int)$_GET['red'];

	$navigate .= $id ? 'Редактирование' : 'Добавление';
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	$locked = $row['locked'];
	
	ob_start();
	?>
  <link rel="stylesheet" href="/js/chosen/chosen.min.css">
  <style>
    .for-page { display:<?=$row['type']=='link'?'none':'table-row'?>;}
    .chosen-search-input { width:auto !important;}
  </style>
  <script src="/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
  <script>
    $(function () {
      //
      $('select[name="type"]').change(function () {
        var val = $(this).find('option:selected').val();
        if(val=='link'){ $('.for-page').hide(); }
        else { $('.for-page').show(); }
      });
      //
      $('select[name="ids_disease\[\]"]').chosen({
        no_results_text: "Нет данных по запросу",
      });
    })
  </script>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <input type="hidden" name="locked" value="<?=$locked?>" />
  <table class="table-edit">
    <tr>
      <th></th>
      <th>Подчинение</th>
      <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id",'name="id_parent"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
    </tr>
    <tr>
      <th></th>
      <th>Название</th>
      <td><?=show_pole('text','name',htmlspecialchars($row['name']),$locked)?></td>
    </tr>
    <tr>
      <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
      <th>Ссылка</th>
      <td><?=show_pole('text','link',$row['link'],$locked)?></td>
    </tr>
    <tr>
      <th></th>
      <th>Дата</th>
      <td><input type="text" class="form-control input-sm datepicker"></td>
    </tr>
		<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
    <tr>
      <th></th>
      <th>Краткое<br />описание</th>
      <td><?=showCK('preview',$row['preview'],'basic','100%',20)?></td>
    </tr>
    <tr class="for-page">
      <th></th>
      <th>Текст</th>
      <td><?=showCK('text',$row['text'])?></td>
    </tr>
    <tr class="for-page">
      <th><?=help('Привязка к объектам из спр-ка болезней<br>для вывода на сайте (в нижней части) соответствующих статей')?></th>
      <th>Спр-к болезней</th>
      <td><?=dll("SELECT * FROM {$prx}disease ORDER BY name",'name="ids_disease[]" multiple data-placeholder="Укажите болезни" style="width:100%"',explode(',',$row['ids_disease']))?></td>
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
    <tr class="for-page">
      <th><?=help('используется вместо названия в &lt;h1&gt;')?></th>
      <th>Заголовок</th>
      <td><?=show_pole('text','h1',htmlspecialchars($row['h1']))?></td>
    </tr>
    <tr class="for-page">
      <th></th>
      <th>title</th>
      <td><?=show_pole('text','title',htmlspecialchars($row['title']))?></td>
    </tr>
    <tr class="for-page">
      <th></th>
      <th>keywords</th>
      <td><?=show_pole('text','keywords',htmlspecialchars($row['keywords']))?></td>
    </tr>
    <tr class="for-page">
      <th></th>
      <th>description</th>
      <td><?=show_pole('textarea','description',$row['description'])?></td>
    </tr>
  </table>
  <div class="frm-btns">
    <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="btn btn-success btn-sm" onclick="loader(true)" />&nbsp;
    <input type="button" value="Отмена" class="btn btn-default btn-sm" onclick="location.href='<?=$script?>'" />
  </div>
  </form>
  <?
	$content = arr($navigate, ob_get_clean());
}
// -----------------ПРОСМОТР-------------------
else
{
	$cur_page = $_SESSION['ss']['page'] ? $_SESSION['ss']['page'] : 1;
	$sitemap = isset($_SESSION['ss']['sitemap']);

	$page_title .= ' :: '.$rubric;
	$navigate .= 'Общий список';

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

  show_listview_btns(($sitemap ? 'Сохранить::' : '') . 'Добавить::Удалить');
	show_filters($script);

	if(!$sitemap){ ?>
    <div style="padding:10px 0 10px 0;">Отобразить <a href="" class="clr-orange" onclick="RegSessionSort('<?=$script?>','sitemap');return false;">Sitemap поля</a></div>
  <? } ?>

  <div class="clearfix"></div>

  <script>
    $(function () {
      $('table.table-list tbody').sortable({
        helper: fixWidthHelper,
        axis: 'y',
        /*containment: 'parent',*/
        cursor: 'move',
        handle: '.fa-sort',
        start: function(event, ui){
          /*var id = ui.item.attr('oid');
          var $dragged = $(this).find('tr[par="'+id+'"]');
          $dragged.appendTo(ui.item);*/
        },
        stop: function(event, ui){

        },
        update: function (event, ui) {
          var cur = { 'id' : ui.item[0].attributes.oid.value, 'par' : ui.item[0].attributes.par.value, };
          var prev = { 'id' : 0, 'par' : 0, 'has-childs' : false, };
          var next = { 'id' : 0, 'par' : 0, 'has-childs' : false, };
          var $prev, $next;
          try {
            prev = {
              'id' : ui.item[0].previousElementSibling.attributes.oid.value,
              'par' : ui.item[0].previousElementSibling.attributes.par.value,
              'has-childs' : strpos(ui.item[0].nextElementSibling.className, 'has-childs'),
            };
          } catch (e){}
          try {
            next = {
              'id' : ui.item[0].nextElementSibling.attributes.oid.value,
              'par' : ui.item[0].nextElementSibling.attributes.par.value,
              'has-childs' : strpos(ui.item[0].nextElementSibling.className, 'has-childs'),
            };
          } catch (e){}

          // допускается ли перемещение
          if(cur.par != prev.par && cur.par != next.par){
            $(this).sortable('cancel');
            $(document).jAlert('show','alert','сортировать строки возможно лишь в рамках одного уровня');
            return ui;
          }

          var childs = [];
          getTree(cur.id, childs);
          var $tree = ui.item;
          var $last = ui.item;
          childs.forEach(function($e) {
            $tree = $tree.add($e);
            $e.detach().insertAfter($last);
            $last = $e;
          });

          $tree.effect("highlight", {}, 1000);

          //var data = $(this).sortable('serialize');
          //console.log(data);

          //console.log(prev.id + ' - ' + prev.par);
          /*if(curRowClass != prevRowClass || curRowClass != nextRowClass){
            $(this).sortable('cancel',{revert:600});
            //alert('сортировать строки возможно лишь в рамках одного уровня');
          }*/
          //var data = $(this).sortable('serialize');
          //console.log(data);
          // POST to server using $.post or $.ajax
          /*$.ajax({
						data: data,
						type: 'POST',
						url: '/your/url/here'
					});*/
        }
      });
    });
    function fixWidthHelper(event, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    }
    function getTree (id, arr) {
      var $ch = $('tr[par="' + id + '"');
      $ch.each(function () {
        arr.push($(this));
        getTree($(this).attr('oid'), arr);
      });
      return arr;
    }
</script>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
        <th><img src="img/image.png" title="изображение" /></th>
        <th width="50%">Название<?//=ShowSortPole($script,$cur_pole,$cur_sort,'Название','name')?></th>
        <? if($sitemap){?>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'lastmod','S.lastmod')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'changefreq','S.changefreq')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'priority','S.priority')?></th>
        <? }?>
        <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ссылка','link')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Тип','type')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Глав. меню','is_main')?> <?=help('отображать объект в главном меню')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'В слайдер','is_slider')?> <?=help('отображать объект в слайдере<br>на главной странице')?></th>
        <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
        <? if(!$_SESSION['ss']['sort']) { ?><th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th><? }?>
        <th style="padding:0 30px;"></th>
      </tr>
    </thead>
  <?
	$mas = getTree($query);
	if(sizeof($mas))
	{
		$i=1;
		?><tbody><?
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
				<th><? if(!$locked){ ?><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /><? }?></th>
				<th nowrap><?=$id/*=$i++*/?></th>
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
				<td><?=$prfx?><a href="?red=<?=$id?>" class="link1"><?=$row['name']?></a></td>
				<? if($sitemap){?>
          <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
          <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
          <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
				<? }?>
				<td><?=$row['type']=='page'?'/':''?><a href="<?=$link?>" class="clr-green" target="_blank"><?=$row['link']?></a><?=$row['type']=='page'?'.htm':''?></td>
				<th><?=$row['type']=='page'?'страница':'ссылка'?></th>
				<th><?=btn_flag($row['is_main'],$id,'action=is_main&id=',$locked)?></th>
        <th><?=btn_flag($row['is_slider'],$id,'action=is_slider&id=',$locked)?></th>
				<th><?=btn_flag($row['status'],$id,'action=status&id=',$locked)?></th>
				<? if(!$_SESSION['ss']['sort']){ ?><th nowrap align="center"><i class="fas fa-sort"></i></th><? }?>
				<th nowrap><?=btn_edit($id,$locked)?></th>
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
  </tbody>
  </table>
  </form>
  <?
	$content = arr($navigate, ob_get_clean(), 'Date - <small class="text-success">20:08:2014</small>');
}

require('tpl/template.php');