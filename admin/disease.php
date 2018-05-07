<?
require('inc/common.php');

$h1 = 'Справочник болезней';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>Общий список';
$tbl = 'disease';
$menu = getRow("SELECT * FROM {$prx}am WHERE link = '{$tbl}' ORDER BY id_parent DESC LIMIT 1");

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
			
			$set = "name='{$name}',
							text=".($text?"'{$text}'":"NULL").",
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

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?		
			break;
		// ----------------- обновление в меню
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v){
				remove_object($id);
      }
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];

	$title .= ' :: ' . ($id ? $row['name'] . ' (редактирование)' : 'Добавление');
	$h = $id ? $row['name'] . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $row['name'] : 'Добавление');
	
	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table class="table-edit">
    <tr>
      <th></th>
      <th>Название</th>
      <td><?=input('text', 'name', $row['name'])?></td>
    </tr>
    <tr>
      <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
      <th>Ссылка</th>
      <td><?=input('text', 'link', $row['link'])?></td>
    </tr>
    <tr>
      <th></th>
      <th>Текст</th>
      <td><?=showCK('text',$row['text'])?></td>
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
	$fl['sort'] = $_GET['fl']['sort'];
	$fl['search'] = stripslashes($_GET['fl']['search']);

	$where = '';
	if($fl['search'] != ''){
		$sf = array('name','link','text','h1','title','keywords','description');
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
	}	else{
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
      <div class="form-group search">
        <label>Контекстный поиск</label><br>
        <input class="form-control input-sm" type="text" value="<?=htmlspecialchars($fl['search'])?>">
        <button type="button" class="btn btn-danger btn-xs"><i class="fas fa-search"></i>найти</button>
      </div>
    </div>
  </div>

  <?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th width="50%"><?=SortColumn('Название','A.name')?></th>
			<? if($fl['sitemap']){?>
        <th nowrap><?=SortColumn('lastmod','S.lastmod')?></th>
        <th nowrap><?=SortColumn('changefreq','S.changefreq')?></th>
        <th nowrap><?=SortColumn('priority','S.priority')?></th>
			<? }?>
      <th nowrap width="50%"><?=SortColumn('Ссылка','link');?></th>
      <th nowrap><?=SortColumn('Статус','status');?></th>
      <th style="padding:0 30px;"></th>
    </tr>
    </thead>
    <tbody>
    <?
    $res = sql($query);
    if(mysqli_num_rows($res))
    {
      $i=1;
      while($row = mysqli_fetch_assoc($res))
      {
        $id = $row['id'];
				?>
        <tr id="item-<?=$row['id']?>">
          <th><input type="checkbox" name="check_del_[<?=$row['id']?>]" id="check_del_<?=$row['id']?>" /></th>
          <th nowrap><?=$i++?></th>
          <td class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></td>
					<? if($fl['sitemap']){?>
            <th class="sitemap sm-lastmod"><input type="text" class="form-control input-sm datepicker" name="lastmod[<?=$id?>]" value="<?=(isset($row['lastmod'])?date('d.m.Y',strtotime($row['lastmod'])):date("d.m.Y"))?>" /></th>
            <th class="sitemap sm-changefreq"><?=dll(array('always'=>'always','hourly'=>'hourly','daily'=>'daily','weekly'=>'weekly','monthly'=>'monthly','yearly'=>'yearly','never'=>'never'),'name="changefreq['.$id.']"',$row['changefreq']?$row['changefreq']:'monthly')?></th>
            <th class="sitemap sm-priority"><input type="text" class="form-control input-sm" name="priority[<?=$id?>]" value="<?=$row['priority']?$row['priority']:'0.5'?>" maxlength="3" /></th>
					<? }?>
          <td class="sp">/disease/<a href="/disease/<?=$row['link']?>.htm" class="clr-green" target="_blank"><?=$row['link']?></a>.htm</td>
          <th><?=btn_flag($row['status'],$id,'action=status&id=')?></th>
          <th nowrap><?=btn_edit($id)?></th>
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
	<?=pagination($count_page, $cur_page, true, 'padding:10px 0 0;')?>
	<?
	$content = arr($h, ob_get_clean());
}

require('tpl/template.php');