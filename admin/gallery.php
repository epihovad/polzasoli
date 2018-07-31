<?
require('inc/common.php');

$h1 = 'Фотогелерея';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'gallery';

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

			if(!$name) jAlert('необходимо указать название !');

			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';

			if($link){
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			} else {
				$link = makeUrl($name);
				if(getField("SELECT id FROM {$prx}{$tbl} WHERE link='{$link}'{$where}"))
					$updateLink = true;
			}

			// полная ссылка на фото
			$rb = gtv('gallery_catalog','*',$id_catalog);
			$url = getCatUrl($rb,false,'gallery_catalog','gallery');

			$set = "id_catalog = '{$id_catalog}',
			        url = '{$url}',
							name = '{$name}',
							text = ".($text?"'{$text}'":"NULL").",
							status = '{$status}',
							`date` = '" . ($date ? formatDateTime($date) : date('Y-m-d')) . "'";
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
		// ----------------- статус
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = top.url()</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v) {
				remove_object($id);
			}
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
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Рубрика</th>
        <td><?=dllTree("SELECT * FROM {$prx}gallery_catalog ORDER BY sort,id",'name="id_catalog"',$row['id_catalog'],array('0'=>'без подчинения'),$id)?></td>
      </tr>
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
			<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
      <tr>
        <th><?=help('При добавлении/изменении объекта, если поле пустое,<br>дата формируется автоматически (присваивается текущая дата).<br>Дата служит для сортировки объектов в клиентской части сайта.')?></th>
        <th>Дата добавления</th>
        <td><?=input('date', 'date', isset($row['date']) ? date('d.m.Y', strtotime($row['date'])) : date('d.m.Y'))?></td>
      </tr>
      <tr>
        <th></th>
        <th>Описание</th>
        <td><?=showCK('text',$row['text'], 'basic')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Статус</th>
        <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
      </tr>
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
elseif(isset($_GET['view']))
{

	ob_start();
	?>
  <link href="css/google-grid.css" rel="stylesheet" media="screen">

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div id="grid-gallery" class="grid-gallery">
        <section class="grid-wrap">
          <ul class="grid">
            <li class="grid-sizer"></li><!-- for Masonry column width -->
						<? for($i=1; $i<=10; $i++){ ?>
              <li>
                <figure>
                  <img src="img/users/user<?=$i?>.jpg" alt="img<?=$i?>"/>
                  <figcaption>
                    <div class="ribbon">
                      <div class="name">Image Name</div>
                    </div>
                  </figcaption>
                </figure>
              </li>
						<?}?>
          </ul>
        </section>
        <section class="slideshow">
          <ul>
						<? for($i=1; $i<=10; $i++){ ?>
              <li>
                <figure>
                  <figcaption>
                    <h3>Ever since the 1500s</h3>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                  </figcaption>
                  <img src="img/users/user<?=$i?>.jpg" alt="img<?=$i?>"/>
                </figure>
              </li>
						<?}?>
          </ul>
          <nav>
            <span class="icon nav-prev"></span>
            <span class="icon nav-next"></span>
            <span class="icon nav-close"></span>
          </nav>
        </section>
      </div>
    </div>
  </div>

  <script src="/js/modernizr.custom.js"></script>
  <!-- Google Grid JS -->
  <script src="/js/GridGallery/js/imagesloaded.pkgd.min.js"></script>
  <script src="/js/GridGallery/js/masonry.pkgd.min.js"></script>
  <script src="/js/GridGallery/js/classie.js"></script>
  <script src="/js/GridGallery/js/cbpGridGallery.js"></script>
  <script type="text/javascript">
    new CBPGridGallery( document.getElementById( 'grid-gallery' ) );
  </script>
	<?
	$content = ob_get_clean();
}
// -----------------СПИСОК-------------------
else {

	$cur_page = (int)$_GET['page'] ?: 1;
	$fl['catalog'] = (int)$_GET['fl']['gallery_catalog'];
	$fl['search'] = stripslashes($_GET['fl']['search']);

	$where = '';
	if($fl['catalog']){
		$ids = getIdChilds("SELECT * FROM {$prx}{$tbl}_catalog",$fl['catalog'],false);
		$where .= "\r\nAND id_catalog IN ({$ids})";
	}
	if($fl['search'] != ''){
		$sf = array('name','text','url','link');
		$w = '';
		foreach ($sf as $field){
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT * FROM {$prx}{$tbl}\r\nWHERE 1{$where}";

	$r = sql($query);
	$count_obj = @mysqli_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$count_page = ceil($count_obj/$count_obj_on_page); // количество страниц

  $query .= "\r\nORDER BY `date`,id LIMIT " . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;

  ob_start();
	//pre($query);

	show_listview_btns('Добавить::Удалить');
	ActiveFilters();
	?>

  <div class="clearfix"></div>

  <? $show_filters = $fl['catalog'] || $fl['search']; ?>
  <div id="filters" class="panel-white">
    <h4 class="heading">Фильтры
      <a href="#"<?//=$show_filters?' class="active"':''?>>
        <i class="fas fa-eye" title="показать фильтры">
        </i><i class="fas fa-eye-slash" title="скрыть фильтры"></i>
      </a>
    </h4>
    <div class="fbody<?//=$show_filters?' active':''?>">
      <div class="form-group">
        <label>Рубрика <?=help('отображаются объекты выбранной рубрики<br>(вместе с объектами подчинённых рубрик)')?></label>
				<?=dllTree("SELECT * FROM {$prx}{$tbl}_catalog ORDER BY sort,id",'onChange="changeURI({\'fl[gallery_catalog]\':this.value});return false;"',$fl['catalog'],array('null'=>'-- все --'))?>
      </div>
      <div class="form-group search">
        <label>Контекстный поиск</label><br>
        <input class="form-control input-sm" type="text" value="<?=htmlspecialchars($fl['search'])?>">
        <button type="button" class="btn btn-danger btn-xs"><i class="fas fa-search"></i>найти</button>
      </div>
    </div>
  </div>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form id="ftl" method="post" target="ajax">
    <table class="table-list">
      <thead>
      <tr>
        <th><input type="checkbox" name="del" /></th>
        <th>№</th>
				<th style="text-align:center"><img src="img/image.png" title="Фото" /></th>
        <th width="<?=$fl['catalog']?'30':'20'?>%"><?=SortColumn('Название','name')?></th>
        <th width="<?=$fl['catalog']?'70':'40'?>%"><?=SortColumn('Ссылка','link')?></th>
				<th width="40%">Рубрика</th>
				<? if(!$fl['sort']) { ?><th nowrap><?=SortColumn('Дата','`date`')?> <?=help('с помощью даты можно изменить<br>порядок вывода объектов в клиентской части сайта')?></th><? }?>
        <th nowrap><?=SortColumn('Статус','status')?></th>
        <th style="padding:0 30px;"></th>
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
          <tr id="item-<?=$id?>" oid="<?=$id?>" par="<?=$row['id_catalog']?>">
            <th><input type="checkbox" name="del[<?=$id?>]"></th>
            <th nowrap><?=$i++?></th>
						<th style="padding:3px 5px;">
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
            <td class="sp"><?
              if($row['url'] and $row['link']){
								?><?=$row['url']?><a href="<?=$row['url']?><?=$row['link']?>.jpg" class="clr-green" target="_blank"><?=$row['link']?></a>.jpg<?
							}
            ?></td><?
            $tree = '';
            if ($row['id_catalog']) {
              $ids_catalog = getArrParents("SELECT id,id_parent FROM {$prx}gallery_catalog WHERE id='%s'", $row['id_catalog']);
              $tree = '';
              foreach ($ids_catalog as $id_catalog) {
                $rb = gtv('gallery_catalog', '*', $id_catalog);
                ob_start();
                ?><a href="<?=$rb['url'].$rb['link']?>/" class="clr-green" target="_blank"><?=$rb['name']?></a><?
                $tree .= ($tree ? ' <i class="fas fa-angle-double-right" style="color:#929292"></i> ' : '') . ob_get_clean();
              }
            }
            ?><td nowrap><?=$tree?></td>
						<? if(!$fl['sort']) { ?><th nowrap align="center"><small><?=date('d.m.Y', strtotime($row['date']))?></small></th><? }?>
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