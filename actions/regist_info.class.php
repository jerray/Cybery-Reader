<?php

class regist_info extends Action
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
		if(isset($_POST['username']) && isset($_POST['password']))
		{
			$username = $_POST['username'];
			$password = $_POST['password'];
			//check
			///////
			if ($db->fetch('users', 'username', $username))
			{
				$this->setMessage('用户名被占用');	
			}
			else
			{
			    $id = $_SESSION['user']['id'];
			    $password = sha1($password);
			    $upd = $db->update('users', $id, array('username' => $username, 'password' => $password));
			    if($upd)
				    header('Location:/cybery-reader/regist/step3/');
			    else
			    {
				    $this->setMessage('糟糕，好像哪里出错了！');
			    }
			}
		}
		$tpl->render('register-step-2.html', $this->PageData);
	}
};

?>
