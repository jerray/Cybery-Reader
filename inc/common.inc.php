<?php
session_start();

/* 定义常量 */

define( 'APP_ROOT' , dirname(__FILE__) . '/../' );
define( 'LIB_ROOT', APP_ROOT. 'lib/' );
define( 'ACTIONS_ROOT', APP_ROOT. 'actions/' );
define( 'VIEW_ROOT' , APP_ROOT. 'views/' );
define( 'MODEL_ROOT' , APP_ROOT. 'models/' );

define( 'APP_DIR' , '/cybery-reader/' );

/* 加载核心模块 */
include( APP_ROOT . 'core/Action.class.php' );

/* 加载基础库 */
include( LIB_ROOT . 'cdb/CDB.php' );
include( LIB_ROOT . 'ctemplate/ctemplate.class.php' );
include( LIB_ROOT . 'rss-crawler/rsscrawler.class.php' );

/* 加载模型 */
include( MODEL_ROOT . 'feedmanager.class.php' );

/* 数据库设置 */
$db = new CDB();
$ret = $db->connect('localhost', 'root', '', 'cybery-reader');
$db->queryf('SET NAMES "UTF8"');

/* 模板引擎设置 */
$tpl = new CTemplate(VIEW_ROOT, VIEW_ROOT. 'compiled/');

?>
