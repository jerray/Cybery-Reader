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
		$alias = $_POST['alias'];
		$id = $_SESSION['user']['id'];
		$sql = "select * from users where id = '$id'";
		$_SESSION['user'] = $sql;
		if(isset($alias))
		{
			//check
			//////
			$upd = $db->update('users', $id, array('alias' => $alias));
			if($upd)
			{
			//	header('Location:/cybery-reader/main/');
			}
			else
			{
				$data['ismsg'] = 'TRUE';
				$data['msg'] = 'Error';
			}
		}
		$tpl->render('register-step-3.html', $data);
	}
};

?>
