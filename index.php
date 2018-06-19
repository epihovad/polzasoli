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

// --------------------- Блок Абонементов
?>
<style>
  #igallery { text-align:center; padding-bottom:65px; background:url(/img/bg-igallery.jpg) no-repeat #424151 top center; height:942px; overflow:hidden;}
  #igallery h3 { color:#fff; font-size:42px; margin:25px 0 27px;}
  #igallery #igallery-video { display:none;}
  #igallery .ch2btn .btn:nth-child(1) span { width:45px; height:36px; background-position:center -158px; margin:3px 15px 0 35px;}
  #igallery .ch2btn .btn:nth-child(2) span { width:46px; height:31px; background-position:center -127px; margin:8px 17px 0 26px;}
  #igallery .row { margin:0;}
  #igallery .item { padding:2px; background-color:#5a5a70;}
  #igallery .item:hover img, #igallery .item.active img { -webkit-filter: brightness(100%); filter: brightness(100%);}
  #igallery .item img { width:100%; -webkit-filter: brightness(50%); filter: brightness(50%);}
</style>

<script>
$(function () {
  $('#igallery-photo .item a').click(function () {
    var ind = parseInt($(this).attr('ind'));
    ind = isNaN(ind) ? 0 : ind;
    var $im = $('#igallery-photo .item a[ind=' + ind + ']');
    var link = $im.attr('href'),
        options = {index: link, index: ind},
        links = $('#igallery-photo .item a');
    blueimp.Gallery(links, options);
    return false;
  });
  $('#igallery-video .item a').click(function () {
    var ind = parseInt($(this).attr('ind'));
    ind = isNaN(ind) ? 0 : ind;
    var $im = $('#igallery-photo .item a[ind=' + ind + ']');
    var link = $im.attr('href'),
      options = {index: link, index: ind},
      links = $('#igallery-video .item a');
    blueimp.Gallery(links, options);
    return false;
  });
})
</script>

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
    </div>

  </div>
</div>
<?

$content = ob_get_clean();
require('tpl/template.php');