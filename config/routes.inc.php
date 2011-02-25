<?php

/*
 * 每条记录表示一个请求分发配置。
 *
 * 记录的key是一个正则表达式，凡是与这个正则表达式相匹配的uri将分发给该action处理
 * 记录的value是一个数组，数组的第一个元素为该Action的名字，也是该Action的唯一标识
 * 数组的第二个元素是该Action文件的路径。
 */

$routes = array(
	'/^play\/action\/(.*)/' => array(
		'PlayGround',
		ACTIONS_ROOT . 'Play.class.php',
	),
	'/^regist\/step1\/$/' => array(
		'RegisterStepOne',
		ACTIONS_ROOT . 'regist_email.class.php',
	),
	'/^regist\/step2\/$/' => array(
		'RegisterStepTwo',
		ACTIONS_ROOT . 'regist_info.class.php',
	),
	'/^regist\/step3\/$/' => array(
		'RegisterStepThree',
		ACTIONS_ROOT . 'regist_finish.class.php',
	),
	'/^regist\/confirm\/(.*)/' => array(
		'Confirm',
		ACTIONS_ROOT . 'confirm.class.php',
	),
	'/^subscribe\/$/' => array(
		'Subscribe',
		ACTIONS_ROOT . 'subscribe.class.php',
	),
	'/^home\/(.*)/' => array(
		'Home',
		ACTIONS_ROOT . 'main.class.php',
	),
	'/^login\/(.*)/' => array(
		'Login',
		ACTIONS_ROOT . 'login.class.php',
	),
	'/^logout\/$/' => array(
		'Logout',
		ACTIONS_ROOT . 'logout.class.php',
	),
);

?>
