<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <meta name="keywords" content="<?=$keywords?>" />
  <meta name="description" content="<?=$description?>" />
  <meta name="viewport" content="user-scalable=no,width=device-width" />
  <title><?=$_SERVER['SERVER_NAME']?> - <?=$title?></title>

  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

  <script src="/js/jquery-3.1.1.min.js"></script>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="/css/animate.css" rel="stylesheet" media="screen">

  <link rel="stylesheet" href="/js/ui/jquery-ui.css" type="text/css">
  <script src="/js/ui/jquery-ui.min.js" type="text/javascript"></script>

  <script src="/js/utils.js"></script>

  <link rel="stylesheet" href="/js/jAlert/jAlert.css" type="text/css" />
  <script type="text/javascript" src="/js/jAlert/jquery.jAlert.min.js"></script>

  <link href="css/style.css" rel="stylesheet" media="screen">
  <link href="css/login.css" rel="stylesheet">

  <!--[if lt IE 9]>
  <script src="/js/html5shiv.js"></script>
  <script src="/js/respond.min.js"></script>
  <![endif]-->



</head>

<body>

<?=$content?>

<iframe name="ajax" id="ajax"></iframe>

</body>
</html>