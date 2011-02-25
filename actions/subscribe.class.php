<?php

class Subscribe extends Action
{
    public function execute($context)
    {
        global $db, $tpl;
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['group']) || $_SESSION['user']['group'] == 0)
        {
		    header('location:../login/?user=false');
		}
		    
        if (isset($_POST['address']))
        {
            $url = $_POST['address'];
            $uid = $_SESSION['user']['id'];
            $fm = new FeedManager($uid);
            if ($fm->add_feed($url))
            {
                header('location:../home/?addr='.$url);
            }
            else
            {
                $data["ismsg"] = TRUE;
                $data["msg"] = '无法识别的地址或您已订阅该Feed';
            }
        }
        else
        {
            $data["ismsg"] = FALSE;
            $data["msg"] = NULL;          
        }
        $tpl->render('subscribe.html', $data);
    }
};

?>
