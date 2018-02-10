<?
function setPriv($mail,$pass)
{
	global $prx;

	unset($_SESSION['user']);

	if($row = getRow("SELECT * FROM {$prx}users WHERE mail='{$mail}' AND pass=md5('{$pass}') AND status=1"))
		$_SESSION['user'] = $row;

	return isset($_SESSION['user']);
}

// МЕНЮ (ОСНОВНОЕ)
function main()
{
	global $prx, $mainID;

	$res = sql("SELECT * FROM {$prx}pages WHERE status=1 AND main=1 ORDER BY sort,id");
	if(!$count = @mysql_num_rows($res)) return;

	$url = $_SERVER['REQUEST_URI'];
	?><div id="main"><?
	while($row = mysql_fetch_assoc($res))
	{
		$link = $row['type']=='link' ? $row['link'] : ($row['link']=='/' ? '/' : "/{$row['link']}.htm");
		$cur = $row['id']==$mainID || ($url=='/' && $link=='/') ? true : false;

		?><a href="<?=$link?>" id="main-<?=$row['id']?>"<?=$cur?' class="active"':''?> style="width:<?=100/$count?>%"><?=$row['name']?></a><?

		/*><li id="main-<?=$row['id']?>"<?=$cur?' class="active"':''?> style="width:<?=100/$count?>%"><a href="<?=$link?>"><?=$row['name']?></a></li><?*/
	}
	?></div><?
}

function islider()
{
	global $prx;

	?><div id="islider"><?
	$r = sql("SELECT * FROM {$prx}slider WHERE status=1 ORDER BY sort,id");
	while($img = @mysql_fetch_assoc($r))
	{
		$id = $img['id'];
		if($img['link']){ ?><a href="<?=$img['link']?>"><img src="/slider/<?=$id?>.jpg"></a><? }
		else { ?><img src="/slider/<?=$id?>.jpg"><? }
	}
	?>
</div>
  <link type="text/css" href="/inc/advanced/jRotator/jRotator.css" rel="stylesheet"/>
  <script type="text/javascript" src="/inc/advanced/jRotator/jRotator.js"></script>
  <script>$(function(){$('#islider').jRotator({pAlign:'center',timeLineColor:'#ff7900'})});</script>
	<?
}

// СТРОКА НАВИГАЦИИ
function navigate()
{
	global $navigate;
	if(!$navigate) return;
	$sep = '<span>&rarr;</span>';
	?><div id="navigate"><div class="in"><a href="/">Главная</a><?=$sep?><?=$navigate?></div></div><?
}
//
function get_navigate()
{
	global $prx, $rubric, $good, $ids_parent_rubric;

	if(!$rubric && !$good)
	  return;

	$sep = '<span>&rarr;</span>';
	$nav = '';

  $ids_parent_rubric = (array)$ids_parent_rubric;
  if(!$ids_parent_rubric) return;

  $tbl = 'catalog';
  $link = "/{$tbl}/";
  $nav .= '<a href="/catalog/">Каталог</a>';

  foreach($ids_parent_rubric as $id_rubric)
  {
    $rb = $id_rubric==$rubric['id'] ? $rubric : gtv($tbl,'name,link',$id_rubric);
    $link .= ($id_rubric==$rubric['id'] ? $rb['link'] : gtv($tbl,'link',$id_rubric)).'/';
    if(!$good && $id_rubric==$rubric['id']){
      $nav .= $sep.$rb['name'];
    } else {
      $nav .= $sep.'<a href="'.$link.'">'.$rb['name'].'</a>';
    }
  }

  if($good) $nav .= ($nav?$sep:'').$good['name'];

  return $nav;
}

function catalog()
{
	global $prx, $rubric, $ids_parent_rubric;

	$ids_parent_rubric = (array)$ids_parent_rubric;

	$tree = getTree("SELECT * FROM {$prx}catalog WHERE status = 1 ORDER BY sort,id");
	if(!sizeof($tree)) return;

	?><div id="catalog"><?
	foreach ($tree as $branch){

		$lvl = $branch['level'];
	  $rb = $branch['row'];
		$id = $rb['id'];
    $active = in_array($id,$ids_parent_rubric) !== false;
    $cur = $id == $rubric['id'];

    ?><div class="lvl<?=$lvl+1?><?=$active?' active':''?><?=$cur?' cur':''?>"><a href="<?=getCatUrl($rb)?>"><?=wordwrap($rb['name'], 50, '<br>')?></a></div><?
  }
  ?></div><?
}

// СЧЕТЧИКИ (ФУТЕР)
function counters()
{
	global $prx;

	$res = sql("SELECT html FROM {$prx}counters WHERE status=1 ORDER BY sort,id");
	while($row = @mysql_fetch_assoc($res))
		echo "&nbsp;{$row['html']}&nbsp;";
}

// ПОЛЕ ДЛЯ ВВОДА КОЛ-ВА
function chQuant($name='quant',$quant=1)
{
	?>
	<div class="input-group">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant<=1?' disabled="disabled"':''?> data-type="minus">
				<span class="glyphicon glyphicon-minus"></span>
			</button>
		</span>
		<input type="text" name="<?=$name?>" class="form-control input-number" value="<?=$quant?>" min="1" max="99">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default btn-number"<?=$quant>=99?' disabled="disabled"':''?> data-type="plus">
				<span class="glyphicon glyphicon-plus"></span>
			</button>
		</span>
	</div>
	<?
}

