<?php

define('CURSCRIPT', 'scripter');

require './include/common.inc.php';
require './include/game.func.php';
$_REQUEST = gstrfilter($_REQUEST);
if ($_REQUEST["script"]=="" && $_REQUEST["name"]=="")
{
	header("Location: index.php");exit();
}
else
{
	if(!$_REQUEST["script"]==""){
		header("Location: scripter.php?name=" . $cuser);exit();
	}
	if(!$cuser||!$cpass) { gexit($_ERROR['no_login'],__file__,__line__); }

	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
	if(!$db->num_rows($result)) { gexit($_ERROR['login_check'],__file__,__line__); }
	$udata = $db->fetch_array($result);
	if($udata['password'] != $cpass) { gexit($_ERROR['wrong_pw'], __file__, __line__); }
	$db->query("UPDATE {$tablepre}users SET groupid='0' WHERE username='$cuser'");
}
$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
$udata = $db->fetch_array($result);
if($udata['groupid'] <= 0) {
	gexit($_ERROR['scripter_ban'], __file__, __line__);
}
header("Location: index.php");exit();

?>