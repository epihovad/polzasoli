<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <meta name="keywords" content="<?=$keywords?>" />
  <meta name="description" content="<?=$description?>" />
  <meta name="viewport" content="user-scalable=no,width=device-width" />
  <title><?=$title?></title>

  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css"/>
  <link rel="stylesheet" href="css/style.css" type="text/css" />

  <script src="/js/jquery-3.1.1.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>

  <script src="/js/ui/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="css/ui/jquery-ui-1.8.1.custom.css" type="text/css">
  <script src="/js/ui/jquery.ui.datepicker-ru.js" type="text/javascript"></script>
  <script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
  <script src="/js/ckfinder/ckfinder.js" type="text/javascript"></script>

  <script src="/js/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
  <link rel="stylesheet" href="/js/arcticmodal/jquery.arcticmodal-0.3.css">
  <link rel="stylesheet" href="/js/arcticmodal/themes/simple.css">

  <script type="text/javascript" src="/js/utils.js"></script>
  <script type="text/javascript" src="js/spec.js"></script>
  <script type="text/javascript" src="js/ready.js"></script>

  <script type="text/javascript" src="/js/jquery.mousewheel.min.js"></script>
  <script type="text/javascript" src="/js/jB/jquery.jB.js"></script>
  <link rel="stylesheet" href="/js/jAlert/jAlert.css" type="text/css" />
  <script type="text/javascript" src="/js/jAlert/jquery.jAlert.js"></script>

  <link type="text/css" href="/js/tooltip/tooltip.css" rel="stylesheet">
  <script type="text/javascript" src="/js/tooltip/tooltip.js"></script>

  <script type="text/javascript" src="/js/highslide/highslide-with-gallery.js"></script>
  <link type="text/css" href="/js/highslide/highslide.css" rel="stylesheet"/>

	<? if ($f_context) { ?>
    <script type="text/javascript" src="/js/jquery.highlight.js"></script>
    <script>
      $(function () {
        $('.sp').highlight('<?=$f_context?>')
      });
    </script>
	<? } ?>

</head>
<body>

<div class="Around">
  <div class="AroundRow">

    <div class="Lcol">
      <div class="lfix">
        <div id="logo"><a href="/"><img src="/img/logo-mini.png" alt="">IRIDA</a></div>
        <?
        $tree = getTree("SELECT * FROM {$prx}am ORDER BY sort,id");
				if (sizeof($tree)) {
					?><div id="menu"><?
					$i = 0;
					$old_level = null;
					foreach ($tree as $vetka) {
						$item = $vetka['row'];
						$level = $vetka['level'];
						$id = $row['id'];

            $active = $item['id'] == 55 || $item['id_parent'] == 55 ? 1 : 0;
						if(!$i || $level > $old_level) { ?><ul><? }

						if($old_level !== null && $level < $old_level){
              ?></li></ul><?
            }

            ?><li class="<?=$active?'has-sub highlight active':''?>"><a href="<?=$item['link']?>.php"><i></i><span><?=$item['name']?></span></a><?

            $old_level = $level;
            $i++;
					}
          ?></li></ul></div><?
				}
        ?>
        <div class="sz" style="padding-top:50px; text-align:center; color:#fff;"></div>
      </div>
    </div>

    <div class="Center">
      <div class="Header">
        <div class="inHeader">
        </div>
      </div>
      <? if($h1){ ?>
        <div class="Middle-Head">
          <h1><?=$h1?></h1>
          <div class="nav"><a href="/">Главная</a><span></span><?=$navigate?></div>
        </div>
      <?}?>
      <div class="Middle"><?=$content?></div>
    </div>
  </div>

</div>

<div class="Footer">
  <div class="inFooter">
    Футер
  </div>
</div>

<iframe name="ajax" id="ajax"></iframe>
<input type="hidden" id="script" value="<?=$script?>">

</body>
</html>