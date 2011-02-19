<?php
class CDB{
	
	var $querynum = 0;	//当前页面进程查询数据库的次数
	var $dblink = false;	//数据库链接资源
	var $dbhost = "";	//服务器
	var $dbuser = "";	//数据库名
	var $dbpw = "";		//密码
	var $dbname = "";	//数据库名


	//链接数据库
	function connect($dbhost, $dbuser, $dbpw, $dbname = ""){
		$this->dblink = mysql_connect($dbhost, $dbuser, $dbpw);
		if(!empty($dbname))
			mysql_select_db($dbname);
		return $this->dblink;
	}


	//选择数据库
	function select_db($dbname){
		return mysql_select_db($dbname);
	}
		
	//执行数据库
	function query($sql){
		if($this->dblink)
		{
			$this->querynum++;
			return mysql_query($sql);
		}else{
			return false;
		}
	}

	//转义字符
	function escape($string){
		if(get_magic_quotes_gpc()){
			$string =stripslashes($string);		
		}
		return mysql_real_escape_string($string);
	}

	/*
	 * @brief : 从数据表table_name中取出一条记录，满足条件：字段名为field_name的字段，其值为value
	 */
	function fetch($table_name, $field_name, $value = NULL){
		if($value == NULL)
			$sql = "SELECT * FROM `$table_name`;";
		else
			$sql = "SELECT * FROM `$table_name` WHERE `$field_name` = '$value';";
		$result = $this->query($sql);
		return mysql_fetch_array($result);
	}
		
	/*
	 * @brief : 从数据表table_name中取出所有符合条件condition的记录
	 */
	function get($table_name, $condition = NULL){
		if($condition == NULL)
			$sql = "SELECT * FROM `$table_name`;";
		else
			$sql = "SELECT * FROM `$table_name` WHERE $condition;";
		$result = $this->query($sql);

		$rs = array();
		while( ($row = mysql_fetch_array($result)) )	$rs[] = $row;
		return $rs;
	}

	/*
	 * @brief : 向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	 */
	function insert($table_name, $data){
		$q="INSERT INTO `".$table_name."` ";
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

	/*
	 * @brief : 更新数据库条目
	 * @desc : 更新数据表table_name中的id为id_value的记录，data是一个关联数组，键名为字段名，值为字段的值
	 * @return true or false
	 */
	function update($table, $id, $data)
	{
		$sql = "UPDATE `$table` SET ";

		if(is_array($data) && count($data) > 0):
			foreach($data as $field => $value):
				$sql .= "`$field` = '". $this->escape($value) ."',";
			endforeach;

			$sql = rtrim($sql, ', ') . " WHERE `id` = '$id'";

			return $this->query($sql);
		else:
			return false;
		endif;

	}
	/*
	 * @brief : 删除$table中id为$id的行
	 */
	function delete($table, $id)
	{
		$sql = "DELETE FROM `$table` WHERE `id` = '$id'";

		return $this->query($sql);
	}
	//具有可变参数个数的函数，类似于sprintf，fsql定义了数据格式，v1, v2等变量定义了要替换的值，然后将替换后的字符串作为数据库查询进行执行
	function queryf(){
			$pa = func_get_args();
			$sql = call_user_func_array("sprintf", $pa);
			$result = mysql_query($sql);
			if(is_bool($result))
				return $result;
			else
				return mysql_fetch_array($result);			
	}
		
	//关闭链接
	function close(){
		return mysql_close();
	}
}
?>
