<?php

class Post extends Action
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
		    'ismsg' => FALSE,
		    'msg' => NULL,
		    'isitem' => FALSE,
		    'ctitle' => '评论',
		    'iscomments' => FALSE,
		);
    }
    
	public function execute($context)
	{
		global $db, $tpl;
		    
		$this->uid = $_SESSION['user']['id'];
		$this->init();
		
		if(isset($_POST['url']))
		{
		    $url = $_POST['url'];
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
		}
		$tpl->render('post.html', $this->PageData);
	}
};

?>
