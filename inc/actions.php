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
		case 'seance':
		  jAlert('привет');
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
    // ------------------- Запись на сеанс
    case 'seance':
      ?>
      <style>
        #frm-seance { width:376px; padding:15px 0 15px 0;}
        #frm-seance h4 { font-size:30px; font-weight:700; margin:0 0 15px; }
        #frm-seance .pad { padding:0 29px;}
        #frm-seance input[name="name"] { margin-bottom:20px; font: 400 16px 'Roboto', sans-serif; color: #000;}
        #frm-seance input[name="phone"] { margin-bottom:10px; font: 400 16px 'Roboto', sans-serif; color: #000;}
        #frm-seance .sguest label {font-size:14px; padding:20px 0 5px;}
        #frm-seance .sguest .sign { color:#939393;}
        #frm-seance .sguest .btn-number { color:#939393;}
        #frm-seance .sguest .btn-number:hover, #frm-seance .sguest .btn-number:active { color:#000;}
        #frm-seance .sguest .input-number { border-color:#419fb9; color:#939393; }
        #frm-seance .sguest .ch .input-group .input-group-btn { border-color:#419fb9; }
        #frm-seance .sep { margin-top:25px; height:1px; border-top:2px dotted #419fb9; padding:22px 0 0;}
        #frm-seance .checkbox { margin:0 0 10px; }
        #frm-seance .checkbox input { margin-top:0;}
        #frm-seance .checkbox label { font-size:13px; line-height:14px;}
        #frm-seance .rules { font-size:13px; line-height:14px; padding-bottom:15px;}
        #frm-seance .rules + .checkbox label { color:#939393;}
        #frm-seance .rules + .checkbox a { color:#939393; text-decoration:underline;}
        #frm-seance .rules + .checkbox a:hover { text-decoration:none;}
        #frm-seance button { margin-top:10px;}
      </style>
    <script>
      $(function () {
        chQuant($('#frm-seance'));
      })
    </script>

      <form id="frm-seance" action="/inc/actions.php?action=seance" class="frm" target="ajax" method="post">
        <div class="pad">
          <h4>Записаться на сеанс</h4>
          <input type="text" class="form-control" name="name" placeholder="Ваше Имя">
          <input type="text" class="form-control" name="phone" placeholder="Контактный телефон">
          <div class="row sguest">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label>Дети (до 7 лет)</label>
              <div class="ch"><?=chQuant('ch7', 0, 0)?></div>
              <span class="sign">/чел.</span>
              <label>Взрослые</label>
              <div class="ch"><?=chQuant('grown', 0, 0)?></div>
              <span class="sign">/чел.</span>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label>Дети (до 16 лет)</label>
              <div class="ch"><?=chQuant('ch16', 0, 0)?></div>
              <span class="sign">/чел.</span>
              <label>Пенсионеры</label>
              <div class="ch"><?=chQuant('pensioner', 0, 0)?></div>
              <span class="sign">/чел.</span>
            </div>
          </div>
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