<?php
$yakoon = new SMSSender('login', 'pass');
 	 $ret = $yakoon->Send(
 	 	'+79101231221',//����������
    	'�����',//��������� � uft-8
        'sender'//����������� (11 ��������)
 	 );