<?php

class Subscribe extends Action
{
    private $PageData;
    private $feedManager;
    private $uid;
    private $url = NULL;
    private $mainPage = false;
    
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
			'onload' => '',
        );

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['group']) || $_SESSION['user']['group'] == 0)
        {
		    header('location:../login/?user=false');
		}

        $this->uid = $_SESSION['user']['id'];
        $this->feedManager = new FeedManager($this->uid);

        if (isset($_POST['main']) && $_POST['main']==1)
        {
            $this->mainPage = true;
        }
		    
        if (isset($_POST['address']))
        {
            $this->url = trim($_POST['address']);
        }

        if ($this->mainPage)
        {
            if ($this->feedManager->add_feed($this->url))
            {
                echo 'OK';
            }
            elseif ($rss = $this->feedManager->find_feed($this->url))
            {
                $RSSMessage = '';
                foreach($rss as $value)
                {
                    $RSSMessage .= '['.$value['title'].' | '.$value['url'].']';
                }
                echo '您提交了一个含有RSS Feed的站点，请选择一个地址然后重新提交：'.$RSSMessage;
            }
            else
            {
                echo 'FALSE';
            }

            return;
        }
        else if ($this->url)
        {
            if ($this->feedManager->add_feed($this->url))
            {
                header('location:../home/?addr='.$this->url);
            }
            elseif ($rss = $this->feedManager->find_feed($this->url))
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
