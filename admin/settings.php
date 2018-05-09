<?
require('inc/common.php');

$tbl = 'settings';

// ------------------- СОХРАНЕНИЕ ------------------------
if(isset($_GET['action']))
{
	$id = clean($_GET['id']);

	switch($_GET['action'])
	{
		// ----------------- сохранение
		case 'save':
			foreach($_POST as $id=>$value){
				$value = clean($value);
				update($tbl,"value='{$value}'",$id);
			}
			foreach($_FILES as $id=>$file){
				@move_uploaded_file($file['tmp_name'],$_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg");
				@chmod($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg",0644);

				$value = basename($file);
				update($tbl,"value='{$value}'",$id);
			}
			unset($_SESSION['cache'][$tbl]);
			jAlert('Изменения успешно сохранены.');
			break;
		// ----------------- удаление картинки
		case 'pic_del':
			if($flag = getField("SELECT flag FROM {$prx}{$tbl} WHERE id='{$id}'"))
			{
				@unlink($_SERVER['DOCUMENT_ROOT']."/uploads/{$tbl}/{$id}.jpg");
				?><script>top.location.href='<?=$script?>?flag=<?=$flag?>&id=<?=$id?>'</script><?
			}
			break;
	}
	exit;
}

$h1 = 'Настройки';
$title .= ' :: ' . $h1;
$flag = $_GET['flag'];
if(in_array($flag, array('admin','client')) === false){
	header("HTTP/1.0 404 Not Found");
	header("Location: settings.php?flag=admin");
	exit;
}
$h = $flag == 'admin' ? 'Административная часть (backend)' : 'Клиентская часть (frontend)';
$navigate = '<span></span>' . $h;

// ----------------- РЕДАКТИРОВАНИЕ / ПРОСМОТР -------------------
ob_start();
$query = "SELECT * FROM {$prx}{$tbl}\r\nWHERE flag='{$flag}'\r\nORDER BY sort,id";
$res = sql($query);
if(@mysqli_num_rows($res)){
	?>
  <form action="?action=save" method="post" enctype="multipart/form-data" target="ajax">
    <table class="table-edit">
			<?
			while($row = mysqli_fetch_assoc($res)){
				?>
        <tr>
          <th><?=($row['help'] ? help($row['help']) : '')?></th>
          <th><?=$row['name']?></th>
          <td><?
						switch($row['type'])
						{
							case 'text':
							case 'password':
							case 'checkbox':
							case 'textarea':
							case 'datetime':
							case 'date':
								echo input($row['type'],$row['id'],$row['value']);
								break;
							case 'fck':
								echo showCK($row['id'],$row['value']);
								break;
							case 'color':
								echo input('color',$row['id'],$row['value']);
								break;
							case 'file':
								echo input('file',$row['id']);
								break;
						}
						?></td>
        </tr>
				<?
			}
			?>
    </table>
    <div class="frm-btns">
      <input type="submit" value="Сохранить" class="btn btn-success btn-sm" onclick="loader(true)" />
    </div>
  </form>
	<?
}
$content = arr($h, ob_get_clean());
$tbl = "settings.php?flag={$flag}";
require('tpl/template.php');