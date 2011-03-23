--TEST--
测试FeedManager的查询操作
--FILE--
<?php
include(dirname(__FILE__).'/../../lib/cdb/CDB.php');
include(dirname(__FILE__).'/../../models/feedmanager.class.php');
include(dirname(__FILE__).'/../../lib/rss-crawler/rsscrawler.class.php');

$db = new CDB();

$db->connect('localhost', 'root', '', 'cybery-reader');

$uid = 1; // ASSUME

$fm = new FeedManager($uid);
$feeds = $fm->get_feeds();
foreach($feeds as $row)
{
    echo $row['link'], "\n";
}

$fid = 1; // Assume we know it
$fm->select_feed($fid);
$items = $fm->get_items();
foreach($items as $row)
{
    if ($row['read'] == TRUE)
        echo $row['link'], "\n";
}

$iid = 1; // Assume again
$comments = $fm->get_comments($iid);
foreach($comments as $row)
{
    echo $row['content'], "\n";
}

?>
--EXPECT--
http://www.7lemon.net
http://www.appinn.com
http://www.7lemon.net/2011/02/connectify.html
AS YOUR WISH
FOR THE HORD
AS YOUR WISH
FOR THE HORD

