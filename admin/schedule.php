<?
require('inc/common.php');
require($_SERVER['DOCUMENT_ROOT'] . '/inc/calendar.php');

$h1 = 'Расписание сеансов';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'schedule';

if(isset($_GET['action'])){
  switch ($_GET['action']){
    // добавление сеанса
    case 'row_time':
      if(isset($_GET['new'])){
        ?>
        <tr id="item-0">
          <th><input type="checkbox" name="del[0]"></th>
          <th nowrap></th>
          <td>
            <select class="form-control input-xs" name="hour[]"><?
							for($v=0; $v<=23; $v++){
								$h = ($v < 10 ? '0' : '') . $v;
								?><option value="<?=$v?>"><?=$h?></option><?
							}
							?></select>
          </td>
          <td>
            <select class="form-control input-xs" name="min[]"><?
							for($v=0; $v<=59; $v++){
								$m = ($v < 10 ? '0' : '') . $v;
								?><option value="<?=$v?>"><?=$m?></option><?
							}
							?></select>
          </td>
          <td><input class="form-control input-xs" name="discount" value=""></td>
          <td></td>
          <td>
            <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить" onclick="DelRowTime($(this))">
              <i class="far fa-trash-alt"></i>
            </button>
          </td>
        </tr>
        <?
      } else {

      }
			exit;
		// добавление сенасов на весь день по шаблону
		case 'row_time_standart':
      $day = (int)$_GET['day'];
			$y = substr($day,0,4);
			$m = substr($day,4,2);
			$d = substr($day,6,2);
			if(checkdate($m, $d, $y) === false){
			  exit;
      }

			$seance = array();

			$date = new DateTime($day);
			$w = $date->format('w');
			// воскресение
			if(!$w){
			  jAlert('для воскресения шаблона нет');
      }
      // суббота
      elseif ($w == 6){
			  $seance = array('920','1020','1120','1220','1320','1420');
      }
      // будни
			else {
				$seance = array('920','1020','1120','1220','1620','1720','1820','1910');
      }

      $r = sql("SELECT * FROM {$prx}time WHERE pktime IN (" . implode(',',$seance) . ")");
			while ($row = mysqli_fetch_assoc($r)){
				?>
        <tr id="item-0">
          <th><input type="checkbox" name="del[0]"></th>
          <th nowrap></th>
          <td>
            <select class="form-control input-xs" name="hour[]"><?
							for($v=0; $v<=23; $v++){
								$h = ($v < 10 ? '0' : '') . $v;
								$selected = $v == $row['ihour'] ? ' selected' : '';
								?><option value="<?=$v?>"<?=$selected?>><?=$h?></option><?
							}
							?></select>
          </td>
          <td>
            <select class="form-control input-xs" name="min[]"><?
							for($v=0; $v<=59; $v++){
								$m = ($v < 10 ? '0' : '') . $v;
								$selected = $m == $row['iminute'] ? ' selected' : '';
								?><option value="<?=$v?>"<?=$selected?>><?=$m?></option><?
							}
							?></select>
          </td>
          <td><input class="form-control input-xs" name="discount" value=""></td>
          <td></td>
          <td>
            <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить" onclick="DelRowTime($(this))">
              <i class="far fa-trash-alt"></i>
            </button>
          </td>
        </tr>
				<?
      }
		  exit;

  }
  exit;
}

$dy = date('Y');
$dm = date('m');
$dd = date('d');
$day = (int)$_GET['day'];

$y = substr($day,0,4);
$m = substr($day,4,2);
$d = substr($day,6,2);

if(checkdate($m, $d, $y) !== false){
	$date = new DateTime($day);
	$dy = $date->format('Y');
	$dm = $date->format('m');
	$dd = $date->format('d');
} else {
	$day = date('Ymd');
}

ob_start();

