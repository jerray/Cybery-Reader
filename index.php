<?php

include('inc/common.inc.php');
include('config/routes.inc.php');

$app_reg = str_replace('/', '\/', APP_DIR);

$uri = preg_replace('/^'.$app_reg.'/', '', $_SERVER['REQUEST_URI']);

foreach($routes as $pattern => $config)
{
	if(preg_match($pattern, $uri))
	{
		if(file_exists($config[1]))
		{
			include($config[1]);

			$action_file_name = basename($config[1]);
			$action_class_name = substr($action_file_name, 0, strpos($action_file_name, '.'));

			if(class_exists($action_class_name)){

				$act = new $action_class_name();

				if(method_exists($act, 'execute')){
					$act->execute(null);
					die();
				}else{
					die('action class invalid');
				}
			}else{
				die('action class not exist');
			}
		}else{
			die('action file not exist');
		}
	}
}
header('location:home/');

?>
