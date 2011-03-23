<?php

class regist_finish extends Action
{
    private $PageData;
    
    private function setMessage( $message )
    {
        $this->PageData['ismsg'] = TRUE;
        $this->PageData['msg'] = $message;
    }
    
	public function execute($context)
	{
		global $db, $tpl;
		$this->PageData = array(
			'ismsg' => FALSE,
			'msg' => NULL,
		);
		$id = $_SESSION['user']['id'];

		if(isset($_POST['alias']))
		{
		    $alias = $_POST['alias'];
			//check
			//////
			$upd = $db->update('users', $id, array('alias' => $alias));
			if($upd)
			{
			    $user = $db->fetch('users', 'id', $id);
			    if ($user)
			    {
			        $_SESSION['user'] = $user;
			        $autoAddFeed = 'http://feed.pureweber.com';
			        $feedManager = new FeedManager($id);
			        $feedManager->add_feed($autoAddFeed);
				    header('Location:../../home/');
				}
				else
				{
				    $this->setMessage('糟糕，好像哪里出错了！');
				}
			}
			else
			{
				$this->setMessage('糟糕，好像哪里出错了！');
			}
		}
		$tpl->render('register-step-3.html', $this->PageData);
	}
};

?>
