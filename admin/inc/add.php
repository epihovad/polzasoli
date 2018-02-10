<?
require('common.php');

if(isset($_GET['show']))
{
	$ids = explode(',',@$_GET['ids']);
	
	ob_start();
	
	switch($_GET['show'])
	{
		// ---------------------- ИКОНКИ
		case 'icons':
			?>
			<link rel="stylesheet" href="../css/pop.css" type="text/css">
      <script src="../js/roma.js" type="text/javascript"></script>
			<input type="hidden" id="list" value="icons" />
      <table id="tab">
        <tr>
        	<th></th>
          <th>№</th>
          <th></th>
          <th style="width:70%">Описание</th>
          <th style="width:30%">Тип</th>
        </tr>
      <?
			$tbl = 'ico';
			$res = sql("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id");
			if(@mysql_num_rows($res))
			{
				$i=1;
				while($row = mysql_fetch_assoc($res))
				{
					$id = $row['id'];
					?>
          <tr>
          	<th><input type="checkbox" id="<?=$id?>" value="<?=str_replace("\r\n",' ',$row['info'])?>"<?=in_array($id,$ids)?' checked':''?>></th>
            <th align="center"><?=$i++?></th>
            <th align="center">
							<? if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg")){ ?>
              <img src="/<?=$tbl?>/<?=$id?>.jpg">
              <? }?>
            </th>
            <td><?=$row['info']?></td>
            <td align="center"><?=$row['type']?></td>
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
		// ---------------------- РУБРИКИ
		case 'catalog':	
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
			
			$mas = getTree("SELECT * FROM {$prx}{$tbl} ORDER BY sort,id");
			if(sizeof($mas))
			{
				?>
				<link rel="stylesheet" href="../css/pop.css" type="text/css">
				<script src="../js/roma.js" type="text/javascript"></script>
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