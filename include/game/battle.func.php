<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}



function findenemy(&$w_pdata) {
	global $log,$mode,$main,$cmd,$battle_title,$attinfo,$skillinfo,$wepk,$wp,$wk,$wg,$wc,$wd,$wf,$weps,$wepe,$nosta,$club,$lvl,$fog,$db,$tablepre,$name,$money;
	global $w_type,$w_name,$w_gd,$w_sNo,$w_icon,$w_hp,$w_mhp,$w_sp,$w_msp,$w_rage,$w_wep,$w_wepk,$w_wepe,$w_lvl,$w_pose,$w_tactic,$w_inf;//,$itmsk0;
	global $rage, $souls, $mhp;
	global $ss;//[u150924]偶像大师怒气技能需要歌魂
	
	if (CURSCRIPT == 'botservice') echo "mode=enemy_spotted\n";
	
	$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$name'");
	$sktime = $db->result($result, 0);
	if (!$sktime) $sktime=0;
	
	$battle_title = '发现敌人';
	extract($w_pdata,EXTR_PREFIX_ALL,'w');
	init_battle();
	$assper=strlen($w_inf)*50+150;
	$egoper=100+$lvl;
	$fper=$lvl*4+50;
	if ($fper>170) $fper=170;
	$pper=floor($wp/5)+1;
	$bper=floor($wg/3)+1;
	$gdm=floor($money/20)+1;
	$supress_cost=floor($mhp*0.15)+1;
	$wepss=0;
	if ($weps!=$nosta) $wepss=$weps;
	if ($wepss>$wepe*2) $wepss=$wepe*2;
	if ($wepss>2000) $wepss=2000;
	if ($gdm>250) $gdm=250;
	if (strpos($w_inf,'P')!==false) {$assper+=50;}
	$log .= "你发现了敌人<span class=\"red\">$w_name</span>！<br>对方好像完全没有注意到你！<br>";
	
//	$cmd .= '现在想要做什么？<br><br>';
//	$cmd .= '向对手大喊：<br><input size="30" type="text" name="message" maxlength="60"><br><br>';
//	$cmd .= '<input type="hidden" name="mode" value="combat">';
	if (CURSCRIPT !== 'botservice') 
	{
		$w1 = substr($wepk,1,1);
		$w2 = substr($wepk,2,1);
		if (($w2=='0')||($w2=='1')) {$w2='';}
		if((($w1 == 'G')||($w1=='J'))&&($weps==$nosta)){ $w1 = 'P'; }
//	$cmd .= '<input type="radio" name="command" id="'.$w1.'" value="'.$w1.'" checked><a onclick=sl("'.$w1.'"); href="javascript:void(0);">'."$attinfo[$w1] (${$skillinfo[$w1]})".'</a><br>';
//	if($w2) {
//		$cmd .= '<input type="radio" name="command" id="'.$w2.'" value="'.$w2.'"><a onclick=sl("'.$w2.'"); href="javascript:void(0);">'."$attinfo[$w2] (${$skillinfo[$w2]})".'</a><br>';
//	}
		include template('battlecmd');
		$cmd = ob_get_contents();
		ob_clean();
	}
//	$cmd .= '<input type="radio" name="command" id="back" value="back"><a onclick=sl("back"); href="javascript:void(0);" >逃跑</a><br>';


	$main = 'battle';
	
	return;
}

function findteam(&$w_pdata){
	global $gametype,$log,$mode,$main,$cmd,$battle_title,$gamestate;
	global $w_type,$w_name,$w_gd,$w_sNo,$w_icon,$w_hp,$w_mhp,$w_sp,$w_msp,$w_rage,$w_wep,$w_wepk,$w_wepe,$w_lvl,$w_pose,$w_tactic,$w_inf;//,$itmsk0;
	global $rage;
	
	if($gametype!=2 && $gamestate>=40){
		$log .= '<span class="yellow">连斗阶段所有队伍取消！</span><br>';
		
		$mode = 'command';
		return;
	}
	$battle_title = '发现队友';
	extract($w_pdata,EXTR_PREFIX_ALL,'w');
	init_battle(1);
	
	$log .= "你发现了队友<span class=\"yellow\">$w_name</span>！<br>";
	for($i = 1;$i <= 6; $i++){
		global ${'itm'.$i},${'itme'.$i},${'itms'.$i};
	}
	include template('findteam');
	$cmd = ob_get_contents();
	ob_clean();
//	$cmd .= '现在想要做什么？<br><br>';
//	$cmd .= '留言：<br><input size="30" type="text" name="message" maxlength="60"><br><br>';
//	$cmd .= '想要转让什么？<input type="hidden" name="mode" value="senditem"><br><input type="radio" name="command" id="back" value="back" checked><a onclick=sl("back"); href="javascript:void(0);" >不转让</a><br><br>';
//	for($i = 1;$i < 6; $i++){
//		global ${'itms'.$i};
//		if(${'itms'.$i}) {
//			global ${'itm'.$i},${'itmk'.$i},${'itme'.$i};
//			$cmd .= '<input type="radio" name="command" id="itm'.$i.'" value="itm'.$i.'"><a onclick=sl("itm'.$i.'"); href="javascript:void(0);" >'."${'itm'.$i}/${'itme'.$i}/${'itms'.$i}".'</a><br>';
//		}
//	}
	$main = 'battle';
	return;
}

