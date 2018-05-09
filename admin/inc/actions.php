<?
@session_start();

require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');

if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		// ----------------- сортировка
		case 'sort':
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");

			if(!$tbl = $_GET['tbl']){
				echo json_encode(array('status' => 'error', 'message' => 'не определена таблица'));
				exit;
			}

			$i = 1;
			$errors = 0;
			foreach ($_POST['item'] as $id){
				if(!update($tbl, "`sort`={$i}", $id)){
					$errors++;
					continue;
				}
				$i++;
			}
			if(!$errors){
				echo json_encode(array('status' => 'ok', 'message' => 'success update ' . sizeof($_POST['item']) . ' items'));
			} else {
				echo json_encode(array('status' => 'error', 'message' => 'произошла ошибка'));
			}
			exit;
		// ---------------- обновление значения в списке
		case 'update':
			$id = (int)$_GET['id'];
			$tbl = clean($_GET['tbl']);
			$field = clean($_GET['field']);
			$val = clean($_GET['val']);
			
			$error = '';
			
			if($tbl=='goods')
			{
				switch($field)
				{
					//
					case 'price':
					case 'old_price':
						$val = round(str_replace(',','.',$val),2);
							$val = str_replace(',','.',$val);
						break;
					//
					case 'nalich':
						$val = (int)$val;
						if($val>0)
						{
							require('spec.php');
							closeZvk($id);
						}
						break;
				}
			}
			
			if($error) { echo json_encode(array('invalidVal'=>$error)); exit; }
			
			update($tbl,"`{$field}`='{$val}'",$id);
			echo json_encode(array($val));
			
			break;
	}
	exit;
}
?>