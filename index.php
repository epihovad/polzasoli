<?
require('inc/common.php');
ob_start();
$index = true;

// --------------------- Блок Абонементов
?>
<style>
  #iabon { text-align:center; padding-bottom:65px;}
  #iabon h3 { font-size:42px; margin:25px 0 27px;}
  #iabon .btn-group .btn { font-size:32px; margin-bottom:17px; border:1px solid #bababa;}
  #iabon .btn-group .btn:nth-child(1) { padding:9px 26px 15px 83px;}
  #iabon .btn-group .btn:nth-child(2) { padding:9px 58px 15px 107px;}
  #iabon .btn-group .btn:focus { background-color:#fff;}
  #iabon .btn-group .btn.active, #iabon .btn-group .btn:hover { background-color:#fbf9e1;}
  #iabon .btn-group .btn span { position:absolute; background-image:url(/img/imcol.png); background-repeat:no-repeat; width:52px; height:43px; font-size:0;}
  #iabon .btn-group .btn:nth-child(1) span { background-position:0 0; margin:3px 0 0 -68px;}
  #iabon .btn-group .btn:nth-child(2) span { background-position:-52px 0; margin:3px 0 0 -68px;}
  #iabon .iabon-list { margin:0;}
  #iabon .iabon-list-item { padding:0 0 2px 0;}
  #iabon .iabon-list-item:nth-child(odd) { padding-right:1px;}
  #iabon .iabon-list-item:nth-child(even) { padding-left:1px;}
  #iabon .iabon-list-item .brd { border:1px solid #e5e5e5; height:272px; overflow:hidden;}
  #iabon .iabon-list-item .in-brd { padding:0 18px;}
  #iabon .iabon-list-item h4 { font-weight:700; font-size:23px; margin:21px 0 7px;}
  #iabon .iabon-list-item .age span { padding:0 65px 7px; border-bottom:2px solid #fff072; display:inline-block;}
  #iabon .iabon-list-item .hblue { font-weight:700; font-size:19px; color:#2292ab; margin:5px 0 7px;}
  #iabon .iabon-list-item .disease { font-size:16px; height: 48px; overflow: hidden; }
  #iabon .iabon-list .bnr {
    display:inline-block; width:100%; height:272px; overflow:hidden;
    background-image:url(/img/iabon-banner.png); background-repeat:no-repeat; background-size:100%; background-color:#0ebac5;
  }
</style>

  <div class="container-fluid">
    <div id="iabon">
      <h3>Медицинские показания к посещению<br>соляной пещеры</h3>

      <div class="btn-group">
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
<style>
  #ipromo {text-align:center; color:#fff; background:url(/img/bg-promo.jpg) no-repeat center; background-size:cover; height:702px; overflow:hidden;}
  #ipromo h3 { font-size:42px; margin:30px 0 54px; line-height:50px;}
  #ipromo .ipromo-list .row:nth-child(1) { padding:0 0 56px 0;}
  #ipromo .ipromo-list .row div { font-size:20px; line-height:24px;}
  #ipromo .ipromo-list .row:nth-child(1) span { display:block; font-size:0; margin-bottom:12px; height:73px; overflow:hidden; }
  #ipromo .ipromo-list .row:nth-child(2) span { display:block; font-size:0; margin-bottom:14px; height:82px; overflow:hidden; }
  #ipromo .ipromo-list i { display:block; background-repeat:no-repeat; background-image:url(/img/imcol-promo.png);}
  #ipromo .ipromo-list .row:nth-child(1) div:nth-child(1) i { height:71px; background-position:center 0; margin-top:1px; }
  #ipromo .ipromo-list .row:nth-child(1) div:nth-child(2) i { height:70px; background-position:center -71px; margin-top:1px;}
  #ipromo .ipromo-list .row:nth-child(1) div:nth-child(3) i { height:56px; background-position:center -141px; margin-top:8px; }
  #ipromo .ipromo-list .row:nth-child(1) div:nth-child(4) i { height:73px; background-position:center -197px; margin-top:0px; }
  #ipromo .ipromo-list .row:nth-child(2) div:nth-child(1) i { height:76px; background-position:center -270px; margin-top:0px; }
  #ipromo .ipromo-list .row:nth-child(2) div:nth-child(2) i { height:81px; background-position:center -346px; margin-top:0px;}
  #ipromo .ipromo-list .row:nth-child(2) div:nth-child(3) i { height:82px; background-position:center -427px; margin-top:0px; }
  #ipromo .ipromo-list .row:nth-child(2) div:nth-child(4) i { height:80px; background-position:center -509px; margin-top:0px; }
  #ipromo .btn { margin-top:48px;}
</style>

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

$content = ob_get_clean();
require('tpl/template.php');