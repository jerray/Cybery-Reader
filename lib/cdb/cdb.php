<?php
class CDB{
	
	var $querynum = 0;	//当前页面进程查询数据库的次数
	var $dblink;	//数据库链接资源
	var $dbhost = "";	//服务器
	var $dbuser = "";	//数据库名
	var $dbpw = "";		//密码
	var $dbname = "";	//数据库名
	var $numRows;		//返回数据数目
		
	//打印出错信息
	function halt($msg){
		$message = "<html>\n<head>\n" ; 
		$message .= "<meta content='text/html;charset=utf-8'>\n" ; 
		$message .= "</head>\n" ; 
		$message .= "<body>\n" ; 
		$message .= "数据库出错：".htmlspecialchars($msg)."\n" ; 
		$message .= "</body>\n" ; 
		$message .= "</html>" ; 
		echo $message ; 
		exit ; 
	}

	//链接数据库
	function connect($dbhost, $dbuser, $dbpw, $dbname ="", $dbcharset = 'utf-8', $pconnect = 0, $halt = true){
		$func = emtpy($pconnect) ? 'mysql_connect':'mysql_pconnect';	//如果$pconnect为空则使用$connect
		$this->dblink = @$func($dbhost, $dbuser, $dbpw);
		if($halt && !$this->dblink)		//当mysql连接失败调用$this->halt:显示错误信息
		{
			$this->halt("无法链接数据库!");
		}
	//设置查询字符集
		mysql_query("SET character_set_connection={$dbcharset}, character_set_results = {$dbcharset}, character_set_client = utf-8",/* $this->dblink */);
		$dbname && @mysql_select_db($dbname,$this->dblink) ;
	}

	//选择数据库
	function select_db($dbname){
		return mysql_select_db($dbname, $this->link);
	}
		
	//执行数据库
	function query($sql){
		$this->querynum++;
		return mysql_query($sql, $this->dblink);
	}

	//转义字符
	function escape($string){
		if(get_magic_quotes_gpc()){
			$string =stripslashes($string);		
		}
		return mysql_real_escape_string($string);
	}

	//从数据表table_name中取出一条记录，满足条件：字段名为field_name的字段，其值为value
	function fetch($table_name, $field_name, $value){
		$sql = "select $field_name from $table_name where $field_name";
		$result = @mysql_query($sql, $this->dblink);
		return mysql_fetch_object($this->result);
	}
		
	//从数据表table_name中取出所有符合条件condition的记录
	function get($table_name, $condition){
		$sql = "select * from $table_name where $condition";
		$this->result = @mysql_query($sql, $this->dblink);
		return mysql_fetch_array($this->result);
	}

	//向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	function insert($table_name, $data){
		$q="INSERT INTO `".$this->$table_name."` ";
		$v=''; $n='';
	
		foreach($data as $key=>$val)
	       	{
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			else $v.= "'".$this->escape($val)."', ";
		}
	
		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	
		if($this->query($q)){
			return mysql_insert_id();
		}
		else return false;
	}

	//更新数据表table_name中的id为id_value的记录，data是一个关联数组，键名为字段名，值为字段的值
	function query_update($table_name, $data){
		$q="UPDATE `".$this->$table_name."` SET ";
	
		foreach($data as $key=>$val)
	       	{
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	
		return $this->query($q);
	}

	//具有可变参数个数的函数，类似于sprintf，fsql定义了据格式，v1, v2等变量定义了要替换的值，然后将替换后的字符串作为数据库查询进行执行
	function queryf(){
		$pa = func_get_args();

		$data = $pa[0] array();			///$pa[0]  格式......???????
		for($i = 1; $i < func_num_args(); $i++)
		{
			$data[$i - 1] = $pa[$i];
		}
		foreach($data as $key=>$val)
	       	{
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}

		$q = "SELECT `". rtrim($q, ', ') ."` from ".$this->$table_name."` " . ' WHERE '.$where.';';
	}
		
	//关闭链接
	function close(){
		return mysql_close($this->dblink);
	}
}
?>
