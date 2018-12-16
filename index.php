<?
require('inc/common.php');
ob_start();
$index = true;

// --------------------- Блок Абонементов
?>
<div class="container-fluid">
  <div id="iabon">
    <h3>Медицинские показания к посещению<br>соляной пещеры</h3>

    <div class="ch2btn btn-group">
      <button type="button" class="btn btn-default active" for="iabon-adults"><span></span>Взрослым</button>
      <button type="button" class="btn btn-default" for="iabon-children"><span></span>Детям</button>
    </div>

    <?
    $tickets = gtv('tickets','*',1);
    ?>

    <div id="iabon-adults">
      <div class="iabon-list row">
				<? for($i=0; $i<6; $i++) { ?>
          <div class="iabon-list-item col-xs-6 col-sm-6 col-md-6">
            <div class="brd">
							<? if($i < 5){ ?>
                <div class="in-brd">
                  <h4>Аденоиды</h4>
                  <div class="age"><span>от 2-х до 16 лет</span></div>
                  <div class="hblue">Диагноз врача</div>
                  <div class="diseases">Аденоид I степени, Аденоид II степени, Аденоид III степени Аденоид I степени, Аденоид II степени, Аденоид III степени</div>
                  <div class="hblue">около 30 сеансов</div>
                  <a class="btn btn-warning btn-sm" data-target="#" href="#" role="button">Забронировать абонемент</a>
                </div>
							<?} else {?>
                <a href="" class="bn"></a>
							<?}?>
            </div>
          </div>
				<? }?>
      </div>
    </div>

    <div id="iabon-children">
      <div class="iabon-list row">
				<? for($i=0; $i<3; $i++) { ?>
          <div class="iabon-list-item col-xs-6 col-sm-6 col-md-6">
            <div class="brd">
							<? if($i < 3){ ?>
                <div class="in-brd">
                  <h4>Аденоиды</h4>
                  <div class="age"><span>от 2-х до 16 лет</span></div>
                  <div class="hblue">Диагноз врача</div>
                  <div class="diseases">Аденоид I степени, Аденоид II степени, Аденоид III степени Аденоид I степени, Аденоид II степени, Аденоид III степени</div>
                  <div class="hblue">около 30 сеансов</div>
                  <a class="btn btn-warning btn-sm" data-target="#" href="#" role="button">Забронировать абонемент</a>
                </div>
							<?} else {?>
                <a href="" class="bnr"></a>
							<?}?>
            </div>
          </div>
				<? }?>
      </div>
    </div>

  </div>
</div>
<?

// --------------------- Блок Промо
?>
<link href="/js/jquery-background-video/jquery.background-video.css" rel="stylesheet">
<script src="/js/jquery-background-video/jquery.background-video.js"></script>
<script>
  $(document).ready(function(){
    $('.polzasoli-bg').bgVideo({fadeIn: 2000, showPausePlay: false, pauseAfter: 0});
  });
</script>

<div id="ipromo">

  <div class="jquery-background-video-wrapper">

    <video class="polzasoli-bg jquery-background-video" loop autoplay muted poster="/img/polzasoli-bg.jpg">
      <source src="/img/polzasoli-bg.mp4" type="video/mp4">
      <source src="/img/polzasoli-bg.webm" type="video/webm">
      <source src="/img/polzasoli-bg.ogv" type="video/ogg">
    </video>
  
    <div id="in-ipromo">
      <div class="container-fluid">
        <h3>В нашей пещере полезно, комфортно<br>и очень весело находится всей семье!</h3>
        <div class="ipromo-list">
          <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3" style="letter-spacing:-0.5pt">
              <span><i></i></span>Просторная и полезная соляная пещера со стерильной атмосферой
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Уютная зона ожидания с книгами и детским уголком
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Бесплатный и быстрый<br>интернет
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Все удобные<br>способы<br>оплаты
            </div>
          </div>
          <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Удобная раздевалка и система хранения личных вещей
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Комфортное для вас время посещения сеансов
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Всегда вежливый и понимающий персонал
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
              <span><i></i></span>Большой выбор полезных товаров и подарков
            </div>
          </div>
        </div>
        <button class="btn btn-warning" onclick="scrollingTo($('#bron'),0,500);">Выбрать время и записаться на сеанс<i class="fas fa-arrow-down"></i></button>
      </div>
    </div>

  </div>
