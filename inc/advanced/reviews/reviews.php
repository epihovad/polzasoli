<?
if(isset($_GET['action']) && $_GET['action']=='save')
{
	@session_start();
	require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php');
	require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php');
	require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php');
	
	foreach($_REQUEST as $k=>$v)
		$$k = clean($v);
	
	$score = (int)$score;
	
	$errors = array();
		
	if($_SESSION['admin'])
	{
		if(!$text) $errors[] = '.textarea';
		
		$set = "`id_obj`='{$id_obj}',`tbl`='{$tbl}',`id_parent`='{$id_parent}',`date`=NOW(),`text`='{$text}',`score`='{$score}',`status`='1'";
	}
	else
	{
		if(!$name) $errors[] = 'input[name="name"]';
		if(!check_mail($mail)) $errors[] = 'input[name="mail"]';
		if(!$text) $errors[] = '.textarea';
		$kod = (int)$_REQUEST['kod'];
		if($kod!=$_SESSION['number_test']) $errors[] = 'input[name="kod"]';
		
		$set = "`id_obj`='{$id_obj}',`tbl`='{$tbl}',`id_parent`='{$id_parent}',`date`=NOW(),`name`='{$name}',`mail`='{$mail}',`text`='{$text}',`score`='{$score}',`status`='0'";
	}
	
	if($errors){ ?><script>top.$('#reviews').find('<?=implode(',',$errors)?>').addClass('error');</script><? exit; }
	
	$alert = '';
	if(!$id = update('reviews',$set)) $alert = 'Во время сохранения данных произошла ошибка.<br>Администрация сайта приносит Вам свои извинения.<br>Мы уже знаем об этой проблеме и работаем над её устранением.';
	else $alert = 'Спасибо! Ваш отзыв успешно отправлен.<br />Мы обязательно опубликуем его<br />после проверки модератором.';
	
	if($id && !$_SESSION['admin'])
	{
		// мылим
		$tema = 'отзыв';
		$text = "<b>Имя</b>: {$_REQUEST['name']}<br /><b>E-mail</b>: {$_REQUEST['mail']}<br /><b>Оценка</b>: {$score}<br /><b>Сообщение</b> {$_REQUEST['text']}";
		mailTo(set('client_mail'),$tema,$text,$_SERVER['HTTP_HOST']);
		// журнал
		update("log","`date`=NOW(),text='отзыв',link='reviews.php?red={$id}'");
	}
	
	?><script>top.$(document).jAlert('show','alert','<?=$alert?>',function(){top.location.href=top.location.href});</script><?
	exit;
}
/*
<link rel="stylesheet" href="/inc/advanced/reviews/reviews.css" type="text/css" />
<script type="text/javascript" src="/inc/advanced/reviews/reviews.js"></script>
$reviews = getTree($q);
*/
function reviews($id_obj,$reviews=array(),$tbl='goods')
{
	?><div id="reviews"><?
	if($count_reviews = sizeof($reviews))
	{
		$n=1;
		foreach($reviews as $vetka)
		{
			$row = $vetka['row'];
			$level = $vetka['level'];
			$id = $row['id'];
			
			$padding = $level ? ' style="padding-left:'.($level*20).'px"' : '';
			
			$name = $row['id_users'] ? gtv('users','name',$row['id_users']) : ($row['name'] ? $row['name'] : 'Администратор')
			
			?>
			<div class="rvw"<?=$padding?> cid="<?=$id?>">
				<div class="rvw-name left"><?=$name?></div>
        <div class="rvw-date left"><?=date('d.m.Y',strtotime($row['date']))?></div>
        <div class="rvw-ans left"><a href="">ответить</a></div>
        <div class="clear"></div>
        <? if($row['score']){ ?><div class="srating mini" active="0" score="<?=$row['score']?>"></div><? } ?>
				<?=nl2br($row['text'])?>
			</div>
			<?
			if($n++<$count_reviews){ ?><div class="sep"></div><? }
		}
		?><div class="clear" style="padding-bottom:20px"></div><?
	}
	else
	{
		?><div style="padding-bottom:20px">Ваш отзыв может быть первым!</div><?
	}
	?>
	<form action="/inc/advanced/reviews/reviews.php?action=save&id_obj=<?=$id_obj?>&tbl=<?=$tbl?>" method="post" target="ajax">
	<h2 id="addReview" style="margin-bottom:10px; float:left;">Добавьте отзыв:</h2>
  <div class="clear"></div>
	<table>
  	<?
		if(!$_SESSION['user'] && !$_SESSION['admin'])
		{
			?>
			<tr><th>Ваше имя:</th><td><input type="text" name="name"></td></tr>
			<tr><th>E-mail:</th><td><input type="text" name="mail"></td></tr>
     	<?
		}
		?>
    <tr>
			<th>Ваша оценка:</th>
      <td colspan="3"><div class="srating" active="1" name="score" score="0"></div></td>
		</tr>
		<tr>
    	<th>Отзыв:</th>
			<td colspan="3" style="padding-top:5px">
      	<div class="who">
        	<input type="hidden" name="id_parent" value="0" />
          <div class="left">Ответ для: <span></span></div>
          <div class="left"><a href="" class="remove inline" title="удалить"></a></div>
          <div class="clear"></div>
        </div>
				<div class="textarea" style="width:250px"><textarea name="text"></textarea></div>
			</td>
		</tr>
    <?
		if(!$_SESSION['user'] && !$_SESSION['admin'])
		{
			?>
			<tr>
        <th>Введите сумму чисел:</th>
        <td colspan="3">
        	<div class="left"><img id="captcha" src="/captcha/"></div>
          <div class="left"><input type="text" name="kod" class="numer" style="text-align:center" maxlength="3" /></div>
          <div class="left" style="margin:3px 0 0 10px"><a href="" onclick="update_captcha();return false;">обновить</a></div>
        </td>
      </tr>
     	<?
		}
		?>
    <tr><td colspan="4" style="padding-top:10px" align="right"><input type="button" class="btn corner" value="Отправить" /></td></tr>
	</table>
	</form>
  </div>
	<?
}
?>