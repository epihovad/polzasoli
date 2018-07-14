<?
// МЕНЮ (ОСНОВНОЕ)
function main()
{
	global $prx, $mainID;

	$mas = getTree("SELECT * FROM {$prx}pages WHERE status=1 AND is_main=1 ORDER BY sort,id");
	if(!sizeof($mas)) return;

  ?><ul id="menu" class="pull-right"><?

  $old_lvl = $lvl = 0;
  foreach($mas as $vetka){
    $row = $vetka['row'];
    $lvl = $vetka['level'];

    $id = $row['id'];
    $link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
		$childs = getIdChilds("SELECT * FROM {$prx}pages WHERE status=1 AND is_main=1", $id);
		$has_childs = sizeof($childs) > 1;
		$cur = $row['id'] == $mainID || ($_SERVER['REQUEST_URI'] == '/' && $link == '/') ? true : false;

		if($old_lvl && !$lvl){
			?></ul></div></li><?
    }

    if(!$lvl){
      if(!$has_childs){
        ?><li><a class="btn btn-default<?=$cur?' active':''?>" data-target="#" href="<?=$link?>" role="button"><?=$row['name']?></a></li><?
      } else {
				?>
        <li>
          <div class="btn-group">
            <a class="btn btn-default dropdown-toggle" data-target="#" href="<?=$link?>" role="button"><?=$row['name']?><span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
				<?
      }
    } else {
      ?><li><a href="<?=$link?>"><?=$row['name']?></a></li><?
    }
		$old_lvl = $lvl;
  }

  if($lvl){
    ?></ul></div></li><?
  }
  ?></ul><?
}

//
function headerSlider()
{
  global $prx, $index;

  if(!$index) return;

  $res = sql("SELECT * FROM {$prx}pages WHERE is_slider = 1 AND status = 1 ORDER BY sort, id");
  if(!mysqli_num_rows($res)) return;

  ?>
  <div id="header-slider">
    <h3>Как соляная пещера помогает людям</h3>
    <?
    $i = 0;
    $btns = $info = '';
    while($row = mysqli_fetch_assoc($res)){

      $id = $row['id'];
			$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");

      ob_start();
      ?><button class="btn btn-default<?=!$i?' active':''?>"><?=$row['name']?></button><?
      $btns .= ob_get_clean();

			ob_start();
			?>
      <div class="item<?=!$i?' active':''?>">
        <h4><?=$row['name']?></h4>
        <a href="<?=$link?>" class="pull-left"><img src="/pages/360x190/<?=$id?>.jpg" alt="<?=htmlspecialchars($row['name'])?>" title="<?=htmlspecialchars($row['title']?$row['title']:$row['name'])?>"></a>
        <div class="preview pull-right"><?=$row['preview']?></div>
        <div class="clearfix"></div>
        <a class="btn btn-warning" data-target="#" href="<?=$link?>" role="button">Узнать больше<i class="fas fa-arrow-right"></i></a>
      </div>
      <?
			$info .= ob_get_clean();
			$i++;
    }
    ?>
    <div class="btns"><?=$btns?></div>
    <div class="btns-preview">
      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
					<?=$info?>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(function () {
      $('.carousel').carousel();
      $('#header-slider .btns .btn').click(function () {
        if($(this).hasClass('active')){
          return false;
        }
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        var ind = $(this).index();
        $('.carousel').carousel(ind);
      });
      //
      $('.carousel').on('slid.bs.carousel', function () {
        var ind = $(this).find('.item.active').index();
        $('#header-slider .btns .btn').removeClass('active');
        $('#header-slider .btns .btn').eq(ind).addClass('active');
      })
    })
  </script>
  <?
}

// ПОЛЕ ДЛЯ ВВОДА КОЛ-ВА
function chQuant($name = 'quant', $quant = 1, $min = 1, $max = 99)
{
	?>
  <div class="input-group">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant<=$min?' disabled="disabled"':''?> data-type="minus">
				<span class="glyphicon glyphicon-minus"></span>
			</button>
		</span>
    <input type="text" name="<?=$name?>" class="form-control input-number" value="<?=$quant?>" min="<?=$min?>" max="<?=$max?>">
    <span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant>=$max?' disabled="disabled"':''?> data-type="plus">
				<span class="glyphicon glyphicon-plus"></span>
			</button>
		</span>
  </div>
	<?
}