function findcorpse(&$w_pdata){
	global $log,$mode,$main,$battle_title,$cmd,$iteminfo,$itemspkinfo;
	global $w_type,$w_name,$w_gd,$w_sNo,$w_icon,$w_hp,$w_mhp,$w_wep,$w_wepk,$w_wepe,$w_lvl,$w_pose,$w_tactic,$w_inf;//,$itmsk0;
	global $w_itembag;
	global $rage;
	
	$battle_title = '发现尸体';
	extract($w_pdata,EXTR_PREFIX_ALL,'w');
	init_battle(1);
	
	if (CURSCRIPT == 'botservice')
	{
		echo "mode=corpse\n";
		foreach (Array('w_wep','w_arb','w_arh','w_ara','w_arf','w_art') as $w_value) 
			if (${$w_value.'s'})
			{
				echo "{$w_value}=".${$w_value}."\n";
				echo "{$w_value}k=".${$w_value.'k'}."\n";
				echo "{$w_value}e=".${$w_value.'e'}."\n";
				echo "{$w_value}s=".${$w_value.'s'}."\n";
				echo "{$w_value}sk=".${$w_value.'sk'}."\n";
			}
		foreach (Array('1','2','3','4','5','6') as $w_itm_id) 
			if (${'w_itms'.$w_itm_id})
			{
				echo "w_itm{$w_itm_id}=".${'w_itm'.$w_itm_id}."\n";
				echo "w_itmk{$w_itm_id}=".${'w_itmk'.$w_itm_id}."\n";
				echo "w_itme{$w_itm_id}=".${'w_itme'.$w_itm_id}."\n";
				echo "w_itms{$w_itm_id}=".${'w_itms'.$w_itm_id}."\n";
				echo "w_itmsk{$w_itm_id}=".${'w_itmsk'.$w_itm_id}."\n";
			}
	}
	else
	{	
		$main = 'battle';
		$log .= '你发现了<span class="red">'.$w_name.'</span>的尸体！<br>';
		foreach (Array('w_wepk','w_arbk','w_arhk','w_arak','w_arfk','w_artk','w_itmk0','w_itmk1','w_itmk2','w_itmk3','w_itmk4','w_itmk5','w_itmk6') as $w_k_value) {
			if(${$w_k_value}){
				foreach($iteminfo as $info_key => $info_value){
					if(strpos(${$w_k_value},$info_key)===0){
						${$w_k_value.'_words'} = $info_value;
						break;
					}
				}
			}
		}
		foreach (Array('w_wepsk','w_arbsk','w_arhsk','w_arask','w_arfsk','w_artsk','w_itmsk0','w_itmsk1','w_itmsk2','w_itmsk3','w_itmsk4','w_itmsk5','w_itmsk6') as $w_sk_value) {
			${$w_sk_value.'_words'} = '';
			if(${$w_sk_value} && ! is_numeric(${$w_sk_value})){
				
				for ($i = 0; $i < strlen($w_sk_value)-1; $i++) {
					$sub = substr(${$w_sk_value},$i,1);
					if(!empty($sub)){
						${$w_sk_value.'_words'} .= $itemspkinfo[$sub];
					}
				}
				
			}
		}
		$witemlist = json_decode($w_itembag,true);
		include template('corpse');
		$cmd = ob_get_contents();
		ob_clean();
	}
	return;
}

