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

			// проверка даты
      if(!MyCheckDate($date)){
				jAlert('Неверная дата');
      }
			$iday = preg_replace("/\D/",'',date('Ymd',strtotime($date)));

			// проверка возможности брони
      if(!$itime){
				jAlert('Пожалуйста, выберите сеанс');
      }
      $cnt = $cnt_child7 + $cnt_child16 + $cnt_grown + $cnt_pensioner;
			$checkBron = checkBron($iday, $itime, $cnt, $id);
			if($checkBron['status'] == 'busy'){
				jAlert('Для выбранного сеанса превышен лимит по кол-ву мест.<br>Доступно мест: ' . $checkBron['avail'] . '<br>' . 'Запрошено мест: ' . $cnt);
      }

      // проверка имени клиента
      if(!$name){
        jAlert('Пожалуйста, укажите «Имя клиента»');
      }
			// проверка телефона
			$phone = substr(preg_replace("/\D/",'',$phone), -10);
			if(strlen($phone) != 10){
			  jAlert('Некорректный номер телефона');
			}
      if($email && !check_mail($email)){
				jAlert('Некорректный Email клиента');
      }

			// добавляем клиента
      if(!$id_user = getField("SELECT id FROM {$prx}users WHERE phone = '{$phone}'")){
				$set = "phone = '{$phone}',
                name = '{$name}',
                email = '{$email}'";
				if(!$id_user = update('users', $set)) {
					jAlert('Во время сохранения данных произошла ошибка.');
				}
      }

			$set = "iday = '{$iday}',
			        itime = '{$itime}',
			        id_user = '{$id_user}',
			        name = '{$name}',
			        phone = '{$phone}',
			        email = '{$email}',
			        first = '{$first}',
			        cnt_child7 = '{$cnt_child7}',
			        cnt_child16 = '{$cnt_child16}',
			        cnt_grown = '{$cnt_grown}',
			        cnt_pensioner = '{$cnt_pensioner}'
			        ";
			if(!$id = update($tbl,$set,$id)) {
				jAlert('Во время сохранения данных произошла ошибка.');
			}

			?><script>top.location.href = '<?=sgp($HTTP_REFERER, 'id', $id, 1)?>';</script><?
			break;
		// -----------------
    case 'getSeanse':
      $date = $_GET['date'];
			if(!MyCheckDate($date)){
				jAlert('Неверная дата');
			}
			$iday = preg_replace("/\D/",'',date('Ymd',strtotime($date)));

			$q = "SELECT 	s.itime,
                    t.ihour,
                    t.iminute,
                    IF(b.busy IS NULL, 0, b.busy) as busy,
                    6 - IF(b.busy IS NULL, 0, b.busy) AS free,
                    IF(b.busy < 6 OR b.busy IS NULL, 1, 0) as is_avail
            FROM {$prx}schedule s
            JOIN {$prx}time t ON t.pktime = s.itime
            LEFT JOIN (
              SELECT 	iday,
                      itime,
                      SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS busy
              FROM {$prx}bron
              WHERE 1=1
                    AND id <> {$id}
              GROUP BY 	iday,
                        itime
            ) b ON b.iday = s.iday AND b.itime = s.itime
            WHERE 1=1
                  AND s.iday = {$iday}
            ORDER BY s.itime";
			$r = sql($q);
			if(!@mysqli_num_rows($r)){
				?><script>top.$('td.itime').html('');</script><?
			  jAlert('Расписание на указанную дату отсутствует');
      }

      $bron = gtv('bron','*',$id);

      ob_start();
			while ($row = mysqli_fetch_assoc($r)) {
				$class = '';
				if(!$row['free']){
					$class = ' busy';
        } elseif($row['free'] <= 2){
          $class = ' red';
        } elseif ($row['free'] <= 4){
          $class = ' yellow';
        } else {
          $class = ' green';
        }
        $disabled = !$row['is_avail'] ? ' disabled' : '';
        $checked = $bron['iday'] == $iday && $bron['itime'] == $row['itime'] ? ' checked' : '';
			  ?>
        <div class="radio<?=$class?>">
          <label>
            <input type="radio" name="itime" value="<?=$row['itime']?>"<?=$disabled?><?=$checked?>>
            <b><?=$row['ihour'].':'.$row['iminute']?></b><i>доступно мест:</i><span><?=$row['free']?></span>
          </label>
        </div>
        <?
			}
      $data = ob_get_clean();

      ?>
      <script>
      top.$('td.itime').html('<?=cleanJS($data)?>');
      </script>
      <?

      break;
		// ----------------- удаление банера
		case 'del':
			remove_object($id);
      // удаление брони из календаря
      if(isset($_GET['from_calendar'])){
        ?><script>
          top.$('#bron-detail tbody #item-<?=$id?>').remove();
          var i = 1;
          top.$('#bron-detail tbody tr').each(function () {
            $(this).find('th').eq(0).html(i++);
          });
        </script><?
      } else {
				?><script>top.location.href = top.url()</script><?
      }
		break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['del'] as $id=>$v) {
				remove_object($id);
			}
			?><script>top.location.href = top.url()</script><?
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
  <script>
    $(function () {
      var id = $('input[name="bron_id"]').val();
      //
      Inputmask({mask: '+7 (999) 999-99-99', showMaskOnHover: false}).mask($('.table-edit input[name="phone"]'));
      //
      getSeanse();
      //
      $('.table-edit .datepicker').change(function () {
        getSeanse();
      });
      //
      function getSeanse() {
        var date = $('.table-edit .datepicker').val();
        inajax('bron.php', 'action=getSeanse&date=' + date + '&id=' + id);
      }
    })
  </script>

  <style>
    .itime * { vertical-align:middle;}
    .itime input { margin:1px 0 0;}
    .itime b { width:40px; display:inline-block;}
    .itime i { width:90px; display:inline-block; font-style:normal;}
    .itime span { display: inline-block; overflow: hidden; text-align: center; width: 15px; height: 15px; line-height: 15px; border-radius: 20px;}
    .itime .green span { background-color: green; color: #fff; }
    .itime .yellow span { background-color: yellow; color: #000; }
    .itime .red span { background-color: red; color: #fff; }
    .itime .busy { color:#999;}
    .itime .busy span { background-color: #6b6b6b; color: #fff; }
  </style>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <input type="hidden" name="bron_id" value="<?=$id?>">
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Дата сеанса</th>
        <td><?=input('date', 'date', $row['iday'] ? date('d.m.Y',strtotime($row['iday'])) : date('d.m.Y'))?></td>
      </tr>
      <tr>
        <th></th>
        <th>Время сеанса</th>
        <td class="itime"></td>
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
        <th>Email клиента</th>
        <td><?=input('text', 'email', $row['email'], 'placeholder="ввод НЕ обязателен"')?></td>
      </tr>
      <tr>
        <th></th>
        <th>Впервые?</th>
        <td><?=dll(array('0'=>'нет','1'=>'да'),'name="first"',isset($row['first'])?$row['first']:0)?></td>
      </tr>
      <tr>
        <th></th>
        <th>Дети до 7 лет</th>
        <td><?=input('text', 'cnt_child7', (int)$row['cnt_child7'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Дети до 16 лет</th>
        <td><?=input('text', 'cnt_child16', (int)$row['cnt_child16'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Взрослые</th>
        <td><?=input('text', 'cnt_grown', (int)$row['cnt_grown'])?></td>
      </tr>
      <tr>
        <th></th>
        <th>Пенсионеры</th>
        <td><?=input('text', 'cnt_pensioner', (int)$row['cnt_pensioner'])?></td>
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
	$fl['user'] = $_GET['fl']['user'];
	$fl['search'] = stripslashes($_GET['fl']['search']);
	$fl['sort'] = $_GET['fl']['sort'];

	$filters['day1'] = "выбор объектов по Дате сеанса (С даты)";
	$filters['day2'] = "выбор объектов по Дате сеанса (ПО дату)";
	$filters['user'] = "выбор объектов по Клиенту";

	// проверка дат
	for($i=1; $i<=2; $i++){
		if(!MyCheckDate($fl['day'.$i])){
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
	if($fl['user']){
		$where .= "\r\nAND b.id_user = '{$fl['user']}'";
	}
	if($fl['search'] != ''){
	  $sf = array('number','name','phone');
		$w = '';
		foreach ($sf as $field){
		  if($field == 'number'){
		    $field = "CONCAT(B.iday,'/',B.itime,'-',B.id)";
      } else {
		    $field = "`{$field}`";
      }
			$w .= ($w ? ' OR' : '') . "\r\n{$field} LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT 	b.*,
                    CONCAT(b.iday,'/',b.itime,'-',b.id) AS number,
                    t.*,
                    SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS cnt
            FROM {$prx}bron b
            JOIN {$prx}time t ON b.itime = t.pktime
            ";

	$query .= "\r\nWHERE 1{$where}";
	$query .= "\r\nGROUP BY b.id";

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
		$query .= "\r\nORDER BY b.iday, b.itime";
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
      <div class="item">
        <label>Клиент</label>
				<?=dll("SELECT id, CONCAT(phone,' (',name,')') as name FROM {$prx}users ORDER BY name",'name="fl[user]" multiple data-placeholder="-- неважно --"',$fl['user']?explode(',',$fl['user']):null,null,'chosen')?>
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
        <th nowrap width="1%"><?=SortColumn('Дата сеанса','b.iday')?></th>
        <th nowrap width="1%"><?=SortColumn('Время сеанса','b.itime')?></th>
        <th nowrap width="50%"><?=SortColumn('Имя клиента','b.name')?></th>
        <th nowrap width="50%"><?=SortColumn('Телефон клиента','b.phone')?></th>
        <th nowrap><?=SortColumn('Впервые','b.first')?></th>
        <th nowrap><?=SortColumn('Забронировано всего','cnt')?></th>
        <th nowrap><?=SortColumn('Дети до 7 лет','b.cnt_child7')?></th>
        <th nowrap><?=SortColumn('Дети до 16 лет','b.cnt_child16')?></th>
        <th nowrap><?=SortColumn('Взрослые','b.cnt_grown')?></th>
        <th nowrap><?=SortColumn('Пенсионеры','b.cnt_pensioner')?></th>
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
          <th class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['number']?></a></th>
          <th><?=date('d.m.Y',strtotime($row['iday']))?></th>
          <th><?=$row['ihour'].':'.$row['iminute']?></th>
          <td class="sp" nowrap>
            <a class="help" href="users.php?red=<?=$row['id_user']?>" title="перейти в редактирование карточки клиента" target="_blank"><?=$row['name']?>
          </td>
          <td class="sp" nowrap>
            <a class="help" href="users.php?red=<?=$row['id_user']?>" title="перейти в редактирование карточки клиента" target="_blank"><?=$row['phone']?></a>
          </td>
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