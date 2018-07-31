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
		$per_load = (float)$bron[$ymd]['per_load'];
		$class = '';
		if($per_load){
			if($per_load <= 40){
				$class = 'green';
			} elseif ($per_load <= 80){
			  $class = 'yellow';
      } elseif ($per_load < 100){
			  $class = 'red';
		  } else {
				$class = 'busy';
      }
    }
    $ttl  = 'Загрузка дня: ' . (float)$bron[$ymd]['per_load'] . '%:<br>';
		$ttl .= 'Доступно мест: ' . (int)$bron[$ymd]['cnt_avail'] . '<br>';
		$ttl .= 'Забронировано мест: ' . (int)$bron[$ymd]['cnt_busy'];

		$calendar.= '<a href="#" day="' . $ymd . '" class="help ' . $class . '" title="' . htmlspecialchars($ttl) . '">' . $list_day . '</a>';
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
			$date = date('d.m.Y', strtotime($day));
      $q = "SELECT  b.*,
                    b.cnt_child7 + b.cnt_child16 + b.cnt_grown + b.cnt_pensioner as cnt,
                    t.*
            FROM {$prx}bron b
            JOIN {$prx}time t on b.itime = t.pktime
            WHERE b.iday = {$day}
            ORDER BY b.itime, b.id_user";
      $r = sql($q);
      ?>
      <style>
        #bron-detail { padding:15px;}
        #bron-detail h3 {margin: 0 0 20px 0; font-size: 22px; color:#eb0d41;}
        #bron-detail .table-list tbody th, #bron-detail .table-list tbody td { text-align:center;}
        #bron-detail .slt { background-color: #d8f2df !important; font-weight:700;}
      </style>

      <script>
        $(function () {
          $('#bron-detail a.help').tooltip({
            track: true,
            delay: 0,
            showURL: false,
            showBody: " - ",
            fade: 300
          });
        })
      </script>

      <form id="bron-detail" action="calendar.php?action=save" method="post" target="ajax">
        <h3>Информация о бронировании на <?=$date?></h3>
        <table class="table-list" style="width:auto">
          <thead>
            <tr>
              <th>№</th>
              <th>Дата сеанса</th>
              <th>Время сеанса</th>
              <th>Имя клиента</th>
              <th>Телефон клиента</th>
              <th>Впервые</th>
              <th class="slt">Всего гостей</th>
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
                <td><a class="help" href="bron.php?fl[day1]=<?=$date?>&fl[day2]=<?=$date?>" title="вывести список забронированных на дату мест" target="_blank"><?=$date?></a></td>
                <td><?=$row['ihour'].':'.$row['iminute']?></td>
                <td><a class="help" href="users.php?red=<?=$row['id_user']?>" title="перейти в редактирование карточки клиента" target="_blank"><?=$row['name']?></a></td>
                <td><a class="help" href="users.php?red=<?=$row['id_user']?>" title="перейти в редактирование карточки клиента" target="_blank"><?=$row['phone']?></a></td>
                <td><?=$row['first']?'<b>ДА</b>':'нет'?></td>
                <td class="slt"><?=$row['cnt']?></td>
                <td><?=$row['cnt_child7']?></td>
                <td><?=$row['cnt_child16']?></td>
                <td><?=$row['cnt_grown']?></td>
                <td><?=$row['cnt_pensioner']?></td>
                <td>
                  <a href="bron.php?red=<?=$id?>" class="help" title="редактировать бронь" target="_blank">
                    <button type="button" class="btn btn-info btn-xs">
                      <i class="far fa-edit"></i>
                    </button>
                  </a>
                  <a href="" class="help" title="удалить текущую бронь"
                    onclick="$(document).jAlert('show', 'confirm', 'Уверены?', function(){inajax('bron.php','action=del&id=<?=$id?>&from_calendar')}); return false;">
                    <button type="button" class="btn btn-danger btn-xs">
                      <i class="far fa-trash-alt"></i>
                    </button>
                  </a>
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
$q = "SELECT 	s.iday,
              COUNT(s.itime) * 6 AS cnt_avail, -- общая вместимость дня для всех сеансов
              b.cnt_busy, -- занято всего по дню
              ROUND(b.cnt_busy / (COUNT(s.itime) * 6) * 100, 2) AS per_load -- процент загрузки дня
      FROM {$prx}schedule s
      JOIN {$prx}day d ON d.pkday = s.iday
      LEFT JOIN (
        SELECT 	b.iday,
                SUM(b.cnt_child7 + b.cnt_child16 + b.cnt_grown + b.cnt_pensioner) AS cnt_busy
        FROM {$prx}bron b
        GROUP BY b.iday
      ) b ON b.iday = s.iday
      WHERE d.iyear = {$year}
      GROUP BY s.iday
      ORDER BY 1";
$r = sql($q);
while($arr = @mysqli_fetch_assoc($r)){
  $bron[$arr['iday']] = $arr;
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