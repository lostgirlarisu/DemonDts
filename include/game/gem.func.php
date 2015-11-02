<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}
function magic_gemwep($wep)
{
global $wep,$wepk,$wepe,$weps,$wepsk;
global $w_wep,$w_wepk,$w_wepe,$w_weps,$w_wepk,$w_wepsk;
global $w_arbsk,$w_arhsk,$w_arask,$w_arfsk,$w_artsk;
global $mhp,$w_mhp,$rage,$rp;
global $log,$name;
	if($wep=='＜上灵＞'){
		$lose_rand=rand(1,6);
		if($lose_rand==1){
			if($w_wepsk!==''){
			$w_wepsk='';$log.="<span class=\"gem\">「灵吸」被触发了，对方武器上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','武器');
			}
		}elseif($lose_rand==2){
			if($w_arbsk!==''){
			$w_arbsk='';$log.="<span class=\"gem\">「灵吸」被触发了，对方身体防具上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','身体防具');
			}
		}elseif($lose_rand==3){
			if($w_arhsk!==''){
			$w_arhsk='';$log.="<span class=\"gem\">「灵吸」被触发了，对方头部防具上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','头部防具');
			}
		}elseif($lose_rand==4){
			if($w_arask!==''){
			$w_arask='';$log.="<span class=\"gem\">「灵吸」被触发了，对方手部防具上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','手部防具');
			}
		}elseif($lose_rand==5){
			if($w_arfsk!==''){
			$w_arfsk='';$log.="<span class=\"gem\">「灵吸」被触发了，对方足部防具上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','足部防具');
			}
		}elseif($lose_rand==6){
			if($w_artsk!==''){
			$w_artsk='';$log.="<span class=\"gem\">「灵吸」被触发了，对方饰品上的属性被破坏了！</span><br>";
			addnews($now,'gem_wep_magic',$name,'＜上灵＞','饰品');
			}
		}
	}elseif($wep=='＜船桨＞'){
		$pbd_a_rand=rand(1,10);$pbd_b_rand=rand(1,10);
		if($rage<=1){$b_rage=1;}
		if($rp>1){
		$punish_bdamage=round(((($w_mhp-$m_hp)*0.1)*(1+($rage/100)))/$rp);
		}else{
		if($rp<=0){$rp=1;}
		$punish_bdamage=round(((($w_mhp-$m_hp)*0.1)*(1+($rage/100)))*$rp);
		}
		$punish_damage=round(($punish_bdamage*$pbd_a_rand)/$pbd_b_rand);
		$log .= "<span class=\"gem\">「制裁」被触发了，对方的生命上限下降了{$punish_damage}点！</span><br>";
		addnews($now,'gem_wep_magic',$name,'＜船桨＞',$punish_damage);
		return $punish_damage;
	}elseif($wep=='＜时刃＞'){
		global $areatime,$starttime,$areanum,$areahour,$now;
		$time_defend_per=$areanum*3;
		$areadvalue=(($areatime-$starttime)/1800)/($areanum/4);
		if($areadvalue<=1){
		include_once GAME_ROOT . './include/system.func.php';
		$areatime+=$areahour*2;
		movehtm();
		save_gameinfo();
		$log .="<span class='gem'>「扭曲」被触发了，你受到的伤害被减少了{$time_defend_per}%！<br>同时禁区时间被延长了！</span><br>";
		addnews($now,'gem_wep_magic',$name,'＜时刃＞','，并延长了下一次禁区时间到来的时间！');
		return $time_defend_per;
		}else{
		$log .="<span class='gem'>「扭曲」被触发了，你受到的伤害被减少了{$time_defend_per}%！</span><br>";
		addnews($now,'gem_wep_magic',$name,'＜时刃＞','！');
		return $time_defend_per;
		}
	}

}
function w_magic_gemwep($w_wep)
{
	global $w_wep,$log,$name;
	if($w_wep=='＜时刃＞'){
		global $areatime,$starttime,$areanum,$areahour,$now;
		global $w_name,$w_wep;
		$w_time_defend_per=$areanum*3;;
		$areadvalue=(($areatime-$starttime)/1800)/($areanum/4);
		if($areadvalue<=1){
		include_once GAME_ROOT . './include/system.func.php';
		$areatime+=$areahour*2;
		movehtm();
		save_gameinfo();
		$log .="<span class='gem'>「扭曲」被触发了，对方受到的伤害被减少了{$w_time_defend_per}%！<br>同时禁区时间被延长了！</span><br>";
		addnews($now,'gem_wep_magic',$w_name,'＜时刃＞','，并延长了下一次禁区时间到来的时间！');
		return $w_time_defend_per;
		}else{
		$log .="<span class='gem'>「扭曲」被触发了，你受到的伤害被减少了{$w_time_defend_per}%！<br>";
		addnews($now,'gem_wep_magic',$w_name,'＜时刃＞','！');
		return $w_time_defend_per;
		}
	}
}
function magic_gem($gemname)
{
	global $gemstate,$gemname,$gempower,$gemexp,$gemlvl;
	global $w_gemstate,$w_gemname,$w_gempower,$w_gemexp,$w_gemlvl;
	global $mhp,$hp,$sp,$msp,$rage,$weather,$wthinfo,$wep,$wepk,$wepsk,$money;
	global $log,$nick,$name,$inf,$club;
	if($gemname=='青金石'){
		if($gemlvl==0){$raisehp=round($mhp*0.1);$raisesp=round($msp*0.1);}
		elseif($gemlvl==1){$raisehp=round($mhp*0.15);$raisesp=round($msp*0.15);}
		elseif($gemlvl==2){$raisehp=round($mhp*0.2);$raisesp=round($msp*0.2);}
		elseif($gemlvl==3){$raisehp=round($mhp*0.3);$raisesp=round($msp*0.3);}		
			$losegem=round($raisehp+$raisesp);
			if($losegem<=$gempower){
				$log.="<span class='gem'>你身上的{$gemname}魔法使你的临时生命与体力增加了！</span><br>";
				if(($club==49)||($club==53)){
				$raisehp=round($raisehp*1.25);$raisesp=round($raisesp*1.25);
				$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
				}
				$hp+=$raisehp;$sp+=$raisesp;
				$gempower-=$losegem;
				if($gemlvl==3){
					$clear_inf_dice=rand(1,100);
						if(($inf!=='')&&($clear_inf_dice>=25)){
						$inf='';
						$log.="<span class='gem'>你身上的高阶{$gemname}魔法使你的负面状态被清除了！</span><br>";
						}
					}
				$log.="{$gemname}的gem被消耗了<span class='red'>{$losegem}</span>点。<br>";				
				$gemexp+=round($losegem/10);
			}else{
			$log .= "<span class='yellow'>你的gem储量无法支撑{$gemname}的消耗，请关闭激活或补充gem！</span><br>";
			}
	}elseif($gemname=='黑曜石'){
		if(($club==49)||($club==53)){
		if($gemlvl==0){return 5;}
		elseif($gemlvl==1){return 10;}
		elseif($gemlvl==2){return 15;}
		elseif($gemlvl==3){return 32;}	
		}else{
		if($gemlvl==0){return 4;}
		elseif($gemlvl==1){return 8;}
		elseif($gemlvl==2){return 12;}
		elseif($gemlvl==3){return 25;}	
		}	
	}elseif($gemname=='红宝石'){
		if(($club==49)||($club==53)){
		if($gemlvl==0){return 7;}
		elseif($gemlvl==1){return 13;}
		elseif($gemlvl==2){return 19;}
		elseif($gemlvl==3){return 25;}	
		}else{
		if($gemlvl==0){return 5;}
		elseif($gemlvl==1){return 10;}
		elseif($gemlvl==2){return 15;}
		elseif($gemlvl==3){return 20;}
		}
	}elseif($gemname=='猫眼石'){
		if($gemlvl==0){$raiserage=15;}
		elseif($gemlvl==1){$raiserage=25;}
		elseif($gemlvl==2){$raiserage=35;}
		elseif($gemlvl==3){$raiserage=50;}	
		if($rage<100){
			if($rage+$raiserage<=100){$losegem=round($raiserage*5);}
			elseif($rage+$raiserage>100){$losegem=round((100-$rage)*5);}
			if($gemlvl==3){$losegem=round($losegem/1.25);}
			if($losegem<=$gempower){
			if(($club==49)||($club==53)){
				$raiserage=round($raiserage*1.25);
				$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
				}
			$rage=min(100,$rage+$raiserage);
			$gempower-=$losegem;
			$gemexp+=round($losegem/10);
			$log.="<span class='yellow'>{$gemname}魔法使你的怒气增加了！</span><br>{$gemname}的gem被消耗了<span class='red'>{$losegem}</span>点。<br>";
			}else{
			$log.="<span class='yellow'>{$gemname}魔法使你的怒气增加了！</span><br>{$gemname}的gem被消耗了<span class='red'>{$gempower}</span>点。<br>";
			$rage=min(100,$rage+($gempower/10));$gemexp+=round($gempower/10);$gempower=0;
			}
		}	
	}elseif($gemname=='翠榴石'){
		if($gemlvl==0){$losegem=250;}
		elseif($gemlvl==1){$losegem=225;}
		elseif($gemlvl==2){$losegem=200;}
		elseif($gemlvl==3){$losegem=200;$free_dice=rand(1,100);}
		if($losegem<=$gempower){
		$log.="你激活了{$gemname}。<br>";
		addnews($now,'gem_magic',$name,$gemname);
		$log.="<span class='clan'>{$gemname}在你手中高速旋转着，化作一道绿光直指苍穹，片刻之后——</span><br>";
		$demantoid_dice=rand(0,30);
			if($demantoid_dice<=17){
				if($weather >= 14 && $weather <= 16){
					if($gempower<1000){
						$log .= "<span class=\"red\">{$gemname}被成功激活了！<br>但是{$gemname}的力量不足以改变极恶劣的天气！</span><br />";
						$gempower-=$losegem;
						return;
					}else{
						$weather=rand(0,13);$log.="<span class='yellow'>{$gemname}</span>耗尽了所有的gem使天气变成了<span class='red'>{$wthinfo[$weather]}</span>！<br>";
						include_once GAME_ROOT . './include/global.func.php';
						save_gameinfo ();
						addnews($now,'demantoidwth',$nick.' '.$name,$gemname,$weather);
						$gempower=0;
						return;
					}
				}else{
				$weather=rand(0,16);$log.="<span class='yellow'>{$gemname}</span>的力量使天气变成了<span class='red'>{$wthinfo[$weather]}</span>！<br>";
				include_once GAME_ROOT . './include/global.func.php';
				save_gameinfo ();
				addnews($now,'demantoidwth',$nick.' '.$name,$gemname,$weather);
				}
			}elseif($demantoid_dice<=20){
				if($wepk!=='WN'){$wepk='WP';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>殴系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=22){
				if($wepk!=='WN'){$wepk='WK';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>斩系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=24){
				if($wepk!=='WN'){$wepk='WG';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>射系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=26){
				if($wepk!=='WN'){$wepk='WC';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>投系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=27){
				if($wepk!=='WN'){$wepk='WD';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>爆系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=28){
				if($wepk!=='WN'){$wepk='WF';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>灵系武器</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}elseif($demantoid_dice<=29){
				if($wepk!=='WN'){$wepk='WJ';$log.="{$gemname}的力量使<span class='yellow'>{$wep}</span>变成了<span class='yellow'>重枪</span>！<br>";addnews($now,'demantoidwep',$nick.' '.$name,$gemname,$wepk);}
				else{$wep='生锈的拳头';$wepsk='p';$log.="{$gemname}的力量使你的拳头变成了涂满毒药的拳头【舔<br>";}
			}else{
				$money=$money*2;$log.="<span class='yellow'>{$gemname}的力量使你当前的金钱翻倍了！</span><br>";
			}
			if(($free_dice<=25)&&($gemlvl==3)){
			$log.="<span class='gem'>高阶{$gemname}魔法使本次激活没有消耗gem！</span><br>";
			$gempower=$gempower;
			}else{
			if(($club==49)||($club==53)){
				$losegem=round($losegem*0.75);
				$log.="<span class='red'>【研究】使宝石魔法的消耗降低了25%！</span><br>";
				}
			$log.="<span class='yellow'>{$gemname}的gem被消耗了<span class='red'>{$losegem}</span>点。</span><br>";
			$gempower-=$losegem;$gemexp+=round($losegem/10);
			}
		}else{
		$log.="<span class='yellow'>gem储量不足，无法激活{$gemname}！</span><br>";
		}
	}elseif($gemname=='碧榴石〖Alexander〗'){
		$log.="<span class='grey'>{$gemname}使你的气息与英雄同化了……</span><br>";
		if(($club==49)||($club==53)){
			$gempower-=94;
			$log.="<span class='red'>【研究】使宝石魔法的消耗降低了25%！</span><br>";
		}else{
		$gempower-=125;
		}
	}elseif($gemname=='淡蓝宝石〖Eltoner〗'){
		$log.="<span class='grey'>{$gemname}使你的气息与武神同化了……</span><br>";
		if(($club==49)||($club==53)){
			$gempower-=94;
			$log.="<span class='red'>【研究】使宝石魔法的消耗降低了25%！</span><br>";
		}else{
		$gempower-=125;
		}
	}
	
	if($gempower<=0){
	$gemstate=1;
	$log .= "<span class='yellow'>{$gemname}由于gem不足已失效，请补充gem！</span><br>";
	}
	if(($gemexp>=100)&&($gemlvl<3)){
	$gemlvl+=1;
	$gemexp=0;
	$log .= "<span class='lime'>{$gemname}升级了！</span><br>";
	}
	if($gemlvl>3){
	$gemexp=0;
	}
}
function w_magic_gem($w_gemname)
{
	global $gemstate,$gemname,$gempower,$emexp,$gemlvl;
	global $w_gemstate,$w_gemname,$w_gempower,$w_gemexp,$w_gemlvl;
	global $name;
	if($w_gemname=='黑曜石'){
		if(($w_club==49)||($w_club==53)){
		if($w_gemlvl==0){return 5;}
		elseif($w_gemlvl==1){return 10;}
		elseif($w_gemlvl==2){return 15;}
		elseif($w_gemlvl==3){return 32;}	
		}else{
		if($w_gemlvl==0){return 4;}
		elseif($w_gemlvl==1){return 8;}
		elseif($w_gemlvl==2){return 12;}
		elseif($w_gemlvl==3){return 25;}	
		}
	}elseif($w_gemname=='红宝石'){
		if(($w_club==49)||($w_club==53)){
		if($w_gemlvl==0){return 7;}
		elseif($w_gemlvl==1){return 13;}
		elseif($w_gemlvl==2){return 19;}
		elseif($w_gemlvl==3){return 25;}	
		}else{
		if($w_gemlvl==0){return 5;}
		elseif($w_gemlvl==1){return 10;}
		elseif($w_gemlvl==2){return 15;}
		elseif($w_gemlvl==3){return 20;}
		}
	}
	
	if($w_gempower<=0){$w_gemstate=1;}
	if($w_gempower>5000){$w_gempower=10000;}//这个卑鄙的判定是为了让NPC装备宝石时可以无限使用，如果有BUG请注释掉他，然后打死我……【
	if(($w_gemexp>=100)&&($w_gemlvl<3)){$w_gemlvl+=1;$w_gemexp=0;}
	if($w_gemlvl>3){$w_gemexp=0;}
}
function magic_gem_moonstone($gemname,$sac_type,$sac_num)
{
	global $gemstate,$gemname,$gempower,$gemexp,$gemlvl;
	global $mhp,$hp,$sp,$msp,$ss,$mss,$money,$club;
	global $log,$name;
	if($gemname=='月长石'){
		if($sac_num==''){
		$log.="献祭值不能为空。<br>";
		return;
		}
		if($club==17){
		$log.="走路萌物不能献祭！<br>";
		return;
		}
		if($gemlvl==0){$sac_limit=100;$sac_gift=0.5;}
		elseif($gemlvl==1){$sac_limit=150;$sac_gift=0.75;}
		elseif($gemlvl==2){$sac_limit=200;$sac_gift=1;}
		elseif($gemlvl==3){$sac_limit=300;$sac_gift=1.5;}	
		if($sac_num>$sac_limit){
		$log.="<span class='red'>献祭值超出了{$gemlvl}级{$gemname}的承载上限！请提升宝石等级或降低献祭量。</span><br>";
		return;
		}
			$gift_dice=rand(0,100);
			if($gemlvl==3){$double_dic=rand(1,10);}
		if($sac_type=='sac_mhp'){
			if($sac_num>=$mhp){
			$log.="<span class='red'>你的生命力不足以完成此次献祭。</span><br>";
			}else{
			addnews($now,'gem_magic',$name,$gemname);
			$log.="<span class='clan'>月长石从天空中接引下了一束清冷的月光，<br>月光照耀在你身上，令你感觉自己的血液正在沸腾。<br>你献祭了<span class='red'>{$sac_num}</span>点生命上限。</span><br>";
			$mhp-=$sac_num;if($hp>$mhp){$hp=$mhp;}
				if($gift_dice>=50){
				$add_money=round($sac_num*3*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_money=$add_money*2;}
					if(($club==49)||($club==53)){
					$add_money=round($add_money*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，从月光中凝结出了一些物品。<br>你走近一看，原来是<span class='yellow'>{$add_money}</span>元！<br>";
				$money+=$add_money;
				}else{
				$add_mss=round($sac_num*2*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_mss=$add_mss*2;}
					if(($club==49)||($club==53)){
					$add_mss=round($add_mss*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，月光向你的身体反馈了一些东西。<br>你仔细感觉后，发现多出了<span class='yellow'>{$add_mss}</span>点歌魂上限！<br>";
				$mss+=$add_mss;$ss+=$add_mss;
				}
				$gempower-=250;$gemexp+=25;
				$log.="{$gemname}的gem被消耗了<span class='red'>250</span>点。<br>";
				addnews($now,'ms_sac',$name,'生命上限');
			}
			
		}elseif($sac_type=='sac_msp'){
			if($sac_num>=$msp){
			$log.="<span class='red'>你的体力不足以完成此次献祭。</span><br>";
			}else{
			addnews($now,'gem_magic',$name,$gemname);
			$log.="<span class='clan'>月长石从天空中接引下了一束清冷的月光，<br>月光照耀在你身上，令你感觉自己的力量在流失。<br>你献祭了<span class='red'>{$sac_num}</span>点体力上限。</span><br>";
			$msp-=$sac_num;$sp=max(0,$sp-$sac_num);
				if($gift_dice>=25){
				$add_money=round($sac_num*2*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_money=$add_money*2;}
					if(($club==49)||($club==53)){
					$add_money=round($add_money*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，从月光中凝结出了一些物品。<br>你走近一看，原来是<span class='yellow'>{$add_money}</span>元！<br>";
				$money+=$add_money;
				}else{
				$add_mhp=round($sac_num*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_mhp=$add_mhp*2;}
					if(($club==49)||($club==53)){
					$add_mhp=round($add_mhp*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，月光向你的身体反馈了一些东西。<br>你仔细感觉后，发现多出了<span class='yellow'>{$add_mhp}</span>点生命上限！<br>";
				$mhp+=$add_mhp;$hp+=$add_mhp;
				}
				$gempower-=250;$gemexp+=25;
				$log.="{$gemname}的gem被消耗了<span class='red'>250</span>点。<br>";
				addnews($now,'ms_sac',$name,'体力上限');
			}
		}elseif($sac_type=='sac_mss'){
			if($sac_num>$mss){
			$log.="<span class='red'>你的歌魂不足以完成此次献祭。</span><br>";
			}else{
			addnews($now,'gem_magic',$name,$gemname);
			$log.="<span class='clan'>月长石从天空中接引下了一束清冷的月光，<br>月光照耀在你身上，令你感觉自己好像失去了什么。<br>你献祭了<span class='red'>{$sac_num}</span>点歌魂上限。</span><br>";
			$mss-=$sac_num;$ss=max(0,$ss-$sac_num);
				if($gift_dice<=25){
				$add_money=round($sac_num*3*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_money=$add_money*2;}
					if(($club==49)||($club==53)){
					$add_money=round($add_money*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，从月光中凝结出了一些物品。<br>你走近一看，原来是<span class='yellow'>{$add_money}</span>元！<br>";
				$money+=$add_money;
				}else{
				$add_msp=round($sac_num*1.5*$sac_gift);
				if(($gemlvl==3)&&($double_dice>=8)){$add_mhp=$add_mhp*2;}
					if(($club==49)||($club==53)){
					$add_msp=round($add_msp*1.25);
					$log.="<span class='red'>【研究】使宝石魔法的效果提高了25%！</span><br>";
					}
				$log.="片刻之后，月光向你的身体反馈了一些东西。<br>你仔细感觉后，发现多出了<span class='yellow'>{$add_msp}</span>点体力上限！<br>";
				$msp+=$add_msp;$sp+=$add_msp;
				}
				$gempower-=250;$gemexp+=25;
				$log.="{$gemname}的gem被消耗了<span class='red'>250</span>点。<br>";
				addnews($now,'ms_sac',$name,'歌魂上限');
			}
		}else{
			$log.="<span class='red'>你的献祭对象有误。</span><br>";
		}
	}
	if(($gemexp>=100)&&($gemlvl<3)){
	$gemlvl+=1;
	$gemexp=0;
	$log .= "<span class='lime'>{$gemname}升级了！</span><br>";
	}
	if($gemlvl>3){
	$gemexp=0;
	}
}
function feed_magic_gem($blocks)
{
	global $gemstate,$gemname,$gempower,$gemexp,$gemlvl;
	global $log,$name,$club;
	
	if(($club!=49)&&($club!=53)&&($gempower==1000)){
	$log.="<span class='yellow'>gem值已满，无需充能。</span><br>";
	return;
	}
	
	if((($club==49)||($club==53))&&($gempower==3000)){
	$log.="<span class='red'>gem值已满，无需充能。</span><br>";
	return;
	}
	
	if(($gemstate!=0)){
	if(($blocks=='白色方块')||($blocks=='黑色方块')||($blocks=='水晶方块')){
	$gem_value=50;
	}elseif(($blocks=='绿色方块')||($blocks=='黄色方块')){
	$gem_value=75;
	}elseif(($blocks=='红色方块')||($blocks=='蓝色方块')||($blocks=='银色方块')||($blocks=='金色方块')){
	$gem_value=100;
	}elseif(($blocks=='红宝石方块')||($blocks=='蓝宝石方块')||($blocks=='绿宝石方块')){
	$gem_value=150;
	}elseif($blocks=='悲叹之种'){
	$gem_value=250;
	}elseif(($blocks=='青金石')||($blocks=='红宝石')||($blocks=='黑曜石')||($blocks=='猫眼石')||($blocks=='月长石')||($blocks=='翠榴石')){
	$gem_value=500;
	}else{
	$log.="<span class='red'>该狗粮不存在！</span><br>";
	return;
	}	
	foreach(Array(1,2,3,4,5,6) as $imn){
	global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
	if(strpos(${'itm'.$imn},$blocks)===0 && ${'itme'.$imn} > 0 ){
		if((strpos(${'itmsk'.$imn},'X')!==false)||(strpos(${'itmsk'.$imn},'Y')!==false)||(strpos(${'itmsk'.$imn},'GEM')!==false)){
		${'itms'.$imn}--;
		}else{
		${'itms'.$imn}=0;
		}
		$log.="你吞噬了{$blocks}，gem值提升了<span class='yellow'>{$gem_value}</span>点！<br>";
		if(${'itms'.$imn}<=0){
		$log.="{$blocks}被吃光了！<br>";
		${'itms'.$imn} = ${'itme'.$imn} = 0;${'itm'.$imn} = ${'itmk'.$imn} = ${'itmsk'.$imn}='';
		}
		break;
		}		
	}
		if(($club==49)||($club==53)){
			$gempower=min(3000,$gempower+$gem_value);
		}else{
			$gempower=min(1000,$gempower+$gem_value);
		}
	}else{
	$log.="<span class='yellow'>你没有绑定魔法宝石，无法吞噬方块！</span><br>";
	}
}
function magic_find_item($iname)
{
	global $club,$gempower,$lvl,$log;
	global $db,$tablepre,$command,$mode,$plsinfo;
	
	if(($club!=49)&&($club!=53)&&($lvl<15)){
	$log.="<span class='red'>你无法使用【洞察】技能！</span><br>";
	return;
	}	
	if($gempower<500){
	$log.="<span class='red'>GEM不足，无法使用技能！</span><br>";
	return;
	}	
	$result = $db->query("SELECT * FROM {$tablepre}mapitem WHERE itm = '$iname'");
	if(!$db->num_rows($result)) {
		$log .= "<span class='red'>地图上没有名为{$iname}的道具，你真的输对名字了吗？</span><br>";
	}else{
		$result = $db->query("SELECT pls FROM {$tablepre}mapitem WHERE itm='$iname'");
		$ipls = $db->result($result);
		$log .= "<span class='yellow'>{$iname}</span>的位置在<span class='yellow'>{$plsinfo[$ipls]}</span>！<br>";
		$gempower-=500;
		$log .= "你消耗了<span class='red'>500</span>点GEM！<br>";
	}
}
?>
