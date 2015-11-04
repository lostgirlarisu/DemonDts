<?php

if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function combat($active = 1, $wep_kind = '',$bsk='') {
	global $log, $mode, $main, $cmd, $battle_title, $db, $tablepre, $pls, $message, $now, $w_log, $nosta, $hdamage, $hplayer;
	global $pid, $name, $club, $inf, $lvl, $exp, $killnum, $bid, $tactic, $pose;
	global $wep, $wepk, $wepe, $weps, $wepsk ,$money;
	global $edata, $w_pid, $w_name, $w_pass, $w_type, $w_endtime,$w_deathtime, $w_gd, $w_sNo, $w_icon, $w_club, $w_hp, $w_mhp, $w_sp, $w_msp, $w_att, $w_def, $w_pls, $w_lvl, $w_exp, $w_money, $w_bid, $w_inf, $w_rage, $w_pose, $w_tactic, $w_killnum, $w_state, $w_wp, $w_wk, $w_wg, $w_wc, $w_wd, $w_wf, $w_teamID, $w_teamPass;
	global $w_wep, $w_wepk, $w_wepe, $w_weps, $w_arb, $w_arbk, $w_arbe, $w_arbs, $w_arh, $w_arhk, $w_arhe, $w_arhs, $w_ara, $w_arak, $w_arae, $w_aras, $w_arf, $w_arfk, $w_arfe, $w_arfs, $w_art, $w_artk, $w_arte, $w_arts, $w_itm0, $w_itmk0, $w_itme0, $w_itms0, $w_itm1, $w_itmk1, $w_itme1, $w_itms1, $w_itm2, $w_itmk2, $w_itme2, $w_itms2, $w_itm3, $w_itmk3, $w_itme3, $w_itms3, $w_itm4, $w_itmk4, $w_itme4, $w_itms4, $w_itm5, $w_itmk5, $w_itme5, $w_itms5,$w_itm6, $w_itmk6, $w_itme6, $w_itms6, $w_wepsk, $w_arbsk, $w_arhsk, $w_arask, $w_arfsk, $w_artsk, $w_itmsk0, $w_itmsk1, $w_itmsk2, $w_itmsk3, $w_itmsk4, $w_itmsk5, $w_itmsk6;
	global $infinfo, $w_combat_inf, $teamID, $teamPass;
	global $rp,$w_rp,$action,$w_action,$achievement,$w_achievement,$skills,$w_skills,$skillpoint,$w_skillpoint;
	global $name, $lvl, $gd, $pid, $pls, $hp,$mhp, $sp, $msp,$rage, $exp, $club, $att, $inf, $message,$w_mhp;
	global $wep, $wepk, $wepe, $weps, $wepsk, $type, $sNo;
	global $wp,$wk,$wc,$wg,$wd,$wf,$skills,$w_skills,$w_club,$skillpoint,$w_skillpoint,$dcloak,$w_sktime,$w_dcloak;
	global $auraa,$aurab,$aurac,$aurad,$w_auraa,$w_aurab,$w_aurac,$w_aurad,$souls,$w_souls,$debuffa,$debuffb,$debuffc,$w_debuffa,$w_debuffb,$w_debuffc;
	global $gemname,$gemstate,$gempower,$gemexp,$gemlvl,$w_gemname,$w_gemstate,$w_gempower,$w_gemexp,$w_gemlvl;
	global $bsk_name; $bsk_name=$bsk;
	global $w_cdowner,$cursedsouls;
	global $mss,$ss,$artk;//[u150910]偶像大师称号用到歌魂
	
	$battle_title = '战斗发生';
	$wt=-1;//天变所对应的天气
	
	if(strpos($bsk,'aurora')===0){
		$wt=ltrim($bsk,'aurora');
	}
	
	if (! $wep_kind) {
		$w1 = substr ( $wepk, 1, 1 );
		$w2 = substr ( $wepk, 2, 1 );
		if ((($w1 == 'G')||($w1=='J')) && ($weps == $nosta)) {
			$wep_kind = $w2 ? $w2 : 'P';
		} else {
			$wep_kind = $w1;
		}
	} elseif (strpos($wepk,$wep_kind)===false && $wep_kind != 'back'){
		$wep_kind = substr ( $wepk, 1, 1 );
	}
	
	$wep_temp = $wep;
	
	if ($active) {
		if ($wep_kind == 'back') {
			$log .= "你逃跑了。";
			$action = '';
			$mode = 'command';
			return;
		}
		$enemyid = $active ? str_replace('enemy','',$action) : $bid;
		if(!$enemyid || strpos($action,'enemy')===false){
			$log .= "<span class=\"yellow\">你没有遇到敌人，或已经离开战场！</span><br>";
			$action = '';
			$mode = 'command';
			return;
		}
		
		$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$enemyid'" );
		if (! $db->num_rows ( $result )) {
			$log .= "对方不存在！<br>";
			$action = '';
			$mode = 'command';
			return;
		}
		
		$edata = $db->fetch_array ( $result );
		
		if ($edata ['pls'] != $pls) {
			$log .= "<span class=\"yellow\">" . $edata ['name'] . "</span>已经离开了<span class=\"yellow\">$plsinfo[$pls]</span>。<br>";
			$action = '';
			$mode = 'command';
			return;
		} elseif ($edata ['hp'] <= 0) {
			global $corpseprotect,$gamestate;
			$log .= "<span class=\"red\">" . $edata ['name'] . "</span>已经死亡，不能被攻击。<br>";
			if($edata['endtime'] < $now -$corpseprotect && $gamestate < 40){
				$action = 'corpse'.$edata['pid'];
				include_once GAME_ROOT . './include/game/battle.func.php';
				findcorpse ( $edata );
			}
			//$action = '';
			return;
		}
		
		if ($message) {
//			foreach ( Array('<','>',';',',') as $value ) {
//				if(strpos($message,$value)!==false){
//					$message = str_replace ( $value, '', $message );
//				}
//			}
			$log .= "<span class=\"lime\">你对{$edata ['name']}大喊：{$message}</span><br>";
			if (! $edata ['type']) {
				$w_log = "<span class=\"lime\">{$name}对你大喊：{$message}</span><br>";
				logsave ( $edata ['pid'], $now, $w_log ,'c');
			}
		}
		
		extract ( $edata, EXTR_PREFIX_ALL, 'w' );
		init_battle ( 1 );
		
		include_once GAME_ROOT . './include/game/attr.func.php';
		
		if(strpos($bsk,'hijack')!==false){
			global $w_art,$w_arts;
			if(!$w_type){
				$log.="你消耗了一个爆炸物，将对方劫持成为了你的人质！<br>";
				$weps--;
				$w_art=$name.'的人质证明';$w_arte=$w_arts=1;$w_artk='A';$w_artsk='Vv';
				addnews($now,'hijack',$name,$w_name,'hijack');
				w_save ( $w_pid );
				return;
			}else{
				$log.="你不能绑架NPC！<br>";
			}	
		}
		
		global $dcloak_crit; $dcloak_crit=0;
		if ($dcloak<$now) $dcloak=0;
		if ($dcloak>0) //踏雪无痕脱隐一击
		{
			addnews ( $now, 'rageskill', $name, $w_name, '__dcloak' );
			$dcloak=0;	
			$dcloak_crit=1;
			$log.="潜伏已久的你向<span class=\"red\">$w_name</span>发起了攻击！<br><span class=\"yellow\">完全没有思想准备的敌方被你的攻击惊呆了，只能任你宰割！</span><br>";
		}
		else  $log .= "你向<span class=\"red\">$w_name</span>发起了攻击！<br>";
		
		if ($bsk!='') 
		{
			$sflag=1;
			if ($bsk=='absorb' && ($w_type!=0 || $w_club==17)) $sflag=0;
			if ($bsk=='net' && $w_type!=0) $sflag=0;
			if ($sflag) addnews ( $now, 'rageskill', $name, $w_name, $bsk );
		}
		
		$att_dmg = attack ( $wep_kind, 1 ,$bsk);
		
		$dcloak_crit=0;	//好了，接下来是敌方的反击，由于代码写得太乱，必须把这个flag重置，不然敌方也必中了……
		
		$w_hp-=$att_dmg;
		if (($bsk=='net')&&(!$w_type)){
			$w_sp=1;
		}
		if (($wt==4)&&(!$w_type)){ //天变暴雨
			$w_sp=1;
		}
		if (($wt==3)&&(!$w_type)){ //天变小雨
			$w_rage=0;
		}

		$bomb_counter=0; $bomber_type=0;
		if (($att_dmg>=$w_hp)&&(strpos($w_arb,'捆绑式炸药')!==false))
		{
			//恐怖份子称号自爆触发
			$bomb_counter=1; $bomber_type=2;
			$w_b_damage = ceil($w_mhp *(1+$w_wd/100));
			$log .= "<br><span class=\"yellow\">你暴风骤雨般的攻击将对方打得毫无还手之力，<br>
				正当你欺身向前，准备了结对方的性命时，对方忽然振臂高呼：</span><br>
				<span class=\"clan\">“安拉胡阿克巴！”</span><br>
				<span class=\"yellow\">你这才发现对方腰间绑着一排明晃晃的炸药，但他已猛地扑了上来。</span><br>
				猛烈的爆炸对你造成了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>";
			$hp -= $w_b_damage;
			$w_arb=$w_arbk=$w_arbsk='';$w_arbe=$w_arbs=0;
			checkdmg ( $w_name, $name, $w_b_damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
			$anla_dice = rand(0,99);
			if($anla_dice>=92)
			{
				addnews ( $now, 'kamikaze_sv', $w_name, $w_type, $name, $type );
				$w_hp=1;
				if ($hp <= 0) 
				{
					$log .= "<br><span class=\"yellow\">你被对方的自爆炸的死得不能再透了！而他则因为对主的虔诚活了下来。</span><br>";
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'anlabomb',$w_name,$w_type );
					if (!$w_type)
					{
						$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"lime\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>敌人被你炸死了，而你则因为对主的虔诚奇迹般地活了下来！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
				else
				{
					$log .= "<br><span class=\"yellow\">你靠着惊人的体质扛过了爆炸，不幸的是对方也因为对主的虔诚活了下来。</span><br>";
					if (!$w_type)
					{
						$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"lime\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>尽管敌人依靠惊人的体格抗过了你的自杀式爆炸袭击，你也因为对主的虔诚奇迹般地活了下来！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
			}
			else
			{
				$w_hp=0;
				if ($hp <= 0) 
				{
					$log .= "<br><span class=\"yellow\">虽然对方也粉身碎骨，你也被对方的自爆炸死了！</span><br>";
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'anlabomb',$w_name,$w_type  );
					if (!$w_type)
					{
						$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。<br>你对敌人造成了<span class=\"red\">{$w_b_damage}</span>点伤害，与敌人同归于尽了！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
				else
				{
					$log .= "<br><span class=\"yellow\">你靠着惊人的体质扛过了爆炸，烟雾散去，你对着<span class=\"yellow\">{$w_name}</span>露出了不屑的笑容。</span><br>";
					if (!$w_type)
					{
						$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。<br>敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害，但敌人依靠惊人的体格抗过了你的自杀式爆炸袭击，你满怀怨念地死去了。</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
			}		
		}
		
		$failed_counter=0;
		if (!$bomb_counter && (($w_hp > 0) && ($w_tactic != 4) && ($w_pose != 5))) {
			global $rangeinfo;
			$w_w1 = substr ( $w_wepk, 1, 1 );
			$w_w2 = substr ( $w_wepk, 2, 1 );
			if ((($w_w1 == 'G')||($w_w1=='J')) && ($w_weps == $nosta)) {
				$w_wep_kind = $w_w2 ? $w_w2 : 'P';
			} else {
				$w_wep_kind = $w_w1;
			}
			
			//射程修正
			$ran=$rangeinfo [$wep_kind];
			$wran=$rangeinfo [$w_wep_kind];
			if (($club==2)&&($lvl>=15)&&($wep_kind=='K')){
				$ran+=5;
			}
			if (($w_club==19)&&($w_lvl>=11))
			{
				$wran=15;
			}
			
			if (($club==3)&&($lvl>=15)&&($wep_kind=='C')){
				$ran=$ran+2;
			}
			if (($w_club==3)&&($w_lvl>=15)&&($w_wep_kind=='C')){
				$wran=$wran+2;
			}
			//[u150925]偶像大师射程修正
			if (($club==70)&&($lvl>=15)&&($artk=='ss')&&($wep_kind=='F')){
					$ran+=4;
			}
			
			//
			
			//if (($rangeinfo [$wep_kind] == $rangeinfo [$w_wep_kind]) || ($rangeinfo [$w_wep_kind] == 'M')) {
			if ($ran <= $wran && $rangeinfo [$wep_kind] !== 0 && $rangeinfo [$w_wep_kind] !== 0) {
				$counter = get_counter ( $w_wep_kind, $w_tactic, $w_club, $w_inf );
				include_once GAME_ROOT.'./include/game/clubskills.func.php';
				$counter *= get_clubskill_bonus_counter($w_club,$w_skills,'w_',$club,$skills,'');
				$counter_dice = rand ( 0, 99 );
				
				if (($w_club==3)&&($w_lvl>=15)){
					$counter_dice=-1;
				}
				if (($w_club==19)&&($w_lvl>=11)) $counter_dice/=1.5;
				
				if ($bsk=='ambush') $counter_dice=1048576;
				if ($bsk=='hunt') $counter_dice=1048576;
				if ($wt==2) $counter_dice=1048576;
				
				if ($counter_dice < $counter) {
					$log .= "<span class=\"red\">{$w_name}的反击！</span><br>";
					
					$log .= npc_chat ( $w_type,$w_name, 'defend' );
		
					$original_hp=$hp;
					
					global $is_second_strike; $is_second_strike=0;
					$def_dmg = defend ( $w_wep_kind );
					
					$w_wep_temp=$w_wep;
					
					if (($def_dmg>=$hp)&&(strpos($arb,'捆绑式炸药')!==false))
					{
						//恐怖份子称号自爆触发
						$bomb_counter=1; $bomber_type=1;
						$b_damage = ceil($mhp *1.25);
						$log .= "<br><span class=\"yellow\">你被对方暴风骤雨般的攻击打得毫无还手之力。<br>
							对方欺身向前，准备了结你的性命。 你不甘就这么死去，振臂高呼到：</span><br>
							<span class=\"clan\">“安拉胡阿克巴！”</span><br>
							<span class=\"yellow\">对方这才发现你腰间绑着一排明晃晃的炸药。你用尽最后的力气拉响了引线，猛地扑了上去。</span><br>
							猛烈的爆炸对<span class=\"yellow\">{$w_name}</span>造成了<span class=\"red\">{$b_damage}</span>点伤害！<br>";
						$w_hp -= $b_damage;
						$arb=$arbk=$arbsk='';$arbe=$arbs=0;
						checkdmg ( $name, $w_name, $b_damage, $type*1000+$sNo, $w_type*1000+$w_sNo );
						$anla_dice = rand(0,99);
						if($anla_dice>=92)
						{
							addnews ( $now, 'kamikaze_sv', $name, $type, $w_name, $w_type );
							$hp=1;
							if ($w_hp <= 0) 
							{
								$log .= "<br><span class=\"yellow\">敌人被你炸死了，而你则因为对主的虔诚奇迹般地活了下来！</span><br>";
								if (!$w_type)
								{
									$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击，对其做出了<span class=\"yellow\">$def_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">对方突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你被炸死了，但对方因为对主的虔诚活了下来！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
							else
							{
								$log .= "<br><span class=\"yellow\">尽管敌人靠着惊人的体质扛过了爆炸，你也因为对主的虔诚奇迹般地活了下来！</span><br>";
								if (!$w_type)
								{
									$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击，对其做出了<span class=\"yellow\">$def_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">对方突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你依靠惊人的体格抗过了敌人的自杀式爆炸袭击，但敌人也因为对主的虔诚活了下来！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
						}
						else
						{
							$hp=0;
							if ($w_hp <= 0) 
							{
								$log .= "<br><span class=\"yellow\">虽然你自己被炸得粉身碎骨，敌人也被你的自爆炸死了！</span><br>";
								include_once GAME_ROOT . './include/state.func.php';
								death ( 'anlabomb',$name,$type );
								if (!$w_type)
								{
									$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击，对其做出了<span class=\"yellow\">$def_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">对方突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ <br>你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！你们同归于尽了！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
							else
							{
								$log .= "<br><span class=\"yellow\">不幸的是，敌人靠着惊人的体质扛过了爆炸，你满怀怨念地死去了。</span><br>";
								include_once GAME_ROOT . './include/state.func.php';
								death ( 'anlabomb',$name,$type);
								if (!$w_type)
								{
									$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">对方突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你抗住了敌人的自爆攻击，烟雾散去，你对着<span class=\"yellow\">{$w_name}</span>露出了不屑的笑容。</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
						}		
					}
					
					include_once GAME_ROOT.'./include/game/clubskills.func.php';
					if ($hp>0 && get_clubskill_bonus_dblhit($w_club,$w_skills))
					{
						$log.="<span class=\"lime\">“你的运气真好，竟然先我一步攻击！但我也不是徒有虚名！”</span><br><span class=\"yellow\">只见{$w_name}以闪电般的速度重整了装备，向你再次发动了反击！</span><br>";
						$log .= npc_chat ( $w_type,$w_name, 'defend' );
						global $is_second_strike; $is_second_strike=1;
						$def_dmg = defend ( $w_wep_kind );
					}
					
				} else {
					
					$log .= npc_chat ( $w_type,$w_name, 'escape' );
					
					$log .= "<span class=\"red\">{$w_name}处于无法反击的状态，逃跑了！</span><br>";
					
					$failed_counter=1;
				}
			} else {
				
				$log .= npc_chat ( $w_type,$w_name, 'cannot' );
				
				if(($w_type==89)&&($w_name=="年兽（？）")){
					global $arealist,$plsinfo;
						$w_pls=array_rand($arealist,1);
						if((strpos($w_pls,0)!==false)||(strpos($w_pls,26)!==false)||(strpos($w_pls,32)!==false)||(strpos($w_pls,34)!==false)){
							$w_pls=1;
						}
						$log .= "<span class=\"red\">年兽被你打的嗷呜一声，向着{$plsinfo[$w_pls]}的方向逃跑了！</span><br>";
						addnews($now,'monsternian',$plsinfo[$w_pls],$w_name,$name);
				}else{
					$log .= "<span class=\"red\">{$w_name}攻击范围不足，不能反击，逃跑了！</span><br>";
				}
				
				$failed_counter=1;
			}
		} elseif($w_hp > 0) {
			$log .= "<span class=\"red\">{$w_name}逃跑了！</span><br>";
			$failed_counter=1;
		}
		include_once GAME_ROOT.'./include/game/clubskills.func.php';
		if ($bsk=='ragestrike') $t2=1; else $t2=0;
		if (!$bomb_counter && $w_hp>0 && $hp>0 && get_clubskill_bonus_dblhit($club,$skills,$t2))
		{
			if ($failed_counter)
				$log.="<span class=\"lime\">“想逃？”</span><br><span class=\"yellow\">你以闪电般的速度重整了装备，向敌人逃跑的背影再次发动了攻击！</span><br>";
			else  $log.="<span class=\"lime\">“这就是你的全部力量么？”</span><br><span class=\"yellow\">你挡住敌人的反击，并以闪电般的速度重整了装备，再次向敌人发动了攻击！</span><br>";
			
			$log .= "你向<span class=\"red\">$w_name</span>再次发起了攻击！<br>";
			$att_dmg = attack ( $wep_kind, 1 );
			if($ggflag){return;}
			$w_hp -= $att_dmg;
		}
	} else {
		$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$bid'" );
		$edata = $db->fetch_array ( $result );
		extract ( $edata, EXTR_PREFIX_ALL, 'w' );
		init_battle ( 1 );
		include_once GAME_ROOT . './include/game/attr.func.php';
		
		$log .= "<span class=\"red\">$w_name</span>突然向你袭来！<br>";
		
		$failed_counter=0;
		$log .= npc_chat ( $w_type,$w_name, 'attack' );
		npc_changewep();
		
		$w_w1 = substr ( $w_wepk, 1, 1 );
		$w_w2 = substr ( $w_wepk, 2, 1 );
		if ((($w_w1 == 'G')||($w_w1=='J')) && ($w_weps == $nosta)) {
			$w_wep_kind = $w_w2 ? $w_w2 : 'P';
		} else {
			$w_wep_kind = $w_w1;
		}
		
		$original_hp=$hp;
		
		global $is_second_strike; $is_second_strike=0;
		$def_dmg = defend ( $w_wep_kind );
		
		$w_wep_temp=$w_wep;
		
		$bomb_counter=0; $bomber_type=0;
		if (($def_dmg>=$hp)&&(strpos($arb,'捆绑式炸药')!==false))
		{
			//恐怖份子称号自爆触发
			$bomb_counter=1; $bomber_type=1;
			$b_damage = ceil($mhp *1.25);
			$log .= "<br><span class=\"yellow\">你被对方暴风骤雨般的攻击打得毫无还手之力。<br>
				对方欺身向前，准备了结你的性命。 你不甘就这么死去，振臂高呼到：</span><br>
				<span class=\"clan\">“安拉胡阿克巴！”</span><br>
				<span class=\"yellow\">对方这才发现你腰间绑着一排明晃晃的炸药。你用尽最后的力气拉响了引线，猛地扑了上去。</span><br>
				猛烈的爆炸对<span class=\"yellow\">{$w_name}</span>造成了<span class=\"red\">{$b_damage}</span>点伤害！<br>";
			$w_hp -= $b_damage;
			$arb=$arbk=$arbsk='';$arbe=$arbs=0;
			checkdmg ( $name, $w_name, $b_damage, $type*1000+$sNo, $w_type*1000+$w_sNo );
			$anla_dice = rand(0,99);
			if($anla_dice>=92)
			{
				addnews ( $now, 'kamikaze_sv', $name, $type, $w_name, $w_type );
				$hp=1;
				if ($w_hp <= 0) 
				{
					$log .= "<br><span class=\"yellow\">敌人被你炸死了，而你则因为对主的虔诚奇迹般地活了下来！</span><br>";
					if (!$w_type)
					{
						$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">对方却突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你被炸死了，但对方因为对主的虔诚活了下来！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
				else
				{
					$log .= "<br><span class=\"yellow\">尽管敌人靠着惊人的体质扛过了爆炸，你也因为对主的虔诚奇迹般地活了下来！</span><br>";
					if (!$w_type)
					{
						$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">对方却突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你依靠惊人的体格抗过了敌人的自杀式爆炸袭击，但敌人也因为对主的虔诚活了下来！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
			}
			else
			{
				$hp=0;
				if ($w_hp <= 0) 
				{
					$log .= "<br><span class=\"yellow\">虽然你自己被炸得粉身碎骨，敌人也被你的自爆炸死了！</span><br>";
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'anlabomb',$name ,$type);
					if (!$w_type)
					{
						$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">对方却突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ <br>你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！你们同归于尽了！</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
				else
				{
					$log .= "<br><span class=\"yellow\">不幸的是，敌人靠着惊人的体质扛过了爆炸，你满怀怨念地死去了。</span><br>";
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'anlabomb',$name,$type);
					if (!$w_type)
					{
						$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击。<br>";
						logsave ( $w_pid, $now, $w_log ,'b');
						$w_log = "<span class=\"yellow\">对方却突然振臂高呼到：<span class=\"clan\">“安拉胡阿克巴！”</span>然后拉响了绑在身上的炸药包，向你扑了过来！ 你受到了<span class=\"yellow\">{$b_damage}</span>点自爆伤害！<br>你抗住了敌人的自爆攻击，烟雾散去，你对着<span class=\"yellow\">{$w_name}</span>露出了不屑的笑容。</span><br>";
						logsave ( $w_pid, $now, $w_log ,'b');
					}
				}
			}		
		}
				
		if (!$bomb_counter && (($hp > 0) && ($tactic != 4) && ($pose != 5))) {
			global $rangeinfo;
			
			//射程修正
			$ran=$rangeinfo [$wep_kind];
			$wran=$rangeinfo [$w_wep_kind];
			if (($club==19)&&($lvl>=11))
			{
				$ran=15;
			}
			if (($w_club==2)&&($w_lvl>=15)&&($w_wep_kind=='K')){
				$wran+=5;
			}
			if (($club==3)&&($lvl>=15)&&($wep_kind=='C')){
				$ran=$ran+2;
			}
			if (($w_club==3)&&($w_lvl>=15)&&($w_wep_kind=='C')){
				$wran=$wran+2;
			}
			//[u150925]偶像大师射程修正
			if (($club==70)&&($lvl>=15)&&($artk=='ss')&&($wep_kind=='F')){
					$ran+=4;
			}
			//
			
			if ($ran >= $wran && $rangeinfo [$w_wep_kind] !== 0 && $rangeinfo [$wep_kind] !== 0) {
				$counter = get_counter ( $wep_kind, $tactic, $club, $inf );
				include_once GAME_ROOT.'./include/game/clubskills.func.php';
				$counter *= get_clubskill_bonus_counter($club,$skills,'',$w_club,$w_skills,'w_');
				$counter_dice = rand ( 0, 99 );
				if (($club==3)&&($lvl>=15)){
					$counter_dice=-1;
				}	
				if (($club==19)&&($lvl>=11)) $counter_dice/=1.5;
				
				if ($counter_dice < $counter) {
					$log .= "<span class=\"red\">你的反击！</span><br>";
					$wep_kind = substr ( $wepk, 1, 1 );
					
					include_once GAME_ROOT . './include/game/attr.func.php';
		
					$log .= "你向<span class=\"red\">$w_name</span>发起了攻击！<br>";
					$att_dmg = attack ( $wep_kind );
					$w_hp-=$att_dmg;
			
					if (($att_dmg>=$w_hp)&&(strpos($w_arb,'捆绑式炸药')!==false))
					{
						//恐怖份子称号自爆触发
						$bomb_counter=1; $bomber_type=2;
						$w_b_damage = ceil($w_mhp *1.25);
						$log .= "<br><span class=\"yellow\">你暴风骤雨般的反击将对方打得毫无还手之力，<br>
							正当你欺身向前，准备了结对方的性命时，对方忽然振臂高呼：</span><br>
							<span class=\"clan\">“安拉胡阿克巴！”</span><br>
							<span class=\"yellow\">你这才发现对方腰间绑着一排明晃晃的炸药，但他已猛地扑了上来。</span><br>
							猛烈的爆炸对你造成了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>";
						$hp -= $w_b_damage;
						$w_arb=$w_arbk=$w_arbsk='';$w_arbe=$w_arbs=0;
						checkdmg ( $w_name, $name, $w_b_damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
						$anla_dice = rand(0,99);
						if($anla_dice>=92)
						{
							$w_hp=1;
							addnews ( $now, 'kamikaze_sv', $w_name, $w_type, $name, $type );
							if ($hp <= 0) 
							{
								$log .= "<br><span class=\"yellow\">你被对方的自爆炸的死得不能再透了！而他则因为对主的虔诚活了下来。</span><br>";
								include_once GAME_ROOT . './include/state.func.php';
								death ( 'anlabomb',$w_name,$w_type );
								if (!$w_type)
								{
									$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击，受到其<span class=\"yellow\">$att_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"lime\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>敌人被你炸死了，而你则因为对主的虔诚奇迹般地活了下来！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
							else
							{
								$log .= "<br><span class=\"yellow\">你靠着惊人的体质扛过了爆炸，不幸的是对方也因为对主的虔诚活了下来。</span><br>";
								if (!$w_type)
								{
									$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击，受到其<span class=\"yellow\">$att_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"lime\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害！<br>尽管敌人依靠惊人的体格抗过了你的自杀式爆炸袭击，你也因为对主的虔诚奇迹般地活了下来！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
						}
						else
						{
							$w_hp=0;
							if ($hp <= 0) 
							{
								$log .= "<br><span class=\"yellow\">虽然对方也粉身碎骨，你也被对方的自爆炸死了！</span><br>";
								include_once GAME_ROOT . './include/state.func.php';
								death ( 'anlabomb',$w_name ,$w_type );
								if (!$w_type)
								{
									$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击，受到其<span class=\"yellow\">$att_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。<br>你对敌人造成了<span class=\"red\">{$w_b_damage}</span>点伤害，与敌人同归于尽了！</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
							else
							{
								$log .= "<br><span class=\"yellow\">你靠着惊人的体质扛过了爆炸，烟雾散去，你对着<span class=\"yellow\">{$w_name}</span>露出了不屑的笑容。</span><br>";
								if (!$w_type)
								{
									$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击，受到其<span class=\"yellow\">$att_dmg</span>点反击。<br>";
									logsave ( $w_pid, $now, $w_log ,'b');
									$w_log = "<span class=\"yellow\">你用尽仅存的力气，高声喊道：<span class=\"clan\">“安拉胡阿克巴！”</span>，然后拉响了绑在身上的炸药包，向敌人扑了过去。<br>敌人受到了<span class=\"red\">{$w_b_damage}</span>点伤害，但敌人依靠惊人的体格抗过了你的自杀式爆炸袭击，你满怀怨念地死去了。</span><br>";
									logsave ( $w_pid, $now, $w_log ,'b');
								}
							}
						}		
					}
					
					include_once GAME_ROOT.'./include/game/clubskills.func.php';
					if ($w_hp>0 && $hp>0 && get_clubskill_bonus_dblhit($club,$skills))
					{
						$log.="<span class=\"lime\">“你的运气真好，竟然先我一步攻击！但我也不是徒有虚名！”</span><br><span class=\"yellow\">你以闪电般的速度重整了装备，向敌人再次发动了反击！</span><br>";
						
						$log .= "你向<span class=\"red\">$w_name</span>再次发起了反击！<br>";
						$att_dmg = attack ( $wep_kind);
						if($ggflag){return;}
						$w_hp -= $att_dmg;
					}
				} else {
					$log .= "<span class=\"red\">你处于无法反击的状态，逃跑了！</span><br>";
					$failed_counter=1;
				}
			} else {
				$log .= "<span class=\"red\">你攻击范围不足，不能反击，逃跑了！</span><br>";
				$failed_counter=1;
			}
		} elseif($hp > 0) {
			$log .= "<span class=\"red\">你逃跑了！</span><br>";
			$failed_counter=1;
		}
		
		include_once GAME_ROOT.'./include/game/clubskills.func.php';
		if ($w_hp>0 && $hp>0 && get_clubskill_bonus_dblhit($w_club,$w_skills))
		{
			if ($failed_counter)
				$log.="<span class=\"lime\">“想逃？”</span><br><span class=\"yellow\">只见{$w_name}以闪电般的速度重整了装备，向正在逃跑的你再次发动了攻击！</span><br>";
			else  $log.="<span class=\"lime\">“这就是你的全部力量么？”</span><br><span class=\"yellow\">只见{$w_name}挡住了你的反击，并以闪电般的速度重整了装备，向你再次发动了攻击！</span><br>";
			
			$log .= "<span class=\"red\">$w_name</span>向你再次发起了攻击！<br>";
			$log .= npc_chat ( $w_type,$w_name, 'attack' );
			npc_changewep();
			global $npc_bsk;
			$npc_bsk = npc_useskill(1);
		
			$w_w1 = substr ( $w_wepk, 1, 1 );
			$w_w2 = substr ( $w_wepk, 2, 1 );
			if ((($w_w1 == 'G')||($w_w1=='J')) && ($w_weps == $nosta)) {
				$w_wep_kind = $w_w2 ? $w_w2 : 'P';
			} else {
				$w_wep_kind = $w_w1;
			}
			
			global $is_second_strike; $is_second_strike=1;
			$def_dmg = defend ( $w_wep_kind, 1 );
		}
	}

	if ($hp<0) $hp=0;
	
	if (($hp==0)&&($w_club==97)&&($w_wepk=='WG')) {
		$w_wepe=$w_wepe+15;
	}
	
	if (($hp==0)&&($w_club==25)) {
		if ($w_hp<($w_mhp+100)) $w_hp=$w_mhp+100;
	}
	
	if($hp == 0 && !$w_action){$w_action = 'pacorpse'.$pid;}
	w_save ( $w_pid );
	$att_dmg = $att_dmg ? $att_dmg : 0;
	$def_dmg = $def_dmg ? $def_dmg : 0;
	
	if (! $w_type && !$bomb_counter) {
		$w_inf_log = '';
		if ($w_combat_inf) {
			global $exdmginf;
			foreach ( $exdmginf as $inf_ky => $w_inf_words ) {
				if (strpos ( $w_combat_inf, $inf_ky ) !== false) {
					$w_inf_log .= "敌人的攻击造成你{$w_inf_words}了！<br>";
				}
			}

		}
		if($active){
			$w_log = "手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>向你袭击！<br>你受到其<span class=\"yellow\">$att_dmg</span>点攻击，对其做出了<span class=\"yellow\">$def_dmg</span>点反击。<br>$w_inf_log";
		}else{
			$w_log = "你发现了手持<span class=\"red\">$wep_temp</span>的<span class=\"yellow\">$name</span>并且先发制人！<br>你对其做出<span class=\"yellow\">$def_dmg</span>点攻击，受到其<span class=\"yellow\">$att_dmg</span>点反击。<br>$w_inf_log";
		}
		if($hp == 0){
			$w_log .= "<span class=\"yellow\">$name</span><span class=\"red\">被你杀死了！</span><br>";
			//include_once GAME_ROOT.'./include/game/achievement.func.php';
			//check_battle_achievement($w_achievement,$w_type,$name);
		}
		
		logsave ( $w_pid, $now, $w_log ,'b');
	}
	
	//if ($w_type==21){
	//	$dm=$def_dmg;
	//	if ($dm<100) {$dm=100;}
	//	if ($dm>=$mhp) {$dm=$mhp-1;}
	//	$mhp=$mhp-$dm;
	//	if ($hp>$mhp) {$hp=$mhp;}
	//}
	
	if (($att_dmg > $hdamage) && ($att_dmg >= $def_dmg)) {
		$hdamage = $att_dmg;
		$hplayer = $name;
		save_combatinfo ();
	} elseif (($def_dmg > $hdamage) && (! $w_type)) {
		$hdamage = $def_dmg;
		$hplayer = $w_name;
		save_combatinfo ();
	}
	
	//$bid = $w_pid;
	
	if ($w_hp <= 0 && ($w_club != 99 || $bomb_counter)) {
		
		$w_bid = $pid;
		
		if (($w_type)&&($bsk=='teach')) { //谈笑风生
			addnews($now , 'teach',$w_name, $name);
			//$w_mhp=round($w_mhp*1.3)+1;
			$w_hp=$w_mhp;$w_sp=$w_msp;
			$tp=$w_wp+200;$tk=$w_wk+200;$tc=$w_wc+200;$tg=$w_wg+200;$td=$w_wd+200;$tf=$w_wf+200;
			$tm=$w_mhp;$ts=$w_msp;$ta=$name."语录";
			$tn=$w_artsk;if (strpos($tn,'H')===false) $tn.='H';
			$money+=$w_money;$w_money=0;
			$modi=1;if ($lvl>=19) {$modi=3;}
			$log .= '<span class="yellow">'.$w_name."被你的姿势水平折服了，成为了你的粉丝！</span><br>";
			$db->query( "UPDATE {$tablepre}players SET state='0',wp='$tp',wc='$tc',wd='$td',wk='$tk',wf='$tf',wg='$tg',hp='$tm',mhp='$tm',sp='$ts',art='$ta',arts='1',arte='$modi',artk='A',artsk='$tn',money='0' WHERE pid = '$w_pid'" );
			$db->query("UPDATE {$tablepre}players SET action='' WHERE action = 'enemy$w_pid'");
			$main = 'battle';
			init_battle ( 1 );
			if (CURSCRIPT !== 'botservice'){
				include template('battleresult');
				$cmd = ob_get_contents();
				ob_clean();
			}
			$action = '';
			return;
		}
		
		$w_hp = 0;
		if ($w_type==0){$killnum ++;};
		$souls++;
		
		global $now,$aurac,$exp,$w_lvl, $lvl;
		if ($w_type==0 && $now<=$aurac) 
		{
			$exp+=$w_lvl*5;
			exprgup ( $lvl, $lvl, $exp, 1, $rage , 1 );
		}
		if (($club==97)&&($wepk=='WG')){
			if (!$w_type){
				$wepe=$wepe+15;
			}else{
				$wepe=$wepe+7;
			}
		}
		if ($club==25){
			if ($hp<$mhp+100) $hp=$mhp+100;
		}
		include_once GAME_ROOT . './include/state.func.php';
		if ($bomb_counter)
			if ($bomber_type==1)
				$killmsg = kill ( 'anlabomb', $w_name, $w_type, $w_pid, $name );
			else  $killmsg = kill ( 'anlabomb', $w_name, $w_type, $w_pid, $w_name );
		else  $killmsg = kill ( $wep_kind, $w_name, $w_type, $w_pid, $wep_temp );
		if ($w_type) $log .= npc_chat ( $w_type,$w_name, 'death' );
		
		include_once GAME_ROOT.'./include/game/achievement.func.php';
		check_battle_achievement($name,$w_type,$w_name,$wep_temp);
			
		$log .= "<span class=\"red\">{$w_name}被你杀死了！</span><br>";
		

		
		if (($w_type==32)&&($w_name=='sillycross')){
			$log .= "<span class=\"lime\">隐藏在一旁的高达突然使用了凸眼鱼，SC身上逆天的防具都被吸走了！</span><br>";
		}
		
		if (($w_club!==99)&&($wep=='＜夜母＞')&&($club==53)&&($w_name!=='夜种')){
				$log .="<span class='gem'>「诅咒」被触发了！…</span><br><span class='grey'>罪恶者的灵魂将被夜种所奴役，直至它面临第二次死亡……</span><br>";
		}
		
		if(($w_type==89)&&($w_name=="年兽（？）")){
		global $state;
			if ($gametype==2){
			$log .= "<span class=\"yellow\">年兽的尸体抽搐了一下，但什么也没发生！</span><br>";
			}else{
			$state = 6;
			include_once GAME_ROOT . './include/system.func.php';
			gameover ( $now, 'end9', $name );
			}
		}
		
		if(($club==49)||($club==53)){
		global $gemstate,$w_gemstate,$gempower,$w_gempower;
			if($gempower<3000){
			$gempower=min(3000,$gempower+200);$getgem=200;
				if($w_gempower>0){
				$getgem=200+$w_gempower;
				$gempower=min(3000,$gempower+$w_gempower);$w_gempower=0;
				}
			$log .= "<span class=\"red\">【汲取】使你获得了{$getgem}点GEM！</span><br>";
			}
		}
		
		//$rp = $rp + 20 ;
		if(!$w_type){$rpup = $w_rp;}
		else{$rpup = 20;}		
		if($club == 28){
			$rpdec = 30;
			$rpdec += get_clubskill_rp_dec($club,$skills);
			$rp += round($rpup*(100-$rpdec)/100);
		}		
		else{
			$rp += $rpup;
		}
		
		if($killmsg){$log .= "<span class=\"yellow\">你对{$w_name}说：“{$killmsg}”</span><br>";}
		include_once GAME_ROOT . './include/game/battle.func.php';
		$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$w_pid'" );
		$cdata = $db->fetch_array ( $result );
		$action = 'corpse'.$edata['pid'];
		findcorpse ( $cdata );
		return;
	} else {
		if($w_hp <= 0){//有第二阶段
			if ($w_type) 
			{
				$log .= npc_chat ( $w_type,$w_name, 'death' );
				include_once GAME_ROOT . './include/system.func.php';
				$npcdata = evonpc ($w_type,$w_name);
				$log .= '<span class="yellow">'.$w_name.'却没死去，反而爆发出真正的实力！</span><br>';
				if($npcdata){
					addnews($now , 'evonpc',$w_name, $npcdata['name'], $name);
					foreach($npcdata as $key => $val){
						${'w_'.$key} = $val;
					}
					$db->query("UPDATE {$tablepre}players SET action='' WHERE action = 'enemy$w_pid'");
				}
			}
			else
			{
				include_once GAME_ROOT . './include/state.func.php';
				$killmsg = kill ( $wep_kind, $w_name, $w_type, $w_pid, $wep_temp );
				$log .= '<span class="yellow">'.$w_name.'由于其及时按了BOMB键而原地满血复活了！</span><br>';
			}	
		}
		$main = 'battle';
		init_battle ( 1 );
		
		if (CURSCRIPT !== 'botservice')
		{
			include template('battleresult');
			//$cmd = '<br><br><input type="hidden" name="mode" value="command"><input type="radio" name="command" id="back" value="back" checked><a onclick=sl("back"); href="javascript:void(0);" >确定</a><br>';
			$cmd = ob_get_contents();
			ob_clean();
			//$bid = $hp <= 0 ? $bid : 0;
		}
		$action = '';
		return;
	}
}

function attack($wep_kind = 'N', $active = 0,$bsk) {
	global $now, $nosta, $log, $infobbs, $infinfo, $attinfo, $skillinfo,  $wepimprate,$specialrate;
	global $name, $lvl, $gd, $pid, $pls, $hp, $sp, $rage, $exp, $club, $att, $def,$inf, $message,$w_mhp,$mhp,$w_msp,$w_sp;
	global $wep, $wepk, $wepe, $weps, $wepsk, $type, $sNo;
	global $w_wep, $w_wepk, $w_wepe, $w_weps, $w_itm0, $w_itmk0, $w_itme0, $w_itms0, $w_itm1, $w_itmk1, $w_itme1, $w_itms1, $w_itm2, $w_itmk2, $w_itme2, $w_itms2, $w_itm3, $w_itmk3, $w_itme3, $w_itms3, $w_itm4, $w_itmk4, $w_itme4, $w_itms4, $w_itm5, $w_itmk5, $w_itme5, $w_itms5,$w_itm6, $w_itmk6, $w_itme6, $w_itms6, $w_wepsk, $w_itmsk0, $w_itmsk1, $w_itmsk2, $w_itmsk3, $w_itmsk4, $w_itmsk5, $w_itmsk6;
	global $w_arb, $w_arh, $w_ara, $w_arf;
	global $w_arbe, $w_arbsk, $w_arhe, $w_arae, $w_arfe,$w_arte;
	global $w_arbk, $w_arhk, $w_arak, $w_arfk;
	global $artk, $arhsk, $arbsk, $arask, $arfsk, $artsk;
	global $w_hp, $w_rage, $w_lvl, $w_pid, $w_gd, $w_name, $w_type, $w_sNo, $w_inf, $w_def;
	global $w_arhsk, $w_arask, $w_arfsk, $w_artsk, $w_artk;
	global $w_art,$w_artk,$w_arte,$w_arts,$w_artsk;//解决GE800版灵魂绑定BUG
	global $w_arhs, $w_aras, $w_arfs, $w_arbs;
	global $wp,$wk,$wc,$wg,$wd,$wf,$skills,$w_skills,$w_club,$skillpoint,$w_skillpoint;
	global $db,$tablepre;
	global $wepexp,$money;
	global $w_wp,$w_wk,$w_wc,$w_wg,$w_wd,$w_wf;
	global $souls,$w_souls,$debuffa,$debuffb,$debuffc,$w_debuffa,$w_debuffb,$w_debuffc;
	global $gemname,$gemstate,$gempower,$gemexp,$gemlvl,$w_gemname,$w_gemstate,$w_gempower,$w_gemexp,$w_gemlvl,$rp,$w_rp;
	global $w_cdowner,$cursedsouls;
	
	$wt=-1;
	
	if(strpos($bsk,'aurora')!==false){
		$wt=ltrim($bsk,'aurora');
	}
	
	//攻击时增加exp
	
	if ($wep=='飞龙刀【双炎】'){
		$wepexp++;
	}
	
	if(($wep=='无后座力迷你加特林')&&(!$type)){
		//玩家不能正常使用加特林
		$log.="也许是前一位拥有者过于粗暴的使用，导致现在{$wep}没有发挥出应有的性能。<br>";
	}
	
	$assper=strlen($w_inf)*50+150;
	if (strpos($w_inf,'P')!==false) {$assper+=50;}
	$kiriper=strlen($w_inf)*40+120;
	
	$hasi=0;$w_hasi=0;
	if ($club==26){
		$haa=$name.'语录';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$haa' AND type>0 AND pls='$pls' AND hp>0");
		$hasi = $db->num_rows($result);
	}
	if ($w_club==26){
		$haw=$w_name.'语录';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$haw' AND type>0 AND pls='$pls' AND hp>0");
		$w_hasi = $db->num_rows($result);
	}
	
	
	//npc_changewep();
	$is_wpg = false;
	$watt=-1;
	if (((strpos ( $wepk, 'G' ) == 1)||(strpos($wepk,'J')==1)) && ($weps == $nosta)) {
		if (($wep_kind == 'G') || ($wep_kind == 'P')||($wep_kind=='J')) {
			$wep_kind = 'P';
			$is_wpg = true;
			$watt = round ( $wepe / 5 );
		} else {
			$watt = $wepe*2;
		}
	}
	
	
	$log .= "使用{$wep}<span class=\"yellow\">$attinfo[$wep_kind]</span>{$w_name}！<br>";
	
	$att_key = getatkkey ( $wepsk, $arhsk, $arbsk, $arask, $arfsk, $artsk, $artk, $is_wpg );
	
	if(strpos($att_key,'R')!==false){//随机伤害无视一切伤害计算
		$maxdmg = $w_mhp > $wepe ? $wepe : $w_mhp;
		$damage = rand(1,$maxdmg);
		$log .= "武器随机造成了<span class=\"red\">$damage</span>点伤害！<br>";
		return $damage;
	}
	
	if(($w_type==30)&&($w_name=="霜火协奏曲")&&(substr($wepk,0,2)!=$w_wepk)){
		$log .= "<span class=\"red\">对各种武器都精通无比的霜火轻松的抵挡了你的攻击！</span><br>";
		return 0;
	}
	
	if(($w_type==31)&&($w_name=="飞雪大大")&&($wepe<=$w_arte)){
		$log .= "<span class=\"red\">你必须使用更好的武器才能对大魔王造成伤害！</span><br>";
		return 0;
	}
	
	if(($w_type==89)&&($w_name=="年兽（？）")){
		$damage=100;
		$log .= "但是只造成了<span class=\"red\">$damage</span>点伤害！<br>";
		checkdmg ( $name, $w_name, $damage );
		return $damage;
	}
	
	if((strpos($att_key,'x')!==false)&&(($w_type<=20)||($w_type==32)&&($w_name!='sillycross'))&&($wep=='【余晖】')){
		$log.="<img src=\"img/other/afterglow.png\"><br>";
		$damage=8000;
		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		$log .= "你的<span class=\"red\">$wep</span>用光了！<br>";
		$wep = '拳头';
		$wepsk = '';
		$wepk = 'WN';
		$wepe = 0;
		$weps = '∞';
		checkdmg ( $name, $w_name, $damage );
		return $damage;
	}
	
	$magic_gemwep_dice=rand(1,100);
	if(($magic_gemwep_dice<=1)&&($club==53)&&($wep=='＜棘枪＞')){
		$damage=$w_mhp;
		$log .= "<span class=\"gem\">「即死」被触发了，棘枪在空中划出一道流光！</span><br>";
		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		addnews($now,'gem_wep_magic',$name,'＜棘枪＞',$w_name);
		checkdmg ( $name, $w_name, $damage );
		return $damage;
	}
	
	if(($w_type==32)&&($w_name=="別忘了我")){
		$log .= "<span class=\"red\">你的攻击对别忘完全无法造成伤害。</span><br>";
		return 0;
	}
	
if (($w_name=='米可')&&($w_type)){
	//好像哪里不对，attack装子弹代码先封了
	/*
	if(($w_wep=='无后座力迷你加特林')&&(($w_weps==0)||($w_weps==$nosta))){
		//被玩家先攻时也能装子弹，但是NPC主动进攻后被反击时不能
		$log.="<span class=\"linen\">“这个要装子弹真是麻烦呢～待会就给你们好看喔～”</span><br>";
		$w_weps=100;
	}
	*/
	if(($w_art=='白诘草御社神的加护')&&($w_arte>0)){
		$log .= "<span class=\"red\">米可的身姿被突然展现的彩光包围起来了！</span><br>";
		$sense_dice=$w_arte%4;
		$rand_dice=rand(1,100);
		if($bsk!=''){
			$log .= "<span class=\"red\">由于你发动了怒气技能的关系，加护在米可身上彩光的形态似乎有所变化。</span><br>";
			$w_arte--;
		}elseif($rand_dice<=20){
			$log .= "<span class=\"red\">毫无预兆地，加护在米可身上彩光的形态似乎有所变化。</span><br>";
			$w_arte--;
		}
		switch($sense_dice){
			case 0:
				$log .= "<span class=\"red\">在彩光的保护下，你的攻击对米可完全无法造成伤害。</span><br>";
				return 0;
				break;
			case 1:
				$log .= "<span class=\"red\">加护在米可身上的彩光变得稀薄了。</span><br>";
				break;
			case 2:
				if(($wep_kind=='N')||($wep_kind=='P')||($wep_kind=='K')||($wep_kind=='D')){
					$log .= "<span class=\"red\">米可在彩光的保护下，遭受到的近距离攻击被完全阻挡了。</span><br>";
					return 0;
				}else{
					$log .= "<span class=\"red\">然而彩光并没有阻挡住你的攻击。</span><br>";
				}
				break;
			case 3:
				if(($wep_kind=='G')||($wep_kind=='C')||($wep_kind=='F')||($wep_kind=='J')){
					$log .= "<span class=\"red\">米可在彩光的保护下，遭受到的远距离攻击被完全阻挡了。</span><br>";
					return 0;
				}else{
					$log .= "<span class=\"red\">然而彩光并没有阻挡住你的攻击。</span><br>";
				}
				break;
			default:
		}
	}
}
	
	if ($wt==0){ //天变晴天
		$att_key=str_replace('f','',$att_key);
		$att_key=$att_key.'f';
		$log.='天变为你的武器附加了<span class="red">灼焰</span>属性。<br>';
	}
	
	if ($wt==7){ //天变下雪
		$att_key=str_replace('k','',$att_key);
		$att_key=$att_key.'k';
		$log.='天变为你的武器附加了<span class="red">冰华</span>属性。<br>';
	}
	
	//附魔
	if ($bsk=='enchant'){
		$nf=rand(1,5);
		if ($nf==1){
			$att_key=str_replace('u','',$att_key);
			$att_key=$att_key.'u';
			$log.='附魔为你的武器附加了<span class="red">火焰</span>属性。<br>';
		}elseif ($nf==2){
			$att_key=str_replace('i','',$att_key);
			$att_key=$att_key.'i';
			$log.='附魔为你的武器附加了<span class="clan">冻气</span>属性。<br>';
		}elseif ($nf==3){
			$att_key=str_replace('e','',$att_key);
			$att_key=$att_key.'e';
			$log.='附魔为你的武器附加了<span class="yellow">电击</span>属性。<br>';
		}elseif ($nf==4){
			$att_key=str_replace('w','',$att_key);
			$att_key=$att_key.'w';
			$log.='附魔为你的武器附加了<span class="grey">音波</span>属性。<br>';
		}elseif ($nf==5){
			$att_key=str_replace('p','',$att_key);
			$att_key=$att_key.'p';
			$log.='附魔为你的武器附加了<span class="purple">带毒</span>属性。<br>';
		}
	}
	
	if (($club==23)&&($lvl>=15)&&(rand(1,100)<=30)){
		$att_key=$att_key.'r';
	}
	
	$surflag=false;
	if ($bsk=='suppress'){
		$supdamage=round($mhp*0.15);
		if ($hp>$supdamage){
			$hp=$hp-$supdamage;
			$surflag=true;
			$log.='<span class="red">你鲁莽的冲向敌人试图将其压制住！</span><br>';
		}else{
			$log.='<span class="red">由于生命值不足，压制技能没有发动！</span><br>';
		}
	}
	
	
	//
	
	$w_def_key = getdefkey ( $w_wepsk, $w_arhsk, $w_arbsk, $w_arask, $w_arfsk, $w_artsk, $w_artk,$bsk );
	$mdr = $skdr = $sldr = false;
	if(strpos($att_key.$w_def_key,'-')!==false){$mdr = true;}//精抽
	if(strpos($att_key.$w_def_key,'*')!==false){$sldr = true;}//魂抽
	if(strpos($att_key.$w_def_key,'+')!==false){$skdr = true;}//技抽
	if (($w_wep=='Solidarity')||($w_wep=='M240通用机枪')) {$mdr = $skdr = $sldr = false;}
	if($mdr || $sldr || $skdr){
		list($wsk,$hsk,$bbsk,$ask,$fsk,$tsk,$tk)=Array($wepsk, $arhsk, $arbsk, $arask, $arfsk, $artsk, $artk);
		list($wwsk,$whsk,$wbsk,$wask,$wfsk,$wtsk,$wtk)=Array( $w_wepsk, $w_arhsk, $w_arbsk, $w_arask, $w_arfsk, $w_artsk, $w_artk);
		if($mdr){
			$log .= "<span class=\"yellow\">精神抽取使双方的防具属性全部失效！</span><br>";
			$hsk = $bbsk = $ask = $fsk = $whsk = $wbsk = $wask = $wfsk = '';
		}
		if($sldr){
			$log .= "<span class=\"yellow\">灵魂抽取使双方的武器和饰物属性全部失效！</span><br>";
			$wsk = $tsk = $tk = $wwsk = $wtsk = $wtk = '';
		}
		if($skdr){
			$log .= "<span class=\"yellow\">技能抽取使双方的武器熟练度在战斗中大幅下降！</span><br>";
			//$bsk = $ask = $fsk = $wbsk = $wask = $wfsk = '';
		}
		$att_key = getatkkey ( $wsk,$hsk,$bbsk,$ask,$fsk,$tsk,$tk, $is_wpg );
		$w_def_key = getdefkey ( $wwsk,$whsk,$wbsk,$wask,$wfsk,$wtsk,$wtk,$bbsk );
	}

	
	//判定直死
	
	/*if (($bsk=='analysis')&&(!$w_type)){
		$damage=floor($w_wepe/3)+1;
		$log .= "造成<span class=\"clan\">$damage</span>点伤害！<br>";
		$rage=$rage-90;
		if ($rage<0) $rage=0;
		checkdmg ( $name, $w_name, $damage );
		return $damage;
	}*/
	
	if (($wt==9)&&(!$w_type)){ //天变浓雾
		$damage=floor(($w_mhp-$w_hp)*1.3)+1;
		$log .= "天变造成了<span class=\"clan\">$damage</span>点伤害！<br>";
		$rage=$rage-30;
		if ($rage<0) $rage=0;
		checkdmg ( $name, $w_name, $damage );
		return $damage;
	}
	
	
	
	if(strpos($att_key,'X')!==false){
		global $ggflag,$teamID;
		$ggflag = false;
		$ddice = rand(0,99);
		if($ddice <=14){
			$log .= "<span class=\"red\">你手中的武器忽然失去了控制，喀吧一声就斩断了什么。你发现那似乎是你的死线。</span><br>";
			include_once GAME_ROOT . './include/state.func.php';
			$death('gg','','',$wep);
			$ggflag = true;
			return 0;
		}
	}
		
	//attack函数是玩家打npc专用，在这里加npc内容是没用的
	
//	if ((strpos($att_key,"X")!==false)&&($type)&&(!$w_type)&&(rand(1,5)>3)){  
//		if ($wep=='燕返262'){
//			$log.="<img src=\"img/other/262.png\"><br>";
//		}
//		$damage=$w_mhp;
//		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
//		checkdmg ( $name, $w_name, $damage );
//		return $damage;
//	}
	
	global ${$skillinfo [$wep_kind]};
	$add_skill = & ${$skillinfo [$wep_kind]};
	if ($club==18){
		if ($lvl<19){
			$wep_skill=round(${$skillinfo [$wep_kind]}*0.7+($wp+$wk+$wc+$wg+$wd+$wf)*0.3);
		}else{
			$wep_skill=round(${$skillinfo [$wep_kind]}*0.5+($wp+$wk+$wc+$wg+$wd+$wf)*0.5);
		}
	}elseif(($club==11)&&($lvl>=19)){
		$wep_skill=$wp;
		if ($wk>$wep_skill) $wep_skill=$wk;
		if ($wc>$wep_skill) $wep_skill=$wc;
		if ($wg>$wep_skill) $wep_skill=$wg;
		if ($wd>$wep_skill) $wep_skill=$wd;
		if ($wf>$wep_skill) $wep_skill=$wf;
	}else{
		$wep_skill=${$skillinfo [$wep_kind]};
	}
	if($skdr){
		$wep_skill=sqrt($wep_skill);
	}
	if ($watt==-1){
		if ($wep_kind == 'N') {
			$watt =  round ($wp*2/3);	
			if ($club==23) $watt=round($watt*3/2);
		} else {
			$watt = $wepe * 2;
		}
	}
	
	$hitrate = get_hitrate ( $wep_kind, $wep_skill, $club, $inf );
	
	include_once GAME_ROOT.'./include/game/clubskills.func.php';
	$hitrate *= get_clubskill_bonus_hitrate($club,$skills,'',$w_club,$w_skills,'w_');
	if ($club==19) $hitrate*=1.2;
	
	//$damage_p = get_damage_p ( $rage, $att_key, 0, '你' , $club, $message);
	
	$damage_p=1;
	$hit_time = get_hit_time ( $att_key, $wep_skill, $hitrate, $wep_kind, $weps, $infobbs [$wep_kind] * get_clubskill_bonus_imfrate($club,$skills,'',$w_club,$w_skills,'w_'), get_clubskill_bonus_imftime($club,$skills,'',$w_club,$w_skills,'w_'), $wepimprate [$wep_kind] * get_clubskill_bonus_imprate($club,$skills,'',$w_club,$w_skills,'w_'), $is_wpg, get_clubskill_bonus_hitrate($club,$skills,'',$w_club,$w_skills,'w_'),$club,$lvl,$bsk);
	if ($hit_time [1] > 0) {
		
		$gender_dmg_p = check_gender ( '你', $w_name, $gd, $w_gd, $att_key );
		if ($gender_dmg_p == 0) {
			$damage = 1;
		} else {
			$w_active = 1 - $active;
			global $auraa,$now,$debuffc;
			$t_att=$att;
			if ($now<=$auraa) $t_att*=0.7;
			if ($now<=$debuffc) $t_att*=0.4;
			$attack = $t_att + $watt;
			$defend = checkdef($w_def , $w_arbe + $w_arhe + $w_arae + $w_arfe , $att_key, 1);
			
			if ($bsk=="slayer"){ 
				$log.="<span class=\"red\">对手在弹雨面前难以招架！</span><br>";
				$kr=$weps;
				if ($kr>$wepe*2) $kr=$wepe*2;
				if ($kr>2000) $kr=2000;
				$defend=$defend-$kr;
				if ($defend<1) $defend=1;
			}
			
			if ($wt==12){ //天变暴风雪
				$log.="<span class=\"red\">天变大幅减少了对手的防御！</span><br>";
				$defend=floor($defend/2)+1;
			}
			
			$damage = get_original_dmg ( '', 'w_', $attack, $defend, $wep_skill, $wep_kind );
			
			if ($wep_kind == 'F') {
				if($sldr){
					$log.="<span class=\"red\">由于灵魂抽取的作用，灵系武器伤害大幅降低了！</span><br>";
				}else{
					$damage = round ( ($wepe + $damage) * get_WF_p ( '', $club, $wepe) ); //get_spell_factor ( 0, $club, $att_key, $sp, $wepe ) );
				}
				
			}
			if ($wep_kind == 'J') {
				$adddamage=$w_mhp/3;
				if ($adddamage>20000) {$adddamage=10000;}
				$damage += round($wepe*2/3+$adddamage);
			}
			checkarb ( $damage, $wep_kind, $att_key, $w_def_key ,1);
			$damage *= $damage_p;
			
			$damage = $damage > 1 ? round ( $damage ) : 1;
			$damage *= $gender_dmg_p;
		}
		if ($w_wepk=='WJ'){
			$log.="<span class=\"red\">由于{$w_name}手中的武器过于笨重，受到的伤害大增！真是大快人心啊！</span><br>";
			$damage+=round($damage*0.5);
		}
		if ($hit_time [1] > 1) {
			$d_temp = $damage;
			if ($hit_time [1] == 2) {
				$dmg_p = 2;
			} elseif ($hit_time [1] == 3) {
				$dmg_p = 2.8;
			} else {
				$dmg_p = 2.8 + 0.6 * ($hit_time [1] - 3);
			}
			//$dmg_p = $hit_time[1] - ($hit_time[1]-1)*0.2;
			$damage = round ( $damage * $dmg_p );
			$log .= "造成{$d_temp}×{$dmg_p}＝<span class=\"red\">$damage</span>点伤害！<br>";
		} else {
			$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		}
		$pdamage = $damage;
		
		//物理伤害类计算
		
		if(($gemname=='红宝石')&&($gemstate==2)){
			include_once GAME_ROOT . './include/game/gem.func.php';
			$raisedmg=magic_gem('红宝石');		
			$losegem=round($damage*$raisedmg/100);
			if(($club==49)||($club==53)){$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";}
				if($losegem<=$gempower){
					$damage+=$losegem;$gempower-=$losegem;$gemexp+=round($losegem/1.25);
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使物理伤害增加了{$raisedmg}%！</span><br>";
				}elseif(($losegem>$gempower)&&(($club==49)||($club==53))&&($lvl>=19)){
					$log.="<span class='red'>【超限】使宝石魔法的效果被完全施展了！</span><br>";
					$damage+=$losegem;$gempower=0;
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使物理伤害增加了{$raisedmg}%！</span><br>";
				}else{
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使物理伤害增加了{$gempower}点！</span><br>";
					$gemexp+=round($gempower/1.25);$damage+=$gempower;$gempower=0;
					$gemstate=1;
					$log .= "<span class=\"yellow\">{$gemname}由于gem不足已失效，请补充gem！</span><br>";
				}
			if(($gemlvl>=3)&&($hp<$mhp)&&($damage>=10)){
				$vampire=round($damage*0.1);
				$hp=min($mhp,$hp+$vampire);
				$log .= "<span class=\"gem\">你身上的高阶{$gemname}魔法吸取了{$vampire}点生命！</span><br>";
			}
			if(($gemexp>=100)&&($gemlvl<3)){$gemlvl+=1;$gemexp=0;$log .= "<span class='lime'>{$gemname}升级了！</span><br>";}
		}
		
		if ($wt==2){ //天变多云
			$damage=round($damage*1.3);
			$log .= "<span class=\"red\">天变使物理伤害变为130%！</span><br>";
		}
		
		if ($bsk=='ambush'){
			$damage=round($damage*1.2);
			$log .= "<span class=\"red\">偷袭使物理伤害变为120%！</span><br>";
		}
		
		if ($bsk=='crit'){
			$damage=$damage*2;
			$log .= "<span class=\"red\">必杀使物理伤害变为200%！</span><br>";
		}
		
		if ($bsk=='net'){
			$damage=round($damage*1.35);
			$log .= "<span class=\"red\">电网使物理伤害变为135%！</span><br>";
		}
		
		if ($bsk=='aim'){
			$damage=round($damage*1.2);
			$log .= "<span class=\"red\">瞄准使物理伤害变为120%！</span><br>";
		}
		
		if ($bsk=='ragestrike' && $damage>0){
			$damage=round($damage*2);
			$log .= "<span class=\"red\">怒刺使物理伤害变为200%！</span><br>";
		}
		
		if (($club==2)&&($lvl>=15)){
			$damage=round($damage*1.15);
			$log .= "<span class=\"red\">居合使物理伤害变为115%！</span><br>";
		}
		
		if (($club==70)&&($lvl>=15)&&($wepk=='WF')&&($artk=='ss')){
			$damage=round($damage*1.2);
			$log .= "<span class=\"red\">言灵使物理伤害变为120%！</span><br>";
		}
		
		if ($bsk=='battlesong'){
			$damage=round($damage*1.5);
			$log .= "<span class=\"red\">士气高昂的歌曲使物理伤害变为150%！</span><br>";
			$att_key=str_replace('w','',$att_key);
			$att_key=$att_key.'w';
			$log.='战歌使你的武器附加了<span class="grey">音波</span>属性。<br>';
		}

		
		
		global $w_aurad,$aurad,$now;
		if ($now<=$aurad)
		{
			$damage=round($damage*0.75);
			$log.= "<span class=\"grey\">在你的光环作用下，你的物理伤害减少了25%！</span><br>";
		}
		
		if ($now<=$w_aurad)
		{
			global $w_lvl;
			$rate=min(25,5+$w_lvl);
			$damage=round($damage*(100-$rate)/100);
			$log.= "<span class=\"grey\">在敌方光环作用下，敌方受到的物理伤害减少了{$rate}%！</span><br>";
		}
		
		global $debuffa,$now;
		if ($now<=$debuffa)
		{
			$damage=round($damage*0.8);
			$log.="<span class=\"grey\">由于你被灵魂缠绕，你的物理伤害减少了20%！</span><br>";
		}
		
		global $w_debuffb,$now;
		if ($now<=$w_debuffb)
		{
			$damage=round($damage*1.3);
			$log.="<span class=\"yellow\">由于敌方处于恐惧状态，受到的物理伤害增加了30%！</span><br>";
		}
		
		global $w_club, $w_lvl;
		if ($w_club==19 && $w_lvl>=3)
		{
			$reduction=min($w_lvl,10);
			$damage=round($damage*(100-$reduction)/100);
			$log.="<span class=\"clan\">敌人坚韧的意志抵挡了{$reduction}%的物理伤害！</span><br>";
		}
		
		//
		
		if ($club==19 && $lvl>=15)
		{
			$log.="<span class=\"grey\">受忍术技能的影响，你的攻击没有造成属性伤害。</span><br>";
		}
		else
		{
			$eedmg=get_ex_dmg ( $w_name, 0, $club, $w_inf, $att_key, $wep_kind, $wepe, $wep_skill, $w_def_key,$lvl ,$bsk);
		
			if ($bsk=='roar'){
				$eedmg=round($eedmg*1.8);
				$log .= "<span class=\"red\">咆哮使属性伤害变为180%！</span><br>";
			}	
		
			if ($wt==6){ //天变雷雨
				$eedmg=round($eedmg*1.4);
				$log .= "<span class=\"red\">天变使属性伤害变为140%！</span><br>";
			}
			
			if (($club==5)&&($lvl>=7)&&($eedmg>=1)){
				$ded=round($eedmg*0.1);
				if ($ded<=1) $ded=1;
				$eedmg=$eedmg+$ded;
				$log .= "<span class=\"clan\">在强击技能的作用下，{$w_name}受到的属性伤害增加了{$ded}点！</span><br>";
			}
			
			if (($club==3)&&($lvl>=7)&&($eedmg>=1)&&($wep_kind=='C')){
				$ded=round($eedmg*0.35);
				if ($ded<=1) $ded=1;
				$eedmg=$eedmg+$ded;
				$log .= "<span class=\"clan\">在花雨技能的作用下，{$w_name}受到的属性伤害增加了{$ded}点！</span><br>";
			}	
		
			if (($w_club==9)&&($w_lvl>=7)&&($eedmg)){
				$ded=floor($eedmg/100*rand(5,15))+1;
				$eedmg=$eedmg-$ded;
				$log .= "<span class=\"clan\">在护体技能的作用下，{$w_name}受到的属性伤害减少了{$ded}点！</span><br>";
			}
			
			global $aurab, $now; 
			if ($now<=$aurab && $eedmg)
			{
				$eedmg=round($eedmg*0.4);
				$log .= "<span class=\"clan\">在你的光环作用下，{$w_name}受到的属性伤害降低了60%！</span><br>";
			}
			
			global $w_aurab, $now, $w_lvl; 
			if ($now<=$w_aurab && $eedmg)
			{
				$rate=max(30,70-round($w_lvl*1.5)); $reducted_rate=100-$rate;
				$eedmg=round($eedmg*$rate/100);
				$log .= "<span class=\"yellow\">在敌方光环作用下，{$w_name}受到的属性伤害降低了{$reducted_rate}%！</span><br>";
			}
			
			if(($w_club==49)&&($eedmg>100)&&($w_gempower>0)){
				if($w_type!=0){$w_gempower+=250;}
				$w_eqs=round($w_gempower*2);
				if($eedmg>$w_eqs){$w_l_edmg=$w_eqs;$eedmg-=$w_l_edmg;$w_gempower=0;}
				else{$w_l_edmg=$eedmg-1;$eedmg=1;$w_gempower-=ceil($w_l_edmg/2);}
				$db->query("UPDATE {$tablepre}players SET gempower=$w_gempower WHERE name='$w_name'");
				$log .= "<span class=\"deeppink\">{$w_name}身上的奥术粒子抵消了{$w_l_edmg}点属性伤害{$w_gempower}！</span><br>";
			}
			
			if(($w_club==53)&&($eedmg>100)&&($w_gempower>0)){
				if($w_type!=0){$w_gempower+=500;}
				$w_eqs=round($w_gempower*4);
				if($eedmg>$w_eqs){$w_l_edmg=$w_eqs;$eedmg-=$w_l_edmg;$w_gempower=0;}
				else{$w_l_edmg=$eedmg-1;$eedmg=1;$w_gempower-=ceil($w_l_edmg/4);}
				$db->query("UPDATE {$tablepre}players SET gempower=$w_gempower WHERE name='$w_name'");
				$log .= "<span class=\"deeppink\">{$w_name}身上的奥秘粒子抵消了{$w_l_edmg}点属性伤害{$w_gempower}！</span><br>";
			}
			
			$damage += $eedmg;
		}
		
		//最终伤害类计算
		
		if (($w_type==31)&&($w_name=='上帝的左手')){
			$damage=1;
			$log .= "<span class=\"clan\">很遗憾，普通的伤害似乎对身经百战的左手不起作用……</span><br>";
		}
		
		if ($surflag==true){
			$damage+=$supdamage;
			$log .= "<span class=\"red\">压制消耗了{$supdamage}点生命值，附加了相同伤害！</span><br>";
		}
		
		if (($club==14)&&($lvl>=15)){
			$mhdmg=round($mhp*0.1);
			if ($mhdmg>120) $mhdmg=120;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">刚击附加了{$mhdmg}点伤害！</span><br>";
		}
		
		/*if (($club==18)&&($lvl>=15)){
			$mhdmg=round($w_wepe*0.12);
			if ($mhdmg>160) $mhdmg=160;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">解构附加了{$mhdmg}点伤害！</span><br>";
		}*/
		
		if (($club==14)&&($lvl>=19)&&($hp<=round($mhp*0.5))){
			$mhdmg=round($w_mhp*0.4);
			if ($mhdmg>400) $mhdmg=400;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">斗魂附加了{$mhdmg}点伤害！</span><br>";
		}
		
		if ($bsk=='bash'){
			if ($w_type){
				//$damage=round($damage*1.3);
				//$log .= "<span class=\"red\">闷棍使最终伤害变为130%！</span><br>";
				$log .= "<span class=\"red\">由于对方是NPC，闷棍没有造成任何效果！</span><br>";
			}else{
				$sxd=$w_msp-$w_sp;
				if ($sxd>0){
					$damage+=$sxd;
					$log .= "<span class=\"red\">闷棍对精神不集中的敌人附加了{$sxd}点伤害！</span><br>";
				}
			}
		}
		
		if ($wt==11){ //天变龙卷风
			if ($w_type){
				$damage=round($damage*1.3);
				$log .= "<span class=\"red\">天变使最终伤害变为130%！</span><br>";
			}else{
				$sxd=$w_msp-$w_sp;
				if ($sxd>0){
					$damage+=$sxd;
					$log .= "<span class=\"red\">天变附加了{$sxd}点伤害！</span><br>";
				}
			}
		}
		
		if ($wt==13){ //天变冰雹
			if ($w_type){
				$damage=round($damage*1.4);
				$log .= "<span class=\"red\">天变使最终伤害变为140%！</span><br>";
			}else{
				$sxd=floor($w_hp/5)+1;
				if ($sxd>0){
					$damage+=$sxd;
					$log .= "<span class=\"red\">天变附加了{$sxd}点伤害！</span><br>";
				}
			}
		}
		
		if ($bsk=='threat'){
			$sxd=floor($money/20)+1;
			if ($sxd>250) $sxd=250;
			$damage+=$sxd;
			$log .= "<span class=\"red\">威压附加了{$sxd}点伤害！</span><br>";
		}
		
		if ($bsk=='blame'){
			$sxd=$hasi*40+60;
			$damage+=$sxd;
			$log .= "<span class=\"red\">你批判了对手一番，附加了{$sxd}点伤害！</span><br>";
		}
		
		$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$name'");
		$sktime = $db->result($result, 0);
		if (!$sktime) $sktime=0;
		
		if ($bsk=='assasinate'){
			$sktime--;
			$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
			$damage=floor($damage*$assper/100)+1;
			$log .= "<span class=\"red\">暗杀使最终伤害变为{$assper}%！</span><br>";
		}
		
		if ($wt==8){ //天变起雾
			$damage=floor($damage*$kiriper/100)+1;
			$log .= "<span class=\"red\">天变使最终伤害变为{$kiriper}%！</span><br>";
		}
		
		if ($bsk=='eagleeye'){
			$log .= "<span class=\"clan\">因枭眼技能，此次攻击完全命中。</span><br>";
		}
		
		if (($club==2)&&($lvl>=7)){
			$damage=round($damage*1.05);
			$log .= "<span class=\"red\">业物使最终伤害变为105%！</span><br>";
		}
		
		if (($club==18)&&($lvl>=3)){
			$damage=round($damage*1.02);
			$log .= "<span class=\"red\">适应使最终伤害变为102%！</span><br>";
		}
		
		if (($club==7)&&($lvl>=7)){
			$extd=30+$lvl*2;
			if (strpos($w_inf,'e')!==false){
				//$extd=50+$lvl*3;
				if ($lvl>=11){
					$rat=round($wep_skill/2)+100;
					$extd=floor($extd*$rat/100)+1;
				}
			}
			$log .= "<span class=\"yellow\">目标因行动不便受到了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if ($bsk=='analysis'){
			$extd=($w_mhp-$w_hp);
			if (($w_club==14)||($w_type>0)) $extd=200;
			$log .= "<span class=\"yellow\">解构附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if ($bsk=='punch'){
			$extd=floor($wp/5)+1;
			$log .= "<span class=\"yellow\">乱击附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if ($bsk=='focus'){
			$extd=$lvl*4+30;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">集中附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if ($bsk=='hunt'){
			$extd=$lvl*4+30;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">追猎附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($hp>=$mhp)&&($club==25)&&($lvl>=7)){
			$extd=$lvl*4+30;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">本气附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($club==25)&&($sktime==$w_pid)){
			$extd=$lvl*4+50;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">敌人因你的标记受到了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if ($bsk=='burst'){
			$extd=floor($wg/3)+1;
			$log .= "<span class=\"yellow\">点射附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($club==1)&&($lvl>=15)&&($wep_kind=='P')&&($hit_time[2]>0)){
			$log .= "<span class=\"yellow\">猛击附加了130点额外伤害！</span><br>";
			$damage+=130;
		}
		
		if (($club==1)&&($lvl>=19)&&($wep_kind=='P')){
			$exd=$wepe/2;
			if ($exd>0){
				$exd=floor($exd)+1;
				if ($exd>250) $exd=250;
				$log .= "<span class=\"yellow\">对手难以抵挡，受到了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if (($club==23)&&($lvl>=11)){
			$exd=100-$rage;
			if ($exd>0){
				$log .= "<span class=\"yellow\">冷静附加了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if ($bsk=='inplosion'){
			if (!$w_type){
				$damage=round($damage*1.3);
				$log .= "<span class=\"red\">内爆使最终伤害变为130%！</span><br>";
			}else{
				$damage=$damage*4;
				$log .= "<span class=\"red\">内爆使最终伤害变为400%！</span><br>";
			}
		}
		
		if ($bsk=='finalsong'){
			if ($w_type){
				$damage=$damage*3;
				$log .= "<span class=\"red\">安魂使最终伤害变为300%！</span><br>";
			}	
		}
		
		if ($wt==1){ //天变大晴
			$damage=round($damage*1.4);
			$log .= "<span class=\"red\">天变使最终伤害变为140%！</span><br>";
		}
		
		if (($wt>=14)&&($wt<=16)&&($w_type)){ //天变 臭氧洞 辐射尘 离子爆 对NPC
			$damage=round($damage*2);
			$log .= "<span class=\"red\">天变使最终伤害变为200%！</span><br>";
		}
		
		if ($wt==10){ //天变瘴气
			$damage=round($damage*2.33);
			$log .= "<span class=\"red\">天变使最终伤害变为233%！</span><br>";
		}
		
		if ($wt==4){ //天变暴雨
			$damage=round($damage*1.1);
			$log .= "<span class=\"red\">天变使最终伤害变为110%！</span><br>";
			if (!$w_type) $log .= "<span class=\"red\">天变使对手暂时难以行动！</span><br>";
		}
		
		if ($wt==3){ //天变小雨
			$exd=round($w_rage*1.5);
			if ($w_type) $exd=150;
			if ($exd>0){
				$log .= "<span class=\"yellow\">天变</span>";
				if (!$w_type) $log .= "<span class=\"yellow\">平复了对手的怒气，并</span>";
				$log .= "<span class=\"yellow\">附加了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if ($wt==5){ //天变台风
			$exd=$wepe/5;
			if ($exd>0){
				$exd=floor($exd)+1;
				if ($exd>200) $exd=200;
				$log .= "<span class=\"yellow\">天变附加了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if (($wt==9)&&($w_type)){ //天变浓雾
			$log .= "<span class=\"yellow\">天变附加了180点额外伤害！</span><br>";
			$damage+=180;
		}
		
		
		
		if ($bsk=='innerfire'){
			$damage=$damage*2;
			$log .= "<span class=\"red\">心火使最终伤害变为200%！</span><br>";
		}
		
		if ($bsk=='ego'){
			$egoper=100+$lvl;
			$damage=round($damage*$egoper/100);
			$log .= "<span class=\"red\">本能使最终伤害变为{$egoper}%！</span><br>";
		}
		
		if ($bsk=='dominate'){
			if (!$w_type){
				$damage=round($damage*1.2);
				$log .= "<span class=\"red\">主宰使最终伤害变为120%！</span><br>";
			}else{
				$damage=$damage*3;
				$log .= "<span class=\"red\">主宰使最终伤害变为300%！</span><br>";
			}
		}
		
		if ($bsk=='storm'){
			$damage=round($damage*1.4);
			$log .= "<span class=\"red\">烈风使最终伤害变为140%，并忽略了目标大部分的防御属性！</span><br>";
		}
		
		if (($club==3)&&($lvl>=19)&&($active==0)){
			$damage=round($damage*1.6);
			$log .= "<span class=\"red\">百出使反击和先制伤害变为160%！</span><br>";
		}
		
		if (($club==25)&&($lvl>=19)&&($sktime==$w_pid)){
			$damage=round($damage*1.2);
			$log .= "<span class=\"red\">一心使总伤害变为120%！</span><br>";
		}
		
		
		if (($club==2)&&($lvl>=3)&&(strlen($w_inf)>0)){
			$damage=round($damage*1.15);
			$log .= "<span class=\"red\">由于敌人已经受伤，总伤害变为115%！</span><br>";
		}
		
		if (($w_club==14)&&($w_lvl>=11)&&(!$active)){
			$damage=round($damage*0.9);
			if ($damage<1) $damage=1;
			$log .= "<span class=\"clan\">在铁骨技能的作用下，{$w_name}受到的先制和反击伤害变为90%！</span><br>";
		}
		
		if (($club==26)&&($lvl>=15)&&($hasi>0)){
			$hcou=3*$hasi;
			$damage+=round($damage*$hcou/100)+1;
			$log .= "<span class=\"red\">你的粉丝使伤害提高了{$hcou}%！</span><br>";
		}
		if (($w_club==26)&&($w_lvl>=15)&&($w_hasi>0)){
			$hcou=3*$w_hasi;
			if ($hcou>=50){
				$hcou=50;
			}
			$damage-=round($damage*$hcou/100);
			$log .= "<span class=\"red\">对方的粉丝使伤害降低了{$hcou}%！</span><br>";
		}
		
		global $dcloak_crit;
		if ($dcloak_crit)
		{
			$damage=round($damage*2);
			$log.="<span class=\"yellow\">敌人被打了个措手不及，受到了200%的伤害！</span><br>";
		}
		
		global $message;			
		if(($club==21)&&($lvl>=11)&&($message=='安拉胡阿克巴')&&($rage>=30)){
			$rage-=30;
			$damage=round($damage*2);
			$log .= "<span class=\"clan\">你忠诚的信仰使造成的伤害提高了200%！</span><br>";
		}
		
		if(strpos($w_inf,'S')!==false){
			$lmh=round($damage/2);$w_mhp=max(1,$w_mhp-$lmh);
			$log.="<span class=\"sienna\">由于对方被石化了，你的攻击使对方的生命上限下降了{$lmh}点！</span><br>";
		}
		
		if(strpos($wepsk,'=')!==false){
			global $hp,$mhp,$sp,$msp;
			$br=rand(1,35);$gb=round($br+$damage/10);
			$hp=min($hp+$gb,$mhp);$sp=min($sp+$gb,$msp);
			$log.="<span class=\"red\">吸血的效果使你恢复了{$gb}点生命与体力！</span><br>";
		}
		
		$damage = checkdmgdef($damage, $att_key,$w_def_key,1,$bsk);
		
		if (($club==2)&&($lvl>=19)){
			$dice=rand(1,100);
			if ($w_hp<$w_mhp) $dice=$dice/3;
			if ($dice<=11){ 
				$damage=$damage*2;
				$log .= "<span class=\"red\">斩击使最终伤害变为200%！</span><br>";
			}
		}
		
		//好人卡特别活动
		if($w_type == 0){
			$gm = ceil(count_good_man_card(0)*rand(80,120)/100);
			if($gm){
				$log .= "在{$w_name}身上的<span class=\"yellow\">好人卡</span>的作用下，{$w_name}受到的伤害增加了<span class=\"red\">$gm</span>点！<br>";
				$damage += $gm;
			}
		}	


		
		
		include_once GAME_ROOT . './include/game/clubskills.func.php';
		if ($w_club==22)
		{
			$ratio=get_clubskill_bonus_ironwill_reduction($w_hp,$w_mhp);
			$log .= "<span class=\"yellow\">敌人钢铁般的意志降低了你的伤害，伤害被减少至{$ratio}%！</span><br>";
			$damage=ceil(1.0*$damage*$ratio/100);
		}
		
		if (($w_club==6)&&($w_lvl>=19)){
			$blk=round($damage*0.2);
			if ($blk>($w_sp-1)) $blk=$w_sp-1;
			if ($blk>0){
				$log .= "<span class=\"clan\">{$w_name}用体力抵挡了{$blk}点伤害！</span><br>";
				$w_sp=$w_sp-$blk;
				$damage=$damage-$blk;
			}
		}
		
		//宝石判定一坨
		include_once GAME_ROOT.'./include/game/gem.func.php';
		if (($w_gemname=='黑曜石')&&($w_gemstate==2)){
			$reducedmg=w_magic_gem('黑曜石');
			$lose_w_gem=round($damage*$reducedmg/100);
			if(($w_club==49)||($w_club==53)){$log.="<span class='red'>【研究】使对方的宝石魔法效果提高了25%！</span><br>";}
			if($lose_w_gem<=$w_gempower){
				$damage-=$lose_w_gem;$w_gempower-=$lose_w_gem;$w_gemexp+=$lose_w_gem;
				$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法使所受伤害减少了{$reducedmg}%！</span><br>";
			}elseif(($lose_w_gem>$w_gempower)&&(($w_club==49)||($w_club==53))&&($w_lvl>=19)){
				$log.="<span class='red'>【超限】使对方的宝石魔法效果被完全施展了！</span><br>";
				$damage-=$lose_w_gem;$w_gempower=0;
				$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法使所受伤害减少了{$reducedmg}%！</span><br>";
			}else{
				$w_gemexp+=$w_gempower;$damage-=$w_gempower;$w_gempower=0;
				$w_gemstate=1;
			}
			if(($w_gemexp>=100)&&($w_gemlvl<3)){$w_gemlvl+=1;$w_gemexp=0;}
		}	
		if(($gemname=='红宝石')&&($gemstate==2)){
			if($gemlvl==0){$rfd=50;}
			elseif($gemlvl==1){$rfd=100;}
			elseif($gemlvl==2){$rfd=200;}
			elseif($gemlvl==3){$rfd=round($gempower/2.5);}		
			if(($club==49)||($club==53)){$rfd=round($rfd*1.25);}
			$damage+=$rfd;
			$log .= "<span class=\"yellow\">你身上的{$gemname}魔法附加了{$rfd}点伤害！</span><br>";
		}
		$magic_gemwep_dice=rand(1,100);
		if(($wep=='＜上灵＞')&&($club==53)&&($magic_gemwep_dice<=15)){
		magic_gemwep('＜上灵＞');
		}
		if(($wep=='＜船桨＞')&&($club==53)&&($w_mhp>$mhp)){
		$punishd=magic_gemwep('＜船桨＞');
		$w_mhp=max(1,$w_mhp-$punishd);
		if($w_hp>$w_mhp){$w_hp=$w_mhp;}
		}
		global $w_wep;
		if (($w_wep=='＜时刃＞')&&($w_club==53)){
		$w_tdp=w_magic_gemwep('＜时刃＞');
		$damage-=round($damage*($w_tdp/100));
		}
		
		if(($cursedsouls>0)&&($club==53)&&($wep=='＜夜母＞')&&($damage<$w_hp)){
		$log .= "<br><span class=\"gem\">「诅咒」被触发了，无数面容狰狞的夜种扑向了你的敌人！</span><br>";
		$cursedmg=round($damage*($cursedsouls/100));
		$w_hp=max(0,$w_hp-$cursedmg);
		$log .= "夜种们造成了<span class=\"red\">{$cursedmg}</span>点伤害！<br>";
		$curse_flag=true;
		}
		$bonus_dmg = get_clubskill_bonus_dmg_rate($club,$skills,$w_club,$w_skills)*100;
		if($bonus_dmg < 100){
			$log.="<span class=\"yellow\">由于技能效果的作用，伤害下降至".$bonus_dmg."%！</span><br>";
			$damage = round($damage * $bonus_dmg / 100);
		}
		$rpdmg=get_clubskill_bonus_dmg_val($club,$skills,$rp,$w_rp);
		if($rpdmg > 0){
			$log .= "<span class=\"yellow\">由于技能的影响，对方受到了<span class=\"red\">$rpdmg</span>点额外伤害。</span><br>";
			$damage += $rpdmg;
		}
		if($pdamage != $damage){
			if($curse_flag){
			$c_dmg=$damage+$cursedmg;
			$log .= "<span class=\"yellow\">造成的总伤害：<span class=\"red\">$c_dmg</span>。</span><br>";
			}else{
			$log .= "<span class=\"yellow\">造成的总伤害：<span class=\"red\">$damage</span>。</span><br>";
			}
		}
		
		global $teamID, $w_teamID;
		checkdmg ( $name, $w_name, $damage, $type*1000+$sNo, $w_type*1000+$w_sNo );
		
		if (($club!=25)||($hp<=$mhp)||($lvl<7)) get_dmg_punish ( '你', $damage, $hp, $att_key ,$club,$lvl);
		
		get_inf ( $w_name, $hit_time [2], $wep_kind,$club,$lvl);
		
		check_KP_wep ( '你', $hit_time [3], $wep, $wepk, $wepe, $weps, $wepsk );
		
		//命中后效果处理
		
		if (($w_type==30)&&($w_name=='Eminem.')){
			$ttr="阔剑地雷";
			$rp=18;
			if (rand(1,100)<5) $rp=rand(1,33);
			$le=rand(1,200)+$mhp-100;
			if ($le<2000) $le=2000;
			$db->query("INSERT INTO {$tablepre}maptrap (itm, itmk, itme, itms, itmsk, pls) VALUES ('$ttr', 'TO', '$le', '1', '$w_pid', '$rp')");
			$le=rand(1,200)+$damage-100;
			if ($le<2000) $le=2000;
			$db->query("INSERT INTO {$tablepre}maptrap (itm, itmk, itme, itms, itmsk, pls) VALUES ('$ttr', 'TO', '$le', '1', '$w_pid', '$rp')");
			$log .= "菜包放置了两颗阔剑地雷！<br>";
		}
		
		if (($club==10)&&($lvl>=7)){
			$exp++;
		}
		
		exprgup ( $lvl, $w_lvl, $exp, 1, $w_rage , 1);
	
		if (($club==1)&&($lvl>=7)&&($wep_kind=='P')&&(!$w_type)){
			$srd=round($damage*2/3)+50;
			if ($srd>=$w_sp) $srd=$w_sp-1;
			$w_sp-=$srd;
			$log.="你的攻击使敌人的体力减少了<span class=\"yellow\">{$srd}</span>点！<br>";
		}	
		
		//if (($club==2)&&($lvl>=19)&&($wep_kind=='K')&&(!$w_type)){
		//	if (strpos($w_inf,'B')===false){
		//		$w_inf.='B';
		//		$log.='你的攻击使敌人进入了<span class="red">裂伤</span>状态！<br>';
		//	}
		//}
			
		if ($bsk=='sting'){
			if (strpos($w_inf,'P')===false){
				$w_inf.='P';
				$w_inf = str_replace('p','',$w_inf);	
				$log.='毒刺技能使敌人进入了<span class="purple">猛毒</span>状态！<br>';
			}
		}
		
		if (($wt==6)&&(!$w_type)){ //天变雷雨
			$td=rand(0,99);
			if ($td<50){
				if (strpos($w_inf,'i')===false){
					$w_inf.='i';	
					$log.='天变使敌人进入了<span class="clan">冻结</span>状态！<br>';
				}
			}else{
				if (strpos($w_inf,'e')===false){
					$w_inf.='e';	
					$log.='天变使敌人进入了<span class="yellow">麻痹</span>状态！<br>';
				}
			}
		}
		
		if (($bsk=='roar')&&(!$w_type)){
			$w_inf = str_replace('b','',$w_inf);
			$w_inf = str_replace('h','',$w_inf);
			$w_inf = str_replace('a','',$w_inf);
			$w_inf = str_replace('f','',$w_inf);
			$w_inf = str_replace('u','',$w_inf);
			$w_inf=$w_inf.'bhafu';
			$log.='<span class="red">咆哮技能使敌人陷入了多种异常状态！</span><br>';
		}
		
		
		
		
		if (($bsk=='inplosion')&&(!$w_type)){
			$hcount=0;
			if (strpos($w_inf,'b')!==false){
				$hcount++;
				$w_inf = str_replace('b','',$w_inf);
				$w_arb='内衣';$w_arbe=0;$w_arbs='∞';$w_arbsk='';
				$log.='<span class="red">敌人的身体防具被摧毁了！</span><br>';
			}
			if (strpos($w_inf,'h')!==false){
				$hcount++;
				$w_inf = str_replace('h','',$w_inf);
				$w_arh='';$w_arhe=0;$w_arhs='0';$w_arhsk='';
				$log.='<span class="red">敌人的头部防具被摧毁了！</span><br>';
			}
			if (strpos($w_inf,'a')!==false){
				$hcount++;
				$w_inf = str_replace('a','',$w_inf);
				$w_ara='';$w_arae=0;$w_aras='0';$w_arask='';
				$log.='<span class="red">敌人的手部防具被摧毁了！</span><br>';
			}
			if (strpos($w_inf,'f')!==false){
				$hcount++;
				$w_inf = str_replace('f','',$w_inf);
				$w_arf='';$w_arfe=0;$w_arfs='0';$w_arfsk='';
				$log.='<span class="red">敌人的腿部防具被摧毁了！</span><br>';
			}
			$w_inf=$w_inf.'bhaf';
			$hrd=round($w_mhp*$hcount/3);
			if ($hrd>=$w_mhp) $hrd=$w_mhp-1;
			if ($hcount){
				$w_mhp-=$hrd;
				if ($w_hp>$w_mhp) $w_hp=$w_mhp;
				$log.="<span class=\"red\">敌人的最大生命减少了{$hrd}点！</span><br>";
			}
		}
		
		if (($bsk=='finalsong') && (!$w_type)){
			$dmg_rate=floor($ss/2)*0.01;
			$w_hp-=floor($w_mhp*$dmg_rate);
		}
		
		if (($club==5)&&(!$w_type)&&($lvl>=11)){
			$idc=rand(1,4);
			if ($idc==1){
				$w_inf=str_replace('b','',$w_inf);
				$w_inf=$w_inf.'b';
				$log.='<span class="yellow">共振使敌人的身体受伤了！</span><br>';
			}
			if ($idc==2){
				$w_inf=str_replace('h','',$w_inf);
				$w_inf=$w_inf.'h';
				$log.='<span class="yellow">共振使敌人的头部受伤了！</span><br>';
			}
			if ($idc==3){
				$w_inf=str_replace('a','',$w_inf);
				$w_inf=$w_inf.'a';
				$log.='<span class="yellow">共振使敌人的手部受伤了！</span><br>';
			}
			if ($idc==4){
				$w_inf=str_replace('f','',$w_inf);
				$w_inf=$w_inf.'f';
				$log.='<span class="yellow">共振使敌人的足部受伤了！</span><br>';
			}
		}
		
		if (($wt==14)&&(!$w_type)){ //天变离子爆
			$hrd=floor($w_msp/10)+1;
			if ($hrd>=$w_msp) $hrd=$w_msp-1;
			if ($hrd){
				$w_msp-=$hrd;
				$log.="<span class=\"red\">敌人的最大体力减少了{$hrd}点！</span><br>";
			}
		}
		
		if (($wt==15)&&(!$w_type)){ //天变辐射尘
			$hrd=floor($w_mhp/10)+1;
			if ($hrd>=$w_mhp) $hrd=$w_mhp-1;
			if ($hrd){
				$w_mhp-=$hrd;
				$log.="<span class=\"red\">敌人的最大生命减少了{$hrd}点！</span><br>";
			}
		}
		if (($wt==16)&&(!$w_type)){ //天变臭氧洞
			$w_inf=str_replace('u','',$w_inf);
			$w_inf=$w_inf.'u';
			$w_inf=str_replace('i','',$w_inf);
			$w_inf=$w_inf.'i';
			$w_inf=str_replace('e','',$w_inf);
			$w_inf=$w_inf.'e';
			$w_inf=str_replace('w','',$w_inf);
			$w_inf=$w_inf.'w';
			$w_inf=str_replace('p','',$w_inf);
			$w_inf=$w_inf.'p';
			$log.='<span class="red">天变使敌人生活不能自理！</span><br>';
		}
		
		
		if (($bsk=='net')&&(!$w_type)){
			$nf=rand(1,4);
			if ($nf==1){
				$w_inf=str_replace('b','',$w_inf);
				$w_inf=$w_inf.'b';
			}elseif ($nf==2){
				$w_inf=str_replace('h','',$w_inf);
				$w_inf=$w_inf.'h';
			}elseif ($nf==3){
				$w_inf=str_replace('a','',$w_inf);
				$w_inf=$w_inf.'a';
			}elseif ($nf==4){
				$w_inf=str_replace('f','',$w_inf);
				$w_inf=$w_inf.'f';
			}
			$log.='<span class="red">电网使敌人暂时难以行动！</span><br>';
		}
		
		if (($club==24 || $club==99) && $lvl>=3)
		{
			if (rand(0,99)<35)
			{
				$log.='<span class="yellow">你吸取了敌人的1点生命上限！</span><br>';
				$mhp++; $w_mhp--; 
			}
			if (rand(0,99)<35)
			{
				$log.='<span class="yellow">你吸取了敌人的1点全系熟练！</span><br>';
				global $w_wp,$w_wk,$w_wc,$w_wg,$w_wd,$w_wf;
				global $wp,$wk,$wc,$wg,$wd,$wf;
				if ($w_wp>0) { $w_wp--; $wp++; }
				if ($w_wk>0) { $w_wk--; $wk++; }
				if ($w_wc>0) { $w_wc--; $wc++; }
				if ($w_wg>0) { $w_wg--; $wg++; }
				if ($w_wd>0) { $w_wd--; $wd++; }
				if ($w_wf>0) { $w_wf--; $wf++; }
			}
		}
		
		
		
	//
	
	} else {
		$damage = 0;
		$log .= "但是没有击中！<br>";
	}
	check_GCDF_wep ( '你', $hit_time [0], $wep, $wep_kind, $wepk, $wepe, $weps, $wepsk ,$club,$lvl,$bsk);
	
	addnoise ( $wep_kind, $wepsk, $now, $pls, $pid, $w_pid, $wep_kind );
	if($club == 10){
		$add_skill +=2;
		if ($lvl>=19) $add_skill++;
	}else{
		$add_skill +=1;
	}

	
	//战斗后效果处理，结算怒气
	
	if (($bsk=='dominate')&&(!$w_type)){
		$destroy_dice=rand(1,5);
		global $w_arb,$w_arbk,$w_arbe,$w_arbs,$w_arbsk;
		global $w_arh,$w_arhk,$w_arhe,$w_arhs,$w_arhsk;
		global $w_ara,$w_arak,$w_arae,$w_aras,$w_arask;
		global $w_arf,$w_arfk,$w_arfe,$w_arfs,$w_arfsk;
		global $w_art,$w_artk,$w_arte,$w_arts,$w_artsk;
		$w_log='';
		if($destroy_dice == 1){
			$log .= "<span class=\"yellow\">你击碎了敌人的身体防具！</span><br>";
			$w_log= "<span class=\"yellow\">敌人击碎了你的身体防具！</span><br>";
			$w_arb = ''; $w_arbk = ''; $w_arbe = 0; $w_arbs = 0; $w_arbsk = '';
		}elseif($destroy_dice == 2){
			$log .= "<span class=\"yellow\">你击碎了敌人的头部防具！</span><br>";
			$w_log= "<span class=\"yellow\">敌人击碎了你的头部防具！</span><br>";
			$w_arh = ''; $w_arhk = ''; $w_arhe = 0; $w_arhs = 0; $w_arhsk = '';
		}elseif($destroy_dice == 3){
			$log .= "<span class=\"yellow\">你击碎了敌人的手部防具！</span><br>";
			$w_log= "<span class=\"yellow\">敌人击碎了你的手部防具！</span><br>";
			$w_ara = ''; $w_arak = ''; $w_arae = 0; $w_aras = 0; $w_arask = '';
		}elseif($destroy_dice == 4){
			$log .= "<span class=\"yellow\">你击碎了敌人的足部防具！</span><br>";
			$w_log= "<span class=\"yellow\">敌人击碎了你的足部防具！</span><br>";
			$w_arf = ''; $w_arfk = ''; $w_arfe = 0; $w_arfs = 0; $w_arfsk = '';
		}elseif($destroy_dice == 5){
			$log .= "<span class=\"yellow\">你击碎了敌人的饰品！</span><br>";
			$w_log= "<span class=\"yellow\">敌人击碎了你的饰品！</span><br>";
			$w_art = ''; $w_artk = ''; $w_arte = 0; $w_arts = 0; $w_artsk = '';
		}
		global $now,$w_pid;
		logsave ( $w_pid, $now, $w_log ,'b');
	}
	
	if (($club==18)&&($lvl>=7)){
		$asd=rand(1,6);
		if ($asd==1) $wp++;
		if ($asd==2) $wk++;
		if ($asd==3) $wc++;
		if ($asd==4) $wd++;
		if ($asd==5) $wf++;
		if ($asd==6) $wg++;
	}

	//[u150910]偶像大师装备歌词卡攻击时增加歌魂
	if (($club==70)&&($lvl>=3)&&($artk=='ss')){
		global $ss,$mss;
		$ss+=5;
		if($ss>$mss) $ss=$mss;
		$log.="你的歌魂恢复了<span class=\"lime\">5</span>点！<br>";
	}
	
	if (($bsk=='absorb')&&(!$w_type)&&($w_club!=17)){
		$ttw=round(($w_wp+$w_wk+$w_wc+$w_wg+$w_wd+$w_wf)/15);
		$wp+=$ttw;$wc+=$ttw;$wd+=$ttw;$wf+=$ttw;$wk+=$ttw;$wg+=$ttw;
		$log.="你的全熟练提升了<span class=\"lime\">{$ttw}</span>点！<br>";
		$rage=$rage-100;
		$sktime--;
		$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
		if ($rage<0) $rage=0;
	}
	
	if (($bsk=='hunt')&&(!$w_type)){
		$db->query("UPDATE {$tablepre}users SET sktime='$w_pid' WHERE username='$name'");
	}
	
	
	if($club == 14){
		if($active){
			$att++;
		}else{
			$def++;
		}
	}
	
	if (($club==7)&&($wep_kind=='P')&&(rand(1,4)==4)&&($lvl>=3)){
		$add_skill +=2;
	}
	
	if (($club==23)&&($lvl>=3)){
		$pdc=rand(1,100);
		if ($pdc<=60) $add_skill++;
		if ($pdc<=30) $add_skill++;
		if ($pdc<=15) $add_skill++;
		if ($pdc<=5) $add_skill++;
	}

	//偶像大师的歌词卡提供HP制御
	if (($club==70)&&($lvl>=11)&&($artk=='ss')){
		$atkcdt .='H';	
	}
	
	if ($bsk=='blame'){
		$rage=$rage-20;
		if ($rage<0) $rage=0;
	}
	if ($wt!=-1){
		$rage=$rage-15;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='focus'){
		$rage=$rage-15;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='hunt'){
		$rage=$rage-50;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='threat'){
		$rage=$rage-30;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='burst'){
		$rage=$rage-25;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='slayer'){
		$rage=$rage-70;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='eagleeye'){
		$rage=$rage-60;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='enchant'){
		$rage=$rage-10;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='net'){
		$rage=$rage-15;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='analysis'){
		$rage=$rage-60;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='punch'){
		$rage=$rage-30;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='crit'){
		if (strpos ( $att_key, "c" ) !== false) {
			$rage=$rage-20;
		}else{
			$rage=$rage-40;
		}
		if ($rage<0) $rage=0;
	}
	if ($bsk=='sting'){
		$rage=$rage-40;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='bash'){
		$rage=$rage-70;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='ambush'){
		$rage=$rage-20;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='boom'){
		$rage=$rage-60;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='inplosion'){
		$rage=$rage-50;
		if ($rage<0) $rage=0;
		$sktime--;
		$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
	}
	if ($bsk=='ego'){
		$rage=$rage-30;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='dominate'){
		$rage=$rage-100;
		$sktime--;
		$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
		if ($rage<0) $rage=0;
	}
	if ($bsk=='suppress'){
		$rage=$rage-30;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='innerfire'){
		$rage=$rage-60;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='storm'){
		$rage=$rage-70;
		if ($rage<0) $rage=0;
	}
	//if ($bsk=='steeldance'){
	//	$rage=$rage-70;
	//	if ($rage<0) $rage=0;
	//}
	if ($bsk=='aim'){
		$rage=$rage-20;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='roar'){
		$rage=$rage-100;
		if ($rage<0) $rage=0;
	}
	if ($bsk=='recharge'){
		$rage=100;
		$sktime--;
		$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
		$log.='<span class="clan">充能完毕，怒气恢复至100点。</span><br>';
	}
	if ($bsk=='ragestrike'){
		$rage-=30;
		if ($rage<0) $rage=0;
	}
	
	if (($w_club==1)&&($w_lvl>=7)){
		$w_rage+=5;
		if ($w_rage>100) $w_rage=100;
	}
	
	//[u150910]偶像大师战歌技能
	if ($bsk=='battlesong'){
		$rage-=5;
		if ($rage<0) $rage=0;
		$ss-=20;
		if ($ss<0)	$ss=0;
	}
	//[u150924]偶像大师安魂技能
	if ($bsk=='finalsong'){
		$rage-=50;
		if ($rage<0) $rage=0;
		$ss=0;
		$sktime--;
		$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
	}
	
	
	//
	//爆头
	
	if (($club==4)&&($lvl>=19)&&($wep_kind=='G')&&($damage>($w_hp*0.85))&&($damage<$w_hp)){
		$damage=$w_hp;
		$log.='<span class="red">你的攻击直接将目标爆头！</span><br>';
	}
	
	//
	
	//附加DEBUFF处理
	
	global $souls;
	if ($bsk=='entangle')
	{
		global $w_debuffa, $now;
		$w_debuffa=$now+180;	//不可叠加
		$log.='<span class="yellow">敌人被怨灵缠绕了（时效180秒，物理伤害输出-20%）。</span><br>';
		$souls--;
	}
	
	if ($bsk=='fear')
	{
		global $w_debuffb, $now;
		$w_debuffb=$now+120;	//不可叠加
		$log.='<span class="yellow">敌人内心的恐惧不可抑制地爆发了（时效120秒，受到物理伤害+30%）。</span><br>';
		$souls--;
	}
	
	if ($bsk=='corrupt')
	{
		global $w_debuffc, $now;
		$w_debuffc=$now+360;	//不可叠加
		$log.='<span class="yellow">敌人被黑暗的灵魂腐蚀了（时效360秒，基础攻防-60%）。</span><br>';
		$souls--;
	}
	
	if (($damage>=1900)&&($w_type==30)&&($w_name=='霜火协奏曲')&&(rand(1,100)>1)){

		$oldwep = $w_wep;
		$c=rand(1,4);
		$tw=${'w_itm'.$c};$tt=${'w_itmk'.$c};$te=${'w_itme'.$c};$ts=${'w_itms'.$c};$tk=${'w_itmsk'.$c};
		${'w_itm'.$c} = $w_wep;${'w_itmk'.$c} = $w_wepk;${'w_itme'.$c} = $w_wepe;${'w_itms'.$c} = $w_weps;${'w_itmsk'.$c} = $w_wepsk;
		$w_wep = $tw; $w_wepk = $tt; $w_wepe = $te;$w_weps = $ts;$w_wepsk = $tk;
		$log .= "<span class=\"yellow\">{$w_name}</span>将手中的<span class=\"yellow\">{$oldwep}</span>卸下，装备了<span class=\"yellow\">{$w_wep}</span>！<br>";
	}
	if(($w_type==31)&&($w_name=="飞雪大大")){
		$w_arte=$wepe;
	}
	if (($w_hp<=$damage)&&($w_type==32)&&($w_name=='sillycross')){
		$w_arb='内衣';$w_arbe=0;$w_arbs='∞';$w_arbsk='';
		$w_arh='';$w_arhe=0;$w_arhs='0';$w_arhsk='';
		$w_arf='';$w_arfe=0;$w_arfs='0';$w_arfsk='';
		$w_ara='';$w_arae=0;$w_aras='0';$w_arask='';
	}
	
	if (($w_hp<=$damage)&&($w_type==21)&&($w_name=='埃尔兰卡')){
		$log .= "<span class=\"deeppink\">在虹光魔眼消散之后，一颗宝石掉在了地上，你想走过去观察一下，那颗宝石却忽然飞起镶在了你的指骨上！<br>";
		global $gemname,$gemstate,$gemlvl;
		$gemname='白欧泊石';$gemstate=3;$gemlvl=3;
	}
	
	if($w_name=='夜种'){
	global $cursedsouls,$w_cdowner;
		if($name==$w_cdowner){
		$log .= "<span class=\"red\">你发出了一阵痛苦的尖叫……<br>";
		$hp-=$damage;
			if($damage>=$w_hp){$cursedsouls--;}
			if($hp<=0){
			include_once GAME_ROOT . './include/state.func.php';
			death('cursedeath',$name);
			}
		}else{
		$result = $db->query("SELECT hp FROM {$tablepre}players WHERE name='$w_cdowner' AND type='0'");
		$bcdhp = $db->result($result, 0);
			if($bcdhp>0){
				$cdhp=max(0,$bcdhp-$damage);
				$db->query("UPDATE {$tablepre}players SET hp=$cdhp WHERE name='$w_cdowner' AND type='0'");
				$log .= "<span class=\"red\">你听到远方传来一阵痛苦的尖叫……<br>";
				if($damage>=$w_hp){
					$result = $db->query("SELECT cursedsouls FROM {$tablepre}players WHERE name='$w_cdowner' AND type='0'");
					$ocdsouls = $db->result($result, 0);
					$ncdsouls=$ocdsouls--;
					$db->query("UPDATE {$tablepre}players SET cursedsouls=$ncdsouls WHERE name='$w_cdowner' AND type='0'");
					}
				if($cdhp<=0){
					$db->query("UPDATE {$tablepre}players SET state=47 WHERE name='$w_cdowner' AND type='0'");
					$db->query("UPDATE {$tablepre}players SET deathtime=$now WHERE name='$w_cdowner' AND type='0'");
					addnews($now,'death47',$w_cdowner);	
					}
			}
		}
	}
	
	if (($w_hp<=$damage)&&($w_club!==99)&&($wep=='＜夜母＞')&&($w_name!=='夜种')&&($club==53)){
	global $cursedsouls,$name;	
		$cursedsouls++;
		addnews($now,'gem_wep_magic',$name,'＜夜母＞',$w_name);	
		include_once GAME_ROOT . './include/system.func.php';
		addnpc (87,0,1,'bzk',$name);
	}
	
	if ($w_hp<=$damage){
		for($i = 1;$i <= 6;$i++){
			if(strpos(${'w_itmsk'.$i},'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">${'w_itm'.$i}</span>也化作灰烬消散了。<br>";
			${'w_itm'.$i} = ${'w_itmk'.$i} = ${'w_itmsk'.$i} = '';
			${'w_itme'.$i} = ${'w_itms'.$i} = 0;
			}
			if(strpos($w_wepsk,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_wep}</span>也化作灰烬消散了。<br>";
			$w_wep = '拳头' ; $w_wepk = 'WN' ; $w_wepsk ='';
			$w_weps = '∞' ; $w_wepe = 0;
			}
			if(strpos($w_arbsk,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_arb}</span>也化作灰烬消散了。<br>";
			$w_arb='内衣';$w_arbk ='DN';$w_arbsk ='';
			$w_arbs='∞';$w_arbe = 0;
			}
			if(strpos($w_arhsk,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_arh}</span>也化作灰烬消散了。<br>";
			$w_arh=$w_arhk=$w_arhsk ='';
			$w_arhs=$w_arhe = 0;
			}
			if(strpos($w_arask,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_ara}</span>也化作灰烬消散了。<br>";
			$w_ara=$w_arak=$w_arask ='';
			$w_aras=$w_arae = 0;
			}
			if(strpos($w_arfsk,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_arf}</span>也化作灰烬消散了。<br>";
			$w_arf=$w_arfk=$w_arfsk ='';
			$w_arfs=$w_arfe = 0;
			}
			if(strpos($w_artsk,'v')!==false){
			$log .= "伴随着对方的死亡，对方的<span class=\"yellow\">{$w_art}</span>也化作灰烬消散了。<br>";
			$w_art = $w_artk = $w_artsk ='';
			$w_arts=$w_arte = 0;
			}
		}
		if((strpos($wepsk,'|')!==false)&&($w_lvl>=10)){
			$wepe+=round($w_lvl-4);
		}
		if((strpos($wepsk,'=')!==false)&&(rand(1,100)<=25)){
			$hp=max($hp,$mhp);$sp=max($sp,$msp);
		}
	}
	
	return $damage;
}

function npc_useskill($secstrike, &$damage, &$eedmg)
{
	global $log, $w_name, $w_club, $w_lvl, $w_rage, $w_wepk, $hp, $w_mhp, $w_hp;
	$rat=$w_hp/$w_mhp;
	if ($damage+$eedmg>=$hp) 
	{
		//打得死，不用技能啦！ （暂时不考虑疾风的25级体力减伤了）
		//当然，充能还是要放的>_<
		if ($w_club==9 && $w_rage<150)
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">对你发动了技能「充能」！</span><br>";
			$log_tail="<span class=\"red\">$w_name</span><span class=\"yellow\">获得了100点怒气！</span><br>";
			$log=$log_head.$log.$log_tail;
			$w_rage+=100;
		}
		return;
	}
	$log_head=""; $log_tail="";
	if ($w_club==0) return;		//无称号的普通重击已经在defend()中判定过了
	if ($w_club==1 || ($w_club==98 && $w_wepk=='WP'))		//街头霸王
	{
		global $msp,$sp;
		if ($w_rage<20 || $w_lvl<3) return;
		$d1=$damage+$msp-$sp; if ($w_rage<70 || $w_lvl<15) $d1=0;
		$d2=round($damage*1.2); if ($w_rage<20 || $w_lvl<3) $d2=0;
		if ($rat>0.4 && max($d1,$d2)-$damage<150 && max($d1,$d2)+$eedmg<$hp) return;	//照样打不死，效果也不强，不放技能
		if ($d1>$d2)	//发动闷棍
		{
			$sxd=$msp-$sp; 
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">70</span>点怒气，对你发动了技能「闷棍」！</span><br>";
			$log_tail="<span class=\"red\">闷棍对精神不集中的你附加了{$sxd}点伤害！</span><br>";
			$damage=$d1; $w_rage-=70;
		}
		else			//发动偷袭
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">50</span>点怒气，对你发动了技能「偷袭」！</span><br>";
			$log_tail="<span class=\"red\">偷袭使物理伤害变为120%！</span><br>";
			$damage=$d2; $w_rage-=20;
		}
		$log=$log_head.$log.$log_tail;
		return;
	}
	if ($w_club==2 || ($w_club==98 && $w_wepk=='WK'))		//见敌必斩
	{
		//见敌必斩需要在defend()内部判定
		//实现思路：先模拟打一次，如果没打死：如果((对方有全系防御或者防斩)或(武器连击次数>=3))就放烈风，否则放舞钢
		//太懒不实现了
		return;
	}
	if ($w_club==5 || ($w_club==98 && $w_wepk=='WD'))		//拆蛋专家
	{
		//拆蛋专家需要在defend()内部判定
		//实现思路：先模拟打一次，如果对方有属防或防爆且当前伤害×2后能打死就放
		//太懒不实现了
		return;
	}
	if ($w_club==3 || ($w_club==98 && $w_wepk=='WC'))		//灌篮高手
	{
		//灌篮高手需要在defend()内部判定
		//实现思路：附魔没用，只放枭眼就行，先模拟打一次，如果当前打出的伤害打不死，但满连后可以打死，就放
		//太懒不实现了
		return;
	}
	if ($w_club==4 || ($w_club==98 && $w_wepk=='WG'))		//狙击鹰眼
	{
		if ($w_rage<20 || $w_lvl<3) return;
		$d1=round($damage*1.2); 
		$d2=round($eedmg*1.8); if ($w_rage<100 || $w_lvl<15) $d2=0;
		if ($rat>0.4 && max($d1+$eedmg,$damage+$d2)-$damage-$eedmg<80 && max($d1+$eedmg,$damage+$d2)<$hp) return;	//照样打不死，效果也不强，不放技能
		if ($d1+$eedmg>=$damage+$d2)
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">50</span>点怒气，对你发动了技能「瞄准」！</span><br>";
			$log_tail="<span class=\"red\">瞄准使物理伤害变为120%！</span><br>";
			$damage=$d1; $w_rage-=20;
		}
		else
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">100</span>点怒气，对你发动了技能「咆哮」！</span><br>";
			$log_tail="<span class=\"red\">咆哮使属性伤害变为180%，并使你陷入了多种异常状态！</span><br>";
			$eedmg=$d2; $w_rage-=100;
			global $inf;
			$inf = str_replace('b','',$inf);
			$inf = str_replace('h','',$inf);
			$inf = str_replace('a','',$inf);
			$inf = str_replace('f','',$inf);
			$inf = str_replace('u','',$inf);
			$inf=$inf.'bhafu';
		}
		$log=$log_head.$log.$log_tail;
		return;
	}
	if ($w_club==7)		//锡安成员
	{
		if ($w_rage<70 || $w_lvl<15) return;
		if ($rat>0.4 && $damage<300 && round($damage*1.35)+$eedmg<$hp) return;	//照样打不死，效果也不强，不放技能
		$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">70</span>点怒气，对你发动了技能「电网」！</span><br>";
		$log_tail="<span class=\"red\">电网使物理伤害变为135%，并使你暂时难以行动！</span><br>";
		global $inf,$sp;
		$nf=rand(1,4);
		if ($nf==1){
			$inf=str_replace('b','',$inf);
			$inf=$inf.'b';
		}elseif ($nf==2){
			$inf=str_replace('h','',$inf);
			$inf=$inf.'h';
		}elseif ($nf==3){
			$inf=str_replace('a','',$inf);
			$inf=$inf.'a';
		}elseif ($nf==4){
			$inf=str_replace('f','',$inf);
			$inf=$inf.'f';
		}
		$damage=round($damage*1.35); $sp=1; $w_rage-=70;
		$log=$log_head.$log.$log_tail;
		return;
	}
	if ($w_club==11)		//富家子弟
	{
		global $w_money;
		if ($w_rage<30 || $w_lvl<7) return;
		$sxd=floor($w_money/20)+1;
		if ($sxd>250) $sxd=250;
		$d1=$damage+$sxd;
		if ($rat>0.4 && $d1-$damage<60 && $d1+$eedmg<$hp) return;	//照样打不死，效果也不强，不放技能
		$damage=$d1; $w_rage-=30;
		$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">30</span>点怒气，对你发动了技能「威压」！</span><br>";
		$log_tail="<span class=\"red\">威压附加了{$sxd}点伤害！</span><br>";
		$log=$log_head.$log.$log_tail;
		return;
	}
	if ($w_club==6)		//宛如疾风
	{
		//天变太复杂，不放
		return;
	}
	if ($w_club==19)		//踏雪无痕
	{
		//踏雪无痕需要在defend()内部判定（暂未实现）
		//实现思路：先模拟打一次，如果当前打出的伤害打不死，但×2后可以打死，就放
		//太懒不实现了
		return;
	}
	if ($w_club==23)		//铁拳无敌
	{
		global $w_wp;
		if ($w_rage<30 || $w_lvl<7) return;
		$extd=floor($w_wp/5)+1;
		$d1=$damage+$extd;
		if ($rat>0.4 && $extd>100 && $w_rage<60 && $d1+$eedmg<$hp) return;	//如果有超过60怒气果断放掉泄怒，否则打不死就不放，这样只留一次技能的怒气打暴击
		$damage=$d1; $w_rage-=30;
		$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">50</span>点怒气，对你发动了技能「乱击」！</span><br>";
		$log_tail= "<span class=\"yellow\">乱击附加了{$extd}点额外伤害！</span><br>";
		$log=$log_head.$log.$log_tail;
		return;
	}
	if ($w_club==14)		//健美兄贵
	{
		//压制损伤自己太厉害，有可能被恶意利用，不放
		return;
	}
	if ($w_club==18)		//天赋异秉
	{
		//解构
		if ($w_rage<60 || $w_lvl<11) return;
		global $mhp,$hp;
		$extd=$mhp-$hp;
		$d1=$damage+$extd;
		if ($rat>0.4 && $d1-$damage<100 && $d1+$eedmg<$hp) return;	//照样打不死，效果也不强，不放技能
		//if ($secstrike) return;			
		$damage=$d1; $w_rage-=60;
		$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">60</span>点怒气，对你发动了技能「解构」！</span><br>";
		$log_tail= "<span class=\"yellow\">解构附加了{$extd}点额外伤害！</span><br>";
		$log=$log_head.$log.$log_tail;
		//吞噬由于万恶的虚子把sktime放到user表里了，无法实现只放一次，不放
		return;
	}
	if ($w_club==10)		//高速成长
	{
		//本能
		if ($w_rage<40 || $w_lvl<3) return;
		$pper=100+$w_lvl;
		$d1=round(($damage+$eedmg)*$pper/100);
		if ($rat>0.4 && $d1-$damage-$eedmg<60 && $d1<$hp) return;	//照样打不死，效果也不强，不放技能
		$damage=round($damage*$pper/100); $eedmg=round($eedmg*$pper/100); $w_rage-=40;
		$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">40</span>点怒气，对你发动了技能「本能」！</span><br>";
		$log_tail= "<span class=\"yellow\">本能使最终伤害变为<span class=\"red\">$pper</span>%</span><br>";
		$log=$log_head.$log.$log_tail;
		//主宰由于同样的原因放不出来，真是令人悲伤
		return;
	}
	if ($w_club==9)		//超能力者
	{
		//这里设定超能力者可以无限放充能
		$useskill='';
		if ($w_lvl>=19)
		{
			if ($w_rage<60) $useskill='recharge'; else $useskill='innerfire';
		}
		else  if ($w_lvl>=11)
		{
			if ($w_rage<40) $useskill='recharge'; else $useskill='crit';
		}
		else  if ($w_lvl>=3)
		{
			if ($w_rage>=40) $useskill='crit';
		}
		
		if ($useskill=='') return;
		if ($useskill=='innerfire' && $eedmg<60 && (($damage+$eedmg)*2<$hp || $damage*2+$eedmg>=$hp)) $useskill='crit';
		if ($useskill=='recharge' && $w_rage<150)
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">对你发动了技能「充能」！</span><br>";
			$log_tail="<span class=\"red\">$w_name</span><span class=\"yellow\">获得了100点怒气！</span><br>";
			$log=$log_head.$log.$log_tail;
			$w_rage+=100;
			return;
		}
		if ($useskill=='crit')
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">40</span>点怒气，对你发动了技能「必杀」！</span><br>";
			$log_tail="<span class=\"yellow\">必杀使物理伤害变为<span class=\"red\">200</span>%</span><br>";
			$log=$log_head.$log.$log_tail;
			$damage*=2; $w_rage-=40;
			return;
		}
		if ($useskill=='innerfire')
		{
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">60</span>点怒气，对你发动了技能「心火」！</span><br>";
			$log_tail="<span class=\"yellow\">心火使最终伤害变为<span class=\"red\">200</span>%</span><br>";
			$log=$log_head.$log.$log_tail;
			$damage*=2; $eedmg*=2; $w_rage-=60;
			return;
		}
		return;
	}
	if ($w_club==8)		//黑衣组织
	{
		//毒刺
		if ($w_rage<40 || $w_lvl<11) return;
		//只要对方没中毒就放
		global $inf;
		if (strpos($inf,'P')===false)
		{
			$inf.='P'; 
			$inf = str_replace('p','',$inf);	
			$log_head="<span class=\"red\">$w_name</span><span class=\"lime\">消耗<span class=\"yellow\">40</span>点怒气，对你发动了技能「毒刺」！</span><br>";
			$log_tail= "毒刺技能使你进入了<span class=\"purple\">猛毒</span>状态！<br>";
			$log=$log_head.$log.$log_tail;
			$w_rage-=40;
		}
		//暗杀由于同样的原因放不出来
		return;
	}
}

function defend($w_wep_kind = 'N', $active = 0,$bsk='') {
	global $now, $nosta, $log, $infobbs, $infinfo, $attinfo, $skillinfo,  $wepimprate,$specialrate,$sp,$rp;
	global $w_name, $w_lvl, $w_gd, $w_pid, $pls, $w_hp, $w_sp, $w_rage, $w_exp, $w_club, $w_att,$w_def, $w_inf,$w_mhp;
	global $w_wep, $w_wepk, $w_wepe, $w_weps, $w_wepsk;
	global $arbe, $arbsk, $arhe, $arae, $arfe,$wepk,$wepe;
	global $w_art, $w_arte,$w_artk, $w_arhsk, $w_arbsk, $w_arask, $w_arfsk, $w_artsk;
	global $hp, $rage, $lvl, $pid, $gd, $name, $inf, $att, $def, $club;
	global $wepsk, $arhsk, $arask, $arfsk, $artsk, $artk;
	global $w_type, $w_sNo, $w_killnum,$mhp, $type, $sNo;
	global $w_wp,$w_wk,$w_wc,$w_wg,$w_wf,$w_wd,$w_skills,$skills,$skillpoint,$w_skillpoint,$w_rp;
	global $souls,$w_souls,$debuffa,$debuffb,$debuffc,$w_debuffa,$w_debuffb,$w_debuffc;
	global $gemname,$gemstate,$gempower,$gemexp,$gemlvl,$w_gemname,$w_gemstate,$w_gempower,$w_gemexp,$w_gemlvl;
	global $w_cdowner,$cursedsouls;	
	global $db,$tablepre;
	
	$wt=-1;
	
	if(strpos($bsk,'aurora')!==false){
		$wt=ltrim($bsk,'aurora');
	}	
	
	$hasi=0;$w_hasi=0;
	if ($club==26){
		$haa=$name.'语录';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$haa' AND type>0 AND pls='$pls' AND hp>0");
		$hasi = $db->num_rows($result);
	}
	if ($w_club==26){
		$haw=$w_name.'语录';
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$haw' AND type>0 AND pls='$pls' AND hp>0");
		$w_hasi = $db->num_rows($result);
	}
	
	$x_temp_log=$log;	//这是为了NPC放技能作弊…… 所以在return之前一！定！要！记得写$log=$x_temp_log.$log！
	$log='';
	
	//npc_changewep();
	$watt=-1;
	$w_wep_temp = $w_wep;
	$is_wpg = false;
	if (((strpos ( $w_wepk, 'G' ) == 1)||(strpos($w_wepk,'J')==1)) && ($w_wep_kind == 'P')) {
		$watt = round ( $w_wepe / 5 );
		$is_wpg = true;
	} 
	
	$log .= "{$w_name}使用{$w_wep}<span class=\"yellow\">$attinfo[$w_wep_kind]</span>你！<br>";
	
	$w_att_key = getatkkey ( $w_wepsk, $w_arhsk, $w_arbsk, $w_arask, $w_arfsk, $w_artsk, $w_artk, $is_wpg );
	
	if (($w_club==23)&&($w_lvl>=15)&&(rand(1,100)<=30)){
		$w_att_key=str_replace('r','',$att_key);
		$w_att_key=$w_att_key.'r';
	}
	if(($w_type==30)&&($w_name=="霜火协奏曲")){
		$inf=str_replace('e','',$inf);
		$inf=$inf.'e';
		$log .= "<span class=\"red\">霜火还没动手，他身上散发的触压就导致你麻痹了！</span><br>";		
	}
	
	$def_key = getdefkey ( $wepsk, $arhsk, $arbsk, $arask, $arfsk, $artsk, $artk );
	if(strpos($w_att_key,'R')!==false){//随机伤害无视一切伤害计算
		$maxdmg = $mhp > $wepe ? $wepe : $mhp;
		$damage = rand(1,$maxdmg);
		$log .= "武器随机造成了<span class=\"red\">$damage</span>点伤害！<br>";
	}
	
	$mdr = $skdr = $sldr = false;
	$dwcnt=1;
	if(strpos($w_att_key.$def_key,'-')!==false){$mdr = true;$dwcnt++;}//精抽
	if(strpos($w_att_key.$def_key,'*')!==false){$sldr = true;$dwcnt++;}//魂抽
	if(strpos($w_att_key.$def_key,'+')!==false){$skdr = true;$dwcnt++;}//技抽
	if (($w_wep=='Solidarity')||($w_wep=='M240通用机枪')) {$mdr = $skdr = $sldr = false;}
	if($mdr || $sldr || $skdr){
		list($wsk,$hsk,$bbsk,$ask,$fsk,$tsk,$tk)=Array($wepsk, $arhsk, $arbsk, $arask, $arfsk, $artsk, $artk);
		list($wwsk,$whsk,$wbsk,$wask,$wfsk,$wtsk,$wtk)=Array( $w_wepsk, $w_arhsk, $w_arbsk, $w_arask, $w_arfsk, $w_artsk, $w_artk);
		if($mdr){
			$log .= "<span class=\"yellow\">精神抽取使双方的防具属性全部失效！</span><br>";
			$hsk = $bbsk = $ask = $fsk = $whsk = $wbsk = $wask = $wfsk = '';
		}
		if($sldr){
			$log .= "<span class=\"yellow\">灵魂抽取使双方的武器和饰物属性全部失效！</span><br>";
			$wsk = $tsk = $tk = $wwsk = $wtsk = $wtk = '';
		}
		if($skdr){
			$log .= "<span class=\"yellow\">技能抽取使双方的武器熟练度在战斗中大幅下降！</span><br>";
			//$bbsk = $ask = $fsk = $wbsk = $wask = $wfsk = '';
		}
		$w_att_key = getatkkey ( $wwsk,$whsk,$wbsk,$wask,$wfsk,$wtsk,$wtk, $is_wpg );
		$def_key = getdefkey ( $wsk,$hsk,$bbsk,$ask,$fsk,$tsk,$tk );
	}

//鱼弹	
global $arb, $arbk, $arbe, $arbs;
global $arh, $arhk, $arhe, $arhs;
global $ara, $arak, $arae, $aras;
global $arf, $arfk, $arfe, $arfs;
global $art, $artk, $arte, $arts;
global $itmk0, $itme0, $itms0, $itmsk0;
global $itm1, $itmk1, $itme1, $itms1, $itmsk1;
global $itm2, $itmk2, $itme2, $itms2, $itmsk2;
global $itm3, $itmk3, $itme3, $itms3, $itmsk3;
global $itm4, $itmk4, $itme4, $itms4, $itmsk4;
global $itm5, $itmk5, $itme5, $itms5, $itmsk5;
global $itm6, $itmk6, $itme6, $itms6, $itmsk6;
global $money,$exp;

if (($w_type==32)&&($w_name=='冰冻青蛙')){
	$log .= "<span class=\"yellow\">D20旋转了起来，你感到一阵眩晕！</span><br>";
	$log .= "<span class=\"yellow\">回过神来，你发现你的防具和物品出现了损坏，生命上限也减少了！</span><br>";
		$destroy_dice=rand(1,4);
		if($destroy_dice == 1){
			$log .= "<span class=\"red\">你的身体防具损坏了！</span><br>";
			$arb = ''; $arbk = ''; $arbe = 0; $arbs = 0; $arbsk = '';
		}elseif($destroy_dice == 2){
			$log .= "<span class=\"red\">你的头部防具损坏了！</span><br>";
			$arh = ''; $arhk = ''; $arhe = 0; $arhs = 0; $arhsk = '';
		}elseif($destroy_dice == 3){
			$log .= "<span class=\"red\">你的手部防具损坏了！</span><br>";
			$ara = ''; $arak = ''; $arae = 0; $aras = 0; $arask = '';
		}elseif($destroy_dice == 4){
			$log .= "<span class=\"red\">你的足部防具损坏了！</span><br>";
			$arf = ''; $arfk = ''; $arfe = 0; $arfs = 0; $arfsk = '';
		}
		$destroy2_dice=rand(1,6);
		if($destroy2_dice == 1){
			$log .= "<span class=\"yellow\">你的{$itm6}损坏了！</span><br>";
			$itm6 = ''; $itmk6 = ''; $itme6 = 0; $itms6 = 0; $itmsk6 = '';
		}elseif($destroy2_dice == 2){
			$log .= "<span class=\"yellow\">你的{$itm1}损坏了！</span><br>";
			$itm1 = ''; $itmk1 = ''; $itme1 = 0; $itms1 = 0; $itmsk1 = '';
		}elseif($destroy2_dice == 3){
			$log .= "<span class=\"yellow\">你的{$itm2}损坏了！</span><br>";
			$itm2 = ''; $itmk2 = ''; $itme2 = 0; $itms2 = 0; $itmsk2 = '';
		}elseif($destroy2_dice == 4){
			$log .= "<span class=\"yellow\">你的{$itm3}损坏了！</span><br>";
			$itm3 = ''; $itmk3 = ''; $itme3 = 0; $itms3 = 0; $itmsk3 = '';
		}elseif($destroy2_dice == 5){
			$log .= "<span class=\"yellow\">你的{$itm4}损坏了！</span><br>";
			$itm4 = ''; $itmk4 = ''; $itme4 = 0; $itms4 = 0; $itmsk4 = '';
		}elseif($destroy2_dice == 6){
			$log .= "<span class=\"yellow\">你的{$itm5}损坏了！</span><br>";
			$itm5 = ''; $itmk5 = ''; $itme5 = 0; $itms5 = 0; $itmsk5 = '';
		}
		$de=rand(1,160);
		if ($de>=$mhp) $de=$mhp-1;
		$mhp=$mhp-$de;
		if ($hp>$mhp) $hp=$mhp;
}

if (($w_name=='甲斐 山诚')&&($w_type)){
	$event_dice=rand(1,100);
	if($event_dice <=30){
		global $wep,$wepk,$wepe,$weps,$wepsk;
		global $ara,$arak,$arae,$aras,$arask;
		global $arf,$arfk,$arfe,$arfs,$arafk;
		global $art,$artk,$arte,$arts,$artsk;
		$log .= "<span class=\"yellow\">甲斐凭借着丰富的经验预判了你的行动，并且准确无误地对你的手脚以及穿戴的装备进行器械磨损性攻击！</span><br>";
		$damage=rand(5,40);
		if(($weps !=0)&&($weps !='∞')){
			$weps-=$damage;
			$log .= "攻击使得<span class=\"red\">$wep</span>的耐久度下降了<span class=\"red\">$damage</span>点！<br>";
			if($weps <= 0){
				$log .= "<span class=\"red\">$wep</span>被彻底破坏了！<br>";
				$wep = $wepk = $wepsk ='';
				$wepe = $weps =0;
			}
		}
		if(($aras !=0)&&($aras !='∞')){
			$aras-=$damage;
			$log .= "攻击使得<span class=\"red\">$ara</span>的耐久度下降了<span class=\"red\">$damage</span>点！<br>";
			if($aras <= 0){
				$log .= "<span class=\"red\">$ara</span>被彻底破坏了！<br>";
				$ara = $arak = $arask ='';
				$arae = $aras =0;
			}
		}
		if(($arfs !=0)&&($arfs !='∞')){
			$arfs-=$damage;
			$log .= "攻击使得<span class=\"red\">$arf</span>的耐久度下降了<span class=\"red\">$damage</span>点！<br>";
			if($arfs <= 0){
				$log .= "<span class=\"red\">$arf</span>被彻底破坏了！<br>";
				$arf = $arfk = $arfsk ='';
				$arfe = $arfs =0;
			}
		}
		if(($arts !=0)&&($arts !='∞')){
			$arts-=$damage;
			$log .= "攻击使得<span class=\"red\">$art</span>的耐久度下降了<span class=\"red\">$damage</span>点！<br>";
			if($arts <= 0){
				$log .= "<span class=\"red\">$art</span>被彻底破坏了！<br>";
				$art = $artk = $artsk ='';
				$arte = $arts =0;
			}
		}
		$inf.='a';
		$inf.='f';
		$log .= "致伤攻击使你的<span class=\"red\">腕部</span>和<span class=\"red\">足部</span>受伤了！<br>";
	}
}

if (($w_name=='條原 乙妃')&&($w_type)){
	$event_dice=rand(1,100);
	if($event_dice <=30){
		$log .= "<span class=\"yellow\">“解构分析完成！来尝尝本小姐的最新研究成果！”條原的某种指令启动了周围隐藏的科技武器！</span><br>";
		$log .= "<span class=\"yellow\">你被不可思议的射线照射了！！</span><br>";
		$event_dice2=rand(1,100);
		if($event_dice2 <=30){
			$mhp = $hp;
			$log .= "科学射线影响了你的<span class=\"red\">生命上限</span>！<br>";
		}else{
			$log .= "科学射线似乎对你没有什么影响。<br>";
			$log .= "“诶诶诶！哪里弄错了吗？！讨厌！！”<br>";
		}
	}
}

if (($w_name=='无聊')&&($w_type)){
	$event_dice=rand(1,100);
	if($event_dice <=30){
		$log .= "<span class=\"yellow\">“制空权已确保！立即对敌进行饱和空袭攻击！”无聊大手一挥作出了战斗指示！</span><br>";
		$log .= "<span class=\"yellow\">数架无人轰炸机从低空呼啸而过，炮火淹没了你！！</span><br>";
			$damage=round($mhp*0.3);
			$log .= "轰炸机空袭造成<span class=\"red\">$damage</span>点伤害！<br>";
			checkdmg ( $w_name, $name, $damage );
			$hp-=$damage;
	}
}

if (($w_name=='数纳 步')&&($w_type)){
	//$log .= "<span class=\"yellow\">北大路抬起手，掌心朝向你，你感到一阵眩晕！</span><br>";
	//$log .= "<span class=\"yellow\">回过神来，你发现你因为受到了超能力的影响，生命上限减少了！</span><br>";
	$up=rand(1,4);
	$w_mhp+=$up;
	$event_dice=rand(1,5);
	if(event_dice<=2){
		$log .= "<span class=\"yellow\">数纳显然是做好了伏击的准备！</span><br>";
		$log .= "<span class=\"yellow\">只见他攻击之余按下了遥控器的按钮，你身边的区域便被强烈的爆炸覆盖！</span><br>";
		$damage=round($mhp/2);
		$log .= "伏击的遥控炸弹造成<span class=\"red\">$damage</span>点伤害！<br>";
		checkdmg ( $w_name, $name, $damage );
		$hp-=$damage;
	}
}

if (($w_name=='北大路 真')&&($w_type)){
	$log .= "<span class=\"yellow\">北大路抬起手，掌心朝向你，你感到一阵眩晕！</span><br>";
	$log .= "<span class=\"yellow\">回过神来，你发现你因为受到了超能力的影响，生命上限减少了！</span><br>";
		$de=rand(1,160);
		if ($de>=$mhp) $de=$mhp-1;
		$mhp=$mhp-$de;
		if ($hp>$mhp) $hp=$mhp;
}

if (($w_name=='五十铃川 兼元')&&($w_type)){
	$log .= "<span class=\"yellow\">五十铃川身边的剑不可思议地飞舞着冲向你！</span><br>";
		if($w_wep!='三日月宗近'){
			$event_dice=rand(1,2);
			if(event_dice==1){
				$log .= "<span class=\"yellow\">三日月宗近斩刺你！造成了<span class=\"red\">250</span>点伤害！并且使你<span class=\"red\">麻痹</span>了！</span><br>";
				$hp-=250;
				if($hp < 0) $hp=0;
				$inf.='e';
			}
		}
		if($w_wep!='大典太'){
			$event_dice=rand(1,2);
			if(event_dice==1){
				$log .= "<span class=\"yellow\">大典太斩刺你！造成了<span class=\"red\">250</span>点伤害！并且使你<span class=\"red\">冻结</span>了！</span><br>";
				$hp-=250;
				if($hp < 0) $hp=0;
				$inf.='i';
			}
		}
		if($w_wep!='鬼丸国纲'){
			$event_dice=rand(1,2);
			if(event_dice==1){
				$log .= "<span class=\"yellow\">鬼丸国纲斩刺你！造成了<span class=\"red\">250</span>点伤害！并且使你<span class=\"red\">点燃</span>了！</span><br>";
				$hp-=250;
				if($hp < 0) $hp=0;
				$inf.='u';
			}
		}
		if($w_wep!='小乌丸天国'){
			$event_dice=rand(1,2);
			if(event_dice==1){
				$log .= "<span class=\"yellow\">小乌丸天国斩刺你！造成了<span class=\"red\">250</span>点伤害！并且使你<span class=\"red\">混乱</span>了！</span><br>";
				$hp-=250;
				if($hp < 0) $hp=0;
				$inf.='w';
			}
		}
		if($w_wep!='数珠丸恒次'){
			$event_dice=rand(1,2);
			if(event_dice==1){
				$log .= "<span class=\"yellow\">数珠丸恒次斩刺你！造成了<span class=\"red\">250</span>点伤害！并且使你<span class=\"red\">中毒</span>了！</span><br>";
				$hp-=250;
				if($hp < 0) $hp=0;
				$inf.='p';
			}
		}
}
if($w_wep=='无后座力迷你加特林'){
	if (($w_name=='米可')&&($w_type)){
		$damage=0;
		if(($w_weps>0)&&($w_weps!=$nosta)){
			$log.="<span class=\"linen\">“盛宴～要开始咯～”</span><br>";
			while($w_weps>0){
				$hit_count=0;$round_damage=0;$max_hits=($w_weps>10)?10:$w_weps;
				for($i=1;$i<=$max_hits;$i++){
					$rand_dice=rand(1,10);
					if($rand_dice<=7){
						$hit_count++;
						$round_damage+=$w_wepe+$w_killnum;
					}
					$w_weps--;
				}
				
				$log .= "{$max_hits}次连续攻击命中<span class=\"yellow\">{$hit_count}</span>次！<br>";
				$log .= "造成{$w_wepe}×{$hit_count}＝<span class=\"red\">{$round_damage}</span>点伤害！<br>";
				$damage+=$round_damage;
			}
			//$w_weps=100;//用于测试时免去装填子弹的回合
			$log .= "<span class=\"yellow\">一连串的扫射最终造成了总共<span class=\"red\">{$damage}</span>点伤害！{$w_name}的{$w_wep}子弹用光了！</span><br>";
			if($w_weps==0){$w_weps=$nosta;}
			$hp-=$damage;
			checkdmg($w_name,$name,$damage);
			if($hp<=0){
				$w_killnum ++;
				include_once GAME_ROOT . './include/state.func.php';
				$killmsg = death ( $w_wep_kind, $w_name, $w_type, $w_wep_temp );
				$log .= npc_chat ( $w_type,$w_name, 'kill' );
			}
			$log = $x_temp_log.$log;	
			return $damage;
		}else{
			//加特林没子弹
			//NPC先攻的话在反击时不会装子弹
			$log.="<span class=\"linen\">“这个要装子弹真是麻烦呢～待会就给你们好看喔～”</span><br>";
			$w_weps=100;
		}
	}else{
		//NPC米可以外的人使用加特林
		$log .= "<span class=\"yellow\">对方的武器似乎卡壳了，没有发挥出原本该有的性能！</span><br>";
	}
}

if(($w_wep=='深渊百科全书真本') && ($w_name=='书库联结 书库娘') && ($w_type)){
	$log .= "<span class=\"yellow\">书库娘手往前一推，散发光芒的百科全书朝向你快速翻动着书页！</span><br>
		<span class=\"linen\">“这样的战斗可是超有价值的哦！展开吧！无限书库的写入程式！”</span><br>";
	$rand_dice=rand(1,666)*($w_killnum+1);
	if($rand_dice>=666){
	//死了
					$log .= "<span class=\"yellow\">书库娘的百科全书散发的光芒把你整个人包覆了！</span><br>
					<span class=\"linen\">“哦哦哦！竟然整个都收录啦！超Lucky～”</span><br>
					<span class=\"yellow\">你被收录了！</span><br>";
					$w_killnum ++;
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'wikicollect',$w_name ,$w_type );
					$log=$x_temp_log.$log;
					return 0;
	}else{
		$rand_dice2=rand(1,100);
		if($rand_dice2<=80){
			$log .= "<span class=\"yellow\">书库娘的百科全书散发的光芒竟毫无预兆地聚焦到了你手持的武器上！</span><br>
				<span class=\"linen\">“喵哈哈哈哈！收录到啦！我要赶紧试试效果！”</span><br>
				<span class=\"yellow\">你的武器性质被书库娘收录了！</span><br>";
			$w_wepk=$wepk;	$w_wepe=$wepe;	$w_wepsk=$wepsk;
			if(($weps==$nosta)||($weps<=20)){
				$w_weps=200;
			}else{
				$w_weps=$weps;
			}
		}else{
			$log .= "<span class=\"yellow\">书库娘的百科全书散发的光芒往四周消散了。</span><br>
				<span class=\"linen\">“呜喵！没收录到！不过不要紧，还有的是机会！”</span><br>";
		}
	}
}

if(($w_wep=='深水鱼弹-金鲤型')&&($w_type)&&(!$type)){
	$log .= "<span class=\"yellow\">深水鱼弹在击中你的瞬间爆炸了！</span><br>";
		$log .= "<span class=\"yellow\">鱼弹引发的爆炸使你的防具损坏了！</span><br>";
		$destroy_dice=rand(1,5);
		if($destroy_dice == 1){
			$log .= "<span class=\"red\">你的身体防具损坏了！</span><br>";
			$arb = ''; $arbk = ''; $arbe = 0; $arbs = 0; $arbsk = '';
		}elseif($destroy_dice == 2){
			$log .= "<span class=\"red\">你的头部防具损坏了！</span><br>";
			$arh = ''; $arhk = ''; $arhe = 0; $arhs = 0; $arhsk = '';
		}elseif($destroy_dice == 3){
			$log .= "<span class=\"red\">你的手部防具损坏了！</span><br>";
			$ara = ''; $arak = ''; $arae = 0; $aras = 0; $arask = '';
		}elseif($destroy_dice == 4){
			$log .= "<span class=\"red\">你的足部防具损坏了！</span><br>";
			$arf = ''; $arfk = ''; $arfe = 0; $arfs = 0; $arfsk = '';
		}elseif($destroy_dice == 5){
			$log .= "<span class=\"red\">你的饰品损坏了！</span><br>";
			$art = ''; $artk = ''; $arte = 0; $arts = 0; $artsk = '';
		}
		
}elseif(($w_wep=='深水鱼弹-石斑型')&&($w_type)&&(!$type)){
	$log .= "<span class=\"yellow\">深水鱼弹在击中你的瞬间爆炸了！</span><br>";
		$log .= "<span class=\"yellow\">鱼弹引发的爆炸使你背包里的某个道具损坏了！</span><br>";
		$destroy2_dice=rand(1,6);
		if($destroy2_dice == 1){
			$log .= "<span class=\"yellow\">你的{$itm6}损坏了！</span><br>";
			$itm6 = ''; $itmk6 = ''; $itme6 = 0; $itms6 = 0; $itmsk6 = '';
		}elseif($destroy2_dice == 2){
			$log .= "<span class=\"yellow\">你的{$itm1}损坏了！</span><br>";
			$itm1 = ''; $itmk1 = ''; $itme1 = 0; $itms1 = 0; $itmsk1 = '';
		}elseif($destroy2_dice == 3){
			$log .= "<span class=\"yellow\">你的{$itm2}损坏了！</span><br>";
			$itm2 = ''; $itmk2 = ''; $itme2 = 0; $itms2 = 0; $itmsk2 = '';
		}elseif($destroy2_dice == 4){
			$log .= "<span class=\"yellow\">你的{$itm3}损坏了！</span><br>";
			$itm3 = ''; $itmk3 = ''; $itme3 = 0; $itms3 = 0; $itmsk3 = '';
		}elseif($destroy2_dice == 5){
			$log .= "<span class=\"yellow\">你的{$itm4}损坏了！</span><br>";
			$itm4 = ''; $itmk4 = ''; $itme4 = 0; $itms4 = 0; $itmsk4 = '';
		}elseif($destroy2_dice == 6){
			$log .= "<span class=\"yellow\">你的{$itm5}损坏了！</span><br>";
			$itm5 = ''; $itmk5 = ''; $itme5 = 0; $itms5 = 0; $itmsk5 = '';
		}
}elseif(($w_wep=='深水鱼干-咸鱼味')&&($w_type)&&(!$type)){
	$log .= "<span class=\"yellow\">深水鱼弹……不对鱼干……卧槽鱼松怎么也能当武器啊！？</span><br>";
		$log .= "<span class=\"yellow\">鱼干引发的爆炸使你一阵头晕目眩！</span><br>";
		$fish_dice=rand(1,3);
			if($fish_dice==1){
				$log .= "<span class=\"yellow\">鱼松引发的爆炸使你背包里的金钱也爆炸了！【这算什么啦！【摔！</span><br>";
				$damage=ceil($money / 12);
				$log .= "金钱爆炸造成<span class=\"red\">$damage</span>点伤害！<br>";
				checkdmg ( $w_name, $name, $damage );
				$hp-=$damage;
				$money=0;
				if($hp<=0){
					$log .= "<span class=\"yellow\">你被深水鱼松引发的爆炸效果炸死了！</span><br>";
					$w_killnum ++;
					$log .= npc_chat ( $w_type,$w_name, 'kill' );
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'fishbomb',$w_name ,$w_type );
					$log=$x_temp_log.$log;
					return $damage;
				}
			}elseif($fish_dice==2){
				$log .= "<span class=\"yellow\">鱼松引发的爆炸使你体内的经验也爆炸了！【这算什么啦！【摔！</span><br>";
				$damage=$exp;
				$log .= "经验爆炸造成<span class=\"red\">$damage</span>点伤害！<br>";
				checkdmg ( $w_name, $name, $damage );
				$hp-=$damage;
				$exp=0;
				if($hp<=0){
					$log .= "<span class=\"yellow\">你被深水鱼松引发的爆炸效果炸死了！</span><br>";
					$w_killnum ++;
					$log .= npc_chat ( $w_type,$w_name, 'kill' );
					include_once GAME_ROOT . './include/state.func.php';
					death ( 'fishbomb',$w_name ,$w_type );
					$log=$x_temp_log.$log;
					return $damage;
				}
			}elseif($fish_dice==3){	
				$log .= "<span class=\"yellow\">鱼松引发的爆炸产生了一阵毒气，不过看起来对你没什么影响！</span><br>";
				for($i = 1; $i <= 6; $i ++) {
				global ${'itm' . $i},${'itmk' . $i},${'itms' . $i},${'itme' . $i};
				if (strpos ( ${'itmk' . $i} , 'H' ) === 0) {
					${'itmk' . $i} = substr_replace(${'itmk' . $i},'P',0,1);
					}	
				}
			}
}
	if((strpos($w_artsk,'t')!==false)&&($w_type)&&(!$type)){
		if(($w_hp < $w_mhp)&&((rand(1,100)<=50)||($w_name=='埃尔兰卡'))){
			$w_hp = min($w_mhp,$w_hp+2000);
			$log .= "<span class=\"red\">$w_name</span>身上的复位属性发动了！<br><span class=\"deeppink\">奥术粒子环绕在了<span class=\"red\">$w_name</span>的身侧！</span><br>";
			if($w_hp>=$w_mhp){
				$log .= "<span class=\"yellow\">你所造成的损伤在复位的作用下消失了！</span><br>";
			}
		}
	}	
	
	if(($w_club==21)&&($w_type)&&($w_name=='虚子')&&(!$type)&&($w_wepe/2>=$mhp)&&(rand(1,100)<30)){
		$log .= "<span class=\"yellow\">对方忽然手持爆炸物劫持了你！并在你身上安装了炸弹！</span><br>";		
		global $artk,$arsk;
		$arb='捆绑式炸药';$arbs=8192;$arbe=1;$arbsk='dV';$arbk='DB';
		$art=$w_name.'的人质证明';$arts=1;$arte=1;$artsk='vV';$artk='DA';
		return;
	}
	
	if((strpos($w_att_key,'x')!==false)&&(!$type)&&($wep=='【余晖】')){
		$log.="<img src=\"img/other/afterglow.png\"><br>";
		$damage=8000;
		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		checkdmg ( $name, $w_name, $damage );
		$log=$x_temp_log.$log;
		return $damage;
	}
	
	if (($w_name=='佐佐木')&&($w_type==33)&&(rand(1,100)<8)){
		$damage=$mhp;
		if ($hp>$damage) {$damage=$hp;}
		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		global $w_teamID;
		checkdmg ( $w_name, $name, $damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
		$hp = 0;
		$w_killnum ++;
		include_once GAME_ROOT . './include/state.func.php';
		$killmsg = death ( $w_wep_kind, $w_name, $w_type, $w_wep_temp );
		$log .= npc_chat ( $w_type,$w_name, 'kill' );
		$log = $x_temp_log.$log;
		return $damage;
	}
	
	if(($w_name=='埃尔兰卡')&&($w_type==21)&&($w_wep=='虹光魔眼')){
		$rainbow_dice=rand(1,7);
		$rainbowdeath_dice=rand(1,100);
		$dad_flag=false;
		if($rainbow_dice==1){
			$log.='<span class="lime">一道绿光从魔眼中溅射出来，即死法术被触发了！</span><br>';
			if(($mhp<$w_mhp)||($rainbowdeath_dice>=50)){$log.='<span class="clan">你成功通过了意志鉴定，豁免了即死法术的效果！</span><br>';
			}else{
				$log.='<span class="clan">你未能通过意志鉴定，即死效果生效了！</span><br>';
				$damage=$mhp;
				if ($hp>$damage) {$damage=$hp;}
				$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
				global $w_teamID;
				checkdmg ( $w_name, $name, $damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
				$hp = 0;
				$w_killnum ++;
				include_once GAME_ROOT . './include/state.func.php';
				$killmsg = death ( $w_wep_kind, $w_name, $w_type, $w_wep_temp );
				$log .= npc_chat ( $w_type,$w_name, 'kill' );
				$log = $x_temp_log.$log;
				return $damage;
			}
		}elseif($rainbow_dice==2){
			$log.='<span class="red">一道红光从魔眼中溅射出来，火焰法术被触发了！</span><br>';
			if(($rainbowdeath_dice>=50)||(strpos($inf,'u')!==false)){$log.='<span class="clan">你成功通过了意志鉴定，豁免了火焰法术的效果！</span><br>';}
			else{$log.='<span class="red">你没能通过意志豁免，火焰法术生效了，你被<span class="red">点燃</span>了！</span><br>';
			$inf.='u';$dad_flag=true;
			}
		}elseif($rainbow_dice==3){
			$log.='<span class="yellow">一道黄光从魔眼中溅射出来，雷电法术被触发了！</span><br>';
			if(($rainbowdeath_dice>=50)||(strpos($inf,'e')!==false)){$log.='<span class="clan">你成功通过了意志鉴定，豁免了雷电法术的效果！</span><br>';}
			else{$log.='<span class="red">你没能通过意志豁免，雷电法术生效了，你被<span class="yellow">麻痹</span>了！</span><br>';
			$inf.='e';$dad_flag=true;
			}
		}elseif($rainbow_dice==4){
			$log.='<span class="orange">一道橙光从魔眼中溅射出来，酸液法术被触发了！</span><br>';
			if(($rainbowdeath_dice>=50)||(strpos($inf,'p')!==false)){$log.='<span class="clan">你成功通过了意志鉴定，豁免了酸液法术的效果！</span><br>';}
			else{$log.='<span class="red">你没能通过意志豁免，酸液法术生效了。你<span class="green">中毒</span>了！</span><br>';
			$inf.='p';$dad_flag=true;
			}
		}elseif($rainbow_dice==5){
			$log.='<span class="blue">一道蓝光从魔眼中溅射出来，石化法术被触发了！</span><br>';
			if($rainbowdeath_dice>=50){$log.='<span class="clan">你成功通过了意志鉴定，豁免了石化法术的效果！</span><br>';}
			else{$log.='<span class="red">你没能通过意志豁免，石化法术生效了。你被<span class="sienna">石化</span>了！</span><br>';
			$inf.='S';
			}
		}elseif($rainbow_dice==6){
			$log.='<span class="clan">一道青光从魔眼中溅射出来，恐惧法术被触发了！</span><br>';
			if($rainbowdeath_dice>=50){$log.='<span class="clan">你成功通过了意志鉴定，豁免了恐惧法术的效果！</span><br>';
			}else{
				$log.='<span class="red">你没能通过意志豁免，恐惧法术生效了。你受到了一次致命伤！</span><br>';
				$a=rand(1,100);$b=rand(1,400);$c_mhp=round($mhp*($a/$b));$mhp=min($c_mhp,$mhp);if($mhp<1){$mhp=1;}$hp=1;
				$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
				checkdmg ( $name, $w_name, $damage );
				return $damage;
			}
		}elseif($rainbow_dice==7){
			$log.='<span class="magenta">一道紫光从魔眼中溅射出来，传送法术被触发了！</span><br>';
			if($rainbowdeath_dice>=50){$log.='<span class="clan">你成功通过了意志鉴定，豁免了传送法术的效果！</span><br>';}
			else{$log.='<span class="red">你没能通过意志豁免，传送法术生效了。你周遭的空间开始扭曲……</span><br>';
			global $arealist,$pls,$plsinfo;
			$tp_pls=array_rand($arealist,1);
			$pls=$tp_pls;
			$log.="<span class='yellow'>你被传送到了{$plsinfo[$tp_pls]}！</span><br>";
			}
		}
		
		if((($rainbow_dice==2)||($rainbow_dice==3)||($rainbow_dice==4))&&($dad_flag)){
			$dadice=rand(1,5);
			if(($dadice==1)&&($arb!=='内衣')){$log.="<span class='red'>射线使你的{$arb}损坏了！</span><br>";$arbe=$arbs=0;$arb=$arbk=$arbsk='';}
			elseif(($dadice==2)&&($arh!=='')){$log.="<span class='red'>射线使你的{$arh}损坏了！</span><br>";$arhe=$arhs=0;$arh=$arhk=$arhsk='';}
			elseif(($dadice==3)&&($ara!=='')){$log.="<span class='red'>射线使你的{$ara}损坏了！</span><br>";$arae=$aras=0;$ara=$arak=$arask='';}
			elseif(($dadice==4)&&($arf!=='')){$log.="<span class='red'>射线使你的{$arf}损坏了！</span><br>";$arfe=$arfs=0;$arf=$arfk=$arfsk='';}
			elseif(($dadice==5)&&($art!=='')){$log.="<span class='red'>射线使你的{$art}损坏了！</span><br>";$arte=$arts=0;$art=$artk=$artsk='';}	
		}
	}
	
	if($w_wep=='茜草之节杖'){
		$log.='<span class="deeppink">对方手中的诅咒之杖指向了你！</span><br>';
		$cw_dice=rand(1,6);
		global $arbsk,$arhsk,$arask,$arfsk,$artsk;
		if(($cw_dice==1)&&($arb!=='')&&(strpos($arbsk,'V')===false)){$log.='<span class="red">你的身体防具被诅咒了！</span><br>';$arbsk.='V';}
		elseif(($cw_dice==2)&&($arh!=='')&&(strpos($arhsk,'V')===false)){$log.='<span class="red">你的头部防具被诅咒了！</span><br>';$arhsk.='V';}
		elseif(($cw_dice==3)&&($ara!=='')&&(strpos($arask,'V')===false)){$log.='<span class="red">你的手部防具被诅咒了！</span><br>';$arask.='V';}
		elseif(($cw_dice==4)&&($arf!=='')&&(strpos($arfsk,'V')===false)){$log.='<span class="red">你的足部防具被诅咒了！</span><br>';$arfsk.='V';}
		elseif(($cw_dice==5)&&($art!=='')&&(strpos($artsk,'V')===false)){$log.='<span class="red">你的饰品被诅咒了！</span><br>';$artsk.='V';}
		else{$log.='<span class="red">但是什么也没有发生！</span><br>';}
	}
	
	if ((strpos($w_att_key,"X")!==false)&&($w_type)&&(!$type)&&((rand(1,100)+30*$dwcnt)>=95)){
		if ($w_wep=='燕返262'){
			$log.="<img src=\"img/other/262.png\"><br>";
		}
		$damage=$mhp;
		if ($hp>$damage) {$damage=$hp;}
		$log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		global $w_teamID;
		checkdmg ( $w_name, $name, $damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
		$hp = 0;
		$w_killnum ++;
		include_once GAME_ROOT . './include/state.func.php';
		$killmsg = death ( $w_wep_kind, $w_name, $w_type, $w_wep_temp );
		$log .= npc_chat ( $w_type,$w_name, 'kill' );
		$log = $x_temp_log.$log;
		return $damage;
	}
	
	global ${'w_' . $skillinfo [$w_wep_kind]};
	$w_add_skill = & ${'w_' . $skillinfo [$w_wep_kind]};
	if ($w_club==18){
		if ($w_lvl<19){
			$w_wep_skill=round(${'w_' .$skillinfo [$w_wep_kind]}*0.7+($w_wp+$w_wk+$w_wc+$w_wg+$w_wd+$w_wf)*0.3);
		}else{
			$w_wep_skill=round(${'w_' .$skillinfo [$w_wep_kind]}*0.5+($w_wp+$w_wk+$w_wc+$w_wg+$w_wd+$w_wf)*0.5);
		}
	}elseif(($w_club==11)&&($w_lvl>=19)){
		$wep_skill=$w_wp;
		if ($w_wk>$w_wep_skill) $w_wep_skill=$w_wk;
		if ($w_wc>$w_wep_skill) $w_wep_skill=$w_wc;
		if ($w_wd>$w_wep_skill) $w_wep_skill=$w_wd;
		if ($w_wf>$w_wep_skill) $w_wep_skill=$w_wf;
		if ($w_wg>$w_wep_skill) $w_wep_skill=$w_wg;
	}else{
		$w_wep_skill=${'w_' .$skillinfo [$w_wep_kind]};
	}
	if($skdr){
		$w_wep_skill=sqrt($w_wep_skill);
	}
	
	if ($watt==-1){
		if ($w_wep_kind == 'N') {
			$watt =  round ($w_wp*2/3);
			if ($w_club==23) ($watt=$watt*3/2);
		} else {
			$watt = $w_wepe * 2;
		}
	}
	

	
	$hitrate = get_hitrate ( $w_wep_kind, $w_wep_skill, $w_club, $w_inf );
	include_once GAME_ROOT.'./include/game/clubskills.func.php';
	$hitrate *= get_clubskill_bonus_hitrate($w_club,$w_skills,'w_',$club,$skills,'');
	if ($w_club==19) $hitrate*=1.2;
	
	global $w_club, $w_rage;
			
	$old_crit=0;
	if ($w_club==0 && $w_rage>=10 && rand(1,2)==1)
	{
		$w_rage-=10;
		$log.="<span class=\"red\">${w_name}发出了重击！</span><br>";
		$old_crit=1;
	}
			
	$damage_p = get_damage_p ( $w_rage, $w_att_key, $w_type, $w_name , $w_club);
	$hit_time = get_hit_time ( $w_att_key, $w_wep_skill, $hitrate, $w_wep_kind, $w_weps, $infobbs [$w_wep_kind] * get_clubskill_bonus_imfrate($w_club,$w_skills,'w_',$club,$skills,''), get_clubskill_bonus_imftime($w_club,$w_skills,'w_',$club,$skills,''), $wepimprate[$w_wep_kind] * get_clubskill_bonus_imprate($w_club,$w_skills,'w_',$club,$skills,''), $is_wpg, get_clubskill_bonus_hitrate($w_club,$w_skills,'w_',$club,$skills,''),$w_club ,$w_lvl);
	
	if ($hit_time [1] > 0) {
		$gender_dmg_p = check_gender ( $w_name, '你', $w_gd, $gd, $w_att_key );
		if ($gender_dmg_p == 0) {
			$damage = 1;
		} else {
			global $w_att, $w_auraa, $now, $w_debuffc;
			$w_active = 1 - $active;
			$t_w_att=$w_att;
			if ($now<=$w_auraa) $t_w_att*=0.7;
			if ($now<=$w_debuffc) $t_w_att*=0.4;
			$attack = $t_w_att + $watt;
			$defend = checkdef($def , $arbe + $arhe + $arae + $arfe,$w_att_key);
		
			$damage = get_original_dmg ( 'w_', '', $attack, $defend, $w_wep_skill, $w_wep_kind );
			
			if ($w_wep_kind == 'F') {
				if($sldr){
					$log.="<span class=\"red\">由于灵魂抽取的作用，灵系武器伤害大幅降低了！</span><br>";
				}else{
					$damage = round ( ($w_wepe + $damage) * get_WF_p ( 'w_', $w_club, $w_wepe) ); //get_spell_factor ( 1, $w_club, $w_att_key, $w_sp, $w_wepe ) );
				}
				
				
			}
			if ($w_wep_kind == 'J') {
				$adddamage=$mhp/3;
				if ($adddamage>20000) {$adddamage=10000;}
				$damage +=round($w_wepe*2/3+$adddamage);
			}
			checkarb ( $damage, $w_wep_kind, $w_att_key, $def_key );
			$damage *= $damage_p;
			
			$damage = $damage > 1 ? round ( $damage ) : 1;
			$damage *= $gender_dmg_p;
		}
		if ($wepk=='WJ'){
			$log.="<span class=\"red\">由于你手中的武器过于笨重，受到的伤害大增！真是大快人心啊！</span><br>";
			$damage+=round($damage*0.5);
		}
		
		if ($hit_time [1] > 1) {
			$d_temp = $damage;
			if ($hit_time [1] == 2) {
				$dmg_p = 2;
			} elseif ($hit_time [1] == 3) {
				$dmg_p = 2.8;
			} else {
				$dmg_p = 2.8 + 0.6 * ($hit_time [1] - 3);
			}
			//$dmg_p = $hit_time[1] - ($hit_time[1]-1)*0.2;
			$damage = round ( $damage * $dmg_p );
			if ($old_crit)
			{
				$damage=round($damage*1.5);
				$log .= "造成{$d_temp}×{$dmg_p}×1.5＝<span class=\"red\">$damage</span>点伤害！<br>";
			}
			else  $log .= "造成{$d_temp}×{$dmg_p}＝<span class=\"red\">$damage</span>点伤害！<br>";
		} else {
			if ($old_crit)
			{
				$d_temp=$damage; $damage=round($damage*1.5);
				$log .= "造成{$d_temp}×1.5＝<span class=\"red\">$damage</span>点伤害！<br>";
			}
			else  $log .= "造成<span class=\"red\">$damage</span>点伤害！<br>";
		}
		$pdamage = $damage;
		
		//物理伤害计算处
		
		if(($w_gemname=='红宝石')&&($w_gemstate==2)){
			include_once GAME_ROOT . './include/game/gem.func.php';
			$raisedmg=w_magic_gem('红宝石');		
			$lose_w_gem=round($damage*$raisedmg/100);
			if(($w_club==49)||($w_club==53)){$log.="<span class='red'>【研究】使对方的宝石魔法效果提高了25%！</span><br>";}
			if($lose_w_gem<=$w_gempower){
				$damage+=$lose_w_gem;$w_gempower-=$lose_w_gem;$w_gemexp+=round($lose_w_gem/1.25);
				$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法使物理伤害增加了{$raisedmg}%！</span><br>";
			}elseif(($lose_w_gem>$w_gempower)&&(($w_club==49)||($w_club==53))&&($w_lvl>=19)){
					$log.="<span class='red'>【超限】使对方的宝石魔法效果被完全施展了！</span><br>";
					$damage+=$lose_w_gem;$w_gempower=0;
					$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法使物理伤害增加了{$raisedmg}%！</span><br>";
			}else{
				$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法使物理伤害增加了{$w_gempower}点！</span><br>";
				$w_gemexp+=round($w_gempower/1.25);$damage+=$w_gempower;$w_gempower=0;
				$w_gemstate=1;
			}
			if(($w_gemlvl>=3)&&($w_hp<$w_mhp)&&($w_damage>=10)){
				$vampire=round($damage*0.1);
				$w_hp=min($w_mhp,$w_hp+$vampire);
				$log .= "<span class=\"gem\">对方身上的高阶{$gemname}魔法吸取了{$vampire}点生命！</span><br>";
			}
			if(($w_gemexp>=100)&&($w_gemlvl<3)){$w_gemlvl+=1;$w_gemexp=0;}
		}
		
		if (($w_club==2)&&($w_lvl>=15)){
			$damage=round($damage*1.15);
			$log .= "<span class=\"red\">居合使物理伤害变为115%！</span><br>";
		}
		
		global $w_aurad,$aurad,$now;
		if ($now<=$aurad)
		{
			global $lvl;
			$rate=min(25,5+$lvl);
			$damage=round($damage*(100-$rate)/100);
			$log.= "<span class=\"clan\">在你的光环作用下，你受到的物理伤害减少了{$rate}%！</span><br>";
		}
		if ($now<=$w_aurad)
		{
			$damage=round($damage*0.75);
			$log.= "<span class=\"grey\">在敌方光环作用下，敌方输出的物理伤害减少了25%！</span><br>";
		}
		
		global $debuffb,$now;
		if ($now<=$debuffb)
		{
			$damage=round($damage*1.3);
			$log.="<span class=\"yellow\">由于你处于恐惧状态，你受到的物理伤害增加了30%！</span><br>";
		}
		
		global $w_debuffa,$now;
		if ($now<=$w_debuffa)
		{
			$damage=round($damage*0.8);
			$log.="<span class=\"grey\">由于敌方被灵魂缠绕，敌方的物理伤害减少了20%！</span><br>";
		}
		
		global $club, $lvl;
		if ($club==19 && $lvl>=3)
		{
			$reduction=min($lvl,10);
			$damage=round($damage*(100-$reduction)/100);
			$log.="<span class=\"clan\">你坚韧的意志抵挡了{$reduction}%的物理伤害！</span><br>";
		}
		
		if ($w_club==19 && $w_lvl>=15)
		{
			$eedmg=0;
			$log.="<span class=\"grey\">对手受忍术技能的影响，攻击没有造成属性伤害。</span><br>";
		}
		else
		{
			$eedmg=get_ex_dmg ( "你", 1, $w_club, $inf, $w_att_key, $w_wep_kind, $w_wepe, $w_wep_skill, $def_key ,$w_lvl,$bsk);
			
			if (($w_club==3)&&($w_lvl>=7)&($eedmg)&&($w_wep_kind=='C')){
				$ded=round($eedmg*0.35);
				$eedmg=$eedmg+$ded;
				$log .= "<span class=\"clan\">在对手花雨技能的作用下，你受到的属性伤害增加了{$ded}点！</span><br>";
			}
		
			if (($w_club==5)&&($w_lvl>=7)&($eedmg)){
				$ded=round($eedmg*0.1);
				$eedmg=$eedmg+$ded;
				$log .= "<span class=\"clan\">在对手强击技能的作用下，你受到的属性伤害增加了{$ded}点！</span><br>";
			}
		
			if (($club==9)&&($lvl>=7)&($eedmg)){
				$ded=floor($eedmg/100*rand(5,15))+1;
				$eedmg=$eedmg-$ded;
				$log .= "<span class=\"clan\">在护体技能的作用下，你受到的属性伤害减少了{$ded}点！</span><br>";
			}
			
			
			
			global $aurab, $now, $lvl; 
			if ($now<=$aurab && $eedmg)
			{
				$rate=max(30,70-round($lvl*1.5)); $reducted_rate=100-$rate;
				$eedmg=round($eedmg*$rate/100);
				$log .= "<span class=\"clan\">在你的光环作用下，你受到的属性伤害降低了{$reducted_rate}%！</span><br>";
			}
		
			global $w_aurab, $now; 
			if ($now<=$w_aurab && $eedmg)
			{
				$eedmg=round($eedmg*0.4);
				$log .= "<span class=\"yellow\">在敌方光环作用下，敌方输出的属性伤害降低了60%！</span><br>";
			}
			
			include_once GAME_ROOT . './include/game/gem.func.php';
			if(($club==49)&&($eedmg>100)&&($gempower>0)){
				$eqs=round($gempower*2);
				if($eedmg>$eqs){$l_edmg=$eqs;$eedmg-=$l_edmg;$gempower=0;}
				else{$l_edmg=$eedmg-1;$eedmg=1;$gempower-=round($l_edmg/2);}
				$log .= "<span class=\"deeppink\">你身上的奥术粒子抵消了{$l_edmg}点属性伤害！</span><br>";
			}
			
			if(($club==53)&&($eedmg>100)&&($gempower>0)){
				$eqs=round($gempower*4);
				if($eedmg>$eqs){$l_edmg=$eqs;$eedmg-=$l_edmg;$gempower=0;}
				else{$l_edmg=$eedmg-1;$eedmg=1;$gempower-=round($l_edmg/4);}
				$log .= "<span class=\"deeppink\">你身上的奥秘粒子抵消了{$l_edmg}点属性伤害！</span><br>";
			}
			
		}
		
		global $is_second_strike;
		if ($w_type!=0) npc_useskill($is_second_strike, $damage, $eedmg);
		
		$damage += $eedmg;
		
		//最终伤害计算处
		
		if (!$w_type){
			$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$w_name'");
			$sktime = $db->result($result, 0);
			if (!$sktime) $sktime=0;
		}
		
		if (($w_club==2)&&($w_lvl>=7)){
			$damage=round($damage*1.05);
			$log .= "<span class=\"red\">业物使最终伤害变为105%！</span><br>";
		}
		
		if (($w_club==18)&&($w_lvl>=3)){
			$damage=round($damage*1.02);
			$log .= "<span class=\"red\">适应使最终伤害变为102%！</span><br>";
		}
		
		if (($w_club==7)&&($w_lvl>=7)){
			$extd=30+$w_lvl*2;
			if (strpos($inf,'e')!==false){
				//$extd=50+$w_lvl*3;
				if ($w_lvl>=11){
					$rat=round($w_wep_skill/2)+100;
					$extd=floor($extd*$rat/100)+1;
				}
			}
			$log .= "<span class=\"yellow\">你因行动不便受到了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($w_club==1)&&($w_lvl>=15)&&($w_wep_kind=='P')&&($hit_time[2]>0)){
			$log .= "<span class=\"yellow\">猛击附加了130点额外伤害！</span><br>";
			$damage+=130;
		}
		
		if (($w_club==1)&&($w_lvl>=19)&&($w_wep_kind=='P')){
			$exd=$w_wepe/2;
			if ($exd>0){
				$exd=floor($exd)+1;
				if ($exd>250) $exd=250;
				$log .= "<span class=\"yellow\">你难以抵挡，受到了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if (($w_club==23)&&($w_lvl>=11)){
			$exd=100-$w_rage;
			if ($exd>0){
				$log .= "<span class=\"yellow\">冷静附加了{$exd}点额外伤害！</span><br>";
				$damage+=$exd;
			}
		}
		
		if (($w_hp>=$w_mhp)&&($w_club==25)&&($w_lvl>=7)){
			$extd=$w_lvl*4+30;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">本气附加了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($w_club==25)&&($sktime==$pid)){
			$extd=$w_lvl*4+30;
			if ($extd>170) $extd=170;
			$log .= "<span class=\"yellow\">你因敌人的标记受到了{$extd}点额外伤害！</span><br>";
			$damage+=$extd;
		}
		
		if (($w_club==14)&&($w_lvl>=15)){
			$mhdmg=round($w_mhp*0.1);
			if ($mhdmg>120) $mhdmg=120;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">刚击附加了{$mhdmg}点伤害！</span><br>";
		}
		
		/*if (($w_club==18)&&($w_lvl>=15)){
			$mhdmg=round($wepe*0.12);
			if ($mhdmg>160) $mhdmg=160;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">解构附加了{$mhdmg}点伤害！</span><br>";
		}*/
		
		if (($w_club==14)&&($w_lvl>=19)&&($w_hp<=round($w_mhp*0.5))){
			$mhdmg=round($mhp*0.4);
			if ($mhdmg>400) $mhdmg=400;
			$damage+=$mhdmg;
			$log .= "<span class=\"red\">斗魂附加了{$mhdmg}点伤害！</span><br>";
		}
		
		if (($w_club==25)&&($w_lvl>=19)&&($sktime==$pid)){
			$damage=round($damage*1.2);
			$log .= "<span class=\"red\">一心使总伤害变为120%！</span><br>";
		}
		
		
		if (($w_club==2)&&($w_lvl>=3)&&(strlen($inf)>0)){
			$damage=round($damage*1.15);
			$log .= "<span class=\"red\">由于你已经受伤，总伤害变为115%！</span><br>";
		}
		
		
		
		if (($w_club==3)&&($w_lvl>=19)&&($active==0)){
			$damage=round($damage*1.6);
			$log .= "<span class=\"red\">百出使反击和先制伤害变为160%！</span><br>";
		}
		
		if (($club==14)&&($lvl>=11)&&($active==0)){
			$damage=round($damage*0.9);
			$log .= "<span class=\"clan\">在铁骨技能的作用下，你受到的反击和先制伤害变为90%！</span><br>";
		}
		
		if (($w_club==26)&&($w_lvl>=15)&&($w_hasi>0)){
			$hcou=3*$w_hasi;
			$damage+=round($damage*$hcou/100)+1;
			$log .= "<span class=\"red\">对方的粉丝使伤害提高了{$hcou}%！</span><br>";
		}
		if (($club==26)&&($lvl>=15)&&($hasi>0)){
			$hcou=3*$hasi;
			if ($hcou>=50){
				$hcou=50;
			}
			$damage-=round($damage*$hcou/100);
			$log .= "<span class=\"red\">你的粉丝使伤害降低了{$hcou}%！</span><br>";
		}
		
		if ($w_type==0 && ($w_club==24 || $w_club==99) && $w_lvl>=3)	
		{
			if (rand(0,99)<35)
			{
				$log.='<span class="yellow">敌人吸取了你的1点生命上限！</span><br>';
				$w_mhp++; $mhp--; 
			}
			if (rand(0,99)<35)
			{
				$log.='<span class="yellow">敌人吸取了你的1点全系熟练！</span><br>';
				global $w_wp,$w_wk,$w_wc,$w_wg,$w_wd,$w_wf;
				global $wp,$wk,$wc,$wg,$wd,$wf;
				if ($wp>0) { $wp--; $w_wp++; }
				if ($wk>0) { $wk--; $w_wk++; }
				if ($wc>0) { $wc--; $w_wc++; }
				if ($wg>0) { $wg--; $w_wg++; }
				if ($wd>0) { $wd--; $w_wd++; }
				if ($wf>0) { $wf--; $w_wf++; }
			}
		}
		
		$damage = checkdmgdef($damage, $w_att_key, $def_key, 0,$bsk);
		
		if (($w_club==2)&&($w_lvl>=19)){
			$dice=rand(1,100);
			if ($hp<$mhp) $dice=$dice/3;
			if ($dice<=11){ 
				$damage=$damage*2;
				$log .= "<span class=\"red\">斩击使最终伤害变为200%！</span><br>";
			}
		}
		
		if (($w_type>0)&&(strpos($w_art,'语录')!==false)&&($w_arte==3)){
			$damage=$damage*3;
			$log .= "<span class=\"red\">三个代表使最终伤害变为300%！</span><br>";
		}
		
		
		//好人卡特别活动
		$gm = ceil(count_good_man_card(1)*rand(80,120)/100);
		if($gm){
			$log .= "在你身上的<span class=\"yellow\">好人卡</span>的作用下，你受到的伤害增加了<span class=\"red\">$gm</span>点！<br>";
			$damage += $gm;
		}
		
		include_once GAME_ROOT . './include/game/clubskills.func.php';
		if ($club==22)
		{
			$ratio=get_clubskill_bonus_ironwill_reduction($hp,$mhp);
			$log .= "<span class=\"yellow\">你钢铁般的意志降低了敌人的伤害，伤害被减少至{$ratio}%！</span><br>";
			$damage=ceil(1.0*$damage*$ratio/100);
		}
		
		if (($club==6)&&($lvl>=19)){
			$blk=round($damage*0.2);
			if ($blk>($sp-1)) $blk=$sp-1;
			if ($blk>0){
				$log .= "<span class=\"clan\">你消耗体力抵挡了{$blk}点伤害！</span><br>";
				$sp=$sp-$blk;
				$damage=$damage-$blk;
			}
		}
		
		if(($w_type==30)&&($w_name=="tabris")){
			$tdd=$damage;
			$damage=$damage+round($damage*3.5*($w_mhp-$w_hp)/$w_mhp)+100;
			$tdd=$damage-$tdd;
			$log .= "<span class=\"red\">tabris的战斗经验使造成的伤害提高了{$tdd}点！</span><br>";
			
		}
		
		if(strpos($inf,'S')!==false){
			$lmh=round($damage/2);$mhp=max(1,$mhp-$lmh);
			$log.="<span class=\"sienna\">由于你被石化了，敌人的攻击使你的生命上限下降了{$lmh}点！</span><br>";
		}
		
		if(strpos($w_wepsk,'=')!==false){
		global $w_hp,$w_mhp,$w_sp,$w_msp;
			$br=rand(1,35);$gb=round($br+$damage/10);
			$w_hp=min($w_hp+$gb,$w_mhp);$w_sp=min($w_sp+$gb,$w_msp);
			$log.="<span class=\"red\">吸血的效果使对方恢复了{$gb}点生命与体力！</span><br>";
		}
		
		//又一坨宝石
		include_once GAME_ROOT.'./include/game/gem.func.php';
		if (($gemname=='黑曜石')&&($gemstate==2)){
			$reducedmg=magic_gem('黑曜石');
			$losegem=round($damage*$reducedmg/100);
			if(($club==49)||($club==53)){$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";}
				if($losegem<=$gempower){
					$damage-=$losegem;$gempower-=$losegem;$gemexp+=$losegem;
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使所受伤害减少了{$reducedmg}%！</span><br>";
				}elseif(($losegem>$gempower)&&(($club==49)||($club==53))&&($lvl>=19)){
					$log.="<span class='red'>【超限】使宝石魔法的效果被完全施展了！</span><br>";
					$damage-=$losegem;$gempower=0;
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使所受伤害减少了{$reducedmg}%！</span><br>";
				}else{
					$log .= "<span class=\"yellow\">你身上的{$gemname}魔法使所受伤害减少了{$gempower}点！</span><br>";
					$gemexp+=$gempower;$damage-=$gempower;$gempower=0;
					$gemstate=1;
					$log .= "<span class=\"yellow\">{$gemname}由于gem不足已关闭，请补充gem！</span><br>";
				}		
			if(($gemexp>=100)&&($gemlvl<3)){$gemlvl+=1;$gemexp=0;$log .= "<span class='lime'>{$gemname}升级了！</span><br>";}
		}			
		if(($w_gemname=='红宝石')&&($w_gemstate==2)){
			if($w_gemlvl==0){$w_rfd=50;}
			elseif($w_gemlvl==1){$w_rfd=100;}
			elseif($w_gemlvl==2){$w_rfd=200;}
			elseif($w_gemlvl==3){$w_rfd=round($gempower/2.5);}		
			if(($w_club==49)||($w_club==53)){$w_rfd=round($w_rfd*1.25);}
			$damage+=$w_rfd;
			$log .= "<span class=\"yellow\">对方身上的{$w_gemname}魔法附加了{$w_rfd}点伤害！</span><br>";
		}
		global $wep;
		if (($wep=='＜时刃＞')&&($club==53)){
			$tdp=magic_gemwep('＜时刃＞');
			$damage-=round($damage*($tdp/100));
		}
		$bonus_dmg = get_clubskill_bonus_dmg_rate($w_club,$w_skills,$club,$skills)*100;
		if($bonus_dmg < 100){
			$log.="<span class=\"yellow\">由于技能效果的作用，伤害下降至".$bonus_dmg."%！</span><br>";
			$damage = round($damage * $bonus_dmg / 100);
		}
		if($damage != $pdamage){
			$log .= "<span class=\"yellow\">造成的总伤害：<span class=\"red\">$damage</span>。</span><br>";
		}
		
		checkdmg ( $w_name, $name, $damage, $w_type*1000+$w_sNo, $type*1000+$sNo );
		
		if ((($w_type!=30)||($w_name!='tabris'))&&(!($bsk=="olaola"))&&(($w_club!=25)||($w_hp<=$w_mhp)||($w_lvl<7))) get_dmg_punish ( $w_name, $damage, $w_hp, $w_att_key,$w_club,$w_lvl );
		
		get_inf ( '你', $hit_time [2], $w_wep_kind,$w_club,$w_lvl);
		
		check_KP_wep ( $w_name, $hit_time [3], $w_wep, $w_wepk, $w_wepe, $w_weps, $w_wepsk );
		
		if (($w_club==10)&&($w_lvl>=7)){
			$w_exp++;
		}
		
		if (($club==1)&&($lvl>=7)){
			$rage+=5;
			if ($rage>100) $rage=100;
		}
		
		exprgup ( $w_lvl, $lvl, $w_exp, 0, $rage , 0 );
		
		//爆头
	
		if (($w_club==4)&&($w_lvl>=19)&&($w_wep_kind=='G')&&($damage>($hp*0.85))&&($damage<$hp)){
			$damage=$hp;
			$log.='<span class="red">敌人的攻击直接将你爆头！</span><br>';
		}
		
		if (($w_club==1)&&($w_lvl>=7)&&($w_wep_kind=='P')&&(!$type)){
			$srd=round($damage*2/3)+50;
			if ($srd>=$sp) $srd=$sp-1;
			$sp-=$srd;
			$log.="敌人的攻击使你的体力减少了<span class=\"yellow\">{$srd}</span>点！<br>";
		}
			
		/*if (($w_club==2)&&($w_lvl>=19)&&($w_wep_kind=='K')){
			if (strpos($inf,'B')===false){
				$inf.='B';
				$log.='敌人的攻击使你进入了<span class="red">裂伤</span>状态！<br>';
			}
		}*/
		
		if (($w_club==5)&&(!$type)&&($w_lvl>=11)){
			$idc=rand(1,4);
			if ($idc==1){
				$inf=str_replace('b','',$inf);
				$inf=$inf.'b';
				$log.='<span class="yellow">共振使你的身体受伤了！</span><br>';
			}
			if ($idc==2){
				$inf=str_replace('h','',$inf);
				$inf=$inf.'h';
				$log.='<span class="yellow">共振使你的头部受伤了！</span><br>';
			}
			if ($idc==3){
				$inf=str_replace('a','',$inf);
				$inf=$inf.'a';
				$log.='<span class="yellow">共振使你的手部受伤了！</span><br>';
			}
			if ($idc==4){
				$inf=str_replace('f','',$inf);
				$inf=$inf.'f';
				$log.='<span class="yellow">共振使你的足部受伤了！</span><br>';
			}
		}
		
	
	} else {
		$damage = 0;
		if($w_name!=='虚子'){
		$log .= "但是没有击中！<br>";
		}
	}
	

	
	
	check_GCDF_wep ( $w_name, $hit_time [0], $w_wep, $w_wep_kind, $w_wepk, $w_wepe, $w_weps, $w_wepsk ,$w_club,$w_lvl,$bsk);
	
	addnoise ( $w_wep_kind, $w_wepsk, $now, $pls, $w_pid, $pid, $w_wep_kind );
	
	if($w_club == 10){
		$w_add_skill +=2;
		if ($w_lvl>=19) $w_add_skill++;
	}else{
		$w_add_skill +=1;
	}
	
	if($w_club == 14){
		if($active){
			$w_att++;
		}else{
			$w_def++;
		}
	}
	
	//锡安圣剑
	
	if (($w_club==18)&&($w_lvl>=7)){
		$asd=rand(1,6);
		if ($asd==1) $w_wp++;
		if ($asd==2) $w_wk++;
		if ($asd==3) $w_wc++;
		if ($asd==4) $w_wd++;
		if ($asd==5) $w_wf++;
		if ($asd==6) $w_wg++;
	}
	
	if (($w_club==7)&&($w_wep_kind=='P')&&(rand(1,4)==4)&&($w_lvl>=3)){
		$w_add_skill +=2;
	}
	
	if (($w_club==23)&&($w_lvl>=3)){
		$pdc=rand(1,100);
		if ($pdc<=60) $w_add_skill++;
		if ($pdc<=30) $w_add_skill++;
		if ($pdc<=15) $w_add_skill++;
		if ($pdc<=5) $w_add_skill++;
	}
	
	if ($hp>0){
		$hp -= $damage;
		
		global $teamID, $w_teamID;
		if ($hp <= 0) {
		global $wepexp, $club, $wep;
			$tmp_club=$club;
			$tmp_wepexp=$wepexp;
			$hp = 0;
			$w_killnum ++;
			$rpup = 20;
			if($w_club == 28){
				$rpdec = 30;
				$rpdec += get_clubskill_rp_dec($w_club,$w_skills);
				$w_rp += round($rpup*(100-$rpdec)/100);
			}
			else{
				$w_rp += $rpup;
			}
			include_once GAME_ROOT . './include/state.func.php';
			$killmsg = death ( $w_wep_kind, $w_name, $w_type, $w_wep_temp );
			$log .= npc_chat ( $w_type,$w_name, 'kill' );
			if ($tmp_club==99){
				$log .= '<span class="yellow">由于你及时按了BOMB键，你原地满血复活了！</span><br>';
			}
			if((strpos($wepsk,'t')!==false)&&($club==53)){
				global $gamestate,$areanum,$starttime,$now,$areatime,$areahour;
				global $gempower,$pls,$typls,$tyowner,$name;
						$log .= '<span class="clan">武器上附着的神秘粒子生效了！你破损的千疮百孔的身体又完好如初了！只是你感觉到一阵虚弱，似乎什么东西从你的体内流失了……</span><br>';
					if(($gempower>=250)&&($wep=='＜厄环＞')&&($typls==$pls)&&($tyowner==$name)){
						$gempower-=250;
						$log .="<span class='gem'>「领域」被触发了，领域内的时间流动被静止了！</span><br><span class='red'>你消耗了250点GEM!</span><br>";
					}else{
						$areatime = $now + 30;
						include_once GAME_ROOT.'./include/system.func.php';
						movehtm();
						save_gameinfo();
						$log.='<span class="yellow">神秘粒子所引起的时空扭曲使禁区将在30秒后到来！</span><br>';
					}
					$log .= '<span class="red">你的生命上限减少了！<br>你的体力上限减少了！</span><br><span class="yellow">神秘粒子被消耗了一部分，你的武器效果降低了！<br></span>';
					if($wepe<=2){$log .= '<span class="yellow">当你再一次站起身时，发现武器上的神秘粒子已经消散殆尽，武器本身也无法使用了。<br></span>';}
			}
			global $gemwepfoinfo;
			if(($tmp_wepexp==0)&&($tmp_club==52)&&(in_array($wep,$gemwepfoinfo))){
					$log .= '<span class="deeppink">就在你将要失去意识之时，武器上的宝石散发出了诡异的光芒，无数不可名状的奇异粒子从中散发而出，他们汇集在你身体附近，修补着破损之处。在这一瞬间，你领悟了其中蕴含的奥秘。</span><br>';
			}
		}
	}
	
	$log = $x_temp_log.$log;
	
	if (($w_wep=="Solidarity")&&($w_type)&&(!$type)&&((rand(1,100))>=20)&&($hp>0)){
		$td=rand(1,6);
		$taunt=array(
			1=>"黑熊又一次粉碎了你们！谢谢！",
			2=>"无知！无聊！",
			3=>"没玩大逃杀好几个小时了！",
			4=>"该玩家不存在！",
			5=>"已经锁定你，说什么都晚了！",
			6=>"不敢反击，还是故意不反击？",
		);
		$log.="<span class=\"linen\">“{$taunt[$td]}”</span><br>";
		defend($w_wep_kind,$active,"olaola");
	}
	if (($w_wep=="M240通用机枪")&&($w_type)&&(!$type)&&((rand(1,100))>=30)&&($hp>0)){
		$log.="<span class=\"yellow\">龙套又一次发动了攻击！</span><br>";
		defend($w_wep_kind,$active,"olaola");
	}
	
	return $damage;
}

function get_original_dmg($w1, $w2, $att, $def, $ws, $wp_kind) {
	global $skill_dmg, $dmg_fluc, $weather, $pls, $log;
	global ${$w1 . 'pose'}, ${$w1 . 'tactic'}, ${$w1 . 'club'}, ${$w1 . 'inf'}, ${$w1 . 'active'}, ${$w2 . 'pose'}, ${$w2 . 'tactic'}, ${$w2 . 'club'}, ${$w2 . 'inf'}, ${$w2 . 'active'},${$w2 . 'skills'},${$w1 . 'skills'},${$w1 . 'lvl'},${$w2 . 'lvl'};
	include_once GAME_ROOT.'./include/game/clubskills.func.php';
	get_clubskill_bonus(${$w1 . 'club'},${$w1 . 'skills'},$w1,${$w2 . 'club'},${$w2 . 'skills'},$w2,$att1,$def1);
	$att+=$att1; $def+=$def1;
	$attack_p = get_attack_p ( $weather, $pls, ${$w1 . 'pose'}, ${$w1 . 'tactic'}, ${$w1 . 'club'}, ${$w1 . 'inf'}, ${$w1 . 'active'} , ${$w1 . 'lvl'});
	$att_pow = $att * $attack_p;
	$defend_p = get_defend_p ( $weather, $pls, ${$w2 . 'pose'}, ${$w2 . 'tactic'}, ${$w2 . 'club'}, ${$w2 . 'inf'}, ${$w2 . 'active'} , ${$w2 . 'lvl'});
	$def_pow = $def * $defend_p;
	get_clubskill_bonus_p(${$w1 . 'club'},${$w1 . 'skills'},$w1,${$w2 . 'club'},${$w2 . 'skills'},$w2,$attfac,$deffac);
	$att_pow *= $attfac;
	$def_pow *= $deffac;
	if($def_pow <= 0){$def_pow = 0.01;}
	$damage = ($att_pow / $def_pow) * $ws * $skill_dmg [$wp_kind];
	
	$dfluc = $dmg_fluc [$wp_kind];
	$dfluc += get_clubskill_bonus_fluc(${$w1 . 'club'},${$w1 . 'skills'},$w1,${$w2 . 'club'},${$w2 . 'skills'},$w2);
	
	$dmg_factor = (100 + rand ( - $dfluc, $dfluc )) / 100;
	
	$damage = round ( $damage * $dmg_factor * rand ( 4, 10 ) / 10 );
	
	if ($w1=='')
	{
		global $club,$lvl;
		if ($club==16 && $lvl>=19)
		{
			global $auraa,$aurab,$aurac,$aurad,$now;
			$cnt=1;
			if ($now<=$auraa) $cnt++;
			if ($now<=$aurab) $cnt++;
			if ($now<=$aurac) $cnt++;
			if ($now<=$aurad) $cnt++;
			$rate=20*$cnt;
			$damage=round($damage*(100+$rate)/100);
			$log.= "<span class=\"yellow\">在你的光环作用下，你的基础伤害增加了{$rate}%！</span><br>";
		}
		/*
		global $club,$lvl,$souls;
		if (($club==99 || $club==24) && $lvl>=3 && $souls>0)
		{
			$rate=min($souls*1,min($lvl*2,30));
			$damage=round($damage*(100+$rate)/100);
			$log.= "<span class=\"yellow\">你囚禁的灵魂为你增加了{$rate}%的基础伤害！</span><br>";
		}
		*/
		global $bsk_name,$souls;
		if ($bsk_name=='nightmare' && $souls>=4)
		{
			$soul_used=floor($souls/4);
			$armor_pierced=$soul_used*6; $rate=$soul_used*9;
			$souls-=$soul_used;
			global $w_def;
			$w_def-=$armor_pierced; if ($w_def<1) $w_def=1;
			$damage=round($damage*(100+$rate)/100);
			$log.="<span class=\"yellow\">你释放了{$soul_used}个灵魂，削弱了对方<span class=\"clan\">{$armor_pierced}</span>点基础防御，并造成了额外<span class=\"clan\">{$rate}%</span>的基础伤害！</span><br>";
		}
	}
	else
	{
		global $w_club,$w_lvl;
		if ($w_club==16 && $w_lvl>=19)
		{
			global $w_auraa,$w_aurab,$w_aurac,$w_aurad,$now;
			$cnt=1;
			if ($now<=$w_auraa) $cnt++;
			if ($now<=$w_aurab) $cnt++;
			if ($now<=$w_aurac) $cnt++;
			if ($now<=$w_aurad) $cnt++;
			$rate=20*$cnt;
			$damage=round($damage*(100+$rate)/100);
			$log.= "<span class=\"yellow\">在敌方光环作用下，你受到的基础伤害增加了{$rate}%！</span><br>";
		}
		/*
		global $w_club,$w_lvl,$w_souls,$w_type;
		if ($w_type==0 && ($w_club==99 || $w_club==24) && $w_lvl>=3 && $w_souls>0)	//NPC的决死结界实际是第一形态，不受技能影响
		{
			$rate=min($w_souls*1,min($w_lvl*2,30));
			$damage=round($damage*(100+$rate)/100);
			$log.= "<span class=\"yellow\">敌方囚禁的灵魂增加了{$rate}%的基础伤害！</span><br>";
		}
		*/
	}
	return $damage;
}

function get_damage_p(&$rg, $atkcdt, $type, $nm,$cl = 0, $msg = '' ) {
	return 1;
	/*
	$cri_dice = rand ( 0, 99 );
	if ($cl == 9) {
		$rg_m = 50;
		$dmg_p = 2;
		if (!empty($msg) || $rg >= 255) {
			$max_dice = 100;
		} elseif ($type != 0) {
			$max_dice = 40;
		} else {
			$max_dice = 0;
		}
		$cri_word = '发动必杀技';
	} else {
		$rg_m = 30;
		$dmg_p = 1.5;
		if ($rg >= 255) {
			$max_dice = 100;
		} else {
			$max_dice = 30;
		}
		$cri_word = '使出重击';
	}
	
	if (strpos ( $atkcdt, "c" ) !== false) {
		$rg_m = $cl == 9 ? 20 : 10;
		if ($max_dice != 0) {
			$max_dice += 30;
		}
	}
	if ($cri_dice <= $max_dice && $rg >= $rg_m) {
		global $log;
		
		$log .= npc_chat ( $type,$nm, 'critical' );
		
		if ($nm == '你') {
			$log .= "{$nm}消耗<span class=\"yellow\">$rg_m</span>点怒气，<span class=\"red\">{$cri_word}</span>！";
		} else {
			$log .= "{$nm}<span class=\"red\">{$cri_word}</span>！";
		}
		$rg -= $rg_m;
		return $dmg_p;
	} else {
		return 1;
	}*/
	/*if ($cl == 9) {
		if ($sd == 0) {
			if ((! empty ( $msg )) && ($rg >= $rg_m) || $rg == 255) {
				$log .= "你消耗<span class=\"yellow\">$rg_m</span>点怒气，<span class=\"red\">发动必杀技</span>！";
				$damage_p = 2;
				$rg -= $rg_m;
			}
		} else {
			if (($cri_dice < $max_dice && ($rg >= $rg_m)) || $rg == 255) {
				global $w_type;
				if ($w_type == 1) {
					$log .= npc_chat ( $w_type, 'critical' );
				}
				$log .= "<span class=\"red\">发动必杀技</span>！";
				$damage_p = 2;
				$rg -= $rg_m;
			}
		}
	} elseif ($cri_dice < $max_dice || $rg == 255) {
		if (($rg >= $rg_m) && ($sk >= 20) &&($lv > 3)) {
			if ($sd == 0) {
				$log .= "你消耗<span class=\"yellow\">$rg_m</span>点怒气，使出";
			} else {
				global $w_type;
				if ($w_type == 1) {
					$log .= npc_chat ( $w_type, 'critical' );
				}
			}
			$log .= "<span class=\"red\">重击</span>！";
			$damage_p = 1.5;
			$rg -= $rg_m;
		}
	}
	return $damage_p;*/
}

function checkdmg($p1, $p2, $d, $p1s, $p2s) {
	if ($d>=100) addnews ( 0, 'damagenew', $p1, $p2, $d, $p1s, $p2s );
	return;
}

function checkdef($def, $ardef, $aky, $active = 0){
	global $specialrate,$log,$w_name,$club,$lvl,$w_club,$w_lvl,$auraa,$w_auraa,$now,$debuffc,$w_debuffc;
	if ($active && $now<=$w_auraa) $def*=3.5;
	if ($active && $now<=$w_debuffc) $def*=0.4;
	if (!$active && $now<=$auraa) $def*=3.5;
	if (!$active && $now<=$debuffc) $def*=0.4;
	$defend = $def + $ardef;
	if(strpos($aky,'N')!==false){
		$Ndice = rand(0,99);
		if($Ndice < $specialrate['N']){
			$defend = $def + round($ardef / 2);
			$log .= $active ? "<span class=\"yellow\">你的攻击隔着{$w_name}的防具造成了伤害！</span><br>" : "<span class=\"yellow\">{$w_name}的攻击隔着你的防具造成了伤害！</span><br>";
		}
	}
	else  
	{
		$flag=0;
		if ($active) 
		{
			if ($club==19 && $lvl>=15 && rand(0,99)<75) $flag=1;
		}
		else
		{
			if ($w_club==19 && $w_lvl>=15 && rand(0,99)<75) $flag=1;
		}
		if ($flag)
		{
			$defend = $def + round($ardef / 2);
			if ($active)
				$log .= "<span class=\"yellow\">长期的忍术训练使你的攻击直击敌人的弱点！你的攻击隔着{$w_name}的防具造成了伤害！</span><br>";
			else  $log .= "<span class=\"yellow\">忍术训练使敌人的攻击直击你的弱点！{$w_name}的攻击隔着你的防具造成了伤害！</span><br>";
		}
	}
	return $defend;
}

function checkarb(&$dmg, $w, $aky, $dky, $active = 0) {
	global $log,$specialrate,$w_name,$club,$lvl,$w_club,$w_lvl;
	$dmginv = false;
	if (strpos ( $aky, 'n' ) !== false && (strpos ( $dky, 'B' ) !== false || strpos ( $dky, $w ) !== false)) {
		$dice = rand ( 0, 99 );
		if ($dice < $specialrate['n']) {
			$log .= $active ? "<span class=\"yellow\">你的攻击贯穿了{$w_name}的防具！</span><br>" : "<span class=\"yellow\">{$w_name}的攻击贯穿了你的防具！</span><br>";
			return;
		}
	}
	/*
	else  
	{
		$flag=0;
		if ($active)
		{
			if ($club==19 && $lvl>=15 && rand(0,99)<20) $flag=1;
		}
		else
		{
			if ($w_club==19 && $w_lvl>=15 && rand(0,99)<20) $flag=1;
		}
		if ($flag)
		{
			if ($active)
				$log.="<span class=\"yellow\">在你精湛的忍术面前，敌人的防御不堪一击！你的攻击贯穿了{$w_name}的防具！</span>";
			else  $log.="<span class=\"yellow\">在敌人精湛的忍术面前，你的防御不堪一击！{$w_name}的攻击贯穿了你的防具！</span><br>";
			return;
		}
	}
	*/
	
	if (strpos ( $dky, 'B' ) !== false) {
		$dice = rand ( 0, 99 );
		if ($dice < $specialrate['B']) {
			$dmg = 1;
			$log .= $active ? "<span class=\"yellow\">你的攻击完全被{$w_name}的装备吸收了！</span><br>" : "<span class=\"yellow\">{$w_name}的攻击完全被你的装备吸收了！</span><br>";
			$dmginv = true;
		}else{
			$log .= $active ? "纳尼？{$w_name}的装备使攻击无效化的属性竟然失效了！<br>" : "纳尼？你的装备使攻击无效化的属性竟然失效了！<br>";
		}
	}
	if (strpos ( $dky, $w ) !== false && !$dmginv) {
		$dice = rand ( 0, 99 );
		if ($dice < 90) {
			$flag=0;
			if ($active)
			{
				if ($club==19 && $lvl>=15 && rand(0,99)<40) $flag=1;
			}
			else
			{
				if ($w_club==19 && $w_lvl>=15 && rand(0,99)<40) $flag=1;
			}
			if ($flag)
			{
				if ($active)
					$log.="<span class=\"yellow\">在你精湛的忍术面前，敌人的防御不堪一击！敌人防具的物理伤害减半属性没有发挥任何作用！</span><br>";
				else  $log.="<span class=\"yellow\">在敌人精湛的忍术面前，你的防御不堪一击！你防具的物理伤害减半属性没有发挥任何作用！</span><br>";
			}
			else
			{
				$dmg /= 2;
				$log .= $active ? "<span class=\"yellow\">{$w_name}的装备使你的攻击伤害减半了！</span><br>" : "<span class=\"yellow\">你的装备使{$w_name}的攻击伤害减半了！</span><br>";
			}
		}else{
			$log .= $active ? "{$w_name}的装备没能发挥减半伤害的效果！<br>" : "你的装备没能发挥减半伤害的效果！<br>";
		}
	}
	return;
}

function checkdmgdef($dmg, $aky, $dky, $active,$bk) {
	global $log, $name, $w_name;
	//if (strpos ( $aky, 'h' ) !== false){
	//	if($active){$nm = '你';}
	//	else{$nm = $w_name;}
	//	$flag = 1;
	if (strpos ( $dky, 'h' ) !== false){
		if($active){$nm = $w_name;}
		else{$nm = '你';}
		$flag = 1;
	}else{$flag = 0;}
	if ($flag) {
		$dice = rand ( 0, 99 );
		if ($bk=="olaola") $dice+=20;
		if($dmg > 1950 + $dice){
			if ($dice < 90) {
				$dmg = 1950 + $dice;
				$log .= "在{$nm}的装备的作用下，攻击伤害被限制了！<br>";
				
			}else{
				$log .= "{$nm}的装备没能发挥限制攻击伤害的效果！<br>";
			}
		}
	}
	return $dmg;
}

function checkdmgreflex(&$dmg, $ar) {
	global $log;
	if (strpos ( $ar, 'B' ) !== false) {
		$dice = rand ( 0, 99 );
		if ($dice < 90) {
			$dmg = 1;
			$log .= "<span class=\"red\">攻击的力量被完全吸收了！</span>";
		}else{
			$log .= "防具使攻击无效化的效果失败了！";
		}
	}
	return;
}

function getatkkey($w, $ah, $ab, $aa, $af, $at, $atkind, $is_wpg) {
	global $ex_attack;
	$atkcdt = '';
	$eqpkey = $w . $ah . $ab . $aa . $af . $at . substr ( $atkind, 1, 1 );
	foreach(Array('c','l','g','H','h','N','n','X','L','-','*','+') as $value){
		if (strpos ( $eqpkey, $value ) !== false) {
			$atkcdt .= '_'.$value;
		}
	}
	if(!$is_wpg){
		foreach(Array('r','R','x') as $value){
			if (strpos ( $w, $value ) !== false) {
				$atkcdt .= '_'.$value;
			}
		}
	}	
	
	foreach ($ex_attack as $value) {
		if (strpos ( $w, $value ) !== false && ! $is_wpg) {
			$atkcdt .= '_'.$value;
		}
	}

	return $atkcdt;
}

function get_hit_time($ky, $ws, $htr, $wk, $lmt, $infr, $inft, $wimpr, $is_wpg = false, $hitratebonus,$cl,$lv,$bsk='') {
	global $log, $nosta;
	if ($lmt == $nosta) {
		$wimpr *= 2;
		if ($is_wpg) {
			$wimpr *= 4;
		}
	}
	$rt=2;
	if (($cl==8)&&($lv>=15))  {$infr=$infr*1.5;}
	if (strpos ( $ky, 'r' ) !== false) {
		$atk_t = $ws >= 800 ? 6 : 2 + floor ( $ws / 200 );
		//if ($bsk=='steeldance') {$atk_t+=$rt;}
		if (($cl==97)&&($lv>=19)&&($wk=='G')) {$atk_t+=$rt;}
		if ($wk == 'C' || $wk == 'D' || $wk == 'F') {
			if ($lmt == $nosta) {
				$lmt = 99;
			}
			if ($atk_t > $lmt) {
				$atk_t = $lmt;
			}
		}
		if ($wk == 'G' && $atk_t > $lmt) {
			$atk_t = $lmt;
		}
		
		$ht_t = 0;
		$inf_t = 0;
		$wimp_t = 0;
		//if($htr>100){$htr=100;}
		for($i = 1; $i <= $atk_t; $i ++) {
			$dice = rand ( 0, 99 );
			$dice2 = rand ( 0, 99 );
			$dice3 = rand ( 0, 99 );
			if ($bsk=='assasinate') $dice=-1;
			if ($bsk=='eagleeye') $dice=-1;
			global $dcloak_crit;
			if ($dcloak_crit) $dice=-1;
			//if ($bsk=='steeldance') $dice=$dice/2;
			if ($bsk=='olaola') $dice=$dice/3;
			if ($bsk=='aim') $dice=$dice*0.85;
			if (($cl==5)&&($lv>=15)) $dice=$dice*0.6;
			if ($bsk=='ragestrike') $dice=$dice*2;
			if (($cl==1)&&($lv>=15)) $dice2=$dice2/3;
			if (($cl==8)&&($lv>=15)) $dice2=$dice2/2;
			if (($cl==5)&&($lv>=7)) $dice2=$dice2*0.4;
			if (($cl==2)&&($lv>=7)) $dice3=$dice3*1.18;
			if ($dice < $htr) {
				$ht_t ++;
				if ($dice2 < $infr) {
					$inf_t += $inft;
				}
				if ($dice3 < $wimpr) {
					$wimp_t ++;
				}
			}
			$htr *= 0.8 * $hitratebonus;
			$infr *= 0.9;
			$wimpr *= $wimpr <= 0 ? 1 : 1.2;
		}
	} else {
		$atk_t = 1;
		//if ($bsk=='steeldance') {$atk_t+=$rt;}
		if (($cl==97)&&($lv>=19)&&($wk=='G')) {$atk_t+=$rt;}
		$ht_t = 0;
		$inf_t = 0;
		$wimp_t = 0;
		for($i = 1; $i <= $atk_t; $i ++) {
			$dice = rand ( 0, 99 );
			$dice2 = rand ( 0, 99 );
			$dice3 = rand ( 0, 99 );
			if ($bsk=='assasinate') $dice=-1;
			if ($bsk=='eagleeye') $dice=-1;
			global $dcloak_crit;
			if ($dcloak_crit) $dice=-1;
			//if ($bsk=='steeldance') $dice=$dice/2;
			if ($bsk=='olaola') $dice=$dice/3;
			if ($bsk=='aim') $dice=$dice*0.85;
			if (($cl==5)&&($lv>=15)) $dice=$dice*0.6;
			if ($bsk=='ragestrike') $dice=$dice*2;
			if (($cl==1)&&($lv>=15)) $dice2=$dice2/3;
			if (($cl==8)&&($lv>=15)) $dice2=$dice2/2;
			if (($cl==5)&&($lv>=7)) $dice2=$dice2*0.4;
			if (($cl==2)&&($lv>=7)) $dice3=$dice3*1.18;
			if ($dice < $htr) {
				$ht_t ++;
				if ($dice2 < $infr) {
					$inf_t += $inft;
				}
				if ($dice3 < $wimpr) {
					$wimp_t ++;
				}
			}
			$htr *= 0.8 * $hitratebonus;
			$infr *= 0.9;
			$wimpr *= $wimpr <= 0 ? 1 : 1.2;
		}
	}
	if ($atk_t > 1 && $ht_t > 0) {
		//if ($bsk=='steeldance') {$log .= "<span class=\"lime\">舞钢提高了你的连击数！</span><br>";}
		if (($cl==97)&&($lv>=19)) {$log .= "<span class=\"lime\">正义提高了你的连击数！</span><br>";}
		$log .= "{$atk_t}次连续攻击命中<span class=\"yellow\">{$ht_t}</span>次！";
	}
	return Array ($atk_t, $ht_t, $inf_t, $wimp_t );
}

function getdefkey($w, $ah, $ab, $aa, $af, $at, $atkind,$bsk='') {
	global $ex_dmg_def;
	$defcdt = '';
	$eqpkey = $w . $ah . $ab . $aa . $af . $at . substr ( $atkind, 1, 1 );
	foreach(Array('B','b','h','R','-','*','+','t',) as $value){
		if (strpos ( $eqpkey, $value ) !== false) {
			$defcdt .= '_'.$value;
		}
	}
	if ($bsk!='storm'){
		if (strpos ( $eqpkey, 'A' ) !== false) {
			$defcdt .= '_P_K_G_C_D_F_J';
		} else {
			foreach(Array('P','K','G','C','D','F') as $value){
				if (strpos ( $eqpkey, $value ) !== false) {
					$defcdt .= '_'.$value;
				}
			}
			if (strpos($eqpkey,'G')!== false){
				$defcdt.='_J';
			}
		}
		if ($bsk!='boom'){
			foreach ($ex_dmg_def as $value) {
				if (strpos ( $eqpkey, $value ) !== false || strpos ( $eqpkey, 'a' ) !== false) {
					$defcdt .= '_'.$value;
				}
			}
		}else{
			$defcdt=str_replace('D','',$defcdt);
		}
	}
	return $defcdt;
}

function get_ex_dmg($nm, $sd, $clb, &$inf, $ky, $wk, $we, $ws, $dky,$lv,$bk) {
	if ($ky) {
		global $log, $exdmgname, $exdmginf, $ex_attack,$specialrate;
		global $ex_dmg_def, $ex_base_dmg,$ex_max_dmg, $ex_wep_dmg, $ex_skill_dmg, $ex_dmg_fluc, $ex_inf, $ex_inf_r, $ex_max_inf_r, $ex_skill_inf_r, $ex_inf_punish, $ex_good_wep, $ex_good_club;
		
		$ex_final_dmg = 0;
		$exinv = false;
		$ex_list = array();
		foreach ( $ex_attack as $ex_dmg_sign ) {
			if (strpos ( $ky, $ex_dmg_sign ) !== false){
				$ex_list[] = $ex_dmg_sign;
			}
		}
		if (strpos ( $dky, 'b' ) !== false && !empty($ex_list)){
			$dice = rand ( 0, 99);
			if ($dice < $specialrate['b']) {//几率4%
				$ex_final_dmg = 1;$exnum = 0;
				foreach ( $ex_attack as $ex_dmg_sign ) {
					if (strpos ( $ky, $ex_dmg_sign ) !== false) {
						$exnum ++;
					}
				}
				$log .= "<span class=\"red\">属性攻击的力量完全被防具吸收了！</span>只造成了<span class=\"red\">{$exnum}</span>点伤害！<br>";
				$exinv = true;
			}else{
				$log .= "纳尼？防具使属性攻击无效化的属性竟然失效了！";
			}
		}
		if(!$exinv){
			foreach ( $ex_list as $ex_dmg_sign ) {
				$dmgnm = $exdmgname [$ex_dmg_sign];
				$def = $ex_dmg_def [$ex_dmg_sign];
				$bdmg = $ex_base_dmg [$ex_dmg_sign];
				$mdmg = $ex_max_dmg [$ex_dmg_sign];
				$wdmg = $ex_wep_dmg [$ex_dmg_sign];
				$sdmg = $ex_skill_dmg [$ex_dmg_sign];
				$fluc = $ex_dmg_fluc [$ex_dmg_sign];
				if (in_array($ex_dmg_sign,array_keys($ex_inf))) {

					$dmginf = $exdmginf [$ex_inf[$ex_dmg_sign]];
					$ex_inf_sign = $ex_inf [$ex_dmg_sign];
					$infr = $ex_inf_r [$ex_inf_sign];
					$minfr = $ex_max_inf_r [$ex_inf_sign];
					$sinfr = $ex_skill_inf_r [$ex_inf_sign];
					$punish = $ex_inf_punish [$ex_dmg_sign];
					$e_htr = $ex_good_club [$ex_inf_sign] == $clb ? 20 : 0;
				} else {
					$ex_inf_sign = '';
					$punish = 1;
					$e_htr = 0;
				}
				$Pflag=false;
				if (($ex_dmg_sign=='p')&&(strpos($inf,'P')!==false)){
					$punish=2.3;
					$dmginf='中猛毒';
					$Pflag=true;
				}
				
				$wk_dmg_p = $ex_good_wep [$ex_dmg_sign] == $wk ? 2 : 1;
				$e_dmg = $bdmg + $we/$wdmg + $ws/$sdmg; 
				if(($mdmg>0)&&($wk!='H')){
					//$e_dmg = $e_dmg > $mdmg ? round($wk_dmg_p*$mdmg*rand(100 - $fluc, 100 + $fluc)/100) : round($wk_dmg_p*$e_dmg*rand(100 - $fluc, 100 + $fluc)/100);
					$e_dmg = round($wk_dmg_p*$mdmg*($e_dmg/($e_dmg+$mdmg/2))*rand(100 - $fluc, 100 + $fluc)/100);
				} else{
					$e_dmg =  round($wk_dmg_p*$e_dmg*rand(100 - $fluc, 100 + $fluc)/100);
				}
				if ($clb==20) $e_dmg *= 1.15;	//宝石骑士称号属性伤害加成
					
				//$e_dmg += round ( ($we / ($we + $wdmg) + $ws / ($ws + $sdmg)) * rand ( 100 - $fluc, 100 + $fluc ) / 200 * $bdmg * $wk_dmg_p );
				$ex_def_dice = rand(0,99);
				
				//锡安磁暴
				if (($clb==7)&&($lv>=11)&&($ex_dmg_sign=='e')){
					$rat=round($ws/2.5)+100;
					$e_dmg=$e_dmg*$rat/100;
					$log .= "<span class=\"yellow\">电击伤害被强化为{$rat}%！</span><br>";
				}
				
				if (($bk=='sting')&&($ex_dmg_sign=='p')){
					$e_dmg=$e_dmg*1.5;
					$log .= "<span class=\"yellow\">毒刺使毒性伤害变为150%！</span><br>";
				}
				
				if (($clb==21)&&($lv>=15)&&($ex_dmg_sign=='d')){
					$e_dmg=$e_dmg*4;
					$log .= "<span class=\"yellow\">娴熟使爆炸伤害变为400%！</span><br>";
				}
				
				if (($clb==8)&&($lv>=7)) {$ex_def_dice=$ex_def_dice+40;}
				if (strpos ( $dky, $def ) === false || $ex_def_dice > 90) {
					if(strpos ( $dky, $def ) !== false){
						$log .= "属性防御装备没能发挥应有的作用！";
					}
					//var_dump( $punish);
					if (($ex_inf_sign && strpos ( $inf, $ex_inf_sign ) !== false && $punish > 1)||($Pflag)) {
						$log .= "由于{$nm}已经{$dmginf}，{$dmgnm}伤害倍增！";
						$e_dmg *= $punish;
					} elseif ($ex_inf_sign && strpos ( $inf, $ex_inf_sign ) !== false && $punish < 1) {
						$log .= "由于{$nm}已经{$dmginf}，{$dmgnm}伤害减少！";
						$e_dmg *= $punish;
					} else {
						$e_htr += $infr + $ws * $sinfr;
						$e_htr = $e_htr > $minfr ? $minfr : $e_htr;
					}				
					
					$e_dmg = round($e_dmg);
					$log .= "{$dmgnm}造成了<span class=\"red\">{$e_dmg}</span>点额外伤害！<br>";
					$PPf=false;
					if (($clb==8)&&($lv>=15)&&($ex_inf_sign=='p')&&(strpos($inf,'P')===false)) {$PPf=true;}
					if ((!empty($ex_inf_sign) && (strpos ( $inf, $ex_inf_sign ) === false))||($PPf)) {
						$dice = rand ( 0, 99 );
						if (($clb==8)&&($lv>=15)) {$dice=floor($dice/2);}
						if (($dice < $e_htr)&&(!(($ex_inf_sign=='p')&&(strpos($inf,'P')!==false)))) {
							$eis=$ex_inf_sign;
							if (($ex_inf_sign=='p')&&($clb==8)&&($lv>=15)){
								$eis='P';
								$inf = str_replace('p','',$inf);
								$dmginf='<span class="purple">中猛毒</span>';
							}
							$inf .= $eis;
							if ($sd == 0) {
								global $w_combat_inf;
								$w_combat_inf .= $eis;
							}
							//echo $w_combat_inf;
							$log .= "并造成{$nm}{$dmginf}了！<br>";
							global $name,$w_name;
							if($nm == '你'){
								addnews($now,'inf',$w_name,$name,$ex_inf_sign);
							}else{
								addnews($now,'inf',$name,$w_name,$ex_inf_sign);
							}	
						}
					}
				} else {
					$e_dmg = round ( $e_dmg / 2 );
					$log .= "{$dmgnm}被防御效果抵消了！造成了<span class=\"red\">{$e_dmg}</span>点额外伤害！<br>";
				}
				
				
				$ex_final_dmg += $e_dmg;
			}
		}
		
		return $ex_final_dmg;
	} else {
		return 0;
	}
	/*
	if (strpos ( $ky, 'p' ) !== false) {
		$ex_dmg_sign = 'p';
		if ($clb == 8) {
			$e_htr = 20;
		} else {
			$e_htr = 0;
		}
	}
	if (strpos ( $ky, 'u' ) !== false) {
		$ex_dmg_sign = 'u';
		$e_htr = 0;
		if ($wk == 'G') {
			//echo 'g';
			$wk_dmg_p = 2;
		}
	}
	if (strpos ( $ky, 'i' ) !== false) {
		$ex_dmg_sign = 'i';
		$e_htr = 0;
	}
	if (isset ( $ex_dmg_sign )) {
		$dmgnm = $exdmgname [$ex_dmg_sign];
		$dmginf = $exdmginf [$ex_dmg_sign];
		$def = $ex_dmg_def [$ex_dmg_sign];
		$bdmg = $ex_base_dmg [$ex_dmg_sign];
		$wdmg = $ex_wep_dmg [$ex_dmg_sign];
		$sdmg = $ex_skill_dmg [$ex_dmg_sign];
		$fluc = $ex_dmg_fluc [$ex_dmg_sign];
		$infr = $ex_inf_r [$ex_dmg_sign];
		$minfr = $ex_max_inf_r [$ex_dmg_sign];
		$sinfr = $ex_skill_inf_r [$ex_dmg_sign];
		$punish = $ex_inf_punish [$ex_dmg_sign];
		$e_dmg = 1 + round ( ($we / ($we + $wdmg) + $ws / ($ws + $sdmg)) * rand (100 - $fluc,100 + $fluc ) / 200 * $bdmg * $wk_dmg_p );
		if (strpos ( $dky, $def ) == false) {
			if (strpos ( $inf, $ex_dmg_sign ) !== false && $punish > 1) {
				$log .= "由于{$nm}已经{$dmginf}，{$dmgnm}伤害倍增！";
				$e_htr = 0;
			} elseif (strpos ( $inf, $ex_dmg_sign ) !== false && $punish < 1) {
				$log .= "由于{$nm}已经{$dmginf}，{$dmgnm}伤害减少！";
				$e_htr = 0;
			} else {
				$e_htr += $infr + $ws * $sinfr;
				$e_htr = $e_htr > $minfr ? $minfr : $e_htr;
			}
			$e_dmg = round ( $e_dmg * $punish );
			$log .= "{$dmgnm}造成了<span class=\"red\">{$e_dmg}</span>点额外伤害！<br>";
			$dice = rand ( 0, 99 );
			if ($dice < $e_htr) {
				$inf .= $ex_dmg_sign;
				if ($sd == 0) {
					global $w_combat_inf;
					$w_combat_inf .= $ex_dmg_sign;
				}
				$log .= "并造成{$nm}{$dmginf}了！<br>";
			}
		} else {
			$e_dmg = round ( $e_dmg / 2 );
			$log .= "{$dmgnm}被防御效果抵消了！造成了<span class=\"red\">{$e_dmg}</span>点额外伤害！<br>";
		}
		return $e_dmg;
	} else {
		return;
	}
*/
}

function get_WF_p($w, $clb, $we) {
	global $log, ${$w . 'sp'}, ${$w . 'skills'};
	if (! empty ( $w )) {
		$factor = 0.5;
	} else {
		$we = $we > 0 ? $we : 1;
		if ($clb == 9) {
			include_once GAME_ROOT.'./include/game/clubskills.func.php';
			$spd0 = round ( 0.2*get_clubskill_bonus_spd($clb,${$w . 'skills'})*$we);
		} else {
			$spd0 = round ( 0.5*$we);
		}
		if ($spd0 >= ${$w . 'sp'}) {
			$spd = ${$w . 'sp'} - 1;
		} else {
			$spd = $spd0;
		}
		$factor = 0.5 + $spd / $spd0 / 2;
		$f = round ( 100 * $factor );
		$log .= "你消耗{$spd}点体力，发挥了灵力武器{$f}％的威力！";
		${$w . 'sp'} -= $spd;
	}
	return $factor;
}

function check_KP_wep($nm, $ht, &$wp, &$wk, &$we, &$ws, &$wsk) {
	global $log, $nosta;
	if ($ht > 0 && $ws == $nosta) {
		$we -= $ht;
		if ($nm == '你') {
			$log .= "{$nm}的{$wp}的攻击力下降了{$ht}！<br>";
		}
		if ($we <= 0) {
			$log .= "{$nm}的<span class=\"red\">$wp</span>使用过度，已经损坏，无法再装备了！<br>";
			$wp = '拳头';
			$wk = 'WN';
			$we = 0;
			$ws = $nosta;
			$wsk = '';
		}
	} elseif ($ht > 0 && $ws != $nosta) {
		$ws -= $ht;
		if ($nm == '你') {
			$log .= "{$nm}的{$wp}的耐久度下降了{$ht}！<br>";
		}
		if ($ws <= 0) {
			$log .= "{$nm}的<span class=\"red\">$wp</span>使用过度，已经损坏，无法再装备了！<br>";
			$wp = '拳头';
			$wk = 'WN';
			$we = 0;
			$ws = $nosta;
			$wsk = '';
		}
	}
	return;
}

function check_GCDF_wep($nm, $ht, &$wp, $wp_kind, &$wk, &$we, &$ws, &$wsk,$cl,$lv,$bk) {
	global $log, $nosta;
	if ((($wp_kind == 'C') || ($wp_kind == 'D')|| ($wp_kind == 'F')) && ($ws != $nosta)) {
		$ws -= $ht;
		if ($nm == '你') {
			$log .= "{$nm}用掉了{$ht}个{$wp}。<br>";
		}
		if ($ws <= 0) {
			$log .= "{$nm}的<span class=\"red\">$wp</span>用光了！<br>";
			$wp = '拳头';
			$wsk = '';
			$wk = 'WN';
			$we = 0;
			$ws = $nosta;
		}
	} elseif ((($wp_kind == 'G')||($wp_kind == 'J')) && ($ws != $nosta)) {
		if (($cl==4)&&($lv>=11)&&(strpos($wsk,'o')===false)&&(strpos('J',$wp_kind)===false)){
			$log .= "<span class=\"clan\">在奔流技能的作用下，{$nm}此次射击没有消耗弹药！</span><br>";
		}else{
			if ($bk=='slayer') $ht=$ws;
			$ws -= $ht;
			if ($nm == '你') {
				$log .= "{$nm}的{$wp}的弹药数减少了{$ht}。<br>";
			}
			if ($ws <= 0) {
				$log .= "{$nm}的<span class=\"red\">$wp</span>的弹药用光了！<br>";
				$ws = $nosta;
			}
		}
	}
	return;
}

function get_inf($nm, $ht, $wp_kind,$cl,$lv) {
	if ($ht > 0) {
		global $infatt;
		$infatt_dice = rand ( 1, 4 );
		if (($infatt_dice == 1) && (strpos ( $infatt [$wp_kind], 'b' ) !== false)) {
			$inf_att = 'b';
		} elseif (($infatt_dice == 2) && (strpos ( $infatt [$wp_kind], 'h' ) !== false)) {
			$inf_att = 'h';
		} elseif (($infatt_dice == 3) && (strpos ( $infatt [$wp_kind], 'a' ) !== false)) {
			$inf_att = 'a';
		} elseif (($infatt_dice == 4) && (strpos ( $infatt [$wp_kind], 'f' ) !== false)) {
			$inf_att = 'f';
		}
		if($nm == '你'){
			$w = '';
		} else {
			$w = 'w_';
		}
		if ($inf_att) {
			global $log, ${$w . 'ar' . $inf_att}, ${$w . 'ar' . $inf_att . 'k'}, ${$w . 'ar' . $inf_att . 'e'}, ${$w . 'ar' . $inf_att . 's'}, ${$w . 'ar' . $inf_att . 'sk'};
			if ((${$w . 'ar' . $inf_att . 's'})&&(!(($cl==8)&&($lv>=7)&&(rand(1,2)==1)))) {
				${$w . 'ar' . $inf_att . 's'} -= $ht;
				if ($nm == '你') {
					$log .= "你的${$w.'ar'.$inf_att}的耐久度下降了{$ht}！<br>";
				}
				if (${$w . 'ar' . $inf_att . 's'} <= 0) {
					$log .= "{$nm}的<span class=\"red\">${$w.'ar'.$inf_att}</span>受损过重，无法再装备了！<br>";
					${$w . 'ar' . $inf_att} = ${$w . 'ar' . $inf_att . 'k'} = ${$w . 'ar' . $inf_att . 'sk'} = '';
					${$w . 'ar' . $inf_att . 'e'} = ${$w . 'ar' . $inf_att . 's'} = 0;
				}
			} else {
				global $log, ${$w . 'inf'}, $infinfo;
				if (strpos ( ${$w . 'inf'}, $inf_att ) === false) {
					${$w . 'inf'} .= $inf_att;
					if ($w == 'w_') {
						global ${$w . 'combat_inf'};
						${$w . 'combat_inf'} .= $inf_att;
					}
					$log .= "{$nm}的<span class=\"red\">$infinfo[$inf_att]</span>部受伤了！<br>";
//					global $name,$w_name;
//					if($nm == '你'){
//						addnews($now,'inf',$w_name,$name,$inf_att);
//					}else{
//						addnews($now,'inf',$name,$w_name,$inf_att);
//					}					
				}
			}
		}
	}
	return;
}

function get_dmg_punish($nm, $dmg, &$hp, $a_ky,$cl,$lv) {
	if ($dmg >= 1000) {
		global $log;
		global $artk;//[u150925]偶像大师特判
		global $w_art;//书库结界
		if ($dmg < 2000) {
			$hp_d = floor ( $hp / 2 );
		} elseif ($dmg < 5000) {
			$hp_d = floor ( $hp * 2 / 3 );
		} else {
			$hp_d = floor ( $hp * 4 / 5 );
		}
		if ((strpos ( $a_ky, 'H' ) != false)||(($cl==18)&&($lv>=3))||(($cl==70)&&($lv>=11)&&($artk=='ss'))) {
			$hp_d = floor ( $hp_d / 10 );
		}
		if(($w_art=='深邃无限书库结界')&&($w_type)){
			$log .= "<span class=\"yellow\">书库娘的{$w_art}使你无法抗衡反噬之理！</span><br>";
			if ($dmg < 2000) {
				$hp_d = floor ( $hp / 2 );
			} elseif ($dmg < 5000) {
				$hp_d = floor ( $hp * 2 / 3 );
			} else {
				$hp_d = floor ( $hp * 4 / 5 );
			}
		}
		if((($nm=='年兽（？）')||($nm=='埃尔兰卡'))&&($type!=0)){
		//保险
		}else{
		$log .= "惨无人道的攻击对{$nm}自身造成了<span class=\"red\">$hp_d</span>点<span class=\"red\">反噬伤害！</span><br>";
		}
		$hp -= $hp_d;
		global $wep,$wepexp,$gemweponinfo;
		if(in_array($wep,$gemweponinfo)){
			$wepexp+=$hp_d;
		}
	}
	return;
}

function exprgup(&$lv_a, $lv_d, &$exp, $isplayer, &$rg, $active) {
	global $log;
	global $rage;
	$expup = round ( ($lv_d - $lv_a) / 3 );
	$expup = $expup > 0 ? $expup : 1;
	if ($active)
	{
		global $now,$aurac;
		if ($now<=$aurac) $expup*=2;
	}
	else
	{
		global $now,$w_aurac;
		if ($now<=$w_aurac) $expup*=2;
	}
	$exp += $expup;
	//$log .= "$isplayer 的经验值增加 $expup 点<br>";
	if ($isplayer) {
		global $upexp;
		$nl_exp = $upexp;
	} else {
		global $w_upexp;
		$nl_exp = $w_upexp;
	}
	if ($exp >= $nl_exp) {
		include_once GAME_ROOT . './include/state.func.php';
		lvlup ( $lv_a, $exp, $isplayer );
	}
	$rgup = round ( ($lv_a - $lv_d) / 3 );
	if ($rgup<=0) $rgup=1;
	
	if ($active)
	{
		global $club,$lvl;
		if ($club==16 && $lvl>=19) $rgup*=2;
	}
	else
	{
		global $w_club,$w_lvl;
		if ($w_club==16 && $w_lvl>=19) $rgup*=2;
	}
	$rg += $rgup;
	if ($rage<100) {$rage++;}
	if ($rg>100) $rg=100;
	if ($rage>100) $rage=100;
	return;
}

function addnoise($wp_kind, $wsk, $ntime, $npls, $nid1, $nid2, $nmode) {
	if ((($wp_kind == 'G') && (strpos ( $wsk, 'S' ) === false)) || ($wp_kind == 'F')) {
		global $noisetime, $noisepls, $noiseid, $noiseid2, $noisemode;
		$noisetime = $ntime;
		$noisepls = $npls;
		$noiseid = $nid1;
		$noiseid2 = $nid2;
		$noisemode = $nmode;
		save_combatinfo ();
	} elseif (strpos ( $wsk, 'd' ) !== false){
		global $noisetime, $noisepls, $noiseid, $noiseid2, $noisemode;
		$noisetime = $ntime;
		$noisepls = $npls;
		$noiseid = $nid1;
		$noiseid2 = $nid2;
		$noisemode = 'D';
		save_combatinfo ();
	}
	if (strlen($wp_kind)>=3){
		global $noisetime, $noisepls, $noiseid, $noiseid2, $noisemode,$wep;
		$noisetime = $ntime;
		$noisepls = $npls;
		$noiseid = $nid1;
		$noiseid2 = $nid2;
		$noisemode = $wp_kind;
		save_combatinfo ();
	}
	
	return;
}

function check_gender($nm_a, $nm_d, $gd_a, $gd_d, $a_ky) {
	$gd_dmg_p = 1;
	if ((((strpos ( $a_ky, "l" ) !== false) && ($gd_a != $gd_d)) || ((strpos ( $a_ky, "g" ) !== false) && ($gd_a == $gd_d))) && (! rand ( 0, 4 ))) {
		global $log;
		$log .= "<span class=\"red\">{$nm_a}被{$nm_d}迷惑，无法全力攻击！</span>";
		$gd_dmg_p = 0;
	} elseif ((((strpos ( $a_ky, "l" ) !== false) && ($gd_a == $gd_d)) || ((strpos ( $a_ky, "g" ) !== false) && ($gd_a != $gd_d))) && (! rand ( 0, 4 ))) {
		global $log;
		$log .= "<span class=\"red\">{$nm_a}被{$nm_d}激怒，伤害加倍！</span>";
		$gd_dmg_p = 2;
	}
	return $gd_dmg_p;
}

function npc_changewep($active = 0){
	global $now,$log,$w_name,$w_type,$w_club,$w_wep, $w_wepk, $w_wepe, $w_weps, $w_itm0, $w_itmk0, $w_itme0, $w_itms0, $w_itm1, $w_itmk1, $w_itme1, $w_itms1, $w_itm2, $w_itmk2, $w_itme2, $w_itms2, $w_itm3, $w_itmk3, $w_itme3, $w_itms3, $w_itm4, $w_itmk4, $w_itme4, $w_itms4, $w_itm5, $w_itmk5, $w_itme5, $w_itms5,$w_itm6, $w_itmk6, $w_itme6, $w_itms6, $w_wepsk, $w_arbsk, $w_arhsk, $w_arask, $w_arfsk, $w_artsk, $w_itmsk0, $w_itmsk1, $w_itmsk2, $w_itmsk3, $w_itmsk4, $w_itmsk5, $w_itmsk6;
	global $w_arb, $w_arbk, $w_arbe, $w_arbs, $w_arh, $w_arhk, $w_arhe, $w_arhs, $w_ara, $w_arak, $w_arae, $w_aras, $w_arf, $w_arfk, $w_arfe, $w_arfs, $w_art, $w_artk, $w_arte, $w_arts;
	global $wepk,$wepsk,$arbsk,$arask,$arhsk,$arfsk,$artsk,$artk,$rangeinfo,$ex_dmg_def;
	if((!$w_name || !$w_type || $w_club != 98)&&($w_name!=='埃尔兰卡')){return;}
	
	$dice = rand(0,99);
	if($dice > 50){
		$weplist = array();
		$wepklist = Array($w_wepk);$weplist2 = array();
		for($i=0;$i<=6;$i++){
			if(${'w_itms'.$i} && ${'w_itme'.$i} && strpos(${'w_itmk'.$i},'W')===0){
				$weplist[] = Array($i,${'w_itm'.$i},${'w_itmk'.$i},${'w_itme'.$i},${'w_itms'.$i},${'w_itmsk'.$i});
				$wepklist[] = ${'w_itmk'.$i};
			}
		}
		if(!empty($weplist)){
			$wepklist = array_unique($wepklist);
			$temp_def_key = getdefkey($wepsk,$arhsk,$arbsk,$arask,$arfsk,$artsk,$artk);
			$wepkAI = $wepskAI = true;
			if(strpos($temp_def_key,'_P_K_G_C_D_F')!==false || strpos($temp_def_key,'B')!==false){$wepkAI = false;}
			if(count($wepklist)<=1){$wepkAI = false;}
			if(strpos($temp_def_key,'_q_U_I_D_E')!==false || strpos($temp_def_key,'b')!==false){$wepskAI = false;}
			
			if($wepkAI){
				if(!$wepk){$wepk_temp = 'WN';}else{$wepk_temp = $wepk;}
				foreach($weplist as $val){
					if($rangeinfo[substr($val[2],1,1)] >= $rangeinfo[substr($wepk_temp,1,1)] && strpos($temp_def_key,substr($val[2],1,1))===false){
						$weplist2[] = $val;
					}
				}
				if($weplist2){
					$weplist = $weplist2;
				}				
			}
			if($wepskAI && $weplist){
				$minus = array();
				foreach($weplist as $val){
					foreach($ex_dmg_def as $key => $val2){
						if(strpos($val[5],$key)!==false && strpos($temp_def_key,$val2)!==false){
							$minus[] = $val;
						}
					}
				}
				//var_dump($minus);
				if(count($minus) < count($weplist)){
					$weplist = array_diff($weplist,$minus);
				}				
			}
		}
//		var_dump($wepkAI);echo '<br>';var_dump($wepskAI);echo '<br>';
//		var_dump($weplist);
//		if(!empty($weplist2)){
//			$weplist = $weplist2;
//		}
		
		if(!empty($weplist)){
			$oldwep = $w_wep;
			shuffle($weplist);
			$chosen = $weplist[0];$c = $chosen[0];
			//var_dump($chosen);
			${'w_itm'.$c} = $w_wep;${'w_itmk'.$c} = $w_wepk;${'w_itme'.$c} = $w_wepe;${'w_itms'.$c} = $w_weps;${'w_itmsk'.$c} = $w_wepsk;
			$w_wep = $chosen[1]; $w_wepk = $chosen[2]; $w_wepe = $chosen[3];$w_weps = $chosen[4];$w_wepsk = $chosen[5];
			//list($c,$w_wep,$w_wepk,$w_wepe,$w_weps,$w_wepsk) = $chosen;
			$log .= "<span class=\"yellow\">{$w_name}</span>将手中的<span class=\"yellow\">{$oldwep}</span>卸下，装备了<span class=\"yellow\">{$w_wep}</span>！<br>";
		}
	}
	return;
}

function npc_chat($type,$nm, $mode) {
	global $npccanchat,$npcchaton;
	if ($npcchaton && in_array($type,$npccanchat)) {
		global $npcchat, $w_itmsk0, $w_hp, $w_mhp;
		$chatcolor = $npcchat[$type][$nm]['color'];
		if(!empty($chatcolor)){
			$npcwords = "<span class = \"{$chatcolor}\">";
		}else{
			$npcwords = '<span>';
		}
		switch ($mode) {
			case 'attack' :
				if (empty ( $w_itmsk0 )) {
					$npcwords .= "{$npcchat[$type][$nm][0]}";
					$w_itmsk0 = '1';
				} elseif ($w_hp > ($w_mhp / 2)) {
					$dice = rand ( 1, 2 );
					$npcwords .= "{$npcchat[$type][$nm][$dice]}";
				} else {
					$dice = rand ( 3, 4 );
					$npcwords .= "{$npcchat[$type][$nm][$dice]}";
				}
				break;
			case 'defend' :
				if (empty ( $w_itmsk0 )) {
					$npcwords .= "{$npcchat[$type][$nm][0]}";
					$w_itmsk0 = '1';
				} elseif ($w_hp > ($w_mhp / 2)) {
					$dice = rand ( 5, 6 );
					$npcwords .= "{$npcchat[$type][$nm][$dice]}";
				} else {
					$dice = rand ( 7, 8 );
					$npcwords .= "{$npcchat[$type][$nm][$dice]}";
				}
				break;
			case 'death' :
				$npcwords .= "{$npcchat[$type][$nm][9]}";
				break;
			case 'escape' :
				$npcwords .= "{$npcchat[$type][$nm][10]}";
				break;
			case 'cannot' :
				$npcwords .= "{$npcchat[$type][$nm][11]}";
				break;
			case 'critical' :
				$npcwords .= "{$npcchat[$type][$nm][12]}";
				break;
			case 'kill' :
				$npcwords .= "{$nm}对你说道：{$npcchat[$type][$nm][13]}";
				break;
		}
		$npcwords .= '</span><br>';
		return $npcwords;
	} elseif ($mode == 'death') {
		global $lwinfo;
		if (is_array ( $lwinfo [$type] )) {
			$lastword = $lwinfo [$type] [$nm];
		} else {
			$lastword = $lwinfo [$type];
		}
		$npcwords = "<span class=\"yellow\">“{$lastword}”</span><br>";
		return $npcwords;
	} else {
		return;
	}
}

function count_good_man_card($active){
	$goodmancard = 0;
	if($active){
		global $itm0,$itmk0,$itms0,$itm1,$itmk1,$itms1,$itm2,$itmk2,$itms2,$itm3,$itmk3,$itms3,$itm4,$itmk4,$itms4,$itm5,$itmk5,$itms5,$itm6,$itmk6,$itms6;
		
		for($i=0;$i<=6;$i++){
			if(${'itms'.$i} && ${'itm'.$i} == '好人卡' && ${'itmk'.$i} == 'Y'){
				$goodmancard += ${'itms'.$i};
			}
		}
	}else{
		global $w_itm0,$w_itmk0,$w_itms0,$w_itm1,$w_itmk1,$w_itms1,$w_itm2,$w_itmk2,$w_itms2,$w_itm3,$w_itmk3,$w_itms3,$w_itm4,$w_itmk4,$w_itms4,$w_itm5,$w_itmk5,$w_itms5,$w_itm6,$w_itmk6,$w_itms6;
		
		for($i=0;$i<=6;$i++){
			if(${'w_itms'.$i} && ${'w_itm'.$i} == '好人卡' && ${'w_itmk'.$i} == 'Y'){
				$goodmancard += ${'w_itms'.$i};
			}
		}
	}
	return $goodmancard;
}
?>
