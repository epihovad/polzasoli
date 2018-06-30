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

// --------------------- Блок Абонементов
?>
<style>
  #bron { text-align:center;}
  #bron h3 { font-size:42px; margin:25px 0 27px;}
  #bron .bron-days { margin:0;}
  #bron .bron-days .bron-day-arr { margin-bottom:20px;}
  #bron .bron-day { width:100%; height:150px; overflow:hidden; background-color:#0f74a8; border:1px solid #c7c7c7; cursor:pointer; }
  #bron .bron-day .ch { height:27px; }
  #bron .bron-day input { margin:9px 0 0; padding:0; cursor:pointer;}
  #bron .bron-day .time { font-size:32px; color:#000; font-weight:700; line-height:34px;}
  #bron .bron-day .place { width:100%; padding:6px 0 3px;}
  #bron .bron-day .place span { width:17px; height:17px; border:1px solid #c2c2c2; background-color:#fff; display:inline-block; margin:0 2px; font-size:21px; line-height:11px;}
  #bron .bron-day .btm { height:100px; padding-top:6px;}
  #bron .bron-day .btm a { color:#2292ab; text-decoration:underline;}
  #bron .bron-day .btm .note span { font-weight:700; }
  #bron .bron-day.busy .ch input { display:none;}
  #bron .bron-day.busy { background-color:#afafaf;}
  #bron .bron-day.busy .place span { background-color:#bbbbbb; border:1px solid #999999; color:#999999;}
  #bron .bron-day.busy .btm { background-color:#e9e9e9; }
  #bron .bron-day.busy .btm .note { color:#c5070a; font-weight:700; }
  #bron .bron-day.red { background-color:#fcf6f6;}
  #bron .bron-day.red .place span.bs { border:1px solid #c5070a; background-color:#c5070a;}
  #bron .bron-day.red .btm .note span { color:#c5070a; }
  #bron .bron-day.green { background-color:#f1fce9;}
  #bron .bron-day.green .place span.bs { border:1px solid #62bc01; background-color:#62bc01;}
  #bron .bron-day.yellow { background-color:#fffdf2;}
  #bron .bron-day.yellow .place span.bs { border:1px solid #f7de32; background-color:#f7de32;}
</style>

<script>

</script>

<div id="bron">
  <div class="container-fluid">

    <h3>Расписание сеансов галотерапии<br>в соляной пещере «Ассоль»</h3>

    <div class="bron-days row">
      <?
      $colors = array('busy','red','green','yellow');
      for($i=0; $i<10; $i++){
        $color = $colors[array_rand($colors)];
        ?>
        <div class="bron-day-arr col-xs-5 col-sm-5 col-md-5">
          <div class="bron-day <?=$color?>">
            <div class="ch"><input type="checkbox" day="20180620"></div>
            <div class="time">9:20</div>
            <? if($color == 'busy'){ ?>
              <div class="place">
                <span class="bs">x</span><span class="bs">x</span><span class="bs">x</span><span class="bs">x</span><span>x</span><span>x</span>
              </div>
              <div class="btm">
                <div class="note">Все места заняты</div>
                <a href="" rel="nofollow">Выберите другой день</a>
              </div>
            <? } else { ?>
              <div class="place">
                <span class="bs"> </span><span class="bs"> </span><span class="bs"> </span><span class="bs"> </span><span> </span><span> </span>
              </div>
              <div class="btm">
                <div class="note">Осталось <span>2 места</span></div>
                <a href="" rel="nofollow">Забронировать</a>
              </div>
            <?}?>
          </div>
        </div>
        <?
      }
      ?>
    </div>

  </div>
</div>
<?


$content = ob_get_clean();
require('tpl/template.php');