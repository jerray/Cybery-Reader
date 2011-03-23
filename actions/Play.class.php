<?php

class Play extends Action
{
	public function execute($context)
	{
		global $db, $tpl;

		/*
		$uid = $db->insert('users', array(
			'username' => 'test',
			'password' => 'test-pass',));

		print_r($db->fetch('users', 'id', $uid));
		 */

		$tpl->render('dummy.html', array( 'hello' => 'hello' ));
	}
};

?>
