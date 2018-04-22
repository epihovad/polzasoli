<?
require('inc/common.php');

$rubric = 'Вопросы-Ответы';
$tbl = 'faq';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];
	
	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $key=>$val)
				$$key = clean($val);
			
			if(!$question) errorAlert('необходимо указать вопрос !');
			if(!$answer) errorAlert('необходимо указать ответ !');
			
			$set = "question='{$question}',
							answer='{$answer}',
							status='{$status}'";
				
			if(!$id = update($tbl,$set,$id))
				errorAlert('Во время сохранения данных произошла ошибка.');

			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?		
			break;
		// ----------------- обновление в меню
		case 'status':
			update_flag($tbl,$_GET['action'],$id);
			break;
		// ----------------- сортировка вверх
		case 'moveup':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_moveup($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- сортировка вниз
		case 'movedown':
			$id_parent = gtv($tbl,'id_parent',$id);
			sort_movedown($tbl,$id,"id_parent='{$id_parent}'");
			?><script>top.location.href = '<?=$script?>?id=<?=$id?>'</script><?
			break;
		// ----------------- удаление одной записи
		case 'del':
			update($tbl,'',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v)
				update($tbl,'',$id);
			?><script>top.location.href = '<?=$script?>'</script><?
			break;
	}
	exit;
}
// ------------------ РЕДАКТИРОВАНИЕ --------------------
elseif(isset($_GET['red']))
{
	$id = (int)$_GET['red'];
	
	$rubric .= ' &raquo; '.($id ? 'Редактирование' : 'Добавление');
	$page_title .= ' :: '.$rubric;
	
	$row = gtv($tbl,'*',$id);
	
	ob_start();
	?>
  <form action="?action=save&id=<?=$id?>" method="post" enctype="multipart/form-data" target="ajax">
  <table width="100%" border="0" cellspacing="0" cellpadding="5" class="tab_red">
    <tr>
      <th class="tab_red_th"></th>
      <th>Вопрос</th>
      <td><?=show_pole('text','question',htmlspecialchars($row['question']))?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Ответ</th>
      <td><?=showFck('answer',$row['answer'],'medium','100%',20)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th>Статус</th>
      <td><?=dll(array('0'=>'заблокировано','1'=>'активно'),'name="status"',isset($row['status'])?$row['status']:1)?></td>
    </tr>
    <tr>
      <th class="tab_red_th"></th>
      <th></th>
      <td align="center">
        <input type="submit" value="<?=($id ? 'Сохранить' : 'Добавить')?>" class="but1" onclick="loader(true)" />&nbsp;
        <input type="button" value="Отмена" class="but1" onclick="location.href='<?=$script?>'" />
      </td>
    </tr>
  </table>
  </form>
  <?
	$content = ob_get_clean();
}
// -----------------ПРОСМОТР-------------------
else
{
	$cur_page = $_SESSION['ss']['page'] ? $_SESSION['ss']['page'] : 1;

	$page_title .= ' :: '.$rubric;
	$rubric .= ' &raquo; Общий список';

	$razdel['Добавить'] = '?red=0';
	$razdel['Удалить'] = "javascript:multidel(document.red_frm,'check_del_','');";
	$subcontent = show_subcontent($razdel);

	$query = "SELECT * FROM {$prx}{$tbl}";

	ob_start();
	// проверяем текущую сортировку
	// и формируем соответствующий запрос
	if($_SESSION['ss']['sort'])
	{
		$sort = explode(':',$_SESSION['ss']['sort']);
		$cur_pole = $sort[0];
		$cur_sort = $sort[1];

		$query .= " ORDER BY {$cur_pole} ".($cur_sort=='up'?'DESC':'ASC');
	}
	else
		$query .= ' ORDER BY sort,id';
	//-----------------------------
	//echo $query;
	
	show_filters($script);
	?>

  <form action="?action=multidel" name="red_frm" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table width="100%" cellspacing="0" cellpadding="0" class="tab1">
    <tr>
      <th><input type="checkbox" name="check_del" id="check_del" /></th>
      <th>№</th>
      <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Вопрос','question')?></th>
      <th width="50%"><?=ShowSortPole($script,$cur_pole,$cur_sort,'Ответ','answer')?></th>
      <th nowrap><?=ShowSortPole($script,$cur_pole,$cur_sort,'Статус','status')?></th>
      <? if(!$_SESSION['ss']['sort']) { ?><th nowrap>Порядок <?=help('параметр с помощью которого можно изменить порядок вывода элемента в клиентской части сайта')?></th><? }?>
      <th style="padding:0 30px;"></th>
    </tr>
		<?
		$res = sql($query);
		if(@mysqli_num_rows($res))
		{
			$i=1;
			while($row = mysqli_fetch_assoc($res))
			{
				$id = $row['id'];
        ?>
        <tr id="row<?=$id?>">
          <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>" /></th>
          <th nowrap><?=$i++?></th>
          <td><a href="?red=<?=$id?>" class="link1"><?=$row['question']?></a></td>
          <td><?=$row['answer']?></td>
          <td align="center"><?=btn_flag($row['status'],$id,'action=status&id=',$locked)?></td>
          <? if(!$_SESSION['ss']['sort']){ ?><td nowrap align="center"><?=btn_sort($id)?></td><? }?>
          <td nowrap align="center"><?=btn_edit($id,$locked)?></td>
        </tr>
        <?
      }
	  } else {
      ?>
      <tr>
        <td colspan="10" align="center">
        по вашему запросу ничего не найдено. <?=help('нет ни одной записи отвечающей критериям вашего запроса,<br>возможно вы установили неверные фильтры')?>
        </td>
      </tr>
      <?
	  }
	?>
  </table>
  </form>
  <?
	$content = $subcontent.ob_get_clean();
}

require('tpl/tpl.php');