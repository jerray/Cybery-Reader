<?php

class Comment extends Action
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
		    'ctitle' => '评论',
		    'iscomments' => FALSE,
			'show' => FALSE,
			'add' => FALSE,
		);
    }
	
	public function execute($context)
	{
		global $db, $tpl;
		    
		$this->uid = $_SESSION['user']['id'];
		$this->init();
		
		if(isset($_POST['guid']))
		{
            $guid = $_POST['guid'];
            $fid = $_SESSION['user']['fid'];
            $r = $db->get('items', "`guid`='$guid' AND `fid`=$fid");
            $r = $r[0];
            $iid = $r['id'];
			$_SESSION['user']['iid'] = $iid;
            $this->PageData['ctitle'] .= ' :: '.$r['title'];
            $this->PageData['comments'] = $this->feedManager->get_comments($iid);
            if ($this->PageData['comments'])
                $this->PageData['iscomments'] = TRUE;
			$this->PageData['show'] = TRUE;
			$this->PageData['guid'] = $guid;
		}
		
		if(isset($_POST['comment']))
		{
			$comment = $_POST['comment'];
			$iid = $_SESSION['user']['iid'];
			$this->PageData['add'] = TRUE;
			$this->PageData['comments']['content'] = $comment;
			$this->PageData['comments']['alias'] = $_SESSION['user']['alias'];
			$this->feedManager->add_comment($iid, $comment);
		}
		
		$tpl->render('comment.html', $this->PageData);
	}
};
?>
