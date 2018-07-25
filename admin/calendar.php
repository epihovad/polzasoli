<?
require('inc/common.php');

function draw_calendar($month, $year, $action = 'none') {

  global $bron;

	$calendar = '<table cellpadding="0" cellspacing="0" class="b-calendar__tb">';

	// вывод дней недели
	$headings = array('Пн','Вт','Ср','Чт','Пт','Сб','Вс');
	$calendar.= '<tr class="b-calendar__row">';
	for($head_day = 0; $head_day <= 6; $head_day++) {
		$calendar.= '<th class="b-calendar__head';
		// выделяем выходные дни
		if ($head_day != 0) {
			if (($head_day % 5 == 0) || ($head_day % 6 == 0)) {
				$calendar .= ' b-calendar__weekend';
			}
		}
		$calendar .= '">';
		$calendar.= '<div class="b-calendar__number">'.$headings[$head_day].'</div>';
		$calendar.= '</th>';
	}
	$calendar.= '</tr>';

	// выставляем начало недели на понедельник
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$running_day = $running_day - 1;
	if ($running_day == -1) {
		$running_day = 6;
	}

	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$day_counter = 0;
	$days_in_this_week = 1;
	$dates_array = array();

	// первая строка календаря
	$calendar.= '<tr class="b-calendar__row">';

	// вывод пустых ячеек
	for ($x = 0; $x < $running_day; $x++) {
		$calendar.= '<td class="b-calendar__np"></td>';
		$days_in_this_week++;
	}

	// дошли до чисел, будем их писать в первую строку
	for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
		$calendar.= '<td class="b-calendar__day';

		// выделяем выходные дни
		if ($running_day != 0) {
			if (($running_day % 5 == 0) || ($running_day % 6 == 0)) {
				$calendar .= ' b-calendar__weekend';
			}
		}
		$calendar .= '">';

		// пишем номер в ячейку
		$ymd = $year . ($month < 10 ? '0' : '') . $month . ($list_day < 10 ? '0' : '') . $list_day;
		$cnt_bron = $bron[$ymd];
		$class = '';
		if($cnt_bron){
			if($cnt_bron <= 2){
				$class = 'green';
			} elseif ($cnt_bron <= 4){
			  $class = 'yellow';
      } elseif ($cnt_bron == 5){
			  $class = 'red';
		  } else {
				$class = 'busy';
      }
    }

		$calendar.= '<a href="#" day="' . $ymd . '" class="' . $class . '" title="Забронировано мест: ' . $cnt_bron . '">' . $list_day . '</a>';
		$calendar.= '</td>';

		// дошли до последнего дня недели
		if ($running_day == 6) {
			// закрываем строку
			$calendar.= '</tr>';
			// если день не последний в месяце, начинаем следующую строку
			if (($day_counter + 1) != $days_in_month) {
				$calendar.= '<tr class="b-calendar__row">';
			}
			// сбрасываем счетчики
			$running_day = -1;
			$days_in_this_week = 0;
		}

		$days_in_this_week++;
		$running_day++;
		$day_counter++;
	}

	// выводим пустые ячейки в конце последней недели
	if ($days_in_this_week < 8) {
		for($x = 1; $x <= (8 - $days_in_this_week); $x++) {
			$calendar.= '<td class="b-calendar__np"> </td>';
		}
	}
	$calendar.= '</tr>';
	$calendar.= '</table>';

	return $calendar;
}

$h1 = 'Календарь сеансов';
$h = 'Календарь на год';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'schedule';

