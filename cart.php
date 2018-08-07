<?
ini_set('display_errors',1);
require('inc/common.php');

if(isset($_GET['action'])){

  exit;
}

ob_start();

switch ($_GET['show']){
	//
  case 'bron':
		$number = clean($_GET['number']);
    if(array_search($number, (array)$_SESSION['my_bron']) === false){
      header('HTTP/1.0 404 Not Found');
      header("Location: /cart/?show=bron_info_expired&number={$number}");
			exit;
    }

		$query = "SELECT 	b.*,
                      CONCAT(b.iday,'/',b.itime,'-',b.id) AS number,
                      t.*,
                      SUM(cnt_child7 + cnt_child16 + cnt_grown + cnt_pensioner) AS cnt
            FROM {$prx}bron b
            JOIN {$prx}time t ON b.itime = t.pktime
            WHERE CONCAT(b.iday,'/',b.itime,'-',b.id) = '{$number}'
            ";

    $bron = getRow($query);
    if(!$bron['id']){
			header('HTTP/1.0 404 Not Found'); $code = '404'; require('errors.php'); exit;
    }

    $h1 = 'Бронь № ' . $number;

    ?>
    <style>
      #bron-detail { border-collapse:collapse;}
      #bron-detail tr { border-bottom:1px solid #c9e4e6;}
      #bron-detail th { padding:2px 20px 2px 0; font-weight:400;}
      #bron-detail td { color:#6c6c6c;}
      #bron-detail .pd10 { padding-left:10px;}
    </style>

    <h3>Подробная информация:</h3>
    <table id="bron-detail">
      <tbody>
        <tr><th>Номер брони</th><td><?=$bron['number']?></td></tr>
        <tr><th>Дата и время сенаса</th><td><?=date('d.m.Y', strtotime($bron['iday']))?> в <?=$bron['ihour']?>:<?=$bron['iminute']?></td></tr>
        <tr><th>Имя</th><td><?=$bron['name']?></td></tr>
        <tr><th>Контактный телефон</th><td>+7<?=$bron['phone']?></td></tr>
        <tr><th>Email</th><td><?=$bron['email']?:'-'?></td></tr>
        <tr><th>Впервые?</th><td><?=$bron['first']?'да':'нет'?></td></tr>
        <tr><th>Кол-во забронированных мест</th><td><?=$bron['cnt']?></td></tr>
        <tr><th class="pd10">- Кол-во детей до 7 лет</th><td><?=$bron['cnt_child7']?></td></tr>
        <tr><th class="pd10">- Кол-во детей до 16 лет</th><td><?=$bron['cnt_child16']?></td></tr>
        <tr><th class="pd10">- Кол-во взрослых</th><td><?=$bron['cnt_grown']?></td></tr>
        <tr><th class="pd10">- Кол-во пенсионеров</th><td><?=$bron['cnt_pensioner']?></td></tr>
      </tbody>
    </table>
    <?

    break;
	//
	case 'bron_info_expired':
		?><div class="nofind">страница устарела</div><?
		break;
	//
	default:
		header("HTTP/1.0 404 Not Found");
		$code = '404';
		require('errors.php');
		exit;
}
$data = ob_get_clean();

?>
<div class="container-fluid">
  <h1><?=$h1?></h1>
  <div class="content" style="padding-bottom:40px;">
    <?=$data?>
  </div>
</div>
<?
$content = ob_get_clean();
require('tpl/template.php');