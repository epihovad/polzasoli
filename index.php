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
                  <div class="disease">Аденоид I степени, Аденоид II степени, Аденоид III степени Аденоид I степени, Аденоид II степени, Аденоид III степени</div>
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

// --------------------- Блок Промо
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
    <button class="btn btn-warning" onclick="scrollingTo($('#bron'),0,500);">Выбрать время и записаться на сеанс<i class="fas fa-arrow-down"></i></button>
  </div>
</div>
<?


// --------------------- Блок Отзывы
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
?>
<div id="bron">
  <div class="container-fluid">

    <h3>Расписание сеансов галотерапии<br>в соляной пещере «Ассоль»</h3>

    <div class="bron-calendar">
      <a href="" rel="nofollow">10</a><a href="" rel="nofollow">11</a><a href="" rel="nofollow">12</a><a href="" rel="nofollow">13</a>
      <a href="" class="prev" rel="nofollow"><i class="fas fa-chevron-left"></i></a>
      <a href="" class="cur" rel="nofollow"><span>15 июня</span><i class="far fa-calendar-alt"></i></a>
      <a href="" class="next" rel="nofollow"><i class="fas fa-chevron-right"></i></a>
      <a href="" rel="nofollow">15</a><a href="" rel="nofollow">16</a><a href="" rel="nofollow">17</a><a href="" rel="nofollow">18</a>
    </div>

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
      <button class="btn btn-warning">Записаться на сеанс <b>18.04.18 в 15:20</b></button>
      <div class="note">Мы обязательно предварительно вам перезвоним<br>и уточним время и другие детали проведения сеанса</div>
      <div class="lnks">
        <div><i class="fas fa-check-circle"></i><a href="">О пользе галотерапии</a></div>
        <div><i class="fas fa-ban"></i><a href="">Противопоказания</a></div>
      </div>
    </div>

  </div>
</div>
<?

// --------------------- Блок расчета стоимости абонемента
?>
<div id="fbron">
  <div class="container-fluid">

    <h3>Расчитайте примерную стоимость<br>подходящего вам абонемента</h3>

    <form action="/inc/actions.php" method="post" target="ajax">
      <input type="text" name="fio" class="form-control" placeholder="Ваше Имя">

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

      <input type="text" name="phone" class="form-control" placeholder="Контактный телефон">
      <button class="btn btn-primary">Расчитать</button>
      <div class="note">Мы вам перезвоним,<br>сообщим точную стоимость<br>и ответим на ваши вопросы</div>
    </form>

  </div>
</div>
<?

// --------------------- FAQ
?>
<div id="ifaq">
  <div class="container-fluid">

    <h3>Ответы на часто задаваемые вопросы</h3>

    <?
    $res = sql("SELECT * FROM {$prx}faq WHERE status = 1 ORDER BY sort,id");
    while ($row = mysqli_fetch_assoc($res)){
      ?>
      <a href="" class="ifaq-q" rel="nofollow"><i>?</i><?=$row['question']?></a>
      <div class="ifaq-a"><h4>Ответ:</h4><?=$row['answer']?></div>
      <?
    }
    ?>

  </div>
</div>
<?

// --------------------- FAQ
?>
<style>
#soc-vidget { text-align:center; min-height:550px;}
#soc-vidget h3 { font-size:36px; margin:25px 0 27px;}
</style>
<div id="soc-vidget">
  <div class="container-fluid">

    <h3>Оставайтесь с нами в социальных сетях<br>и узнавайте первыми информацию о скидках<br>и специальных предложениях!</h3>


  </div>
</div>
<?

// --------------------- Мини-баннер
banner_mini();

$content = ob_get_clean();
require('tpl/template.php');