if(isset($_GET['action'])){
  switch ($_GET['action']){
    //
    case 'bron_info':
      $day = (int)$_GET['day'];
      $q = "SELECT b.*, t.*
            FROM {$prx}bron b
            JOIN {$prx}time t on b.itime = t.pktime
            WHERE b.iday = {$day}
            ORDER BY b.itime";
      $r = sql($q);
      ?>
      <style>
        #bron-detail { padding:15px;}
        #bron-detail h3 {margin: 0 0 20px 0; font-size: 22px; color:#eb0d41;}
        #bron-detail .cnt-input input { width:95px; text-align:center;}
        #bron-detail .datepicker { text-align:center; width:95px;}
      </style>

      <script>
        $(function () {
          //
          $('#bron-detail .datepicker').datepicker();
          //
          $('#bron-detail tbody tr .btn').click(function () {
            $(this).parents('tr:first').remove();
            UpdateBronNum();
          });
        });
        //
        function UpdateBronNum(){
          var i = 1;
          $('#bron-detail tbody tr').each(function () {
            $(this).find('th').eq(1).html(i++);
          });
        }
      </script>

      <form id="bron-detail" action="calendar.php?action=save" method="post" target="ajax">
        <h3>Информация о бронировании на <?=date('d.m.Y', strtotime($day))?></h3>
        <table class="table-list" style="width:auto">
          <thead>
            <tr>
              <th>№</th>
              <th>Дата сеанса</th>
              <th>Время сеанса, ч.</th>
              <th>Время сеанса, мин.</th>
              <th>Имя клиента</th>
              <th>Телефон клиента</th>
              <th>Впервые</th>
              <th>Дети до 7 лет</th>
              <th>Дети до 16 лет</th>
              <th>Взрослые</th>
              <th>Пенсионеры</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?
          if(@mysqli_num_rows($r)){
            $i=1;
            while ($row = mysqli_fetch_assoc($r)){
              $id = $row['id'];
							?>
              <tr id="item-<?=$id?>">
                <th><?=$i++?></th>
                <td><input class="form-control input-xs datepicker" name="date[<?=$id?>]" value="<?=date('d.m.Y', strtotime($day))?>"></td>
                <td>
                  <select class="form-control input-xs" name="hour[<?=$id?>]"><?
										for($v=0; $v<=23; $v++){
											$h = ($v < 10 ? '0' : '') . $v;
											$selected = $v == $row['ihour'] ? ' selected' : '';
											?><option value="<?=$v?>"<?=$selected?>><?=$h?></option><?
										}
										?></select>
                </td>
                <td>
                  <select class="form-control input-xs" name="min[<?=$id?>]"><?
										for($v=0; $v<=59; $v++){
											$m = ($v < 10 ? '0' : '') . $v;
											$selected = $m == $row['iminute'] ? ' selected' : '';
											?><option value="<?=$v?>"<?=$selected?>><?=$m?></option><?
										}
										?></select>
                </td>
                <td><?=input('text', 'name['.$id.']', $row['name'])?></td>
                <td><?=input('text', 'phone['.$id.']', $row['phone'])?></td>
                <td style="text-align:center;<?=$row['first']?' color:green':''?>"><?=$row['first']?'<b>ДА</b>':'нет'?></td>
                <td class="cnt-input"><?=input('text', 'cnt_child7['.$id.']', $row['cnt_child7'])?></td>
                <td class="cnt-input"><?=input('text', 'cnt_child16['.$id.']', $row['cnt_child16'])?></td>
                <td class="cnt-input"><?=input('text', 'cnt_grown['.$id.']', $row['cnt_grown'])?></td>
                <td class="cnt-input"><?=input('text', 'cnt_pensioner['.$id.']', $row['cnt_pensioner'])?></td>
                <td>
                  <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить">
                    <i class="far fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
							<?
            }
					} else {
						?>
            <tr class="nofind">
              <td colspan="20">
                <div class="bg-warning">брони отсутствуют</div>
              </td>
            </tr>
						<?
					}
          ?>
          </tbody>
        </table>
        <button type="submit" class="btn btn-success" style="margin-top:20px;">Сохранить</button>
      </form>
      <?
      break;
  }
  exit;
}

$curYear = date('Y');
$curMonth = date('m');
$year = (int)$_GET['year'];
if(checkdate('01', '01', $year) === false) {
	$year = date('Y');
}

ob_start();

