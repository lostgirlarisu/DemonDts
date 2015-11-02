<?php

define('CURSCRIPT', 'solo');

require './include/common.inc.php';

if ($server_addr!=$cache_server_addr && $is_cache_server)
{
        header("Location: {$server_addr}index.php");
        exit(); 
}

if(!$cuser||!$cpass) { gexit($_ERROR['no_login'],__file__,__line__); }

$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
if(!$db->num_rows($result)) { gexit($_ERROR['login_check'],__file__,__line__); }
$udata = $db->fetch_array($result);
if($udata['password'] != $cpass) { gexit($_ERROR['wrong_pw'], __file__, __line__); }
if($udata['groupid'] <= 0) { gexit($_ERROR['user_ban'], __file__, __line__); }

include_once './include/game/gametype.func.php';

if ($action=='remove') delete_solo_game($sid);

if ($action=='setopponent' && check_can_solo()==2 && $oid!=$cuser)
{
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$oid'");
	if(!$db->num_rows($result)) { gexit($_ERROR['user_not_exists'], __file__, __line__); }
	$udata = $db->fetch_array($result);
	if ($udata['oid']==$cuser)	//约战成功
	{
		add_solo_game($cuser,$udata['username'],0);
		$db->query("UPDATE {$tablepre}users SET oid='' WHERE username='$oid'");
	}
	else  $db->query("UPDATE {$tablepre}users SET oid='$oid' WHERE username='$cuser'");
}

if ($action=='newteamfight' && get_groupid()>=5)
{
	$p1=(int)$maxteamsize; 
	if (isset($randteam)) $p2=1; else $p2=0;
	if (1<=$p1 || $p1<=5) add_teamfight_game($p1,$p2);
}

header("Location: index.php");

?>