?>
<style type="text/css">
  .b-calendar { font: 14px/1.2 Arial, sans-serif; background: #f2f2f2;}
  .b-calendar hr { height: 1px; overflow: hidden; font-size: 0; line-height: 0; background: #ccc; margin: 50px 0; border: 0; }
  .b-calendar--along { width: 250px; padding: 15px; margin: 0 auto; }
  .b-calendar--many { padding: 15px; width: 250px; display: inline-block; vertical-align: top; margin:15px 15px 0 0; }
  .b-calendar__title { text-align: center; margin: 0 0 20px;}
  .b-calendar__year { font-weight: bold; color: #333; }
  .b-calendar__tb { width: 100%;}
  .b-calendar__head { font: bold 14px/1.2 Arial, sans-serif; padding: 5px; text-align: center; border-bottom: 1px solid #c0c0c0;}
  .b-calendar__np { padding: 5px; }
  .b-calendar__day { font: 14px/1.2 Arial, sans-serif; padding: 8px 5px; text-align: center;}
  .b-calendar__weekend a { color: red;}
  .b-calendar a { }
  #chDay .pull-left { margin-right:5px; position:relative; vertical-align:top;}
  #chDay button { margin-top: 20px; font-size: 12px; height: 24px;}
  #chDay { padding-bottom:20px;}
</style>

<script>
  $(function () {
    $('#chDay button').click(function () {
      var y = $('#chDay select').eq(2).val();
      var m = $('#chDay select').eq(1).val();
      var d = $('#chDay select').eq(0).val();
      $('#chDay input[name="day"]').val(y+m+d);
      $('#chDay form').submit();
    });
    //
    $('#time_add').click(function () {
      AddRowTime();
    });
    //
    $('#time_standart').click(function () {
      //$(document).jAlert('show', 'confirm', 'Уверены?', function () {
        StandartRowTime();
      //});
    });
  });
  //
  function StandartRowTime(){
    $.ajax({
      type: 'GET',
      url: 'schedule.php',
      data: 'action=row_time_standart&day=<?=$day?>',
      success: function(data){
        $('.table-list tbody tr').remove();
        $('.table-list tbody').append(data);
        UpdateRowTimeNum();
      }
    });
  }
  //
  function AddRowTime(url){
    $.ajax({
      type: 'GET',
      url: 'schedule.php',
      data: 'action=row_time&new',
      success: function(data){
        $('.table-list tbody').append(data);
        UpdateRowTimeNum();
      }
    });
  }
  //
  function DelRowTime($obj) {
    if($obj == undefined){
      $('.table-list tbody tr').remove();
    } else {
      $obj.parents('tr:first').remove();
    }
    UpdateRowTimeNum();
  }
  //
  function UpdateRowTimeNum(){
    var i = 1;
    $('.table-list tbody tr').each(function () {
      $(this).find('th').eq(1).html(i++);
    });
  }
</script>

<div id="chDay">
  <form action="schedule.php" method="get" target="_self">
  <input type="hidden" name="day" value="<?=$day?>">
    <div class="pull-left">
      <label>День</label>
      <select class="form-control input-xs">
        <?
        for($d=1; $d<=31; $d++){
          $selected = $d == $dd ? ' selected' : '';
          $d = ($d < 10 ? '0' : '') . $d;
          ?><option value="<?=$d?>"<?=$selected?>><?=$d?></option><?
        }
        ?>
      </select>
    </div>
    <div class="pull-left">
      <label>Месяц</label>
      <select class="form-control input-xs">
        <?
        $r = sql("SELECT DISTINCT imonth, cmonth_big_ru FROM {$prx}day ORDER BY imonth");
        while ($arr = mysqli_fetch_assoc($r)){
          $m = $arr['imonth'];
          $selected = $m == $dm ? ' selected' : '';
					$m = ($m < 10 ? '0' : '') . $m;
          ?><option value="<?=$m?>"<?=$selected?>><?=$arr['cmonth_big_ru']?></option><?
        }
        ?>
      </select>
    </div>
    <div class="pull-left">
      <label>Год</label>
      <select class="form-control input-xs">
        <?
        $arr = getArr("SELECT DISTINCT iyear FROM {$prx}day ORDER BY iyear");
        foreach ($arr as $y){
          $selected = $y == $dy ? ' selected' : '';
          ?><option value="<?=$y?>"<?=$selected?>><?=$y?></option><?
        }
        ?>
      </select>
    </div>
    <div class="pull-left">
      <button class="btn btn-info btn-xs">перейти</button>
    </div>
  </form>
  <div class="clearfix"></div>
</div>
<?

if(isset($_GET['show'])){

  switch ($_GET['show']){
		// ------------------- КАЛЕНДАРЬ НА МЕСЯЦ
    case 'month':

      $dm = date('m');
      $dy = date('Y');
      $ym = (int)$_GET['ym'];

      try {
				$date = new DateTime($ym . '01');
				$dm = $date->format('m');
				$dy = $date->format('Y');
      } catch (Exception $e) {}

			?>
      <div class="b-calendar b-calendar--along">
        <div class="b-calendar__title"><span class="b-calendar__month"><?=getRusDate('MU', '01.'.$dm.'.'.$dy)?></span> <span class="b-calendar__year">'<?=$dy?></span></div>
				<?=draw_calendar((int)$dm, $dy)?>
      </div>
      <?

      break;
		// ------------------- КАЛЕНДАРЬ НА ГОД
		case 'year':

			$dy = date('Y');
			$y = (int)$_GET['y'];

			try {
				$date = new DateTime($y . '0101');
				$dy = $date->format('Y');
			} catch (Exception $e) {}

      for ($m = 1; $m <= 12; $m++) {
			  $dm = $m < 10 ? '0'.$m : $m;
			  ?>
        <div class="b-calendar b-calendar--many">
          <div class="b-calendar__title"><span class="b-calendar__month"><?=getRusDate('MU', '01.'.$dm.'.'.$dy)?></span> <span class="b-calendar__year">'<?=$dy?></span></div>
          <?=draw_calendar($m, $dy)?>
        </div>
        <?
			}

			break;
  }

} else {

	// ------------------- КАЛЕНДАРЬ НА ДЕНЬ
	?>
  <button id="time_standart" type="button" class="btn btn-default btn-xs" onclick=""><i class="fa fa-plus"></i> <span>Шаблон</span></button>
  <button id="time_add" type="button" class="btn btn-success btn-xs" onclick=""><i class="fa fa-plus"></i> <span>Добавить</span></button>
  <button id="time_del" type="button" class="btn btn-danger btn-xs" onclick=""><i class="far fa-trash-alt"></i> <span>Удалить</span></button>

  <form id="ftl" method="post" target="ajax" style="margin-top:20px">
    <input type="hidden" name="day" value="<?=$day?>">
    <table class="table-list" tbl="<?=$tbl?>" style="width:auto">
      <thead>
      <tr>
        <th><input type="checkbox" name="del" /></th>
        <th>№</th>
        <th>Время сеанса, ч.</th>
        <th>Время сеанса, мин.</th>
        <th>Скидка, %</th>
        <th>Информация</th>
        <th></th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </form>
  <?
}

$content = arr($h, ob_get_clean());


/*
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

			if(!$question) jAlert('необходимо ввести «Вопрос»');
			if(!$answer) jAlert('необходимо ввести «Ответ»');

			$set = "question='{$question}',
							answer='{$answer}',
							status='{$status}'";

			if(!$id = update($tbl,$set,$id))
				jAlert('Во время сохранения данных произошла ошибка.');

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
			foreach($_POST['del'] as $id=>$v) {
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

	$title .= ' :: ' . ($id ? $row['question'] . ' (редактирование)' : 'Добавление');
	$h = $id ? $row['question'] . ' <small>(редактирование)</small>' : 'Добавление';
	$navigate = '<span></span><a href="' . $script . '">' . $h1 . '</a><span></span>' . ($id ? $row['question'] : 'Добавление');

	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
    <table class="table-edit">
      <tr>
        <th></th>
        <th>Вопрос</th>
        <td><?=input('text', 'question', $row['question'])?></td>
      </tr>
        <th></th>
        <th>Ответ</th>
        <td><?=showCK('answer',$row['answer'], 'basic')?></td>
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
	$fl['sort'] = $_GET['fl']['sort'];

	$query = "SELECT * FROM {$prx}{$tbl}";

	// проверяем текущую сортировку и формируем соответствующий запрос
	if($fl['sort']){
		foreach ($fl['sort'] as $f => $t){
			$query .= "\r\nORDER BY {$f} {$t}";
			break;
		}
	} else {
		$query .= "\r\nORDER BY sort,id";
	}

	ob_start();

	show_listview_btns('Добавить::Удалить');
	ActiveFilters();
  ?>

  <div class="clearfix"></div>

  <form id="ftl" method="post" target="ajax">
    <table class="table-list" tbl="<?=$tbl?>">
      <thead>
      <tr>
        <th><input type="checkbox" name="check_del" id="check_del" /></th>
        <th>№</th>
	      <? if(!$fl['sort']){ ?><th nowrap><?=help('параметр с помощью которого можно изменить<br>порядок вывода объектов в клиентской части сайта')?></th><? }?>
        <th width="50%"><?=SortColumn('Вопрос','question')?></th>
        <th width="50%"><?=SortColumn('Ответ','answer')?></th>
        <th nowrap><?=SortColumn('Статус','status')?></th>
        <th style="padding:0 30px;"></th>
      </tr>
      </thead>
      <tbody>
			<?
			$res = sql($query);
			if(mysqli_num_rows($res)){
				$i=1;
				while($row = mysqli_fetch_assoc($res))
				{
					$id = $row['id'];
					?>
          <tr id="item-<?=$id?>" oid="<?=$id?>" par="0">
            <th><input type="checkbox" name="del[<?=$id?>]"></th>
            <th nowrap><?=$i++?></th>
						<? if(!$fl['sort']){ ?><th nowrap align="center"><i class="fas fa-sort"></i></th><? }?>
            <td><a href="?red=<?=$id?>"><?=$row['question']?></a></td>
            <td><?=$row['answer']?></td>
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
	<?
	$content = arr($h, ob_get_clean());
}*/
require('tpl/template.php');