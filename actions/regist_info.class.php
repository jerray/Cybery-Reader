<?php
class regist_info extends Action
{
	public function execute($context)
	{
		global $db, $tpl;
		$data = array(
			'ismsg' => FALSE,
			'msg' => NULL,
		);
		if(isset($_POST['username']) && isset($_POST['password']))
		{
			$username = $_POST['username'];
			$password = $_POST['password'];
			//check
			///////
			$id = $_SESSION['user']['id'];
			$password = sha1($password);
			$upd = $db->update('users', $id, array('username' => $username, 'password' => $password));
			if($upd)
				header('Location:/cybery-reader/regist/step3/');
			else
			{
				$data['ismsg'] = 'TRUE';
				$data['msg'] = 'Error';	
			}
		}
		$tpl->render('register-step-2.html', $data);
	}
};

?>
