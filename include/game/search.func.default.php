<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}

function move($moveto = 99) {
	global $name,$db,$tablepre,$log,$pls,$plsinfo,$inf,$hp,$mhp,$sp,$def,$club,$lvl,$arealist,$areanum,$hack,$areainfo,$gamestate,$pose,$weather,$wp;
	global $gamestate;
	global $itm0,$itme0,$itms0,$itmk0,$itmsk0;
	global $wepk,$arb,$art;
	$f=false;
	if ($pls==34 && $gamestate<50) $f=true;
	if ($moveto==34 && $gamestate<50) $f=true;
	$plsnum = sizeof($plsinfo);
	if((strpos($arb,'捆绑式炸药')!==false)||(strpos($art,'的人质证明')!==false)){
		$log .= '你现在是人家的人质，怎么可能让你乱动！？<br>';
		return;
	}
	if(strpos($wepk,'GS')!==false){
		$log .= '你手上还拿着枪械部件呢，还是先放到包里再去探索吧。<br>';
		return;
	}
	if($itm0!=='' && ($itms0>0 || $itms0=='∞')){
		$log .= '你手上还拿着的东西呢，先处理完再行动吧！<br>';
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
		return;
	}
	if(($moveto == 'main')||($moveto < 0 )||($moveto >= $plsnum)){
		$log .= '请选择正确的移动地点。<br>';
		return;
	} elseif($pls == $moveto){
		$log .= '相同地点，不需要移动。<br>';
		return;
	} elseif(array_search($moveto,$arealist) <= $areanum && !$hack){
		$log .= $plsinfo[$moveto].'是禁区，还是离远点吧！<br>';
		return;
	}


	//足部受伤，20；足球社，12；冻伤，30；正常，15；去gamecfg里改吧
	$movesp = 15;
	if ($inf) {
		global $inf_move_sp;
		foreach ($inf_move_sp as $inf_ky => $sp_down) {
			if(strpos($inf,$inf_ky)!==false){$movesp+=$sp_down;}
		}
	}
	//if(strpos($inf, 'f') !== false){ $movesp += 5; }
	//if(strpos($inf, 'i') !== false){ $movesp += 15; }
	if($club == 6){
		if($lvl>=20){
			$movesp -= 14;
		}else{
			$movesp -= 10+floor($lvl/5);
		}
	}

	
	if($sp <= $movesp){
		if($hp <= $movesp){
		$log .= "燃烧生命也无法移动了！<br>还是先休息下吧！<br>";
		return;
		}else{
		$hp-= $movesp;
		$log .= "<span class='red'>你的体力不足以支持你继续移动，<br>但你燃烧了自己的生命强行移动了！</span><br>";
		}
		//$log .= "体力不足，不能移动！<br>还是先睡会儿吧！<br>";
		//return;
	}

	$moved = false;
	$result = $db->query ( "SELECT sktime FROM {$tablepre}users WHERE username='$name'" );
	$sktime = $db->result($result, 0);
	if (($club==23)&&($sktime<1)&&($wp>=50)){
		$wp=$wp-5;
		if ($wp<50) $wp=50;
	}
	if($weather == 11) {//龙卷风
		if($hack){$pls = rand(0,sizeof($plsinfo)-1);}
		else {$pls = rand($areanum+1,sizeof($plsinfo)-1);$pls=$arealist[$pls];}
		$log = ($log . "龙卷风把你吹到了<span class=\"yellow\">$plsinfo[$pls]</span>！<br>");
		$moved = true;
	} elseif($weather == 13) {//冰雹
		$damage = round($mhp/12) + rand(0,20);
		$hp -= $damage;
		$log .= "被<span class=\"blue\">冰雹</span>击中，生命减少了<span class=\"red\">$damage</span>点！<br>";
		if($hp <= 0 ) {
			include_once GAME_ROOT.'./include/state.func.php';
			death('hsmove');
			return;
//		} else {
//			$pls = $moveto;
//			$log .= "消耗<span class=\"yellow\">{$movesp}</span>点体力，移动到了<span class=\"yellow\">$plsinfo[$pls]</span>。<br>";
		}
	} elseif($weather == 14){//离子暴
		$dice = rand(0,8);
		if($dice ==0 && strpos($inf,'e')===false){
			$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"yellow\">身体麻痹</span>了！<br>";
			$inf = str_replace('e','',$inf);
			$inf .= 'e';
		}elseif($dice ==1 && strpos($inf,'w')===false){
			$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"grey\">混乱</span>了！<br>";
			$inf = str_replace('w','',$inf);
			$inf .= 'w';
		}elseif($dice ==2 && (strpos($inf,'w')===false || strpos($inf,'e')===false)){
			if (strpos($inf,'w')===false)
			{
				$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"grey\">混乱</span>了！<br>";
				$inf = str_replace('w','',$inf);
				$inf .= 'w';
			}
			if (strpos($inf,'e')===false)
			{
				$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"yellow\">身体麻痹</span>了！<br>";
				$inf = str_replace('e','',$inf);
				$inf .= 'e';
			}
		}else{
			$log .= "空气中充斥着狂暴的电磁波……<br>";
		}
	} elseif($weather == 15){//辐射尘
		$dice = rand(0,3);
		if($dice == 0){
			$mhpdown = rand(4,8);
			if($mhp > $mhpdown){
				$log .= "空气中弥漫着的<span class=\"green\">放射性尘埃</span>导致你的生命上限减少了<span class=\"red\">{$mhpdown}</span>点！<br>";
				$mhp -= $mhpdown;
				if($hp > $mhp){$hp = $mhp;}
			}
		}elseif ($dice==1 && strpos($inf,'p')===false){
			$log .= "空气中弥漫着的<span class=\"green\">放射性尘埃</span>导致你<span class=\"purple\">中毒</span>了！<br>";
			$inf = str_replace('p','',$inf);
			$inf .= 'p';
		}else{
			$log .= "空气中弥漫着放射性尘埃……<br>";
		}
	} elseif($weather == 16){//臭氧洞
		$dice = rand(0,7);
		if($dice <= 3){
			$defdown = rand(4,8);
			if($def > $defdown){
				$log .= "高强度的<span class=\"purple\">紫外线照射</span>导致你的防御力减少了<span class=\"red\">{$defdown}</span>点！<br>";
				$def -= $defdown;
			}
		}elseif($dice <=5 && strpos($inf,'u')===false){
			$log .= "高强度的<span class=\"purple\">紫外线照射</span>导致你<span class=\"red\">烧伤</span>了！<br>";
			$inf = str_replace('u','',$inf);
			$inf .= 'u';
		}else{
			$log .= "高强度的紫外线灼烧着大地……<br>";
		}
	} 
	if(!$moved) {
		$pls = $moveto;
		if($sp > $movesp){
			$sp -= $movesp;
			$log .= "消耗<span class=\"yellow\">{$movesp}</span>点体力，移动到了<span class=\"yellow\">$plsinfo[$pls]</span>。<br>";
		}elseif(($sp <= $movesp)&&($hp > $movesp)){
			$movehp=$movesp*2;
			$log .= "消耗<span class=\"yellow\">{$movehp}</span>点生命，移动到了<span class=\"yellow\">$plsinfo[$pls]</span>。<br>";
		}
	}else{$f=false;}
	
	if($inf){
		global $infwords,$inf_move_hp;
		foreach ($inf_move_hp as $inf_ky => $o_dmg) {
			if(strpos($inf,$inf_ky)!==false){
				$damage = round($mhp * $o_dmg) + rand(0,15);
				$hp -= $damage;
				$log .= "{$infwords[$inf_ky]}减少了<span class=\"red\">$damage</span>点生命！<br>";
				if($hp <= 0 ){
					include_once GAME_ROOT.'./include/state.func.php';
					if ($inf_ky=='P') {$inf_ky='p';}
					death($inf_ky.'move');
					return;
				}
			}			
		}
		if ((strpos($inf,'P')!==false)&&(rand(1,4)==1)) {
			$inf = str_replace('P','',$inf);
			$log.='你的猛毒状态消失了。<br>';
		}
		if ((strpos($inf,'B')!==false)&&(rand(1,4)==1)) {
			$inf = str_replace('B','',$inf);
			$log.='你的裂伤状态消失了。<br>';
		}
	}
	
	if(($typls==$pls)&&($tyowner==$name)){
		$log.="<span class='clan'>你进入了你的领域之中。</span><br>";
		global $gempower,$gemstate;
		if(($gemstate!=0)&&($gempower<3000)){
		global $club;
			if(($club==49)||($club==53)){
			$gempower=min(3000,$gempower+25);
			}else{
			$gempower=min(1000,$gempower+10);
			}
			$log.="<span class='lime'>领域内充斥着的宝石魔力为你补充了一定的GEM！</span><br>";
		}
	}
	
	if($club==21){
		$hart=$name.'的人质证明';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$hart' AND arts = '1'");
		if($db->num_rows($result)) {
			$log.="<span class='lime'>你将你的人质也拽到了{$plsinfo[$pls]}！</span><br>";
			$db->query("UPDATE {$tablepre}players SET pls=$pls WHERE art='$hart'");
			addnews($now,'hijack',$name,$plsinfo[$pls],'hmove');
		}
	}
	
	$log .= $areainfo[$pls].'<br>';	
	if ($f) {
		if (CURSCRIPT !== 'botservice') $log.="<span id=\"HsUipfcGhU\"></span>";	//刷新页面标记
		return;
	}
	$enemyrate = 40;
	if($gamestate == 40){$enemyrate += 20;}
	elseif($gamestate == 50){$enemyrate += 40;}
	if($pose==3){$enemyrate -= 20;}
	elseif($pose==4){$enemyrate += 10;}
	discover($enemyrate);
	/*
	$enemyrate = 70;
	if($gamestate == 40){$enemyrate += 10;}
	elseif($gamestate == 50){$enemyrate += 15;}
	if($pose==3){$enemyrate -= 20;}
	elseif($pose==4){$enemyrate += 10;}
	discover($enemyrate);
	*/
	return;

}

