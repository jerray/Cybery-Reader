<?php

/*
 * 该类用来支持一个用户对其Feed的操作，包括订阅，查看，删除，评论
 * 创建对象时需要指定用户id，例：$user = new feedManager($uid);
 * 附加了一个find_feed()方法，作为在用户提交URL失败时的额外择项
 */

class FeedManager
{
    private $uid;
    private $fid;
    private $ismine = TRUE;
    private $db = NULL;

    function __construct($uid = NULL)
    {
        global $db;
        $this->uid = $uid;
        if (!$this->db)
        {
            $this->db = $db;
            $this->db->queryf('SET NAMES \'UTF8\'');
        }
        $this->items = NULL;
        $this->fid = NULL;
    }
    
    //获取用户订阅列表
    //返回一个关联数组
    //数组各个键：id,title,link,description
    const MYFEED = 1;
    const FAVFEED = 2;
    function get_feeds($type = FeedManager::MYFEED)
    {
        switch($type)
        {
            case FeedManager::MYFEED:
                $feeds = $this->db->queryf(
                    "SELECT f.* FROM feeds AS f
                    LEFT JOIN userfeed AS uf ON f.id = uf.fid
                    WHERE uf.uid = %d ORDER BY f.id ASC", $this->uid);
                break;
            case FeedManager::FAVFEED:
                $feeds = $this->db->queryf(
                    "SELECT f.* FROM feeds AS f
                     ORDER BY f.feednum DESC LIMIT 0,9");
                break;
        }
        if (!$feeds)
            return FALSE;
        return $feeds;
    }
    
    //设定当前使用的Feed编号
    function select_feed($fid)
    {
        $this->fid = $fid;
        if($this->db->get('userfeed', "`uid`=$this->uid AND `fid`=$fid"))
            $this->ismine = TRUE;
        else
            $this->ismine = FALSE;
    }

    //获取编号为$fid的Feed下的所有文章
    function get_items()
    {
        if($this->ismine)
        {
            $items = $this->db->queryf(
                "SELECT i.*, ui.read, ui.share, ui.fav
                FROM items AS i LEFT JOIN useritem AS ui ON ui.uid = %d 
                WHERE i.id = ui.iid AND i.fid = %d 
                ORDER BY i.pubdate DESC", $this->uid, $this->fid
            );
        }
        else
        {
            $items = $this->db->queryf(
                "SELECT i.* 
                FROM items AS i
                WHERE i.fid = %d 
                ORDER BY i.pubdate DESC", $this->fid
            );
        }
        if (!$items)
            return FALSE;
        return $items;
    }

    //获取编号为$iid的条目的所有评论
    function get_comments($iid)
    {
        $comments = $this->db->queryf(
            "SELECT c.*, u.username, u.alias FROM comments AS c 
            LEFT JOIN users AS u ON c.uid = u.id
            WHERE c.iid = %d ORDER BY c.date ASC", $iid);
        if (!$comments)
            return FALSE;
        return $comments;
    }

    //向编号为$iid的条目添加一条评论
    function add_comment($iid, $content)
    {
        $data = array(
            'iid' => $iid,
            'uid' => $this->uid,
            'date' => date("U"),
            'content' => $content
        );
        return $this->db->insert('comments', $data);
    }

    //当前用户添加一个订阅源
    function add_feed($url)
    {
        $feed = $this->db->fetch('feeds','url', $url);
        if ($feed) //如果数据库中存储了该Feed
        {
            $fid = $feed['id'];
            $fnum = $feed['feednum'];

            $condition = "`uid`=$this->uid and `fid`=$fid";
            if ($this->db->get('userfeed', $condition))
            {
                return FALSE;
            }
            return $this->link_feed($fid, $fnum);
        }
        else //如果数据库中不存在该Feed
            return $this->new_feed($url);
    }
    
    //向数据库中添加新Feed
    private function new_feed($url)
    {
        $rss = new RSSCrawler();
        if ($rss->open($url))
        {
            if (!$rss->valid())
                return FALSE;
            $tpc = $rss->get_channel();
            $channel = array(
                'link' => $tpc['link']['value'],
                'url' => $url,
                'title' => $tpc['title']['value'],
                'description' => $tpc['description']['value'],
                'lastBuildDate' => strtotime($tpc['lastBuildDate']['value']),
                'feednum' => 1,
            );
            $fid = $this->db->insert('feeds', $channel);
            if (!$fid)
                return FALSE;

            $uf = array(
                'uid' => $this->uid,
                'fid' => $fid,
            );
            $this->db->insert('userfeed', $uf);

            while($tpi = $rss->read_item())
            {
                $item = array(
                    'fid' => $fid,
                    'title' => $tpi['title']['value'],
                    'link' => $tpi['link']['value'],
                    'guid' => sha1($tpi['guid']['value']),
                    'pubdate' => strtotime($tpi['pubDate']['value']),
                    'description' => $tpi['description']['value'],
                    'content' => $tpi['content:encoded']['value'],
                );
                $iid = $this->db->insert('items', $item);
                if(!$iid)
                    return FALSE;

                $ui = array(
                    'uid' => $this->uid,
                    'iid' => $iid,
                    'read' => FALSE,
                    'share' => FALSE,
                    'fav' => FALSE,
                );
                $this->db->insert('useritem', $ui);
            }
            $rss->close();
            return TRUE;
        }
        return FALSE;
    }

    //将当前用户与编号为$fid的Feed建立关联
    private function link_feed($fid, $fnum)
    {
        $fnum += 1;
        $this->db->queryf("UPDATE `feeds` SET `feednum` = %d WHERE `id`=%d", $fnum, $fid);
        
        $uf = array(
            'uid' => $this->uid,
            'fid' => $fid,
        );
        $this->db->insert('userfeed', $uf);

        $tpi = $this->db->get('items', "`fid`=$fid");
        if (!$tpi)
            return FALSE;
        foreach($tpi as $row)
        {
            $ui = array(
                'uid' => $this->uid,
                'iid' => $row['id'],
                'read' => FALSE,
                'share' => FALSE,
                'fav' => FALSE,
            );
            $this->db->insert('useritem', $ui);
        }
        return TRUE;
    }
    
    //尝试在HTML页面查找该站点的订阅链接。
    //如果找到RSS，则返回一个关联数组：
    //title字段为RSS标题，url字段为RSS的链接。
    function find_feed($url)
    {
        $content = file_get_contents($url);
        if (!$content)
            return NULL;
        $pattern = '<link.+((type.+application/rss\+xml.+)|(title.+"(.*)".+)|(href.+"(.*)".+)){3}/>';
        preg_match_all($pattern, $content, $out);
        if (!$out)
            return NULL;
        $count = count($out[0]);
        $rss = array();
        for ($i = 0; $i < $count; $i++)
        {
            $rss[$i]['title'] = $out[4][$i];
            $rss[$i]['url'] = $out[6][$i];
        }
        return $rss;
    }

    //取消该用户与编号为$fid的Feed的关联
    function unlink_feed($fid)
    {
        $feed = $this->fetch('feeds', 'id', $fid);
        $fnum = $feed['feednum'];
        $fnum -= 1;
        $this->db->queryf("UPDATE `feeds` SET `feednum` = %d WHERE `id`=%d", $fnum, $fid);
        
        $result = $this->db->get('items', "`fid`=$fid");
        if (!$result)
            return FALSE;
        foreach($result as $row)
        {
            if (!$this->db->queryf("DELETE FROM `useritem` WHERE `uid`=%d AND `iid`=%d", $this->uid, $row['id']))
                return FALSE;
        }
        return $this->db->queryf("DELETE FROM `userfeed` WHERE `uid`=%d AND `fid`=%d", $this->uid, $fid);
    }
    
    //更新用户对某篇文章的标记
    //需要的参数是一个关联数组
    //各个键名对应useritem表中的字段名
    function item_action($attr)
    {
        $sql = "update `useritem` set `read`='".$attr['read']."',
                `share`='".$attr['share']."',
                `fav`='".$attr['fav']."'
                where `uid`=".$attr['uid']."
                and `iid`=".$attr['iid'];
        return $this->db->queryf($sql);
    }

}



?>
