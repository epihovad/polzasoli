<?
	@session_start();
	
	// доступ
	if(!$_SESSION['admin']) { ?><script>top.location.href='login.php?action=vhodc&urlback=<?=$_SERVER['REQUEST_URI']?>';</script><? exit; }

	// модули специально для админки
	require('spec.php'); // постоянные
	require('func.php'); // переменные
	// ------------------------------
	
	// общие модули
	require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php'); // коннектимся к базе
	require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php'); // разные полезные функции
	require($_SERVER['DOCUMENT_ROOT'].'/inc/tree.php'); // функции для дерева
	require($_SERVER['DOCUMENT_ROOT'].'/inc/advanced/advanced.php'); // "навороты" к сайту
	// ------------------------------
	
	// функции, константы, переменные
	$page_title = 'Администрирование';
	$script = basename($_SERVER['SCRIPT_FILENAME']);
	// ------------------------------	
?>