function search(){
	global $db,$name,$tablepre,$lvl,$log,$pls,$arealist,$areanum,$hack,$plsinfo,$club,$sp,$gamestate,$pose,$weather,$hp,$mhp,$def,$inf,$wp;
	global $itm0,$itme0,$itms0,$itmk0,$itmsk0;
	global $arb,$art;
	if((strpos($arb,'捆绑式炸药')!==false)||(strpos($art,'的人质证明')!==false)){
		$log .= '你现在是人家的人质，怎么可能让你乱动！？<br>';
		return;
	}
	if(strpos($wepk,'GS')!==false){
		$log .= '你手上还拿着枪械部件呢，还是先放到包里再去探索吧。<br>';
		return;
	}
	if($itm0!=='' && ($itms0>0 || $itms0=='∞')){
		$log .= '你手上还拿着的东西呢，先处理完再探索吧！<br>';
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
		return;
	}
	if(array_search($pls,$arealist) <= $areanum && !$hack){
		$log .= $plsinfo[$pls].'是禁区，还是赶快逃跑吧！<br>';
		return;
	}

	//腕部受伤，20；冻伤：30；侦探社，12；正常，15；改到gamecfg
	$schsp =15;
	if ($inf) {
		global $inf_search_sp;
		foreach ($inf_search_sp as $inf_ky => $sp_down) {
			if(strpos($inf,$inf_ky)!==false){$schsp+=$sp_down;}
		}
	}
	//if(strpos($inf, 'a') !== false){ $schsp += 5; }
	//if(strpos($inf, 'i') !== false){ $schsp += 15; }
	if($club == 6){
		if($lvl>=20){
			$schsp -= 14;
		}else{
			$schsp -= 10+floor($lvl/5);
		}
	}

	if($sp <= $schsp){
		if($hp <= $schsp){
		$log .= "燃烧生命也无法探索了！<br>还是先休息下吧！<br>";
		return;
		}else{
		$hp-= $schsp;
		$log .= "<span class='red'>你的体力不足以支持你继续探索，<br>但你燃烧了自己的生命强行探索了！</span><br>";
		}
		//$log .= "体力不足，不能探索！<br>还是先睡会儿吧！<br>";
		//return;	
	}
	
	$result = $db->query ( "SELECT sktime FROM {$tablepre}users WHERE username='$name'" );
	$sktime = $db->result($result, 0);
	if (!$sktime) $sktime=0;
	if (($club==23)&&($sktime<1)&&($wp>=50)){
		$wp=$wp-5;
		if ($wp<50) $wp=50;
	}
	if($weather == 11) {//龙卷风
		if($hack){$pls = rand(0,sizeof($plsinfo)-1);}
		else {$pls = rand($areanum+1,sizeof($plsinfo)-1);$pls=$arealist[$pls];}
		$log = ($log . "龙卷风把你吹到了<span class=\"yellow\">$plsinfo[$pls]</span>！<br>");
		$moved = true;
	} elseif($weather == 13) {//冰雹
		$damage = round($mhp/12) + rand(0,20);
		$hp -= $damage;
		$log .= "被<span class=\"blue\">冰雹</span>击中，生命减少了<span class=\"red\">$damage</span>点！<br>";
		if($hp <= 0 ) {
			include_once GAME_ROOT.'./include/state.func.php';
			death('hsmove');
			return;
//		} else {
//			$pls = $moveto;
//			$log .= "消耗<span class=\"yellow\">{$movesp}</span>点体力，移动到了<span class=\"yellow\">$plsinfo[$pls]</span>。<br>";
		}
	} elseif($weather == 14){//离子暴
		$dice = rand(0,8);
		if($dice ==0 && strpos($inf,'e')===false){
			$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"yellow\">身体麻痹</span>了！<br>";
			$inf = str_replace('e','',$inf);
			$inf .= 'e';
		}elseif($dice ==1 && strpos($inf,'w')===false){
			$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"grey\">混乱</span>了！<br>";
			$inf = str_replace('w','',$inf);
			$inf .= 'w';
		}elseif($dice ==2 && (strpos($inf,'w')===false || strpos($inf,'e')===false)){
			if (strpos($inf,'w')===false)
			{
				$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"grey\">混乱</span>了！<br>";
				$inf = str_replace('w','',$inf);
				$inf .= 'w';
			}
			if (strpos($inf,'e')===false)
			{
				$log .= "空气中充斥着的<span class=\"linen\">狂暴电磁波</span>导致你<span class=\"yellow\">身体麻痹</span>了！<br>";
				$inf = str_replace('e','',$inf);
				$inf .= 'e';
			}
		}else{
			$log .= "空气中充斥着狂暴的电磁波……<br>";
		}
	} elseif($weather == 15){//辐射尘
		$dice = rand(0,3);
		if($dice == 0){
			$mhpdown = rand(4,8);
			if($mhp > $mhpdown){
				$log .= "空气中弥漫着的<span class=\"green\">放射性尘埃</span>导致你的生命上限减少了<span class=\"red\">{$mhpdown}</span>点！<br>";
				$mhp -= $mhpdown;
				if($hp > $mhp){$hp = $mhp;}
			}
		}elseif ($dice==1 && strpos($inf,'p')===false){
			$log .= "空气中弥漫着的<span class=\"green\">放射性尘埃</span>导致你<span class=\"purple\">中毒</span>了！<br>";
			$inf = str_replace('p','',$inf);
			$inf .= 'p';
		}else{
			$log .= "空气中弥漫着放射性尘埃……<br>";
		}
	} elseif($weather == 16){//臭氧洞
		$dice = rand(0,7);
		if($dice <= 3){
			$defdown = rand(4,8);
			if($def > $defdown){
				$log .= "高强度的<span class=\"purple\">紫外线照射</span>导致你的防御力减少了<span class=\"red\">{$defdown}</span>点！<br>";
				$def -= $defdown;
			}
		}elseif($dice <=5 && strpos($inf,'u')===false){
			$log .= "高强度的<span class=\"purple\">紫外线照射</span>导致你<span class=\"red\">烧伤</span>了！<br>";
			$inf = str_replace('u','',$inf);
			$inf .= 'u';
		}else{
			$log .= "高强度的紫外线灼烧着大地……<br>";
		}
	} 
	
	if($sp > $schsp){
	$sp -= $schsp;
	$log .= "消耗<span class=\"yellow\">{$schsp}</span>点体力，你搜索着周围的一切。。。<br>";
	}elseif(($sp <= $schsp)&&($hp > $schsp)){
	$schhp=$schsp*2;
	$log .= "消耗<span class=\"yellow\">{$schhp}</span>点生命，你搜索着周围的一切。。。<br>";
	}
	
	if($inf){
		global $infwords,$inf_search_hp;
		foreach ($inf_search_hp as $inf_ky => $o_dmg) {
			if(strpos($inf,$inf_ky)!==false){
				$damage = round($mhp * $o_dmg) + rand(0,10);
				$hp -= $damage;
				$log .= "{$infwords[$inf_ky]}减少了<span class=\"red\">$damage</span>点生命！<br>";
				if($hp <= 0 ){
					include_once GAME_ROOT.'./include/state.func.php';
					death($inf_ky.'move');
					return;
				}
			}			
		}
		if ((strpos($inf,'P')!==false)&&(rand(1,4)==1)) {
			$inf = str_replace('P','',$inf);
			$log.='你的猛毒状态消失了。<br>';
		}
		if ((strpos($inf,'B')!==false)&&(rand(1,4)==1)) {
			$inf = str_replace('B','',$inf);
			$log.='你的裂伤状态消失了。<br>';
		}
	}
	
	global $typls,$tyowner,$name;
	if(($typls==$pls)&&($tyowner==$name)){
		global $gempower,$gemstate;
		if(($gemstate!=0)&&($gempower<3000)){
			global $club;
			if(($club==49)||($club==53)){
			$gempower=min(3000,$gempower+25);
			}else{
			$gempower=min(1000,$gempower+10);
			}
			$log.="<span class='lime'>领域内充斥着的宝石魔力为你补充了一定的GEM！</span><br>";
		}
	}
	
	/*if(strpos($inf, 'p') !== false){
		$damage = round($mhp/32) + rand(0,5);
		$hp -= $damage;
		$log .= "<span class=\"purple\">毒发</span>减少了<span class=\"red\">$damage</span>点生命！<br>";
		if($hp <= 0 ){
			include_once GAME_ROOT.'./include/state.func.php';
			death('pmove');
			return;
		}
	}
	if(strpos($inf, 'u') !== false){
		$damage = round($mhp/32) + rand(0,15);
		$hp -= $damage;
		$log .= "<span class=\"yellow\">烧伤发作</span>减少了<span class=\"red\">$damage</span>点生命！<br>";
		if($hp <= 0 ){
			include_once GAME_ROOT.'./include/state.func.php';
			death('umove');
			return;
		}
	}*/
	$enemyrate = 40;
	if($gamestate == 40){$enemyrate += 20;}
	elseif($gamestate == 50){$enemyrate += 30;}
	if($pose==3){$enemyrate -= 20;}
	elseif($pose==4){$enemyrate += 10;}
	discover($enemyrate);
//	$log .= '遇敌率'.$enemyrate.'%<br>';
//	if(($gamestate>=40)&&($pose!=3)) {
//		discover(75);
//	} else {
//		discover(30);
//	}
	return;

}

