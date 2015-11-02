<?php

if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function death($death, $kname = '', $ktype = 0, $annex = '') {
	global $now, $db, $tablepre, $alivenum, $deathnum, $name, $state, $deathtime, $type, $lvl, $bid, $killmsginfo, $typeinfo, $hp, $mhp, $wp, $wk, $wg, $wc, $wd, $wf, $sp, $msp, $club, $pls , $nick;
	global $wep, $wepsk, $wepe, $weps, $arhe, $arhs, $arbe, $arbs, $arae, $aras, $arfe, $arafs ,$inf;
	global $gamestate,$areanum,$starttime,$now,$areatime,$areahour;
	if (! $death) {
		return;
	}
	$hp = 0;
	if ($death == 'N') {
		$state = 20;
	} elseif ($death == 'P') {
		$state = 21;
	} elseif ($death == 'K') {
		$state = 22;
	} elseif ($death == 'G') {
		$state = 23;
	} elseif ($death == 'C') {
		$state = 24;
	} elseif ($death == 'D') {
		$state = 25;
	} elseif ($death == 'F') {
		$state = 29;
	} elseif ($death == 'J') {
		$state = 23;
	}elseif ($death == 'poison') {
		$state = 26;
	} elseif ($death == 'trap') {
		$state = 27;
	} elseif ($death == 'event') {
		$state = 13;
	} elseif ($death == 'hack') {
		$state = 14;
	} elseif ($death == 'pmove') {
		$state = 12;
	} elseif ($death == 'Pmove') {
		$state = 12;
	} elseif ($death == 'Bmove') {
		$state = 19;
	} elseif ($death == 'hsmove') {
		$state = 17;
	} elseif ($death == 'umove') {
		$state = 18;
	} elseif ($death == 'button') {
		$state = 30;
	} elseif ($death == 'suiside') {
		$state = 31;
	} elseif ($death == 'gradius') {
		$state = 33;
	} elseif ($death == 'SCP') {
		$state = 34;
	} elseif ($death == 'salv'){
		$state = 35;
	} elseif ($death == 'kagari1'){
		$state = 36;
	} elseif ($death == 'kagari2'){
		$state = 37;
	} elseif ($death == 'kagari3'){
		$state = 38;
	} elseif ($death == 'gg'){
		$state = 39;
	} elseif ($death == 'thunde'){
		$state = 40;
	} elseif ($death == 'fake_dn'){
		$state = 28;
	} elseif ($death == 'team_gg'){
		$state = 41;
	} elseif ($death == 'anlabomb'){
		$state = 42;
	} elseif ($death == 'failtrapcvt'){
		$state = 44;
	} elseif ($death == 'fishbomb'){
		$state = 46;
	} elseif ($death == 'cursedeath'){
		$state = 47;
	} else {
		$state = 10;
	}
	
	$killmsg = '';
	if ($ktype == 0 && $kname) {
		$result = $db->query ( "SELECT killmsg FROM {$tablepre}users WHERE username = '$kname'" );
		$killmsg = $db->result ( $result, 0 );
	} elseif ($ktype != 0 && $kname) {
		$killmsg = $killmsginfo [$ktype];
		$kname = "$typeinfo[$ktype] $kname";
	} else {
		$kname = '';
		$killmsg = '';
	}
	
	if (! $type) {
		$result = $db->query ( "SELECT lastword FROM {$tablepre}users WHERE username = '$name'" );
		$lastword = $db->result ( $result, 0 );
		$lwname = $typeinfo [$type] . ' ' . $name;
		/*$result = $db->query("SELECT pls FROM {$tablepre}players WHERE name = '$name' AND type = '$type'");
		$pls = $db->result($result, 0);*/
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	}
	$deathtime = $now;
	$result = $db->query("SELECT nick FROM {$tablepre}players WHERE name = '$kname' AND type = '$ktype'");
	$knick = $db->result($result, 0);
	
	global $gametype2lastword;
	if ($gametype2lastword != '' && substr($gametype2lastword,0,1)=='/') $lastword=$gametype2lastword;
	
	if ($death=='anlabomb')
		if ($kname==$name && $ktype==0)
		{
			addnews ( $now, 'death' . $state, $name, $type, '/self', $annex, $lastword );
			$state=43;
		}
		else  addnews ( $now, 'death' . $state, $name, $type, $knick.' '.$kname, $annex, $lastword );
	else  addnews ( $now, 'death' . $state, $name, $type, $knick.' '.$kname, $annex, $lastword );
	//$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
	
	if ($type==0 && $club==99 && ($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" || $death=="D" || $death=="F" || $death=="J" || $death=="trap"))	
	{
		global $souls; 
		addnews($now,'revival',$name);	//玩家春哥附体称号的处理
		$hp=$mhp; $sp=$msp;
		$club=24; $state=0;
		$souls=round($souls/2);
		$alivenum++;
	}
	
	//复位属性复活
	if ($type==0 && strpos($wepsk,'t')!==false && ($club==53)&&($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" || $death=="D" || $death=="F" || $death=="J"))	
	{
		global $gempower,$pls,$typls,$tyowner,$name,$plsinfo;
		$mhp-=round($mhp*0.2);$msp-=round($msp*0.25);
		$wepe=round($wepe*0.85);
		if($wepe<=2){$weps=0;}
		$hp=$mhp; $sp=$msp; $inf='';
		$state=0;
		$alivenum++;
		if(($gempower>=250)&&($wep=='＜厄环＞')&&($typls==$pls)&&($tyowner==$name)){
		addnews($now,'gem_wep_magic',$name,'＜厄环＞',$plsinfo[$typls]);
		}else{
		addnews($now,'therest',$name);	
		addnews($now,'theresthack',$name);	
		}
	}
	
	global $wepexp,$gemwepfoinfo;
	if ($type==0 &&($club==52)&&($wepexp==0)&&(in_array($wep,$gemwepfoinfo))&&($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" || $death=="D" || $death=="F" || $death=="J" ||$death=="trap"))	
	{
		addnews($now,'therest',$name);	//获得复位时的效果
		$hp=$mhp; $sp=$msp; $inf='';
		$wepexp++; $state=0;
		$alivenum++;
	}
	
	if($club==21){
	global $log;
		$hart=$name.'的人质证明';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$hart' AND arts = '1'");
		if($db->num_rows($result)) {
			$log.="<span class='lime'>伴随着你的死亡，你的人质们都作鸟兽散了。</span><br>";
			$db->query("UPDATE {$tablepre}players SET arb='内衣',arbk='DN',arbe='0',arbs='∞',arbsk='',art='',artk='',arte='0',arts='0',artsk='' WHERE art = '$hart' AND arts = '1'");
		}
	}
	
	//死亡时灵魂绑定的道具也会消失
	global $wep,$arb,$arh,$ara,$arf,$art,$itm1,$itm2,$itm3,$itm4,$itm5,$itm6;
	global $weps,$arbs,$arhs,$aras,$arfs,$arts,$itms1,$itms2,$itms3,$itms4,$itms5,$itms6;
	global $wepe,$arbe,$arhe,$arae,$arfe,$arte,$itme1,$itme2,$itme3,$itme4,$itme5,$itme6;
	global $wepk,$arbk,$arhk,$arak,$arfk,$artk,$itmk1,$itmk2,$itmk3,$itmk4,$itmk5,$itmk6;
	global $wepsk,$arbsk,$arhsk,$arask,$arfsk,$artsk,$itmsk1,$itmsk2,$itmsk3,$itmsk4,$itmsk5,$itmsk6;
	global $log;
		for($i = 1;$i <= 6;$i++){
			if(strpos(${'itmsk'.$i},'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">${'itm'.$i}</span>也化作灰烬消散了。<br>";
			${'itm'.$i} = ${'itmk'.$i} = ${'itmsk'.$i} = '';
			${'itme'.$i} = ${'itms'.$i} = 0;
			//return;
			}
			if(strpos($wepsk,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$wep}</span>也化作灰烬消散了。<br>";
			$wep='拳头';$wepk ='WN';$wepsk ='';
			$weps='∞';$wepe = 0;
			}
			if(strpos($arbsk,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$arb}</span>也化作灰烬消散了。<br>";
			$arb='内衣';$arbk ='DN';$arbsk ='';
			$arbs='∞';$arbe = 0;
			}
			if(strpos($arhsk,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$arh}</span>也化作灰烬消散了。<br>";
			$arh=$arhk=$arhsk ='';
			$arhs=$arhe = 0;
			}
			if(strpos($arask,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$ara}</span>也化作灰烬消散了。<br>";
			$ara=$arak=$arask ='';
			$aras=$arae = 0;
			}
			if(strpos($arfsk,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$arf}</span>也化作灰烬消散了。<br>";
			$arf=$arfk=$arfsk ='';
			$arfs=$arfe = 0;
			}
			if(strpos($artsk,'v')!==false){
			$log .= "伴随着你的死亡，<span class=\"yellow\">{$art}</span>也化作灰烬消散了。<br>";
			$art=$artk=$artsk ='';
			$arts=$arte = 0;
			}
		}
	
	$alivenum --;
	$deathnum ++;
	save_gameinfo ();
	
	return $killmsg;
}


function kill($death, $dname, $dtype = 0, $dpid = 0, $annex = '') {
	global $now, $db, $tablepre, $alivenum, $deathnum, $name, $w_state, $type, $pid, $typeinfo, $pls, $lwinfo, $w_achievement;
	
	if (! $death || ! $dname) {
		return;
	}
	
	if ($death == 'N') {
		$w_state = 20;
	} elseif ($death == 'P') {
		$w_state = 21;
	} elseif ($death == 'K') {
		$w_state = 22;
	} elseif ($death == 'G') {
		$w_state = 23;
	} elseif ($death == 'J') {
		$w_state = 23;
	} elseif ($death == 'C') {
		$w_state = 24;
	} elseif ($death == 'D') {
		$w_state = 25;
	} elseif ($death == 'F') {
		$w_state = 29;
	} elseif ($death == 'dn') {
		$w_state = 28;
	} elseif ($death == 'gg'){
		$w_state = 39;
	} elseif ($death == 'team_gg'){
		$w_state = 41;
	} elseif ($death == 'anlabomb'){
		$w_state = 42;
	} elseif ($death == 'nuclbomb'){
		$w_state = 45;
	} elseif ($death == 'fishbomb'){
		$w_state = 46;
	} else {
		$w_state = 10;
	}
	
	$killmsg = '';
	$result = $db->query ( "SELECT killmsg FROM {$tablepre}users WHERE username = '$name'" );
	$killmsg = $db->result ( $result, 0 );
	
	if (! $dtype) {
		//$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
		$alivenum --;
	}
	$deathnum ++;
	
	
	if ($dtype) {
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		if (is_array ( $lwinfo [$dtype] )) {
			$lastword = $lwinfo [$dtype] [$dname];
		} else {
			$lastword = $lwinfo [$dtype];
		}
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	} else {
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		$result = $db->query ( "SELECT lastword FROM {$tablepre}users WHERE username = '$dname'" );
		$lastword = $db->result ( $result, 0 );
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	}
	$result = $db->query("SELECT nick FROM {$tablepre}players WHERE name = '$name' AND type = '$type'");
	$knick = $db->result($result, 0);
	global $gametype2lastword;
	if ($gametype2lastword != '' && substr($gametype2lastword,0,1)=='/') $lastword=$gametype2lastword;
	
	if ($death=='anlabomb')
		if ($annex==$dname)
		{
			addnews ( $now, 'death' . $w_state, $dname, $dtype, '/self', $lastword );
			$w_state=43;
		}
		else  addnews ( $now, 'death' . $w_state, $dname, $dtype, $annex, $lastword );
	else  addnews ( $now, 'death' . $w_state, $dname, $dtype, $knick.' '.$name, $annex, $lastword );
	
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid=$dpid" );
	$res=$db->fetch_array($result);
	$revivaled=false;
	if ($res['type']==0 && $res['club']==99 && ($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" ||$death=="D" || $death=="F" || $death=="J" || $death=="trap"))	
	{
		addnews($now,'revival',$res['name']);	//玩家春哥附体称号的处理
		$db->query ( "UPDATE {$tablepre}players SET hp=mhp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET sp=msp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET club=24 WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET state=0 WHERE pid=$dpid" );
		$new_souls=round($res['souls']/2);
		$db->query ( "UPDATE {$tablepre}players SET souls=$new_souls WHERE pid=$dpid" );
		$alivenum++;
		$revivaled=true;
	}
	if (!$revivaled) $db->query ( "UPDATE {$tablepre}players SET hp='0',endtime='$now',deathtime='$now',bid='$pid',state='$w_state' WHERE pid=$dpid" );
//	if($dtype == 1 || $dtype == 9){
//		global $rdown,$bdown;
//		if($dtype == 1){
//			$rdown = 1;
//			storyputchat($now,'rdown');
//		}elseif($dtype == 9){
//			$bdown = 1;
//			storyputchat($now,'bdown');
//		}			
//	}
	save_gameinfo ();
	return $killmsg;
}

function kill3($death, $name, $type, $dname, $dtype = 0, $dpid = 0, $annex = '') {
	global $now, $db, $tablepre, $alivenum, $deathnum, $pid, $typeinfo, $pls, $lwinfo;
	
	if (! $death || ! $dname) {
		return;
	}
	
	if ($death == 'N') {
		$w_state = 20;
	} elseif ($death == 'P') {
		$w_state = 21;
	} elseif ($death == 'K') {
		$w_state = 22;
	} elseif ($death == 'G') {
		$w_state = 23;
	} elseif ($death == 'J') {
		$w_state = 23;
	} elseif ($death == 'C') {
		$w_state = 24;
	} elseif ($death == 'D') {
		$w_state = 25;
	} elseif ($death == 'F') {
		$w_state = 29;
	} elseif ($death == 'dn') {
		$w_state = 28;
	} elseif ($death == 'team_gg'){
		$w_state = 41;
	} else {
		$w_state = 10;
	}
	
	$killmsg = '';
	$result = $db->query ( "SELECT killmsg FROM {$tablepre}users WHERE username = '$name'" );
	$killmsg = $db->result ( $result, 0 );
	
	if (! $dtype) {
		//$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
		$alivenum --;
	}
	$deathnum ++;
	
	
	if ($dtype) {
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		if (is_array ( $lwinfo [$dtype] )) {
			$lastword = $lwinfo [$dtype] [$dname];
		} else {
			$lastword = $lwinfo [$dtype];
		}
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	} else {
		$lwname = $typeinfo [$dtype] . ' ' . $dname;
		$result = $db->query ( "SELECT lastword FROM {$tablepre}users WHERE username = '$dname'" );
		$lastword = $db->result ( $result, 0 );
		
		$db->query ( "INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('3','$now','$lwname','$pls','$lastword')" );
	}
	$result = $db->query("SELECT nick FROM {$tablepre}players WHERE name = '$name' AND type = '$type'");
	$knick = $db->result($result, 0);
	global $gametype2lastword;
	if ($gametype2lastword != '' && substr($gametype2lastword,0,1)=='/') $lastword=$gametype2lastword;

	addnews ( $now, 'death' . $w_state, $dname, $dtype, $knick.' '.$name, $annex, $lastword );
	
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid=$dpid" );
	$res=$db->fetch_array($result);
	$revivaled=false;
	if ($res['type']==0 && $res['club']==99 && ($death=="N" || $death=="P" || $death=="K" || $death=="G" || $death=="C" ||$death=="D" || $death=="F" || $death=="J" || $death=="trap"))	
	{
		addnews($now,'revival',$res['name']);	//玩家春哥附体称号的处理
		$db->query ( "UPDATE {$tablepre}players SET hp=mhp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET sp=msp WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET club=24 WHERE pid=$dpid" );
		$db->query ( "UPDATE {$tablepre}players SET state=0 WHERE pid=$dpid" );
		$alivenum++;
		$revivaled=true;
	}
	if (!$revivaled) $db->query ( "UPDATE {$tablepre}players SET hp='0',endtime='$now',deathtime='$now',bid='$pid',state='$w_state' WHERE pid=$dpid" );
//	if($dtype == 1 || $dtype == 9){
//		global $rdown,$bdown;
//		if($dtype == 1){
//			$rdown = 1;
//			storyputchat($now,'rdown');
//		}elseif($dtype == 9){
//			$bdown = 1;
//			storyputchat($now,'bdown');
//		}			
//	}
	
	
	save_gameinfo ();
	return $killmsg;
}

function lvlup(&$lvl, &$exp, $isplayer = 1) {
	global $baseexp;
	$up_exp_temp = round ( (2 * $lvl + 1) * $baseexp );
	if ($exp >= $up_exp_temp && $lvl < 255) {
		if ($isplayer) {
			$perfix = '';
		} else {
			$perfix = 'w_';
		}
		global ${$perfix . 'name'}, ${$perfix . 'hp'}, ${$perfix . 'mhp'}, ${$perfix . 'sp'}, ${$perfix . 'msp'}, ${$perfix . 'att'}, ${$perfix . 'def'}, ${$perfix . 'upexp'}, ${$perfix . 'club'}, ${$perfix . 'type'}, ${$perfix . 'skillpoint'},${$perfix . 'money'};
		global ${$perfix . 'wp'}, ${$perfix . 'wk'}, ${$perfix . 'wc'}, ${$perfix . 'wg'}, ${$perfix . 'wd'}, ${$perfix . 'wf'};
		$sklanginfo = Array ('wp' => '殴熟', 'wk' => '斩熟', 'wg' => '射熟', 'wc' => '投熟', 'wd' => '爆熟', 'wf' => '灵熟', 'all' => '全系熟练度', 'wind' => '全系熟练度','money'=>'金钱' );
		$sknlist = Array (1 => 'wp', 2 => 'wk', 3 => 'wc', 4 => 'wg', 5 => 'wd',6=>'wind', 9 => 'wf', 16 => 'all',11=>'money' );
		$skname = $sknlist [${$perfix . 'club'}];
		//升级判断
		$lvup = 1 + floor ( ($exp - $up_exp_temp) / $baseexp / 2 );
		$lvup = $lvup > 255 - $lvl ? 255 - $lvl : $lvup;
		if (($lvl<19)&&(($lvup+$lvl)>=19)&&(${$perfix . 'club'}==26)){
			global $db,$tablepre;
			$ta=${$perfix . 'name'}."语录";
			$db->query( "UPDATE {$tablepre}players SET arte='3' WHERE art = '$ta' AND type>0" );
		}
		$lvuphp = $lvupatt = $lvupdef = $lvupskill = $lvupsp = $lvupspref = 0;
		for($i = 0; $i < $lvup; $i += 1) {
			if (${$perfix . 'club'} == 14) {
				$lvuphp += rand ( 12, 15 );
			} else if (${$perfix . 'club'} == 16) {
				$lvuphp += rand ( 10, 13 );
			} else if (${$perfix . 'club'} == 20) {
				$lvuphp += rand ( 10, 13 );
			} else {
				$lvuphp += rand ( 8, 11 );
			}
			$lvupsp += rand(4,6);
			if (${$perfix . 'club'} == 14) {
				$lvupatt += rand ( 4, 6 );
				$lvupdef += rand ( 5, 8 );
			} else {
				$lvupatt += rand ( 2, 4 );
				$lvupdef += rand ( 3, 5 );
			}
			if ($skname == 'all') {
				$lvupskill += rand ( 1, 3 );
			}elseif ($skname == 'money') {
				if (($i+$lvl)>=11){
					$lvupskill += 200;
				}
			}elseif ($skname == 'wind') {
				if (($i+$lvl)>=15){
					$lvupskill += rand(9,11);
				}
			} elseif ($skname == 'wd' || $skname == 'wf') {
				$lvupskill += rand ( 3, 5 );
			}elseif($skname){
				$lvupskill += rand ( 4, 6 );
			}
			$lvupspref += round(${$perfix . 'msp'} * 0.1);		
		}
		$lvl += $lvup;
		$up_exp_temp = round ( (2 * $lvl + 1) * $baseexp );
		if ($lvl >= 255) {
			$lvl = 255;
			$exp = $up_exp_temp;
		}
		${$perfix . 'upexp'} = $up_exp_temp;
		${$perfix . 'hp'} += $lvuphp;
		${$perfix . 'mhp'} += $lvuphp;
		${$perfix . 'sp'} += $lvupsp;
		${$perfix . 'msp'} += $lvupsp;
		${$perfix . 'att'} += $lvupatt;
		${$perfix . 'def'} += $lvupdef;
		${$perfix . 'skillpoint'} += $lvup;
		if (($skname == 'all')||($skname=='wind')) {
			${$perfix . 'wp'} += $lvupskill;
			${$perfix . 'wk'} += $lvupskill;
			${$perfix . 'wg'} += $lvupskill;
			${$perfix . 'wc'} += $lvupskill;
			${$perfix . 'wd'} += $lvupskill;
			${$perfix . 'wf'} += $lvupskill;
		} elseif ($skname) {
			${$perfix . $skname} += $lvupskill;
		}
		
		if (${$perfix . 'sp'}+$lvupspref >= ${$perfix . 'msp'}) {
			$lvupspref =  ${$perfix . 'msp'} - ${$perfix . 'sp'};
			
		}
		${$perfix . 'sp'} += $lvupspref;
		if (($skname) && ($lvupskill>0)) {
			$sklog = "，{$sklanginfo[$skname]}+{$lvupskill}";
		}
		if ($isplayer) {
			global $log;
			$log .= "<span class=\"yellow\">你升了{$lvup}级！生命上限+{$lvuphp}，体力上限+{$lvupsp}，攻击+{$lvupatt}，防御+{$lvupdef}{$sklog}，体力恢复了{$lvupspref}，获得了{$lvup}点技能点！</span><br>";
		} elseif (! $w_type) {
			global $w_pid, $now;
			$w_log = "<span class=\"yellow\">你升了{$lvup}级！生命上限+{$lvuphp}，体力上限+{$lvupsp}，攻击+{$lvupatt}，防御+{$lvupdef}{$sklog}，体力恢复了{$lvupspref}，获得了{$lvup}点技能点！</span><br>";
			logsave ( $w_pid, $now, $w_log,'s');
		}
	} elseif ($lvl >= 255) {
		$lvl = 255;
		$exp = $up_exp_temp;
	}
	return;
}

/*function lvlup(&$lvl, &$exp, $isplayer = 1) {
	global $log,$baseexp;
	$up_exp_temp = round((2*$lvl+1)*$baseexp);
	if($exp >= $up_exp_temp && $lvl<255) {
		if($isplayer){
			global $name,$hp,$mhp,$sp,$msp,$att,$def,$upexp,$club;
			$sknlist=Array(1=>'wp',2=>'wk',3=>'wc',4=>'wg',5=>'wd',9=>'wf');//每级提升熟练
			
			$skname=$sknlist[$club];
			if($skname){
				global ${$skname},$skilllaninfo;
			}
			$lvup = 1+floor(($exp - $up_exp_temp)/$baseexp/2);
			$lvup = $lvup > 255-$lvl ? 255-$lvl : $lvup;
			//$log .="$lvup<br>";
			$lvuphp = $lvupatt = $lvupdef = $lvupskill =0;
			
			for ($i=0;$i<$lvup;$i+=1){
				$lvuphp += rand(8,10);$lvupatt += rand(2,4);$lvupdef += rand(3,5);
				if($skname){
					$lvupskill += rand(3,5);
				}
				$sp += ($msp * 0.1);
			}
			$lvl += $lvup;$up_exp_temp = round((2*$lvl+1)*$baseexp);

			if($lvl>=255){$lvl=255;$exp=$up_exp_temp;}
			$upexp=$up_exp_temp;
			$hp += $lvuphp;$mhp += $lvuphp;
			$att += $lvupatt;$def += $lvupdef;
			${$skname} += $lvupskill;
			if($sp >= $msp){$sp = $msp;}
			if($skname){
				$sklog = "，{$skilllaninfo[$skname]}+{$lvupskill}";
			}
			$log .= "<span class=\"yellow\">你升了{$lvup}级！生命+{$lvuphp}，攻击+{$lvupatt}，防御+{$lvupdef}{$sklog}！</span><br>";
		} else {
			global $now,$w_type,$w_pid,$w_name,$w_hp,$w_mhp,$w_sp,$w_msp,$w_att,$w_def,$w_upexp,$w_club;
			$sknlist=Array(1=>'wp',2=>'wk',3=>'wg',4=>'wc',5=>'wd',9=>'wf');//每级提升熟练
			$skname=$sknlist[$w_club];
			if($skname){
				global ${'w_'.$skname},$skilllaninfo;
			}
			$lvup = 1+floor(($exp - $up_exp_temp)/$baseexp/2);
			$lvup = $lvup > 255-$lvl ? 255-$lvl : $lvup;
			$lvuphp = $lvupatt = $lvupdef = $lvupskill = 0;
			for ($i=0;$i<$lvup;$i+=1){
				$lvuphp += rand(8,10);$lvupatt += rand(2,4);$lvupdef += rand(3,5);
				if($skname){
					$lvupskill += rand(3,5);
				}
				$w_sp += ($w_msp * 0.1);
			}
			$lvl += $lvup;$up_exp_temp = round((2*$lvl+1)*$baseexp);

			if($lvl>=255){$lvl=255;$exp=$up_exp_temp;}
			$w_upexp=$up_exp_temp;
			$w_hp += $lvuphp;$w_mhp += $lvuphp;
			$w_att += $lvupatt;$w_def += $lvupdef;
			${'w_'.$skname} += $lvupskill;
			if($w_sp >= $w_msp){$w_sp = $w_msp;}
			if(!$w_type){
				if($skname){
					$sklog = "，{$skilllaninfo[$skname]}+{$lvupskill}";
				}
				$w_log = "<span class=\"yellow\">你升了{$lvup}级！生命+{$lvuphp}，攻击+{$lvupatt}，防御+{$lvupdef}{$sklog}！</span><br>";
				logsave($w_pid,$now,$w_log);
			}
		}
	} elseif($lvl >= 255){$lvl=255;$exp=$up_exp_temp;}
	return;
}*/

//玩家被攻击时的生命恢复未实现


function rest($command) {
	global $now, $log, $mode, $cmd, $state, $endtime, $hp, $mhp, $sp, $rage,$msp, $sleep_time, $heal_time, $restinfo, $pose, $inf,$club,$exdmginf,$rage;
	global $wepsk,$arhsk,$arbsk,$arask,$arfsk,$artsk;
	
	//回复怒气
	
	$resttime=$now-$endtime;
	$addrage=floor($resttime*$resttime/40)+1;
	$checkcs=$wepsk.$arhsk.$arbsk.$arask.$arfsk.$artsk;
	if (strpos($checkcs,'c')!==false) {$addrage=round($addrage*1.4);}
	if ($resttime<=1) $addrage=0;
	$res=$rage+$addrage;
	if ($res>100){
		$res=100;
		$addrage=100-$rage;
	}
	$rage=$res;
	
	$log .= "你休息了<span class=\"yellow\">$resttime</span>秒，蓄积了<span class=\"yellow\">$addrage</span>点怒气。<br>";
	
	if ($state == 1) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldsp = $sp;
		$upsp = round ( $msp * $resttime / $sleep_time / 100 );
		if ($pose == 5) {
			$upsp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$upsp = round ( $upsp / 2 );
		}
		if ($club ==16){
			$upsp *= 2;
		}
		$sp += $upsp;
		if ($sp >= $msp) {
			$sp = $msp;
		}
		$upsp = $sp - $oldsp;
		$log .= "你的体力恢复了<span class=\"yellow\">$upsp</span>点。<br>";
	} elseif ($state == 2) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldhp = $hp;
		$uphp = round ( $mhp * $resttime / $heal_time / 100 );
		if ($pose == 5) {
			$uphp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$uphp = round ( $uphp / 2 );
		}
		if ($club ==16){
			$uphp *= 2;
		}
		if ($oldhp>$mhp){
			$uphp=0;
			$hp += $uphp;
		}else{
			$hp += $uphp;
			if ($hp >= $mhp) {
				$hp = $mhp;
			}
			$uphp = $hp - $oldhp;
		}
		$log .= "你的生命恢复了<span class=\"yellow\">$uphp</span>点。<br>";
	} elseif ($state == 3) {
		$resttime = $now - $endtime;
		$endtime = $now;
		$oldsp = $sp;
		$upsp = round ( $msp * $resttime / $sleep_time / 100 );
		if ($pose == 5) {
			$upsp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$upsp = round ( $upsp / 2 );
		}
		if ($club ==16){
			$upsp *= 2;
		}
		$sp += $upsp;
		if ($sp >= $msp) {
			$sp = $msp;
		}
		$upsp = $sp - $oldsp;
		$oldhp = $hp;
		$uphp = round ( $mhp * $resttime / $heal_time / 100 );
		if ($pose == 5) {
			$uphp *= 2;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$uphp = round ( $uphp / 2 );
		}
		if ($club ==16){
			$uphp *= 2;
		}
		
		if ($oldhp>$mhp){
			$uphp=0;
			$hp += $uphp;
		}else{
			$hp += $uphp;
			if ($hp >= $mhp) {
				$hp = $mhp;
			}
			$uphp = $hp - $oldhp;
		}
		$log .= "你的体力恢复了<span class=\"yellow\">$upsp</span>点，生命恢复了<span class=\"yellow\">$uphp</span>点。<br>";
		
		$refintv = 90;
		if ($pose == 5) {
			$refintv -= 30;
		}
		if (strpos ( $inf, 'b' ) !== false) {
			$refintv += 30;
		}
		$spinf = preg_replace("/[h|b|a|f]/", "", $inf);
		$spinflength = strlen($spinf);
		if($spinf){
			$refflag = false;
			do{
				$dice = rand(0,$refintv);
				if($dice + 15 < $resttime){
					$infno = rand(0,$spinflength-1);
					$refinfstr = substr($spinf,$infno,1);
					$inf = str_replace($refinfstr,'',$inf);
					$spinf = str_replace($refinfstr,'',$spinf);
					$log .= "<span class=\"yellow\">你从{$exdmginf[$refinfstr]}状态中恢复了！</span><br>";
					$spinflength -= 1;
					$refflag = true;
				}
				$resttime -= $refintv;
			} while ($resttime > 0 && $spinflength > 0);
			if(!$refflag){
				$log .= "也许是时间不够吧……你没有治好任何异常状态。<br>";
			}
		}
	} else {
		$mode = 'command';
	}
	
	if ($command != 'rest') {
		$state = 0;
		$endtime = $now;
		$mode = 'command';
	}
	return;
}

?>