// Страницы навигации
// show_navigate_pages(количество страниц,текущая,'ссылка = ?topic=news&page=')
function show_navigate_pages()
{
	global $count_obj,$count_obj_on_page,$kol_str,$cur_page,$dopURL;
	$x = $kol_str; $p = $cur_page;
	if($x<2) return;
	
	preg_match('/(&page=[0-9]+)/',$_SERVER['REQUEST_URI'],$h);
	$link = str_replace($h[1],'',$_SERVER['REQUEST_URI']);
	
	?><div id="navPages"><div class="pages"><?
	if($p!=1)
	{
		?><a class="bk4" href="<?=$link?>&page=<?=($p-1)?><?=$dopURL?>" title="предыдущая">Назад</a><?
	}  
	if($x<4)
	{
		for($i=1;$i<=$x;$i++)
		{
			if($i==$p) echo '<b class="bk4">'.$i.'</b>';
			else echo get_href($link,$i);
		}
	}
	if($x==4)
	{
		if($p==1) 		echo '<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 1
		if($p==2) 		echo get_href($link,1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 2
		if(($p-1)==2) echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$x);// 3
		if($p==$x) 		echo get_href($link,1).'<span>...</span>'.get_href($link,$x-1).'<b class="bk4">'.$p.'</b>';// 4
	}
	if($x>4)
	{
		if($p==1) 					echo '<b class="bk4">1</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 1
		elseif($p==2) 			echo get_href($link,1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 2
		elseif(($p-1)==2) 	echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);// 3
		elseif(($x-$p)==1) 	echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$x);// 4
		elseif($p==$x) 			echo get_href($link,1).'<span>...</span>'.get_href($link,$x-1).'<b class="bk4">'.$p.'</b>';// 5
		else								echo get_href($link,1).'<span>...</span>'.get_href($link,$p-1).'<b class="bk4">'.$p.'</b>'.get_href($link,$p+1).'<span>...</span>'.get_href($link,$x);
	}
	if($p<$x)
	{
		?><a class="bk4" href="<?=$link?>&page=<?=($p+1)?>" title="следующая">Вперед</a><?
	}	
	$start = $count_obj_on_page*$p-$count_obj_on_page;
	$end = $count_obj_on_page+$start;
  $end = $end>$count_obj?$count_obj:$end;
	?></div><div class="info">Показано с <?=$start+1?> по <?=$end?> из <?=$count_obj?> (<?=$x?> <?=num2str($x,'страница')?>)</div></div><?
}
function get_href($link,$page)
{
	global $dopURL;
	ob_start();
	?><a class="bk4" href="<?=$link?>&page=<?=$page?><?=$dopURL?>"><?=$page?></a><?
	return ob_get_clean();
}
//
function pagination($count_page, $cur_page)
{
  if($count_page < 2) return;

	preg_match('/(&page=[0-9]+)/',$_SERVER['REQUEST_URI'],$h);
	$link = str_replace($h[1],'',$_SERVER['REQUEST_URI']);

  ?>
  <ul class="pagination pagination-sm">
    <li<?=$cur_page==1?' class="disabled"':''?>><a href="<?=$link?>&page=<?=$cur_page>1?$cur_page-1:1?>">&laquo;</a></li>
		<?
		for ($page = 1; $page <= $count_page; $page++) {
			if ($page == $cur_page) {
				?><li class="active"><a href="<?=$link?>&page=<?=$page?>"><?=$page?> <span class="sr-only">(current)</span></a></li><?
			} else {
				?><li><a href="<?=$link?>&page=<?=$page?>"><?=$page?></a></li><?
			}
		}
		?>
    <li<?=$cur_page==$count_page?' class="disabled"':''?>><a href="<?=$link?>&page=<?=$cur_page<$count_page?$cur_page+1:$count_page?>">&raquo;</a></li>
  </ul>
  <?
}

function num2str($count,$txt='товар')
{
	$pat = array( 'товар'=>array('товар','товара','товаров'),
                'страница'=>array('страница','страницы','страниц')
  );
	
	$count = $count%100;
  if($count>19) $count = $count%10;
  switch($count)
	{
    case 1:  return($pat[$txt][0]);
    case 2: case 3: case 4:  return($pat[$txt][1]);
    default: return($pat[$txt][2]);
  }
}

// ВЫВОД ALERT ОБ ОШИБКЕ (и прерывание выполнения)
function jAlert($text,$method='',$type='',$func='',$prm='',$exit=true)
{
  global $jAlert_js;

	$method = $method ? $method : 'show';
	$type = $type ? $type : 'alert';
	$prm = $prm ? $prm : '{}';
	?>
  <script>
		top.jQuery(document).jAlert('<?=$method?>','<?=$type?>','<?=$text?>',function(){<?=$func?>},<?=$prm?>);
		top.jQuery('#ajax').attr('src','/inc/none.htm');
	  <?=$jAlert_js?>
  </script>
  <?
  if($exit) exit;
}