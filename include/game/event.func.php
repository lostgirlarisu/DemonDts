<?php
if(!defined('IN_GAME')) {
	exit('Access Denied');
}

function event(){
	global $mode,$log,$hp,$sp,$inf,$pls,$rage,$money;
	global $mhp,$msp,$wp,$wk,$wg,$wc,$wd,$wf;
	global $rp,$killnum,$state;

	$dice1 = rand(0,5);
	$dice2 = rand(20,40);//原为rand(5,10)
	if($pls == 0) { //分校
	} elseif($pls == 1) { //北海岸
		$log .= "一个好大的海浪突然打过来！<BR>";
		if($dice1 <= 3){
			$dice2 += 10;
			if($sp <= $dice2){
				$dice2 = $sp-1;
			}
			$sp-=$dice2;
			$log .= "被卷到了海中，好不容易爬了上来！<BR>体力减少 <font color=\"red\"><b>{$dice2}</b></font>  点。<BR>";
		}else{
			$log .= "呼...幸好没被卷进去...<BR>";
		}
	} elseif($pls == 2) { //北村住宅区
		$log = ($log . "突然，天空出现一大群乌鸦！<BR>");
		if($dice1 == 2){
			$log = ($log . "被乌鸦袭击，头部受了伤！<BR>");
			$inf = str_replace('h','',$inf);
			$inf = ($inf . 'h');
		}elseif($dice1 == 3){
			$log = ($log . "被乌鸦袭击，受到<font color=\"red\"><b>{$dice2}</b></font> 点伤害！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼，总算击退了。<BR>");
		}
	} elseif($pls == 3) { //北村公所
	} elseif($pls == 4) { //邮电局
	} elseif($pls == 5) { //消防署
	} elseif($pls == 6) { //观音堂
	} elseif($pls == 7) { //清水池
		$log = ($log . "糟糕，脚下滑了一下！<BR>");
		if($dice1 <= 3){
			$dice2 += 10;
			if($sp <= $dice2){
				$dice2 = $sp-1;
			}
			$sp-=$dice2;
			$log = ($log . "你摔进了池里！<BR>从水池里爬出来<span class=\"red\">消耗了{$dice2}点体力</span>。<BR>");
		}else{
			$log = ($log . "万幸，你没跌进池中。<BR>");
		}
	} elseif($pls == 8) { //白诘草神社
	} elseif($pls == 9) { //墓地
	} elseif($pls == 10) { //山丘地带
		$log = ($log . "哇！悬崖崩坏倒塌！<BR>");
		if($dice1 == 2){
			$log = ($log . "已经尽量闪避，不过，还是被石头滑落打伤了脚！<BR>");
			$inf = str_replace('f','',$inf);
			$inf = ($inf . "f");
		}elseif($dice1 == 3){
			$log = ($log . "石头滑落，受到<font color=\"red\"><b>{$dice2}</b></font> 点伤害！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼...总算是避开了...<BR>");
		}
	} elseif($pls == 11) { //隧道
		$log = ($log . "哇！脚下发现有生锈的钉子！<BR>");
		if($dice1 == 2){
			$log = ($log . "不小心踩到了钉子上，脚受伤了！<BR>");
			$inf = str_replace('f','',$inf);
			$inf = ($inf . "f");
		}elseif($dice1 == 3){
			$log = ($log . "脚被钉子扎伤，受到<font color=\"red\"><b>{$dice2}</b></font> 点伤害！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼...总算是避开了...<BR>");
		}
	} elseif($pls == 12) { //西村住宅区
		$log = ($log . "突然，天空出现一大群乌鸦！<BR>");
		if($dice1 == 2){
			$log = ($log . "被乌鸦袭击，<span class=\"red\">头部受了伤</span>！<BR>");
			$inf = str_replace('h','',$inf);
			$inf = ($inf . 'h');
		}elseif($dice1 == 3){
			$log = ($log . "被乌鸦袭击，<span class=\"red\">受到{$dice2}点伤害</span>！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼，总算击退了。<BR>");
		}
	} elseif($pls == 13) { //废弃寺庙
	} elseif($pls == 14) { //废校
	} elseif($pls == 15) { //灵子研究中心
	} elseif($pls == 16) { //常磐森林
		$log = ($log . "一只野狗突然向你袭来！<BR>");
		if($dice1 == 2){
			$log = ($log . "手臂被咬伤了！<BR>");
			$inf = str_replace('a','',$inf);
			$inf = ($inf . "a");
		}elseif($dice1 == 3){
			$log = ($log . "被野狗袭击，受到<font color=\"red\"><b>{$dice2}</b></font> 点伤害！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼...总算击退了...<BR>");
		}
	} elseif($pls == 17) { //剑塚湖
		$log = ($log . "糟糕，脚下滑了一下！<BR>");
		if($dice1 <= 3){
			$dice2 += 10;
			if($sp <= $dice2){
				$dice2 = $sp-1;
			}
			$sp-=$dice2;
			if($dice1 == 1){
				$hp = round($hp/2);
				if($hp <= 0){$hp = 1;}
				$log = ($log . "掉进湖里了，挣扎着爬了上来！<BR>不但体力减少 <font color=\"red\"><b>{$dice2}</b></font> 点，<BR>还被湖底的锐器割伤了！<BR>");
			}else{
				$log = ($log . "掉进湖里了，不过，已努力爬了上来！<BR>体力减少 <font color=\"red\"><b>{$dice2}</b></font> 点。<BR>");
			}
		}else{
			$log = ($log . "呼...幸好没掉进湖里...<BR>");
		}
	} elseif($pls == 18) { //南村住宅区
		$log = ($log . "突然，天空出现一大群乌鸦！<BR>");
		if($dice1 == 2){
			$log = ($log . "被乌鸦袭击，<span class=\"red\">头部受了伤</span>！<BR>");
			$inf = str_replace('h','',$inf);
			$inf = ($inf . 'h');
		}elseif($dice1 == 3){
			$log = ($log . "被乌鸦袭击，<span class=\"red\">受到{$dice2}点伤害</span>！<BR>");
			$hp-=$dice2;
		}else{
			$log = ($log . "呼，总算击退了。<BR>");
		}

	} elseif($pls == 19) { //诊所
	} elseif($pls == 20) { //灯塔
	} elseif($pls == 21) { //南海岸
		$log .= "一个好大的海浪突然打过来！<BR>";
		if($dice1 <= 3){
			$dice2 += 10;
			if($sp <= $dice2){
				$dice2 = $sp-1;
			}
			$sp-=$dice2;
			$log .= "被卷到了海中，好不容易爬了上来！<BR>体力减少 <font color=\"red\"><b>{$dice2}</b></font>  点。<BR>";
		}else{
			$log .= "呼...幸好没被卷进去...<BR>";
		}
	} elseif($pls == 22) { //深渊之口
	} elseif($pls == 23) { //战术核潜艇
		$log = ($log . "糟糕，不小心触发了警报！<BR>");
		if($dice1 == 2){
			$log = ($log . "被潜伏的狙击手袭击，<span class=\"red\">头部受了伤</span>！<BR>");
			$inf = str_replace('h','',$inf);
			$inf = ($inf . 'h');
		}elseif($dice1 == 3){
			$hp = round($hp/2);
			if($hp <= 0){$hp = 1;}
			$log = ($log . "被潜伏的狙击手袭击，<span class=\"red\">生命减半了</span>！<BR>");
		}else{
			$sp-=$dice2;
			$log = ($log . "你一路狂奔，<BR>体力减少 <font color=\"red\"><b>{$dice2}</b></font>  点，迅速逃离了。<BR>");
		}
	} elseif($pls == 24) { //广播塔
	} elseif($pls == 25) { //凉亭
	}	else {
	}

	if($hp<=0 && $state < 10){
//		global $now,$alivenum,$deathnum,$name,$state;
//		$hp = 0;
//		$state = 13;
//		addnews($now,'death13',$name,0);
//		$alivenum--;
//		$deathnum++;
//		include_once GAME_ROOT.'./include/system.func.php';
//		save_gameinfo();
		include_once GAME_ROOT . './include/state.func.php';
		death('event');
	}
	return;
}


