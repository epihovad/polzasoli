<?
require('inc/common.php');

$h1 = 'Расписание сеансов';
$h = 'Расписание сеансов на день';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'schedule';

function SeanseRow($row = array(), $i = 1){
	?>
  <tr id="item-0">
    <th><input type="checkbox" name="del[0]"></th>
    <th nowrap><?=$i?></th>
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
    <td><input class="form-control input-xs" name="discount[]" value="<?=$row['discount']?>"></td>
    <td>
      <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить" onclick="DelRowTime($(this))">
        <i class="far fa-trash-alt"></i>
      </button>
    </td>
  </tr>
	<?
}

if(isset($_GET['action'])){
  switch ($_GET['action']){
    //
    case 'save':
      $day = (int)$_POST['day'];
      // что-то надо сделать с бронью

      // мочим расписание
      sql("DELETE FROM {$prx}{$tbl} WHERE iday = {$day}");
      if(!sizeof($_POST['hour'])){
				jAlert('Расписание на ' . date('d.m.Y', strtotime($day)) . ' успешно удалено.');
      }
      // добавляем новое
      $values = '';
      foreach ($_POST['hour'] as $i => $h){
        $h = (int)$h;
        $m = (int)$_POST['min'][$i];
        $discount = (int)($_POST['discount'][$i]);
        $values .= ($values ? ', ' : '') . "('".$day."','".$h.$m."','".$discount."')";
      }
      $query = "INSERT INTO {$prx}{$tbl} (iday, itime, discount) VALUES " . $values;
      if(!$res = sql($query)){
        jAlert('ошибка при добавлении данных');
      }
      if(!$inserted = mysqli_affected_rows($mysqli)){
				jAlert('ошибка при добавлении данных');
      } else {
				jAlert('Данные успешно сохранены.<br>Добавлено сеансов: ' . $inserted);
      }
      break;
    // добавление одного сеанса
    case 'row_time':
			SeanseRow();
			break;
		// добавление сенасов на весь день по шаблону
		case 'row_time_standart':
      $day = (int)$_GET['day'];
			if(!MyCheckDate($day, 'Ymd')){
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
			$i=1;
      while ($row = mysqli_fetch_assoc($r)){
				SeanseRow($row, $i++);
      }
			break;
  }
  exit;
}

$dy = date('Y');
$dm = date('m');
$dd = date('d');
$day = (int)$_GET['day'];

if(MyCheckDate($day, 'Ymd')){
	$date = new DateTime($day);
	$dy = $date->format('Y');
	$dm = $date->format('m');
	$dd = $date->format('d');
} else {
	$day = date('Ymd');
}

$ref_day = getRow("SELECT * FROM {$prx}day WHERE pkday = '{$day}'");

ob_start();

?>
<style type="text/css">
  #chDay label { position:relative; display:inline-block; margin:0 5px 0 0; vertical-align:middle;}
  #chDay input { text-align:center; width:80px; display:inline-block; vertical-align:middle;}
  #chDay { padding:10px;}
</style>

<script>
  $(function () {
    $('#chDay input').change(function () {
      var v = $(this).val();
      var d = moment(v, 'DD.MM.YYYY');
      if(!d._isValid){
        $(document).jAlert('show','alert','Указана некорректная дата');
        return false;
      }
      location.href = 'schedule.php?day=' + d.format('YYYYMMDD');
    });
    //
    $('#time_add').click(function () {
      AddRowTime();
    });
    //
    $('#time_del').click(function () {
      DelSelectedRowTime();
    });
    //
    $('#time_standart').click(function () {
      $(document).jAlert('show', 'confirm', 'Шаблон перезатрёт все текущие данные. Уверены?', function () {
        StandartRowTime();
      });
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
      data: 'action=row_time',
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
  function DelSelectedRowTime() {
    var $ch = $('.table-list tbody input[name^=del]:checked');
    if(!$ch.length){
      $(document).jAlert('show','alert','Для удаления выберите хотя бы один сеанс');
      return false;
    }
    $ch.each(function () {
      $(this).parents('tr:first').remove();
    });
    $('.table-list thead input[name=del]').prop('checked',false);
  }
  //
  function UpdateRowTimeNum(){
    var i = 1;
    $('.table-list tbody tr').each(function () {
      $(this).find('th').eq(1).html(i++);
    });
  }
</script>

<div id="chDay" class="panel-white">
  <label>Выберите дату</label>
  <input class="form-control input-xs datepicker" value="<?=date('d.m.Y', strtotime($day))?>">
  - <b><?=$ref_day['iday'] . ' ' . $ref_day['cmonth_ru'] . ', ' . $ref_day['сday_of_week_ru']?></b>
  <div class="clearfix"></div>
</div>


<button id="time_standart" type="button" class="btn btn-default btn-xs"><i class="fa fa-plus"></i> <span>Шаблон</span></button>
<button id="time_add" type="button" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> <span>Добавить</span></button>
<button id="time_del" type="button" class="btn btn-danger btn-xs"><i class="far fa-trash-alt"></i> <span>Удалить</span></button>

<form id="ftl" action="schedule.php?action=save" method="post" target="ajax" style="margin-top:20px">
  <input type="hidden" name="day" value="<?=$day?>">
  <table class="table-list" style="width:auto">
    <thead>
    <tr>
      <th><input type="checkbox" name="del" /></th>
      <th>№</th>
      <th>Время сеанса, ч.</th>
      <th>Время сеанса, мин.</th>
      <th>Скидка, %</th>
      <th></th>
    </tr>
    </thead>
    <tbody>
    <?
    $q = "SELECT S.*, T.*
          FROM {$prx}{$tbl} S
          JOIN {$prx}time T ON T.PKTIME = S.ITIME
          WHERE S.iday = {$day}
          ORDER BY S.itime";
    $r = sql($q);
    $i = 1;
    while ($row = @mysqli_fetch_assoc($r)){
      SeanseRow($row, $i++);
    }
    ?>
    </tbody>
  </table>
  <button type="submit" class="btn btn-success" style="margin-top:20px;">Сохранить</button>
</form>
<?

$content = arr($h, ob_get_clean());
require('tpl/template.php');