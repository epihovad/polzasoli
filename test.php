<?
ini_set('memory_limit', '3072M');
ini_set('max_execution_time', '10000');
ini_set('display_errors',1);

require_once($_SERVER['DOCUMENT_ROOT'].'/inc/db.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');

require_once($_SERVER['DOCUMENT_ROOT']."/inc/PHPExcel/Classes/PHPExcel.php");
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

//
function RECUR($ID){
  global $conn, $tree, $I;

  if(!$ID)
    return $tree;

  $q = "SELECT  TT.ITICKETS2 AS ITICKET,
                TS.CNAME AS CSTATUS
        FROM VEGA_DS.TBL_D_GLPI_TICKETS_TICKETS TT
        JOIN VEGA_DS.TBL_D_GLPI_TICKETS T ON T.PKID = TT.ITICKETS2
        JOIN VEGA_DS.TBL_D_GLPI_TICKET_STATUS TS ON TS.PKSTATUS = T.CSTATUS
        WHERE 1=1
              AND ITICKETS1 = '{$ID}'
              AND ILINK = 1";

	foreach ($conn->query($q)->fetchAll(PDO::FETCH_ASSOC) as $arr) {
		if(in_array($arr['ITICKET'], $I) !== false){
			continue;
		}
		$I[] = $arr['ITICKET'];
		$tree[$arr['ITICKET']] = $arr['CSTATUS'];
		RECUR($arr['ITICKET']);
	}

	$q = "SELECT  TT.ITICKETS1 AS ITICKET,
                TS.CNAME AS CSTATUS
        FROM VEGA_DS.TBL_D_GLPI_TICKETS_TICKETS TT
        JOIN VEGA_DS.TBL_D_GLPI_TICKETS T ON T.PKID = TT.ITICKETS2
        JOIN TBL_D_GLPI_TICKET_STATUS TS ON TS.PKSTATUS = T.CSTATUS
        WHERE 1=1
              AND ITICKETS2 = '{$ID}'
              AND ILINK = 1";

	foreach ($conn->query($q)->fetchAll(PDO::FETCH_ASSOC) as $arr) {
		if(in_array($arr['ITICKET'], $I) !== false){
			continue;
		}
		$I[] = $arr['ITICKET'];
		$tree[$arr['ITICKET']] = $arr['CSTATUS'];
		RECUR($arr['ITICKET']);
	}

	return $tree;
}
// запрос на получение набора связных заявок
$sql_cross = "WITH
              NBR AS (
                SELECT  CONNECT_BY_ROOT TT.ITICKETS1 AS IROOT,
                        TT.ITICKETS1,
                        TT.ITICKETS2,
                        CONNECT_BY_ISCYCLE AS CY
                FROM TBL_D_GLPI_TICKETS_TICKETS TT
                WHERE 1=1
                      AND TT.ILINK = 1
                      AND (
                        TT.ITICKETS1 = %s OR TT.ITICKETS2 = %s
                      )
                CONNECT BY NOCYCLE PRIOR TT.ITICKETS1 = TT.ITICKETS2
              )
              SELECT DISTINCT TT.ITICKETS1 AS IIDS
              FROM (
                SELECT ITICKETS1 AS ITICKETS
                FROM NBR
                UNION
                SELECT ITICKETS2 AS ITICKETS
                FROM NBR
                UNION
                SELECT IROOT AS ITICKETS
                FROM NBR
              ) T
              JOIN TBL_D_GLPI_TICKETS_TICKETS TT ON TT.ITICKETS1 = T.ITICKETS OR TT.ITICKETS2 = T.ITICKETS
