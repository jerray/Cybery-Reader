<?php 

class Login extends Action
{
    public function execute($context)
    {
        global $db, $tpl;
        $data['ismsg'] = FALSE;
		$data['msg'] = NULL;
		
        if (isset($_SESSION['user']) && isset($_SESSION['user']['group']) && $_SESSION['user']['group'] != 0)
        {
		    header('location:../home/');
		}
		
		if (isset($_POST['username']) && isset($_POST['password']))
		{
		    $username = $_POST['username'];
		    $password = $_POST['password'];
		    // check
		    $password = sha1($password);
		    $result = $db->get('users', "`username`='".$username."' AND `password`='".$password."'");
		    if ($result)
		    {
		        $_SESSION['user'] = $result[0];
		        header('location:../home/?user=login');
		    }
		    else
		    {
		        $data['ismsg'] = TRUE;
		        $data['msg'] = '用户名或密码错误';
		    }
		}
		else if (isset($_GET['user']) && $_GET['user']=='false')
		{
		    $data['ismsg'] = TRUE;
		    $data['msg'] = '请先登录';
		}
		
		$tpl->render('login.html', $data);
    }
};

?>