function findhostage(&$w_pdata){
	global $gametype,$log,$mode,$main,$cmd,$battle_title,$gamestate;
	global $w_type,$w_name,$w_gd,$w_sNo,$w_icon,$w_hp,$w_mhp,$w_sp,$w_msp,$w_rage,$w_wep,$w_wepk,$w_wepe,$w_lvl,$w_pose,$w_tactic,$w_inf;
	global $w_arb,$w_arbs;
	global $rage;

	$battle_title = '发现人质';
	extract($w_pdata,EXTR_PREFIX_ALL,'w');
	init_battle(1);
	$log .= "你发现了在你控制下的人质<span class=\"yellow\">$w_name</span>！<br>";
	include template('findhostage');
	$cmd = ob_get_contents();
	ob_clean();
	$main = 'battle';
	return;
}
function ctrlhostage(){
	global $gametype,$db,$tablepre,$log,$mode,$main,$command,$cmd,$battle_title,$pls,$plsinfo,$message,$now,$name,$w_log,$teamID,$gamestate,$action;
	global $lvl,$money,$pls,$plsinfo,$name;
	$hostageid = str_replace('hijack','',$action);
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$hostageid'");
	if(!$db->num_rows($result)){
		$log .= "对方不存在！<br>";
		$action = '';
		$mode = 'command';
		return;
	}
	$edata = $db->fetch_array($result);
	if($edata['hp'] <= 0){
		$log .= '<span class="yellow">'.$edata['name'].'</span>已经死亡。<br>';
		$mode = 'command';
		$action = '';
		return;
	}elseif(($edata['art']!=$name.'的人质证明')||($edata['arts']!=1)){
		$log .= '<span class="yellow">'.$edata['name'].'</span>并非你的人质。<br>';
		$mode = 'command';
		$action = '';
		return;
	}
	if($command != 'back'){
		if($command=='threat'){
			$log .= "<span class=\"lime\">你威胁了{$edata['name']}一番，要求对方乖♂乖♂站♂好。</span><br>";
			$w_log = "<span class=\"lime\">{$name}威胁了你一番，要求你乖♂乖♂站♂好</span><br>";
			if(!$edata['type']){logsave($edata['pid'],$now,$w_log,'c');}
		}elseif($command=='loot'){
			if(($lvl<=30)&&($money<=4000)){
				$gmoney=round(($edata['lvl']*2)+1);$money+=$gmoney;
				$log .= "<span class=\"lime\">你开始勒索{$edata['name']}，要求其家人提供赎金，很快你成功从这只肉票身上获得了{$gmoney}元！</span><br>";
			}else{
				$log .= "<span class=\"red\">你作为一个资深的恐怖分子，不应该用这种低级的方式赚钱！你为自己下意识的行为感到羞愧。</span><br>";
			}
		}elseif($command=='onbodybomb'){
			foreach(Array(1,2,3,4,5,6) as $imn){
			global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
			if(strpos(${'itm'.$imn},'捆绑式炸药')!==false && ${'itme'.$imn} > 0 ){
				${'itms'.$imn} = ${'itme'.$imn} = 0;${'itm'.$imn} = ${'itmk'.$imn} = ${'itmsk'.$imn}='';	
				$bodybomb=true;
				break;
				}
			}
			if($bodybomb){
				$log.='<span class="lime">你使用了捆绑式炸药，你成功的将你的人质们改造成了肉弹！</span><br/>';
				$db->query("UPDATE {$tablepre}players SET arb='捆绑式炸药',arbk='DB',arbe='1',arbs='8192',arbsk='dV' WHERE pid='$hostageid'");
				addnews($now,'hijack',$name,$edata['name'],'onbodybomb');
			}else{
				$log.='<span class="red">你身上没有捆绑式炸药！</span><br />';
			}
		}elseif($command=='offbodybomb'){
			$log.='<span class="lime">你粗鲁的拆下了人质身上的炸药，感觉它没爆炸已经是万幸了！</span><br/>';
			$db->query("UPDATE {$tablepre}players SET arb='内衣',arbk='DN',arbe='0',arbs='∞',arbsk='' WHERE pid='$hostageid'");
			addnews($now,'hijack',$name,$edata['name'],'offbodybomb');
		}elseif($command=='freehtg'){
			$db->query("UPDATE {$tablepre}players SET arb='内衣',arbk='DN',arbe='0',arbs='∞',arbsk='',art='',artk='',arte='0',arts='0',artsk='' WHERE pid='$hostageid'");
			$log.='<span class="lime">你释放了这位可怜的人质，现在他自由了！</span><br/>';
			addnews($now,'hijack',$name,$edata['name'],'freehtg');
		}else{
			$log.='你思考了一下，还是决定什么也不干，人质被你的神经质吓了一跳。<br/>';
		}
	}
	$action = '';
	$mode = 'command';
	return;
}