";
// вспомогательный запрос на выгрузку набора связных заявок
$sql = "SELECT *
        FROM (
          SELECT  TO_CHAR(T.DDATE, 'YYYY-MM-DD') AS CDATE,
                  T.IID,
                  T.CSTATUS,
                  SUM(T.IIS_ACTIVE) OVER() AS ICOUNT_ACTIVE,
                  T.CNAME,
                  T.CCATEGORY0,
                  T.CCATEGORY1,
                  HR.CNAME0 AS CSUPPLIER_DEP0,
                  HR.CNAME1 AS CSUPPLIER_DEP1,
                  US.CFIO AS CSUPPLIER_NAME,
                  CASE WHEN RN = 1 THEN 'Y' ELSE 'N' END AS CIS_MAIN
          FROM (
            SELECT  T.DDATE,
                    T.PKID AS IID,
                    TS.CNAME AS CSTATUS,
                    CASE WHEN NOT T.CSTATUS IN ('solved','closed','rejected') THEN 1 ELSE 0 END AS IIS_ACTIVE,
                    T.CNAME AS CNAME,
                    C0.CNAME AS CCATEGORY0,
                    C1.CNAME AS CCATEGORY1,
                    (
                      SELECT MAX(TU.IUSER_ID) KEEP (DENSE_RANK LAST ORDER BY TU.PKID)
                      FROM TBL_D_GLPI_TICKET_USERS TU
                      WHERE 1=1
                            AND TU.IUSER_TYPE = 1
                            AND TU.ITICKET_ID = T.PKID
                    ) AS ISUPPLIER,
                    ROW_NUMBER() OVER (ORDER BY T.PKID) AS RN
            FROM TBL_D_GLPI_TICKETS T
            JOIN TBL_D_GLPI_TICKET_STATUS TS ON TS.PKSTATUS = T.CSTATUS
            JOIN TBL_D_GLPI_ITILCATEGORIES C1 ON C1.PKID = T.IITILCATEGORIES_ID
            LEFT JOIN TBL_D_GLPI_ITILCATEGORIES C0 ON C0.PKID = C1.IITILCATEGORIES_ID
            WHERE 1=1
                  AND T.PKID IN (%s)
                  AND T.ITYPE_ID = 2 -- ЗнИ
            ORDER BY T.PKID
          ) T
          LEFT JOIN TBL_D_GLPI_USERS US ON US.PKID = T.ISUPPLIER AND RN = 1
          LEFT JOIN TBL_D_HR_DEPARTMENT HR ON HR.CGUID = US.CHR_DEPARTMENT
        )
        WHERE ICOUNT_ACTIVE > 0
";

$dim = array(
	'CDATE' => 'Дата заявки',
	'IID' => 'Номер',
	'CSTATUS' => 'Статус',
	'ISIZE' => 'Кол-во',
	'CNAME' => 'Тема (заголовок)',
	'CCATEGORY0' => 'Категория корн.',
	'CCATEGORY1' => 'Категория уровень 1',
	'CSUPPLIER_DEP0' => 'Дирекция заказчика',
	'CSUPPLIER_DEP1' => 'Отдел заказчика',
	'CSUPPLIER_NAME' => 'Заказчик',
);
$srv_list = array(
  'CRM',
  //'HelpDesk',
  //'InigsSklad',
  //'IP-телефония',
  'Phub',
  'PriceStat (парсер цен Яндекс-Маркета)',
  'Pricing',
  //'Stat',
  'WMS',
  'WTIS',
  //'Видеонаблюдение в рознице',
  'Отчетность BI',
  //'Портал ВИ',
  'Сайт',
  '1С',
);
// -------------------------

$I = array();
$G = array();

$get = explode(',',$_GET['ids']);
$IDS = array();
if(sizeof($get)){
  foreach ($get as $id){
    if($id = (int)$id){
      $IDS[] = $id;
    }
  }
}

// условия выборки
$where = '';
if(sizeof($IDS)){
	$where .= ' AND T.PKID IN ('.implode(',', $IDS).')';
} else {
	//$where .= ' AND IDAY >= 20180101';
}

// основной запрос на выгрузку заявок
$q = "SELECT  DISTINCT
							T.PKID AS ITICKET,
							TS.CNAME AS CSTATUS,
							'N' AS CIS_SINGLE -- заявки со связями
			FROM VEGA_DS.TBL_D_GLPI_TICKETS T
			JOIN VEGA_DS.TBL_D_GLPI_TICKET_STATUS TS ON TS.PKSTATUS = T.CSTATUS
			JOIN VEGA_DS.TBL_D_GLPI_TICKETS_TICKETS TT ON TT.ITICKETS1 = T.PKID OR TT.ITICKETS2 = T.PKID
			WHERE 1=1{$where}
			
			UNION ALL
			
			SELECT  DISTINCT
							T.PKID AS ITICKET,
							TS.CNAME AS CSTATUS,
							'Y' AS CIS_SINGLE -- одиночные заявки
			FROM VEGA_DS.TBL_D_GLPI_TICKETS T
			JOIN VEGA_DS.TBL_D_GLPI_TICKET_STATUS TS ON TS.PKSTATUS = T.CSTATUS
			LEFT JOIN VEGA_DS.TBL_D_GLPI_TICKETS_TICKETS TT ON TT.ITICKETS1 = T.PKID OR TT.ITICKETS2 = T.PKID
			WHERE 1=1
						AND TT.ITICKETS1 IS NULL{$where}
			
			ORDER BY 1";

