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

			$phone = substr(preg_replace("/\D/",'',$phone), -10);
			if(strlen($phone) != 10) jAlert('Некорректный номер телефона');

      jAlert($stime);
			//if(!$name) jAlert('Укажите название');

			?><script>top.location.href = '<?=sgp($HTTP_REFERER, 'id', $id, 1)?>';</script><?
			break;
		// -----------------
    case 'getSeanse':
      $date = $_GET['date'];
			$d = substr($date,0,2);
			$m = substr($date,3,2);
			$y = substr($date,6,4);
			if(checkdate($m, $d, $y) === false){
				jAlert('Неверная дата');
			}
			$iday = preg_replace("/\D/",'',date('Ymd',strtotime($date)));

			$q = "SELECT 	s.itime,
                    t.ihour,
                    t.iminute,
                    IF(b.busy IS NULL, 0, b.busy) as busy,
                    6 - IF(b.busy IS NULL, 0, b.busy) AS free
            FROM ps_schedule s
            JOIN ps_time t ON t.pktime = s.itime
            LEFT JOIN (
              SELECT 	iday,
                      itime,
                      SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS busy
              FROM ps_bron
              WHERE 1=1
                    AND id <> {$id}
              GROUP BY 	iday,
                        itime
            ) b ON b.iday = s.iday AND b.itime = s.itime
            WHERE 1=1
                  AND s.iday = {$iday}
                  AND (b.busy < 6 OR b.busy IS NULL)
            ORDER BY s.itime";
			$r = sql($q);
			if(!@mysqli_num_rows($r)){
				?><script>top.$('td.stime').html('');</script><?
			  jAlert('Расписание на указанную дату отсутствует');
      }

      ob_start();
			while ($row = mysqli_fetch_assoc($r)) {
				$class = '';
        if($row['free'] <= 2){
          $class = 'red';
        } elseif ($row['free'] <= 4){
          $class = 'yellow';
        } else {
          $class = 'green';
        }
			  ?>
        <div class="radio">
          <label>
            <input type="radio" name="stime" value="<?=$row['itime']?>">
						<b><?=$row['ihour'].':'.$row['iminute']?></b> доступно мест: <span class="<?=$class?>"><?=$row['free']?></span>
          </label>
        </div>
        <?
			}
      $data = ob_get_clean();

      ?>
      <script>
      top.$('td.stime').html('<?=cleanJS($data)?>');
      </script>
      <?

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
  <script src="/js/inputmask.min.js"></script>
  <script src="/js/inputmask.phone.extensions.min.js"></script>
  <script>
    $(function () {
      //
      Inputmask({mask: '+7 (999) 999-99-99', showMaskOnHover: false}).mask($('.table-edit input[name="phone"]'));
      //
      $('.table-edit .datepicker').change(function () {
        var date = $(this).val();
        var id = $('input[name="bron_id"]').val();
        inajax('bron.php', 'action=getSeanse&date=' + date + '&id=' + id);
      });
    })
  </script>

  <style>
    .stime * { vertical-align:middle;}
    .stime input { margin:1px 0 0;}
    .stime span { display: inline-block; overflow: hidden; text-align: center; width: 15px; height: 15px; line-height: 15px; border-radius: 20px;}
    .stime span.green { background-color: green; color: #fff; }
    .stime span.yellow { background-color: yellow; color: #000; }
    .stime span.red { background-color: red; color: #fff; }
  </style>

  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <input type="hidden" name="bron_id" value="<?=$id?>">
    <input type="hidden" name="HTTP_REFERER" value="<?=$_SERVER['HTTP_REFERER']?>">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Дата сеанса</th>
        <td><?=input('date', 'iday', $row['iday'] ? date('d.m.Y',strtotime($row['iday'])) : date('d.m.Y'))?></td>
      </tr>
      <tr>
        <th></th>
        <th>Время сеанса</th>
        <td class="stime"></td>
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
          <th class="sp" nowrap><a href="?red=<?=$id?>"><?=$row['number']?></a></th>
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