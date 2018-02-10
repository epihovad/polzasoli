<?
require('inc/common.php');

if(isset($_GET['action']))
{
  switch($_GET['action'])
  {
    case 'mods':
      ini_set('memory_limit','256M');
      foreach($_POST as $key=>$value)
        $$key = clean($value);

      if(!$_FILES['userfile']['name']) errorAlert('выберите файл импорта');

      @unlink($_SERVER['DOCUMENT_ROOT'].'/tmp/mods.xlsx');
      @move_uploaded_file($_FILES['userfile']['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/tmp/mods.xlsx');

      // создаем файл логов
      @unlink($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_SESSION['report_mods']);
      $_SESSION['report_mods'] = 'report_mods_'.time().'.txt';
      $log = fopen($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_SESSION['report_mods'],'w');

      try{
        include_once($_SERVER['DOCUMENT_ROOT'].'/inc/PHPExcel/Classes/PHPExcel/IOFactory.php');
        $objPHPExcel = PHPExcel_IOFactory::load($_SERVER['DOCUMENT_ROOT'].'/tmp/mods.xlsx');
      }
      catch(Exception $e){
        fwrite($log,$e->getMessage()."\n");
        fclose($log);
        errorAlert('Fatal error! см. протокол!');
        ?><script>top.location.reload(true)</script><?
        exit;
      }

      $objPHPExcel->setActiveSheetIndex(0);
      $sheet = $objPHPExcel->getActiveSheet();

      $data = array();

      foreach($sheet->getRowIterator() as $row)
      {
        $cellIterator = $row->getCellIterator();
        $item = array();
        foreach($cellIterator as $cell)
          array_push($item,win2utf($cell->getCalculatedValue()));
        array_push($data, $item);
      }

      $find = 0;
      $nKol = sizeof(@$data[0]); // кол-во колонок

      // Колонки:
      // Наименование товара - Размер - Кол-во вставок - Опт стандарт - Опт с полочкой - Розн стандарт - Розн с полочкой

      if($nKol<>7)
      {
        fwrite($log,"неверный формат загружаемого файла\n");
        $errors++;
      }
      else
      {
        $head = $data[0]; // шапка таблицы

        $kolRows = sizeof($data); // кол-во строк
        // обязательные поля
        $fields = array('good_name','mod_name','sections','price_opt','price_shelf_opt','price','price_shelf');
        // погнали по товарам
        for($s=1; $s<$kolRows; $s++)
        {
          $nrow = $s+1;

          $good_name = clean($data[$s][0]);
					$mod_name = clean($data[$s][1]);

					// валидация размера
					preg_match_all('/([0-9]+)/',$mod_name,$m);
					if($m[0][0] && $m[0][1]){
						$mod_name = $m[0][0].'x'.$m[0][1];
					}

          for($n=2; $n<7; $n++){
						$$fields[$n] = preg_replace('/[^0-9\.]*/','',$data[$s][$n]);
						$$fields[$n] = preg_replace('/[\.]+/','.',$$fields[$n]);
						$$fields[$n] = ceil($$fields[$n]);
						if(!$$fields[$n]){ fwrite($log,"в строке №{$nrow} в колонке ".($n-1)." не определена цена модификации\n"); $errors++; }
          }

          // проверка
          if(!$good_name){ fwrite($log,"в строке №{$nrow} отсутствует «Наименование» товара\n"); $errors++; continue; }
          if(!$mod_name){ fwrite($log,"в строке №{$nrow} отсутствует «Размер» модификации\n"); $errors++; continue; }

          if(!$id_good = getField("SELECT id FROM {$prx}goods WHERE name = '{$good_name}'")){
            fwrite($log,"в строке №{$nrow} не удалось найти товар в базе данных\n");
            $errors++;
            continue;
          }

          $id_mod = getField("SELECT id FROM {$prx}mods WHERE id_good='{$id_good}' AND name = '{$mod_name}'");

          // добавляем модификацию
          $set = "`id_good`='{$id_good}',
                  `name`='{$mod_name}',
                  `sections`='{$sections}',
                  `price_opt`='{$price_opt}',
                  `price_shelf_opt`='{$price_shelf_opt}',
                  `price`='{$price}',
                  `price_shelf`='{$price_shelf}'
                  ";

          if(!$id_mod = update('mods',$set,$id_mod))
          {
            fwrite($log,"строка №{$nrow}: ошибка сохранения данных ".mysql_error()."\n");
            $errors++;
            continue;
          }

          $flag++; // модификация загружена
        }
      }

      fclose($log);

      if($flag)
      {
        errorAlert('Загрузка успешно завершена.\nОбработано позиций: '.$flag,false);
        if($errors)
          errorAlert('В процессе загрузки данных возникли ошибки.\nПроверьте протокол загрузки.',false);
      }
      else
      {
        if($errors) errorAlert('В процессе загрузки данных возникли ошибки.\nПроверьте протокол загрузки.',false);
        else errorAlert('Ни одной строки не обработано.\nВозможно записи в загружаемом файле отсутствуют.',false);
      }

      if(!$errors) @unlink($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_SESSION['report_mods']);

      ?><script>top.location.reload(true)</script><?
      break;
  }
  exit;
}

function protokol($type='mods')
{
  if($_SESSION['report_'.$type] && file_exists($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_SESSION['report_'.$type]))
  {
    ?><div class="protokol"><a href="/tmp/<?=$_SESSION['report_'.$type]?>" target="_blank">протокол</a></div><?
  }
  else
  {
    $rp = getFileFormat($_SERVER['DOCUMENT_ROOT']."/tmp/report_{$type}_*",true);
    foreach($rp as $f) @unlink($f);
    unset($_SESSION['report_'.$type]);
  }
}

ob_start();

$rubric = 'Импорт';
$page_title .= ' :: '.$rubric;

?>
<style>
  .protokol { padding: 5px 0; }
  .protokol a { color: #FF6600;}
</style>

<form action="?action=mods" method="post" enctype="multipart/form-data" target="ajax">
  <div style="float:left; padding-right:30px;"><input type="file" name="userfile"></div>
  <div style="float:left"><input type="submit" value="импорт" class="but1" /></div>
</form>
<div class="clear" style="padding-bottom:10px;"></div>
<?=protokol()?>
<div><a href="inc/import.xlsx">образец</a></div>
<?

$content = ob_get_clean();
require("tpl/tpl.php");