$db = new DB();
$conn = $db->conn_oracle('vegads');
foreach ($conn->query($q)->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $ID = $row['ITICKET'];
  //
  if(in_array($ID, $I) !== false){
    continue;
  }
  $I[] = $ID;

	$tree = array();
	$tree[$ID] = $row['CSTATUS'];

	if($row['CIS_SINGLE'] == 'N'){
		RECUR($ID);
	}

	$G[$ID] = $tree;
}
//pre($G); exit;
unset($I);

$Tickets = array();

// погнали по наборам
foreach ($G as $nabor){

  $q = sprintf($sql, implode(',', array_keys($nabor)));
  $mainID = ''; // основная заявка-родитель

	foreach ($conn->query($q)->fetchAll(PDO::FETCH_ASSOC) as $arr) {

		$ID = $arr['IID'];

	  if($arr['CIS_MAIN'] == 'Y' && !isset($Tickets[$ID])){
			$mainID = $ID;
			$Tickets[$mainID] = array();
			foreach ($dim as $k => $none){
			  $v = $k == 'ISIZE' ? sizeof($nabor) : $arr[$k];
				$Tickets[$mainID][$k] = $v;
      }
    }

		$Tickets[$mainID]['MATRIX'][$arr['CCATEGORY1']][$ID] = $arr['CSTATUS'];
	}
}
ksort($Tickets);
//pre($Tickets); exit;

// выводим в Excel
$xls = new PHPExcel();

$columns = array();
$romNum = 1;

// шапка таблицы
$colNum = 0;
foreach ($dim as $head){
	$xls->getActiveSheet()->setCellValueByColumnAndRow($colNum++, $romNum, $head);
}
$romNum++;

foreach ($Tickets as $ID => $row){

  // пропускаем заявки относящиеся к одной системы
  /*if(sizeof($row['MATRIX']) == 1){
    continue;
  }*/

  // измерения
  $colNum = 0;
  foreach ($dim as $field => $head){
		$xls->getActiveSheet()->setCellValueByColumnAndRow($colNum++, $romNum, $row[$field]);
  }

  // матрица
  $old_srv = '';
  $rn = $romNum;
	$rns = array($rn);
  $active = 0;
	foreach ($row['MATRIX'] as $srv => $ids){

    if(in_array($srv, $srv_list) === false){
      continue;
    }

    if($srv != $old_srv){
      $rn = $romNum;
    }

    $k = array_search($srv, $columns);
    if($k !== false){
      $colNum = $k;
    } else {
      $colNum = !sizeof($columns) ? sizeof($dim) : max(array_keys($columns)) + 2;
      $columns[$colNum] = $srv;
    }

    foreach ($ids as $num => $status){
			$xls->getActiveSheet()->setCellValueByColumnAndRow($colNum, $rn, $num);
			$xls->getActiveSheet()->setCellValueByColumnAndRow($colNum+1, $rn, $status);
			$rns[] = $rn++;
			$active++;
    }

		$old_srv = $srv;
	}

  if($active){
	  // обновляем кол-во заявок
		$xls->getActiveSheet()->setCellValueByColumnAndRow(3, $romNum, $active);
    $bg = $old_bg == 'B8CCE4' ? 'D8E4BC' : 'B8CCE4';
		// дублируем димы (для фильтрации)
    for($r = $romNum; $r <= max($rns); $r++){
      $cl = 0;
			foreach ($dim as $field => $head){
				$xls->getActiveSheet()->setCellValueByColumnAndRow($cl++, $r, $row[$field]);
			}
			// красим номер основной заявки
			$xls->getActiveSheet()->getStyle('B'.$r)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => $bg)
					)
				)
			);
    }
		$old_bg = $bg;
    //
	  $romNum = max($rns) + 1;
  } else {
		$xls->getActiveSheet()->removeRow($romNum, max($rns) - $romNum + 1);
	}

}

// матрица (шапка)
foreach ($columns as $colNum => $srv){
	$xls->getActiveSheet()->setCellValueByColumnAndRow($colNum, 1, $srv);
	// объединяем
	$l1 = PHPExcel_Cell::stringFromColumnIndex($colNum);
	$l2 = PHPExcel_Cell::stringFromColumnIndex($colNum+1);
	$xls->getActiveSheet()->mergeCells($l1.'1:'.$l2.'1');
}

// автофильтры
$xls->getActiveSheet()->setAutoFilter('A1:'.$xls->getActiveSheet()->getHighestDataColumn().'1');
// ширина
foreach (range('A', $xls->getActiveSheet()->getHighestDataColumn()) as $col) {
	$xls->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
	//$xls->getActiveSheet()->getStyle($col.'2:'.$col.$xls->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
}

$xls->getActiveSheet()->freezePane('C2');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=glpi_matrix.xlsx');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$objWriter->save('php://output');

$xls->disconnectWorksheets();
unset($xls);