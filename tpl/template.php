<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?/*<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">*/?>
  <meta name="keywords" content="<?=$keywords?>"/>
  <meta name="description" content="<?=$description?>"/>
  <title><?=$title?></title>

  <link rel="icon" href="favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

  <script src="/js/jquery-3.1.1.min.js"></script>

  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="/css/fonts/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" type="text/css" />

  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery.css" type="text/css"/>
  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery-indicator.css">
  <script src="/js/blueimp-gallery/blueimp-gallery.js"></script>
  <script src="/js/blueimp-gallery/blueimp-gallery-indicator.js"></script>

  <link rel="stylesheet" href="/css/style.css" type="text/css" />

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
  <a class="close">?</a>
  <a class="play-pause"></a>
  <ol class="indicator"></ol>
</div>

<header<?=$index?' class="index"':''?>>
  <div class="head">
    <div class="container-fluid">
      <div class="row">
        <div class="hb1 col-xs-3 col-sm-3 col-md-3">
          <div class="head-phone"><?=set('phone')?></div>
          <div class="head-address"><?=set('address')?></div>
        </div>
        <div class="hb2 col-xs-3 col-sm-3 col-md-3">
          <div class="head-wtime"><i class="far fa-clock"></i><?=set('wtime')?></div>
          <button type="button" class="btn btn-primary">Расписание сеансов <i class="fas fa-arrow-down"></i></button>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <a id="logo" class="pull-left" href="/" title="на главную"><img src="/img/logo.png"></a>
    <ul id="menu" class="pull-right">
      <li><a href="">1</a></li>
      <li><a href="">2</a></li>
      <li><a href="">3</a></li>
    </ul>
  </div>
</header>

<div class="content">
  <?=$content?>
</div>

<footer>
  <div class="container-fluid">
    Place sticky footer content here
  </div>
</footer>

</body>
</html>