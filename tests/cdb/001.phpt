--TEST--
测试CDB的增删改查操作
--FILE--
<?php 
include(dirname(__FILE__).'/../../lib/cdb/CDB.php');

$db = new CDB();

$db->connect('localhost', 'root', '', 'cybery-reader');

$uid = $db->insert('users', array( 'username' => 'test', 'password' => 'test-pass' ));

$user = $db->fetch('users', 'id', $uid);

echo $user['username'], ' - ', $user['password'], "\n";

$update = $db->update('users', $uid, array( 'username' => 'duan', 'alias' => 'Zhiyan' ));

$user = $db->fetch('users', 'id', $uid);
echo $user['username'], ' - ', $user['password'], ' - ', $user['alias'],"\n";

$delete = $db->delete('users', $uid);

$user = $db->fetch('users', 'id', $uid);

echo $user['username'], "\n";
echo $update, "\n";
echo $delete, "\n";


?>
--EXPECT--
test - test-pass
duan - test-pass - Zhiyan

1
1
