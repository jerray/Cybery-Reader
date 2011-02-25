<?php

class Main extends Action
{
	public function execute($context)
	{
		global $db, $tpl;
		if (!isset($_SESSION['user']) || !isset($_SESSION['user']['group']) || $_SESSION['user']['group'] == 0)
        {
            header('location:../login/?user=false');
		}
		    
		$uid = $_SESSION['user']['id'];
		$fm = new FeedManager($uid);
		$data = array(
		    'myfeed' => $fm->get_feeds(),
		    'isfeed' => FALSE,
		    'ismsg' => FALSE,
		    'isitem' => FALSE,
		    'ctitle' => '评论',
		    'iscomments' => FALSE,
		);
		
		if ($data['myfeed'])
		{
		    $data['isfeed'] = TRUE;
		    $n = count($data['myfeed']);
		    for($i = 0; $i < $n; $i++)
		    {
		        $fm->select_feed($data['myfeed'][$i]['id']);
		        $tmp_items = $fm->get_items();
		        $data['myfeed'][$i]['unread'] = 0;
		        foreach($tmp_items as $item)
		        {
		            if (!$item['read'])
		                $data['myfeed'][$i]['unread']++;
		        }
		    }
		}
		
        if (isset($_GET['url']))
        {
            $url = $_GET['url'];
            // check
            $result = $db->fetch('feeds', 'url', $url);
            if ($result)
            {
                $fid = $result['id'];
                $data['feedname'] = $result['title'];
                $fm->select_feed($fid);
                $data['items'] = $fm->get_items();
                if ($data['items'])
                {
                    $data['isitem'] = TRUE;
                }
            }
            else
            {
                $data['ismsg'] =TRUE;
                $data['msg'] = '糟糕，好像哪里出错了！';
            }
            
            if (isset($_GET['guid']))
            {
                $guid = $_GET['guid'];
                // check
                $r = $db->fetch('items', 'guid', $guid);
                $iid = $r['id'];
                $data['ctitle'] .= '@'.$r['title'];
                $data['comments'] = $fm->get_comments($iid);
                if ($data['comments'])
                    $data['iscomments'] = TRUE;
            }
            
        }
        else if(isset($_GET['addr']))
        {
            $url = $_GET['addr'];
            // check
            $data['ismsg'] = TRUE;
            $result = $db->fetch('feeds', 'url', $url);
            if ($result)
            {
                $fid = $result['id'];
                $data['feedname'] = $result['title'];
                $fm->select_feed($fid);
                $data['items'] = $fm->get_items();
                if ($data['items'])
                {
                    $data['isitem'] = TRUE;
                }
                $data['msg'] = '已成功订阅'.$result['title'];
            }
            else
            {
                $data['msg'] = '糟糕，好像哪里出错了！';
            }
        }
        
        if(isset($_GET['user']) && $_GET['user']=='login')
        {
            $data['ismsg'] = TRUE;
            $data['msg'] = $_SESSION['user']['alias'].'，欢迎回来。';
        }

        $tpl->render('main.html', $data);
	}
};

?>
