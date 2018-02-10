<?	
@session_start();

//header('Content-Type: text/html; charset=utf-8');

// общие модули
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/db.php'); // коннектимся к базе
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/utils.php'); // разные полезные функции
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/tree.php'); // функции для дерева
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/advanced/advanced.php'); // "навороты" к сайту
// ------------------------------

// функции, константы, переменные
$title = set('title');
$keywords = set('keywords');
$description = set('description');
$script = basename($_SERVER['SCRIPT_FILENAME']);
$cache = $const = array();
$index = $_SERVER['REQUEST_URI']=='/';

// модули специально для клиентской части
require($_SERVER['DOCUMENT_ROOT'].'/inc/spec.php'); //функции
// ------------------------------

/*if(!@$_SESSION['user'] && @$_COOKIE['inUser'])
{
	$user = explode('/',$_COOKIE['inUser']);
	setPriv($user[0],$user[1]);
}*/

// определяем количество пользователей на сайте
online();

// СТАТИСТИКА ПОСЕЩЕНИЙ САЙТА
if(!getField("SELECT COUNT(*) FROM {$prx}users_visit WHERE ip='{$_SERVER['REMOTE_ADDR']}' AND `date`='".date('Y-m-d')."'"))
	update('users_visit',"`date`=NOW(),ip='{$_SERVER['REMOTE_ADDR']}'");