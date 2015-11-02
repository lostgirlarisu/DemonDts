<?php


if(!defined('IN_GAME')) {
	exit('Access Denied');
}

function getword(){
	global $db,$tablepre,$name,$motto,$lastword,$killmsg;
	
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$name'");
	$userinfo = $db->fetch_array($result);
	$motto = $userinfo['motto'];
	$lastword = $userinfo['lastword'];
	$killmsg = $userinfo['killmsg'];
	
}

function chgword($nmotto,$nlastword,$nkillmsg) {
	global $db,$tablepre,$name,$log;
	
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$name'");
	$userinfo = $db->fetch_array($result);

//	foreach ( Array('<','>',';',',','\\\'','\\"') as $value ) {
//		if(strpos($nmotto,$value)!==false){
//			$nmotto = str_replace ( $value, '', $nmotto );
//		}
//		if(strpos($nlastword,$value)!==false){
//			$nlastword = str_replace ( $value, '', $nlastword );
//		}
//		if(strpos($nkillmsg,$value)!==false){
//			$nkillmsg = str_replace ( $value, '', $nkillmsg );
//		}
//	}

	
	if($nmotto != $userinfo['motto']) {
		$log .= $nmotto == '' ? '口头禅已清空。' : '口头禅变更为<span class="yellow">'.$nmotto.'</span>。<br>';
	}
	if($nlastword != $userinfo['lastword']) {
		$log .= $nlastword == '' ? '遗言已清空。' : '遗言变更为<span class="yellow">'.$nlastword.'</span>。<br>';
	}
	if($nkillmsg != $userinfo['killmsg']) {
		$log .= $nkillmsg == '' ? '杀人留言已清空。' : '杀人留言变更为<span class="yellow">'.$nkillmsg.'</span>。<br>';
	}

	$db->query("UPDATE {$tablepre}users SET motto='$nmotto', lastword='$nlastword', killmsg='$nkillmsg' WHERE username='$name'");
	
	$mode = 'command';
	return;
}

function chgpassword($oldpswd,$newpswd,$newpswd2){
	global $db,$tablepre,$name,$log;
	
	if (!$oldpswd || !$newpswd || !$newpswd2){
		$log .= '放弃了修改密码。<br />';
		$mode = 'command';
		return;
	} elseif ($newpswd !== $newpswd2) {
		$log .= '<span class="red">两次输入的新密码不一致。</span><br />';
		$mode = 'command';
		return;
	}
	
	$oldpswd = md5($oldpswd);$newpswd = md5($newpswd);
	
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$name'");
	$userinfo = $db->fetch_array($result);
	
	if($oldpswd == $userinfo['password']){
		$db->query("UPDATE {$tablepre}users SET `password` ='$newpswd' WHERE username='$name'");
		$log .= '<span class="yellow">密码已修改！</span><br />';
		
		//include_once GAME_ROOT.'./include/global.func.php';
		
		gsetcookie('pass',$newpswd);
		$mode = 'command';
		return;
	}else{
		$log .= '<span class="red">原密码输入错误！</span><br />';
		$mode = 'command';
		return;
	}
}
function oneonone($sb,$sf){
	global $db,$gold,$mode,$now,$tablepre,$log,$name,$art,$arte,$artk,$arts,$artsk;
	$mode = 'command';
	if($sb == $sf){
		$log .= "不能自我约战。<br>";
		return;
	}
	if(($artk=='XX')||($artk=='XY')){
		$log .= "不能重复约战。<br>";
		return;
	}
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE name='$sb' AND type = 0");
	$edata = $db->fetch_array($result);
	$a1=$edata['art'];
	$a2=$edata['artk'];
	$a3=$edata['pid'];
	$a4=$edata['hp'];
	if (!$a3){
		$log .= "该ID不存在！<br>";
		return;
	}
	if (!$a4){
		$log .= "不能和死人约战。<br>";
		return;
	}
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE name='$sf' AND type = 0");
	$edata = $db->fetch_array($result);
	$a1=$edata['money'];
	if ($a1<1500){
		$log .= "需要携带1500G才能约战。<br>";
		return;
	}
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$sb'");
	$edata = $db->fetch_array($result);
	$a1=$edata['ip'];
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$sf'");
	$edata = $db->fetch_array($result);
	$a2=$edata['ip'];
	if($a1 == $a2){
		//$log .= "不能自我约战。<br>";
		//return;
	}
	if(preg_match('/[,|<|>|&|;|#|"|\s|\p{C}]+/u',$sb)) { $log.='请不要尝试注入……';return; }
	$art=$sb;$artk='XY';$arte=1;$arts=1;$artsk='';
	$taunt=$sf.'喊道：“'.$sb.'，来，战♂个♂痛♂快！”';
	$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,msg) VALUES ('4','$now','$name','$taunt')");
	$result = $db->query("SELECT * FROM {$tablepre}players WHERE name='$sb' AND type = 0");
	$edata = $db->fetch_array($result);
	$a1=$edata['art'];
	$a2=$edata['artk'];
	if (($a1==$sf)&&($a2=='XY')){
		$artk='XX';
		$db->query ( "UPDATE {$tablepre}players SET artk='XX' WHERE `name` ='$sb' AND type=0 ");
		$taunt='约战成立！';
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,msg) VALUES ('4','$now','$name','$taunt')");
	}
	return;
}

