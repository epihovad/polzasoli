<?
require('inc/common.php');

$h1 = 'Бронирование';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'bron';

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

			if(!$name) jAlert('Укажите название');

			$set = "name = '{$name}',
			        text = ".($text ? "'{$text}'" : 'NULL').",
			        price = '{$price}',
			        old_price = '{$old_price}',
			        validity = ".($validity ? "'{$validity}'" : 'NULL').",
			        age = '{$age}',
			        /*seance = ".($seance ? "'{$seance}'" : 'NULL').",*/
			        ids_type = ".(sizeof($_POST['ids_type']) > 0 ? "'".implode(',', $_POST['ids_type'])."'" : 'NULL').",
			        ids_who = ".(sizeof($_POST['ids_who']) > 0 ? "'".implode(',', $_POST['ids_who'])."'" : 'NULL').",
			        ids_disease = ".(sizeof($_POST['ids_disease']) > 0 ? "'".implode(',', $_POST['ids_disease'])."'" : 'NULL').",
			        status = '{$status}',
							h1 = " . ($h1 ? "'{$h1}'" : "NULL") . ",
							title = " . ($title ? "'{$title}'" : "NULL") . ",
							keywords = " . ($keywords ? "'{$keywords}'" : "NULL") . ",
							description = " . ($description ? "'{$description}'" : "NULL");

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

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
		// ----------------- удаление банера
		case 'del':
			remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v)
				remove_object($id);
			?><script>top.location.href = '<?=$script?>'</script><?
		break;
	}
	exit;
}
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
  $id = (int)$_GET['red'];
	$q = "SELECT b.*, t.*
        FROM {$prx}{$tbl} b
        JOIN {$prx}time t on b.itime = t.pktime
        WHERE b.id = {$id}";
	$row = getRow($q);
	$id = $row['id'];

	$hname = (date('d.m.Y', strtotime($row['iday'])).' - '.$row['ihour'].':'.$row['iminute'].' ('.$row['name'].')');
	$title .= ' :: ' . ($id ? $hname . ' (редактирование)' : 'Добавление');
	$h = $id ? $hname . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $hname : 'Добавление');
	
	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Дата сеанса</th>
        <td><?=input('date', 'iday', $row['iday'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Время сеанса, ч.</th>
        <td>
          <select class="form-control input-xs" name="hour[<?=$id?>]"><?
          for($v=0; $v<=23; $v++){
            $hour = ($v < 10 ? '0' : '') . $v;
            $selected = $v == $row['ihour'] ? ' selected' : '';
            ?><option value="<?=$v?>"<?=$selected?>><?=$hour?></option><?
          }
          ?></select>
        </td>
      </tr>
      <tr>
        <th></th>
        <th>Время сеанса, мин.</th>
        <td>
          <select class="form-control input-xs" name="min[<?=$id?>]"><?
          for($v=0; $v<=59; $v++){
            $m = ($v < 10 ? '0' : '') . $v;
            $selected = $m == $row['iminute'] ? ' selected' : '';
            ?><option value="<?=$v?>"<?=$selected?>><?=$m?></option><?
          }
          ?></select>
        </td>
      </tr>
      <tr>
        <th></th>
        <th>Имя клиента</th>
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Телефон клиента</th>
        <td><?=input('text', 'phone', $row['phone'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Дети до 7 лет</th>
        <td><?=input('text', 'cnt_child7', $row['cnt_child7'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Дети до 16 лет</th>
        <td><?=input('text', 'cnt_child16', $row['cnt_child16'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Взрослые</th>
        <td><?=input('text', 'cnt_grown', $row['cnt_grown'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Пенсионеры</th>
        <td><?=input('text', 'cnt_pensioner', $row['cnt_pensioner'])?></td>
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
	$cur_page = (int)$_GET['page'] ?: 1;
	$fl['day1'] = $_GET['fl']['day1'];
	$fl['day2'] = $_GET['fl']['day2'];
	$fl['number'] = $_GET['fl']['number'];
	$fl['search'] = stripslashes($_GET['fl']['search']);
	$fl['sort'] = $_GET['fl']['sort'];

	$filters['day1'] = "выбор объектов по Дате сеанса (С даты)";
	$filters['day2'] = "выбор объектов по Дате сеанса (ПО дату)";

	// проверка дат
	for($i=1; $i<=2; $i++){
		$d = substr($fl['day'.$i],0,2);
		$m = substr($fl['day'.$i],3,2);
		$y = substr($fl['day'.$i],6,4);
		if(checkdate($m, $d, $y) === false){
			$fl['day'.$i] = null;
		}
  }

	$where = '';
	if($fl['day1']){
	  $where .= "\r\nAND b.iday >= " . date('Ymd', strtotime($fl['day1']));
	}
	if($fl['day2']){
		$where .= "\r\nAND b.iday <= " . date('Ymd', strtotime($fl['day2']));
	}
	if($fl['number']){
		$where .= "\r\nAND CONCAT(b.iday <= " . date('Ymd', strtotime($fl['day2']));
	}
	if($fl['search'] != ''){
	  $sf = array('name','phone');
		$w = '';
		foreach ($sf as $field){
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT 	B.*,
                    CONCAT(B.iday,'/',B.itime,'-',B.id) AS number,
                    T.*,
                    SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS cnt
            FROM ps_bron B
            JOIN ps_time T ON B.itime = T.pktime
            ";

	$query .= "\r\nWHERE 1{$where}";
	$query .= "\r\nGROUP BY B.id";

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
		$query .= "\r\nORDER BY B.iday, B.itime";
	}

	$query .= "\r\nLIMIT " . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;

	ob_start();
  //pre($query);

	show_listview_btns(($fl['sitemap'] ? 'Сохранить::' : '') . 'Добавить::Удалить');
	ActiveFilters();
  ?>

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
        <label>Дата сеанса</label>
        <div>
          с <?=input('date', 'fl[day1]', $fl['day1'])?>
          по <?=input('date', 'fl[day2]', $fl['day2'])?>
        </div>
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
        <th width="1%"><input type="checkbox" name="del" /></th>
        <th width="1%"><?=SortColumn('Номер','number')?></th>
        <th nowrap width="1%"><?=SortColumn('Дата сеанса','B.iday')?></th>
        <th nowrap width="1%"><?=SortColumn('Время сеанса','B.itime')?></th>
        <th nowrap width="50%"><?=SortColumn('Имя клиента','B.name')?></th>
        <th nowrap width="50%"><?=SortColumn('Телефон клиента','B.phone')?></th>
        <th nowrap><?=SortColumn('Впервые','B.first')?></th>
        <th nowrap><?=SortColumn('Забронировано всего','cnt')?></th>
        <th nowrap><?=SortColumn('Дети до 7 лет','B.cnt_child7')?></th>
        <th nowrap><?=SortColumn('Дети до 16 лет','B.cnt_child16')?></th>
        <th nowrap><?=SortColumn('Взрослые','B.cnt_grown')?></th>
        <th nowrap><?=SortColumn('Пенсионеры','B.cnt_pensioner')?></th>
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
          <th nowrap><?=$row['number']?></th>
          <th><?=date('d.m.Y',strtotime($row['iday']))?></th>
          <th><?=$row['ihour'].':'.$row['iminute']?></th>
          <td class="sp" nowrap><?=$row['name']?></td>
          <td class="sp" nowrap><?=$row['phone']?></td>
          <td style="text-align:center;<?=$row['first']?' color:green':''?>"><?=$row['first']?'<b>ДА</b>':'нет'?></td>
          <th style="text-align:center"><?=$row['cnt']?></th>
          <th style="text-align:center"><?=$row['cnt_child7']?></th>
          <th style="text-align:center"><?=$row['cnt_child16']?></th>
          <th style="text-align:center"><?=$row['cnt_grown']?></th>
          <th style="text-align:center"><?=$row['cnt_pensioner']?></th>
          <th nowrap><?=btn_edit($id)?></th>
        </tr>
        <?
      }
    } else {
      ?>
      <tr class="nofind">
        <td colspan="20">
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