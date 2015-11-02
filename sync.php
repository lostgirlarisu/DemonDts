<?php

require './include/common.inc.php';

if ($server_addr==$cache_server_addr) die();

if (isset($_GET['cuser'])) $cuser=$_GET['cuser'];
if (isset($_GET['cpass'])) $cpass=$_GET['cpass'];

if(!$cuser||!$cpass) { gexit($_ERROR['no_login'],__file__,__line__); }
$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
if(!$db->num_rows($result)) { gexit($_ERROR['login_check'],__file__,__line__); }
$udata = $db->fetch_array($result);
if($udata['password'] != $cpass) { gexit($_ERROR['wrong_pw'], __file__, __line__); }
elseif(($udata['groupid'] <= 8)&&($cuser!==$gamefounder)) { gexit('只有权限9以上的管理员才能进行此操作。', __file__, __line__); }

if ($is_cache_server && isset($_GET['action']) && $_GET['action']=='sync')
{
	if (file_exists(GAME_ROOT.'sync.lock'))
	{
		echo "另一个同步进程正在进行中！退出。";
		exit();
	}

	$result = $db->query("SELECT pw FROM dianbo_pw");
	if(!$db->num_rows($result)) { gexit('数据库错误。',__file__,__line__); }
	$udata = $db->fetch_array($result);

	touch(GAME_ROOT.'sync.lock');
	$result=shell_exec("./sync.sh {$udata['pw']}");
	unlink(GAME_ROOT.'sync.lock');
	echo "<html><body>";
	echo "日志：<br>";
	echo "<pre>".$result."</pre><br>";
	echo "<a href=\"{$server_addr}index.php\">返回主页</a>";
	echo "</body></html>";
}
else  if ($is_cache_server)
{
	header("Location: {$server_addr}sync.php");
      exit(); 
}
else
{
	echo "<html><body>";
	echo "即将把除img目录以外的所有文件同步到高速服。<br>";
	echo "这将花费几分钟时间，<font color=#ff0000>请不要关闭页面或重复点击</font>。同步完成后会显示日志。<br>";
	echo "<a href=\"{$cache_server_addr}sync.php?cuser=".urlencode($cuser)."&cpass={$cpass}&action=sync\">点我开始同步</a>";
	echo "&nbsp;&nbsp;<a href=\"{$server_addr}index.php\">返回主页</a>";
	echo "</body></html>";
}
?>
