<?php
class confirm extends Action
{
	public function execute($context)
	{
		global $db, $tpl;
		$data = array(
			'ismsg' => FALSE,
			'msg' => NULL,
		);
			
		$id = $_GET['id'];
		$email = $_GET['email'];
		$secret = $_GET['secret'];
		if(isset($email))
		{
			$sql="select * from users where email='$email' and secret='$secret'";
			$sel = mysql_query($sql);
			if($sel)
			{
				$sql=$db->update('users', $id, array('group' => 1));
				if($sql)
				{
					header('Location:/cybery-reader/regist/step2/');		
				}
				else
				{
					$data['ismsg'] = TRUE;
					$data['msg'] = '邮箱认证失败';
				}
			}
			else
			{
				$data['ismsg'] = TRUE;
				$data['msg'] = '好想出错了';
			}
		}
		else
		{
			$data['ismsg'] = TRUE;
			$data['msg'] = '链接错误或已经过期';
		}

		$tpl->render('confirm.html', $data);
	}
};

?>
