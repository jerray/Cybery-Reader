<?php

class Logout extends Action
{
	public function execute($context)
	{
		global $db, $tpl;
        if(isset($_SESSION['user']))
        {
            session_unregister('user');
        }
        session_destroy;
        header("Location:../login/");
	}
};

?>
