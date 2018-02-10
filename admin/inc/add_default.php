<?
require('common.php');

if(isset($_GET['show']))
{
	$ids = explode(',',@$_GET['ids']);
	
	ob_start();
	
	switch($_GET['show'])
	{
		// ---------------------- ТОВАРЫ
		case 'goods':
			$list = clean($_GET['list']);
			$ids = clean($_GET['ids']);
			$tbl = 'goods';	
			
			$cur_page = (int)$_GET['p'];
				$cur_page = $cur_page ? $cur_page : 1;
			$f_catalog = (int)$_GET['catalog'];
			$f_context = stripslashes(trim(preg_replace("/\s+/u",' ',$_GET['search'])));
				
			$where = '';
			if($f_catalog)
			{
				$ids_catalog = getIdChilds("SELECT id FROM {$prx}catalog WHERE id_parent='%s'",$f_catalog,false);
				if($ids_good = getArr("SELECT id FROM {$prx}{$tbl} WHERE id_catalog IN ({$ids_catalog})"))
					$where .= " AND id IN (".implode(',',$ids_good).")";
				else
					$where .= " AND id IN (0)";
			}
			if($f_context)	$where .= " AND ( `code` LIKE '%{$f_context}%' OR
																				articul LIKE '%{$f_context}%' OR
																				name LIKE '%{$f_context}%' )";
			
			$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";
			
			$count_obj = getField(str_replace('*','COUNT(*)',$query)); // кол-во объектов в базе
			$count_obj_on_page = 30; // кол-во объектов на странице
			$kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц
			
			$query .= ' ORDER BY name LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;
			
			ob_start();
			//echo $query;
			?>
			<link rel="stylesheet" href="../css/pop.css" type="text/css">
			<script src="../js/pop.js" type="text/javascript"></script>
			<?
			if($f_context)
			{
				?>
				<script type="text/javascript" src="/js/jquery.highlight.js"></script>
				<script>$(function(){ $('.sp').highlight('<?=$f_context?>') });</script>
				<?
			}
			?>
			
			<form id="frm" method="get" style="margin-bottom:5px;">
      <input type="hidden" name="show" value="goods" />
      <input type="hidden" id="list" name="list" value="<?=$list?>" /><input type="hidden" name="ids" value="<?=$ids?>" />
			<table id="filters">
				<tr>
					<td align="left">Рубрика</td>
					<td colspan="2">
						<?
						if($rash) { ?><input type="hidden" name="catalog" value="<?=$f_catalog?>" /><b><?=gtv('catalog','name',$f_catalog)?></b><? }
						else
							echo dllTree("SELECT id,name FROM {$prx}catalog WHERE id_parent='%s' ORDER BY sort,id",'name="catalog" style="width:100%" onChange="this.form.submit();return false;"',$f_catalog,array(''=>'-- не важно --'));
						?>	
          </td>
				</tr>
        <? if($rash){ ?>
        <tr>
          <td>Выбрано товаров</td>
          <td colspan="2"><input type="hidden" name="ids" value="<?=$ids?>" /><b><?=$ids?sizeof(explode(',',$ids)):0?></b></td>
        </tr>
        <? } ?>
				<tr>
					<td>Контекстный поиск</td>
					<td><input type="text" name="search" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
					<td><a href="javascript:$('#frm').submit()" class="link">найти</a></td>
				</tr>
			</table>
			</form>
			 
			<?
			$script = "?show=goods&p=%s&list={$list}&ids={$ids}&search={$f_context}&catalog=".$f_catalog;
			$session = false;
			show_navigate_pages($kol_str,$cur_page,$script);
			
			$ids = explode(',',$ids);
			?>  
			<table id="tab">
				<tr>
					<th width="20"></th>
					<th>Артикул</th>
					<th>Название</th>
				</tr>
				<?
				$res = mysql_query($query);
				if(@mysql_num_rows($res))
				{
					while($row = mysql_fetch_assoc($res))
					{
						?>
						<tr>
							<th><input type="checkbox" id="<?=$row['id']?>" value="<?=htmlspecialchars($row['articul'])?> - <?=htmlspecialchars($row['name'])?>"<?=in_array($row['id'],$ids)?' checked':''?>></th>
							<td nowrap align="center" class="sp"><?=$row['articul']?></td>
							<td align="left" class="sp" width="100%"><?=$row['name']?></td>
						</tr>
						<?
					}
				}
				else
				{
					?><tr><td colspan="10" align="center">товары не найдены</td></tr><?
				}
				?>
			</table>
			<?
			if(!$rash)
			{
				?>
				<div align="center" style="padding-top:10px;">
					<input type="button" value="добавить" class="but1">
					<input type="button" value="отмена" class="but1">
				</div>
				<?
			}
			break;
		// ---------------------- СПРАВОЧНИК ХАРАКТЕРИСТИК -----------------------------
		case 'users':
			$who = (int)$_GET['who'];
			$mails = clean($_GET['mails']);

			$cur_page = (int)$_GET['p'];
			if(!$cur_page) $cur_page = 1;
			$f_context = stripslashes(trim(preg_replace("/\s+/u",' ',$_GET['search'])));
			
			$where = '';
			if($who)				$where .= " AND id_users_gr='{$who}'";
			if($f_context)	$where .= " AND ( name LIKE '%{$f_context}%' OR org LIKE '%{$f_context}%' OR mail LIKE '%{$f_context}%')";
			
			$query = "SELECT id,name,org,mail FROM {$prx}users WHERE 1{$where}";
			
			$count_obj = getField(str_replace('*','COUNT(*)',$query)); // кол-во объектов в базе
			$count_obj_on_page = 30; // кол-во объектов на странице
			$kol_str = ceil($count_obj/$count_obj_on_page); // количество страниц
			
			$query .= ' ORDER BY name LIMIT '.($count_obj_on_page*$cur_page-$count_obj_on_page).",".$count_obj_on_page;

			?>
			<link rel="stylesheet" href="../css/pop.css" type="text/css">
      <script src="../js/pop.js" type="text/javascript"></script>
      <?
			if($f_context)
			{
				?>
				<script type="text/javascript" src="/js/jquery.highlight.js"></script>
				<script>$(function(){ $('.sp').highlight('<?=$f_context?>') });</script>
				<?
			}
			?>
      <form id="frm" style="margin-bottom:5px;">
      <input type="hidden" name="show" value="users" />
      <input type="hidden" id="list" name="list" value="mails" />
      <input type="hidden" name="mails" value="<?=$mails?>" />
      <input type="hidden" name="who" value="<?=$who?>" />
      <table id="filters">
        <tr>
          <td>Контекстный поиск</td>
          <td><input type="text" name="search" value="<?=htmlspecialchars($f_context)?>" style="width:200px;"></td>
          <td><a href="javascript:$('#frm').submit()" class="link">найти</a></td>
        </tr>
      </table>
      </form>
      
      <?
			$script = "add.php?show=users&p=%s&who={$who}&mails={$mails}&search={$f_context}";
			$session = false;
			show_navigate_pages($kol_str,$cur_page,$script);
			
			$mails = explode(',',$mails);
			?>
      <table id="tab">
        <tr>
        	<th></th>
          <th>№</th>
          <th style="width:70%">Организация/Имя</th>
          <th style="width:30%">e-mail</th>
        </tr>
      <?
			$res = mysql_query($query);
			if(@mysql_num_rows($res))
			{
				$i=1;
				while($row = mysql_fetch_assoc($res))
				{
					?>
          <tr>
          	<th><input type="checkbox" id="<?=$row['mail']?>" value="<?=$row['mail']?>"<?=in_array($row['mail'],$mails)?' checked':''?>></th>
            <th align="center"><?=$i++?></th>
            <td><?=$row['ut']=='ur'?$row['org']:$row['name']?></td>
            <td><?=$row['mail']?></td>
          </tr>
          <?
        }
				?>	
        </table>			
				<div align="center" style="margin-top:10px;">
          <input type="button" value="добавить" class="but1">
          <input type="button" value="отмена" class="but1">
				</div>
				<?
			}
			else
			{
				?>
          <tr>
            <th></th>
            <td align="center" colspan="3">пользователи не найдены</td>
          </tr>
        </table>
				<?
			}
			break;
		// ---------------------- СПРАВОЧНИК ХАРАКТЕРИСТИК -----------------------------
		case 'classifer':
			?>
			<link rel="stylesheet" href="../css/pop.css" type="text/css">
      <script src="../js/pop.js" type="text/javascript"></script>
			<input type="hidden" id="list" value="classifer" />
      <table id="tab">
        <tr>
        	<th></th>
          <th>№</th>
          <th style="width:30%">Характеристика</th>
          <th style="width:70%">Описание</th>
        </tr>
      <?
			$res = mysql_query("SELECT * FROM {$prx}classifer ORDER BY sort,id");
			if(@mysql_num_rows($res))
			{
				$i=1;
				while($row = mysql_fetch_assoc($res))
				{
					?>
          <tr>
          	<th><input type="checkbox" id="<?=$row['id']?>" value="<?=$row['name']?>"<?=in_array($row['id'],$ids)?' checked':''?>></th>
            <th align="center"><?=$i++?></th>
            <td><?=$row['name']?></td>
            <td><?=$row['about']?></td>
          </tr>
          <?
        }
				?>	
        </table>			
				<div align="center" style="margin-top:10px;">
				<input type="button" value="добавить" class="but1">
				<input type="button" value="отмена" class="but1">
				</div>
				<?
			}
			else
			{
				?>
        <tr>
        	<th></th>
          <td align="center" colspan="3">справочник характеристик пуст</td>
        </tr>
        </table>
				<?
			}
			break;
		// ---------------------- РУБРИКИ -----------------------------
		case 'rubrics':	
			if(!$tbl = clean($_GET['tbl'])) $tbl = 'catalog';
			if(!$list = clean($_GET['list'])) $list = 'ids_rubrics';
			
			$opened = array();
			foreach($ids as $id)
			{
				$parents = getArrParents("SELECT id,id_parent FROM {$prx}{$tbl} WHERE id='%s'",$id);
				foreach($parents as $id_parent)
					if(!in_array($id_parent,$opened))
						$opened[] = $id_parent;
			}
			
			$mas = getTree("SELECT * FROM {$prx}{$tbl} WHERE id_parent='%s' ORDER BY sort,id");
			if(sizeof($mas))
			{
				?>
				<link rel="stylesheet" href="../css/pop.css" type="text/css">
				<script src="../js/pop.js" type="text/javascript"></script>
        <script src="../js/catalog.js" type="text/javascript"></script>
				<script>
				$(function(){
					<? foreach($opened as $id){ ?>
						$('#cat_<?=$id?>').show();
						$('#znak_<?=$id?> img').attr('src','../img/cat_opened.gif');
						$('#folder_<?=$id?> img').attr('src','../img/cat_folder_open1.gif');
					<? } ?>					
				});
				</script>
				<style>.cat_name { font:normal 12px Arial, Helvetica, sans-serif; }</style>  
				<input type="hidden" id="list" value="<?=$list?>" />
				
				<div align="center" style="padding:10px 0 10px 0px;">
				<a href="" class="cat_open" style="color:#697079;">Развернуть каталог</a>&nbsp;|&nbsp;<a href="" class="cat_close" style="color:#697079;">Свернуть каталог</a>
				</div>
				
				<?
				$old_lvl = 0;
				foreach($mas as $vetka)
				{
					$lvl = $vetka['level'];
					$cat_id = $vetka['row']['id'];
					$cat_id_parent = $vetka['row']['id_parent'];
					$cat_name = $vetka['row']['name'];
					
					$otstup = $lvl>0 ? "padding-left:".($lvl*20)."px;" : "";
					ins_div($lvl,$old_lvl,$cat_id_parent,$cat_id,$cur_id);
					$get_chaild = find_chaild($tbl,$cat_id);
					
					$checked = in_array($cat_id,$ids) ? ' checked' : '';
					
					?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:3px 0 3px 0;">
						<tr>
							<td width="10"><input type="checkbox" id="<?=$cat_id?>" value="<?=$cat_name?>"<?=$checked?>></td>
							<td width="20" align="center" style="<?=$otstup?>">
								<?
								if($get_chaild)
								{
									?><span id="znak_<?=$cat_id?>" class="rubric_znak"><img src="../img/cat_closed.gif" align="absmiddle" /></span><?
								}
								?>
							</td>			
							<td width="20" align="center">
								<span id="folder_<?=$cat_id?>"class="rubric_znak"><img src="../img/cat_folder_close1.gif" align="absmiddle" /></span>
							</td>
							<td align="left" class="cat_name" style="padding-left:5px;"><?=$cat_name?></td>
						</tr>
					</table>
					<?
					
					$old_lvl = $lvl;
				}
				ins_div(0,$old_lvl,$cat_id_parent,$cat_id,$cur_id);
				?>
				
				<div align="center" style="margin-top:10px;">
				<input type="button" value="добавить" class="but1">
				<input type="button" value="отмена" class="but1">
				</div>
				<?
			}
			else
			{
				?><center>рубрики не найдены</center><?
			}
			break;
	}
	
	$content = ob_get_clean();
	require('../tpl/tpl_popup.php');
}
?>