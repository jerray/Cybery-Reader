<?php
class CDB{
	
	var $querynum = 0;	//当前页面进程查询数据库的次数
	var $dblink = false;	//数据库链接资源
	var $dbhost = "";	//服务器
	var $dbuser = "";	//数据库名
	var $dbpw = "";		//密码
	var $dbname = "";	//数据库名

	var $insert_id = 0;


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
			$r =  mysql_query($sql);

			$id = mysql_insert_id();
			if($id)
				$this->insert_id = $id;

			return $r;
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
		if($result){
			while( ($row = mysql_fetch_array($result)) )	$rs[] = $row;
		}
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
			$this->insert_id = mysql_insert_id();
			return $this->insert_id;
		}
		else return false;
	}
	function insert_id()
	{
		return $this->insert_id;
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
	const NUM = 'd';
	const STR = 's';
	const RAW = 'r';
	const ESC = '%';

	function queryf()
	{
		$args = func_get_args();

		if( ($argCount = count($args)) == 0 )
			return false;

		$format = $args[0];
		$arg_pos = 1;
		$esc_pos = false;
		$v_pos = 0;

		$sql = '';

		while(true)
		{
			$esc_pos = strpos($format, CDB::ESC, $v_pos);
			if($esc_pos === false)
			{
				$sql .= substr($format, $v_pos);
				break;
			}

			$sql .= substr($format, $v_pos, $esc_pos - $v_pos);

			$esc_pos++;
			$v_pos = $esc_pos + 1;

			if($esc_pos == strlen($format))
			{// % 后面没有类型字符
				return false;
			}

			$v_char = $format{$esc_pos};

			if($v_char != CDB::ESC)
			{
				if($argCount <= $arg_pos)
				{// 参数个数不够
					return false;
				}
				$arg = $args[$arg_pos++];
			}

			switch($v_char){
			case CDB::NUM:
				$sql .= intval($arg);
				break;
			case CDB::STR:
				$sql .= $this->escape($arg);
				break;
			case CDB::RAW:
				$sql .= $arg;
			case CDB::ESC:
				$sql .= CDB::ESC;
				break;
			default: //非法的符号
				return false;
			}
		}

		$rs = $this->query($sql);

		if(is_bool($rs))
		{
			return $rs;
		}
		else
		{
			$r = array();
			while( ($row = mysql_fetch_array($rs)) )	$r[] = $row;

			return $r;
		}
	}
	
	//关闭链接
	function close(){
		return mysql_close();
	}
}
?>
