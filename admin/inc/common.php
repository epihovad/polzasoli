<?
@session_start();
//ini_set('display_errors',1);
//error_reporting('E_ALL');

// доступ
if(!$_SESSION['admin']) { ?><script>top.location.href='login.php?action=loginc&urlback=<?=$_SERVER['REQUEST_URI']?>';</script><? exit; }

// модули специально для админки
require('spec.php'); // постоянные
require('func.php'); // переменные

// общие модули
require($_SERVER['DOCUMENT_ROOT'].'/inc/db.php'); // коннектимся к базе
require($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php'); // разные полезные функции
require($_SERVER['DOCUMENT_ROOT'].'/inc/tree.php'); // функции для дерева

// функции, константы, переменные
$title = 'Администрирование';
$script = basename($_SERVER['SCRIPT_FILENAME']);
$script_name = basename($_SERVER['SCRIPT_FILENAME'],'.php');