function discover($schmode = 0) {
	global $gametype,$teamID,$art,$pls,$now,$log,$mode,$command,$cmd,$event_obbs,$weather,$pls,$club,$pose,$tactic,$inf,$item_obbs,$enemy_obbs,$trap_min_obbs,$trap_max_obbs,$bid,$db,$tablepre,$gamestate,$corpseprotect,$action,$skills,$dcloak,$rp;
	global $tyowner,$typls,$name;
	$event_dice = rand(0,99);
	if(($event_dice < $event_obbs)||(($art!="Untainted Glory")&&($pls==34)&&($gamestate != 50))){
		include_once GAME_ROOT.'./include/game/event.func.php';
		event();
		$mode = 'command';
		return;
	}
	include_once GAME_ROOT. './include/game/aievent.func.php';//AI事件
	$aidata = false;//用于判断天然呆AI（冴冴这样的）是否已经来到你身后并且很生气
	aievent(20);//触发AI事件的概率
	include_once GAME_ROOT.'./include/game/gametype.func.php';
	$trap_multipler=1;
	if ($gametype==2 && $teamID && check_teamfight_groupattack_setting())
	{
		$result = $db->query("SELECT pid FROM {$tablepre}players WHERE teamID='$teamID' AND pls='$pls' AND hp>0");
		$trap_multipler=$db->num_rows($result);
		if ($trap_multipler<1) $trap_multipler=1;
		$trap_multipler=pow($trap_multipler,0.5);
	}
	$trap_dice=rand(0,99);//随机数，开始判断是否踩陷阱
	//echo $trap_multipler;
	if($trap_dice < $trap_max_obbs*$trap_multipler){ //踩陷阱概率最大值
		$trapresult = $db->query("SELECT * FROM {$tablepre}maptrap WHERE pls = '$pls' ORDER BY itmk DESC");
//		$traplist = Array();
//		while($trap0 = $db->fetch_array($result)){
//			$traplist[$trap0['tid']] = $trap0;
//			if($trap0['itmk'] == 'TOc'){
//				$xtrap = true;
//				$xtrapid = $
//			}
//		}
		$xtrp = $db->fetch_array($trapresult);
		$xtrpflag = false;
		//echo $xtrp['itm'];
		if($xtrp['itmk'] == 'TOc'){
			$xtrpflag = true;
		}
		$trpnum = $db->num_rows($trapresult);
		if($trpnum){//看地图上有没有陷阱	
			//echo "踩陷阱概率：{$real_trap_obbs}%";
			if($xtrpflag){
				global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
				$itm0=$xtrp['itm'];
				$itmk0=$xtrp['itmk'];
				$itme0=$xtrp['itme'];
				$itms0=$xtrp['itms'];
				$itmsk0=$xtrp['itmsk'];
				$tid = $xtrp['tid'];
				$db->query("DELETE FROM {$tablepre}maptrap WHERE tid='$tid'");
				include_once GAME_ROOT.'./include/game/itemmain.func.php';
				itemfind();
				return;
			}else{
				$real_trap_obbs = $trap_min_obbs + $trpnum/4;
				//Anti-Meta RP System Version 2.00 ~ Nemo
				//冴冴我喜欢你！ ~ 四面
				//17rp/177rp+1%
				if($gamestate >= 50) {$real_trap_obbs = $real_trap_obbs + $rp / 177; }
				else{ $real_trap_obbs = $real_trap_obbs + $rp/30; }
				if($pose==1){$real_trap_obbs+=1;}
				elseif($pose==3){$real_trap_obbs+=3;}//攻击和探索姿势略容易踩陷阱
				if($gamestate >= 40){$real_trap_obbs+=3;}//连斗以后略容易踩陷阱
				if($pls == 0){$real_trap_obbs+=15;}//在后台非常容易踩陷阱
				if($club == 6){$real_trap_obbs/=3;}//人肉搜索称号遭遇陷阱概率减少
				if(($typls==$pls)&&($tyowner==$name)){$real_trap_obbs=0;}//在自己的领域中不会遇到陷阱
				$real_trap_obbs*=$trap_multipler;
				//echo $real_trap_obbs;
				if($trap_dice < $real_trap_obbs){//踩陷阱判断
					$itemno = rand(0,$trpnum-1);
					$db->data_seek($trapresult,$itemno);
					$mi=$db->fetch_array($trapresult);
					global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
					$itm0=$mi['itm'];
					$itmk0=$mi['itmk'];
					$itme0=$mi['itme'];
					$itms0=$mi['itms'];
					$itmsk0=$mi['itmsk'];
					$tid=$mi['tid'];
					$db->query("DELETE FROM {$tablepre}maptrap WHERE tid='$tid'");
					if($itms0){
						include_once GAME_ROOT.'./include/game/itemmain.func.php';
						itemfind();
						return;
					}
				}
			}
		}
	}
//	$trap_dice =  rand(0,99);
//	if($pose==1){$trap_dice-=5;}
//	elseif($pose==3){$trap_dice-=8;}//攻击和探索姿势略容易踩陷阱
//	if($gamestate >= 40){$trap_dice-=5;}//连斗以后略容易踩陷阱
//	if($trap_dice < $trap_obbs){
//		$result = $db->query("SELECT * FROM {$tablepre}{$pls}mapitem WHERE itmk = 'TO'");
//		$trpnum = $db->num_rows($result);
//		if($trpnum){
//			$itemno = rand(0,$trpnum-1);
//			$db->data_seek($result,$itemno);
//			$mi=$db->fetch_array($result);
//			global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
//			$itm0=$mi['itm'];
//			$itmk0=$mi['itmk'];
//			$itme0=$mi['itme'];
//			$itms0=$mi['itms'];
//			$itmsk0=$mi['itmsk'];
//			$iid=$mi['iid'];
//			$db->query("DELETE FROM {$tablepre}{$pls}mapitem WHERE iid='$iid'");
//			if($itms0){
//				include_once GAME_ROOT.'./include/game/itemmain.func.php';
//				itemfind();
//				return;
//			}
//		}
//	}
	include_once GAME_ROOT.'./include/game/attr.func.php';

	$mode_dice = rand(0,99);
	if($mode_dice < $schmode) {
		global $pid,$corpse_obbs,$teamID,$fog,$bid,$gamestate;
//		if($gamestate < 40) {
//			$result = $db->query("SELECT * FROM {$tablepre}players WHERE pls='$pls' AND pid!='$pid' AND pid!='$bid'");
//		} else {
//			$result = $db->query("SELECT * FROM {$tablepre}players WHERE pls='$pls' AND pid!='$pid'");
//		}
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE pls='$pls' AND pid!='$pid'");
		if(!$db->num_rows($result)){
			$log .= '<span class="yellow">周围一个人都没有。</span><br>';
			if(CURSCRIPT == 'botservice') echo "noenemy=1\n";
			$mode = 'command';
			return;
		}

		$enemynum = $db->num_rows($result);
		$enemyarray = range(0, $enemynum - 1);
		shuffle($enemyarray);
		$find_r = get_find_r($weather,$pls,$pose,$tactic,$club,$inf,$lvl);
		$find_obbs = $enemy_obbs + $find_r;
		
		foreach($enemyarray as $enum){
			$db->data_seek($result, $enum);
			$edata = $db->fetch_array($result);
			$ishasi=false;
			if (($edata['type'])&&(strpos($edata['art'],'语录')!==false)) {$ishasi=true;}
			if(!$edata['type'] || $gamestate < 50 || $edata['type']==25 || $ishasi){
				if($edata['hp'] > 0) {
					global $art,$artk,$name; 
					global $gemname,$gemstate,$gempower,$gemlvl;
					if ((!$edata['type'])&&($artk=='XX')&&(($edata['artk']!='XX')||($edata['art']!=$name))&&($gamestate<50)){
						continue;
					}
					if (($artk!='XX')&&($edata['artk']=='XX')&&($gamestate<50)){
						continue;
					}
					if (($edata['art']==$name.'的契约书')&&($edata['type']==25)){
						continue;
					}
					if (($gemname=='碧榴石〖Alexander〗')&&($gemstate==2)&&($edata['type']==21)){
						include_once GAME_ROOT.'./include/game/gem.func.php';
						magic_gem('碧榴石〖Alexander〗');
						continue;
					}
					if (($gemname=='淡蓝宝石〖Eltoner〗')&&($gemstate==2)&&($edata['type']==20)){
						include_once GAME_ROOT.'./include/game/gem.func.php';
						magic_gem('淡蓝宝石〖Eltoner〗');
						continue;
					}
					if ($edata['club']==21){
						$ename=$edata['name'].'的人质证明';
						$result = $db->query("SELECT * FROM {$tablepre}players WHERE art='$ename'");
						if($db->num_rows($result)){
							$log.="<span class='yellow'>由于{$edata['name']}绑架了人质，你无法接近对方！</span><br>";
							continue;
						}
					}
					
					$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$name'");
					$sktime = $db->result($result, 0);
					if (!$sktime) $sktime=0;

					$hide_r = get_hide_r($weather,$pls,$edata['pose'],$edata['tactic'],$edata['club'],$edata['inf']);
					include_once GAME_ROOT.'./include/game/clubskills.func.php';
					$hide_r *= get_clubskill_bonus_hide($edata['club'],$edata['skills']);
					$enemy_dice = rand(0,99);
					if($enemy_dice < ($find_obbs - $hide_r) && ($now>$edata['dcloak'])) {	//成功发现且对方没有隐身
						if($teamID&&(!$fog)&&($gametype==2 || $gamestate<40)&&($teamID == $edata['teamID'])){
							if ($gametype==2) 
							{
								//$hideflag = true;
								continue;	//团战模式不会在探索中发现队友，因为送东西可以直接送
							}
							$bid = $edata['pid'];
							$action = 'team'.$edata['pid'];
							include_once GAME_ROOT.'./include/game/battle.func.php';
							findteam($edata);
							return;
						}elseif(($edata['art']==$name.'的人质证明')&&($edata['arts']==1)){
							$bid = $edata['pid'];
							$action = 'hijack'.$edata['pid'];
							include_once GAME_ROOT.'./include/game/battle.func.php';
							findhostage($edata);
							return;
						}elseif(($edata['art']==$name.'语录')&&($edata['type'])){
							if ($sktime==1){
								$bid = $edata['pid'];
								$action = 'hasi'.$edata['pid'];
								include_once GAME_ROOT.'./include/game/battle.func.php';
								findhasi($edata);
								return;
							}else{continue;}
						} else {
							$active_r = get_active_r($weather,$pls,$pose,$tactic,$club,$inf,$edata['pose'],$lvl);
							include_once GAME_ROOT.'./include/game/clubskills.func.php';
							$active_r *= get_clubskill_bonus_active($club,$skills,$edata['club'],$edata['skills']);
							if ($active_r>96) $active_r=96;
							$bid = $edata['pid'];
							$active_dice = rand(0,99);
							if($active_dice <  $active_r || $now<=$dcloak || (($gemname=='白欧泊石')&&($gemstate==2))) {	//如果自身处于隐身状态必定可以主动选择战斗
								if(($gemname=='白欧泊石')&&($gemstate==2)){
								$log.='<span class="gem">白欧泊石的折射魔法使你顺利的偷袭了敌人！</span><br><span class="red">白欧泊石已经进入冷却状态！</span><br>';
								$gemstate=4;
								}
								$action = 'enemy'.$edata['pid'];
								include_once GAME_ROOT.'./include/game/battle.func.php';
								findenemy($edata);
								return;
							} else {
								if (CURSCRIPT == 'botservice') 
								{
									echo "passive_battle=1\n";
									echo "passive_w_name={$edata['name']}\n";
									echo "passive_w_type={$edata['type']}\n";
									echo "passive_w_sNo={$edata['sNo']}\n";
								}
								include_once GAME_ROOT.'./include/game/combat.func.php';
								combat(0);
								return;
							}
						}
					}else{
						$hideflag = true;
					}
				} else {
					$corpse_dice = rand(0,99);
					if($corpse_dice < $corpse_obbs) {
						
						if($gamestate <40 && $edata['endtime'] < $now - $corpseprotect && (($edata['weps'] && $edata['wepe'])||($edata['arbs'] && $edata['arbe'])||$edata['arhs']||$edata['aras']||$edata['arfs']||$edata['arts']||$edata['itms0']||$edata['itms1']||$edata['itms2']||$edata['itms3']||$edata['itms4']||$edata['itms5']||$edata['money'])){
							
							$bid = $edata['pid'];
							$action = 'corpse'.$edata['pid'];
							include_once GAME_ROOT.'./include/game/battle.func.php';
							findcorpse($edata);
							return;
						} else {
							//这看上去是个bug…… 会导致地图上最后一个兵很难摸到…… 
							//改成discover(100)应该就能解决问题…… 但修复了可能导致平衡性问题…… 所以暂时留在这……
							discover(50);	
							return;
						}
					}
				}
			}
		}
		if($hideflag == true){
			$log .= '似乎有人隐藏着……<br>';
		}else{
			if ($gametype!=2)
				$log .= '<span class="yellow">周围可能没有敌人？</span><br>';
			else  $log .= '<span class="yellow">周围似乎没有敌人的样子。</span><br>';
		}
		$mode = 'command';
		return;
	} else {
		$find_r = get_find_r($weather,$pls,$pose,$tactic,$club,$inf,$lvl);
		$find_obbs = $item_obbs + $find_r;
		$item_dice = rand(0,99);
		if($item_dice < $find_obbs) {
			//$mapfile = GAME_ROOT."./gamedata/mapitem/{$pls}mapitem.php";
			//$mapitem = openfile($mapfile);
			//$itemnum = sizeof($mapitem) - 1;
//			$result = $db->query("SELECT * FROM {$tablepre}mapitem WHERE map='$pls'");
//			$itemnum = $db->num_rows($result);
			$result = $db->query("SELECT * FROM {$tablepre}mapitem WHERE pls = '$pls'");
			$itemnum = $db->num_rows($result);
			if($itemnum <= 0){
				$log .= '<span class="yellow">周围找不到任何物品。</span><br>';
				$mode = 'command';
				return;
			}
			$itemno = rand(0,$itemnum-1);
			$db->data_seek($result,$itemno);
			$mi=$db->fetch_array($result);
			global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
			$itm0=$mi['itm'];
			$itmk0=$mi['itmk'];
			$itme0=$mi['itme'];
			$itms0=$mi['itms'];
			$itmsk0=$mi['itmsk'];
			$iid=$mi['iid'];
			$db->query("DELETE FROM {$tablepre}mapitem WHERE iid='$iid'");
			//list($itm0,$itmk0,$itme0,$itms0,$itmsk0) = explode(',', $mapitem[$itemno]);
			//array_splice($mapitem,$itemno,1);
			//writeover($mapfile,implode('', $mapitem),'wb');
			//unset($mapitem);

			if($itms0){
				include_once GAME_ROOT.'./include/game/itemmain.func.php';
				itemfind();
				return;
			} else {
				$log .= "但是什么都没有发现。可能是因为道具有天然呆属性。<br>";
			}
		} else {
			$log .= "但是什么都没有发现。<br>";
		}
	}
	$mode = 'command';
	return;

}



?>
