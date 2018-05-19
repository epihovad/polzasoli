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

			$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");

      ob_start();
      ?><button class="btn btn-default<?=!$i?' active':''?>"><?=$row['name']?></button><?
      $btns .= ob_get_clean();

			ob_start();
			?>
      <div class="item<?=!$i?' active':''?>">
        <h4><?=$row['name']?></h4>
        <a href="" class="pull-left"><img src="/pages/360x190/10.jpg" alt="..."></a>
        <div class="preview pull-right">222<?=$row['preview']?></div>
        <div class="clearfix"></div>
        <a class="btn btn-warning" data-target="#" href="<?=$link?>" role="button">Узнать больше<i class="fas fa-arrow-right"></i></a>
      </div>
      <?
			/*?>
      <div class="item<?=!$i?' active':''?>">
        <img src="/pages/10.jpg">
      </div>
			<?*/
			$info .= ob_get_clean();
//break;
			$i++;
    }
    ?>
    <div class="btns"><?=$btns?></div>
    <div class="btns-info">
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
    })
  </script>
  <?
}
















