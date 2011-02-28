<?php

class Main extends Action
{
    private $PageData;
    private $feedManager;
    private $uid;
    
    private function setMessage( $message )
    {
        $this->PageData['ismsg'] = TRUE;
        $this->PageData['msg'] = $message;
    }
    
    private function init()
    {
        $this->feedManager = new FeedManager($this->uid);
		$this->PageData = array(
		    'myfeed' => $this->feedManager->get_feeds(FeedManager::MYFEED),
		    'favfeed' => $this->feedManager->get_feeds(FeedManager::FAVFEED),
		    'ismyfeed' => FALSE,
		    'isfavfeed' => FALSE,
		    'ismsg' => FALSE,
		    'msg' => NULL,
		    'isitem' => FALSE,
		    'ctitle' => '评论',
		    'iscomments' => FALSE,
		);
		if ($this->PageData['myfeed'])
		    $this->PageData['ismyfeed'] = TRUE;
		if ($this->PageData['favfeed'])
		    $this->PageData['isfavfeed'] = TRUE;
    }
    
    private function countUnreadItems()
    {
        if ($this->PageData['ismyfeed'])
		{
		    $n = count($this->PageData['myfeed']);
		    for($i = 0; $i < $n; $i++)
		    {
		        $this->feedManager->select_feed($this->PageData['myfeed'][$i]['id']);
		        $tmp_items = $this->feedManager->get_items();
		        $this->PageData['myfeed'][$i]['unread'] = 0;
		        foreach($tmp_items as $item)
		        {
		            if (!$item['read'])
		                $this->PageData['myfeed'][$i]['unread']++;
		        }
		    }
		}
    }
    
	public function execute($context)
	{
		global $db, $tpl;
		if (!isset($_SESSION['user']) || 
		     !isset($_SESSION['user']['group']) || 
		     $_SESSION['user']['group'] == 0)
        {
            header('location:../login/?user=false');
		}
		    
		$this->uid = $_SESSION['user']['id'];
		$this->init();
		$this->countUnreadItems();
		
        if (isset($_GET['url']))
        {
            $url = $_GET['url'];
            // check
            $result = $db->fetch('feeds', 'url', $url);
            if ($result)
            {
                $fid = $result['id'];
                $this->PageData['feedname'] = $result['title'];
                $this->feedManager->select_feed($fid);
                $this->PageData['items'] = $this->feedManager->get_items();
                if ($this->PageData['items'])
                {
                    $this->PageData['isitem'] = TRUE;
                }
            }
            else
            {
                $this->setMessage('糟糕，好像哪里出错了！');
            }
            
            if (isset($_GET['guid']))
            {
                $guid = $_GET['guid'];
                // check
                $r = $db->fetch('items', 'guid', $guid);
                $iid = $r['id'];
                $this->PageData['ctitle'] .= '@'.$r['title'];
                $this->PageData['comments'] = $this->feedManager->get_comments($iid);
                if ($this->PageData['comments'])
                    $this->PageData['iscomments'] = TRUE;
            }
            
        }
        else if(isset($_GET['addr']))
        {
            $url = $_GET['addr'];
            // check
            $result = $db->fetch('feeds', 'url', $url);
            if ($result)
            {
                $fid = $result['id'];
                $this->PageData['feedname'] = $result['title'];
                $this->feedManager->select_feed($fid);
                $this->PageData['items'] = $this->feedManager->get_items();
                if ($this->PageData['items'])
                {
                    $this->PageData['isitem'] = TRUE;
                }
                $this->setMessage('已成功订阅'.$result['title']);
            }
            else
            {
                $this->setMessage('糟糕，好像哪里出错了！');
            }
        }
        
        if(isset($_GET['user']) && $_GET['user']=='login')
        {
            $this->setMessage($_SESSION['user']['alias'].'，欢迎回来。');
        }

        $tpl->render('main.html', $this->PageData);
	}
};

?>
