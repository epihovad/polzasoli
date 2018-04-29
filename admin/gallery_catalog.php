<?
require('inc/common.php');

$h1 = 'Фото-рубрикатор';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>Общий список';
$tbl = 'gallery_catalog';
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

			if(!$name) jAlert('необходимо указать название !');

			$updateLink = false;
			$where = $id ? " AND id<>'{$id}'" : '';

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

			$set = "id_parent='{$id_parent}',
							name='{$name}',
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
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка
		case 'sort':
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");

			$i = 1;
			$errors = 0;
			foreach ($_POST['item'] as $id){
				if(!update($tbl, "`sort`={$i}", $id)){
					$errors++;
					continue;
				}
				$i++;
			}
			if(!$errors){
				echo json_encode(array('status' => 'ok', 'message' => 'success update ' . sizeof($_POST['item']) . ' items'));
			} else {
				echo json_encode(array('status' => 'error', 'message' => 'произошла ошибка'));
			}
			break;
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v) {
				remove_object($id);
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
        <th>Подчинение</th>
        <td><?=dllTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id",'name="id_parent"',$row['id_parent'],array('0'=>'без подчинения'),$id)?></td>
      </tr>
      <tr>
        <th></th>
        <th>Название</th>
        <td><input type="text" class="form-control input-sm" name="name" value="<?=htmlspecialchars($row['name'])?>"></td>
      </tr>
      <tr>
        <th><?=help('при отсутствии значения в данном поле<br>ссылка формируется автоматически')?></th>
        <th>Ссылка</th>
        <td><input type="text" class="form-control input-sm" name="link" value="<?=htmlspecialchars($row['link'])?>"></td>
      </tr>
			<?=show_tr_images($id,'Фото','',1,$tbl,$tbl)?>
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
        <td><input type="text" class="form-control input-sm" name="h1" value="<?=htmlspecialchars($row['h1'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>title</th>
        <td><input type="text" class="form-control input-sm" name="title" value="<?=htmlspecialchars($row['title'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>keywords</th>
        <td><input type="text" class="form-control input-sm" name="keywords" value="<?=htmlspecialchars($row['keywords'])?>"></td>
      </tr>
      <tr>
        <th></th>
        <th>description</th>
        <td><textarea class="form-control input-sm" name="description"><?=$row['description']?></textarea></td>
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
else
{

	ob_start();
	?>

  <link href="css/google-grid.css" rel="stylesheet" media="screen">
	<?/*<link href="/js/GridGallery/css/component.css" rel="stylesheet" media="screen">*/?>
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
require('tpl/template.php');