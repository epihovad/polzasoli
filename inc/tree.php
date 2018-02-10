<?
// ПОЛУЧЕНИЕ ДЕРЕВА
// $query = "SELECT * FROM {$prx}{$tbl} ORDER BY sort,id", ветка с которой начинаем стоить дерево, "глубина" дерева, текущая глубина - не задается, массив таблицы - не задается
function getTree($query, $id_parent=0, $depth=0, $level=0, &$rows=NULL)
{
	if(is_null($rows))
	{
		$rows = $tree = array();
		$res = sql($query);
		while($row = mysql_fetch_assoc($res))
			$rows[$row['id_parent']][] = $row;
	}	
	if(!$depth || $depth>$level)
		foreach((array)$rows[$id_parent] as $row)
		{
			$tree[] = array('level'=>$level, 'row'=>$row);
			$tree = array_merge($tree, (array)getTree('', $row['id'], $depth, $level+1, $rows));
		}
	return (array)$tree;
}
// префикс относительно уровня вложенности дерева
function getPrefix($level=0, $prefix="&raquo;&nbsp;") 
{
	$prefix = str_repeat("&mdash;&nbsp;",$level).$prefix;
	return $prefix;
}
// ВЫПАДАЮЩИЙ СПИСОК ДЛЯ ДЕРЕВА
// $sql = "SELECT * FROM {$prx}{$tbl} WHERE id_parent='%s'", 
// св-ва списка, 
// значение, 
// "пустое" значение(может быть массивом),  
// значение скрываемой рубрики (и ее подрубрик), 
// id начала веток, 
// глубина дерева, 
// свой префикс
function dllTree($sql, $properties, $value="", $default=NULL, $hidevalue="", $id_parent=0, $depth=0, $prefix=NULL)
{ 
	ob_start();
	?>
	<select <?=$properties?>>
	<?
	if(!is_null($default))
	{
		if(is_array($default)) 
		{
			foreach($default as $k=>$v)
			{
				?><option value="<?=htmlspecialchars($k)?>"><?=$v?></option><?
			}
		} 
		else 
		{ 
			?><option value=""><?=$default?></option><?	
		}
	}
	if($tree = getTree($sql, $id_parent, $depth))
	{
		foreach ($tree as $vetka) 
		{
			$row =  $vetka["row"];
			$level = $vetka["level"];
				
			// не выводим скрываемую рубрику и ее подрубрики
			if($row['id'] == $hidevalue)
			{
				$hide_pages_level = $level;
				continue;
			}
			if(isset($hide_pages_level) && $hide_pages_level < $level)
				continue;
			else
				unset($hide_pages_level);
			
			$prx = $prefix===NULL ? getPrefix($level) : str_repeat($prefix, $level);
			
			?><option value="<?=$row['id']?>"<?=($row['id']==$value ? " selected" : "")?>><?=$prx.$row["name"]?></option><?
        }
	}
	?>				
	</select>
	<? 	
	return ob_get_clean();
}
// КОЛ-ВО ПОДЧИНЕННЫХ ЭЛЕМЕНТОВ В ДЕРЕВЕ
function find_chaild($tbl,$id) 
{
	global $prx;
	
	return (int)getField("SELECT count(*) FROM {$prx}{$tbl} WHERE id_parent={$id}");
}
// массив родительских рубрик
function getArrParents($sql, $id, $parent_fill="id_parent") // $sql = "SELECT id,id_parent FROM {$prx}{$tbl} WHERE id='%s'"
{
	do
	{
		$row = getRow(sprintf($sql, $id));
		$tree[] = $row['id'];
		$id = $row[$parent_fill];
	}
	while($id);

	return (array)array_reverse($tree);
}
// возвращает id ветки и всех ее подветок
function getIdChilds($sql,$id=0,$arr=true) // $sql = "SELECT * FROM {$prx}{$tbl}", id ветки, возврещать в виде массива/строки
{
	$childs[] = $id;
	if($tree = getTree($sql, $id))
		foreach($tree as $vetka)
			$childs[] = $vetka['row']['id'];

	return $arr	? $childs : implode(',',$childs);
}
//
function getCatUrl($rubric,$echo=false,$prfx='catalog')
{
	global $prx;
	
	$href = $prfx ? "/{$prfx}/" : '';
	$str = $href;
	
	$ids = getArrParents("SELECT id,id_parent FROM {$prx}catalog WHERE id='%s'",$rubric['id']);
	foreach($ids as $id_catalog)
	{
		if($id_catalog==$rubric['id'])
		{
			$href .= $rubric['link'].'/';
			$str .= '<a href="'.$href.'" style="color:#090" target="_blank">'.$rubric['link'].'</a>/';
		}
		else
		{
			$link = gtv('catalog','link',$id_catalog);
			$href .= $link.'/';
			$str .= $link.'/';
		}
	}
	
	if($echo) echo $str; // для админки
	else return $href;
}
// КОЛИЧЕСТВО ВХОДЯЩИХ В РУБРИКУ ТОВАРОВ (К ПРИМЕРУ)
function getCountSub($id,$tbl1='catalog',$tbl2='goods',$parent='id_catalog') 
{
	global $prx;
	
	if(!$id) return '0';
	
	$ids = getIdChilds("SELECT id FROM {$prx}{$tbl1} WHERE id_parent='%s'",$id,false);
	return (int)getField("SELECT COUNT(*) FROM {$prx}{$tbl2} WHERE {$parent} IN ({$ids})");
}