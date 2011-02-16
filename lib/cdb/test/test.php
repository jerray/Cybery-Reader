<?php


function connect($dbhost, $dbuser, $dbpw){

 return	$dblink =  mysql_connect($dbhost, $dbuser, $dbpw);
	
		
}



function select_db($dbname){
		return mysql_select_db($dbname);
	}


function escape($string){
		if(get_magic_quotes_gpc()){
			$string =stripslashes($string);		
		}
		return mysql_real_escape_string($string);
	}

function query_update($table_name, $data, $where)
	{
		$q="UPDATE `".$table_name."` SET ";

		foreach($data as $key=>$val)
	       	{
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".escape($val)."', ";
		}
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';


echo '</p>';
echo "$q";
echo '</p>';
		return mysql_query($q);
	}


function fetch($table_name, $field_name, $value){
		$sql = "select $field_name from $table_name where $field_name = $value; ";


echo $sql;
echo '</p>';

		$result = @mysql_query($sql);
if(!$result)
echo "wocaole";
		return mysql_fetch_object($result);
	}


$result = connect(localhost, root, shoutao);

if($result)
	echo "OK~";
else
	echo "ffuck";

echo '</p>';

$sql = select_db(wow);
if($sql)
	echo "o3o";
else
	echo "f3f";

echo '</p>';

$dd = array() ;

$dd[copy_id] = 233;
$dd[name] = caocaocao;

$lk = fetch(boss, boss_id, 2);

if($lk)
echo "11";
else
echo "11failed";
$fuckyou = query_update(boss, $dd, "`boss_id` = 6");


echo '</p>';
echo "1111";
echo '</p>';
echo "$fuckyou";
echo '</p>';
echo "2222";
echo '</p>';

if($fuckyou )
	echo "fuck you successful";
else
	echo "sorry ,i can't do so";

?>
