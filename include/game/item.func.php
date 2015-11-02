<?php

if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function calc_skillbook_value($x, $v)
{
	$k=pow($v,0.5)/25+1.05;
	return pow($x/($k-1)/$v+1,1-$k)*$v;
}

function itemuse($itmn) {
	global $mode, $log, $nosta, $pid, $name, $state, $now,$nick,$achievement,$wepexp,$club;
	if ($itmn < 1 || $itmn > 6) {
		$log .= '此道具不存在，请重新选择。';
		$mode = 'command';
		return;
	}
	
	global ${'itm' . $itmn}, ${'itmk' . $itmn}, ${'itme' . $itmn}, ${'itms' . $itmn}, ${'itmsk' . $itmn};
	$itm = & ${'itm' . $itmn};
	$itmk = & ${'itmk' . $itmn};
	$itme = & ${'itme' . $itmn};
	$itms = & ${'itms' . $itmn};
	$itmsk = & ${'itmsk' . $itmn};
	$i=$itm;$ik=$itmk;$ie=$itme;$is=$itms;$isk=$itmsk;
	
	if (($itms <= 0) && ($itms != $nosta)) {
		$itm = $itmk = $itmsk = '';
		$itme = $itms = 0;
		$log .= '此道具不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	if(strpos ( $itmk, 'W' ) === 0 || strpos ( $itmk, 'D' ) === 0 || strpos ( $itmk, 'A' ) === 0 || strpos ( $itmk, 'ss' ) === 0){
		
		if(strpos ( $itmk, 'W' ) === 0) {
			global $club;
			if ($club==23){
				$log .= "<span class=\"yellow\">拳法家不需要无用的武器。</span><br>";
				$mode = 'command';
				return;
			}
			$eqp = 'wep';
			global $wepk,$wepsk;
			if (strpos($wepsk,'O')!==false)
			{
				$wepk.='-'.(string)($wepexp);
			}
			if (strpos($itmsk,'O')!==false)
			{
				if ($itmk[2]!=='-')
					$wepexp=0;
				else  $wepexp=(int)substr($itmk,3);
				$itmk=substr($itmk,0,2);
			}
			$noeqp = 'WN';
		}elseif(strpos ( $itmk, 'DB' ) === 0) {
			$eqp = 'arb';
			$noeqp = 'DN';
		}elseif(strpos ( $itmk, 'DH' ) === 0) {
			$eqp = 'arh';
			$noeqp = '';
		}elseif(strpos ( $itmk, 'DA' ) === 0) {
			$eqp = 'ara';
			$noeqp = '';
		}elseif(strpos ( $itmk, 'DF' ) === 0) {
			$eqp = 'arf';
			$noeqp = '';
		}elseif (strpos ( $itmk, 'A' ) === 0) {
			$eqp = 'art';
			$noeqp = '';
		}elseif (strpos ( $itmk, 'ss' ) === 0) {
			$eqp = 'art';
			$noeqp = '';
		}elseif (strpos ( $itmk, 'XX' ) === 0) {
			$eqp = 'art';
			$noeqp = '';
		}elseif (strpos ( $itmk, 'XY' ) === 0) {
			$eqp = 'art';
			$noeqp = '';
		}
		global ${$eqp}, ${$eqp.'k'}, ${$eqp.'e'}, ${$eqp.'s'}, ${$eqp.'sk'};
		if((($artk=='XX')||($artk=='XY'))&&($eqp == 'art')){
			$log .= '你的饰品不能替换！<br>';
			$mode = 'command';
			return;
		}
		if(($club==21)&&(strpos($itmk,'WD')===false)){
			$log .= '恐怖分子的尊严不允许你使用别的武器！<br>';
			$mode = 'command';
			return;
		}
		if(strpos($itmsk,'^')!==false){
			global $itmnumlimit;
			$itmnumlimit = $itme>=$itms ? $itms : $itme;
		}
		if (($noeqp && strpos ( ${$eqp.'k'}, $noeqp ) === 0) || ! ${$eqp.'s'}) {
			${$eqp} = $itm;
			${$eqp.'k'} = $itmk;
			${$eqp.'e'} = $itme;
			${$eqp.'s'} = $itms;
			${$eqp.'sk'} = $itmsk;
			$log .= "装备了<span class=\"yellow\">$itm</span>。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		} else {
			global $wepsk,$arbsk,$arask,$arhsk,$arfsk,$artsk;
			if(strpos(${$eqp.'sk'},'V')!==false){
				$log .= '被诅咒的装备无法替换！<br>';
				$mode = 'command';
				return;
			}
			$itmt = ${$eqp};
			$itmkt = ${$eqp.'k'};
			$itmet = ${$eqp.'e'};
			$itmst = ${$eqp.'s'};
			$itmskt = ${$eqp.'sk'};
			${$eqp} = $itm;
			${$eqp.'k'} = $itmk;
			${$eqp.'e'} = $itme;
			${$eqp.'s'} = $itms;
			${$eqp.'sk'} = $itmsk;
			$itm = $itmt;
			$itmk = $itmkt;
			$itme = $itmet;
			$itms = $itmst;
			$itmsk = $itmskt;
			$log .= "卸下了<span class=\"red\">$itm</span>，装备了<span class=\"yellow\">${$eqp}</span>。<br>";
		}
	} elseif (strpos ( $itmk, 'HS' ) === 0) {
		global $sp, $msp,$club;
		if ($sp < $msp) {
			$oldsp = $sp;
			if($club == 16){
				$spup = round($itme*2.5);
			}else{
				$spup = $itme;
			}
			$sp += $spup;
			$sp = $sp > $msp ? $msp : $sp;
			$oldsp = $sp - $oldsp;
			$log .= "你使用了<span class=\"red\">$itm</span>，恢复了<span class=\"yellow\">$oldsp</span>点体力。<br>";
			if ($itms != $nosta) {
				$itms --;
				if ($itms <= 0) {
					$log .= "<span class=\"red\">$itm</span>用光了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			}
		} else {
			$log .= '你的体力不需要恢复。<br>';
		}
	} elseif (strpos ( $itmk, 'HH' ) === 0) {
		global $hp, $mhp,$club;
		if ($hp < $mhp) {
			$oldhp = $hp;
			if($club == 16){
				$hpup = round($itme*2.5);
			}else{
				$hpup = $itme;
			}
			$hp += $hpup;
			$hp = $hp > $mhp ? $mhp : $hp;
			$oldhp = $hp - $oldhp;
			$log .= "你使用了<span class=\"red\">$itm</span>，恢复了<span class=\"yellow\">$oldhp</span>点生命。<br>";
			if ($itms != $nosta) {
				$itms --;
				if ($itms <= 0) {
					$log .= "<span class=\"red\">$itm</span>用光了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			
			}
		} else {
			$log .= '你的生命不需要恢复。<br>';
		}
	}elseif (strpos ( $itmk, 'HM' ) === 0) {
		global $mss,$ss;
		$mss+=$itme;
		$ss+=$itme;
		$log .= "你使用了<span class=\"red\">$itm</span>，增加了<span class=\"yellow\">$itme</span>点歌魂。<br>";
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	}elseif (strpos ( $itmk, 'HT' ) === 0) {
		global $ss, $mss;
		$ssup=$itme;
		if ($ss < $mss) {
			$oldss = $ss;
			$ss += $ssup;
			$ss = $ss > $mss ? $mss : $ss;
			$oldss = $ss - $oldss;
			$log .= "你使用了<span class=\"red\">$itm</span>，恢复了<span class=\"yellow\">$oldss</span>点歌魂。<br>";
			if ($itms != $nosta) {
				$itms --;
				if ($itms <= 0) {
					$log .= "<span class=\"red\">$itm</span>用光了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			
			}
		} else {
			$log .= '你的歌魂不需要恢复。<br>';
		}
	} elseif (strpos ( $itmk, 'HB' ) === 0) {
		global $hp, $mhp, $sp, $msp,$club;
		if (($hp < $mhp) || ($sp < $msp)) {
			if($club == 16){
				$bpup = round($itme*2.5);
			}else{
				$bpup = $itme;
			}
			$oldsp = $sp;
			$sp += $bpup;
			$sp = $sp > $msp ? $msp : $sp;
			$oldsp = $sp - $oldsp;
			$oldhp = $hp;
			if ($hp<$mhp){
				$hp += $bpup;
				$hp = $hp > $mhp ? $mhp : $hp;
			}
			$oldhp = $hp - $oldhp;
			$log .= "你使用了<span class=\"red\">$itm</span>，恢复了<span class=\"yellow\">$oldhp</span>点生命和<span class=\"yellow\">$oldsp</span>点体力。<br>";
			if ($itms != $nosta) {
				$itms --;
				if ($itms <= 0) {
					$log .= "<span class=\"red\">$itm</span>用光了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			}
		} else {
			$log .= '你的生命和体力都不需要恢复。<br>';
		}
	} elseif (strpos ( $itmk, 'P' ) === 0) {
		global $lvl, $db, $tablepre, $now, $hp, $inf, $bid;
		if (strpos ( $itmk, '2' ) === 2) {
			$damage = round ( $itme * 2 );
		} elseif (strpos ( $itmk, '1' ) === 2) {
			$damage = round ( $itme * 1.5 );
		} else {
			$damage = round ( $itme );
		}
		if (strpos ( $inf, 'p' ) === false) {
			$inf .= 'p';
		}
		$hp -= $damage;
		if ($itmsk && is_numeric($itmsk)) {
			$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$itmsk'" );
			$wdata = $db->fetch_array ( $result );
			$log .= "糟糕，<span class=\"yellow\">$itm</span>中被<span class=\"yellow\">{$wdata['name']}</span>掺入了毒药！你受到了<span class=\"dmg\">$damage</span>点伤害！<br>";
			addnews ( $now, 'poison', $nick.' '.$name, $wdata ['name'], $itm );
		} else {
			$log .= "糟糕，<span class=\"yellow\">$itm</span>有毒！你受到了<span class=\"dmg\">$damage</span>点伤害！<br>";
		}
		if ($hp <= 0) {
			if ($itmsk) {
				$bid = $itmsk;
				$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$itmsk'" );
				$wdata = $db->fetch_array ( $result );
				/*
				if($wdata['hp'] > 0){
					$expup = round(($wdata['lvl'] - $lvl)/3);
					$wdata['exp'] += $expup;
				}
				*/
				include_once GAME_ROOT . './include/state.func.php';
				$killmsg = death ( 'poison', $wdata ['name'], $wdata ['type'], $itm );
				$log .= "你被<span class=\"red\">" . $wdata ['name'] . "</span>毒死了！";
				if($killmsg){$log .= "<span class=\"yellow\">{$wdata['name']}对你说：“{$killmsg}”</span><br>";}
			} else {
				//$bid = 0;
				include_once GAME_ROOT . './include/state.func.php';
				death ( 'poison', '', 0, $itm );
				$log .= "你被毒死了！";
			}
		}
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	
	} elseif (strpos ( $itmk, 'T' ) === 0) {
		global $pls, $exp, $upexp, $wd, $club,$lvl,$db,$tablepre;
		if ($club==23){
			$log .= "<span class=\"yellow\">拳法家的尊严不允许你这么做。</span><br>";
			$mode = 'command';
			return;
		}
		$trapk = str_replace('TN','TO',$itmk);
		//$mapfile = GAME_ROOT . "./gamedata/mapitem/{$pls}mapitem.php";
		//$itemdata = "$itm,TO,$itme,1,$pid,\n";
		//writeover ( $mapfile, $itemdata, 'ab' );
		$db->query("INSERT INTO {$tablepre}maptrap (itm, itmk, itme, itms, itmsk, pls) VALUES ('$itm', '$trapk', '$itme', '1', '$pid', '$pls')");
		$log .= "设置了陷阱<span class=\"red\">$itm</span>。<br>小心，自己也很难发现。<br>";
		//echo $exp;
		if($club == 5){$exp += 2;$wd+=2;}
		else{$exp++;$wd++;}
		
		if ($exp >= $upexp) {
			include_once GAME_ROOT . './include/state.func.php';
			//lvlup ( $exp, $upexp );
			lvlup ($lvl, $exp, 1);
		}
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	} elseif (strpos ( $itmk, 'GB' ) === 0) {
		global $wep, $wepk, $weps, $wepsk;
		if ((strpos ( $wepk, 'WG' ) !== 0)&&(strpos ( $wepk, 'WJ' ) !== 0)) {
			$log .= "<span class=\"red\">你没有装备枪械，不能使用子弹。</span><br>";
			$mode = 'command';
			return;
		}
		
		if (strpos ( $wepsk, 'o' ) !== false) {
			$log .= "<span class=\"red\">{$wep}不能装填弹药。</span><br>";
			$mode = 'command';
			return;
		}elseif (strpos ($wepk,'WG')===false){
			if ($itmk=='GBh'){
			$bulletnum = 1;	
			}else{
			$log .= "<span class=\"red\">枪械类型和弹药类型不匹配。</span><br>";
			$mode = 'command';
			return;
			}
		}elseif (strpos ( $wepsk, 'e' ) !== false || strpos ( $wepsk, 'w' ) !== false) {
			if ($itmk == 'GBe') {
				$bulletnum = 10;
			} else {
				$log .= "<span class=\"red\">枪械类型和弹药类型不匹配。</span><br>";
				$mode = 'command';
				return;
			}
		} elseif (strpos ( $wepsk, 'i' ) !== false || strpos ( $wepsk, 'u' ) !== false) {
			if ($itmk == 'GBi') {
				$bulletnum = 10;
			} else {
				$log .= "<span class=\"red\">枪械类型和弹药类型不匹配。</span><br>";
				$mode = 'command';
				return;
			}
		} else {
			if (strpos ( $wepsk, 'r' ) !== false) {
				if ($itmk == 'GBr') {
					$bulletnum = 20;
				} else {
					$log .= "<span class=\"red\">枪械类型和弹药类型不匹配。</span><br>";
					$mode = 'command';
					return;
				}
			} else {
				if ($itmk == 'GB') {
					$bulletnum = 6;
				} else {
					$log .= "<span class=\"red\">枪械类型和弹药类型不匹配。</span><br>";
					$mode = 'command';
					return;
				}
			}
		}
		if ($weps == $nosta) {
			$weps = 0;
		}
		global $club,$wepe;
		if ($club==97){
			if ($bulletnum<($wepe))$bulletnum=$wepe;
		}
		$bullet = $bulletnum - $weps;
		if ($bullet <= 0) {
			$log .= "<span class=\"red\">{$wep}的弹匣是满的，不能装弹。</span>";
			return;
		} elseif ($bullet >= $itms) {
			$bullet = $itms;
		}
		$itms -= $bullet;
		$weps += $bullet;
		$log .= "为<span class=\"red\">$wep</span>装填了<span class=\"red\">$itm</span>，<span class=\"red\">$wep</span>残弹数增加<span class=\"yellow\">$bullet</span>。<br>";
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	} elseif (strpos ( $itmk, 'R' ) === 0) {
		//$log.= $itm .'已经废弃，请联系管理员。';
		if ($itme > 0) {
			$log .= "使用了<span class=\"red\">$itm</span>。<br>";
			include_once GAME_ROOT . './include/game/item2.func.php';
			newradar ( $itmsk );
			$itme --;
			if ($itme <= 0) {
				$log .= $itm . '的电力用光了，请使用电池充电。<br>';
			}
		} else {
			$itme = 0;
			$log .= $itm . '没有电了，请先充电。<br>';
		}
	} elseif (strpos ( $itmk, 'C' ) === 0) {
		global $inf, $exdmginf,$ex_inf;
		$ck=substr($itmk,1,1);
		if($ck == 'a'){
			$flag=false;
			$log .= "服用了<span class=\"red\">$itm</span>。<br>";
			foreach ($ex_inf as $value) {
				if(strpos ( $inf, $value ) !== false){
					$inf = str_replace ( $value, '', $inf );
					$log .= "{$exdmginf[$value]}状态解除了。<br>";
					$flag=true;
				}
			}
			if(!$flag){
				$log .= '但是什么也没发生。<br>';
			}
		}elseif(in_array($ck,$ex_inf)){
			if(strpos ( $inf, $ck ) !== false){
				$inf = str_replace ( $ck, '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf[$ck]}状态解除了。<br>";
			}else{
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
		}elseif ($ck == 'x'){
			$inf = "puiewhbaf";
			$log .= "服用了<span class=\"red\">$itm</span>，<br>";
			$log .= "但是，假冒伪劣的<span class=\"red\">$itm</span>导致你{$exdmginf['p']}了！<br>";
			$log .= "假冒伪劣的<span class=\"red\">$itm</span>导致你{$exdmginf['u']}了！<br>";
			$log .= "假冒伪劣的<span class=\"red\">$itm</span>导致你{$exdmginf['i']}了！<br>";
			$log .= "假冒伪劣的<span class=\"red\">$itm</span>导致你{$exdmginf['e']}了！<br>";
			$log .= "而且，假冒伪劣的<span class=\"red\">$itm</span>还导致你{$exdmginf['w']}了！<br>";
			$log .= "你遍体鳞伤地站了起来。<br>";
			$log .= "真是大快人心啊！<br>";
		}else{
			$log .= "服用了<span class=\"red\">$itm</span>……发生了什么？<br>";
		}
		
		$itms --;
		/*if (strpos ( $itm, '烧伤药剂' ) === 0) {
			if (strpos ( $inf, 'u' ) !== false) {
				$inf = str_replace ( 'u', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['u']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		} elseif (strpos ( $itm, '麻痹药剂' ) === 0) {
			if (strpos ( $inf, 'e' ) !== false) {
				$inf = str_replace ( 'e', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['e']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		
		} elseif (strpos ( $itm, '解冻药水' ) === 0) {
			if (strpos ( $inf, 'i' ) !== false) {
				$inf = str_replace ( 'i', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['i']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		
		} elseif (strpos ( $itm, '解毒剂' ) === 0) {
			if (strpos ( $inf, 'p' ) !== false) {
				$inf = str_replace ( 'p', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['p']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		
		} elseif (strpos ( $itm, '清醒药剂' ) === 0) {
			if (strpos ( $inf, 'w' ) !== false) {
				$inf = str_replace ( 'w', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['w']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		
		} elseif (strpos ( $itm, '全恢复药剂' ) === 0) {
			if (strpos ( $inf, 'w' ) !== false) {
				$inf = str_replace ( 'w', '', $inf );
				$log .= "服用了<span class=\"red\">$itm</span>，{$exdmginf['w']}状态解除了。<br>";
			} else {
				$log .= "服用了<span class=\"red\">$itm</span>，但是什么效果也没有。<br>";
			}
			$itms --;
		
		} else {
			$log .= "服用了<span class=\"red\">$itm</span>……发生了什么？<br>";
			$itms --;
		}*/
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	
	} elseif (strpos ( $itmk, 'V' ) === 0) {
		$skill_minimum = 60;
		$skill_limit = 600;
		$wlim=385+rand(-25,25);
		$log .= "你阅读了<span class=\"red\">$itm</span>。<br>";
		$dice = rand ( - 10, 10 );
		if (strpos ( $itmk, 'VV' ) === 0) {
			global $wp, $wk, $wg, $wc, $wd, $wf;
			$ws_sum = $wp + $wk + $wg + $wc + $wd + $wf;
			if ($ws_sum < $skill_minimum * 6) {
				$vefct = $itme;
			} elseif ($ws_sum < $skill_limit * 6) {
				$vefct = round(calc_skillbook_value($ws_sum-$skill_minimum*6,$itme));
			} else {
				$vefct = 0;
			}
			$wp += $vefct; //$itme;
			$wk += $vefct; //$itme;
			$wg += $vefct; //$itme;
			$wc += $vefct; //$itme;
			$wd += $vefct; //$itme; 
			$wf += $vefct; //$itme;
			$wsname = "全系熟练度";
		} elseif (strpos ( $itmk, 'VP' ) === 0) {
			global $wp;
			if ($wp < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wp < $skill_limit) {
				$vefct = round(calc_skillbook_value($wp-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wp,rand(-10,10)));
			$wp += $vefct; //$itme;
			$wsname = "斗殴熟练度";
		} elseif (strpos ( $itmk, 'VK' ) === 0) {
			global $wk;
			if ($wk < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wk < $skill_limit) {
				$vefct = round(calc_skillbook_value($wk-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wk,rand(-10,10)));
			$wk += $vefct; //$itme;
			$wsname = "斩刺熟练度";
		} elseif (strpos ( $itmk, 'VG' ) === 0) {
			global $wg;
			if ($wg < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wg < $skill_limit) {
				$vefct = round(calc_skillbook_value($wg-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wg,rand(-10,10)));
			$wg += $vefct; //$itme; 
			$wsname = "射击熟练度";
		} elseif (strpos ( $itmk, 'VC' ) === 0) {
			global $wc;
			if ($wc < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wc < $skill_limit) {
				$vefct = round(calc_skillbook_value($wc-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wc,rand(-10,10)));
			$wc += $vefct; //$itme; 
			$wsname = "投掷熟练度";
		} elseif (strpos ( $itmk, 'VD' ) === 0) {
			global $wd;
			if ($wd < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wd < $skill_limit) {
				$vefct = round(calc_skillbook_value($wd-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wd,rand(-10,10)));
			$wd += $vefct; //$itme; 
			$wsname = "引爆熟练度";
		} elseif (strpos ( $itmk, 'VF' ) === 0) {
			global $wf;
			if ($wf < $skill_minimum) {
				$vefct = $itme;
			} elseif ($wf < $skill_limit) {
				$vefct = round(calc_skillbook_value($wf-$skill_minimum,$itme));
			} else {
				$vefct = 0;
			}
			$vefct=min($vefct,max($wlim-$wf,rand(-10,10)));
			$wf += $vefct; //$itme; 
			$wsname = "灵击熟练度";
		}
		if ($vefct > 0) {
			$log .= "嗯，有所收获。<br>你的{$wsname}提高了<span class=\"yellow\">$vefct</span>点！<br>";
		} elseif ($vefct == 0) {
			$log .= "对你来说书里的内容过于简单了。<br>你的熟练度没有任何提升。<br>";
		} else {
			$vefct = - $vefct;
			$log .= "对你来说书里的内容过于简单了。<br>而且由于盲目相信书上的知识，你反而被编写者的纰漏所误导了！<br>你的{$wsname}下降了<span class=\"red\">$vefct</span>点！<br>";
		}
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	} elseif(strpos ( $itmk, 'm' ) === 0) {
		global $money,$rp;
		$log .= "你使用了<span class=\"yellow\">$itm</span>，钱忽然从你的钱包里溢出来！<br>";
		$money *= 1+$itme/100;$money = round($money);
		$rp += $money / 10;
		$log .= "你的金钱增加了<span class=\"yellow\">{$itme}%</span>，变成了<span class=\"yellow\">$money</span>。<br>然而你觉得肩膀好像变得沉重了很多。";
		addnews ($now , 'makemoney', $name, $itm);
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	} elseif (strpos ( $itmk, 'M' ) === 0) {
		$log .= "你服用了<span class=\"red\">$itm</span>。<br>";
		$wlim=385+rand(-25,25);
		if (strpos ( $itmk, 'MA' ) === 0) {
			global $att;
			$att_min = 200;
			$att_limit = 500;
			$dice = rand ( - 5, 5 );
			if ($att < $att_min) {
				$mefct = $itme;
			} elseif ($att < $att_limit) {
				$mefct = round ( $itme * (1 - ($att - $att_min) / ($att_limit - $att_min)) );
			} else {
				$mefct = 0;
			}
			if ($mefct < 5) {
				if ($mefct < $dice) {
					$mefct = - $dice;
				}
			}
			$att += $mefct;
			$mdname = "基础攻击力";
		} elseif (strpos ( $itmk, 'MD' ) === 0) {
			global $def;
			$def_min = 200;
			$def_limit = 500;
			$dice = rand ( - 5, 5 );
			if ($def < $def_min) {
				$mefct = $itme;
			} elseif ($def < $def_limit) {
				$mefct = round ( $itme * (1 - ($def - $def_min) / ($def_limit - $def_min)) );
			} else {
				$mefct = 0;
			}
			if ($mefct < 5) {
				if ($mefct < $dice) {
					$mefct = - $dice;
				}
			}
			$def += $mefct;
			$mdname = "基础防御力";
		} elseif (strpos ( $itmk, 'ME' ) === 0) {
			global $exp, $upexp, $baseexp;
			$lvlup_objective = $itme / 10;
			$mefct = round ( $baseexp * 2 * $lvlup_objective + rand ( 0, 5 ) );
			if(strpos($itm,'奇异咸食')!==false){
			$exp -= $mefct;
			}else{
			$exp += $mefct;
			}
			$mdname = "经验值";
		} elseif (strpos ( $itmk, 'MS' ) === 0) {
			global $sp, $msp, $club;
			$mefct = $itme;
			if ($club==21) $mefct = 0;
			$sp += $mefct;
			$msp += $mefct;
			$mdname = "体力上限";
		} elseif (strpos ( $itmk, 'MH' ) === 0) {
			global $hp, $mhp, $club;
			$mefct = $itme;
			if ($club==21) $mefct = 0;
			$hp += $mefct;
			$mhp += $mefct;
			$mdname = "生命上限";
		} elseif (strpos ( $itmk, 'MV' ) === 0) {
			global $wp, $wk, $wg, $wc, $wd, $wf;
			$skill_minimum = 60;
			$skill_limit = 600;
			$dice = rand ( - 10, 10 );
			$ws_sum = $wp + $wk + $wg + $wc + $wd + $wf;
			if ($ws_sum < $skill_minimum * 6) {
				$mefct = $itme;
			} elseif ($ws_sum < $skill_limit * 6) {
				$mefct = round(calc_skillbook_value($ws_sum-$skill_minimum*6,$itme));
			} else {
				$mefct = 0;
			}
			/*
			if ($mefct < 10) {
				if ($mefct < $dice) {
					$mefct = - $dice;
				}
			}
			*/
			$wp += $mefct;
			$wk += $mefct;
			$wg += $mefct;
			$wc += $mefct;
			$wd += $mefct;
			$wf += $mefct;
			$mdname = "全系熟练度";
		}
		if ($itm=='奇异咸食') {
			$log .= "这……这屎里……有……有毒……！<br>你的{$mdname}下降了<span class=\"red\">$mefct</span>点！<br>";
			if($exp<0){
			include_once GAME_ROOT . './include/state.func.php';
			$log .= "<span class=\"red\">你无法忍受这屎一样的味道，被活生生毒死了！</span><br>";
			death ( 'poison', '', 0, $itm );
			}
		}elseif ($mefct > 0) {
			$log .= "身体里有种力量涌出来！<br>你的{$mdname}提高了<span class=\"yellow\">$mefct</span>点！<br>";
		} elseif ($mefct == 0) {
			$log .= "已经很强了，却还想靠药物继续强化自己，是不是太贪心了？<br>你的能力没有任何提升。<br>";
		} else {
			$mefct = - $mefct;
			$log .= "已经很强了，却还想靠药物继续强化自己，是不是太贪心了？<br>你贪婪的行为引发了药物的副作用！<br>你的{$mdname}下降了<span class=\"red\">$mefct</span>点！<br>";
		}
		if (strpos ( $itmk, 'ME' ) === 0) {
			
			if ($exp >= $upexp) {
				global $lvl;
				include_once GAME_ROOT . './include/state.func.php';
				lvlup ( $lvl, $exp, 1 );
			}
		}
		if ($itms != $nosta) {
			$itms --;
			if ($itms <= 0) {
				$log .= "<span class=\"red\">$itm</span>用光了。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		}
	} elseif ( strpos( $itmk,'EW' ) ===0 )	{
		include_once GAME_ROOT . './include/game/item2.func.php';
		wthchange ( $itm,$itmsk);
		$itms--;
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	} elseif (strpos ( $itmk, 'EE' ) === 0 || $itm == '移动PC') {//移动PC
		include_once GAME_ROOT . './include/game/item2.func.php';
		hack ( $itmn );
	} elseif (strpos ( $itmk, 'ER' ) === 0) {//雷达
		if ($itme > 0) {
			$log .= "使用了<span class=\"red\">$itm</span>。<br>";
			include_once GAME_ROOT . './include/game/item2.func.php';
			global $club;
			if ($club==7 && $itmsk!=2) newradar(3); else newradar ( $itmsk );	//锡安成员称号永远可以探测全图
			if($club == 7){
				$e_dice = rand(0,1);
				if($e_dice == 1){
					$itme--;
					$log .= "消耗了<span class=\"yellow\">$itm</span>的电力。<br>";
				}else{
					$log .= "由于操作迅速，<span class=\"yellow\">$itm</span>的电力没有消耗。<br>";
				}
			}else{
				$itme--;
				$log .= "消耗了<span class=\"yellow\">$itm</span>的电力。<br>";
			}
			if ($itme <= 0) {
				$log .= $itm . '的电力用光了，请使用电池充电。<br>';
			}
		} else {
			$itme = 0;
			$log .= $itm . '没有电了，请先充电。<br>';
		}
	} elseif (strpos ( $itmk, 'B' ) === 0) {
		$flag = false;
		global $elec_cap;
		$bat_kind = substr($itmk,1,1);
		for($i = 1; $i <= 6; $i ++) {
			global ${'itm' . $i}, ${'itmk' . $i}, ${'itme' . $i}, ${'itms' . $i};
			if (${'itmk' . $i} == 'E'.$bat_kind && ${'itms' . $i}) {
				if(${'itme' . $i} >= $elec_cap){
					$log .= "包裹{$i}里的<span class=\"yellow\">${'itm'.$i}</span>已经充满电了。<br>";
				}else{
					${'itme' . $i} += $itme;
					if(${'itme' . $i} > $elec_cap){${'itme' . $i} = $elec_cap;}
					$itms --;
					$flag = true;
					$log .= "为包裹{$i}里的<span class=\"yellow\">${'itm'.$i}</span>充了电。";
					break;
				}				
			}
		}
		if (! $flag) {
			$log .= '你没有需要充电的物品。<br>';
		}
		if ($itms <= 0 && $itm) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';



			$itme = $itms = 0;
		}		
	} elseif(strpos ( $itmk, 'p' ) === 0){
		global $hp;
		$oitm = $itm;
		$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
		if(strpos( $itmk, 'ps' ) === 0){//银色盒子
			global $gamecfg;
			include_once config('randomitem',$gamecfg);
			$dice = rand(1,100);
			if($dice <= 75){//一般物品
				$itemflag = $itmlow;
			}elseif($dice <= 95){//中级道具
				$itemflag = $itmmedium;
			}elseif($dice <= 97){//神装
				$itemflag = $itmhigh;
			}elseif($dice <= 99){//礼品盒和游戏王
				$file = config('present',$gamecfg);
				$plist = openfile($file);
				$file2 = config('box',$gamecfg);
				$plist2 = openfile($file2);
				$plist = array_merge($plist,$plist2);
				$rand = rand(0,count($plist)-1);
				list($in,$ik,$ie,$is,$isk) = explode(',',$plist[$rand]);
				$itmflag = false;
			}else{//三抽
				$itemflag = $antimeta;
			}
			if($itemflag){
				$itemflag = explode("\r\n",$itemflag);
				$rand = rand(0,count($itemflag)-1);
				list($in,$ik,$ie,$is,$isk) = explode(',',$itemflag[$rand]);
			}
		}else{
			$gg_dice=rand(1,100); 
			if($gg_dice>=95){
				$log.="<span class=\"red\">但是里面突然窜出来两条鱼将你撞翻在地，然后快速的游走了！</span><br>";
				$hp=50;
				include_once GAME_ROOT . './include/system.func.php';
				addnews ($now , 'eyu', $nick.' '.$name);
				addnpc ( 24, 0,1);
				addnpc ( 24, 1,1);
			}else{
				$file = config('present',$gamecfg);
				$plist = openfile($file);
				$rand = rand(0,count($plist)-1);
				list($in,$ik,$ie,$is,$isk) = explode(',',$plist[$rand]);
			}			
		}
		
		$itms--;
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}	
		if(isset($in)){
			global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$mode;
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$nick.' '.$name,$oitm,$in);
			include_once GAME_ROOT.'./include/game/itemmain.func.php';
			itemget();
		}
	} elseif(strpos ( $itmk, 'ygo' ) === 0){
		$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
		$file1 = config('box',$gamecfg);
		$plist1 = openfile($file1);
		$rand1 = rand(0,count($plist1)-1);
		list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$mode;
		$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
		addnews($now,'present',$nick.' '.$name,$itm,$in);
		$itms1--;
		if ($itms1 <= 0 && $itms != '∞') {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
	} elseif(strpos ( $itmk, 'jew' ) === 0){
		$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
		$file = config('jewbox',$gamecfg);
		$plist = openfile($file);
		$rand = rand(0,count($plist)-1);
		list($in,$ik,$ie,$is,$isk) = explode(',',$plist[$rand]);
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$mode;
		$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
		addnews($now,'present',$nick.' '.$name,$itm,$in);
		$itms--;
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}	
	} elseif(strpos ( $itmk, 'gbox' ) === 0){
		$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
		$file = config('gembox',$gamecfg);
		$plist = openfile($file);
		$rand = rand(0,count($plist)-1);
		list($in,$ik,$ie,$is,$isk) = explode(',',$plist[$rand]);
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$mode;
		$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
		addnews($now,'present',$nick.' '.$name,$itm,$in);
		$itms--;
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}	
	} elseif(strpos ( $itmk, 'fy' ) === 0){
	global $hp;
		$gg_dice=rand(1,100);
		if($gg_dice>=95){
		$log.="<span class=\"red\">但是里面突然窜出来两条鱼将你撞翻在地，然后快速的游走了！</span><br>";
		$hp=50;
		include_once GAME_ROOT . './include/system.func.php';
		addnews ($now , 'eyu', $nick.' '.$name);
			addnpc ( 24, 0,1);
		addnpc ( 24, 1,1);
		$itms1--;
		}else{
		$file1 = config('fy',$gamecfg);
		$plist1 = openfile($file1);
		$rand1 = rand(0,count($plist1)-1);
		list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$mode;
		$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
		addnews($now,'present',$nick.' '.$name,$itm,$in);
		$itms1--;
		include_once GAME_ROOT.'./include/game/itemmain.func.php';
		itemget();	
		}	
		
		if ($itms1 <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	}elseif($itmk=='GEM'){
		global $gemstate,$gemname,$gempower,$gemexp,$gemlvl;
		global $club;
			$gempower=1000;$gemlvl=0;$gemexp=0;$gemname=$itm;$gemstate=1;
			if(($club==49)||($club==53)){$gempower=3000;}
			if(($gemname=='碧榴石〖Alexander〗')||($gemname=='淡蓝宝石〖Eltoner〗')){$gemlvl=3;}
			if(($gemname=='翠榴石')||($gemname=='月长石')){$gemstate=5;}
			if($gemname=='橡树之心'){$log .= "宝石中蕴藏的自然气息与你同化了！<br><span class=\"deeppink\">你成为了一名森林之拳！</span><br>";$club=43;}
			if(($club==20)&&(rand(1,100)<=10)&&($itm!=='橡树之心')){$log .= "你对于宝石敏锐的直觉使你领悟了宝石中蕴藏的奥秘！<br><span class=\"deeppink\">你成为了一名奥秘学者！</span><br>";$club=49;}
		$log .= "你将<span class=\"yellow\">$itm</span>镶嵌在了指骨上。现在你可以使用宝石魔法了！<br>";
		addnews($now,'get_gem_magic',$name,$gemname);
		$itm=$itmk=$itmsk='';$itme=$itms=0;
	}elseif ($itmk=='U') {
		global $db, $tablepre,$pls;
		$trapresult = $db->query("SELECT * FROM {$tablepre}maptrap WHERE pls = '$pls' AND itme>='$itme'");
		$trpnum = $db->num_rows($trapresult);
		$itms--;
		if ($trpnum>0){
			$itemno = rand(0,$trpnum-1);
			$db->data_seek($trapresult,$itemno);
			$mi=$db->fetch_array($trapresult);
			$deld = $mi['itm'];
			$delp = $mi['tid'];
			$db->query("DELETE FROM {$tablepre}maptrap WHERE tid='$delp'");
			$log.="远方传来一阵爆炸声，伟大的<span class=\"yellow\">{$itm}</span>用生命和鲜血扫除了<span class=\"yellow\">{$deld}</span>。<br><span class=\"red\">实在是大快人心啊！</span><br>";
		}else{
			$log.="你使用了<span class=\"yellow\">{$itm}</span>，但是没有发现陷阱。<br>";
		}
	}elseif ($itmk == 'GP'){
		global $wep,$wepe,$wepk,$weps,$wepsk,$wg;
		if($wepk !== 'WG' && $wepk !== 'WJ' && $wepk !== 'WGK' && $wepk !== 'WDG'){
			$log .= '<span class="red">你没有装备枪械,无法使用枪械挂件！</span><br />';
			return;
		}
		if(strpos($wepsk,'T')===false){
			$log .= '<span class="red">该武器的兼容性过差,无法进行改装！</span><br />';
			return;
		}
		$a = str_split($wepsk);
		$acount = $a[0] == '' ? 0 : count($a);
		$b = str_split($itmsk);
		$bcount = $b[0] == '' ? 0 : count($b);
		$x = array_diff($b,$a);
		$xcount = $x[0] == '' ? 0 : count($x);
		if($xcount + $acount > 25){
			$log .= '<span class="red">{$wep}属性数目达到上限，无法改造！</span><br />';
			return;
		}
		$gbd=round($wg/4);$gsd=rand(0,100);
		$log .= "你开始动手改装<span class =\"yellow\" >{$wep}</span>。<br>";
		if($gbd>=$gsd){
			$log .= "“呼……成了！”<br>你心满意足的擦了下汗水。<br>";
			if($bcount > 0 && $xcount > 0){
				global $itemspkinfo;
				$log .= "“经过你的改装，{$wep}增加了";
				foreach($x as $value){
					$log .= "<span class=\"yellow\">$itemspkinfo[$value]</span>";
					$wepsk .= $value;
				}
				$log .= '属性！<br />';
			}
			if($itme > 0){
					$flag = true;
					global $wepe;
					$log .= "添加此附件使<span class =\"yellow\" >$wep</span>增加了<span class =\"red\" >$itme</span>点基础效果！<br />";
					$wepe += $itme;
			}
		}else{
			$log .= "当你兴致勃勃的开始改装时，才发现自己对枪械结构一窍不通！<br><span class='red'>对于枪械的改造失败了！</span><br>";
			if($gsd-$gbd>50){
				$log .= "<span class='red'>而且你粗暴的行为使枪械受到了一定程度的破坏！</span><br>";
				$wepe-=$itme;
				if($wepe<=0){
					$log .= "<span class='yellow'>{$wep}</span>彻底损坏了！<br>";
					$wep='拳头';$wepk='WN';$wepe=0;$weps='∞';$wepsk='';
				}
			}
		}
		$itms--;
		if ($itms <= 0) {
			$log .= "<span class=\"red\">$itm</span>用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	}elseif ($itmk == 'GT'){
	global $wep,$wepe,$wepk,$weps,$wepsk,$wg;
	global $gun_other,$gun_body,$gun_barrel,$gun_aiming,$gun_trigger,$gun_ammo,$gun_k;
		//拆枪
		if($itm=='枪械拆卸工具'){
			if($wepk !== 'WG' && $wepk !== 'WJ' && $wepk !== 'WGK' && $wepk !== 'WDG'){
				$log .= '<span class="red">你没有装备枪械,无法对其进行拆解！</span><br />';
				return;
			}
			if((strpos($wep,'-改')!==false)||(strpos($wepsk,'V')!==false)){
				$log .= '<span class="red">该武器结构不稳定，无法进行拆解！</span><br />';
				return;
			}
			$unfull=false;
			if(strpos($wepsk,'o')!==false){
				$unfull=true;
			}
			$log .= "你小心翼翼的将枪械拆解成基本零件。<br>";
			//获得枪械附件
			$eflag=false;
			$partsk=str_split($wepsk);
			$sknum=sizeof($partsk);
			if($sknum<5){
				$partsk=array_pad($partsk,5,'');
			}
			$gp_sk=$partsk[array_rand($partsk)];
			$g_other=Array('S','R','c','d','u','e','w','i','r','N','n');
			if(!in_array($gp_sk,$g_other)){
				$gp_sk='';
			}
			global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
		//	if($unfull){
		//
		//	}else{
				$log .= "你从枪械的零件中挑选出了一些合适的附件，看来可以用来改造其他枪械！<br>";
				$itm0=$gun_other[$gp_sk]; 
				$itmk0='GP'; 
				$itme0=round(($wepe/10)*(rand(1,10)/rand(1,10))+1); 
				$itms0=1; 
				$itmsk0=$gp_sk;
		//	}
			include_once GAME_ROOT . './include/game/itemmain.func.php';
			itemget();
			//获得枪械组件
			$gp_bk=$gun_body[$wepk];
			$gp_ek=$gun_barrel[$wepk];
			$gp_ak=$gun_aiming[$wepk];
			$gp_tk = strpos($wepsk,'r') !== false ? $gun_trigger[1] : $gun_trigger[0];
			$gp_mk_array=Array(0);
			if(strpos($wepsk,'d')!==false){	
				array_push($gp_mk_array,1);
			}
			if((strpos($wepsk,'u')!==false)||(strpos($wepsk,'i')!==false)){
				array_push($gp_mk_array,2);
			}
			if((strpos($wepsk,'e')!==false)||(strpos($wepsk,'w')!==false)){
				array_push($gp_mk_array,3);
			}
			$gp_mk=$gun_ammo[$gp_mk_array[array_rand($gp_mk_array)]];
			$gp_k=Array($gp_tk,$gp_bk,$gp_ek,$gp_ak,$gp_mk);
			$wep=$gp_k[array_rand($gp_k)];
			$wepk=$gun_k[$wep];
			$wepe=round(($wepe/10)*(rand(1,10)/rand(1,10))+1);
			$weps=1;
			$wepsk='';
			$log .= "与此同时，你把看起来还算完好的枪械组件拿在了手上……也许可以用它来拼成一把新枪？<br>";
			$log .= "获得了物品<span class='yellow'>{$wep}</span>。<br>将<span class='yellow'>{$wep}</span>拿在了手上。<br>";
		//拼抢
		}elseif($itm=='枪械组装工具'){
			$barrel=false;$body=false;$trigger=false;$aiming=false;$ammo=false;$gunmix=false;$gun_e=0;
			for($i = 1; $i <= 6; $i ++) {
			global ${'itm' . $i},${'itme' . $i}, ${'itms' . $i}, ${'itmk' . $i}, ${'itmsk' . $i};
				if(${'itmk' . $i}=='GSe'){
					$barrel=true;
					$barrel_name=${'itm' . $i};
					$gun_e+=${'itme' . $i};
				}
				if(${'itmk' . $i}=='GSb'){
					$body=true;
					$body_name=${'itm' . $i};
					$gun_e+=${'itme' . $i};
				}
				if(${'itmk' . $i}=='GSt'){
					$trigger=true;
					$trigger_name=${'itm' . $i};
					$gun_e+= $ammo_name!=='单发式扳机' ? ${'itme' . $i} : round(${'itme' . $i}*1.4);
				}
				if(${'itmk' . $i}=='GSa'){
					$aiming=true;
					$aiming_name=${'itm' . $i};
					$gun_e+=${'itme' . $i};
				}
				if(${'itmk' . $i}=='GSm'){
					$ammo=true;
					$ammo_name=${'itm' . $i};
					$gun_e+= $ammo_name!=='普通弹匣' ? ${'itme' . $i} : round(${'itme' . $i}*1.4);
				}
			}
			if(($barrel)&&($body)&&($aiming)){
				if(!$ammo){
					$ammo_name=='';
				}
				if(!$trigger){
					$trigger_name='';
				}
				$log .= "你将枪械组件整理好，然后开始使用手中的精密仪器进行扫描。<br>";
				include_once GAME_ROOT . './include/game/item2.func.php';
				mixgun($gun_e,$barrel_name,$body_name,$trigger_name,$aiming_name,$ammo_name);
			}else{
				$log .= "<span class='red'>你背包里的枪械组件不足以拼出一把完整的枪！</span><br>";
			}
		}else{	
			$log .= '这东西该怎么用呢？';
		}
	}elseif (strpos ( $itmk, 'Y' ) === 0 || strpos ( $itmk, 'Z' ) === 0) {
		if ($itm == '电池') {
			//功能需要修改，改为选择道具使用YE类型道具可充电
			$flag = false;
			for($i = 1; $i <= 6; $i ++) {
				global ${'itm' . $i}, ${'itme' . $i};
				if (${'itm' . $i} == '移动PC') {
					${'itme' . $i} += $itme;
					$itms --;
					$flag = true;
					$log .= "为<span class=\"yellow\">${'itm'.$i}</span>充了电。";
					break;
				}
			}
			if (! $flag) {
				$log .= '你没有需要充电的物品。<br>';
			}
		}	elseif ($itm == '群青多面体') {
			global $plsinfo,$nosta,$db,$tablepre;
			$result = $db->query("SELECT pid,name,pls FROM {$tablepre}players WHERE type = 14 && hp > 0");
			$ndata = array();
			while($nd = $db->fetch_array($result)){
				$ndata[$nd['name']] = $nd;
			}
			if(!empty($ndata)){
				foreach($ndata as $key => &$val){
					$npls = $val['pls'];
					while($npls == $val['pls']){
						$npls = rand(1,count($plsinfo)-1);
					}				
					$val['pls'] = $npls;$npls = $plsinfo[$npls];
					$log .= "<span class=\"yellow\">{$key}</span>响应道具号召，移动到了<span class=\"yellow\">{$npls}</span>。<br>";
					addnews($now,'npcmove',$name,$key);
				}
				$db->multi_update("{$tablepre}players",$ndata,'pid');
				if($itms != $nosta){$itms --;}
			}
			
			return;
		}	elseif ($itm == '残响兵器') {
			global $cmd;
			foreach(Array('wep','arb','arh','ara','arf','art') as $val) {
				global ${$val},${$val.'k'}, ${$val.'e'}, ${$val.'s'},${$val.'sk'};
			}
			for($i = 1; $i <= 6; $i ++) {
				global ${'itmk' . $i},${'itm' . $i}, ${'itme' . $i}, ${'itms' . $i},${'itmsk' . $i};
			}
			
			include template('nametag');
			
			$cmd = ob_get_contents();
			ob_clean();
			return;
		} elseif ($itm == '白诘草印章'){
			//巫女探测器
			global $sp;
			$log .= '你手持印章开始祈愿……<br>';
			if ($sp >= 50){
				$sp -=50;
				$log .= '突然间某种神秘力量夺走了你的体力！<br>与此同时，你似乎得到了某种启示——<br><br>';
				include_once GAME_ROOT . './include/game/item2.func.php';
				newradarex(3);
			}else{
				$log .= '然而什么事都没有发生。<br>';
			}
		} elseif ($itm == '毒药') {
			global $cmd;
			for($i = 1; $i <= 6; $i ++) {
				global ${'itmk' . $i},${'itm' . $i}, ${'itme' . $i}, ${'itms' . $i};
			}
			include template('poison');
			
			$cmd = ob_get_contents();
			ob_clean();
			return;
		} elseif (strpos ( $itm, '磨刀石' ) !== false) {
			global $wep, $wepk, $wepe, $weps, $wepsk;
			if (strpos ( $wepk, 'K' ) == 1 && strpos ( $wepsk, 'Z' ) === false) {
				if (strpos($wepsk,'j')!==false){
					$log.='多重武器不能改造。<br>';
					return;
				}
				if (strpos($wepsk,'O')!==false){
					$log.='进化武器不能改造。<br>';
					return;
				}
				$dice = rand ( 0, 100 );
				if ($dice >= 15) {
					$wepe += $itme;					
					$log .= "使用了<span class=\"yellow\">$itm</span>，<span class=\"yellow\">$wep</span>的攻击力变成了<span class=\"yellow\">$wepe</span>。<br>";
					if (strpos ( $wep, '锋利的' ) === false) {
						$wep = '锋利的'.$wep;
					}
				} else {
					$wepe -= ceil ( $itme / 2 );
					if ($wepe <= 0) {
						$log .= "<span class=\"red\">$itm</span>使用失败，<span class=\"red\">$wep</span>损坏了！<br>";
						$wep = $wepk = $wepsk = '';
						$wepe = $weps = 0;
					} else {
						$log .= "<span class=\"red\">$itm</span>使用失败，<span class=\"red\">$wep</span>的攻击力变成了<span class=\"red\">$wepe</span>。<br>";
					}
				}
				
				$itms --;
			} elseif(strpos ( $wepsk, 'Z' ) !== false){
				$log .= '咦……刀刃过于薄了，感觉稍微磨一点都会造成不可逆的损伤呢……<br>';
			} else {
				$log .= '你没装备锐器，不能使用磨刀石。<br>';
			}
		} elseif (preg_match ( "/钉$/", $itm ) || preg_match ( "/钉\[/", $itm )) {
			global $wep, $wepk, $wepe, $weps, $wepsk;
			if (( strpos ( $wep, '棍棒' ) !== false) && ($wepk == 'WP')) {
				if (strpos($wepsk,'j')!==false){
					$log.='多重武器不能改造。<br>';
					return;
				}
				if (strpos($wepsk,'O')!==false){
					$log.='进化武器不能改造。<br>';
					return;
				}
				$dice = rand ( 0, 100 );
				if ($dice >= 10) {
					$wepe += $itme;
					$log .= "使用了<span class=\"yellow\">$itm</span>，<span class=\"yellow\">$wep</span>的攻击力变成了<span class=\"yellow\">$wepe</span>。<br>";
					if (strpos ( $wep, '钉' ) === false) {
						$wep = str_replace ( '棍棒', '钉棍棒', $wep );
					}
				} else {
					$wepe -= ceil ( $itme / 2 );
					if ($wepe <= 0) {
						$log .= "<span class=\"red\">$itm</span>使用失败，<span class=\"red\">$wep</span>损坏了！<br>";
						$wep = $wepk = $wepsk = '';
						$wepe = $weps = 0;
					} else {
						$log .= "<span class=\"red\">$itm</span>使用失败，<span class=\"red\">$wep</span>的攻击力变成了<span class=\"red\">$wepe</span>。<br>";
					}
				}
				
				$itms --;
			} else {
				$log .= '你没装备棍棒，不能安装钉子。<br>';
			}
		} elseif ($itm == '针线包') {
			global $arb, $arbk, $arbe, $arbs, $arbsk, $noarb;
			if (($arb == $noarb) || ! $arb) {
				$log .= '你没有装备防具，不能使用针线包。<br>';
			} elseif(strpos($arbsk,'^')!==false){
				$log .= '<span class="yellow">你不能对背包使用针线包。<br>';
			} elseif(strpos($arbsk,'Z')!==false){
				$log .= '<span class="yellow">该防具太单薄以至于不能使用针线包。</span><br>你感到一阵蛋疼菊紧，你的蛋疼度增加了<span class="yellow">233</span>点。<br>';
			}else {
				$arbe += (rand ( 0, 2 ) + $itme);
				$log .= "用<span class=\"yellow\">$itm</span>给防具打了补丁，<span class=\"yellow\">$arb</span>的防御力变成了<span class=\"yellow\">$arbe</span>。<br>";
				$itms --;
			}
		} elseif ($itm == '力量药剂') {
			global $hp, $mhp;
			if($hp > $mhp){
				$log.="<span class=\"yellow\">不要滥用，否则……</span><br />————药剂上的说明文字<br />";
			}else{
				$hp = $mhp+$itme;
				$log .= "咕咚咕咚，你喝下了<span class=\"yellow\">$itm</span>，好像没有感觉有太大的变化<br />";
				$itms --;
			}
		} elseif ($itm == '★核打击目标指示弹★') {
			global $db,$tablepre,$pid,$name,$pls,$now,$plsinfo;
			$log.= '你把这枚改装过后的驱云弹发射向了天空，驱云弹发出了眩目的红光，把整个大地都照成了红色。<br>
				远处的天空中，一颗明亮的星星，渐渐显露出来。<br>
				定睛看去，那其实不是星星，是飞行器一般的东西，正高速划破天空，身后留下清晰可见的尾迹。<br>
				高速飞行的东西已经很近了，圆锥形的脑袋在阳光下反射着耀眼的金光。<br>
				你急忙趴下身子，就在那一瞬间，整个岛屿便被千万倍亮于太阳的刺眼光芒所淹没了……<br>';
			
			include_once GAME_ROOT.'./include/news.func.php';
			addnews ( 0, 'nuclatt', $name, $itm, $plsinfo[$pls]);
	
			$result = $db->query("SELECT name,hp,tactic,pid FROM {$tablepre}players WHERE pls='$pls' AND type=0 AND hp>0 AND pid<>'$pid'");
			while($tdata = $db->fetch_array($result)) 
			{
				$dmg=rand(500,600);
				if ($tdata['tactic']==2) $dmg=ceil($dmg*0.9);
				$tdata['hp']-=$dmg;
				$w_log = "<span class=\"yellow\">{$name}在{$plsinfo[$pls]}引爆了一颗核弹，你受到了{$dmg}点伤害！</span><br>";
				logsave ( $tdata['pid'], $now, $w_log ,'b');
				if ($tdata['hp']<=0) 
				{
					$w_log = "<span class=\"red\">你被核弹杀死了！</span><br>";
					logsave ( $tdata['pid'], $now, $w_log ,'b');
					$tdata['hp']=0;
					include_once GAME_ROOT . './include/state.func.php';
					$killmsg = kill ( 'nuclbomb', $tdata['name'], 0, $tdata['pid'], $name );
				}	
				$db->array_update("{$tablepre}players",$tdata,"pid={$tdata['pid']}");
			}
			$itms --;
		} elseif ($itm == '消音器') {
			global $wep, $wepk, $wepe, $weps, $wepsk;
			if (strpos ( $wepk, 'WG' ) !== 0) {
				$log .= '你没有装备枪械，不能使用消音器。<br>';
			} elseif (strpos ( $wepsk, 'S' ) === false) {
				$wepsk .= 'S';
				$log .= "你给<span class=\"yellow\">$wep</span>安装了<span class=\"yellow\">$itm</span>。<br>";
				$itms --;
			} else {
				$log .= "你的武器已经安装了消音器。<br>";
			}
		} elseif ($itm == '探测器电池') {
			$flag = false;
			for($i = 1; $i <= 6; $i ++) {
				global ${'itmk' . $i}, ${'itme' . $i}, ${'itm' . $i};
				if (${'itmk' . $i} == 'R') {
					//if((strpos(${'itm'.$i}, '雷达') !== false)&&(strpos(${'itm'.$i}, '电池') === false)) {
					${'itme' . $i} += $itme;
					$itms --;
					$flag = true;
					$log .= "为<span class=\"yellow\">${'itm'.$i}</span>充了电。";
					break;
				}
			}
			if (! $flag) {
				$log .= '你没有探测仪器。<br>';
			}
		} elseif ($itm == '御神签') {
			$log .= "使用了<span class=\"yellow\">$itm</span>。<br>";
			include_once GAME_ROOT . './include/game/item2.func.php';
			divining ();
			$itms --;
		} elseif ($itm == '凸眼鱼') {
			global $db, $tablepre, $name,$now,$corpseprotect;
			$tm = $now - $corpseprotect;//尸体保护
			$db->query ( "UPDATE {$tablepre}players SET weps='0',arbs='0',arhs='0',aras='0',arfs='0',arts='0',itms0='0',itms1='0',itms2='0',itms3='0',itms4='0',itms5='0',itms6='0',money='0' WHERE hp <= 0 AND endtime <= $tm" );
			$cnum = $db->affected_rows ();
			addnews ( $now, 'corpseclear', $nick.' '.$name, $cnum );
			$log .= "使用了<span class=\"yellow\">$itm</span>。<br>突然刮起了一阵怪风，吹走了地上的{$cnum}具尸体！<br>";
			$itms --;
			
		} elseif ($itm == '天候棒') {
			global $weather, $wthinfo, $name;
			$weather = rand ( 10, 13 );
			include_once GAME_ROOT . './include/system.func.php';
			save_gameinfo ();
			addnews ( $now, 'wthchange', $name, $weather );
			$log .= "你转动了几下天候棒。<br>天气突然转变成了<span class=\"red b\">$wthinfo[$weather]</span>！<br>";
			$itms --;
		}	elseif ($itm == '天然呆四面的奖赏') {
			global $wep, $wepk, $wepe, $weps, $wepsk;
			if (! $weps || ! $wepe) {
				$log .= '请先装备武器。<br>';
				return;
			}
			if (strpos($wepsk,'j')!==false){
				$log.='多重武器不能改造。<br>';
				return;
			}
			if (strpos($wepsk,'O')!==false){
				$log.='进化武器不能改造。<br>';
				return;
			}
			$log .= "使用了<span class='yellow'>天然呆四面的奖赏</span>。<br>";
			$log .= "你召唤了<span class='lime'>天然呆四面</span>对你的武器进行改造！<br>";
			addnews ( $now, 'newwep', $name, $itm, $wep );
			$dice=rand(0,99);
			if ($dice<70)
			{
				$log.="<span class='lime'>天然呆四面</span>把你的武器弄坏了！<br>";
				$log.="你的武器变成了一块废铁！<br>";
				$log.="<span class='lime'>“不小心把你的武器弄坏了，还真是对不起呢……<br>";
				$wep="一块废铁"; $wepk="WP"; $wepe=1; $weps=1; $wepsk="";
				$log.="那么…… 给你点补偿吧，请务必收下。”<br></span>";
				$itm=""; $itmk=""; $itme=0; $itms=0; $itmsk="";
				$dice2=rand(0,99);
				global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
				$itm0='四面亲手制作的■DeathNote■'; $itmk0='Y'; $itme0=1; $itms0=1; $itmsk0='z';
				include_once GAME_ROOT . './include/game/itemmain.func.php';
				itemget();
			}
			else  if ($dice<90)
			{
				$log.="<span class='lime'>天然呆四面</span>把玩了一会儿你的武器。<br>";
				$log.="你的武器的耐久似乎稍微多了一点。<br>";
				if (strpos ( $wep, '-改' ) === false) $wep = $wep . '-改';
				$weps += ceil ( $wepe / 200 );
				$itm=""; $itmk=""; $itme=0; $itms=0; $itmsk="";
			}
			else
			{
				$log.="<span class='lime'>天然呆四面</span>把玩了一会儿你的武器。<br>";
				$log.="你的武器似乎稍微变强了一点。<br>";
				if (strpos ( $wep, '-改' ) === false) $wep = $wep . '-改';
				$wepe += ceil ( $wepe / 200 );
				$itm=""; $itmk=""; $itme=0; $itms=0; $itmsk="";
			}
		} elseif ($itm == '武器师安雅的奖赏') {
			global $wep, $wepk, $wepe, $weps, $wepsk, $wp, $wk, $wg, $wc, $wd, $wf, $gemwepinfo;
			if (! $weps || ! $wepe) {
				$log .= '请先装备武器。<br>';
				return;
			}
			if (strpos($wepsk,'j')!==false){
				$log.='多重武器不能改造。<br>';
				return;
			}
			if (strpos($wepsk,'T')!==false){
				$log.='兼容性武器不能改造。<br>';
				return;
			}
			if (strpos($wepsk,'O')!==false){
				$log.='进化武器不能改造。<br>';
				return;
			}
			if (in_array($wep,$gemwepinfo)){
				$log.='魔法宝石武器不能改造。<br>';
				return;
			}
			$dice = rand ( 0, 99 );
			$dice2 = rand ( 0, 99 );
			$skill = array ('WP' => $wp, 'WK' => $wk, 'WG' => $wg, 'WC' => $wc, 'WD' => $wd, 'WF' => $wf );
			arsort ( $skill );
			$skill_keys = array_keys ( $skill );
			$nowsk = substr ( $wepk, 0, 2 );
			$maxsk = $skill_keys [0];
			if (($skill [$nowsk] != $skill [$maxsk]) && ($dice < 30)) {
				$wepk = $maxsk;
				$kind = "更改了{$wep}的<span class=\"yellow\">类别</span>！";
			} elseif (($weps != $nosta) && ($dice2 < 70)) {
				$weps += ceil ( $wepe / 2 );
				$kind = "增强了{$wep}的<span class=\"yellow\">耐久</span>！";
			} else {
				$wepe += ceil ( $wepe / 2 );
				$kind = "提高了{$wep}的<span class=\"yellow\">攻击力</span>！";
			}
			$log .= "你使用了<span class=\"yellow\">$itm</span>，{$kind}";
			addnews ( $now, 'newwep', $nick.' '.$name, $itm, $wep );
			if (strpos ( $wep, '-改' ) === false) {
				$wep = $wep . '-改';
			}
			$itms --;
		} elseif ($itm == '准光折变晶体') {
			global $wep,$wepe,$wepsk,$gemweponinfo,$gemweptwinfo,$gemweptrinfo,$gemwepfoinfo,$gemwepinfo;
			if((in_array($wep,$gemweponinfo))||(in_array($wep,$gemweptwinfo))||(in_array($wep,$gemweptrinfo))||(in_array($wep,$gemwepfoinfo))||(in_array($wep,$gemwepinfo))){
				$upwepe=round($wepe*0.3);$wepe+=$upwepe;
				$log .= "你手中的宝石将晶体吞噬了。宝石中蕴含的信息得以被提纯！<br><span class='yellow'>你的武器效果被提升了<span class='red'>{$upwepe}</span>点！</span><br>";
				$itms --;
			}else{
				$log .= "你身上没有宝石武器可以改造。<br>";
				return;
			}
		} elseif ($itm == '光折变晶体') {
			global $wep,$wepe,$wepsk,$gemweponinfo,$gemweptwinfo,$gemweptrinfo,$gemwepfoinfo,$gemwepinfo;
			if((in_array($wep,$gemweponinfo))||(in_array($wep,$gemweptwinfo))||(in_array($wep,$gemweptrinfo))||(in_array($wep,$gemwepfoinfo))||(in_array($wep,$gemwepinfo))){				
				$upwepe=round($wepe*0.6);$wepe+=$upwepe;
				$log .= "你手中的宝石将晶体吞噬了。宝石中蕴含的信息得以被提纯！<br><span class='yellow'>你的武器效果被提升了<span class='red'>{$upwepe}</span>点！</span><br>";
				$up_dice=rand(1,100);
				if((strpos($wepsk,'r')===false)&&($up_dice<=20)){
					$wepsk.='r';
					$log .= "<span class='yellow'>你的武器具有了连击属性！</span><br>";}
				if((strpos($wepsk,'d')===false)&&($up_dice<=25)){
					$wepsk.='d';
					$log .= "<span class='yellow'>你的武器具有了爆炸属性！</span><br>";}	
				$itms --;
			}else{
				$log .= "你身上没有宝石武器可以改造。<br>";
				return;
			}			
		} elseif ($itm == '■DeathNote■' || $itm == '四面亲手制作的■DeathNote■') {
			$mode = 'deathnote';
			$log .= "你翻开了{$itm}<br>";
			return;
		} elseif ($itm == '游戏解除钥匙') {
			global $url;
			$state = 6;
			$url = 'end.php';
			include_once GAME_ROOT . './include/system.func.php';
			gameover ( $now, 'end3', $name );
		} elseif ($itm == '『C.H.A.O.S』') {
			global $gametype,$ss,$rp,$killnum,$att,$def,$log;
			if ($gametype==2)
			{
				$log.="你使用了$itm，但是什么也没有发生。$itm用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				$flag=false;
				$log.="一阵强光刺得你睁不开眼。<br>强光逐渐凝成了光球，你揉揉眼睛，发现包裹里的东西全都不翼而飞了。<br>";
				for ($i=1;$i<=6;$i++){
					global ${'itm'.$i},${'itmk'.$i},${'itme'.$i},${'itms'.$i},${'itmsk'.$i};
					$itm = & ${'itm'.$i};
					$itmk = & ${'itmk'.$i};
					$itme = & ${'itme'.$i};
					$itms = & ${'itms'.$i};
					$itmsk = & ${'itmsk'.$i};
					if ($itm=='黑色发卡') {$flag=true;}
					$itm = '';
					$itmk = '';
					$itme = 0;
					$itms = 0;
					$itmsk = '';
				}
				global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
				$karma=$rp*$killnum-$def+$att;
				$f1=false;
				//『G.A.M.E.O.V.E.R』itmk:Y itme:1 itms:1 itmsk:zxZ
				if (($ss>=600)&&($killnum<=15)){
					$itm0='『T.E.R.R.A』';
					$itmk0='Y';
					$itme0=1;
					$itms0=1;
					$itmsk0='z';
					include_once GAME_ROOT . './include/game/itemmain.func.php';
					itemget();
					$f1=true;
				}
				if ($karma<=2000){
					$itm0='『A.Q.U.A』';
					$itmk0='Y';
					$itme0=1;
					$itms0=1;
					$itmsk0='x';
					include_once GAME_ROOT . './include/game/itemmain.func.php';
					itemget();
					$f1=true;
				}
				if ($flag==true){
					$itm0='『V.E.N.T.U.S』';
					$itmk0='Y';
					$itme0=1;
					$itms0=1;
					$itmsk0='Z';
					include_once GAME_ROOT . './include/game/itemmain.func.php';
					itemget();
					$f1=true;
				}
				if ($f1==false){
					$itm0='『S.C.R.A.P』';
					$itmk0='Y';
					$itme0=1;
					$itms0=1;
					include_once GAME_ROOT . './include/game/itemmain.func.php';
					itemget();
				}
			}
		} elseif ($itm == '『G.A.M.E.O.V.E.R』') {
			global $url;
			$state = 6;
			$url = 'end.php';
			include_once GAME_ROOT . './include/system.func.php';
			gameover ( $now, 'end7', $name );
		} elseif ($itm == '杏仁豆腐的ID卡') {
			include_once GAME_ROOT . './include/system.func.php';
			$duelstate = duel($now,$itm);
			if($duelstate == 50){
				$log .= "<span class=\"yellow\">你使用了{$itm}。</span><br><span class=\"evergreen\">“干得不错呢，看来咱应该专门为你清扫一下战场……”</span><br><span class=\"evergreen\">“所有的NPC都离开战场了。好好享受接下来的杀戮吧，祝你好运。”</span>——林无月<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}elseif($duelstate == 51){
				$log .= "你使用了<span class=\"yellow\">{$itm}</span>，不过什么反应也没有。<br><span class=\"evergreen\">“咱已经帮你准备好舞台了，请不要要求太多哦。”</span>——林无月<br>";
			} else {
				$log .= "你使用了<span class=\"yellow\">{$itm}</span>，不过什么反应也没有。<br><span class=\"evergreen\">“表演的时机还没到呢，请再忍耐一下吧。”</span>——林无月<br>";
			}
		} elseif ($itm == '紧急情况指示器') {
			include_once GAME_ROOT . './include/system.func.php';
			$duelstate = duel($now,$itm);
			if($duelstate == 50){
				$log .= "<span class=\"yellow\">你使用了{$itm}。</span><br><span class=\"evergreen\">“这里是单向通信，已确实收到岛屿不再适合用于实验计划的紧急情况指示。即将疏散撤离战场上的所有相关人员……”</span><br><span class=\"evergreen\">所有的NPC似乎都离开战场了。但是只有逃杀实验计划并未终止，接下来的杀戮已经无法避免……</span><br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}elseif($duelstate == 51){
				$log .= "你使用了<span class=\"yellow\">{$itm}</span>，<br><span class=\"evergreen\">指示器响起了电子音，“指示已经执行……请勿重复发信……”</span><br>";
			} else {
				$log .= "你使用了<span class=\"yellow\">{$itm}</span>，不过什么反应也没有。<br><span class=\"evergreen\">看来发信的时机不对的样子。</span><br>";
			}
		} elseif ($itm == '奇怪的按钮') {
			global $gametype,$bid;
			$button_dice = rand ( 1, 10 );
			if ($button_dice < 5 || $gametype==2) {
				$log .= "你按下了<span class=\"yellow\">$itm</span>，不过好像什么都没有发生！";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} elseif ($button_dice < 8) {
				global $url;
				$state = 6;
				$url = 'end.php';
				include_once GAME_ROOT . './include/system.func.php';
				gameover ( $now, 'end5', $name );
			} else {
				$log .= '好像什么也没发生嘛？<br>咦，按钮上的标签写着什么？“危险，勿触”……？<br>';
				include_once GAME_ROOT . './include/state.func.php';
				$log .= '呜哇，按钮爆炸了！<br>';
				//$bid = 0;
				death ( 'button', '', 0, $itm );
			}
		} elseif ($itm == '装有H173的注射器') {
			global $wp, $wk, $wg, $wc, $wd, $wf, $club, $bid, $att, $def;
			$log .= '你考虑了一会，<br>把袖子卷了起来，给自己注射了H173。<br>';
			$deathdice = rand ( 0, 8191 );
			if ($deathdice == 8191 || $club == 15) {
				$log .= '你突然感觉到一种不可思议的力量贯通全身！<br>';
				$wp = $wk = $wg = $wc = $wd = $wf = 3000;
				$att = $def = 5000;
				$club = 15;
				addnews ( $now, 'suisidefail',$nick.' '.$name );
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} else {
				include_once GAME_ROOT . './include/state.func.php';
				$log .= '你失去了知觉。<br>';
				//$bid = 0;
				death ( 'suiside', '', 0, $itm );
			}
		} elseif ($itm == '觉醒剂试制二型'){
			global $wp, $wk, $wg, $wc, $wd, $wf, $club, $bid, $att, $def;
			global $hp,$sp;
			$log .= '你考虑了一会，<br>把袖子卷了起来，给自己注射了觉醒剂试制二型。<br>';
			$deathdice = rand ( 1, 100 );
			if ($deathdice <= 5 || $club == 0) {
				$moedice = rand (1,100);
				if($club ==0) $moedice+=20;
				if(($wp<250)&&($wk<250)&&($wg<250)&&($wc<250)&&($wd<250)&&($wf<250)){
					$log .= '你突然感觉到一种不可思议的力量贯通全身！<br>';
					$wp = $wk = $wg = $wc = $wd = $wf = 250;
					//addnews ( $now, 'suisidefail',$nick.' '.$name );
					if($moedice>=95){
						$log .= '你突然觉醒成了走路萌物！<br>';
						$club = 17;
					}
				}else{
					$log .= '你突然感觉到一种不可思议的力量贯通全身,但是不一会儿便消散去了大部分。<br>';
					$wp = floor($wp*1.1);
					$wk = floor($wk*1.1);
					$wg = floor($wg*1.1);
					$wc = floor($wc*1.1);
					$wd = floor($wd*1.1);
					$wf = floor($wf*1.1);
					if($moedice>=75){
						$log .= '你突然觉醒成了走路萌物！<br>';
						$club = 17;
					}
				}
				
			} elseif($deathdice > 35) {
				$log .= '你突然感觉到强烈的痛苦伴随着一种不可思议的力量贯通全身！<br>
				持续了一段时间的煎熬结束了，你发现自己并没有什么显著变化。';
			} else {
				$log .= '你突然感觉到强烈的痛苦伴随着一种不可思议的力量贯通全身！<br>
				持续了一段时间的煎熬结束了，你感觉十分虚弱。';
				$hp=1;
				$sp=0;
				//include_once GAME_ROOT . './include/state.func.php';
				//$log .= '你失去了知觉。<br>';
				//$bid = 0;
				//death ( 'suiside', '', 0, $itm );
			}
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		} elseif (strpos($itm, '溶剂SCP-294')===0) {
			global $wp, $wk, $wg, $wc, $wd, $wf, $club, $att, $def, $hp, $mhp, $sp, $msp, $rp;
			if ($club==23){
				$log .= "<span class=\"yellow\">拳法家不走捷径。</span><br>";
				$mode = 'command';
				return;
			}
			if($itm == '溶剂SCP-294_PT_Poini_Kune'){
				$log .= '你考虑了一会，一扬手喝下了杯中中冒着紫色幽光的液体。<br><span class="yellow">你感到全身就像燃烧起来一样，不禁扪心自问这值得么？</span><br>';
				if ($mhp > 573){
					$up = rand (0, $mhp + $msp);
				} else{
					$up = rand (0, 573);
				}
				
				if ($club==21) $up=10000;
				
				if($club == 17){
					$hpdown = $spdown = round($up * 2.5);
				}elseif($club == 13){
					//根性兄贵加成消失
					$hpdown = round($up*1.5)+200;
					$spdown = round($up*1.5);
				}else{
					$hpdown = $spdown = round($up*1.5);
				}
				$wp += $up;$wk += $up;$wg += $up;$wc += $up;$wd += $up;$wf += $up;
				$rp += 500;
				//$down = $club == 17 ? round($up * 1.5) : $up;
				
				$mhp = $mhp - $hpdown;
				$msp = $msp - $spdown;				
				$log .= '你的生命上限减少了<span class="yellow">'.$hpdown.'</span>点，体力上限减少了<span class="yellow">'.$spdown.'</span>点，而你的全系熟练度提升了<span class="yellow">'.$up.'</span>点！<br>';
			} elseif ($itm == '溶剂SCP-294_PT_Arnval'){
				$log .= '你考虑了一会，一扬手喝下了杯中中冒着白色气泡的清澈液体。<br><span class="yellow">你感到全身就像燃烧起来一样，不禁扪心自问这值得么？</span><br>';
				if ($msp > 573){
					$up = rand (0, $msp * 1.5);
				} else{
					$up = rand (0, 573);
				}
				if ($club==21) $up=10000;
				$mhp = $mhp + $up;
				$def = $def + $up;
				$down = $club == 17 ? round($up * 1.5) : $up;
				$rp += 200;
				$msp = $msp - $down;
				$att = $att - $down;
				
				$log .= '你的体力上限和攻击力减少了<span class="yellow">'.$down.'</span>点，而你的生命上限和防御力提升了<span class="yellow">'.$up.'</span>点！<br>';
			} elseif ($itm == '溶剂SCP-294_PT_Strarf') {
				$log .= '你考虑了一会，一扬手喝下了杯中中冒着灰色气泡的清澈液体。<br><span class="yellow">你感到全身就像燃烧起来一样，不禁扪心自问这值得么？</span><br>';
				if ($mhp > 573){
					$up = rand (0, $msp * 1.5);
				} else{
					$up = rand (0, 573);
				}
				if ($club==21) $up=10000;
				$msp = $msp + $up;
				$att = $att + $up;
				$down = $club == 17 ? round($up * 1.5) : $up;
				$rp += 200;
				$mhp = $mhp - $down;
				$def = $def - $down;
				$log .= '你的生命上限和防御力减少了<span class="yellow">'.$down.'</span>点，而你的体力上限和攻击力提升了<span class="yellow">'.$up.'</span>点！<br>';
			} elseif ($itm == '溶剂SCP-294_PT_ErulTron') {
				$log .= '你考虑了一会，<br>一扬手喝下了杯中中冒着粉红光辉的液体。<br>你感到你整个人貌似变得更普通了点。<br>';
				global $lvl, $exp;
				if ($club!=21)
				{
					$lvl = $exp = 0;
					$att = round($att * 0.8);
					$def = round($def * 0.8);
					$log .= '<span class="yellow">你的等级和经验值都归0了！但是，你的攻击力和防御力也变得更加普通了。</span><br>';
				}
				else
				{
					$mhp = $msp = 0;
					$att = round($att * 0.8);
					$def = round($def * 0.8);
					$log .= '<span class="yellow">你的生命和体力值都归0了！但是，你的攻击力和防御力也变得更加普通了。</span><br>';
				}
			}
			if($att < 0){$att = 0;}
			if($def < 0){$def = 0;}
			if($hp > $mhp){$hp = $mhp;}
			if($sp > $msp){$sp = $msp;}
			$deathflag = false;
			if($mhp <= 0){$hp = $mhp =0;$deathflag = true;}
			if($msp <= 0){$sp = $msp =0;$deathflag = true;}
			if($deathflag){
				$log .= '<span class="yellow">看起来你的身体无法承受药剂的能量……<br>果然这一点都不值得……<br></span>';
				include_once GAME_ROOT . './include/state.func.php';
				death ( 'SCP', '', 0, $itm );
			} else {
				$club = 17;
				addnews ( $now, 'notworthit', $nick.' '.$name );
			}
			$itms --;
			if($itms <= 0){
				if($hp > 0){$log .= "<span class=\"yellow\">{$itm}用完了。</span><br>";}
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '挑战者之印') {
			global $gametype;				
			if ($gametype==2)
			{
				$log.="你使用了{$itm}，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= '你已经呼唤了幻影执行官，现在寻找并击败他们，<br>并且搜寻他们的ID卡吧！<br>';
				addnpc ( 7, 0,1);
				addnpc ( 7, 1,1);
				addnpc ( 7, 2,1);
				addnews ($now , 'secphase',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '可疑的发信器') {
			global $gametype;				
			if ($gametype==2)
			{
				$log.="你使用了{$itm}，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= '你启动了发信器，<br>也许什么人已经迅速定位了你的位置并且正在前来，<br>搜寻打倒他们还是赶紧逃跑？赶紧作出抉择吧！<br>';
				addnpc ( 16, 0,1);
				addnpc ( 16, 1,1);
				$ex_add=rand(1,10);
				if ($ex_add <= 5) addnpc ( 16, 2,1);
				addnews ($now , 'ghost9',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '书库联结') {
			global $gametype;				
			if ($gametype==2)
			{
				$log.="你使用了{$itm}，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= "你启动了{$itm}，<br>“资料重载……具现率25%……40%……70%……95%……具现完成。确认资讯，旧具现体损坏率70%以上，判断废弃。”<br>
				“全面出力处理完成，沟通界面解放。”<br>“姆呼呼呼……我和整个书库界面一起在灵子研究中心等着你们哦。<br>”";
				addnpc ( 13, 1,1);
				addnews ($now , 'wikigirl',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '测试NPC召唤器') {
			global $gametype;				
			if ($gametype==2)
			{
				$log.="你使用了{$itm}，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$test = 1;
				if($test){
					$log .= "你启动了{$itm}，近期某个开发测试中的NPC被你召唤出来了！<br>";
					addnpc ( 13, 0,1);
					addnews ($now , 'testnpc',$nick.' '.$name);
				}else{
					$log .= "你启动了{$itm}，但是由于最近并没有什么开发测试中的NPC，所以什么事都没有发生。<br>";
				}
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '恶魔晶状体') {
			global $gametype;				
			if ($gametype==2)
			{
				$log.="你使用了{$itm}，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$rollnum=rand(1,10);
				if ($rollnum <= 2){
				$log .= '你不顾后果召唤了某个深渊灾厄，<br>现在后悔也来不及了。<br>';
				$npcno=rand(0,4);
				addnpc ( 17, $npcno,1);
				addnews ($now , 'demon',$nick.' '.$name);
				}else{
				$log .= '你不顾后果企图召唤深渊灾厄，<br>但是什么事也没有发生。<br>';
				}
				$itms --;
				if($itms <= 0){
					if($hp > 0){$log .= "<span class=\"yellow\">{$itm}用完了。</span><br>";}
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			}
		} elseif ($itm == '霜雪之心') {
			global $wepk,$wepsk;
			if($wepk=='WN'){
				$log .="你尝试使用{$itm}，但是什么事也没有发生。<br>";
			}else{
				$log .="{$itm}好像受到了某种引力一样，融进了你手持的武器里！<br>";
				$wepsk=str_replace('i','',$wepsk);
				$wepsk.='i';
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '空白歌词卡') {
			if($club != 70){
				$log .="你尝试创作一首歌，但是由于才能不足没有成功。<br>";
			}else{
				global $art,$artk,$arte,$arts;
				if(strpos($artk,'A')===false){
					$log .="你尝试创作一首歌，但是你没有得到足够的灵感。<br>";
				}else{
					$log .="你灵思如泉涌，为你佩戴的{$art}创作了一首歌！<br>随后你运用娴熟的手法，把{$art}潜藏的能量都注入到歌词卡里了！<br>";
					$log .="{$itm}用完了！<br>";
					$art .="之歌";
					$artk ='ss';
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			}
		} elseif ($itm == '北大路的便笺') {
			$log .= '你读着便笺上的内容：“……看得出来，伍长一直觊觎着这个叫做九系统的东西。<br>……为了避免本次任务节外生枝，不能让他知道潜艇秘密搬运了九系统的其中一台……”<br>';
		} elseif ($itm == '九老人的手记卷轴') {
			$log .= '你读了手记，了解到了九系统的一些秘密以及重启指令。<br>据手记描述，九系统重启的话会发生超光加速……关于之后会导致什么事情发生的描述文字被涂抹掉了。”<br>';
		} elseif ($itm == '九系统的重启方法') {
			global $pls;
			if($pls!=23){
				$log .="在考虑如何实施之前，近距离接触到九系统是必要的！<br>";			
			}else{
				global $url;
				$state = 6;
				$url = 'end.php';
				include_once GAME_ROOT . './include/system.func.php';
				gameover ( $now, 'end11', $name );
			}
		} elseif ($itm == '不要按这个按钮！') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="你按下了按钮，但是什么也没有发生。$itm用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= "<span class=\"linen\">“你们都干了些什么？没有看见上面写的“不要按这个按钮”吗？你们启动了解离系统，还怎么完成虚拟幻境的测试呀？”</span><br><span class=\"lime\">紧急纠错代码已在四季之镇活性化！</span><br>";
				addnpc ( 30, 0,1);
				addnpc ( 30, 1,1);
				addnpc ( 30, 2,1);
				addnpc ( 30, 3,1);
				addnews ($now , 'heromode1',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '幻境配置终端') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="你使用了终端，但是什么也没有发生。{$itm}用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= "<span class=\"linen\">“你以为你可以随意的玩弄本公司的幻境系统这一杰作吗？”</span><br><span class=\"lime\">紧急纠错代码已在三所学校活性化！</span><br>";
				addnpc ( 31, 0,1);
				addnpc ( 31, 1,1);
				addnpc ( 31, 2,1);
				addnews ($now , 'heromode2',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '写着代码的小纸条') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="纸条化为一阵烟消失了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= "纸条化为一阵烟消失了。<br><span class=\"lime\">幻境变得不稳定起来，森林中出现了新的代码聚合体！</span><br>";
				addnpc ( 32, 0,1);
				addnpc ( 32, 1,1);
				addnpc ( 32, 2,1);
				addnews ($now , 'heromode3',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '幻境解离代码') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="你使用了代码，但是好像什么都没有发生！<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= "幻境剧烈的震动起来。这就是……幻境解离吗？<br><span class=\"linen\">“不要以为你赢了！我要亲手处理你们！”</span><br><span class=\"lime\">从冰封墓场传来强大的能量反应！</span><br>";
				addnpc ( 33, 0,1);
				addnpc ( 33, 0,1);
				addnews ($now , 'heromode4',$nick.' '.$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '破灭之诗') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="你使用了$itm，但是什么也没有发生。$itm用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				global $hack,$rp;
				$rp = -23333;
				include_once GAME_ROOT . './include/system.func.php';
				$log .= '在你唱出那单一的旋律的霎那，<br>整个虚拟世界起了翻天覆地的变化……<br>';
				addnpc ( 4, 0,1);
				include_once GAME_ROOT . './include/game/item2.func.php';
				$log .= '世界响应着这旋律，产生了异变……<br>';
				wthchange( $itm,$itmsk);
				addnews ($now , 'thiphase',$nick.' '.$name);
				$hack = 1;
				$log .= '因为破灭之歌的作用，全部锁定被打破了！<br>';
				//include_once GAME_ROOT.'./include/system.func.php';
				movehtm();
				addnews($now,'hack2',$nick.' '.$name);
				save_gameinfo();
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '黑色碎片') {
			global $gametype;
			if ($gametype==2)
			{
				$log.="你使用了$itm，但是什么也没有发生。$itm用完了。<br>";
				$itm = ''; $itmk = ''; $itme = 0; $itms = 0; $itmsk = '';
			}
			else
			{
				include_once GAME_ROOT . './include/system.func.php';
				$log .= '你已经呼唤了一个未知的存在，现在寻找并击败她，<br>并且搜寻她的游戏解除钥匙吧！<br>';
				addnews ($now , 'dfphase', $nick.' '.$name);
				addnpc ( 12, 0,1);
			
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			}
		} elseif ($itm == '佣兵召唤器') {
				include_once GAME_ROOT . './include/system.func.php';
				$yd=rand(0,10);
				addnpc ( 25, $yd,1,$name);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
		} elseif ($itm == '镣铐的碎片') {
//			include_once GAME_ROOT . './include/system.func.php';
//			$log .= '呜哦，看起来你闯了大祸……<br>请自己去收拾残局！<br>';
//			addnpc ( 12, 0,1);
//			addnews ($now , 'dfsecphase', $name);
//			$itm = $itmk = $itmsk = '';
//			$itme = $itms = 0;
		} elseif($itm == '莱卡召唤器') {
//			include_once GAME_ROOT . './include/system.func.php';
//			global $db,$tablepre;
//			$result = $db->query("SELECT pid FROM {$tablepre}players WHERE type = 13");
//			$num = $db->num_rows($result);
//			if($num){
//				$log.= '召唤器似乎用尽了能量。<br>';
//			}else{
//				addnpc ( 13, 0,1);
//				$log.= '你成功召唤了小莱卡，去测试吧。<br>';
//			}
//			$n_name = evonpc (1,'红暮');
//			if($n_name){
//				addnews($now , 'evonpc','红暮', $n_name);
//			}
		} elseif ($itm == '提示纸条A') {
			$log .= '你读着纸条上的内容：<br>“执行官其实都是幻影，那个红暮的身上应该有召唤幻影的玩意。”<br>“用那个东西然后打倒幻影的话能用游戏解除钥匙出去吧。”<br>';
		} elseif ($itm == '提示纸条B') {
			$log .= '你读着纸条上的内容：<br>“我设下的灵装被残忍地清除了啊……”<br>“不过资料没全部清除掉。<br>用那个碎片加上传奇的画笔和天然属性……”<br>“应该能重新组合出那个灵装。”<br>';
		} elseif ($itm == '提示纸条C') {
			$log .= '你读着纸条上的内容：<br>“小心！那个叫红暮的家伙很强！”<br>“不过她太依赖自己的枪了，有什么东西能阻挡那伤害的话……”<br>';
		} elseif ($itm == '提示纸条D') {
			$log .= '你读着纸条上的内容：<br>“我不知道另外那个孩子的底细。如果我是你的话，不会随便乱惹她。”<br>“但是她貌似手上拿着符文册之类的东西。”<br>“也许可以利用射程优势？！”<br>“你知道的，法师的射程都不咋样……”';
		} elseif ($itm == '提示纸条E') {
			$log .= '你读着纸条上的内容：<br>“生存并不能靠他人来喂给你知识，”<br>“有一套和元素有关的符卡的公式是没有出现在帮助里面的，用逻辑推理好好推理出正确的公式吧。”<br>“金木水火土在这里都能找到哦～”<br>';
		} elseif ($itm == '提示纸条F') {
			$log .= '你读着纸条上的内容：<br>“喂你真的是全部买下来了么……”<br>“这样的提示纸条不止这六种，其他的纸条估计被那两位撒出去了吧。”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '提示纸条G') {
			$log .= '你读着纸条上的内容：<br>“上天保佑，”<br>“请不要在让我在模拟战中被击坠了！”<br>“空羽 上。”<br>';
		} elseif ($itm == '提示纸条H') {
			$log .= '你读着纸条上的内容：<br>“在研究施设里面出了大事的SCP竟然又输出了新的样本！”<br>“按照董事长的意见就把这些家伙当作人体试验吧！”<br>署名看不清楚……<br>';
		} elseif ($itm == '提示纸条I') {
			$log .= '你读着纸条上的内容：<br>“嗯……”<br>“制作神卡所用的各种认证都可以在商店里面买到。”<br>“其实卡片真的有那么强大的力量么？”<br>';
		} elseif ($itm == '提示纸条J') {
			$log .= '你读着纸条上的内容：<br>“知道么？”<br>“果酱面包果然还是甜的好，哪怕是甜的生姜也能配制出如地雷般爆炸似的美味。”<br>“祝你好运。”<br>';
		} elseif ($itm == '提示纸条K') {
			$log .= '你读着纸条上的内容：<br>“水符？”<br>“你当然需要水，然后水看起来是什么颜色的？”<br>“找一个颜色类似的东西合成就有了吧。”<br>';
		} elseif ($itm == '提示纸条L') {
			$log .= '你读着纸条上的内容：<br>“木符？”<br>“你当然需要树叶，然后说到树叶那是什么颜色？”<br>“找一个颜色类似的东西合成就有了吧。”<br>';
		} elseif ($itm == '提示纸条M') {
			$log .= '你读着纸条上的内容：<br>“火符？”<br>“你当然需要找把火，然后说到火那是什么颜色？”<br>“找一个颜色类似的东西合成就有了吧。”<br>';
		} elseif ($itm == '提示纸条N') {
			$log .= '你读着纸条上的内容：<br>“土符？”<br>“说到土那就是石头吧，然后说到石头那是什么颜色？”<br>“找一个颜色类似的东西合成就有了吧。”<br>';
		} elseif ($itm == '提示纸条P') {
			$log .= '你读着纸条上的内容：<br>“金符？这个的确很绕人……”<br>“说到金那就是炼金，然后这是21世纪了，炼制一个金色方块需要什么？”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '提示纸条Q') {
			$log .= '你读着纸条上的内容：<br>“据说在另外的空间里面；”<br>“一个吸血鬼因为无聊就在她所居住的地方洒满了大雾，”<br>“真任性。”<br>';
		} elseif ($itm == '提示纸条R') {
			$log .= '你读着纸条上的内容：<br>“知道么，”<br>“东方幻想乡这作游戏里面EXTRA的最终攻击”<br>“被老外们称作『幻月的Rape Time』，当然对象是你。”<br>';
		} elseif ($itm == '提示纸条S') {
			$log .= '你读着纸条上的内容：<br>“土水符？”<br>“哈哈哈那肯定是需要土和水啦，可能还要额外的素材吧。”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '提示纸条T') {
			$log .= '你读着纸条上的内容：<br>“我一直对虚拟现实中的某些迹象很在意……”<br>“这种未名的威压感是怎么回事？”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '提示纸条U') {
			$log .= '你读着纸条上的内容：<br>“纸条啥的……”<br>“希望这张纸条不会成为你的遗书。”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '提示纸条金') {
			$log .= '你读着纸条上的内容：<br>“存在便是真理。”<br>';
		} elseif ($itm == '提示纸条木') {
			$log .= '你读着纸条上的内容：<br>“落华终将消亡。”<br>';
		} elseif ($itm == '提示纸条水') {
			$log .= '你读着纸条上的内容：<br>“源数生出万物。”<br>';
		} elseif ($itm == '提示纸条火') {
			$log .= '你读着纸条上的内容：<br>“放弃则为获取。”<br>';
		} elseif ($itm == '提示纸条土') {
			$log .= '你读着纸条上的内容：<br>“一事不能二解。”<br>';
		} elseif ($itm == '提示纸条雷') {
			$log .= '你读着纸条上的内容：<br>“Saki, Lets kill Kazumi.”<br>';
		} elseif ($itm == '提示纸条风') {
			$log .= '你读着纸条上的内容：<br>“崇公道——12？13？”<br>';
		} elseif ($itm == '提示纸条震') {
			$log .= '你读着纸条上的内容：<br>“i18n为必要却实际多余。”<br>';
		} elseif ($itm == '提示纸条泽') {
			$log .= '你读着纸条上的内容：<br>“Someday isnt today.”<br>';
		} elseif ($itm == '提示纸条城壁') {
			$log .= '你读着纸条上的内容：<br>“过去的航海图永远不属于未来。”<br>';
		} elseif ($itm == '提示纸条主教') {
			$log .= '你读着纸条上的内容：<br>“元素的排列并不是真正的色彩。”<br>';
		} elseif ($itm == '提示纸条骑士') {
			$log .= '你读着纸条上的内容：<br>“拿起和放下不等于生命的重量。”<br>';
		} elseif ($itm == '提示纸条国王') {
			$log .= '你读着纸条上的内容：<br>“十五之轮回是不可理解的漩涡。”<br>';
		} elseif ($itm == '提示纸条皇后') {
			$log .= '你读着纸条上的内容：<br>“知识的深度将不敌蓝海的渊博。”<br>';
		} elseif ($itm == '提示纸条氢') {
			$log .= '你读着纸条上的内容：<br>“MAX300的BPM不会减慢。”<br>';
		} elseif ($itm == '提示纸条氦') {
			$log .= '你读着纸条上的内容：<br>“生命之源业已停止流动。”<br>';
		} elseif ($itm == '提示纸条锂') {
			$log .= '你读着纸条上的内容：<br>“列车阻断了世界的两边。”<br>';
		} elseif ($itm == '提示纸条铍') {
			$log .= '你读着纸条上的内容：<br>“故事却已经为了老友画上句点。”<br>';
		} elseif ($itm == '提示纸条硼') {
			$log .= '你读着纸条上的内容：<br>“戏里戏外的经验亦皆是广告中的人生。”<br>';
		} elseif ($itm == '提示纸条碳') {
			$log .= '你读着纸条上的内容：<br>“二零零一的太空幻想却未曾实现。”<br>';
		} elseif ($itm == '提示纸条氮') {
			$log .= '你读着纸条上的内容：<br>“繁花的歌颂终将飘散。 ”<br>';
		} elseif ($itm == '提示纸条氧') {
			$log .= '你读着纸条上的内容：<br>“石桥之方程式刻痕已然熄灭。”<br>';
		} elseif ($itm == '提示纸条氟') {
			$log .= '你读着纸条上的内容：<br>“重复的轮回究竟有何意义？”<br>';
		} elseif ($itm == '提示纸条氖') {
			$log .= '你读着纸条上的内容：<br>“勇者聆听着E大人最美好的声音。”<br>';
		} elseif ($itm == '提示纸条钠') {
			$log .= '你读着纸条上的内容：<br>“可重复的描写只能为文豪使用一次。”<br>';
		} elseif ($itm == '提示纸条镁') {
			$log .= '你读着纸条上的内容：<br>“金石的重量，鲜有人知——”<br>';
		} elseif ($itm == '提示纸条铝') {
			$log .= '你读着纸条上的内容：<br>“一百单八将的次序，有人记得。 ”<br>';
		} elseif ($itm == '提示纸条硅') {
			$log .= '你读着纸条上的内容：<br>“艺术家的疯狂绘画最终也是角落的回忆。”<br>';
		} elseif ($itm == '提示纸条磷') {
			$log .= '你读着纸条上的内容：<br>“从繁到简才是真正的出路。”<br>';
		} elseif ($itm == '提示纸条铀') {
			$log .= '你读着纸条上的内容：<br>“从天外而来的启示，是开启新计划的钥匙……？”<br>';
		} elseif ($itm == '破烂的日记') {
			$log .= '你翻开了日记，但是前面的字迹全部都莫名其妙的无法认知，只有最后一页的可以理解：<br>“我已经不记得这是第多少次了，就连我本人的记忆也因为重复次数的过多导致开始消失了……或许这个计划真的是不可逆转的，但是我已经没有退路了。”<br>';
		} elseif ($itm == '人品探测器') {
			global $rp;
			$log .= '你读着纸条上的内容：<br>“你的RP值为'.$rp.'。”<br>“总之祝你好运。”<br>';
		} elseif ($itm == '仪水镜') {
			global $rp;
			$log .= '水面上映出了你自己的脸，你仔细端详着……<br>';
			if ($rp < 40){
				$log .= '你的脸看起来十分白皙。<br>';
			} elseif ($rp < 200){
				$log .= '你的脸看起来略微有点黑。<br>';
			} elseif ($rp < 550){
				$log .= '你的脸上貌似笼罩着一层黑雾。<br>';
			} elseif ($rp < 1200){
				$log .= '你的脸已经和黑炭差不多了，赶快去洗洗！<br>';
			} elseif ($rp < 5499){
				$log .= '你印堂漆黑，看起来最近要有血光之灾！<br>';
			} elseif ($rp > 5500){
				$log .= '水镜中已经黑的如墨一般了。<br>希望你的H173还在……<br>';
			} else{
				$log .= '你的脸从水镜中消失了。<br>';
			}
		} elseif ($itm == '送给全能之人的纪念评价簿') {
			global $rp;
			$log .= "你打开了纪念簿，纪念簿第一页写到<span class=\"clan\"><br>
					“當你翻開這一本紀念簿時，想必我已經被推倒了。<br>
					很榮幸能為你接下來的PVE道路提供一些微不足道的幫助，<br>
					這本紀念簿上記載了一些隱藏合成，和一些對你今後PVE幫助的小提示。<br>
					順帶一提，</span><br>";
					if ($rp < 40){
						$log .= "<span class=\"clan\">你現在的RP值可以讓你<span class=\"red\">規避一切危險事件</span>，請放心前去雛菊或圣G:)</span><br>";
					}elseif ($rp > 40){
						$log .= "<span class=\"clan\">你現在的RP值比較<span class=\"red\">危險</span>，最好去摸一個<span class=\"yellow\">【RP回覆設備】</span>再去雛菊或圣G等帶有危險事件的地區。<br>
								出於安全，<span class=\"red\">我已經將你的RP值歸0了</span>，只要不過多擊殺NPC或玩家就不會有大問題:)</span><br>";
					}
					$rp=0;
					$log .= '你翻到了纪念簿的第二页，上面罗列着密密麻麻的文字，你第一眼就看到了<br>';
					$dice=rand(1,5);
					if($dice==1){
					$log .= "<span class=\"red\">“恐怖份子轉換地雷失敗的概率是60%，因此要儘量選擇價值高的地雷轉換。”</span><br>";
					$log .= "<span class=\"yellow\">“我也只能幫你這些了，剩下的路就要靠你自己走了。”</span><br>随着一阵声音响起，纪念簿化为一阵烟尘消散了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
					}elseif($dice==2){
					$log .= "<span class=\"red\">“棍棒如果上釘，該棍棒如果是某合成的其中一部分素材是不能合成的，比如冰釘棍棒就不能拿去合成大9符。”</span><br>";
					$log .= "<span class=\"yellow\">“我也只能幫你這些了，剩下的路就要靠你自己走了。”</span><br>随着一阵声音响起，纪念簿化为一阵烟尘消散了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
					}elseif($dice==3){
					$log .= "<span class=\"red\">“祝福寶石可以強化道具至+4，但是有1/3的失效率，其實對於想合成悲歎之種的人來說，強化成功就意味著失敗呢。”</span><br>";
					$log .= "<span class=\"yellow\">“我也只能幫你這些了，剩下的路就要靠你自己走了。”</span><br>随着一阵声音响起，纪念簿化为一阵烟尘消散了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
					}elseif($dice==4){
					$log .= "<span class=\"red\">“衝擊屬性的概率被提高至80%，現在是非常有用的屬性。”</span><br>";
					$log .= "<span class=\"yellow\">“我也只能幫你這些了，剩下的路就要靠你自己走了。”</span><br>随着一阵声音响起，纪念簿化为一阵烟尘消散了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
					}elseif($dice==5){
					$log .= "<span class=\"red\">“超量合成時，只需要有一張素材，如果另一張卡片的星數相同，素材和卡片也可以超量。很有用的技巧呢（笑”</span><br>";
					$log .= "<span class=\"yellow\">“我也只能幫你這些了，剩下的路就要靠你自己走了。”</span><br>随着一阵声音响起，纪念簿化为一阵烟尘消散了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
					}
		} elseif ($itm == '风祭河水'){
			global $rp, $wp, $wk, $wg, $wc, $wd, $wf;
			$slv_dice = rand ( 1, 20 );
				if ($slv_dice < 8) {
				$log .= "你一口干掉了<span class=\"yellow\">$itm</span>，不过好像什么都没有发生！";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} elseif ($slv_dice < 16) {
				$rp = $rp - 10*$slv_dice;
				$log .= "你感觉身体稍微轻了一点点。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} elseif ($slv_dice < 20) {
				$rp = 0 ;
				$log .= "你头晕脑胀地躺到了地上，<br>感觉整个人都被救济了。<br>你努力着站了起来。<br>";
				$wp = $wk = $wg = $wc = $wd = $wf = 100;
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} else {
				$log .= '你头晕脑胀地躺到了地上，<br>感觉整个人都被救济了。<br>';
				include_once GAME_ROOT . './include/state.func.php';
				$log .= '然后你失去了意识。<br>';
				//$bid = 0;
				death ( 'salv', '', 0, $itm );
			}
		} elseif ($itm == '『灵魂宝石』' || $itm == '『祝福宝石』') {
			global $cmd;
			$cmd = '<input type="hidden" name="mode" value="item"><input type="hidden" name="usemode" value="qianghua"><input type="hidden" name="itmp" value="' . $itmn . '">你想强化哪一件装备？<br><input type="radio" name="command" id="menu" value="menu" checked><a onclick=sl("menu"); href="javascript:void(0);" >返回</a><br><br><br>';
			for($i = 1; $i <= 6; $i ++) {
				global ${'itmsk' . $i};
				if ((strpos ( ${'itmsk' . $i}, 'Z' ) !== false) && (strpos ( ${'itm' . $i}, '宝石』' ) === false)) {
					global ${'itm' . $i}, ${'itme' . $i}, ${'itms' . $i};
					$cmd .= '<input type="radio" name="command" id="itm' . $i . '" value="itm' . $i . '"><a onclick=sl("itm' . $i . '"); href="javascript:void(0);" >' . "${'itm'.$i}/${'itme'.$i}/${'itms'.$i}" . '</a><br>';
				  $flag = true;
				}
			}
			$cmd .= '<br><br><input type="button" onclick="postCmd(\'gamecmd\',\'command.php\');" value="提交">';
			if (! $flag) {
				$log .='唔？你的包裹里没有可以强化的装备，是不是没有脱下来呢？DA☆ZE<br><br>';
			}else{
				$log .="宝石在你的手上发出异样的光芒，似乎有个奇怪的女声在你耳边说道<span class=\"yellow\">\"我是从天界来的凯丽\"</span>.";
			}				
			return;
		} elseif($itm=='★捆绑式炸药★'){
		global $club,$clubinfo,$hp,$mhp;
		$anla_flag=false;
		if(($club==21)||($club==5)||($club==17)||($club==23)||($club==24)){
			$log .="<span class=\"yellow\">{$clubinfo[$club]}的尊严不允许你成为恐怖分子！</span><br>";
			return;
		}
		if(($club==8)||(rand(0,100)>=25)){
			$log .="<span class=\"yellow\">炸弹顺利的绑在了你的腰间，你从此成为了一名光荣的恐怖分子，可喜可贺，可喜可贺！</span><br>";	
			$club =21;
			$anla_flag=true;
		}else{
			$log .="<span class=\"yellow\">正当你准备贴上胶带时，你粗劣的手法使得炸药忽然爆炸了！</span><br>";
			$kamikaze_damage=ceil($mhp*0.75);
			$hp-=$kamikaze_damage;
			$log .="<span class=\"yellow\">爆炸对你造成了<span class=\"red\">$kamikaze_damage</span>点伤害！</span><br>";
			if($hp<=0){
				$log .="<span class=\"yellow\">你被炸药炸死了！可喜可贺，可喜可贺！</span><br>";
				include_once GAME_ROOT . './include/state.func.php';
				death ( 'failtrapcvt', '', 0, $itm );	
			}
		}
		if($anla_flag){
		global $wep,$wepk,$wepe,$weps,$wepsk;
		$wep='粗劣的炸药';$wepk='WD';$wepe=400;$weps=1;$wepsk='od';
		}
		$itm = $itmk = $itmsk = '';
		$itme = $itms = 0;
		} elseif($itm=='《血染的风采》'){
		global $club,$hp,$mhp;
		if(($club!=97)&&($club!=17)&&($club!=23)&&($club!=10)&&($club!=24)&&($club!=70)){
			$log .="<span class=\"yellow\">在充分领会了书中精神后，你决定为革命事业奉献青春。</span><br>";
			$club =97;
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}elseif($club==97){
			$log .="<span class=\"yellow\">你已经是一名党的优秀战士了。</span><br>";
		}else{
			$log .="<span class=\"yellow\">由于种种原因，你没能领悟书中的精神。</span><br>";
		}
		} elseif($itm=='《时尚周刊》'){
		global $club,$hp,$mhp,$mss,$sktime,$db,$tablepre;
		if(($club!=70)&&($club!=17)&&($club!=23)&&($club!=24)&&($club!=97)){
			$log .="<span class=\"yellow\">此书让你领会到名气与潮流的重要性。为了把大家从这个大逃杀游戏中拯救出来，你决定成为偶像！</span><br>";
			$club =70;
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
			$mss+=50;
			$db->query("UPDATE {$tablepre}users SET sktime=1 WHERE username='$name'" );//刷新技能使用次数
			include_once GAME_ROOT.'./include/news.func.php';
			addnews ( 0, 'becomeidol', $name);
		}elseif($club==70){
			$log .="<span class=\"yellow\">你已经是魅力四射的偶像了。</span><br>";
		}else{
			$log .="<span class=\"yellow\">由于种种原因，你没能领悟书中的精神。</span><br>";
		}
		} elseif($itm=='亚莉丝的神奇药剂'){
		//方便测试的道具，必须灵魂绑定
		$log .="<span class=\"yellow\">喝下了{$itm}。</span><br>";
		global $exp,$mhp,$hp,$msp,$sp,$mss,$ss;
		$exp+=500;
		$hp=$mhp;
		$sp=$msp;
		$ss=$mss;
		} elseif($itm=='哭泣的黑曜石'){
		global $typls,$pls,$name,$tyowner,$wep,$club,$plsinfo;
		if(($wep=='＜厄环＞')&&($club==53)){
			if(($typls==99)&&($tyowner=='')){
				$typls=$pls;$tyowner=$name;
				$log .= "你使用了<span class=\"yellow\">$itm</span>。{$plsinfo[$typls]}现在成为了你的领域！<br>";
				addnews($now,'cty',$tyowner,$plsinfo[$typls],$itm);
				$itm=$itmk=$itmsk='';$itme=$itms=0;
			}else{
				$log .= "你不能设置多个领域！<br>";
			}
		}else{
			$log .= "你还没有足够的能力使用<span class=\"yellow\">$itm</span>！<br>";
		}
		} elseif($itm=='光芒石'){
		global $gemstate,$gempower,$club;
		if(($club==49)||($club==53)){
			$log .= "虚空中传来一道声音：<span class=\"deeppink\">“你已经领悟奥术的实质了，不需要再次激活光芒石了！”</span><br>";
			return;
		}
		if(($gemstate==0)||($gempower<1000)||($club==17)){
			$log .= "虚空中传来一道声音：<span class=\"deeppink\">“你还没有能力接触奥术的实质。”</span><br>";
			return;
		}
		if($gempower==1000){
			$club=49;
			$gempower=0;
			$log .= "你激活了光芒石。<br><span class='orange'>光芒石散发出的光线迅速暗淡下去，石身上深黑色的瞳孔静静的注视着你。<br>你沿着那道缝隙看去，却感到一阵强烈的晕厥感，在你昏迷前的那一瞬间……你看到了……</span><br>";
			$log .= "等你醒来时，你已经领悟了奥术的实质。<br>";
		}
		} elseif($itm=='镶有宝石的盒子'){
		$log .="你打开了盒子。<br>";
		global $cmd;				
		include template('easybuyitem');
		$cmd = ob_get_contents();
		ob_clean();
		return;
		} elseif ($itm == '水果刀') {
			$flag = false;
			
			for($i = 1; $i <= 6; $i ++) {
				global ${'itm' . $i}, ${'itmk' . $i},${'itms' . $i},${'itme' . $i},$wk;
				foreach(Array('香蕉','苹果','西瓜') as $fruit){
					
					if ( strpos ( ${'itm' . $i} , $fruit ) !== false && strpos ( ${'itm' . $i} , '皮' ) === false && (strpos ( ${'itmk' . $i} , 'H' ) === 0 || strpos ( ${'itmk' . $i} , 'P' ) === 0 )) {
						if($wk >= 120){
							$log .= "练过刀就是好啊。你娴熟地削着果皮。<br><span class=\"yellow\">${'itm'.$i}</span>变成了<span class=\"yellow\">★残骸★</span>！<br>咦为什么会出来这种东西？算了还是不要吐槽了。<br>";
							${'itm' . $i} = '★残骸★';
							${'itme' . $i} *= rand(2,4);
							${'itms' . $i} *= rand(3,5);
							$flag = true;
							$wk++;
						}else{
							$log .= "想削皮吃<span class=\"yellow\">${'itm'.$i}</span>，没想到削完发现只剩下一堆果皮……<br>手太笨拙了啊。<br>";
							${'itm' . $i} = str_replace($fruit, $fruit.'皮',${'itm' . $i} );
							${'itmk' . $i} = 'TN';
							${'itms' . $i} *= rand(2,4);
							$flag = true;
							$wk++;
						}
						break;
					}
				}
				if($flag == true) {break;};
			}
			if (! $flag) {
				$log .= '包裹里没有水果。<br>';
			} else {
				$dice = rand(1,5);
				if($dice==1){
					$log .= "<span class=\"red\">$itm</span>变钝了，无法再使用了。<br>";
					$itm = $itmk = $itmsk = '';
					$itme = $itms = 0;
				}
			}
		} elseif($itm=='雄黄酒'){
			global $nick,$name,$nicks,$db,$tablepre;
			$result = $db->query("SELECT nicks FROM {$tablepre}users WHERE username='$name'");
			$nicks = $db->result($result);
			$nicks=str_replace('爱马仕','劫后余生',$nicks);
			$nick='劫后余生';
			$log .= "你一口饮下了<span class=\"yellow\">{$itm}</span>，你感到一股通透浸满全身。
			<br><span class=\"red\">虽然你还是习惯性了舔了一口印有PinkiePie的棒棒糖，但至少你现在不会抱着小马抱枕睡觉并且到处咬人了？【摊手</span><br>";
			$db->query("UPDATE {$tablepre}users SET nicks='$nicks' WHERE username='$name'");
			save_gameinfo();
			$itms--;
		} elseif(strpos($itm,'粽子')!==false){
			global $def,$att,$hp,$mhp,$sp,$msp,$ss,$mss,$money,$exp;
			global $wc,$wk,$wd,$wf,$wg,$wp;
			$log .= "你剥开了<span class=\"yellow\">$itm</span>的皮，里面露出了一张纸条。<br>";
			$dice = rand(1,10);
			 if($dice==1){
				 $log .= "纸条上写着：<span class=\"yellow\">今天虐了八个人，他们都太菜了。——不愿透露姓名的宇宙神触L轩之徒</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $def+=150;
				 $log .= "<span class=\"yellow\">你的防御力提升了150点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
			 }elseif($dice==2){
				 $log .= "纸条上写着：<span class=\"yellow\">抢钱抢粮抢娘们，想跑？你已经没戏唱了，快去死吧！——资深切糕传销员</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $att+=150;
				 $log .= "<span class=\"yellow\">你的攻击力提升了150点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
			 }elseif($dice==3){
				 $log .= "纸条上写着：<span class=\"yellow\">态度改变历史，倒不是说你的声音有多酸爽，只是时代的审美不同了。——不愿透露姓名的自称小老婆的神秘人士</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $mss+=75;
				 $ss=$mss;
				 $log .= "<span class=\"yellow\">你的歌魂提升了75点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
		   	 }elseif($dice==4){
				 $log .= "纸条上写着：<span class=\"yellow\">对对对，直死不仅概率是100%，而且实装了反抹消，你看我像傻逼吗，呵呵。——一位肤色偏黑身材臃肿的不明巨兽</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $mhp+=75;
				 $hp=$mhp;
				 $log .= "<span class=\"yellow\">你的生命上限提升了75点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
		   	 }elseif($dice==5){
				 $log .= "纸条上写着：<span class=\"yellow\">啊呀我是追踪键带触摸板，多练习你也可以，不要想多啦。——不愿透露姓名的喵星人近卫队长</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $msp+=75;
				 $sp=$msp;
				 $log .= "<span class=\"yellow\">你的体力上限提升了75点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
		   	  }elseif($dice==6){
				 $log .= "纸条上写着：<span class=\"yellow\">这个茶叶蛋，就九百七卖给你吧。——不愿透露姓名的十二湾玉</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $money+=970;
				 $log .= "<span class=\"yellow\">你获得了一张面额为970的纸币。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
		   	  }elseif($dice==7){
				 $log .= "纸条上写着：<span class=\"yellow\">上什么学啊反正看看就会了，你们别那么叫啦，我会无视你们的……啊呀真的会无视真的真的~——不愿透露姓名的千金大小姐</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $exp+=75;
				 $log .= "<span class=\"yellow\">你的经验增加了75点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
		   	 }elseif($dice==8){
				 $log .= "<span class=\"yellow\">纸条上写着什么……？卧槽这傻逼玩意儿居然有毒！</span><br>";
				 $hp=0;
				 $log .= "你带着对粽子制作者的怨恨永远的闭上了眼睛。<br>";
				 include_once GAME_ROOT . './include/state.func.php';
				 death ( 'poison', '', 0, $itm );
			}elseif($dice==9){
				 $log .= "<span class=\"yellow\">你们不要哭夭我小剧场了，不就是一周内拿干货吗，给你看，看啊，怎么了，不能吃，他好歹也是饼啊！——不愿透露姓名的身残志坚者</span><br>你感到一阵清明，仿佛领悟了这深奥的禅理<br>";
				 $wc+=25;$wp+=25;$wd+=25;$wg+=25;$wf+=25;$wk+=25;
				 $log .= "<span class=\"yellow\">你的全系熟练增加了25点。</span><br>看完纸条，粽子被你三两口吃完了。<br>";
			}elseif($dice==10){
				 $log .= "<span class=\"yellow\">是啊，你与我无冤无仇，但是我平生最恨傻逼，所以对不起我只能向你开炮了。——不愿透露姓名的咸鱼干</span><br>你感到纸条下面有什么东西<br>";
				 $log .= "你摸到了一小瓶雄黄酒，上面写着【包治疑难杂病】，哦哦，真是来的太及时了！<br>";
				global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
					$itm0 = '雄黄酒';
					$itmk0 = 'Y';
					$itme0 = 1;
					$itms0 = 1;
				include_once GAME_ROOT . './include/game/itemmain.func.php';
				itemget();
			}
			$itms--;
		} elseif(strpos($itm,'RP回复设备')!==false){
			global $rp;
			$rp = 0;
			$log .= "你使用了<span class=\"yellow\">$itm</span>。你的RP归零了。<br>";
		} elseif($itm=='投币式铠甲（未激活）'){
			global $money;
			if($money<5){
				$log .= "没钱不要乱点！<br>";
				return;
			}
			$a_n='投币式铠甲（已激活）';
			$lose_money=rand(5,$money);
			$money-=$lose_money;
			$log .= "你向<span class=\"yellow\">$itm</span>里胡乱丢了{$lose_money}元。<br>";
			$a_e=round($lose_money/5);
			$a_s=$a_e;
			$k=Array('DB','DH','DA','DF');
			$a_k=$k[array_rand($k)];
			if($a_e>=300){
				$ask=Array('U','I','E','W','q');
				$a_sk=$ask[array_rand($ask)];
			}
			if($a_e>=700){
				$Ask=Array('P','K','C','D','G','F');
				$a_sk.=$Ask[array_rand($Ask)];
			}
			if($a_e>=1000){
				$msk=Array('c','m','M','a');
				$a_sk.=$msk[array_rand($msk)];
			}
			if($a_e>=3000){
				$Msk=Array('B','b','A','z');
				$a_sk.=$Msk[array_rand($Msk)];
			}
			if($a_e>=10000){
				$hsk=Array('h','x','H','Z');
				$a_sk.=$hsk[array_rand($hsk)];
			}
			$log .= "<span class=\"yellow\">$itm</span>duang一下变得很油、很亮！<br>";
			$itm = $a_n;
			$itmk = $a_k;
			$itmsk = $a_sk;
			$itme = $a_e;
			$itms = $a_s;
		} else {
			$log .= " <span class=\"yellow\">$itm</span> 该如何使用呢？<br>";
		}
		
		if (($itms <= 0) && ($itm)) {
			$log .= "<span class=\"red\">$itm</span> 用光了。<br>";
			$itm = $itmk = $itmsk = '';
			$itme = $itms = 0;
		}
	} else {
		$log .= "你使用了道具 <span class=\"yellow\">$itm</span> 。<br>但是什么也没有发生。<br>";
	}
	
	include_once GAME_ROOT.'./include/game/achievement.func.php';
	check_item_achievement($name,$i,$ie,$is,$ik,$isk);
		
	$mode = 'command';
	return;
}

?>
