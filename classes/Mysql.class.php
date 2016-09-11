<?php
class Mysql{
	private $host;
	private $name;
	private $pass;
	private $table;
	private $ut; 
	private $conn; 
	public  $countsql=0;//执行sql语句
	public  $sqlarr=array();
	private $iudcount=0;//更新插入删除条数
	private $iudarr=array();
	private $tran=false;//默认没有启动事务
	public  $nowsql;//当前执行SQL
	//初始化类
	function __construct($config){
		$this->host=$config['host']; //主机名
		$this->table=$config['dbname'];  //数据库名
		$this->name=$config['dbuser']; //登录名称
		$this->pass=$config['dbass']; //登录密码
		$this->ut=$config['charset']; //编码方式
		$this->connect(); 
	} 

	//类的销毁
	function __destruct(){
		if($this->conn){
			$this->tranend();
			$this->close();
		}
	}
	
	//链接数据库
	private function connect(){
		$this->conn=mysql_connect($this->host,$this->name,$this->pass) or die ($this->error()); //连接服务器
		mysql_select_db($this->table,$this->conn) or die('没有该数据库：'.$this->table); 
		$this->query("SET NAMES '$this->ut'"); //编码
		mysql_query("set character_set_results=utf8");
	} 
	
	function query($sql){
		$sql=trim($sql);
		$this->countsql++;//统计SQL语句
		$this->sqlarr[]=$sql;
		$this->nowsql=$sql;
		return mysql_query($sql,$this->conn);
	} 
	
	//读取一行
	function getone($table,$where,$fields='*'){//获取某一行
		$sql="select $fields from $table where $where limit 1";
		$res=$this->query($sql);
		if($res){
			$row=$this->fetch_array($res);
			return $row;
		}else{
			return false;
		}
	}
	
	//读取全部
	function getall($sql){
		$res=$this->query($sql);
		if($res){
			$arr=array();
			while($row=$this->fetch_array($res)){
				$arr[]=$row;
			}
			return $arr;
		}else{
			return false;
		}
	}
	
	//读取某行某字段的
	function getmou($table,$fields,$where){
		$sql="select $fields from $table where $where";
		$res=$this->query($sql);
		if($res){
			$row=mysql_fetch_row($res);
			return $row[0];
		}else{
			return false;
		}
	}	
	
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}
	
	function insert_id(){
		return mysql_insert_id($this->conn);
	}
	
	
	//启用事务
	private function tranbegin($sql){
		$this->iudcount++;
		if(!$this->tran){
			$this->query('BEGIN');
			$this->tran=true;
		}
		$rsa=$this->query($sql);
		$this->iudarr[]=$rsa;
		return $rsa;
	}
	
	//事务结束
	private function tranend(){
		if($this->tran){
			if(!$this->backsql()){
				$this->query('ROLLBACK');//回滚
			}else{
				$this->query('COMMIT');//提交事务
			}
			$this->query('END');
		}
		$this->tran=false;
	}
	
	//判断插入更新删除sql语句是否有错
	function backsql(){
		$subt=true;
		foreach($this->iudarr as $tra){
			if(!$tra){
				$subt=false;//有错误
				break;
			}
		}	
		return $subt;	
	}
	
	function insert($table,$name,$values,$sel=false){
		$sql="insert into `$table` ($name) ";
		if(!$sel){
			$sql.="values($values)";
		}else{
			$sql.=$values;
		}
		return $this->tranbegin($sql);
	}
	
	function update($table,$content,$where){
		$sql="update `$table` set $content where $where ";
		return $this->tranbegin($sql);
	}	
	function delete($table,$where){
		$sql="delete from `$table` where $where ";
		return $this->tranbegin($sql);
	}
	
	//返回总条数
	function num_rows($sql){
		return mysql_num_rows($this->query($sql));
	}
	
	//返回总条数
	function rows($table,$where,$rowtype='count(*)'){
		return $this->getmou($table,$rowtype,$where);
	}	
	
	//返回总字段数
	function num_fields($sql){ 
		return mysql_num_fields($this->query($sql));
	}
	
	//返回所有数据库的表
	function getalltable(){
		@$result = mysql_list_tables($this->table);
		while($row = mysql_fetch_row($result)) {
			$arr[]=$row[0];
		}	
		return $arr;
	}
	
	//返回表所有字段
	function getallfields($table){
		$sql='select * from `'.$table.'`';
		$row=$this->query($sql);
		$fieldscount=$this->num_fields($sql);
		for($i=0;$i<$fieldscount;$i++){
			$arr[]=mysql_field_name($row,$i);
		}
		return $arr;
	}
	
	function error(){
		return mysql_error();
	}
	
	function close(){
		return mysql_close();
	}	
}

?>