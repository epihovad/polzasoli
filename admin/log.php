<?
require('inc/common.php');

$h1 = 'Журнал событий';
$h = 'Общий список';
$title .= ' :: ' . $h1;
$navigate = '<span></span>' . $h;
$tbl = 'log';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = (int)$_GET['id'];

	switch($_GET['action'])
	{
		// ----------------- удаление одной записи
		case 'del':
			remove_object($id);
			?><script>top.location.href = top.url()</script><?
			break;
		// ----------------- удаление нескольких записей
		case 'multidel':
			foreach($_POST['check_del_'] as $id=>$v) {
				remove_object($id);
			}
			?><script>top.location.href = top.url()</script><?
			break;
	}
	exit;
}
// -----------------ПРОСМОТР-------------------
else
{
	$cur_page = (int)$_GET['page'] ?: 1;
	$fl['day1'] = $_GET['fl']['day1'];
	$fl['day2'] = $_GET['fl']['day2'];
	$fl['search'] = stripslashes($_GET['fl']['search']);

	$filters['day1'] = "выбор событий по Дате (С даты)";
	$filters['day2'] = "выбор событий по Дате (ПО дату)";

	$where = '';
	if($fl['day1']){
		$where .= "\r\nAND date >= STR_TO_DATE('{$fl['day1']}', '%d.%m.%Y')";
	}
	if($fl['day2']){
		$where .= "\r\nAND date < STR_TO_DATE('{$fl['day2']}', '%d.%m.%Y') + INTERVAL 1 DAY";
	}
	if($fl['search'] != ''){
		$sf = array('type','notes');
		$w = '';
		foreach ($sf as $field){
			$w .= ($w ? ' OR' : '') . "\r\n`{$field}` LIKE '%{$fl['search']}%'";
		}
		$where .= "\r\n AND ({$w}\r\n)";
	}

	$query = "SELECT * FROM {$prx}{$tbl} WHERE 1{$where}";

	$r = sql($query);
	$count_obj = @mysqli_num_rows($r); // кол-во объектов в базе
	$count_obj_on_page = 30; // кол-во объектов на странице
	$count_page = ceil($count_obj/$count_obj_on_page); // количество страниц

	// проверяем текущую сортировку и формируем соответствующий запрос
	if($fl['sort']){
		foreach ($fl['sort'] as $f => $t){
			$query .= "\r\nORDER BY {$f} {$t}";
			break;
		}
	} else {
		$query .= "\r\nORDER BY id DESC";
	}

	$query .= "\r\nLIMIT " . ($count_obj_on_page * $cur_page - $count_obj_on_page) . ',' . $count_obj_on_page;

	ob_start();

	show_listview_btns('Удалить');
	ActiveFilters();
	?>

  <div class="clearfix"></div>

	<div id="filters" class="panel-white">
    <h4 class="heading">Фильтры
      <a href="#">
        <i class="fas fa-eye" title="показать фильтры">
        </i><i class="fas fa-eye-slash" title="скрыть фильтры"></i>
      </a>
    </h4>
    <div class="fbody">
      <div class="item">
        <label>Дата события</label>
        <div>
          с <?=input('date', 'fl[day1]', $fl['day1'])?>
          по <?=input('date', 'fl[day2]', $fl['day2'])?>
        </div>
      </div>
      <div class="item search">
        <label>Контекстный поиск</label><br>
        <div><?=input('text', 'fl[search]', $fl['search'])?></div>
      </div>
      <button class="btn btn-danger" onclick="setFilters()"><i class="fas fa-search"></i>Поиск</button>
    </div>
  </div>

  <style>
    .table-list .date span { color: #7a7a7a; font-size: 10px;}
  </style>

	<?=pagination($count_page, $cur_page, true, 'padding:0 0 10px;')?>
  <form id="ftl" method="post" target="ajax">
  <input type="hidden" id="cur_id" value="<?=(int)@$_GET['id']?>" />
  <table class="table-list">
    <thead>
      <tr>
        <th width="1%"><input type="checkbox" name="check_del" id="check_del" /></th>
        <th width="1%">№</th>
        <th width="1%"><?=SortColumn('Дата','date')?></th>
        <th nowrap width="50%"><?=SortColumn('Событие','type')?></th>
        <th nowrap width="1%">Ссылка</th>
        <th nowrap width="50%">Информация</th>
        <th style="padding:0 30px;"></th>
      </tr>
    </thead>
    <tbody>
    <?
    $res = sql($query);
    if(mysqli_num_rows($res)){
      $i=1;
      while($row = mysqli_fetch_assoc($res)) {
        $id = $row['id'];
        $active = $id == $_GET['id'] ? ' active' : '';
				?>
        <tr id="item-<?=$id?>" class="<?=$active?>">
          <th><input type="checkbox" name="check_del_[<?=$id?>]" id="check_del_<?=$id?>"></th>
          <th nowrap><?=$i++?></th>
          <th nowrap class="date"><?=date('d.m.Y', strtotime($row['date']))?> <span><?=date('H:i:s', strtotime($row['date']))?></span></th>
          <td class="sp"><?=$row['type']?></td>
          <th><?
            if($row['link']){
              ?><a href="<?=$row['link']?>" class="clr-green im-lnk" target="_blank"><i class="fas fa-link"></i></a><?
            }
          ?></th>
          <td class="sp"><?=nl2br($row['notes'])?></a></td>
          <th nowrap>
            <button type="button" class="btn btn-danger btn-xs" alt="удалить" title="удалить"
              onclick="$(document).jAlert('show', 'confirm', 'Уверены?', function () { toajax(url('file') + '?action=del&id=<?=$id?>'); })">
              <i class="far fa-trash-alt"></i>
            </button>
          </th>
        </tr>
				<?
			}
		} else {
      ?>
      <tr class="nofind">
        <td colspan="10">
          <div class="bg-warning">
            по вашему запросу ничего не найдено.
						<?=help('нет ни одной записи отвечающей критериям вашего запроса,<br>возможно вы установили неверные фильтры')?>
          </div>
        </td>
      </tr>
      <?
    }
    ?>
    </tbody>
  </table>
  </form>
	<?
	$content = arr($h, ob_get_clean());
}

require('tpl/template.php');