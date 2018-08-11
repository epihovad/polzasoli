<?
ini_set('display_errors',1);
require('inc/common.php');

$ticket = array();
if($link = clean($_GET['link'])){
  $ticket = getRow("SELECT * FROM {$prx}tickets WHERE link = '{$link}' AND status = 1");
}

// страница абонемента
if($ticket){
  include('ticket.php');
  exit;
}

// список абонементов

ob_start();


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