function adtsk(){
	global $log,$mode,$club,$wep,$wepk,$wepe,$weps,$wepsk,$lvl;
	if($wepk == 'WN' || !$wepe || !$weps){
		$log .= '<span class="red">你没有装备武器，无法改造！</span><br />';
		$mode = 'command';
		return;
	}
	if (strpos($wepsk,'j')!==false){
		$log.='多重武器不能改造。<br>';
		$mode='command';
		return;
	}
	if (strpos($wepsk,'O')!==false){
		$log.='进化武器不能改造。<br>';
		$mode='command';
		return;
	}
	if($club == 7){//电脑社，电气改造
		$position = 0;
		foreach(Array(1,2,3,4,5,6) as $imn){
			global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
			if(strpos(${'itmk'.$imn},'B')===0 && ${'itme'.$imn} > 0 ){
				$position = $imn;
				break;
			}
		}
		if($position){
			if(strpos($wepsk,'e')!==false){
				$log .= '<span class="red">武器已经带有电击属性，不用改造！</span><br />';
				$mode = 'command';
				return;
			}elseif(strlen($wepsk)>=5){
				$log .= '<span class="red">武器属性数目达到上限，无法改造！</span><br />';
				$mode = 'command';
				return;
			}
			
			
			${'itms'.$position}-=1;
			$itm = ${'itm'.$position};
			$log .= "<span class=\"yellow\">用{$itm}改造了{$wep}，{$wep}增加了电击属性！</span><br />";
			$wep = '电气'.$wep;
			$wepsk .= 'e';
			if(${'itms'.$position} == 0){
				$log .= "<span class=\"red\">$itm</span>用光了。<br />";
				${'itm'.$position} = ${'itmk'.$position} = ${'itmsk'.$position} = '';
				${'itme'.$position} =${'itms'.$position} =0;				
			}
			$mode = 'command';
			return;
		}else{
			$log .= '<span class="red">你没有电池，无法改造武器！</span><br />';
			$mode = 'command';
			return;
		}
	}elseif($club == 8){//带毒改造
		$position = 0;
		foreach(Array(1,2,3,4,5,6) as $imn){
			global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
			if(${'itm'.$imn} == '毒药' && ${'itmk'.$imn} == 'Y' && ${'itme'.$imn} > 0 ){
				$position = $imn;
				break;
			}
		}
		if($position){
			if(strpos($wepsk,'p')!==false){
				$log .= '<span class="red">武器已经带毒，不用改造！</span><br />';
				$mode = 'command';
				return;
			}elseif(strlen($wepsk)>=5){
				$log .= '<span class="red">武器属性数目达到上限，无法改造！</span><br />';
				$mode = 'command';
				return;
			}

			$wepsk .= 'p';
			$log .= "<span class=\"yellow\">用毒药为{$wep}淬毒了，{$wep}增加了带毒属性！</span><br />";
			$wep = '毒性'.$wep;
			if ($lvl>=3) {
				$wepe=floor($wepe*1.1)+1;
				if ($weps!='∞')	{$weps=floor($weps*1.1)+1;}
				$log .= "<span class=\"yellow\">淬炼</span>技能强化了你的武器。<br />";
			}
			${'itms'.$position}-=1;
			$itm = ${'itm'.$position};
			if(${'itms'.$position} == 0){
				$log .= "<span class=\"red\">$itm</span>用光了。<br />";
				${'itm'.$position} = ${'itmk'.$position} = ${'itmsk'.$position} = '';
				${'itme'.$position} =${'itms'.$position} =0;				
			}
			$mode = 'command';
			return;
		}else{
			$log .= '<span class="red">你没有毒药，无法给武器淬毒！</span><br />';
			$mode = 'command';
			return;
		}
	}else{
		$log .= '<span class="red">你不懂得如何改造武器！</span><br />';
		$mode = 'command';
		return;
	}
}