// Мини-биннер
function banner_mini(){
  global $prx;

  if($_COOKIE['bnr-mini'] == 'hide'){
    return;
  }

  if(!$bnr = getRow("SELECT * FROM {$prx}banner_mini ORDER BY is_through DESC, RAND() LIMIT 1")){
    return;
  }

  ?>
  <style>
    #bnr-mini { background-color:#434350; color:#fff;}
    #bnr-mini .container-fluid { position:relative;}
    #bnr-mini i.hdn { color:#8d8da7; font-size:20px; position:absolute; right:7px; top:7px; cursor:pointer;}
    #bnr-mini i.hdn:hover { color:#FFFE4E;}
    #bnr-mini .ar { width:70%; margin:0 auto; text-align:left; padding:20px 0;}
    #bnr-mini img { float:left; margin-right:25px;}
    #bnr-mini h4 { margin:0 0 5px; }
    #bnr-mini .btn { margin-top:20px;}
  </style>
  <script>
    $(function () {
      //$.removeCookie('bnr-mini');
      $('#bnr-mini i.hdn').click(function () {
        $('#bnr-mini').slideUp(300,function () {
          $(this).remove();
        });
        $.cookie('bnr-mini', 'hide', { expires: 1 });
      });
    })
  </script>
  <div id="bnr-mini">
    <div class="container-fluid">
      <i class="fas fa-times hdn"></i>
      <div class="ar">
        <img src="/banner_mini/1.jpg">
        <h4><?=$bnr['name']?></h4>
        <div><?=$bnr['text']?></div>
        <a class="btn btn-warning btn-sm" data-target="#" href="<?=$bnr['link']?>" role="button">Узнать больше<i class="fas fa-arrow-right"></i></a>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  <?
}

// Подписка
function subscribe() {
	?>
  <div id="subscribe">
    <div class="container-fluid">

      <h3>Получайте советы по улучшению состояния<br>вашего здоровья и схемы дыхательных гимнастик<br><b>абсолютно бесплатно на ваш E-mail прямо сейчас</b></h3>

      <div class="frm">
        <input type="text" name="email" class="form-control" placeholder="Введите Ваш Email"><i class="fab fa-telegram-plane"></i>
      </div>

      <?=bmain()?>

    </div>
  </div>
	<?
}

function bmain(){
  global $prx, $mainID;

  $res = sql("SELECT * FROM {$prx}pages WHERE is_bmain = 1 AND `status` = 1 ORDER BY sort, id");
  $cnt = mysqli_num_rows($res);
  $col = array(array(),array());
  $cnt_in_col  = ceil($cnt/2);
  $i=1;
  while ($row = @mysqli_fetch_assoc($res)){
		$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
		$cur = $row['id'] == $mainID || ($_SERVER['REQUEST_URI'] == '/' && $link == '/') ? true : false;
    ob_start();
    ?><div><a href="<?=$link?>" class="<?=$cur?'active':''?>"><?=$row['name']?></a></div><?
    $col[$i++ <= $cnt_in_col ? 0 : 1][] = ob_get_clean();
  }
  ?>

  <div id="bmain" class="row">
    <div class="copy col-xs-3 col-sm-3 col-md-3">
      © 2016 Соляная пещера «Ассоль»
    </div>
    <div class="col2 col-xs-3 col-sm-3 col-md-3">
      <? foreach ($col[0] as $item) { echo $item; }?>
    </div>
    <div class="col3 col-xs-3 col-sm-3 col-md-3">
			<? foreach ($col[1] as $item) { echo $item; }?>
    </div>
    <div class="soc col-xs-3 col-sm-3 col-md-3">
      <a href="#"><i class="fab fa-vk"></i></a>
      <a href="#"><i class="fab fa-odnoklassniki"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
  <?
}

// Спр-к болезней
function diseases(){
  global $prx;


}










