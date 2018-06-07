<?
require('inc/common.php');
ob_start();
$index = true;

// --------------------- Блок Абонементов
?>
<style>
  #iabon { text-align:center; padding-bottom:600px;}
  #iabon h3 { font-size:42px; margin:25px 0 27px;}
  #iabon .iabon-ch-type .btn { font-size:32px; margin-bottom:17px; border:1px solid #bababa;}
  #iabon .iabon-ch-type .btn:nth-child(1) { padding:9px 26px 15px 83px;}
  #iabon .iabon-ch-type .btn:nth-child(2) { padding:9px 58px 15px 107px;}
  #iabon .iabon-ch-type .btn.active, #iabon .iabon-ch-type .btn:hover, #iabon .iabon-ch-type .btn:focus { background-color:#fbf9e1;}
  #iabon .iabon-ch-type .btn span { position:absolute; background-image:url(/img/imcol.png); background-repeat:no-repeat; width:52px; height:43px; font-size:0;}
  #iabon .iabon-ch-type .btn:nth-child(1) span { background-position:0 0; margin:3px 0 0 -68px;}
  #iabon .iabon-ch-type .btn:nth-child(2) span { background-position:-52px 0; margin:3px 0 0 -68px;}
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

      <div class="iabon-ch-type btn-group">
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

$content = ob_get_clean();
require('tpl/template.php');