function trap_adtsk($which){
	global $log,$mode,$club,${'itm'.$which},${'itmk'.$which},${'itme'.$which},${'itms'.$which};
	if(strpos(${'itmk'.$which},'T')!==0){
		$log .= '<span class="red">这个物品不是陷阱，无法改造！</span><br />';
		$mode = 'command';
		return;
	}
	if(${'itmk'.$which}=='TOc' || ${'itmk'.$which}=='TNc'){
		$log .= '<span class="red">奇迹陷阱不允许改造！</span><br />';
		$mode = 'command';
		return;
	}
	if($club == 7){//电脑社，电气改造
		if (strpos(${'itm'.$which},'电气')!==false){
			$log .= '<span class="red">陷阱已经带有电击属性，不用改造！</span><br />';
			$mode='command';
			return;
		}
		$position = 0;
		foreach(Array(1,2,3,4,5,6) as $imn){
			global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
			if(strpos(${'itmk'.$imn},'B')===0 && ${'itme'.$imn} > 0 ){
				$position = $imn;
				break;
			}
		}
		if($position){
			${'itms'.$position}-=1;
			$itm = ${'itm'.$position}; $citm=${'itm'.$which};
			$log .= "<span class=\"yellow\">用{$itm}改造了{$citm}，{$citm}增加了电击属性！</span><br />";
			${'itm'.$which} = '电气'.${'itm'.$which};
			if(${'itms'.$position} == 0){
				$log .= "<span class=\"red\">$itm</span>用光了。<br />";
				${'itm'.$position} = ${'itmk'.$position} = ${'itmsk'.$position} = '';
				${'itme'.$position} =${'itms'.$position} =0;				
			}
			$mode = 'command';
			return;
		}else{
			$log .= '<span class="red">你没有电池，无法改造陷阱！</span><br />';
			$mode = 'command';
			return;
		}
	}elseif($club == 8){//带毒改造
		if (strpos(${'itm'.$which},'毒性')!==false){
			$log .= '<span class="red">陷阱已经带毒，不用改造！</span><br />';
			$mode='command';
			return;
		}
		$position = 0;
		foreach(Array(1,2,3,4,5,6) as $imn){
			global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
			if(${'itm'.$imn} == '毒药' && ${'itmk'.$imn} == 'Y' && ${'itme'.$imn} > 0 ){
				$position = $imn;
				break;
			}
		}
		if($position){
			${'itms'.$position}-=1;
			$itm = ${'itm'.$position}; $citm=${'itm'.$which};
			$log .= "<span class=\"yellow\">用{$itm}改造了{$citm}，{$citm}增加了带毒属性！</span><br />";
			${'itm'.$which} = '毒性'.${'itm'.$which};
			if(${'itms'.$position} == 0){
				$log .= "<span class=\"red\">$itm</span>用光了。<br />";
				${'itm'.$position} = ${'itmk'.$position} = ${'itmsk'.$position} = '';
				${'itme'.$position} =${'itms'.$position} =0;				
			}
			$mode = 'command';
			return;
		}else{
			$log .= '<span class="red">你没有毒药，无法给武器淬毒！</span><br />';
			$mode = 'command';
			return;
		}
	}else{
		$log .= '<span class="red">你不懂得如何改造陷阱！</span><br />';
		$mode = 'command';
		return;
	}
}

