<?php

define('CURSCRIPT', 'winner');

require './include/common.inc.php';

if ($server_addr!=$cache_server_addr && $is_cache_server)
{
        header("Location: {$server_addr}winner.php");
        exit(); 
}

if(!isset($command)){$command = 'ref';}
if($command == 'info') {
	$result = $db->query("SELECT * FROM {$tablepre}winners WHERE gid='$gnum' LIMIT 1");
	$pdata = $db->fetch_array($result);
	$pdata['gdate'] = floor($pdata['gtime']/3600).':'.floor($pdata['gtime']%3600/60).':'.($pdata['gtime']%60);
	$pdata['gsdate'] = date("m/d/Y H:i:s",$pdata['gstime']);
	$pdata['gedate'] = date("m/d/Y H:i:s",$pdata['getime']);
	extract($pdata);
	include GAME_ROOT.'./include/game.func.php';
	init_playerdata();
	init_profile();
} elseif($command == 'news') {
	//include  GAME_ROOT.'./include/news.func.php';
	$hnewsfile = GAME_ROOT."./gamedata/bak/{$gnum}_newsinfo.html";
	if(file_exists($hnewsfile)){
		$hnewsinfo = readover($hnewsfile);
	}
} else {
	if(!isset($start) || !$start){
		$result = $db->query("SELECT gid,gametype,name,icon,gd,wep,wmode,getime,motto,hdp,hdmg,hkp,hkill,winnum,namelist,gdlist,iconlist,weplist,teamID FROM {$tablepre}winners ORDER BY gid desc LIMIT $winlimit");
	} else {
		$result = $db->query("SELECT gid,gametype,name,icon,gd,wep,wmode,getime,motto,hdp,hdmg,hkp,hkill,winnum,namelist,gdlist,iconlist,weplist,teamID FROM {$tablepre}winners WHERE gid<='$start' ORDER BY gid desc LIMIT $winlimit");
	}
	while($wdata = $db->fetch_array($result)) {
		$wdata['date'] = date("Y-m-d",$wdata['getime']);
		$wdata['time'] = date("H:i:s",$wdata['getime']);
		$wdata['iconImg'] = $wdata['gd'] == 'f' ? 'f_'.$wdata['icon'].'.gif' : 'm_'.$wdata['icon'].'.gif';
		if ($wdata['gametype']==2 && $wdata['wmode']==2)
		{
			$arr=explode(",",$wdata['namelist']);
			for ($i=0; $i<$wdata['winnum']; $i++) $wdata["winname$i"]=$arr[$i];
			$arr=explode(",",$wdata['gdlist']);
			for ($i=0; $i<$wdata['winnum']; $i++) $wdata["wingd$i"]=$arr[$i];
			$arr=explode(",",$wdata['iconlist']);
			for ($i=0; $i<$wdata['winnum']; $i++) $wdata["winicon$i"]=$arr[$i];
			$arr=explode(",",$wdata['weplist']);
			for ($i=0; $i<$wdata['winnum']; $i++) $wdata["winwep$i"]=$arr[$i];
			for ($i=0; $i<$wdata['winnum']; $i++)
				$wdata["iconImg$i"] = $wdata["wingd$i"] == 'f' ? 'f_'.$wdata["winicon$i"].'.gif' : 'm_'.$wdata["winicon$i"].'.gif';
		}
		$winfo[$wdata['gid']] = $wdata;
	}
	$listnum = floor($gamenum/$winlimit);

	for($i=0;$i<$listnum;$i++) {
		$snum = ($listnum-$i)*$winlimit;
		$enum = $snum-$winlimit+1;
		$listinfo .= "<input style='width: 120px;' type='button' value='{$snum} ~ {$enum} 回' onClick=\"document['list']['start'].value = '$snum'; document['list'].submit();\">";
		if(is_int(($i+1)/3)&&$i){$listinfo .= '<br>';}
	}
}

for ($i=0; $i<=10; $i++) $p1[$i]=$i;

include template('winner');

?>
