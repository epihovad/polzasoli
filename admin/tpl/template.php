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
  <?/*
  <script src="/js/ui/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="css/ui/jquery-ui-1.8.1.custom.css" type="text/css">
  <script src="/js/ui/jquery.ui.datepicker-ru.js" type="text/javascript"></script>*/?>
  <script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
  <script src="/js/ckfinder/ckfinder.js" type="text/javascript"></script>

  <script src="/js/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
  <link rel="stylesheet" href="/js/arcticmodal/jquery.arcticmodal-0.3.css">
  <link rel="stylesheet" href="/js/arcticmodal/themes/simple.css">

  <script type="text/javascript" src="/js/utils.js"></script>
  <script type="text/javascript" src="js/spec.js"></script>
  <script type="text/javascript" src="js/ready.js"></script>

  <link rel="stylesheet" href="/js/jAlert/jAlert.css" type="text/css" />
  <script type="text/javascript" src="/js/jAlert/jquery.jAlert.min.js"></script>

  <link type="text/css" href="/js/tooltip/tooltip.css" rel="stylesheet">
  <script type="text/javascript" src="/js/tooltip/tooltip.js"></script>

  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery.css" type="text/css" />
  <link rel="stylesheet" href="/js/blueimp-gallery/blueimp-gallery-indicator.css">
  <script src="/js/blueimp-gallery/blueimp-gallery.js"></script>
  <script src="/js/blueimp-gallery/blueimp-gallery-indicator.js"></script>

	<? if ($f_context) { ?>
    <script type="text/javascript" src="/js/jquery.highlight.js"></script>
    <script>
      $(function () {
        $('.sp').highlight('<?=$f_context?>')
      });
    </script>
	<? } ?>

  <link rel="stylesheet" href="css/style.css" type="text/css" />
  <style>
    .blackout {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #000;
      opacity: 0.6;
      z-index: 100;
    }
  </style>
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
    <a href="#" data-original-title="" title="">
      MarkerCMS
      <span class="menu-toggle hidden-xs"><i class="fa fa-bars"></i></span>
    </a>
  </div>

</header>

<aside id="sidebar" style="left: 0px;">

	<?
	$tree = getTree("SELECT * FROM {$prx}am ORDER BY sort,id");
	if (sizeof($tree)) {
		?><div id="menu"><?
		$i = 0;
		$old_level = null;
	foreach ($tree as $vetka) {
		$item = $vetka['row'];
		$level = $vetka['level'];
		$id = $item['id'];

		$parents = getArrParents("SELECT id,id_parent FROM {$prx}am WHERE id='%s'", $id);
		$childs = getIdChilds("SELECT * FROM {$prx}am", $id);
		$has_childs = sizeof($childs) > 1;

	if(!$i || $level > $old_level) {
		if(!$level){
			$display = 'block';
		} else {
			$display = in_array($menu['id'], $parents) !== false ? 'block' : 'none';
		}
		?><ul style="display:<?=$display?>"><?
			}
			if($old_level !== null && $level < $old_level){ ?></li></ul><? }

	if(!$level){

		$class = '';
		if($has_childs) $class .= ' has-sub';
		//if(in_array($menu['id'], $parents) !== false) $class .= ' highlight';
		if(in_array($menu['id'], $childs) !== false) $class .= ' highlight active';

		?><li class="<?=$class?>"><a href="<?=$has_childs?'#':$item['link'].'.php'?>"><i class="<?=$item['im']?>"></i><span><?=$item['name']?></span></a><?
	} else {

	$class = '';
	if($id == $menu['id']) $class .= ' select';

	?><li><a class="<?=$class?>" href="<?=$item['link']?>.php"><span><?=$item['name']?></span></a><?
	}

		$old_level = $level;
		$i++;
	}
		?></li></ul></div><?
	}
	?>

</aside>

<div class="dashboard-wrapper">

  <? if($h1){ ?>
    <div class="top-bar">
      <div class="page-title"><?=$h1?></div>
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