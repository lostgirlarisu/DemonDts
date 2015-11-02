<?php

define('CURSCRIPT', 'game');
require './include/common.inc.php';
require GAME_ROOT.'./include/game.func.php';

if ($server_addr!=$cache_server_addr && !$is_cache_server)
{
	header("Location: {$server_addr}index.php");
	exit();
}

if(!$cuser||!$cpass) { gexit($_ERROR['no_login'],__file__,__line__); } 
if($mode == 'quit') {

	gsetcookie('user','');
	gsetcookie('pass','');
	header("Location: index.php");
	exit();

}
$result = $db->query("SELECT * FROM {$tablepre}players WHERE name = '$cuser' AND type = 0");
if(!$db->num_rows($result)) { header("Location: valid.php");exit(); }

$pdata = $db->fetch_array($result);
if($pdata['pass'] != $cpass) {
	$tr = $db->query("SELECT `password` FROM {$tablepre}users WHERE username='$cuser'");
	$tp = $db->fetch_array($tr);
	$password = $tp['password'];
	if($password == $cpass) {
		$db->query("UPDATE {$tablepre}players SET pass='$password' WHERE name='$cuser'");
	} else {
		gexit($_ERROR['wrong_pw'],__file__,__line__);
	}
}



if($gamestate == 0) {
	header("Location: end.php");exit();
}


if ($server_addr!=$cache_server_addr && $is_cache_server)	//在分高速低速服务器的情况下，禁区状况要自己处理
{
	$movehtm = GAME_ROOT.TPLDIR.'/move.htm';
	$movedata = '<option value="main">■ 移动 ■<br />';

	foreach($plsinfo as $key => $value) {
		if(array_search($key,$arealist) > $areanum || $hack){
		$movedata .= "<option value=\"$key\"><!--{if \$pls == $key}--><--现在位置--><!--{else}-->$value($xyinfo[$key])<!--{/if}--><br />";
		}
	} 
	
	$odata=readover($movehtm);
	if ($odata!=$movedata) writeover($movehtm,$movedata);
}


extract($pdata);

$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$name'");
$sktime = $db->result($result, 0);
if (!$sktime) $sktime=0;

if ($club==25){
	$result = $db->query("SELECT pls FROM {$tablepre}players WHERE pid = '$sktime'");
	$mp = $db->result($result, 0);
	if (($mp>=0)&&($lvl>=12)&&($sktime>0)){
		$markpls=$plsinfo[$mp];
	}else{
		$markpls="暂无目标";
	}
}

init_playerdata();
init_profile();



if ($gametype==2 && !$teamPass)
{
	include_once GAME_ROOT.'./include/game/gametype.func.php';
	$always_randteam=check_teamfight_always_randteam();
	include template('gametype2team');
	exit();
}

$log = '';
//读取聊天信息
$chatdata = getchat(0,$teamID);
if ($teamID) $teamchatdata = getteamchat(0,$teamID,4);

//读取玩家互动信息
$result = $db->query("SELECT lid,time,log FROM {$tablepre}log WHERE toid = '$pid' AND prcsd = 0 ORDER BY time,lid");
$llist = '';
while($logtemp = $db->fetch_array($result)){
	$log .= date("H:i:s",$logtemp['time']).'，'.$logtemp['log'].'<br />';
	$llist .= $logtemp['lid'].',';
}
if(!empty($llist)){
	$llist = '('.substr($llist,0,-1).')';
	$db->query("UPDATE {$tablepre}log SET prcsd=1 WHERE toid = '$pid' AND lid IN $llist");
}
if($hp > 0){//判断冷却时间是否过去
	//读取背包内道具
		include_once GAME_ROOT.'./include/game/itembag.func.php';
		$itemlist = item_arr();
	//显示枪声信息
	if(($now <= $noisetime+$noiselimit)&&$noisemode&&($noiseid!=$pid)&&($noiseid2!=$pid)) {
		if(($now-$noisetime) < 60) {
			$noisesec = $now - $noisetime;
			$log .= "<span class=\"yellow b\">{$noisesec}秒前，{$plsinfo[$noisepls]}传来了{$noiseinfo[$noisemode]}。</span><br>";
		} else {
			$noisemin = floor(($now-$noisetime)/60);
			$log .= "<span class=\"yellow b\">{$noisemin}分钟前，{$plsinfo[$noisepls]}传来了{$noiseinfo[$noisemode]}。</span><br>";
		}
	}
	if($coldtimeon){
		$cdover = $cdsec*1000 + $cdmsec + $cdtime;
		$nowmtime = floor(getmicrotime()*1000);
		$rmcdtime = $nowmtime >= $cdover ? 0 : $cdover - $nowmtime;
	}
}
//var_dump($itm3);
if($hp <= 0){
	$dtime = date("Y年m月d日H时i分s秒",$endtime);
	$kname='';
	if($bid) {
		$result = $db->query("SELECT name FROM {$tablepre}players WHERE pid='$bid'");
		if($db->num_rows($result)) { $kname = $db->result($result,0); }
	}
	$mode = 'death';
} elseif($state ==1 || $state == 2 || $state == 3){
	$mode = 'rest';
} elseif($itms0){
	$mode = 'itemmain';
} else {
	$mode = 'command';
}

