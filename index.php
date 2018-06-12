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
      <button type="button" class="btn btn-default active"><span></span>Взрослым</button>
      <button type="button" class="btn btn-default"><span></span>Детям</button>
    </div>

    <div class="iabon-list row">
    <? for($i=0; $i<6; $i++) { ?>
      <div class="iabon-list-item col-xs-6 col-sm-6 col-md-6">
        <div class="brd">
          <? if($i < 5){ ?>
          <div class="in-brd">
            <h4>Аденоиды</h4>
            <div class="age"><span>от 2-х до 16 лет</span></div>
            <div class="hblue">Диагноз врача</div>
            <div class="disease">Аденоид I степени, Аденоид II степени, Аденоид III степени Аденоид I степени, Аденоид II степени, Аденоид III степени</div>
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
<?

// --------------------- Блок Абонементов
?>
<div id="ipromo">
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
    <a class="btn btn-warning" data-target="#" href="#" role="button">Выбрать время и записаться на сеанс<i class="fas fa-arrow-down"></i></a>
  </div>
</div>
<?


// --------------------- Блок Абонементов
?>
<div id="ireviews">
  <div class="container-fluid">

  <h3>Мнение наших любимых клиентов</h3>

  <div class="ch2btn btn-group">
    <button type="button" class="btn btn-default active" for="ireviews-story"><span></span>Рассказы</button>
    <button type="button" class="btn btn-default" for="ireviews-video"><span></span>Видео</button>
  </div>

  <div id="ireviews-story">
    <div class="txt">
			<? for($i=0; $i<5; $i++){?>
      <div class="item<?=!$i?' active':''?>">
        <?=mb_substr('Здравствуйте, хочу рассказать о результатах пользы соляной пещеры‚как раз пошла эпидемия вируса и конечно же ребёнок заболеп‚наконец то температура ушла‚но кашель! Лающий‚ ночной, до рвоты, не помогало ничего! Ни табпетки‚ ни полоскания, и мы пошли в галокамеру, наутро я не поверила‚что мы спали ночь! Ура! Потом он перешёл во влажный и постепенно проходит‚так же и насморк тоже вылечили,а не спасали никакие капли‚ ужасное состояние было.',0,rand(300,400))?>
      </div>
			<?}?>
    </div>
    <div class="author">
      <div class="row">
        <? for($i=0; $i<5; $i++){?>
        <div class="item<?=!$i?' active':''?>">
          <img class="img-circle" src="/uploads/reviews/104x104/5.jpg">
          <span>Надежда</span>
        </div>
        <?}?>
      </div>
    </div>
  </div>

  <div id="ireviews-video">
    <div class="video">
			<? for($i=0; $i<5; $i++){?>
        <div class="item<?=!$i?' active':''?>">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/Z_m0Ip7XmNg" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
			<?}?>
    </div>
    <div class="author">
      <div class="row">
				<? for($i=0; $i<5; $i++){?>
          <div class="item<?=!$i?' active':''?>">
            <img class="img-circle" src="/uploads/reviews/104x104/5.jpg">
            <span>Ольга</span>
          </div>
				<?}?>
      </div>
    </div>
  </div>

  <button class="btn btn-warning">Отставить отзыв</button>

  </div>
</div>
<?

$content = ob_get_clean();
require('tpl/template.php');