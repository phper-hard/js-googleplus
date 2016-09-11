<?php
date_default_timezone_set('PRC');
$config=array(
	'host'=>'localhost',
	'dbname'=>'fenxiang',
	'dbuser'=>'root',
	'dbass'=>'123456',
	'port'=>3306,
	'charset'=>'utf8',
);

require('./classes/Mysql.class.php');
$db=new Mysql($config);
// $sql="select * from et_friend f left join et_users u on f.fid_jieshou=u.user_id where f.fid_fasong=3";//我收听的 就是好友
// $res=$db->getall($sql);
// print_r($res);exit;
?>