<?php

include dirname(__FILE__) . '/../lib/rss-crawler/rsscrawler.class.php';
include dirname(__FILE__) . '/../lib/cdb/CDB.php';

$xml = new RSSCrawler();
$db = new CDB();

$db->connect('localhost', 'root', '', 'cybery-reader');
$db->query('SET NAMES \'utf8\'');


$feeds = $db->get('feeds');
if (!$feeds)
    exit;
foreach($feeds as $r)
{
    if ($xml->open($r['url']))
    {
        if (!$xml->valid())
        {
            $xml->close();
            continue;
        }
        $lbd = $r['lastBuildDate'];

        $tmp_chnl = $xml->get_channel();
        $channel = array(
            'title' => $tmp_chnl['title']['value'],
            'link' => $tmp_chnl['link']['value'],
            'description' => $tmp_chnl['description']['value'],
            'lastBuildDate' => strtotime($tmp_chnl['lastBuildDate']['value']),
        );
        $db->update('feeds', $r['id'], $channel);

        if ($channel['lastBuildDate'] == $lbd)
        {
            $xml->close();
            continue;
        }

        while($ir = $xml->read_item())
        {
            $item = array(
                'fid' => $r['id'],
                'title' => $ir['title']['value'],
                'link' => $ir['link']['value'],
                'guid' => sha1($ir['guid']['value']),
                'pubdate' => strtotime($ir['pubDate']['value']),
                'description' => $ir['description']['value'],
                'content' => $ir['content:encoded']['value'],
            );

            //pubDate > $lct -- insert
            if ($item['pubdate'] > $lbd)
            {
                $db->insert('items', $item);
            }
            //pubDate < $lct -- update
            else
            {
                $result = $db->fetch('items', 'guid', $item['guid']);
                $db->update('items', $result['id'], $item);
            }
        }
        
        $xml->close();
    }
}
$db->close();
exit;

?>
