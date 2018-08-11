<?
if(!$ticket) { header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit; }

$id = $ticket['id'];

// -------------------- TITLE + КЛЮЧЕВЫЕ СЛОВА
$title = $ticket['name'];
foreach(array('title','keywords','description') as $val)
	if($ticket[$val]) $$val = $ticket[$val];

$h1 = $ticket['h1'] ?: $ticket['name'];

$navigate = '<a href="/tickets/">Услуги и цены</a>';

ob_start();

?>
<style>
  #ticket-page .col-img { display:table-cell; padding:0 40px 40px 0; vertical-align:top;}
  #ticket-page .col-info { display:table-cell; padding:0 0 40px 0; vertical-align:top;}
  #ticket-page .col-info .who { font-weight:700; color:#2292ab;}
  #ticket-page .col-info .who b { color:#333;}
  #ticket-page .ticket-disease { padding:10px; background-color:#f1f6fa; margin:20px 0 20px;}
  #ticket-page .ticket-disease div { font-weight:700; padding-bottom:10px;}
  #ticket-page .ticket-disease a { color:#2292ab; }
</style>

<div id="ticket-page" class="row">
  <div class="col-img">
		<?
		$src = '/uploads/no_photo.jpg';
		$big_src = '/uploads/no_photo.jpg';
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/uploads/tickets/{$id}.jpg")){
			$src = "/tickets/320x320/{$id}.jpg";
			$big_src = "/tickets/{$id}.jpg";
		}
		?>
    <a href="<?=$big_src?>" class="blueimp" title="<?=htmlspecialchars($title)?>">
      <img src="<?=$src?>" style="max-height:320px; max-width:320px;">
    </a>
  </div>
  <div class="col-info">
		<?=$ticket['text']?>
    <p class="who">
      <?
      if($ticket['ids_who']){
        $who = getArr("SELECT name FROM {$prx}tickets_who WHERE id IN ({$ticket['ids_who']}) AND status = 1");
				?><b>Для кого</b>: <?=implode(', ', $who)?><br><?
      }
      ?>
      <b>Срок действия</b>: <?=$ticket['validity']?>
    </p>
    <?
    // болезни
    if($ticket['ids_disease']){
      ?><div class="ticket-disease"><div>Подходит для значительного улучшения состояния организма и профилактики при диагнозах врача:</div><?
      $r = sql("SELECT name, link FROM {$prx}disease WHERE id IN ({$ticket['ids_disease']}) AND status = 1");
      $str = '';
      while ($arr = @mysqli_fetch_assoc($r)){
        ob_start();
        ?><a href="/disease/<?=$arr['link']?>.htm"><?=$arr['name']?></a><?
        $str .= ($str ? ', ' : '') . ob_get_clean();
      }
      echo $str;
			?></div><?
     }
    ?>
  </div>
</div>
<?

$data = ob_get_clean();

?>
<div class="container-fluid">
  <?=navigate()?>
  <h1><?=$h1?></h1>
  <div class="content" style="padding-bottom:40px;">
    <?=$data?>
  </div>
</div>
<?
$content = ob_get_clean();
require('tpl/template.php');