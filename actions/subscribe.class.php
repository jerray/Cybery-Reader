<?php

class Subscribe extends Action
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
        
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['group']) || $_SESSION['user']['group'] == 0)
        {
		    header('location:../login/?user=false');
		}
		    
        if (isset($_POST['address']))
        {
            $url = $_POST['address'];
            $uid = $_SESSION['user']['id'];
            $feedManager = new FeedManager($uid);
            if ($feedManager->add_feed($url))
            {
                header('location:../home/?addr='.$url);
            }
            elseif ($rss = $feedManager->find_feed($url))
            {
                $RSSMessage = '';
                foreach($rss as $value)
                {
                    $RSSMessage .= '['.$value['title'].' | '.$value['url'].']';
                }
                $this->setMessage('您提交了一个含有RSS Feed的站点，请选择一个地址然后重新提交：'.$RSSMessage);
            }
            else
            {
                $this->setMessage('无法识别的地址或您已订阅该Feed');
            }
        }

        $tpl->render('subscribe.html', $this->PageData);
    }
};

?>
