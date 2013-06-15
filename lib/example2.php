<?php
$yakoon = new SMSSender('login', 'pass');
 	 $ret = $yakoon->Send(
 	 	'+79101231221',//Получатель
    	'Текст',//Сообщение в uft-8
        'sender'//Отправитель (11 символов)
 	 );