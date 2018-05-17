<?	
@session_start();

//header('Content-Type: text/html; charset=utf-8');

// общие модули
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/db.php'); // коннектимся к базе
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php'); // разные полезные функции
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/tree.php'); // функции для дерева
// ------------------------------

// функции, константы, переменные
$title = set('title');
$keywords = set('keywords');
$description = set('description');
$script = basename($_SERVER['SCRIPT_FILENAME']);
$cache = $const = array();

// модули специально для клиентской части
require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php'); //функции
// ------------------------------

/*if(!@$_SESSION['user'] && @$_COOKIE['inUser'])
{
	$user = explode('/',$_COOKIE['inUser']);
	setPriv($user[0],$user[1]);
}*/