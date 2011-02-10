<?php
class CDB{
	
	var $querynum = 0;	//当前页面进程查询数据库的次数
	var $dblink;	//数据库链接资源
		
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
	}

	//选择数据库
	function select_db($dbname){
	}
		
	//执行数据库
	function query($sql){
	}

	//转义字符
	function escape($string){
	}

	//从数据表table_name中取出一条记录，满足条件：字段名为field_name的字段，其值为value
	function fetch($table_name, $field_name, $value){
	}
		
	//从数据表table_name中取出所有符合条件condition的记录
	function get($table_name, $condition){
	}

	//向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	function insert($table_name, $data){
	}

	//更新数据表table_name中的id为id_value的记录，data是一个关联数组，键名为字段名，值为字段的值
	function query_update($table_name, $data){
	}

	//具有可变参数个数的函数，类似于sprintf，fsql定义了据格式，v1, v2等变量定义了要替换的值，然后将替换后的字符串作为数据库查询进行执行
	function queryf(){
	}
		
	//关闭链接
	function close(){
	}
}
?>
