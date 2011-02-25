<?php

class regist_finish extends Action
{
	public function execute($context)
	{
		global $db, $tpl;
		$data = array(
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
				    header('Location:../../home/');
				}
				else
				{
				    $data['ismsg'] = 'TRUE';
				    $data['msg'] = '糟糕，好像哪里出错了！';
				}
			}
			else
			{
				$data['ismsg'] = 'TRUE';
				$data['msg'] = '糟糕，好像哪里出错了！';
			}
		}
		$tpl->render('register-step-3.html', $data);
	}
};

?>
