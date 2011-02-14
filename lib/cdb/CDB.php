<?php
class CDB{
	
	var $querynum = 0;	//当前页面进程查询数据库的次数
	var $dblink = false;	//数据库链接资源
	var $dbhost = "";	//服务器
	var $dbuser = "";	//数据库名
	var $dbpw = "";		//密码
	var $dbname = "";	//数据库名


	//链接数据库
	function connect($dbhost, $dbuser, $dbpw, $dbname =""){
		$dblink = mysql_connect($dbhost, $dbuser, $dbpw, $dbname);
		return $dblink;
	}


	//选择数据库
	function select_db($dbname){
		return mysql_select_db($dbname, $dblink);
	}
		
	//执行数据库
	function query($sql){
		$querynum++;
		return mysql_query($sql, $dblink);
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
		$result = @mysql_query($sql, $dblink);
		return mysql_fetch_object($result);
	}
		
	//从数据表table_name中取出所有符合条件condition的记录
	function get($table_name, $condition){
		$sql = "select * from $table_name where $condition";
		$result = @mysql_query($sql, $dblink);
		return mysql_fetch_array($result);
	}

	//向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	function insert($table_name, $data){
		$q="INSERT INTO `".$table_name."` ";
		$v=''; $n='';
	
		foreach($data as $key=>$val)
	       	{
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			else $v.= "'".escape($val)."', ";
		}
	
		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	
		if(mysql_query($q)){
			return mysql_insert_id();
		}
		else return false;
	}

	//更新数据表table_name中的id为id_value的记录，data是一个关联数组，键名为字段名，值为字段的值  e.g. query_update(boss, $data, "`boss_id` = 6");
	function query_update($table_name, $data, $where){
		$q="UPDATE `".$table_name."` SET ";
	
		foreach($data as $key=>$val)
	       	{
			
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".escape($val)."', ";
		}
		$q = rtrim($q, ', ').';';
	
		return mysql_query($q);
	}

	//具有可变参数个数的函数，类似于sprintf，fsql定义了数据格式，v1, v2等变量定义了要替换的值，然后将替换后的字符串作为数据库查询进行执行
	function queryf(){
		$pa = func_get_args();
		$args_num = func_num_args();		

	}
		
	//关闭链接
	function close(){
		return mysql_close($dblink);
	}
}
?>



