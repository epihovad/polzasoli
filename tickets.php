<?
ini_set('display_errors',1);
require('inc/common.php');

$ticket = array();
if($link = clean($_GET['link'])){
  if(!$ticket = getRow("SELECT * FROM {$prx}tickets WHERE link = '{$link}' AND status = 1")){
		header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit;
  }
}

ob_start();

// ---------------------------- страница абонемента
if($ticket){

	$id = $ticket['id'];

	// -------------------- TITLE + КЛЮЧЕВЫЕ СЛОВА
	$title = $ticket['name'];
	foreach(array('title','keywords','description') as $val)
		if($ticket[$val]) $$val = $ticket[$val];

	$h1 = $ticket['h1'] ?: $ticket['name'];

	$navigate = '<a href="/tickets/">Услуги и цены</a>';

	?>
  <style>
    #ticket-page { margin:0 auto;}
    #ticket-page .col-img { display:table-cell; padding:0 40px 40px 0; vertical-align:top;}
    #ticket-page .col-info { display:table-cell; padding:0 0 40px 0; vertical-align:top;}
    #ticket-page .col-info .who { font-weight:700; color:#2292ab;}
    #ticket-page .col-info .who b { color:#333;}
    #ticket-page .ticket-disease { padding:10px; background-color:#f1f6fa; margin:20px 0 10px;}
    #ticket-page .ticket-disease div { font-weight:700; padding-bottom:10px;}
    #ticket-page .ticket-disease a { color:#2292ab; }
    #ticket-page .ar-price * { vertical-align:middle; position:relative;}
    #ticket-page #buy i { padding:0 10px 0 0; }
    #ticket-page .old-price { display:inline-block; margin-right:20px; color: #666; font-size: 22px; text-decoration:line-through;}
    #ticket-page .old-price span { font-size:16px;}
    #ticket-page .price { display:inline-block; margin-right:20px; font-size: 36px;}
    #ticket-page .price span { font-size:24px;}
  </style>

  <div class="container-fluid" style="padding-bottom:40px">
		<?=navigate()?>
    <h1><?=$h1?></h1>

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
            ?><a href="/diseases/<?=$arr['link']?>.htm"><?=$arr['name']?></a><?
            $str .= ($str ? ', ' : '') . ob_get_clean();
          }
          echo $str;
          ?></div><?
        }
        ?>
        <div class="ar-price">
          <div class="old-price"><?=$ticket['old_price']?> <span>руб.</span></div>
          <div class="price"><?=$ticket['price']?> <span>руб.</span></div>
          <button id="buy" class="btn btn-warning"><i class="fas fa-shopping-cart"></i>Купить</button>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
    <a href="" class="back" rel="nofollow"><i class="fas fa-arrow-left"></i>назад</a>
  </div>
	<?

	// --------------------- Блок Отзывы
	reviews();
	// --------------------- Блок бронирования
	bron();
	// --------------------- FAQ
	FAQ();

}
// ---------------------------- список абонементов
else {

  $mainID = 4;

  $h1 = 'Услуги и цены';

	$navigate = '<a href="/tickets/">Услуги и цены</a>';

  ?>
  <div class="container-fluid" style="padding-bottom:40px">
    <?=navigate()?>
    <h1><?=$h1?></h1>

    <div id="tickets">
      111
    </div>
    <a href="" class="back" rel="nofollow">назад</a>
  </div>
  <?

}

$content = ob_get_clean();
require('tpl/template.php');