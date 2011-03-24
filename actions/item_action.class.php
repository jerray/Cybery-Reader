<?php

class Item_Action extends Action
{
    private $feedManager;
    private $uid;

    private function get_attr($iid)
    {
        global $db;
        $r = $db->get('useritem', "`uid`=$this->uid AND `iid`=$iid");
        return $r[0];
    }

    public function execute($context)
    {
        global $db, $tpl;
        $this->feedManager = new FeedManager();
        $this->uid = $_SESSION['user']['id'];
        $iid = $_SESSION['user']['iid'];
        $fid = $_SESSION['user']['fid'];

        if(isset($_POST['guid']) && isset($_POST['read']))
        {
            $isread = $_POST['read'];
            $attr = $this->get_attr($iid);
            $attr['read'] = (bool)$isread;
            $this->feedManager->item_action($attr);
        }

        if(isset($_POST['guid']) && isset($_POST['fav']))
        {
            $guid = $_POST['guid'];
            $isfav = $_POST['fav'];
            $r = $db->get('items', "`guid`='$guid' AND `fid`=$fid");
            $r = $r[0];
            $iid = $r['id'];
            $attr = $this->get_attr($iid);
            $attr['fav'] = (bool)$isfav;
            $this->feedManager->item_action($attr);
        }
    }

}


?>