</div>
<?


// --------------------- Блок Отзывы
reviews();

// --------------------- Блок Фото
?>
<div id="igallery">
  <div class="container-fluid">

    <h3>Наши любимые посетители</h3>

    <div class="ch2btn btn-group">
      <button type="button" class="btn btn-default active" for="igallery-photo"><span></span>Фото</button>
      <button type="button" class="btn btn-default" for="igallery-video"><span></span>Видео</button>
    </div>

    <div id="igallery-photo">
      <?
			$images = array();
			// остальные фото
			//$imgs = getImages('goods',$good['id']);
			$imgs = array('1.jpg','2.jpg','3.jpg','1.jpg');
			foreach ($imgs as $im){
				$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . '/uploads/gallery/' . $im);
				if($size === false){
				  continue;
        }
				$w = $size[0] >= $size[1] ? '500' : '-';
				$h = $size[0] >= $size[1] ? '-' : '330';
				// на всякий случай
				if($size === false) die();
				$images[] = array(
					'base' => "/gallery/{$w}x{$h}/{$im}",
					'href' => "/gallery/{$im}",
				);
			}
			?><div class="row"><?
        $i=0;
        foreach ($images as $im){
          ?>
          <div class="item col-xs-6 col-sm-6 col-md-6">
            <a href="<?=$im['href']?>" ind="<?=$i++?>" title="Тестовое фото №1" data-gallery=""><img src="<?=$im['base']?>"></a>
          </div>
          <?
        }
      ?></div>
      <a href="/gallery/" class="more">Посмотреть больше фотографий</a>
    </div>

    <div id="igallery-video">
      <div class="row">
        <div class="item col-xs-6 col-sm-6 col-md-6">
          <a href="https://www.youtube.com/watch?time_continue=2&v=Z_m0Ip7XmNg"
            title="LES TWINS - An Industry Ahead"
            type="text/html"
            data-youtube="Z_m0Ip7XmNg"
          ><img src="/gallery/500x330/1.jpg"></a>
        </div>
        <div class="item col-xs-6 col-sm-6 col-md-6">
          <a href="https://www.youtube.com/watch?time_continue=2&v=Z_m0Ip7XmNg"
            title="LES TWINS - An Industry Ahead"
            type="text/html"
            data-youtube="Z_m0Ip7XmNg"
          ><img src="/gallery/500x330/2.jpg"></a>
        </div>
      </div>
      <a href="/video/" class="more">Посмотреть больше видео</a>
    </div>

  </div>
</div>
<?

// --------------------- Блок бронирования
bron();

// --------------------- Блок расчета стоимости абонемента
?>
<div id="fbron">
  <div class="container-fluid">

    <h3>Расчитайте примерную стоимость<br>подходящего вам абонемента</h3>

    <form id="what_cost" action="/inc/actions.php?action=what_cost" method="post" target="ajax">
      <input type="text" name="name" class="form-control" placeholder="Ваше Имя">

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

      <input type="text" name="phone" class="form-control" placeholder="Контактный телефон">
      <button class="btn btn-primary">Расчитать</button>
      <div class="note">Мы вам перезвоним,<br>сообщим точную стоимость<br>и ответим на ваши вопросы</div>
    </form>

  </div>
</div>
<?

// --------------------- FAQ
FAQ();

// --------------------- Виждеты соц сетей
SocVidgets();

// --------------------- Мини-баннер
banner_mini();

$content = ob_get_clean();
require('tpl/template.php');