?>
<style type="text/css">
  .b-calendar { font: 14px/1.2 Arial, sans-serif; border:1px solid #e1e1e1;}
  .b-calendar.current { background-color:#d1eadf; border:1px solid #a0c8b6;}
  .b-calendar hr { height: 1px; overflow: hidden; font-size: 0; line-height: 0; background: #ccc; margin: 50px 0; border: 0; }
  .b-calendar--along { width: 250px; padding: 15px; margin: 0 auto; }
  .b-calendar--many { padding: 15px; /*width: 250px;*/ display: inline-block; vertical-align: top; margin:15px 0 0 0; }
  .b-calendar__title { text-align: center; margin: 0 0 20px;}
  .b-calendar__year { font-weight: bold; color: #333; }
  .b-calendar__tb { width: 100%;}
  .b-calendar__head { font: bold 14px/1.2 Arial, sans-serif; padding:5px 0; text-align: center; border-bottom: 1px solid #c0c0c0;}
  .b-calendar.current .b-calendar__head { border-bottom: 1px solid #a0c8b6;}
  .b-calendar__np { padding: 5px; }
  .b-calendar__day { font: 14px/1.2 Arial, sans-serif; padding: 8px 0; text-align: center;}
  .b-calendar__weekend a { color: red;}
  .b-calendar a {
    display: inline-block; width: 21px; height: 21px; text-align: center; overflow: hidden; background-color: #f2f2f2;
    line-height: 22px; font-size: 12px; border-radius: 5px; vertical-align: top; text-decoration:none;
  }
  .b-calendar a.green { background-color: green; color: #fff; }
  .b-calendar a.yellow { background-color: yellow; color: #000; }
  .b-calendar a.red { background-color: red; color: #fff; }
  .b-calendar a.busy { background-color: #6b6b6b; color: #fff; }

  .b-calendar-row {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display:         flex;
    flex-wrap: wrap;
  }
  .b-calendar-row > [class*='col-'] {
    display: flex;
    flex-direction: column;
  }

  @media screen and (min-width : 1200px) and (max-width: 1700px) {
    .b-calendar-row > [class*='col-'] {
      width:25%;
    }
  }

  #chYear label { position:relative; display:inline-block; margin:0 5px 0 0; vertical-align:middle;}
  #chYear select { display:inline-block; vertical-align:middle; width:auto;}
  #chYear { padding:10px;}
</style>

<script>
  $(function () {
    $('#chYear select').change(function () {
      location.href = 'calendar.php?year=' + $(this).val();
    });
    //
    $('.b-calendar a').click(function () {
      var day = $(this).attr('day');
      jPop('calendar.php?action=bron_info&day=' + day);
      return false;
    });
  });
</script>

<div id="chYear" class="panel-white">
  <label>Выберите год</label>
  <select class="form-control input-xs"><?
		for($v=2018; $v<=($curYear+1); $v++){
			$selected = $v == $year ? ' selected' : '';
			?><option value="<?=$v?>"<?=$selected?>><?=$v?></option><?
		}
  ?></select>
  <div class="clearfix"></div>
</div>

<?
// массив бронек на год
$bron = array();
$q = "SELECT 	b.iday,
              SUM(b.cnt_child7 + b.cnt_child16 + b.cnt_grown + b.cnt_pensioner) AS cnt
      FROM {$prx}bron b
      JOIN {$prx}day d on d.pkday = b.iday
      WHERE d.iyear = {$year}
      GROUP BY b.iday
      ORDER BY 1";
$r = sql($q);
while($arr = @mysqli_fetch_assoc($r)){
  $bron[$arr['iday']] = $arr['cnt'];
}

?><div class="b-calendar-row row"><?
for ($m = 1; $m <= 12; $m++) {
  $dm = $m < 10 ? '0'.$m : $m;
  ?>
  <div class="col-xs-6 col-sm-6 col-md-4 col-lg-2">
    <div class="b-calendar b-calendar--many<?=$dm.$year==$curMonth.$curYear?' current':''?>">
      <div class="b-calendar__title"><span class="b-calendar__month"><?=getRusDate('MU', '01.'.$dm.'.'.$year)?></span> <span class="b-calendar__year">'<?=$year?></span></div>
			<?=draw_calendar($m, $year)?>
    </div>
    <div class="clearfix"></div>
  </div>
  <?
  /*?>
  <div class="b-calendar b-calendar--many<?=$dm.$year==$curMonth.$curYear?' current':''?>">
    <div class="b-calendar__title"><span class="b-calendar__month"><?=getRusDate('MU', '01.'.$dm.'.'.$year)?></span> <span class="b-calendar__year">'<?=$year?></span></div>
    <?=draw_calendar($m, $year)?>
  </div>
  <?*/
}
?></div><?

$tbl = 'calendar';
$content = arr($h, ob_get_clean());
require('tpl/template.php');