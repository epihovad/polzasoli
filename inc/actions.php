<?
@session_start();

require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php');

if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		// -------------------
		case 'bron':
			foreach($_REQUEST as $k=>$v)
				$$k = clean($v);

			// защита от спама
			$refererUrlArr = parse_url($_SERVER['HTTP_REFERER']);
			if($refererUrlArr['host'] != $_SERVER['HTTP_HOST'])
				exit;

			$iday = preg_replace("/\D/",'',date('Ymd',strtotime($date)));
			$first = (int)$first;

			// проверка имени клиента
			if(!$name){
				jAlert('Пожалуйста, укажите Ваше имя');
			}
			// проверка телефона
			$phone = substr(preg_replace("/\D/",'',$phone), -10);
			if(strlen($phone) != 10){
				jAlert('Некорректный номер телефона');
			}
			// проверим Email
			if($email && !check_mail($email)){
				jAlert('Некорректный Email');
			}
			// проверим кол-во гостей
			if(!$cnt = $cnt_child7 + $cnt_child16 + $cnt_grown + $cnt_pensioner){
				jAlert('Пожалуйста, укажите количество гостей');
			}
			// проверим дату
			if(!MyCheckDate($date)){
				jAlert('Неверная дата бронирования');
			}
			// проверим время
			if(!$itime = getField("SELECT pktime FROM {$prx}time WHERE pktime = '".(int)$itime."'")){
				jAlert('Пожалуйста, выберите сеанс');
			}
			$checkBron = checkBron($iday, $itime, $cnt);
			if($checkBron['status'] == 'busy'){
				jAlert('Для выбранного сеанса превышен лимит по кол-ву мест.<br>Доступно мест: ' . $checkBron['avail'] . '<br>' . 'Запрошено мест: ' . $cnt);
			}
			if(!$pdata = (int)$pdata){
				jAlert('Пожалуйста, примите согласасие на<br>обработку Ваших персональных данных');
			}

			// добавляем клиента в базу
			if(!$id_user = getField("SELECT id FROM {$prx}users WHERE phone = '{$phone}'")){
				$set = "phone = '{$phone}',
                name = '{$name}',
                email = '{$email}'";
				update('log', "type = 'ошибка при сохранении клиента', notes = '".clean($set)."'");
				if(!$id_user = update('users', $set)) {
					// логируем
					update('log', "type = 'ошибка при сохранении клиента', notes = '".clean($set)."'");
					jAlert('Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.');
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
			if(!$id = update('bron', $set)) {
				// логируем
				update('log', "type = 'ошибка при сохранении брони', notes = '".clean($set)."'");
				jAlert('Во время сохранения данных произошла ошибка.');
			}

			$number = $iday . '/' . $itime . '-' . $id;
			$_SESSION['my_bron'][] = $number;

			// журнал
			update('log', "type = 'новая бронь', link = 'bron.php?red={$id}'");

			$message  = 'Уважаемый(ая) '.$name.'!';
			$message .= '<br>Номер Вашей брони: <b>'.$number.'</b>';
			$message .= '<br>Наш менеджер свяжется с Вами для уточнения заказа.';

			?><script>top.jQuery(document).jAlert('show','alert','<?=$message?>',function(){top.location.href='/cart/?show=bron&number=<?=$number?>'});</script><?
			break;
		// ------------------- Форма обратной связи
		case 'feedback':

			$type = '';
			switch ($_GET['type']){
				case 'cns': $type = 'Консультация'; break;
				case 'opt': $type = 'Оптовики'; break;
				default:    $type = 'Сообщение'; break;
			}

			foreach($_POST as $k=>$v)
				$$k = clean($v);

			if($hdn) exit; // спам-боты

			if($type == 'Оптовики' && !$firma) jAlert('Пожалуйста, укажите название организации');
			if(!$name) jAlert($type == 'Оптовики' ? 'Пожалуйста, укажите контактное лицо' : 'Пожалуйста, представьтесь');
			if($type == 'Консультация' || $type == 'Оптовики'){
				$phone = substr(preg_replace("/\D/",'',$phone), -10);
				if(strlen($phone) != 10) jAlert('Некорректный номер телефона');
			}
			if($type != 'Консультация'){
				if(!check_mail($email)) jAlert('Введен некорректный E-mail');
			}
			if(!$text) jAlert('Пожалуйста, введите Ваше сообщение');

			$mailto = array();

			ob_start();
			if($type == 'Оптовики'){
				?><b>Организация</b>: <?=$firma?><br /><?
			}
			?>
      <b><?=$type == 'Оптовики' ? 'Контактное лицо' : 'Имя'?></b>: <?=$name?><br />
      <b>E-mail</b>: <?=$email?><br />
      <b>Телефон</b>: <?=$phone?'+7'.$phone:'-'?><br />
      <b>Сообщение</b>: <?=$text?><br />
			<?
			$mailto['text'] = ob_get_clean();

			$set = "type='{$type}', firma='{$firma}', name='{$name}', email='{$email}', phone='{$phone}', text='{$text}'";

			if(!update('msg', $set)){
				$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
				$mailto['theme'] = "Ошибка ({$type})";
			} else {
				$alert = 'Спасибо за Ваше обращение.<br>Сообщение уже отправлено нашему менеджеру';
				$mailto['theme'] = $type;
			}

			// мылим админу
			mailTo(array(set('admin_mail'),'info@estill.ru'), $mailto['theme'], $mailto['text']);

			?><script>top.jQuery(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.jQuery.arcticmodal('close')});</script><?
			exit;

		// ------------------- Подписка на рассылку
		case 'subscribe':
			foreach($_REQUEST as $k=>$v)
				$$k = clean($v);

			// защита от спама
			$refererUrlArr = parse_url($_SERVER['HTTP_REFERER']);
			if($refererUrlArr['host'] != $_SERVER['HTTP_HOST'])
				exit;
			//if($hdn) exit;

			if(!check_mail($email)){
				$alert = 'Введен некорректный Email';
				?>
        <script>
          top.$('#subscribe .frm i').removeClass('disabled');
          top.$(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.$('#subscribe .frm input').val('')});
        </script>
				<?
				exit;
			}

			// проверка подписан ли уже email
			if($subs = getRow("SELECT * FROM {$prx}subscribers WHERE email = '{$email}'")){

				if(!$subs['unsubscribe_date']){
					$alert = 'Вы уже подписаны на нашу рассылку';
				} else {
					if(!update('subscribers',"unsubscribe_date = NULL", $subs['id'])){
						$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
					} else {
						$alert = 'Вы успешно подписаны на рассылку.<br>Благодарим за проявленный интерес.<br>Вы не пожалеете!';
						// мылим админу
						mailTo(array(set('admin_mail')), 'Новый подписчик', 'У нас новый подписчик:<br>'.$email);
					}
				}
			} else {

				if(!update('subscribers',"email = '{$email}'")){
					$alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
				} else {
					$alert = 'Вы успешно подписаны на рассылку.<br>Благодарим за проявленный интерес.<br>Вы не пожалеете!';
					// мылим админу
					mailTo(array(set('admin_mail')), 'Новый подписчик', 'У нас новый подписчик:<br>'.$email);
				}
			}

			?>
      <script>
        top.$('#subscribe .frm i').removeClass('disabled');
        top.$(document).jAlert('show','alert','<?=cleanJS($alert)?>',function(){top.$('#subscribe .frm input').val('')});
      </script>
			<?
			exit;
	}
	exit;
}