$cmd = $main = '';
if((strpos($action,'corpse')===0 || strpos($action,'pacorpse')===0) && $gamestate<40){
	$cid = strpos($action,'corpse')===0 ? str_replace('corpse','',$action) : str_replace('pacorpse','',$action);
	if($cid){
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$cid' AND hp=0");
		if($db->num_rows($result)>0){
			$edata = $db->fetch_array($result);
			include_once GAME_ROOT.'./include/game/battle.func.php';
			findcorpse($edata);
			extract($edata,EXTR_PREFIX_ALL,'w');
			init_battle(1);
			$main = 'battle';
		}
	}	
}
if($hp > 0 && $coldtimeon && $showcoldtimer && $rmcdtime){$log .= "行动冷却时间：<span id=\"timer\" class=\"yellow\">0.0</span>秒<script type=\"text/javascript\">demiSecTimerStarter($rmcdtime);</script><br>";}

if ($club==0)
{
	include_once GAME_ROOT.'./include/game/clubslct.func.php';
	getclub($name,$c1,$c2,$c3);
	$clubavl[0]=0; $clubavl[1]=$c1; $clubavl[2]=$c2; $clubavl[3]=$c3;
}

if ($gametype==2 && $teamID)
{
	$result = $db->query("SELECT pid,mhp,hp,pls,name,flare FROM {$tablepre}players WHERE teamID='$teamID' AND pid<>'$pid'");
	$teammate_num = $db->num_rows($result);
	$i=0; 
	if ($hp>0) 
	{
		$total_mhp=$mhp; $total_hp=$hp;
	}
	else
	{
		$total_mhp=0; $total_hp=0;
	}
	while($data = $db->fetch_array($result)) 
	{
		$i++; $teammateinfo[$i]=$data; $teammateinfo[$i]['dummy']=0;
		if ($teammateinfo[$i]['pls']==$pls && $teammateinfo[$i]['hp']>0)
		{
			$total_mhp+=$teammateinfo[$i]['mhp'];
			$total_hp+=$teammateinfo[$i]['hp'];
		}
	}
	for ($k=$i+1; $k<=5; $k++) { $teammateinfo[$k]['dummy']=1; $teammateinfo[$k]['pls']=-1; }
	
	include_once GAME_ROOT.'./include/game/gametype.func.php';
	if (!check_teamfight_groupattack_setting() || !$teamID) unset($total_mhp);	
}
else $teammate_num=0;

$auratext="";
if ($auraa>=$now) { $tleft=$auraa-$now; $auratext.='<img src="img/auraA.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurab>=$now) { $tleft=$aurab-$now; $auratext.='<img src="img/auraB.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurac>=$now) { $tleft=$aurac-$now; $auratext.='<img src="img/auraC.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurad>=$now) { $tleft=$aurad-$now; $auratext.='<img src="img/auraD.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($club==16 && $lvl>=25) $auratext.='<img src="img/auraE.gif" title="每个光环将提升基础伤害30%" width="24" height="24">';
if ($debuffa>=$now) { $tleft=$debuffa-$now; $auratext.='<img src="img/debuffA.png" title="恶灵缠绕，物理伤害输出降低20%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($debuffb>=$now) { $tleft=$debuffb-$now; $auratext.='<img src="img/debuffB.gif" title="恐惧状态，受到物理伤害增加20%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($debuffc>=$now) { $tleft=$debuffb-$now; $auratext.='<img src="img/debuffC.gif" title="灵魂腐蚀，基础攻防降低60%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($dcloak>=$now) { $tleft=$dcloak-$now; $auratext.='<img src="img/cloak.gif" title="隐身状态&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($club==23 && $sktime<1) { $auratext.='<img src="img/duel.gif" title="决斗状态" width="24" height="24">'; }

$lowercasealphabet='abcde';
$randstring=$lowercasealphabet[rand(0,4)];
$trick['a']=$lowercasealphabet[rand(0,25)];
$trick['b']=$lowercasealphabet[rand(0,25)];
$trick['c']=$lowercasealphabet[rand(0,25)];
$trick['d']=$lowercasealphabet[rand(0,25)];
$trick['e']=$lowercasealphabet[rand(0,25)];
$trick[$randstring]=$vcode;
		
include template('game');

?>
