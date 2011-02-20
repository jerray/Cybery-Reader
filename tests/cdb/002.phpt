--TEST--
测试CDB的queryf
--FILE--
<?php 
include(dirname(__FILE__).'/../../lib/cdb/CDB.php');

$db = new CDB();

$db->connect('localhost', 'root', '', 'cybery-reader');

$username = 'zhiyan';
$password = 'zhiyan-pass';
$alias = 'Duan Zhiyan';

$r = $db->queryf("INSERT INTO users(username, password, alias) VALUES('%s', '%s', '%s')", $username, $password, $alias);
echo intval($r), "\n";
$uid = $db->insert_id();

$r = $db->queryf("INSERT INTO feeds(url, title) VALUES('%s', '%s')", "http://feed.feedsky.com/tunwu", "吞吴");
echo intval($r), "\n";
$fid = $db->insert_id();

$r = $db->queryf("INSERT INTO userfeed(uid, fid) VALUES(%d, %d)", $uid, $fid);
echo intval($r), "\n";

$r = $db->queryf("INSERT INTO feeds(url, title) VALUES('%s', '%s')", "http://www.ruanyifeng.com/feed.html", "阮一峰");
echo intval($r), "\n";
$fid = $db->insert_id();

$r = $db->queryf("INSERT INTO userfeed(uid, fid) VALUES(%d, %d)", $uid, $fid);
echo intval($r), "\n";

$feeds = $db->queryf("SELECT f.*, u.id as uid, u.username FROM feeds AS f
	LEFT JOIN userfeed AS uf ON f.id = uf.fid
	LEFT JOIN users AS u ON uf.uid = u.id
	WHERE uf.uid = %d ORDER BY f.id ASC", $uid);

if(is_array($feeds) && count($feeds) > 0)
{
	foreach($feeds as $feed):
		echo $feed['url'] , ' - ', $feed['title'], ' - ' , $feed['username'] , "\n";
	endforeach;
}

$r = $db->queryf("DELETE FROM feeds WHERE id IN (
	SELECT uf.fid FROM userfeed AS uf
	LEFT JOIN users AS u ON uf.uid = u.id
	WHERE uf.uid = %d
	)", $uid);
echo intval($r), "\n";

if(!$r)
	echo mysql_error();

$r = $db->queryf("DELETE FROM userfeed WHERE uid = %d", $uid);
echo intval($r), "\n";

$r = $db->queryf("DELETE FROM users WHERE id = %d", $uid);
echo intval($r), "\n";

?>
--EXPECT--
1
1
1
1
1
http://feed.feedsky.com/tunwu - 吞吴 - zhiyan
http://www.ruanyifeng.com/feed.html - 阮一峰 - zhiyan
1
1
1
