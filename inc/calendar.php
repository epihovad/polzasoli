<?

function draw_calendar($month, $year, $action = 'none') {

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
		$href = $year . ($month < 10 ? '0' : '') . $month . ($list_day < 10 ? '0' : '') . $list_day;
		$calendar.= '<a href="schedule.php?day=' . $href . '">' . $list_day . '</a>';
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