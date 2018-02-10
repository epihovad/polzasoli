<?
@session_start();

require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');

if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
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