if(isset($_GET['show']))
{
	switch($_GET['show'])
	{
		case 'avail_bron_days':

			$iday = (int)$_GET['day'] ?: date('Ymd');

			$avail_days = GetFreeSeanseDays();

			// если дата кривая или она не доступна
			if(!MyCheckDate($iday, 'Ymd') || in_array($iday, $avail_days) === false){
				// берём первую доступную дату
				$iday = $avail_days[0];
			}

			$date = date('d.m.Y', strtotime($iday));

			ob_start();

			$i=0;
			foreach ($avail_days as $k => $d){
				$num_day = date('d', strtotime($d));
				if($d == $iday){
					$prev = @$avail_days[$i-1];
					$next = @$avail_days[$i+1];
					?><a href="" day="<?=$prev?>" class="prev<?=!$prev?' disabled':''?>" rel="nofollow"><i class="fas fa-chevron-left"></i></a><?
          ?><a href="" class="active<?=$d==date('Ymd')?' cur':''?>" rel="nofollow"><span><?=$num_day?> <?=getRusDate('M',$date)?></span><?/*<i class="far fa-calendar-alt"></i>*/?></a><?
          ?><a href="" day="<?=$next?>" class="next<?=!$next?' disabled':''?>" rel="nofollow"><i class="fas fa-chevron-right"></i></a><?
        } else {
					?><a href="" day="<?=$d?>" rel="nofollow"><?=$num_day?></a><?
				}
				$i++;
			}
			$data = ob_get_clean();

			?>
      <script>
        top.$('#bron .bron-calendar').html('<?=cleanJS($data)?>');
      </script>
			<?

			break;
		//
		case 'schedule_on_day':

			$iday = (int)$_GET['day'];

			// если дата кривая
			if(!MyCheckDate($iday, 'Ymd')){
				// берём текущую
				$iday = date('Ymd');
			}

			$avail_seanse = GetFreeSeanse($iday);

			// проверим доступна ли переданная дата на момент открытия окна
			if(in_array($iday, array_keys($avail_seanse)) === false){
				// берём первую доступную дату
				$iday = array_keys($avail_seanse)[0];
			}

			$date = date('d.m.Y', strtotime($iday));

			ob_start();
			$query = "SELECT 	s.iday,
                        s.itime,
                        s.discount,
                        t.ihour,
                        t.iminute,
                        IF(b.busy IS NULL, 0, b.busy) AS busy,
                        6 - IF(b.busy IS NULL, 0, b.busy) AS free,
                        IF(b.busy < 6 OR b.busy IS NULL, 1, 0) AS is_avail
                FROM {$prx}schedule s
                JOIN {$prx}time t ON t.pktime = s.itime
                LEFT JOIN (
                  SELECT 	iday,
                          itime,
                          SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS busy
                  FROM {$prx}bron
                  GROUP BY 	iday,
                            itime
                ) b ON b.iday = s.iday AND b.itime = s.itime
                WHERE s.iday = {$iday}
                ORDER BY s.itime";

			$res = sql($query);
			while ($row = @mysqli_fetch_assoc($res)){
				if($row['busy'] <= 2) $color = 'green';
        elseif ($row['busy'] <= 4) $color = 'yellow';
        elseif ($row['busy'] <= 5) $color = 'red';
				else $color = 'busy';
				?>
        <div class="bron-day-arr col-xs-5 col-sm-5 col-md-5">
          <div class="bron-day <?=$color?>">
						<? if($row['discount']){ ?>
              <div class="discount">-<?=$row['discount']?>%</div>
						<?}?>
            <div class="ch">
              <input type="checkbox"
                day="<?=$row['iday']?>-<?=$row['itime']?>"
                time="<?=$date?> в <?=$row['ihour']?>:<?=$row['iminute']?>"
                discount="<?=$row['discount']?>"
              >
            </div>
            <div class="time"><?=$row['ihour'].':'.$row['iminute']?></div>
						<? if($color == 'busy'){ ?>
              <div class="place"><?=str_repeat('<span class="bs">x</span>',6)?></div>
              <div class="btm">
                <div class="note">Все места заняты</div>
                <a href="" rel="nofollow">Выберите другой день</a>
              </div>
						<? } else { ?>
              <div class="place">
								<?=str_repeat('<span class="bs"> </span>',$row['busy'])?><?=str_repeat('<span> </span>',$row['free'])?>
              </div>
              <div class="btm">
                <div class="note">Осталось <span><?=$row['free']?> <?=num2str($row['free'],'место')?></span></div>
                <a href="" rel="nofollow">Забронировать</a>
              </div>
						<?}?>
          </div>
        </div>
				<?
			}
			$data = ob_get_clean();

			?>
      <script>
        top.$('#seanse-list').html('<?=cleanJS($data)?>');
      </script>
			<?
			break;
		// ------------------- Запись на сеанс
		case 'popup_bron':
			$iday = date('Ymd');
			$date = date('d.m.Y');
			$itime = 0;

			// если дата и время передаётся из блока выбора сеанса
			if($day = $_GET['day']){
				$arr = explode('-', $day);
				$iday_ = (int)$arr[0];
				$itime = (int)$arr[1];
				if(MyCheckDate($iday_, 'Ymd')){
					$iday = $iday_;
					$date = date('d.m.Y', strtotime($iday));
				}
			}

			// массив доступных сеансов
			$avail_seanse = GetFreeSeanse($iday);

			// проверим доступна ли переданная дата на момент открытия окна
			if(in_array($iday, array_keys($avail_seanse)) === false){
				// берём первую доступную дату
				$iday = array_keys($avail_seanse)[0];
				$date = date('d.m.Y', strtotime($iday));
				$itime = 0;
			}

			$last_avail_day = array_keys($avail_seanse)[sizeof(array_keys($avail_seanse)) - 1];
			$avail_days = array();
			foreach (array_keys($avail_seanse) as $d){
			  $avail_days[] = date('d.m.Y', strtotime($d));
      }

			$iy1 = date('Y', strtotime($iday));
			$im1 = date('m', strtotime($iday)) - 1;
			$id1 = date('d', strtotime($iday));

			$iy2 = date('Y', strtotime($last_avail_day));
			$im2 = date('m', strtotime($last_avail_day)) - 1;
			$id2 = date('d', strtotime($last_avail_day));
			?>
      <script>
        $(function () {
          chQuant($('#frm-seance'));
          //
          var avail_days = ['<?=implode("','", $avail_days)?>'];
          $('#frm-seance .dt').datepicker({
            minDate: new Date(<?=$iy1?>, <?=$im1?>, <?=$id1?>),
            maxDate: new Date(<?=$iy2?>, <?=$im2?>, <?=$id2?>),
            beforeShowDay: function(date){
              var string = $.datepicker.formatDate('dd.mm.yy', date);
              return [ avail_days.indexOf(string) >= 0 ]
            }
          }).change(function () {
            var d = moment($(this).val(), 'DD.MM.YYYY');
            if(!d._isValid){
              $(document).jAlert('show','alert','Указана некорректная дата');
              return false;
            }
            var iday = d.format('YYYYMMDD');
            $('#frm-seance .tm option[dt!='+iday+']').addClass('hidden');
            $('#frm-seance .tm option').removeClass('selected');
            $('#frm-seance .tm option[dt='+iday+']').removeClass('hidden');
            $('#frm-seance .tm option[dt='+iday+']:first').addClass('selected');
            $('#frm-seance .tm').val($('#frm-seance .tm option[dt='+iday+']:first').val()).change();
          });
          //
          $('#frm-seance .tm').change(function () {
            $(this).find('option').removeClass('selected');
            $(this).find('option:selected').addClass('selected');
          });
        })
      </script>
      <form id="frm-seance" action="/inc/actions.php?action=bron" class="frm" target="ajax" method="post">
        <div class="pad">
          <h4>Записаться на сеанс</h4>
          <input type="text" class="form-control" name="name" placeholder="Ваше Имя">
          <input type="text" class="form-control" name="phone" placeholder="Контактный телефон">
          <input type="text" class="form-control" name="email" placeholder="Ваш Email (не обязательно)">
          <div class="row sguest">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label>Дети (до 7 лет)</label>
              <div class="ch"><?=chQuant('cnt_child7', 0, 0)?></div>
              <span class="sign">/чел.</span>
              <label>Взрослые</label>
              <div class="ch"><?=chQuant('cnt_grown', 0, 0)?></div>
              <span class="sign">/чел.</span>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label>Дети (до 16 лет)</label>
              <div class="ch"><?=chQuant('cnt_child16', 0, 0)?></div>
              <span class="sign">/чел.</span>
              <label>Пенсионеры</label>
              <div class="ch"><?=chQuant('cnt_pensioner', 0, 0)?></div>
              <span class="sign">/чел.</span>
            </div>
          </div>
        </div>
        <div class="sep"></div>
        <div class="pad">
          <h5>Желаемые дата и время сеанса:</h5>
					<?
					if($avail_seanse){
						?>
            <input type="text" class="dt form-control" name="date" value="<?=$date?>">
            <select class="form-control tm" name="itime"><?
							foreach ($avail_seanse as $d => $item){
								$hidden = $d != $iday ? 'hidden' : '';
								foreach ($item as $tm => $arr){
									$selected = $d == $iday && $tm == $itime ? ' selected' : '';
									$disabled = !$arr['is_avail'] ? ' disabled' : '';
									?><option value="<?=$tm?>" dt="<?=$d?>" class="<?=$hidden?>"<?=$selected?><?=$disabled?>><?=$arr['ihour']?>:<?=$arr['iminute']?> <span>доступно мест: <?=$arr['free']?></span></option><?
								}
							}
							?></select>
            <div class="clearfix"></div>
						<?
					}
					?>
        </div>
        <div class="sep"></div>
        <div class="pad">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="first" value="1"> Я иду в первый раз
            </label>
          </div>
          <div class="rules">
            Пожалуйста, <a href="" target="_blank">ознакомьтесь с правилами посещения соляной пещеры</a>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="pdata" value="1" checked> Я согласен(на) на обработку <a href="" target="_blank">моих персональных данных</a>
            </label>
          </div>
          <div class="text-center"><button class="btn btn-warning">Забронировать</button></div>
        </div>
      </form>
      <script>
        $(function () {
          Inputmask({mask: '+7 (999) 999-99-99',showMaskOnHover: false}).mask($('#frm-seance input[name="phone"]'));
        });
      </script>
			<?
			break;
		/*// ------------------- Форма обратной связи
		case 'feedback':
			$type = $_GET['type'];
			$h3 = '';
		  switch ($type){
        case 'phone': $h3 = 'Позвоните нам по бесплатному телефону <span>'.set('phone').'</span><br>или просто оставьте сообщение'; break;
				case 'email': $h3 = 'Напишите нам на E-mail:<br><span>'.nl2br(set('email')).'</span><br>или просто оставьте сообщение'; break;
        case 'cns':   $h3 = 'Закажите бесплатную консультацию'; break;
        case 'opt':   $h3 = 'Заявка для оптовых покупателей'; break;
        default:      $h3 = 'Оставьте нам сообщение'; break;
      }
		  ?>
			<style>
				#frm-fb {text-align:center; width:385px;}
				#frm-fb h3 {margin: 0px 0px 20px;}
        #frm-fb h3 span { color:#313132; display:inline-block; padding-bottom:15px;}
				#frm-fb .form-control { width:100%;}
				#frm-fb textarea { resize:none; }
        #frm-fb .policy { padding-top:5px; font-size:12px; line-height:14px; color:#7a7a7a;}
      </style>

			<form id="frm-fb" action="/inc/actions.php?action=feedback&type=<?=$type?>" class="frm" target="ajax" method="post">
				<h3><?=$h3?></h3>
        <? if ($type == 'opt'){?>
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-home"></span></span>
          <input class="form-control" placeholder="Название организации" name="firma" type="text">
        </div>
        <div class="clear" style="padding-bottom:10px;"></div>
        <?}?>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
					<input class="form-control" placeholder="<?=$type=='opt'?'Контактное лицо':'Как к Вам обращаться?'?>" name="name" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
				<? if(in_array($type,array('cns','opt')) !== false){ ?>
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-phone-alt"></span></span>
          <input class="form-control" placeholder="+7 (___) ___-__-__" name="phone" type="text">
        </div>
        <div class="clear" style="padding-bottom:10px;"></div>
        <?}?>
				<? if($type != 'cns'){ ?>
				<div class="input-group">
					<span class="input-group-addon">@</span>
					<input class="form-control" placeholder="Введите Ваш E-mail адрес" name="email" type="text">
				</div>
				<div class="clear" style="padding-bottom:10px;"></div>
        <?}?>
				<div class="input-group">
					<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
					<textarea class="form-control" placeholder="Введите Ваше сообщение" rows="4" name="text"></textarea>
				</div>
				<div class="clear" style="padding-top:15px;"></div>
        <div class="btn btn-mini" onclick="jQuery('#frm-fb').submit();"><div>Отправить</div></div>
        <div class="hdn"><input type="text" name="hdn" value=""></div>
        <div class="clear" style="padding-bottom:10px;"></div>
        <div class="policy">
          Нажимая на кнопку «Отправить» я даю своё согласие на обработку моих <a href="/personal_data.htm" target="_blank">персональных данных</a>,
          в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей, определенных
          <a href="/privacy_policy.htm" target="_blank">Политикой конфиденциальности</a>.
        </div>
			</form>
			<?if(in_array($type,array('cns','opt')) !== false){?>
      <script src="/js/jquery/inputmask.min.js"></script>
      <script src="/js/jquery/inputmask.phone.extensions.min.js"></script>
      <script>
        jQuery(document).ready(function( $ ) {
          Inputmask({mask: '+7 (999) 999-99-99',showMaskOnHover: false}).mask($('#frm-fb input[name="phone"]'));
        });
      </script>
      <?}
			break;*/
	}
	exit;
}