function findhasi(&$w_pdata){
	global $gametype,$log,$mode,$main,$cmd,$battle_title,$gamestate,$lvl;
	global $w_type,$w_name,$w_gd,$w_sNo,$w_icon,$w_hp,$w_mhp,$w_sp,$w_msp,$w_rage,$w_wep,$w_wepk,$w_wepe,$w_lvl,$w_pose,$w_tactic,$w_inf;
	global $w_arb,$w_arbs;
	global $rage;

	$battle_title = '发现粉丝';
	extract($w_pdata,EXTR_PREFIX_ALL,'w');
	$ham=$w_lvl*12;
	init_battle(1);
	$log .= "你发现了你的粉丝<span class=\"yellow\">$w_name</span>！<br>";
	include template('findhasi');
	$cmd = ob_get_contents();
	ob_clean();
	$main = 'battle';
	return;
}
function ctrlhasi(){
	global $gametype,$db,$tablepre,$log,$mode,$main,$command,$cmd,$battle_title,$pls,$plsinfo,$message,$now,$name,$teamID,$gamestate,$action;
	global $lvl,$money,$pls,$plsinfo,$name,$rage;
	$hid = str_replace('hasi','',$action);
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$hid'");
	if(!$db->num_rows($result)){
		$log .= "对方不存在！<br>";
		$action = '';
		$mode = 'command';
		return;
	}
	$edata = $db->fetch_array($result);
	if($edata['hp'] <= 0){
		$log .= '<span class="yellow">'.$edata['name'].'</span>已经死亡。<br>';
		$mode = 'command';
		$action = '';
		return;
	}elseif(($edata['art']!=$name.'语录')||(!$edata['type'])){
		$log .= '<span class="yellow">'.$edata['name'].'</span>并非你的人质。<br>';
		$mode = 'command';
		$action = '';
		return;
	}
	extract ( $edata, EXTR_PREFIX_ALL, 'w' );
	$ham=$w_lvl*12;
	if($command != 'back'){
		if($command=='ad'){
			$log .= "<span class=\"lime\">你给了<span class=\"yellow\">{$edata['name']}</span>一点人生的经验，<span class=\"yellow\">{$edata['name']}</span>的基础攻防提高了。</span><br>";
			$w_att+=20;$w_def+=20;
		}elseif($command=='sk'){
			$log .= "<span class=\"lime\">你提高了<span class=\"yellow\">{$edata['name']}</span>的姿势水平，<span class=\"yellow\">{$edata['name']}</span>的熟练度增加了。</span><br>";
			$w_wp+=10;$w_wk+=10;$w_wf+=10;$w_wc+=10;$w_wg+=10;$w_wd+=10;
		}elseif($command=='hp'){
			$log .= "<span class=\"lime\">你告诉<span class=\"yellow\">{$edata['name']}</span>一句中国古话<span class=\"red\">闷声发大财</span>，<span class=\"yellow\">{$edata['name']}</span>的生命上限提高了。</span><br>";
			$w_mhp=$w_mhp+50;
			$w_hp=round($w_hp+$w_mhp*0.4);
			if ($w_hp>$w_mhp) {$w_hp=$w_mhp;}
			if ($w_sp>$w_msp) {$w_sp=$w_msp;}
		}elseif($command=='go'){
			$log .= "<span class=\"lime\"><span class=\"yellow\">{$edata['name']}</span>离开了虚拟战场，你获得了<span class=\"yellow\">{$ham}</span>元。</span><br>";
			$db->query( "UPDATE {$tablepre}players SET hp='0',state='48',weps='0',arbs='0',arhs='0',aras='0',arfs='0',arts='0',itms0='0',itms1='0',itms2='0',itms3='0',itms4='0',itms5='0',itms6='0',money='0' WHERE pid='$hid'" );
			$money+=$ham;
			$action = '';
			$mode = 'command';
			return;	
		}elseif($command=='we'){
			if (($rage>=30)&&($lvl>=11)){
				$log .= "<span class=\"lime\">你为<span class=\"yellow\">{$edata['name']}</span>灌输了西方的先进理论，<span class=\"yellow\">{$edata['name']}</span>被大幅强化了！</span><br>";
				$w_mhp=round($w_mhp*1.25)+1;
				$w_wp+=50;$w_wk+=50;$w_wf+=50;$w_wc+=50;$w_wg+=50;$w_wd+=50;
				if ($w_wepe>0) $w_wepe+=50;
				if ($w_hp<$w_mhp) {$w_hp=$w_mhp;}
				if ($w_sp<$w_msp) {$w_sp=$w_msp;}
				$rage=$rage-30;
			}else{
				$log .= "你还不能使用这一技能！<br>";
				$action = '';
				$mode = 'command';
				return;
			}
		}
	}
	if (($command=='ad')||($command=='hp')||($command=='sk')||($command=='we'))
		$db->query( "UPDATE {$tablepre}players SET hp='$w_hp',mhp='$w_mhp',sp='$w_sp',wp='$w_wp',wk='$w_wk',wc='$w_wc',wg='$w_wg',wd='$w_wd',wf='$w_wf',wepe='$w_wepe',att='$w_att',def='$w_def' WHERE pid='$hid'" );
	$action = '';
	$mode = 'command';
	return;
}
function senditem(){
	global $gametype,$db,$tablepre,$log,$mode,$main,$command,$cmd,$battle_title,$pls,$plsinfo,$message,$now,$name,$w_log,$teamID,$gamestate,$action;
	global $rage;
	
	$mateid = str_replace('team','',$action);
	if(!$mateid || strpos($action,'team')===false){
		$log .= '<span class="yellow">你没有遇到队友，或已经离开现场！</span><br>';
		$action = '';
		$mode = 'command';
		return;
	}
	if($gametype!=2 && $gamestate>=40){
		$log .= '<span class="yellow">连斗阶段无法赠送物品！</span><br>';
		$action = '';
		$mode = 'command';
		return;
	}
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$mateid'");
	if(!$db->num_rows($result)){
		$log .= "对方不存在！<br>";
		$action = '';
		$mode = 'command';
		return;
	}

	$edata = $db->fetch_array($result);
	if($edata['pls'] != $pls) {
		$log .= '<span class="yellow">'.$edata['name'].'</span>已经离开了<span class="yellow">'.$plsinfo[$pls].'</span>。<br>';
		$mode = 'command';
		$action = '';
		return;
	} elseif($edata['hp'] <= 0) {
		$log .= '<span class="yellow">'.$edata['name'].'</span>已经死亡，不能接受物品。<br>';
		$mode = 'command';
		$action = '';
		return;
	} elseif(!$teamID || $edata['teamID']!=$teamID){
		$log .= '<span class="yellow">'.$edata['name'].'</span>并非你的队友，不能接受物品。<br>';
		$mode = 'command';
		$action = '';
		return;
	}

	if($message){
//		foreach ( Array('<','>',';',',') as $value ) {
//			if(strpos($message,$value)!==false){
//				$message = str_replace ( $value, '', $message );
//			}
//		}
		$log .= "<span class=\"lime\">你对{$edata['name']}说：“{$message}”</span><br>";
		$w_log = "<span class=\"lime\">{$name}对你说：“{$message}”</span><br>";
		if(!$edata['type']){logsave($edata['pid'],$now,$w_log,'c');}
	}
	
	if($command != 'back'){
		$itmn = substr($command, 3);
		global ${'itm'.$itmn},${'itmk'.$itmn},${'itme'.$itmn},${'itms'.$itmn},${'itmsk'.$itmn};
		if (!${'itms'.$itmn}) {
			$log .= '此道具不存在！';
			$action = '';
			$mode = 'command';
			return;
		}
		$itm = & ${'itm'.$itmn};
		$itmk = & ${'itmk'.$itmn};
		$itme = & ${'itme'.$itmn};
		$itms = & ${'itms'.$itmn};
		$itmsk = & ${'itmsk'.$itmn};
		
		if(strpos($itmsk,'v')!==false){
			$log .= '这件道具与你的灵魂绑定了，你无法赠送此道具！';
			$action = '';
			$mode = 'command';
			return;
		}
		
		if(strpos($itmsk,'V')!==false){
			$log .= '这件装备被诅咒了，你无法赠送此道具！';
			$action = '';
			$mode = 'command';
			return;
		}
		
		//global $w_pid,$w_name,$w_pass,$w_type,$w_endtime,$w_gd,$w_sNo,$w_icon,$w_club,$w_hp,$w_mhp,$w_sp,$w_msp,$w_att,$w_def,$w_pls,$w_lvl,$w_exp,$w_money,$w_bid,$w_inf,$w_rage,$w_pose,$w_tactic,$w_killnum,$w_state,$w_wp,$w_wk,$w_wg,$w_wc,$w_wd,$w_wf,$w_teamID,$w_teamPass,$w_wep,$w_wepk,$w_wepe,$w_weps,$w_arb,$w_arbk,$w_arbe,$w_arbs,$w_arh,$w_arhk,$w_arhe,$w_arhs,$w_ara,$w_arak,$w_arae,$w_aras,$w_arf,$w_arfk,$w_arfe,$w_arfs,$w_art,$w_artk,$w_arte,$w_arts,$w_itm0,$w_itmk0,$w_itme0,$w_itms0,$w_itm1,$w_itmk1,$w_itme1,$w_itms1,$w_itm2,$w_itmk2,$w_itme2,$w_itms2,$w_itm3,$w_itmk3,$w_itme3,$w_itms3,$w_itm4,$w_itmk4,$w_itme4,$w_itms4,$w_itm5,$w_itmk5,$w_itme5,$w_itms5,$w_itm6,$w_itmk6,$w_itme6,$w_itms6,$w_wepsk,$w_arbsk,$w_arhsk,$w_arask,$w_arfsk,$w_artsk,$w_itmsk0,$w_itmsk1,$w_itmsk2,$w_itmsk3,$w_itmsk4,$w_itmsk5,$w_itmsk6,$nick;
		global $w_pid, $w_name, $w_pass, $w_type, $w_endtime,$w_deathtime, $w_gd, $w_sNo, $w_icon, $w_club, $w_hp, $w_mhp, $w_sp, $w_msp, $w_att, $w_def, $w_pls, $w_lvl, $w_exp, $w_money, $w_bid, $w_inf, $w_rage, $w_pose, $w_tactic, $w_killnum, $w_state, $w_wp, $w_wk, $w_wg, $w_wc, $w_wd, $w_wf, $w_teamID, $w_teamPass;
		global $w_wep, $w_wepk, $w_wepe, $w_weps, $w_arb, $w_arbk, $w_arbe, $w_arbs, $w_arh, $w_arhk, $w_arhe, $w_arhs, $w_ara, $w_arak, $w_arae, $w_aras, $w_arf, $w_arfk, $w_arfe, $w_arfs, $w_art, $w_artk, $w_arte, $w_arts, $w_itm0, $w_itmk0, $w_itme0, $w_itms0, $w_itm1, $w_itmk1, $w_itme1, $w_itms1, $w_itm2, $w_itmk2, $w_itme2, $w_itms2, $w_itm3, $w_itmk3, $w_itme3, $w_itms3, $w_itm4, $w_itmk4, $w_itme4, $w_itms4, $w_itm5, $w_itmk5, $w_itme5, $w_itms5,$w_itm6, $w_itmk6, $w_itme6, $w_itms6, $w_wepsk, $w_arbsk, $w_arhsk, $w_arask, $w_arfsk, $w_artsk, $w_itmsk0, $w_itmsk1, $w_itmsk2, $w_itmsk3, $w_itmsk4, $w_itmsk5, $w_itmsk6;
		global $w_combat_inf, $w_rp,$w_action,$w_achievement,$w_skills,$w_skillpoint;
	
		extract($edata,EXTR_PREFIX_ALL,'w');

		for($i = 1;$i <= 6; $i++){
			if(!${'w_itms'.$i}) {
				${'w_itm'.$i} = $itm;
				${'w_itmk'.$i} = $itmk;
				${'w_itme'.$i} = $itme;
				${'w_itms'.$i} = $itms;
				${'w_itmsk'.$i} = $itmsk;
				$log .= "你将<span class=\"yellow\">${'w_itm'.$i}</span>送给了<span class=\"yellow\">$w_name</span>。<br>";
				$w_log = "<span class=\"yellow\">$name</span>将<span class=\"yellow\">${'w_itm'.$i}</span>送给了你。";
				if(!$w_type){logsave($w_pid,$now,$w_log,'t');}
				addnews($now,'senditem',$nick.' '.$name,$w_name,$itm);
				w_save($w_pid);
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
				$action = '';
				return;
			}
		}
		$log .= "<span class=\"yellow\">$w_name</span> 的包裹已经满了，不能赠送物品。<br>";
	}
	$action = '';
	$mode = 'command';
	return;
}

?>
