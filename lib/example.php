<?php
 ini_set('display_errors',1);
 error_reporting(E_ALL);
 require_once('./api.php');

 $status_sms = '';
 $status     = '';

 if(isset($_COOKIE['I'],$_COOKIE['L'],$_COOKIE['P'])){ 	$yakoon = new SMSSender($_COOKIE['L'],$_COOKIE['P'],true);
 	$ret    = $yakoon->Status($_COOKIE['I']);
 	if($ret) $status_sms = 'Доставлено';
 	$status = $yakoon->getInfo();
 	unset($yakoon); }
 if(!empty($_POST['do'])){ 	 $yakoon = new SMSSender($_POST['login'],$_POST['pass']);
 	 $ret = $yakoon->Send(
 	 	$_POST['num'],
    	iconv('CP1251','UTF-8',$_POST['text']),
        $_POST['sender']
 	 );
 	 if(!$ret){ 	 	$status = $yakoon->getError();
 	 	setcookie('I','',time()-604800);
 	 	setcookie('L','',time()-604800);
 	 	setcookie('P','',time()-604800);
 	 }
 	 else{ 	 	setcookie('I',$ret,time()+604800);
 	 	setcookie('L',$_POST['login'],time()+604800);
 	 	setcookie('P',md5($_POST['pass']),time()+604800);
 	 	header('Location: /index.php');
 	 	exit; 	 } }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=Windows-1251">
  <title>SMS Sender</title>
 </head>
 <body>
  <h1>SMS Sender</h1>
  <h3 style='color:#FF0000;'><?=$status?></h3>
  <h3 style='color:#009900;'><?=$status_sms?></h3>
  <form action='/index.php' method='post'>
  <table border='1' cellpadding='2' cellspacing='0'>
  	<tr><td><b>Логин:</b></td><td><input name='login' type='text' value='W3M'></td></tr>
  	<tr><td><b>Пароль:</b></td><td><input name='pass' type='password' value=''></td></tr>
  	<tr><td>Номер:</td><td><input name='num' type='text' value=''></td></tr>
  	<tr><td>Отправитель:</td><td><input name='sender' type='text' value='W3M'></td></tr>
  	<tr><td colspan='2'>Текст:</td></tr>
  	<tr><td colspan='2'><textarea name='text' rows='5' cols='26'></textarea></td></tr>
  	<tr><td colspan='2' align='center'><input name='do' type='submit' value='Send'></td></tr>
  </table>
  </form>
 </body>
</html>