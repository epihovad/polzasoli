<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?/*<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">*/?>
  <meta name="keywords" content="<?=$keywords?>"/>
  <meta name="description" content="<?=$description?>"/>
  <title><?=$title?></title>

  <link href="favicon.ico" rel="icon" type="image/x-icon"/>
  <link href="favicon.ico" rel="shortcut icon" type="image/x-icon"/>

  <script src="/js/jquery-3.1.1.min.js"></script>

  <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link href="/css/fonts/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" rel="stylesheet" type="text/css" />
  <?/*<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">*/?>

  <link rel="stylesheet" href="/js/ui/jquery-ui.css" type="text/css">
  <script src="/js/ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="/js/ui/jquery-ui.datepicker-ru.js" type="text/javascript"></script>

  <script src="/js/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
  <link rel="stylesheet" href="/js/arcticmodal/jquery.arcticmodal-0.3.css">
  <link rel="stylesheet" href="/js/arcticmodal/themes/simple.css">

  <script src="/js/jquery.scrollUp.js"></script>
  <script src="/js/js-url-master/url.min.js"></script>

  <script type="text/javascript" src="/js/utils.js"></script>
  <script type="text/javascript" src="/js/spec.js?v=20180825"></script>

  <script type="text/javascript" src="/js/moment.min.js"></script>

  <link href="/js/blueimp-gallery/css/blueimp-gallery.css" rel="stylesheet" type="text/css"/>
  <link href="/js/blueimp-gallery/css/blueimp-gallery-indicator.css" rel="stylesheet">
  <link href="/js/blueimp-gallery/css/blueimp-gallery-video.css" rel="stylesheet">
  <script src="/js/blueimp-gallery/js/blueimp-gallery.js"></script>
  <script src="/js/blueimp-gallery/js/blueimp-gallery-indicator.js"></script>
  <script src="/js/blueimp-gallery/js/blueimp-gallery-video.js"></script>
  <script src="/js/blueimp-gallery/js/blueimp-gallery-youtube.js"></script>

  <link rel="stylesheet" href="/js/jAlert/jAlert.css" type="text/css" />
  <script type="text/javascript" src="/js/jAlert/jquery.jAlert.min.js"></script>

  <script src="/js/inputmask.min.js"></script>
  <script src="/js/inputmask.phone.extensions.min.js"></script>

  <script src="/js/jquery.cookie.js"></script>

  <link href="/css/style.css?v=20181101" rel="stylesheet" type="text/css" />

  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
  <div class="slides"></div>
  <h3 class="title"></h3>
  <a class="prev">‹</a>
  <a class="next">›</a>
  <a class="close"><i class="far fa-times-circle"></i></a>
  <a class="play-pause"></a>
  <ol class="indicator"></ol>
</div>

<header>
  <section class="fill-bg section-bottom-10">
    <div class="head">
      <div class="container-fluid">
        <div class="row">
          <div class="hb1 col-xs-3 col-sm-3 col-md-3">
            <div class="head-phone"><span><?=set('phone')?></span></div>
            <div class="head-address"><?=set('address')?></div>
          </div>
          <div class="hb2 col-xs-3 col-sm-3 col-md-3">
            <div class="head-wtime"><i class="far fa-clock"></i><?=set('wtime')?></div>
            <button type="button" class="btn btn-primary" onclick="scrollingTo($('#bron'), 0, 700);">Расписание сеансов <i class="fas fa-arrow-down"></i></button>
          </div>
          <div class="hb3 col-xs-3 col-sm-3 col-md-3">
            <button type="button" class="btn btn-warning">Записаться на сеанс</button>
            <div class="callme"><i class="fas fa-phone"></i><a href="" class="abtn">Перезвоните мне</a></div>
          </div>
          <div class="hb4 col-xs-3 col-sm-3 col-md-3">
            <div class="soc">
              <div class="h">Оставайтесь с нами:</div>
              <div class="lnk">
                <a href="#"><i class="fab fa-vk"></i></a>
                <a href="#"><i class="fab fa-odnoklassniki"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <a id="logo" class="pull-left" href="/" title="на главную"><img src="/img/logo.png"></a>
      <?=main()?>
      <div class="clearfix"></div>
    </div>
  </section>
  <? if($index){?>
  <div class="container-fluid">
    <h1 class="section-34">Добро пожаловать в соляную пещеру<br>в «Заведенском»</h1>
  </div>
  <section id="header-bg"></section>
  <section class="section-34 fill-bg">
    <div class="container-fluid">
	  <?=headerSlider()?>
  </section>
  <?}?>
</header>

<div class="content">
  <?=$content?>
</div>

<footer>
  <?=subscribe()?>
  <?=diseases()?>
</footer>

<iframe name="ajax" id="ajax"></iframe>

</body>
</html>