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
        ?><li><a class="btn btn-default<?=$cur?' active':''?>" href="<?=$link?>" role="button"><?=$row['name']?></a></li><?
      } else {
				?>
        <li>
          <div class="btn-group">
            <a class="btn btn-default dropdown-toggle" href="<?=$link?>" role="button" data-toggle="dropdown"><?=$row['name']?><span class="caret"></span></a>
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