function syncro($sb){
	global $itm0,$itmk0,$itme0,$itms0,$itmsk0,$name,$nick;
	list($n,$k,$e,$s,$sk,$r)=explode('_',$sb);
	$itm0=$n;$itmk0=$k;$itme0=$e;$itms0=$s;$itmsk0=$sk;
	if ($r>0) {addnews($now,'syncmix',$nick.' '.$name,$itm0);}
	else {addnews($now,'overmix',$nick.' '.$name,$itm0);}
	include_once GAME_ROOT.'./include/game/itemmain.func.php';
	itemget();
	return;
}
function weaponswap(){
	global $log,$mode,$club,$wep,$wepk,$wepe,$weps,$wepsk,$gamecfg;
	if (strpos($wepsk,'j')===false){
		$log.='你的武器不能变换。<br>';
		$mode = 'command';
		return;
		}
	$oldw=$wep;
	$file = config('wepchange',$gamecfg);
	$wlist = openfile($file);
	$wnum = count($wlist)-1;
	for ($i=0;$i<=$wnum;$i++){
		list($on,$nn,$nk,$ne,$ns,$nsk) = explode(',',$wlist[$i]);
		if ($wep==$on){
			$wep=$nn;$wepk=$nk;$wepe=$ne;$weps=$ns;$wepsk=$nsk;
			$log.="<span class=\"yellow\">{$oldw}</span>变换成了<span class=\"yellow\">{$wep}</span>。<br>";
			return;
		}
	}
	$log.="<span class=\"yellow\">{$oldw}</span>由于改造或其他原因不能变换。<br>";
}
function chginf($infpos){
	global $log,$mode,$inf,$inf_sp,$inf_sp_2,$sp,$infinfo,$exdmginf,$club;
	$normalinf = Array('h','b','a','f');
	if(!$infpos){$mode = 'command';return;}
	if($infpos == 'A'){  
		if($club == 16){
			$spdown = 0;
			foreach($normalinf as $value){
				if(strpos($inf,$value)!== false){
					$spdown += $inf_sp;
				}
			}
			if(!$spdown){
				$log .= '你并没有受伤！';
				$mode = 'command';
				return;
			}elseif($sp <= $spdown){
				$log .= "包扎全部伤口需要{$spdown}点体力，先回复体力吧！";
				$mode = 'command';
				return;
			}
			$inf = str_replace('h','',$inf);
			$inf = str_replace('b','',$inf);
			$inf = str_replace('a','',$inf);
			$inf = str_replace('f','',$inf);
			$sp -= $spdown;
			$log .= "消耗<span class=\"yellow\">$spdown</span>点体力，全身伤口都包扎好了！";
			$mode = 'command';
			return;
		}else{
			$log .= '你不懂得怎样快速包扎伤口！';
			$mode = 'command';
			return;
		}
	}elseif(in_array($infpos,$normalinf) && strpos($inf,$infpos) !== false){	//普通伤口
		if($sp <= $inf_sp) {
			$log .= "包扎伤口需要{$inf_sp}点体力，先回复体力吧！";
			$mode = 'command';
			return;
		} else {
			$inf = str_replace($infpos,'',$inf);
			$sp -= $inf_sp;
			$log .= "消耗<span class=\"yellow\">$inf_sp</span>点体力，{$infinfo[$infpos]}<span class=\"red\">部</span>的伤口已经包扎好了！";
			$mode = 'command';
			return;
		}
	}elseif(strpos($inf,$infpos) !== false){  //特殊状态
		if($club == 16){
			if($sp <= $inf_sp_2) {
				$log .= "处理异常状态需要{$inf_sp_2}点体力，先回复体力吧！";
				$mode = 'command';
				return;
			} else {
				$inf = str_replace($infpos,'',$inf);
				$sp -= $inf_sp_2;
				$log .= "消耗<span class=\"yellow\">$inf_sp_2</span>点体力，{$exdmginf[$infpos]}状态已经完全治愈了！";
				$mode = 'command';
				return;
			}
		}else{
			$log .= '你不懂得怎样治疗异常状态！';
			$mode = 'command';
			return;
		}
	}else{
		$log .= '你不需要包扎这个伤口！';
		$mode = 'command';
		return;
	}
}

function chkpoison($itmn){
	global $log,$mode,$club;
	if($club != 8){
		$log .= '你不会查毒。';
		$mode = 'command';
		return;
	}

	if ( $itmn < 1 || $itmn > 6 ) {
		$log .= '此道具不存在，请重新选择。';
		$mode = 'command';
		return;
	}

	global ${'itm'.$itmn},${'itmk'.$itmn},${'itme'.$itmn},${'itms'.$itmn},${'itmsk'.$itmn};
	$itm = & ${'itm'.$itmn};
	$itmk = & ${'itmk'.$itmn};
	$itme = & ${'itme'.$itmn};
	$itms = & ${'itms'.$itmn};
	$itmsk = & ${'itmsk'.$itmn};

	if(!$itms) {
		$log .= '此道具不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	if(strpos($itmk,'P') === 0) {
		$log .= '<span class="red">'.$itm.'有毒！</span>';
	} else {
		$log .= '<span class="yellow">'.$itm.'是安全的。</span>';
	}
	$mode = 'command';
	return;
}

function press_bomb(){
	global $log,$mode,$club,$wp,$wk,$wg,$wc,$wd,$wf,$mhp,$hp,$msp,$sp,$att,$def,$rage,$lvl;
	if($club != 99){
		$log .= '你的称号不能使用该技能。';
		$mode = 'command';
		return;
	}

	$club=24;
	$wp=ceil($wp*1.3); $wk=ceil($wk*1.3); $wg=ceil($wg*1.3); $wc=ceil($wc*1.3); $wd=ceil($wd*1.3); $wf=ceil($wf*1.3);
	$mhp=ceil($mhp*1.25); $hp=ceil($hp*1.25); $msp=ceil($msp*1.1); $sp=ceil($sp*1.1); 
	$att=ceil($att*1.2); $def=ceil($def*1.4); 
	$log.="你按下了X按钮，你突然感觉到一股力量贯通全身！"; 
	$mode = 'command';
	return;
}

