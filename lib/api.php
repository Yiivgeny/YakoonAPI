<?php
 DEFINE('SMS_FORMAT_ENGLISH',1);
 DEFINE('SMS_FORMAT_UNICODE',2);

 DEFINE('SMS_TYPE_NORMAL'   ,0);
 DEFINE('SMS_TYPE_FLASH'    ,1);
 DEFINE('SMS_TYPE_WAPPUSH'  ,2);

 DEFINE('SMS_STATUS_WAITING'      ,1);
 DEFINE('SMS_STATUS_PROCESSING'   ,2);
 DEFINE('SMS_STATUS_SCHEDULED'    ,3);
 DEFINE('SMS_STATUS_CANCELED'     ,4);
 DEFINE('SMS_STATUS_FAILED'       ,5);
 DEFINE('SMS_STATUS_ROUTING_ERROR',6);
 DEFINE('SMS_STATUS_CREDIT'       ,7);
 DEFINE('SMS_STATUS_UNKNOWN'      ,8);
 DEFINE('SMS_STATUS_DELIVERED'    ,9);

 class SMSSender extends yakoon_api{ 	public    $Charset = SMS_FORMAT_UNICODE;
 	public    $Type    = SMS_TYPE_NORMAL;

 	protected $LastId  = 0;
 	protected $Info    = array(
 		'Balance' => 0,
 		'Name'    => '',
 		'Surname' => '',
   		'Country' => '',
        'Email'   => '',
        'Mobile'  => ''
 	);
 	public    function __construct($login, $pwd, $hash = false){
		parent::__construct($login,$pwd,$hash);
		$this->Profile();
		if(parent::getErrorCode()%100 != 0) throw new Exception(parent::getError(),parent::getErrorCode());
		return $this;
	}

	public    function getInfo($field = 'Balance'){		return array_key_exists($field,$this->Info)?$this->Info[$field]:false;	}
	public    function Profile(){		$ret = parent::Action('Login');
		if(!$ret) return false;
		$this->Info = array(
			'Balance' => intval($ret[0]),
 			'Name'    => $ret[1],
 			'Surname' => $ret[2],
   			'Country' => $ret[3],
        	'Email'   => $ret[4],
        	'Mobile'  => $ret[5]
		);
		return true;	}    public    function Send($number,$data,$sender = 'Yakoon',$delay = 0,$notify = false){    	if($this->getInfo() == 0){    		$this->Status = 3176;    	 	return false;
    	}    	$req['Sender']       = $sender;
    	$req['Recipient']    = $number;
    	$req['Template']     = 0;
    	$req['Content']      = $data;
    	$req['SendOn']       = ($delay > 0)?$delay:'';
    	$req['Notification'] = intval($notify);
    	$req['Format']       = 0;
    	switch($this->Type){
    		case 1:
    			$req['Format']  = 2;
    		case 0:
    			$req['Format'] += $this->Charset;
    			break;
    		case 2:
    			$req['Format']  = $this->Type;
    			break;
       	}
       	$ret = parent::Action('Send',$req);
       	if($ret == false) return false;
       	$this->Info['Balance'] = intval($ret[0]);
       	return $ret[1];    }
    public    function Status($id = false,$simple = true){    	if($id === false) $id = $this->LastId;
    	if($id === 0)     return false;
    	$req['IDSms'] = $id;
    	$req['IDInt'] = false;
    	$ret = parent::Action('Status',$req);
    	if($ret == false) return false;
    	if($simple) return ($ret[0][3] == SMS_STATUS_DELIVERED)?true:false;
    	else        return $ret[0][3];    }
 }

 class yakoon_api{ 	//Constants 	const Version = 'Yakoon API Class/0.1.1 (24 Oct 2007; http://public.w3m.ru/)';
	//Variables
	protected static $statuses = array(
		1100 => 'Registration done',
		1151 => 'Other database error',
		1170 => 'Already exist: Username',
		1171 => 'Already exist: Email',
		1172 => 'Already exist: Mobile',
		1101 => 'Wrong data submitted: Username',
		1104 => 'Wrong data submitted: FirstName',
		1105 => 'Wrong data submitted: LastName',
		1106 => 'Wrong data submitted: Country',
		1107 => 'Wrong data submitted: Email',
		1108 => 'Wrong data submitted: Mobile',
		1109 => 'Wrong data submitted: Reseller',
		1110 => 'Wrong data submitted: Culture',
		1199 => 'IP blocked',
		1200 => 'Activation done',
		1251 => 'Other database error',
		1254 => 'Access denied: Or account already activated',
		1260 => 'Activation done: No credits available on reseller account',
		1261 => 'Activation done: Voucher sending fault',
		1282 => 'Not exist: Mobile number to send voucher',
		1201 => 'Wrong data submitted: Username',
		1202 => 'Wrong data submitted: Password',
		1211 => 'Wrong data submitted: Activation',
		1299 => 'IP blocked',
		1300 => 'Updated',
		1351 => 'Other database error',
		1353 => 'Access denied: Account is not activated',
		1371 => 'Already exist: Email',
		1301 => 'Wrong data submitted: Username',
		1302 => 'Wrong data submitted: Password',
		1303 => 'Wrong data submitted: Password1',
		1307 => 'Wrong data submitted: Email',
		1310 => 'Wrong data submitted: Culture',
		1399 => 'IP blocked',
		1400 => 'Password updated',
		1451 => 'Other database error',
		1453 => 'Access denied: Account is not activated',
		1401 => 'Wrong data submitted: Username',
		1407 => 'Wrong data submitted: Email',
		1410 => 'Wrong data submitted: Culture',
		1499 => 'IP blocked',
		1500 => 'Login',
		1551 => 'Other database error',
		1553 => 'Access denied: Account is not activated',
		1552 => 'Access denied',
		1501 => 'Wrong data submitted: Username',
		1502 => 'Wrong data submitted: Password',
		1599 => 'IP blocked',
		1600 => 'Email changed',
		1651 => 'Other database error',
		1655 => 'Access denied: Or email already confirmed',
		1601 => 'Wrong data submitted: Username',
		1602 => 'Wrong data submitted: Password',
		1612 => 'Wrong data submitted: Confirm',
		1699 => 'IP blocked',
		1700 => 'Registration email updated',
		1751 => 'Other database error',
		1752 => 'Access denied',
		1771 => 'Already exist: Email',
		1701 => 'Wrong data submitted: Username',
		1702 => 'Wrong data submitted: Password',
		1707 => 'Wrong data submitted: Email',
		1710 => 'Wrong data submitted: Culture',
		1799 => 'IP blocked',
		2100 => 'Generation done',
		2153 => 'Access denied: Account is not activated',
		2175 => 'No credits: To generate vouchers',
		2151 => 'Other database error',
		2101 => 'Wrong data submitted: Username',
		2102 => 'Wrong data submitted: Password',
		2113 => 'Wrong data submitted: Numbers',
		2114 => 'Wrong data submitted: Credits',
		2199 => 'IP blocked',
		2200 => 'Voucher redeemed',
		2280 => 'Not exist: Username',
		2285 => 'Not exist: Voucher',
		2251 => 'Other database error',
		2201 => 'Wrong data submitted: Username',
		2215 => 'Wrong data submitted: Voucher',
		2299 => 'IP blocked',
		3100 => 'Message submitted',
		3151 => 'Other database error',
		3153 => 'Access denied: Account is not activated',
		3176 => 'No credits: To send message',
		3181 => 'Not exist: Recipients',
		3101 => 'Wrong data submitted: Username',
		3102 => 'Wrong data submitted: Password',
		3116 => 'Wrong data submitted: SenderID',
		3117 => 'Wrong data submitted: Recipients',
		3118 => 'Wrong data submitted: Template',
		3119 => 'Wrong data submitted: Content',
		3120 => 'Wrong data submitted: Format',
		3121 => 'Wrong data submitted: dateSendOn',
		3122 => 'Wrong data submitted: Notification',
		3199 => 'IP blocked',
		3200 => 'Sended',
		3251 => 'Other database error',
		3253 => 'Access denied: Account is not activated',
		3284 => 'Not exist: SMS to status',
		3201 => 'Wrong data submitted: Username',
		3202 => 'Wrong data submitted: Password',
		3223 => 'Wrong data submitted: IDSms',
		3224 => 'Wrong data submitted: IDInt',
		3299 => 'IP blocked',
		3300 => 'Cancelled',
		3351 => 'Other database error',
		3353 => 'Access denied: Account is not activated',
		3383 => 'Not exist: SMS to cancel',
		3301 => 'Wrong data submitted: Username',
		3302 => 'Wrong data submitted: Password',
		3323 => 'Wrong data submitted: IDSms',
		3399 => 'IP blocked',
		3400 => 'New messages arrived',
		3451 => 'Other database error',
		3453 => 'Access denied: Account is not activated',
		3486 => 'Not exist: Inbox SMS',
		3401 => 'Wrong data submitted: Username',
		3402 => 'Wrong data submitted: Password',
		3425 => 'Wrong data submitted: IDInc',
		3499 => 'IP blocked',
		3500 => 'Updated',
		3551 => 'Other database error',
		3553 => 'Access denied: Account is not activated',
		3501 => 'Wrong data submitted: Username',
		3502 => 'Wrong data submitted: Password',
		3525 => 'Wrong data submitted: IDInc',
		3526 => 'Wrong data submitted: Readed',
		3527 => 'Wrong data submitted: Deleted',
		3599 => 'IP blocked'
	);
 	protected static $actions = array(
   		'Registration' => array(
   			'http://sms.yakoon.com/customers.asmx/Registration',array(
       			'FirstName' => 0,
				'LastName'  => 0,
				'Country'	=> 1,
				'Email'	    => 1,
				'Mobile'	=> 1,
				'Reseller'	=> 1,
				'Culture'	=> 1
			)
   		),
   		'Activation'   => array(
   			'http://sms.yakoon.com/customers.asmx/Activation',array(
       			'Activation' => 1
			)
   		),
   		'Update'       => array(
   			'http://sms.yakoon.com/customers.asmx/Update',array(
       			'Password1' => 0,
				'EmailNew'  => 0,
				'Culture'	=> 1
			)
   		),
   		'Reset'        => array(
   			'http://sms.yakoon.com/customers.asmx/Reset',array(
				'Email'	    => 1,
				'Culture'	=> 1
			)
   		),
   		'Login'        => array(
   			'http://sms.yakoon.com/customers.asmx/Login',array()
   		),
   		'Confirm'      => array(
   			'http://sms.yakoon.com/customers.asmx/Confirm',array(
       			'Confirm' => 1
			)
   		),
   		'Reg_Upd'      => array(
   			'http://sms.yakoon.com/customers.asmx/Registration',array(
				'EmailNew'	=> 1,
				'Culture'	=> 1
			)
   		),
   		'Generate'     => array(
   			'http://sms.yakoon.com/vouchers.asmx/Generate',array(
       			'Number'   => 1,
				'Credits'  => 1
			)
   		),
   		'Redeem'       => array(
   			'http://sms.yakoon.com/vouchers.asmx/Redeem',array(
       			'Voucher' => 1
			)
   		),
   		'Send'         => array(
   			'http://sms.yakoon.com/sms.asmx/Send',array(
       			'Sender'       => 1,
				'Recipient'    => 1,
				'Template'	   => 1,
				'Content'      => 1,
				'Format'       => 0,
				'SendOn'	   => 1,
				'Notification' => 0
			)
   		),
   		'Status'       => array(
   			'http://sms.yakoon.com/sms.asmx/Status',array(
       			'IDSms' => 1,
				'IDInt' => 1
			)
   		),
   		'Cancel'       => array(
   			'http://sms.yakoon.com/sms.asmx/Cancel',array(
       			'IDSms' => 1
    		)
   		),
   		'Inbox'        => array(
   			'http://sms.yakoon.com/sms.asmx/Inbox',array(
       			'IDInc' => 1
			)
   		),
   		'InboxUpdate'  => array(
   			'http://sms.yakoon.com/sms.asmx/InboxUpdate',array(
       			'Readed'  => 1,
				'Deleted' => 1
			)
   		)
   	);
 	protected $Login;
 	protected $Pwd;
 	protected $Status;
	//OOP
	public    function __construct($login, $pwd, $hash = false){		$this->Login = $login;
		$this->Pwd   = $hash?$pwd:md5($pwd);
		return $this;	}
	public    function getErrorCode(){		return $this->Status;	}
	public    function getError($code = false){		if($code === false) $code = $this->Status;
		return array_key_exists($code,self::$statuses)?self::$statuses[$code]:'';	} 	//Zero-Level Functions
 	protected static function getQueryString($arr){ 		$ret = '';
 		foreach($arr AS $key => &$val) $ret .= "{$key}={$val}&";
 		if($ret) $ret = substr($ret,0,-1);
 		return $ret; 	} 	protected static function getRequest($address,$data = array()){ 		if($ch = curl_init($address)){ 			$post = self::getQueryString($data); 			//Options
 			curl_setopt($ch, CURLOPT_HEADER         , 0);
 			curl_setopt($ch, CURLOPT_POST           , 1);
 			curl_setopt($ch, CURLOPT_FOLLOWLOCATION , 1);
 			curl_setopt($ch, CURLOPT_POSTFIELDS     , $post);
 			curl_setopt($ch, CURLOPT_USERAGENT      , self::Version);
 			curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
 			//End Options
 			$ret = curl_exec($ch);
 			if(curl_errno($ch) > 0) throw new Exception(curl_error($ch), curl_errno($ch));
 			curl_close($ch);
 		}
 		return $ret; 	}    protected function checkData($type,$data){    	if(!array_key_exists($type,self::$actions)) throw new Exception("Method {$type} not found",1);
    	$data = array_intersect_key($data, self::$actions[$type][1]);
    	foreach(self::$actions[$type][1] AS $key => &$val){    		if($val == 1 && !array_key_exists($key,$data)) throw new Exception("Required parameter '{$key}' not defined",1);    	}
    	$data['Username'] = $this->Login;
    	$data['Password'] = $this->Pwd;
    	return $data;    }
    //Low-Level Functions
    public    function Action($action,$data = array()){    	$data         = $this->checkData($action,$data);
     	$link         = self::$actions[$action][0];
     	$ret          = self::getRequest($link,$data);
     	if(!$ret)$ret = 'Empty';
     	if(!preg_match('#\<string xmlns="http:\/\/sms.yakoon.com\/literalTypes"\>(.+)\<\/string\>#',$ret,$match)) throw new Exception($ret,1);
     	$ret          = $match[1];
     	$ret          = explode(':',$ret,2);
     	$this->Status = $ret[0];
     	$arr          = explode(';',$ret[1]);
     	$data         = array();
     	if(sizeof($arr > 0)){     		foreach($arr AS $val){    			$tmp = explode(',',$val);
    			$data[] = sizeof($tmp)>1?$tmp:$tmp[0];
    		}
     	}
     	if($this->Status%100 != 0) return false;
     	return $data;    }
 }
?>