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














