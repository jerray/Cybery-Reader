<?php

class FeedList extends Action
{
    private $PageData;
    private $feedManager;
    private $uid;
    
    private function init()
    {
        $this->feedManager = new FeedManager($this->uid);
		$this->PageData = array(
		    'myfeed' => $this->feedManager->get_feeds(FeedManager::MYFEED),
		    'favfeed' => $this->feedManager->get_feeds(FeedManager::FAVFEED),
		    'ismyfeed' => FALSE,
		    'isfavfeed' => FALSE,
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
		    
		$this->uid = $_SESSION['user']['id'];
		$this->init();
		$this->countUnreadItems();
		
        $tpl->render('feedlist.html', $this->PageData);
    }
}
