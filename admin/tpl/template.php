<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <meta name="keywords" content="<?=$keywords?>" />
  <meta name="description" content="<?=$description?>" />
  <meta name="viewport" content="user-scalable=no,width=device-width" />
  <title><?=$title?></title>

  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

  <script src="/js/jquery-3.1.1.min.js"></script>

  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="css/fonts/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" type="text/css" />

  <link rel="stylesheet" href="/js/ui/jquery-ui.css" type="text/css">
  <script src="/js/ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="/js/ui/jquery-ui.datepicker-ru.js" type="text/javascript"></script>

  <script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
  <script src="/js/ckfinder/ckfinder.js" type="text/javascript"></script>

  <script src="/js/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
  <link rel="stylesheet" href="/js/arcticmodal/jquery.arcticmodal-0.3.css">
  <link rel="stylesheet" href="/js/arcticmodal/themes/simple.css">

  <link rel="stylesheet" href="/js/chosen/chosen.min.css">
  <script src="/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>

  <script src="/js/jquery.scrollUp.js"></script>
  <script src="/js/js-url-master/url.min.js"></script>

  <script type="text/javascript" src="/js/utils.js"></script>
  <script type="text/javascript" src="js/spec.js"></script>

  <link rel="stylesheet" href="/js/jAlert/jAlert.css" type="text/css" />
  <script type="text/javascript" src="/js/jAlert/jquery.jAlert.min.js"></script>

  <link type="text/css" href="/js/tooltip/tooltip.css" rel="stylesheet">
  <script type="text/javascript" src="/js/tooltip/tooltip.js"></script>

  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery.css" type="text/css" />
  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery-indicator.css">
  <script src="/js/blueimp-gallery/blueimp-gallery.js"></script>
  <script src="/js/blueimp-gallery/blueimp-gallery-indicator.js"></script>

	<? if ($fl['search']) { ?>
    <script type="text/javascript" src="/js/jquery.highlight.js"></script>
    <script>
      $(function () {
        $('.sp').highlight('<?=$fl['search']?>')
      });
    </script>
	<? } ?>

  <link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
  <div class="slides"></div>
  <h3 class="title"></h3>
  <a class="prev">‹</a>
  <a class="next">›</a>
  <a class="close">×</a>
  <a class="play-pause"></a>
  <ol class="indicator"></ol>
</div>

<header>

  <div class="logo">
    <a href="/" title="<?=$_SERVER['SERVER_NAME']?>" target="_blank">
      MarkerCMS
      <span class="menu-toggle hidden-xs"><i class="fa fa-bars"></i></span>
      <div><?=$_SERVER['SERVER_NAME']?></div>
    </a>
  </div>

</header>

<aside id="sidebar" style="left: 0px;">
	<?=menu()?>
</aside>

<div class="dashboard-wrapper">

	<? if($h1){ ?>
    <div class="top-bar">
      <div class="page-title"><?=$h1?></div>
      <div class="clearfix"></div>
      <div class="navigate"><a href="/admin/">Главная</a><?=$navigate?></div>
    </div>
	<?}?>

  <div class="main-container">
    <div class="spacer">
      <?=$content?>
    </div>
  </div>

  <footer>
    Copyright Everest Admin Panel 2014.
  </footer>

</div>

<iframe name="ajax" id="ajax"></iframe>
<input type="hidden" id="script" value="<?=$script?>">

</body>
</html>