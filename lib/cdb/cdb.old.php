<?php

class db_mysql
{
	var $querynum = 0; 	//µ±Ç°Ò³Ãæœø³Ì²éÑ¯ÊýŸÝ¿âµÄŽÎÊý
	var $dblink;	//ÊýŸÝ¿âÁŽœÓ×ÊÔŽ

	//ÁŽœÓÊýŸÝ¿â
	function connect($dbhost, $dbuser, $dbpw, $dbname = "", $dbcharset = 'utf-8', $pconnect = 0, $halt = true)
	{
		$func = emtpy($pconnect) ? 'mysql_connect':'mysql_pconnect';	//Èç¹û$pconnectÎª¿ÕÔòÊ¹ÓÃ$connect
		$this->dblink = @$func($dbhost, $dbuser, $dbpw);
		if($halt && !$this->dblink)		//µ±mysqlÁ¬œÓÊ§°Üµ÷ÓÃ$this->halt:ÏÔÊŸŽíÎóÐÅÏ¢
		{
			$this->halt("ÎÞ·šÁŽœÓÊýŸÝ¿â!");
		}
	}
	//ÉèÖÃ²éÑ¯×Ö·ûŒ¯
	mysql_query("SET character_set_connection={$dbcharset}, character_set_results = {$dbcharset}, character_set_client = utf-8",/* $this->dblink */);

	//Ñ¡ÔñÊýŸÝ¿â
	function select_db($dbname)
	{
		return mysql_select_db($dbname, $this->link);
	}
	//ÖŽÐÐÊýŸÝ¿â
	function query($sql)
	{
		$this->querynum++;
		return mysql_query($sql, $this->dblink);
	}

	//×ªÒå×Ö·û
	function escape($string) 
	{
		if(get_magic_quotes_gpc()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

	//ŽÓÊýŸÝ±ítable_nameÖÐÈ¡³öÒ»ÌõŒÇÂŒ£¬Âú×ãÌõŒþ£º×Ö¶ÎÃûÎªfield_nameµÄ×Ö¶Î£¬ÆäÖµÎªvalue
	function fetch($table_name, $field_name, $value)
	{
		$sql = select $field_name from $table_name where $field_name = $value;
		$result = mysql_query($sql);
		return mysql_fetch_object($result);
	}

	//ŽÓÊýŸÝ±ítable_nameÖÐÈ¡³öËùÓÐ·ûºÏÌõŒþconditionµÄŒÇÂŒ
	function get($table_name, $condition)
	{
		return select * from $table_name where $condition;
	}

	//ÏòÊýŸÝ±ítable_nameÖÐ²åÈëÒ»ÌõŒÇÂŒ£¬dataÊÇÒ»žö¹ØÁªÊý×é£¬ŒüÃûÎª×Ö¶ÎÃû£¬ÖµÎª×Ö¶ÎµÄÖµ
	function insert($table_name, $data)
       	{
		$q="INSERT INTO `".$this->pre.$table_name."` ";
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
		
	//žüÐÂÊýŸÝ±ítable_nameÖÐµÄidÎªid_valueµÄŒÇÂŒ£¬dataÊÇÒ»žö¹ØÁªÊý×é£¬ŒüÃûÎª×Ö¶ÎÃû£¬ÖµÎª×Ö¶ÎµÄÖµ
	function query_update($table_name, $data)
	{
		$q="UPDATE `".$this->pre.$table_name."` SET ";
	
		foreach($data as $key=>$val)
	       	{
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	
		return $this->query($q);
	}

	//ŸßÓÐ¿É±ä²ÎÊýžöÊýµÄº¯Êý£¬ÀàËÆÓÚsprintf£¬fsql¶šÒåÁËŸÝžñÊœ£¬v1, v2µÈ±äÁ¿¶šÒåÁËÒªÌæ»»µÄÖµ£¬È»ºóœ«Ìæ»»ºóµÄ×Ö·ûŽ®×÷ÎªÊýŸÝ¿â²éÑ¯œøÐÐÖŽÐÐ

	
	function queryf()
	{
		$pa = func_get_args();

		$data = $pa[0] array();			///$pa[0]  žñÊœ......???????
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

		$q = "SELECT `". rtrim($q, ', ') ."` from ".$this->pre.$table_name."` " . ' WHERE '.$where.';';
	}

	//¹Ø±ÕÁ¬œÓ
	function close()
	{
		return mysql_close($this->dblink);
	}


}
?>  


















