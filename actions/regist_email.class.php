<?php

class regist_email extends Action
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
		if(isset($_POST['email']))
		{
			$email = $_POST['email'];
			//check
			///////
			if ($db->fetch('users', 'email', $email))
			{
				$this->setMessage('该邮箱已被占用');
		    }
		    else
		    {
			    $secret = substr(sha1(time()), 0, 16);
			    $id = $db->insert('users', array('email' => $email, 'secret' => $secret));
			    $_SESSION['user']['id'] = $id;
			    if($id)
			    {
				    ///send email
				    //发送邮件开始
				    include dirname(__FILE__) . '/../models/smtp.class.php';
				    $url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/regist/confirm/'.'?'.'id='.$id.'&'.'email='.$email.'&'.'secret='.$secret;

				    $smtpserver = "ssl://smtp.gmail.com";	//smtp服务器地址
				    $port = 465;	//端口号，一般为25
				    $smtpuser = "cybery.reader@gmail.com";	//登录smtp服务器的用户名，及邮箱名
				    $smtppwd = "pureweber";	//登录smtp服务器的密码，及邮箱密码
				    $mailtype = "HTML";	//邮件的类型，可以是TXT或HTML
				    $sender = "cybery.reader@gmail.com";	//发件人，一般与登录smtp服务器的用户名相同
				    $smtp = new smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
				    //$smtp->debug = true;		//显示一些发送信息
				    $to = "$email";	//收件人
				    $subject = "获取激活邮件";
				    $subject = "=?UTF-8?B?".
base64_encode($subject)."?=";
				    $body = '<html><body>'.'注册成功。您的激活码是：'.'<a href="'.$url.'" target="_blank">'.$url.'</a><br>'.'请点击该地址，激活您的用户！'.'</body></html>';
				    $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);

				    $this->setMessage('认证邮件已发出，请检查邮箱。');
		            //发送邮件结束
				
			    }
			    else
			    {
				    $this->setMessage('糟糕，好像哪里出错了！');
			    }
			}
		}
		$tpl->render('register-step-1.html', $this->PageData);
	}
};

?>