function gemming_itme_buff(&$itm,&$itmk,&$itme,&$itms,&$itmsk,$lb,$ub)
{
	global $nosta, $log;
	if ($itms==$nosta) 
	{
		$up_e=rand(round($lb*0.85),round($ub*0.85)); 
		$log.="你的装备<span class=\"yellow\">{$itm}</span>的效果值增加了<span class=\"yellow\">{$up_e}</span>点！<br>";
		$itme+=$up_e;
	}
	else
	{
		$up_all=rand($lb,$ub); 
		$up_e=ceil(1.0*$up_all*$itme/($itme+$itms));
		$up_s=floor(1.0*$up_all*$itms/($itme+$itms));
		$log.="你的装备<span class=\"yellow\">{$itm}</span>的效果值增加了<span class=\"yellow\">{$up_e}</span>点，耐久值增加了<span class=\"yellow\">{$up_s}</span>点！<br>";	
		$itme+=$up_e; $itms+=$up_s;
	}
}

function gemming($t1, $t2)	//宝石骑士宝石buff技能
{
	global $log,$mode,$club,$itemspkinfo,$name,$nosta;
	if ($club!=20)
	{
		$log .= '你的称号不能使用该技能。<br>';
		$mode = 'command';
		return;
	}
	
	if ($t1!='wep' && $t1!='arb' && $t1!='arh' && $t1!='ara' && $t1!='arf')
	{
		$log.='你只能给你的武器/防具增加属性。<br>';
		$mode = 'command';
		return;
	}
	
	global ${$t1},${$t1.'k'},${$t1.'e'},${$t1.'s'},${$t1.'sk'};
	$itm=&${$t1}; $itmk=&${$t1.'k'}; $itme=&${$t1.'e'}; $itms=&${$t1.'s'}; $itmsk=&${$t1.'sk'};
	
	if ($t1=='wep'|| !$itme || !$itms)
	{
		if ($itmk=='WN')
		{
			$log.='你试图改造你的武器，但是你没有装备武器。<br>';
			$mode = 'command';
			return;
		}
		if (strpos($itmsk,'O')!==false){
			$log.='进化武器不能改造。<br>';
			$mode='command';
			return;
		}
	}
	else
	{
		if (($itms <= 0) && ($itms != $nosta)) 
		{
			$log.='本防具不存在，请重新选择。<br>';
			$mode = 'command';
			return;
		}
	}
	
	if(strlen($itmsk)>=5){
		$log .= '你选择的物品属性数目已达到上限，无法改造！<br>';
		$mode = 'command';
		return;
	}
			
	$t2=(int)$t2;
	if ($t2<1 || $t2>6)
	{
		$log.='你选择的宝石/方块不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	global ${'itm'.$t2},${'itmk'.$t2},${'itme'.$t2},${'itms'.$t2},${'itmsk'.$t2};
	$gem=&${'itm'.$t2}; $gemk=&${'itmk'.$t2}; $geme=&${'itme'.$t2}; $gems=&${'itms'.$t2}; $gemsk=&${'itmsk'.$t2};
	
	if (($gems <= 0) && ($gems != $nosta)) 
	{
		$log.='你选择的宝石/方块不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	$buff=Array();
	
	if ($gem=='红色方块')	//火焰
		if ($t1=='wep')
			$buff=Array(Array(35,'u'));
		else  $buff=Array(Array(30,'P'),Array(70,'U'));
	else  if ($gem=='黄色方块')	//重辅
		if ($t1=='wep')
			$buff=Array(Array(100,'c'));
		else  $buff=Array(Array(30,'D'),Array(70,'c'));
	else  if ($gem=='蓝色方块')	//冻气
		if ($t1=='wep')
			$buff=Array(Array(35,'i'));
		else  $buff=Array(Array(30,'G'),Array(70,'I'));
	else  if ($gem=='绿色方块')	//带毒
		if ($t1=='wep')
			$buff=Array(Array(35,'p'));
		else  $buff=Array(Array(30,'K'),Array(70,'q'));
	else  if ($gem=='金色方块')	//电击
		if ($t1=='wep')
			$buff=Array(Array(35,'e'));
		else  $buff=Array(Array(30,'C'),Array(70,'E'));
	else  if ($gem=='银色方块')	//音波
		if ($t1=='wep')
			$buff=Array(Array(35,'w'));
		else  $buff=Array(Array(30,'F'),Array(70,'W'));
	else  if ($gem=='红宝石方块')	//火焰/灼焰
		if ($t1=='wep')
			$buff=Array(Array(70,'u'),Array(30,'f'));
		else  $buff=Array(Array(100,'U'));
	else  if ($gem=='蓝宝石方块')	//冻气/冰华
		if ($t1=='wep')
			$buff=Array(Array(70,'i'),Array(30,'k'));
		else  $buff=Array(Array(100,'I'));
	else  if ($gem=='绿宝石方块')	//随机防御属性
	{	
		$buff=Array(Array(15,'C'),Array(15,'D'),Array(15,'F'),Array(15,'G'),Array(15,'K'),Array(15,'P'),Array(2,'A'),Array(2,'a'),Array(2,'l'),Array(2,'g'),Array(2,'H'));
	}
	else  if ($gem=='水晶方块')	//连击/HP制御
		if ($t1=='wep')
			$buff=Array(Array(1,'r'));
		else  $buff=Array(Array(5,'H'));
	else  if ($gem=='黑色方块')	//贯穿
		if ($t1=='wep')
			$buff=Array(Array(5,'n'));
		else  $buff=Array(Array(5,'m'));
	else  if ($gem=='白色方块')	//冲击
		if ($t1=='wep')
			$buff=Array(Array(5,'N'));
		else  $buff=Array(Array(5,'M'));
	else 
	{
		$log.="你的物品不是合法的宝石或方块。<br>请参阅帮助获得所有合法的宝石或方块及它们的对应改造属性的列表。<br>";
		$mode = 'command';
		return;
	}
		
	$dice=rand(1,100); $flag=0;
	$log.="你将<span class=\"yellow\">{$gem}</span>镶嵌到了<span class=\"yellow\">{$itm}</span>上。<br>";
	
	$lb=10; $ub=20;
	if (strpos($gem,'宝石') !== false) { $lb=round($lb*1.75); $ub=round($ub*1.75); }	//宝石强化效果更高
	
	foreach ($buff as $value)
	{
		if ($dice<=$value[0])
		{
			$flag=1;
			gemming_itme_buff($itm,$itmk,$itme,$itms,$itmsk,$lb,$ub);
			$log.="同时，你的装备<span class=\"yellow\">{$itm}</span>还获得了“<span class=\"yellow\">{$itemspkinfo[$value[1]]}</span>”属性！<br>";
			include_once GAME_ROOT.'./include/news.func.php';
			addnews ( 0, 'gemming', $name, $gem, $itm, $itemspkinfo[$value[1]]);
			if (strpos($itmsk,$value[1]) === false) $itmsk.=$value[1];
			break;
		}
		else  $dice-=$value[0];
	}
	
	if (!$flag) 
	{
		$lb=round($lb/2); $ub=round($ub/2);
		gemming_itme_buff($itm,$itmk,$itme,$itms,$itmsk,$lb,$ub);
		$log.="但是你的装备并没有获得额外属性。看起来运气不太好的样子。<br>";
	}
	
	$gems--;
	$log.="消耗了一枚{$gem}。<br>";
	if ($gems<=0)
	{
		$log.="{$gem}用完了。<br>";
		$gem=''; $gemk=''; $gems=0; $geme=0; $gemsk='';
	}
	$mode='command';
}

function trapcvt($t2)	//恐怖份子陷阱转换功能
{
	global $log,$mode,$club,$itemspkinfo,$name;
	global $wd;
/*	if ($club!=21)
	{
		$log .= '你的称号不能使用该技能。<br>';
		$mode = 'command';
		return;
	}*/
	
	$t2=(int)$t2;
	if ($t2<1 || $t2>6)
	{
		$log.='你选择的道具不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	global ${'itm'.$t2},${'itmk'.$t2},${'itme'.$t2},${'itms'.$t2},${'itmsk'.$t2};
	$itm=&${'itm'.$t2}; $itmk=&${'itmk'.$t2}; $itme=&${'itme'.$t2}; $itms=&${'itms'.$t2}; $itmsk=&${'itmsk'.$t2};
	
	if ($itms==$nosta)
	{
		$log.='你选择的道具耐久是无限，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	$log.="你小心翼翼地操作着陷阱里复杂的导线和机械……<br>";
	$basic_dice=50;$add_dice=round($wd/4);$reduce_dice=round($itme/10);
	$final_dice=$basic_dice+$add_dice-$reduce_dice;
	if($final_dice<30) $final_dice=30;
	elseif($final_dice>90) $final_dice=90;
	if (rand(1,100)>$final_dice)
	{
		$log.="<span class=\"yellow\">“嗑哒……”</span><br>突然，你听见了一个细微的金属碰撞声音。<br><span class=\"clan\">“糟糕…… 难道……”</span><br>还没反应过来，陷阱就在你手中爆炸了。<br>";
		global $hp; 
		$damage=rand(1,$itme);
		$log.="你受到了<span class=\"red\">{$damage}</span>点伤害！<br>";
		
		$itms--; $tmp_itm=$itm;
		$log.="消耗了一枚{$itm}。<br>";
		if ($itms<=0)
		{
			$log.="{$itm}用完了。<br>";
			$itm=''; $itmk=''; $itms=0; $itme=0; $itmsk='';
		}
		
		if ($hp<=0)
		{
			$log.='<span class="red">你被杀死了！</span><br>';
			$hp=0;
			include_once GAME_ROOT . './include/state.func.php';
			death ('failtrapcvt','','',$tmp_itm);
		}
		return;
	}
	
	$log.="<span class=\"clan\">“呼……”</span><br>你接完最后一根导线，擦掉脸上的汗水，长长地出了一口气。改装成功了。<br><br>";
	global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
	$itm0='便携式'.$itm; $itmk0='WD'; $itme0=$itme; $itms0=1; $itmsk0='d';
	include_once GAME_ROOT.'./include/news.func.php';
	addnews ( 0, 'trapcvt', $name, $itm, $itm0);
			
	$itms--;
	$log.="消耗了一枚{$itm}。<br>";
	if ($itms<=0)
	{
		$log.="{$itm}用完了。<br>";
		$itm=''; $itmk=''; $itms=0; $itme=0; $itmsk='';
	}
	
	include_once GAME_ROOT.'./include/game/itemmain.func.php';
	itemfind();
}
function wepbomb()
{
	global $wep,$wepk,$wepe,$weps,$wepsk,$wd;
	if($wepk=='WN'){
		$log.="求求你告诉我怎么把拳头改造成爆炸物？！<br>";
		return;
	}
	if($wepk=='WD'){
		$log.="武器已经是爆炸物了，不需要改造！<br>";
		return;
	}
	$flag=0;
	for($i=1;$i<=6;$i++){
		global ${'itm'.$i},${'itmk'.$i},${'itme'.$i},${'itms'.$i},${'itmsk'.$i};
		if(${'itm'.$i}=='火药' && ${'itms'.$i}>0){
			$flag=$i;
			break;
		}
	}
	if(!$flag){
		$log.="你的背包里没有火药，无法改造武器！<br>";
		return;
	}
	$basic_dice=30;$add_dice=round($wd/3);$reduce_dice=round($itme/10);
	$d_flag=false;
	if(strpos($wepsk,'d')!==false) $d_flag=true;
	if(strlen($wepsk)>0){
		$sk=$wepsk;
		if($d_flag) $sk=str_replace('d','',$sk);
		$sknum=strlen($sk);
		$reduce_dice=round($reduce_dice/$sknum);
	}
	$final_dice=$basic_dice+$add_dice-$reduce_dice;
	if($final_dice<30) $final_dice=30;
	elseif($final_dice>90) $final_dice=90;
	$log.="你小心翼翼地将火药和武器结合在了一起……<br>";
	if(rand(1,100)>$final_dice)
	{
		$log.="<span class=\"yellow\">“ssss……”</span><br>突然，你听见了什么被引燃了的声音。<br><span class=\"clan\">“卧槽…… 难道……”</span><br>还没反应过来，武器就在你手中爆炸了。<br>";
		global $hp; 
		$damage=rand(1,$wepe);
		$log.="你的{$wep}爆炸了！<br>";
		$log.="你受到了<span class=\"red\">{$damage}</span>点伤害！<br>";
		$wep='拳头'; $wepk='WN'; $weps='∞'; $wepe=0; $wepsk='';	
		if ($hp<=0)
		{
			$log.='<span class="red">你被杀死了！</span><br>';
			$hp=0;
			include_once GAME_ROOT . './include/state.func.php';
			death ('failtrapcvt','','',$tmp_itm);
		}
		return;
	}
	$log.="<span class=\"clan\">“呼……”</span><br>虽然不知道为什么装上火药就会爆炸了，但是你的{$wep}成功被改造成了爆炸物！<br><br>";
	$wepk='WD';
	$log.="消耗了一个火药。<br>";
	${'itms'.$i}--;
	if (${'itms'.$i}<=0)
	{
		$log.="火药用完了。<br>";
		${'itm'.$i}=''; ${'itmk'.$i}=''; ${'itms'.$i}=0; ${'itme'.$i}=0; ${'itmsk'.$i}='';
	}
}
function rcktcvt($t1,$t2)	//恐怖份子陷阱转换功能
{
	global $log,$mode,$club,$itemspkinfo,$name;
	if ($club!=21)
	{
		$log .= '你的称号不能使用该技能。<br>';
		$mode = 'command';
		return;
	}
	
	$t1=(int)$t1;
	if ($t1<1 || $t1>6)
	{
		$log.='你选择的道具不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	$t2=(int)$t2;
	if ($t2<1 || $t2>6)
	{
		$log.='你选择的道具不存在，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	global ${'itm'.$t1};
	if (${'itm'.$t1}!='驱云弹')
	{
		$log.='你选择的道具不是驱云弹，请重新选择。<br>';
		$mode = 'command';
		return;
	}
	
	global ${'itm'.$t2};
	if (${'itm'.$t2}=='毒药')
	{
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
		$itm0='瘴气弹'; $itmk0='EW'; $itme0=1; $itms0=1; $itmsk0='10';
	}
	else  if (${'itm'.$t2}=='『红石电路』')
	{
		global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
		$itm0='★核打击目标指示弹★'; $itmk0='Y'; $itme0=1; $itms0=1; $itmsk0='';
	}
	else
	{
		$log.='你选择的道具无法参与改装，请参阅帮助获得所有可行的改装列表。<br>';
		$mode = 'command';
		return;
	}
	
	global ${'itm'.$t1},${'itmk'.$t1},${'itme'.$t1},${'itms'.$t1},${'itmsk'.$t1};
	$itm=&${'itm'.$t1}; $itmk=&${'itmk'.$t1}; $itme=&${'itme'.$t1}; $itms=&${'itms'.$t1}; $itmsk=&${'itmsk'.$t1};
	
	$itms--; $tmp_itm=$itm;
	$log.="消耗了一枚{$itm}。<br>";
	if ($itms<=0)
	{
		$log.="{$itm}用完了。<br>";
		$itm=''; $itmk=''; $itms=0; $itme=0; $itmsk='';
	}
		
	global ${'itm'.$t2},${'itmk'.$t2},${'itme'.$t2},${'itms'.$t2},${'itmsk'.$t2};
	$itm=&${'itm'.$t2}; $itmk=&${'itmk'.$t2}; $itme=&${'itme'.$t2}; $itms=&${'itms'.$t2}; $itmsk=&${'itmsk'.$t2};
	
	$itms--; $tmp_itm=$itm;
	$log.="消耗了一枚{$itm}。<br>";
	if ($itms<=0)
	{
		$log.="{$itm}用完了。<br>";
		$itm=''; $itmk=''; $itms=0; $itme=0; $itmsk='';
	}
	
	$log.="改装成功，获得了物品<span class=\"yellow\">{$itm0}</span>。<br><br>";
	
	include_once GAME_ROOT.'./include/news.func.php';
	addnews ( 0, 'rcktcvt', $name, $itm0);
			
	include_once GAME_ROOT.'./include/game/itemmain.func.php';
	itemfind();
}


function shoplist($sn) {
	global $gamecfg,$mode,$itemdata,$areanum,$areaadd,$iteminfo,$itemspkinfo,$club;
	global $db,$tablepre;
	$arean = floor($areanum / $areaadd); 
	$result=$db->query("SELECT * FROM {$tablepre}shopitem WHERE kind = '$sn' AND area <= '$arean' AND num > '0' AND price > '0' ORDER BY sid");
	$shopnum = $db->num_rows($result);
	for($i=0;$i< $shopnum;$i++){
		$itemlist = $db->fetch_array($result);
		$itemdata[$i]['sid']=$itemlist['sid'];
		$itemdata[$i]['kind']=$itemlist['kind'];
		$itemdata[$i]['num']=$itemlist['num'];
		$itemdata[$i]['price']= $club == 11 ? round($itemlist['price']*0.75) : $itemlist['price'];
		$itemdata[$i]['area']=$itemlist['area'];
		$itemdata[$i]['item']=$itemlist['item'];
		$itemdata[$i]['itme']=$itemlist['itme'];
		$itemdata[$i]['itms']=$itemlist['itms'];
		//list($sid,$kind,$num,$price,$area,$item,$itmk,$itme,$itms,$itmsk)=explode(',',$itemlist);
		foreach($iteminfo as $info_key => $info_value){
			if(strpos($itemlist['itmk'],$info_key)===0){
				$itemdata[$i]['itmk_words'] = $info_value;
				break;
			}
		}
		$itemdata[$i]['itmsk_words'] = '';
		if($itemlist['itmsk'] && ! is_numeric($itemlist['itmsk'])){
			for ($j = 0; $j < strlen($itemlist['itmsk']); $j++) {
				$sub = substr($itemlist['itmsk'],$j,1);
				if(!empty($sub)){
					$itemdata[$i]['itmsk_words'] .= $itemspkinfo[$sub];
				}
			}
		}
		//$itemdata[$i] = array('sid' => $sid, 'kind' => $kind,'num' => $num, 'price' => $price, 'area' => $area, 'item' => $item,'itmk_words' => $itmk_words,'itme' => $itme, 'itms' => $itms,'itmsk_words' => $itmsk_words);
	}
	
	$mode = 'shop';

	return;

}

?>
