<?
require('inc/common.php');

$h1 = 'Клиенты';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'users';

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

			//
			$phone = substr(preg_replace("/\D/",'',$phone), -10);
			if(strlen($phone) != 10){
			  jAlert('Некорректный номер телефона');
			}
			//
			if($email && !check_mail($email)){
				jAlert('Некорректный Email');
      }
      //
      if(getField("SELECT id FROM {$prx}{$tbl} WHERE phone = '{$phone}' AND id <> '{$id}'")){
				jAlert('Клиент с данным номером телефона уже зарегистрирован в системе');
      }

      $set = "phone = '{$phone}',
              name = '{$name}',
              email = '{$email}',
              notes=".($notes?"'{$notes}'":"NULL").",
              status = '{$status}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=sgp($HTTP_REFERER, 'id', $id, 1)?>';</script><?
			break;
		// ----------------- удаление банера
		case 'del':
			remove_object($id, array('bron'=>'id_user'));
			?><script>top.location.href = top.url()</script><?
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v) {
				remove_object($id, array('bron' => 'id_user'));
			}
			?><script>top.location.href = top.url()</script><?
		break;
	}
	exit;
}
// ------------------РЕДАКТИРОВАНИЕ--------------------
if(isset($_GET['red']))
{
	$row = gtv($tbl,'*',(int)$_GET['red']);
	$id = $row['id'];

	$hname = $row['phone'].' ('.$row['name'].')';
	$title .= ' :: ' . ($id ? $hname . ' (редактирование)' : 'Добавление');
	$h = $id ? $hname . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $hname : 'Добавление');
	
	ob_start();
	?>
  <script>
    $(function () {
      //
      Inputmask({mask: '+7 (999) 999-99-99', showMaskOnHover: false}).mask($('.table-edit input[name="phone"]'));
    })
  </script>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>ID</th>
        <td><b><?=$row['id']?></b></td>
      </tr>
      <tr>
        <th></th>
        <th>Дата регистрации</th>
        <td><?=$row['date'] ? date('d.m.Y H:i:s',strtotime($row['date'])) : ''?></td>
      </tr>
      <tr>
        <th></th>
        <th>Телефон</th>
        <td><?=input('text', 'phone', $row['phone'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Имя</th>
        <td><?=input('text', 'name', $row['name'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Email</th>
        <td><?=input('text', 'email', $row['email'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Примечание</th>
        <td><?=input('textarea', 'notes', $row['notes'])?></td>
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
else
{
	$cur_page = (int)$_GET['page'] ?: 1;
	$fl['search'] = stripslashes($_GET['fl']['search']);
	$fl['sort'] = $_GET['fl']['sort'];

	$where = '';
	if($fl['search'] != ''){
	  $sf = array('phone','name','email','notes');
		$w = '';
		foreach ($sf as $field){
		  $search = $fl['search'];
		  if($field == 'phone'){
				$search = preg_replace("/\D/",'',$search);
      }
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$search}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT  u.*,
                    b.cnt_day,
                    b.cnt_seans,
                    b.cnt_guest
            FROM ps_users u
            -- бронь
            LEFT JOIN (
              SELECT  id_user,
                      COUNT(DISTINCT iday) AS cnt_day,
                      COUNT(DISTINCT id) AS cnt_seans,
                      SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS cnt_guest
              FROM ps_bron
              GROUP BY id_user
            ) b ON b.id_user = u.id
            ";

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
		$query .= "\r\nORDER BY u.id DESC";
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
        <th><input type="checkbox" name="del" /></th>
        <th><?=SortColumn('ID','u.id')?></th>
        <th nowrap><?=SortColumn('Дата регистрации','u.date')?></th>
        <th nowrap width="33%"><?=SortColumn('Телефон','u.phone')?></th>
        <th nowrap width="33%"><?=SortColumn('Имя','u.name')?></th>
        <th nowrap width="33%"><?=SortColumn('Email','u.email')?></th>
        <th nowrap><?=SortColumn('Кол-во дней','b.cnt_day')?></th>
        <th nowrap><?=SortColumn('Кол-во сеансов','b.cnt_seans')?></th>
        <th nowrap><?=SortColumn('Кол-во гостей','b.cnt_guest')?></th>
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
        <tr id="item-<?=$row['id']?>">
          <th><input type="checkbox" name="del[<?=$id?>]"></th>
          <th><?=$id?></th>
          <th nowrap><?=date('d.m.Y H:i:s',strtotime($row['date']))?></a></th>
          <th class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['phone']?></a></th>
          <th class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['name']?></a></th>
          <th class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['email']?></a></th>
          <th style="text-align:center"><?=$row['cnt_day']?:'-'?></th>
          <th style="text-align:center"><?=$row['cnt_seans']?:'-'?></th>
          <th style="text-align:center"><?=$row['cnt_guest']?:'-'?></th>
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