function death_kagari($type){
	/*迷之少女事件。详细触发参考原版的雏菊之丘地区
	global $log,$hp,$inf,$gamestate;
	if($type == 1){
		$log = ($log . "从少女的身上延伸出了红色的丝带，<BR>如巨蟒般将你紧紧地捆住。<BR>");
		if ($gamestate == 50 ){
			$log = ($log . "不过，在你即将被绞碎时，上空射来的奇异光束烧毁了丝带，救了你一命。<BR>少女见状扭头离去了。<br>");
			$inf = str_replace('b','',$inf);
			$inf .= 'b';
			$hp = round($hp/100);
			if($hp <= 0){$hp = 1;}
		}else{
			include_once GAME_ROOT . './include/state.func.php';
			death('kagari1');
			return;
		}	
	}elseif($type == 2){
		$log = ($log . "从少女的身上延伸出了红色的丝带，<BR>锋利的丝带朝着你的头部飞来！<BR>");
		if ($gamestate == 50 ){
			$log = ($log . "不过，在你即将身首异处时，上空射来的奇异光束烧毁了丝带，救了你一命。<BR>少女见状扭头离去了。<br>");
			$hp = round($hp/100);
			$inf = str_replace('h','',$inf);
			$inf .= 'h';
			if($hp <= 0){$hp = 1;}
		}else{
			include_once GAME_ROOT . './include/state.func.php';
			death('kagari2');
			return;
		}		
	}elseif($type == 3){
		$log = ($log . "从少女的身上延伸出了红色的丝带，<BR>灼热的丝带朝着你高速飞来！<BR>");
		if ($gamestate == 50 ){
			$log = ($log . "不过，在喷射着岩浆的丝带即将把你融化时，上空射来的奇异光束烧毁了丝带，救了你一命。<BR>少女见状扭头离去了。<br>");
			$hp = round($hp/100);
			$inf = str_replace('u','',$inf);
			$inf .= 'u';
			if($hp <= 0){$hp = 1;}
		}else{
			include_once GAME_ROOT . './include/state.func.php';
			death('kagari3');
			return;
		}	
	}else{
		return;
	}
	*/
}
function event_rp_up($rpup){
	global $rp,$club,$skills;
	if($club != 28 || $rpup <= 0){
		$rp += $rpup;
	}else{
		include_once GAME_ROOT.'./include/game/clubskills.func.php';
		$rpdec = 30;
		$rpdec += get_clubskill_rp_dec($club,$skills);
		$rp += round($rpup*(100-$rpdec)/100);
	}
	return;
}
?>
