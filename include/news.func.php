<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}


function  nparse_news($start = 0, $range = 0  ){//$type = '') {
	global $week,$nowep,$db,$tablepre,$lwinfo,$plsinfo,$wthinfo,$typeinfo,$exdmginf,$newslimit;
	//$file = $file ? $file : $newsfile;	
	//$ninfo = openfile($file);
	$range = $range == 0 ? $newslimit : $range ;
	$result = $db->query("SELECT * FROM {$tablepre}newsinfo ORDER BY nid DESC LIMIT $start,$range");
	//$r = sizeof($ninfo) - 1;
//	$rnum=$db->num_rows($result);
//	if($range && ($range <= $rnum)) {
//		$nnum = $range;
//	} else{
//		$nnum = $rnum;
//	}
	$newsinfo = '';
	$nday = 0;
	//for($i = $start;$i <= $r;$i++) {
	//for($i = 0;$i < $nnum;$i++) {
	while($news0=$db->fetch_array($result)) {
		//$news0=$db->fetch_array($result);
		$time=$news0['time'];$news=$news0['news'];$a=$news0['a'];$b=$news0['b'];$c=$news0['c'];$d=$news0['d'];$e=$news0['e'];
		list($sec,$min,$hour,$day,$month,$year,$wday) = explode(',',date("s,i,H,j,n,Y,w",$time));
		//if($day != $nday) {
		//	$newsinfo .= "<span class=\"evergreen\"><B>{$month}月{$day}日(星期$week[$wday])</B></span><br>";
		//	$nday = $day;
		//}
		//$sec='??';
		if($news == 'newgame') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">第{$a}回深渊BR大逃杀开始了</span><br>\n";
		} elseif($news == 'solostart') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">本局游戏为SOLO局，对决者为<span class=\"lime\">{$a}</span>与<span class=\"lime\">{$b}</span>！</span><br>\n";
		} elseif($news == 'teamfightstart') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">本局游戏为组队团战局！</span>";
			if ($a) $newsinfo .= "<span class=\"lime\">特殊设置：强制随机组队。</span>";
			$newsinfo .= "<br>\n";
		} elseif($news == 'gameover') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">第{$a}回深渊BR大逃杀结束了</span><br>\n";
		} elseif($news == 'newpc') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}({$b})进入了大逃杀战场</span><br>\n";
		} elseif($news == 'newgm') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">管理员-{$a}({$b})华丽地乱入了战场</span><br>\n";
		} elseif($news == 'teammake') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$b}创建了队伍{$a}</span><br>\n";
		} elseif($news == 'teamjoin') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$b}加入了队伍{$a}</span><br>\n";
		} elseif($news == 'teamquit') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$b}退出了队伍{$a}</span><br>\n";
		} elseif($news == 'senditem') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}将<span class=\"yellow\">$c</span>赠送给了{$b}</span><br>\n";
		} elseif($news == 'addarea') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，增加禁区：";
			$alist = explode('_',$a);
			foreach($alist as $ar) {
				$newsinfo .= "$plsinfo[$ar] ";
			}
			$newsinfo .= "<span class=\"yellow\">【天气：{$wthinfo[$b]}】</span><br>\n";
		} elseif($news == 'hack') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}启动了hack程序，全部禁区解除！</span><br>\n";
		} elseif($news == 'hack2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}启动了救济程序，全部禁区解除！</span><br>\n";
		} elseif($news == 'combo') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">游戏进入连斗阶段！</span><br>\n";
		} elseif($news == 'solo_combo1') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">参与SOLO对决的两人均已进入游戏，游戏进入连斗阶段！</span><br>\n";
		} elseif($news == 'solo_combo2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">SOLO局已开始3分钟，游戏自动进入连斗阶段！</span><br>\n";
		} elseif($news == 'comboupdate') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">连斗判断死亡数修正为{$a}人，当前死亡数为{$b}人！</span><br>\n";
		} elseif($news == 'duel') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">游戏进入死斗阶段！</span><br>\n";
		} elseif($news == 'end0') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">游戏出现故障，意外结束</span><br>\n";
		} elseif($news == 'end1') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">参与者全部死亡！</span><br>\n";
		} elseif($news == 'end2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">优胜者——{$a}！</span><br>\n";
		} elseif($news == 'end3') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}解除了精神锁定，游戏紧急中止</span><br>\n";
		} elseif($news == 'end4') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">无人参加，游戏自动结束</span><br>\n";
		} elseif($news == 'end5') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}引爆了核弹，毁坏了整个岛屿</span><br>\n";
		} elseif($news == 'end6') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">本局游戏被GM中止</span><br>\n";
		} elseif($news == 'end9') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}击倒了年兽使黄鸡病毒得以扩散，游戏紧急中止</span><br>\n";
		} elseif($news == 'end11') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}重启了九系统，游戏本身的存在消匿在时空尽头了。</span><br>\n";
		} elseif($news == 'revival') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}因为及时按了BOMB键而原地满血复活了！</span><br>\n";	
		} elseif($news == 'getreset') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}领悟了伟大秘仪，掌握了神秘粒子的复位方法！</span><br>\n";		
		} elseif($news == 'teach') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>和<span class=\"yellow\">$b</span>谈笑风生后成为了<span class=\"yellow\">$b</span>的粉丝！";
		}elseif(strpos($news,'death') === 0) {
			if($news == 'death11') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因滞留在<span class=\"red\">禁区【{$plsinfo[$c]}】</span>死亡";
			} elseif($news == 'death12') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">毒发</span>死亡";
			} elseif($news == 'death13') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">意外事故</span>死亡";
			} elseif($news == 'death14') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">入侵禁区系统失败</span>死亡";
			} elseif($news == 'death15') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"red\">？？？强行消除</span>";
			} elseif($news == 'death16') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"red\">由理直接拉入SSS团</span>";
			} elseif($news == 'death17') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"red\">冰雹砸死</span>";
			} elseif($news == 'death18') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">烧伤发作</span>死亡";
			} elseif($news == 'death19') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">失血过多</span>死亡";
			}elseif($news == 'death20') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>使用<span class=\"red\">$d</span>击飞";
			} elseif($news == 'death21') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>使用<span class=\"red\">$d</span>殴打致死";
			} elseif($news == 'death22') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>使用<span class=\"red\">$d</span>斩杀";
			} elseif($news == 'death23') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>使用<span class=\"red\">$d</span>射杀";
			} elseif($news == 'death24') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>投掷<span class=\"red\">$d</span>致死";
			} elseif($news == 'death25') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>埋设<span class=\"red\">$d</span>伏击炸死";
			} elseif($news == 'death29') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>发动<span class=\"red\">$d</span>以灵力杀死";
			} elseif($news == 'death26') {
				if($c) {
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因食用了<span class=\"yellow\">$c</span>下毒的<span class=\"red\">$d</span>被毒死";
				} else {
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因食用了有毒的<span class=\"red\">$d</span>被毒死";
				}
			} elseif($news == 'death27') {
				if(($c)&&($c!=' ')){
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因触发了<span class=\"yellow\">$c</span>设置的陷阱<span class=\"red\">$d</span>被杀死";
				} else {
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因触发了陷阱<span class=\"red\">$d</span>被杀死";
				}
			} elseif($news == 'death28') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"yellow\">$d</span>意外身亡";
			} elseif($news == 'death30') {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因误触伪装成核弹按钮的蛋疼机关被炸死";
			} elseif($news == 'death31'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因L5发作自己挠破喉咙身亡！";
			} elseif($news == 'death32'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，躲藏于<span class=\"red\">$plsinfo[$c]</span>的<span class=\"yellow\">$a</span><span class=\"red\">挂机时间过长</span>，被在外等待的愤怒的玩家们私刑处死！";
			} elseif($news == 'death33'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因卷入特殊部队『天使』的实弹演习，被坠落的少女和机体“亲吻”而死";
			} elseif($news == 'death34'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因摄入过量突变药剂，身体组织崩解而死！";
			} elseif($news == 'death35'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因为敌意过剩，被虚拟意识救♀济！";
			} elseif($news == 'death36'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因为敌意过剩，被虚拟意识腰★斩！";
			} elseif($news == 'death37'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因为敌意过剩，被虚拟意识断★头！";
			} elseif($news == 'death38'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因为敌意过剩，被虚拟意识救♀济！";
			} elseif($news == 'death39'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>遭到手中的武器<span class=\"yellow\">$d</span>反噬而死！";
			} elseif($news == 'death40'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因为祈求神德，被御柱砸死。";
			} elseif($news == 'death41'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>遭到队友手中的武器<span class=\"yellow\">$d</span>反噬而死！";
			} elseif($news == 'death42'){
				if ($c=='/self')
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>拉响了绑在身上的炸药包自爆身亡！";
				else  $newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">$c</span>的自杀式爆炸袭击炸死了！";
			} elseif($news == 'death44'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因在试图把陷阱<span class=\"yellow\">{$d}</span>转换成武器过程中，操作失误引爆了陷阱而死亡！";
			} elseif($news == 'death45'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>在核弹袭击中丧生了！";
			} elseif($news == 'death46'){
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>在鱼弹袭击中被一发入魂，可喜可贺，可喜可贺！";
			} elseif($news == 'death47') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}</span>遭到了诅咒的反噬，灵魂也消散殆尽。";
			} else {
				$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>因<span class=\"red\">不明原因</span>死亡（技术信息：死因为{$news}）";
			}
			$dname = $typeinfo[$b].' '.$a;
//			if($b == 0) {
//				//$dname = $a;
//				$lwresult = $db->query("SELECT lastword FROM {$tablepre}users WHERE username = '$a'");
//				$lastword = $db->result($lwresult, 0);
//			} else {
//				//$dname = $typeinfo[$b].' '.$a;
//				$lastword = is_array($lwinfo[$b]) ? $lwinfo[$b][$a] : $lwinfo[$b];
//			}
			if(!$e){
				$newsinfo .= "<span class=\"yellow\">【{$dname} 什么都没说就死去了】</span><br>\n";
			}elseif (substr($e,0,1)=='/') {
				$newsinfo .= substr($e,1,strlen($e)-1)."<br>\n";
			}else {
				$newsinfo .= "<span class=\"yellow\">【{$dname}：“{$e}”】</span><br>\n";
			}
		} elseif($news == 'kamikaze_sv') {
			$name1 = $typeinfo[$b].' '.$a;
			$name2 = $typeinfo[$d].' '.$c;
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$name1}</span>对<span class=\"yellow\">{$name2}</span>发动了自杀式爆炸袭击，但<span class=\"yellow\">{$name1}</span>因为对主的虔诚而奇迹般地活了下来！<br>\n";
		} elseif($news == 'poison') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"purple\">{$a}食用了{$b}下毒的{$c}</span><br>\n";
		} elseif($news == 'trap') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}中了{$b}设置的陷阱{$c}，受到了{$d}点伤害！</span><br>\n";
		} elseif($news == 'trapmiss') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}回避了{$b}设置的陷阱{$c}</span><br>\n";
		} elseif($news == 'trapdef') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}依靠迎击装备抵御了{$b}设置的陷阱{$c}的伤害</span><br>\n";
		} elseif($news == 'trapdef1') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}依靠强壮的体魄抵御了{$b}设置的陷阱{$c}的伤害</span><br>\n";
		}elseif($news == 'duelkey') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}使用了{$b}，启动了死斗程序！</span><br>\n";
		} elseif($news == 'corpseclear') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了凸眼鱼，{$b}具尸体被吸走了！</span><br>\n";
		} elseif($news == 'wthchange') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$c}，天气变成了{$wthinfo[$b]}！</span><br>\n";
		} elseif($news == 'wthfail') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$c}，但是恶劣的天气并未发生改变！</span><br>\n";
		} elseif($news == 'syswthchg') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">奇迹和魔法都是存在的！当前天气变成了{$wthinfo[$a]}！</span><br>\n";
		} elseif($news == 'sysaddarea') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">奇迹和魔法都是存在的！禁区提前增加了！</span><br>\n";
		} elseif($news == 'syshackchg') {
			if($a){$hackword = '全部禁区都被解除了';$class = 'lime';}
			else{$hackword = '禁区恢复了未解除状态';$class = 'yellow';}
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"{$class}\">奇迹和魔法都是存在的！{$hackword}！</span><br>\n";
		} elseif($news == 'sysgschg') {
			if($a == 20){
				$chgword = '当前游戏立即开始了！';
				$class = 'lime';
			}	elseif($a == 30){
				$chgword = '当前游戏停止激活！';
				$class = 'yellow';
			}	elseif($a == 40){
				$chgword = '当前游戏进入连斗阶段！';
				$class = 'red';
			}	elseif($a == 50){
				$chgword = '当前游戏进入死斗阶段！';
				$class = 'red';
			}	else{
				$chgword = '异常语句，请联系管理员！';
				$class = 'red';
			}
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"{$class}\">奇迹和魔法都是存在的！{$chgword}</span><br>\n";
		} elseif($news == 'newwep') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$b}，改造了<span class=\"yellow\">$c</span>！</span><br>\n";
		} elseif($news == 'newwep2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$b}，强化了<span class=\"yellow\">$c</span>！</span><br>\n";
		}elseif($news == 'evowep') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}的武器<span class=\"yellow\">$b</span>进化成了<span class=\"yellow\">$c</span>！</span><br>\n";
		} elseif($news == 'itemmix') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}合成了{$b}</span><br>\n";
		}elseif($news == 'syncmix') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}同调合成了{$b}</span><br>\n";
		}elseif($news == 'overmix') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}超量合成了{$b}</span><br>\n";
		}elseif($news == 'mixfail') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}合成游戏王卡牌失败，素材全部消失！真是大快人心啊！</span><br>\n";
		}elseif($news == 'gemming') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用{$b}为{$c}添加了{$d}属性！</span><br>\n";
		}elseif($news == 'trapcvt') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}成功将陷阱{$b}改装成了爆系武器！</span><br>\n";
		}elseif($news == 'nuclatt') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}使用了{$b}，在{$c}召来了一枚核弹袭击！</span><br>\n";
		}elseif($news == 'becomeidol') {//[u151029]偶像宣言
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}为了把大家从这个大逃杀游戏中拯救出来，决定成为偶像！！</span><br>\n";
		}elseif($news == 'songattack') {//[u151029]绝唱
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}奏唱了撕裂灵魂之歌，肆虐了{$b}整个地区！</span><br>\n";
		}elseif($news == 'rcktcvt') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}成功把一枚驱云弹改装成了{$b}！</span><br>\n";
		}elseif($news == 'npcmove') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，{$a}使<span class=\"yellow\">{$b}</span>的位置移动了！<br>\n";
		}elseif($news == 'song') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}</span>在<span class=\"yellow\">{$b}</span>歌唱了<span class=\"red\">{$c}</span>。<br>\n";
		}  elseif($news == 'itembuy') {
			//$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}购买了{$b}</span><br>\n";
			$newsinfo .= "\n";
		} elseif($news == 'damage') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"clan\">$a</span><br>\n";
		} elseif($news == 'alive') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>被<span class=\"yellow\">政府紧急复活</span><br>\n";
		} elseif($news == 'delcp') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}的尸体被政府销毁了</span><br>\n";
		} elseif($news == 'editpc') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}遭到了政府的生化改造！</span><br>\n";
		} elseif($news == 'suisidefail') {
			$newsinfo .= "<li><font style=\"background:url(http://dts.acfun.tv/img/backround4.gif) repeat-x\">{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}注射了H173，却由于RP太高进入了发狂状态！！</font></span><br>\n";
		} elseif($news == 'inf') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"red\">{$a}的攻击致使{$b}</span>{$exdmginf[$c]}<span class=\"red\">了</span><br>\n";
		} elseif($news == 'addnpc') {
			if($a=='夜种 夜种'){
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"grey\">痛苦的尖啸声回荡在空间之中……一个被诅咒的灵魂正在不属于他的躯体中煎熬着……</span><br>\n";
			}else{
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}乱入战场！</span><br>\n";
			}
		} elseif($news == 'addnpcs') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$b}名{$a}加入战斗！</span><br>\n";
		} elseif($news == 'secphase') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了挑战者之证，让3名幻影执行官加入了战场！打倒他们去获得ID卡来解除游戏吧！</span><br>\n";
		} elseif($news == 'ghost9') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了可疑的发信器，使得什么可疑人物加入了战场！</span><br>\n";
		} elseif($news == 'wikigirl') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了书库联结装置，使得全面出力的书库娘具现到了灵子研究中心！</span><br>\n";
		} elseif($news == 'testnpc') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}呼唤了一名还处于开发测试阶段NPC！大家可以尝试去挑战！</span><br>\n";
		} elseif($news == 'demon') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}呼唤了玩家脑子一热自荐的为追求个性却完全违背游戏平衡的文案所设计的NPC深渊灾厄！</span><br>\n";
		} elseif($news == 'demon2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了恶魔晶状体，使得什么可怕存在加入了战场！</span><br>\n";
		} elseif($news == 'heromode1') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}手贱导致幻境的除错机制被启动了！</span><br>\n";
		} elseif($news == 'heromode2') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了幻境配置终端，但被系统阻止了！除错机制再次启动！</span><br>\n";
		} elseif($news == 'heromode3') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}导致幻境中出现了错误的代码！幻境运营者开始输入外部数据来尝试除错！</span><br>\n";
		} elseif($news == 'heromode4') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">幻境运营者开始改写原始数据来应对{$a}的破坏行为！</span><br>\n";
		}elseif($news == 'thiphase') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}触发了对虚拟现实的救济！虚拟意识已经在■■■■活性化！</span><br>\n";
		} elseif($news == 'dfphase') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了黑色碎片，让1名未知存在加入了战场！打倒她去获得ID卡来解除游戏吧！</span><br>\n";
		} elseif($news == 'dfsecphase') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}闯了大祸，打破了Dark Force的封印！</span><br>\n";
		} elseif($news == 'eyu') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}碰巧打开了潘多拉的午餐盒，里面的恶鱼冲了出来！</span><br>\n";
		} elseif($news == 'monsternian') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$b}被{$c}驱赶到了<span class=\"yellow\">{$a}</span>！</span><br>\n";
		} elseif($news == 'evonpc') {
			if($a == 'Dark Force幼体'){
				$nword = "<span class=\"lime\">{$c}击杀了{$a}，却没料到这只是幻影……{$b}的封印已经被破坏了！</span>";
			}elseif($a == '小莱卡'){
				$nword = "<span class=\"lime\">{$c}击杀了{$a}，却发现这只是幻象……真正的{$b}受到惊动，方才加入战场！</span>";
			}else{
				$nword = "<span class=\"lime\">{$c}击杀了{$a}，却发现对方展现出了第二形态：{$b}！</span>";
			}
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，$nword<br>\n";
		} elseif($news == 'notworthit') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}做出了一个他自己可能会后悔很长一段时间的决定。</span><br>\n";
		} elseif($news == 'present') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}打开了{$b}，获得了{$c}！</span><br>\n";		
		} elseif($news == 'mixgun') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用枪械组装仪器制造了一把{$b}【{$c}】！</span><br>\n";
		} elseif($news == 'therest') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}通过神秘粒子的力量修复了破损的躯体！但这样的行为不是无偿的……</span><br>\n";
		} elseif($news == 'makemoney') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}运用{$b}的力量得到了大量金钱！</span><br>\n";
		} elseif($news == 'theresthack') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}神秘粒子引发的时空扭曲致使禁区提前增加了！</span><br>\n";	
		} elseif($news == 'cty') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$c}，利用＜厄环＞的力量将{$b}设为了领域！</span><br>\n";
		} elseif($news == 'get_gem_magic') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}使用了{$b}，掌握了激活其宝石魔法的方法！</span><br>\n";
		} elseif($news == 'gem_magic') {
			if($b=='月长石'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}主动激活了{$b}魔法进行献祭！</span><br>\n";}
			elseif($b=='翠榴石'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}主动激活了{$b}魔法进行转换！</span><br>\n";}		
			else{$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}{$c}了{$b}魔法！</span><br>\n";}	
		} elseif($news == 'demantoidwep') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}激活了{$b}魔法，改变了武器的类别！</span><br>\n";
		} elseif($news == 'demantoidwth') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}激活了{$b}魔法，使天气变为了{$wthinfo[$c]}！</span><br>\n";
		} elseif($news == 'ms_sac') {
			$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}献祭了一定的{$b}，并得到了来自月光的回馈！</span><br>\n";
		} elseif($news == 'gem_wep_magic') {
			if($b=='＜上灵＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「灵吸」，破坏了对方的{$c}！</span><br>\n";}
			elseif($b=='＜船桨＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「制裁」，使对方的生命上限下降了{$c}点！</span><br>\n";}		
			elseif($b=='＜时刃＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「扭曲」，减少了受到的伤害{$c}</span><br>\n";}	
			elseif($b=='＜棘枪＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「即死」，贯穿了{$c}的死线！</span><br>\n";}	
			elseif($b=='＜厄环＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「领域」，并在其领域【{$c}】内依靠神秘粒子的力量复活了！</span><br>\n";}	
			elseif($b=='＜夜母＞'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}依靠{$b}发动了「诅咒」，并将{$c}的灵魂转化为了夜种！</span><br>\n";}	
		} elseif($news == 'rageskill') {
			$word='';
			if ($c=='threat') $word='威压';
			if ($c=='punch') $word='乱击';
			if ($c=='focus') $word='集中';
			if ($c=='hunt') $word='追猎';
			if ($c=='sting') $word='毒刺';
			if ($c=='assasinate') $word='暗杀';
			if ($c=='blame') $word='批判一番';
			if ($c=='absorb') $word='吞噬';
			if ($c=='ego') $word='本能';
			if ($c=='dominate') $word='主宰';
			if ($c=='analysis') $word='解构';
			if ($c=='boom') $word='高能';
			if ($c=='inplosion') $word='内爆';
			if ($c=='crit') $word='必杀';
			if ($c=='recharge') $word='充能';
			if ($c=='innerfire') $word='心火';
			if ($c=='net') $word='电网';
			if ($c=='suppress') $word='压制';
			if ($c=='aim') $word='瞄准';
			if ($c=='roar') $word='咆哮';
			if ($c=='eagleeye') $word='枭眼';
			if ($c=='enchant') $word='附魔';
			if ($c=='bash') $word='闷棍';
			if ($c=='ambush') $word='偷袭';
			if ($c=='storm') $word='烈风';
			if ($c=='steeldance') $word='舞钢';
			if ($c=='ragestrike') $word='怒刺';
			if ($c=='burst') $word='点射';
			if ($c=='slayer') $word='连射';
			if ($c=='entangle') $word='缠绕';
			if ($c=='fear') $word='恐惧';
			if ($c=='corrupt') $word='腐蚀';
			if ($c=='nightmare') $word='噩梦';
			if (strpos($c,'aurora') === 0) $word='天变';
			if ($c=='__dcloak') $word='破隐一击';
			if ($c=='__bstorm') $word='铸剑';
			if ($c=='__callin') $word='召唤佣兵';
			if ($c=='__lat') $word='祈求天命';
			if ($c=='__ultrasong') $word='绝唱';//[u151029]
			if ($c=='battlesong') $word='战歌';//[u150910]
			if ($c=='finalsong') $word='安魂';//[u150924]
			if ($word!='') 
				if ($b!='')
					$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"clan\">{$a}对{$b}发动了技能<span class=\"yellow\">「{$word}」</span></span><br>\n";
				else  $newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"clan\">{$a}发动了技能<span class=\"yellow\">「{$word}」</span></span><br>\n";
		}elseif($news == 'hijack'){
			if($c=='hijack'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"seagreen\">{$a}将{$b}劫持成了自己的<span class=\"orange\">人质</span>！</span><br>\n";}
			elseif($c=='onbodybomb'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"seagreen\">{$a}在自己的人质<span class=\"yellow\">{$b}</span>身上安装了<span class=\"orange\">人肉炸弹</span>！</span><br>\n";}
			elseif($c=='offbodybomb'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"seagreen\">{$a}卸下了人质<span class=\"yellow\">{$b}</span>身上的<span class=\"orange\">炸弹</span>！</span><br>\n";}
			elseif($c=='hmove'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"seagreen\">{$a}与自己的人质们转移到了<span class=\"yellow\">{$b}</span>！</span><br>\n";}
			elseif($c=='freehtg'){$newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"seagreen\">{$a}释放了人质<span class=\"yellow\">{$b}</span>，这个可怜人现在重获自由了！</span><br>\n";}
		} elseif($news == 'damagenew') {
			$p1=$a; $p2=$b; $d=(int)$c;
			/*
			if (($d >= 100) && ($d < 150)) {
				$words = "{$p1}对{$p2}施加了一定程度的伤害。（100-150）";
			} elseif (($d >= 150) && ($d < 200)) {
				$words = "{$p1}拿了什么神兵？{$p2}所受的损伤已经不可忽略了。（150-200）";
			} elseif (($d >= 200) && ($d < 250)) {
				$words = "{$p1}简直不是人！{$p2}只能狼狈招架。（200-250）";
			} elseif (($d >= 250) && ($d < 300)) {
				$words = "{$p1}发出会心一击！{$p2}瞬间损失了大量生命！（250-300）";
			} elseif (($d >= 300) && ($d < 400)) {
				$words = "{$p1}使出浑身解数奋力一击！{$p2}想必凶多吉少！（300-400）";
			} elseif (($d >= 400) && ($d < 500)) {
				$words = "{$p1}使出武器中内藏的力量！可怜的{$p2}已经承受不住凶残的攻击了！（400-500）";
			} elseif (($d >= 500) && ($d < 600)) {
				$words = "{$p1}眼色一变使出绝招！{$p2}无法抵挡，只能任人宰割！（500-600）";
			} elseif (($d >= 600) && ($d < 750)) {
				$words = "{$p1}手中的武器闪耀出七彩光芒！{$p2}的身躯几乎融化在光芒中！（600-750）";
			} elseif (($d >= 750) && ($d < 1000)) {
				$words = "{$p1}受到天神的加护，打出惊天动地的一击！{$p2}此刻已不成人形！（750-1000）";
			} elseif (($d >= 1000) && ($d < 5000)) {
				$words = "{$p1}燃烧自己的生命得到了不可思议的力量！{$p2}，你还活着吗？（1000-5000）";
			} elseif (($d >= 5000) && ($d < 10000)) {
				$words = "{$p1}超越自己的极限爆发出了震天动地的力量！受此神力摧残的{$p2}化作了一颗流星！（5000-10000）";
			} elseif (($d >= 10000) && ($d < 50000)) {
				$words = "{$p1}运转百万匹周天，吐气扬声，一道霸气的光束直逼{$p2}，后者的身躯瞬间被力量的洪流所吞没！（10000-50000）";
			} elseif (($d >= 50000) && ($d < 200000)) {
				$words = "{$p1}已然超越了人类的极限！【{$d}】点的伤害——疾风怒涛般的攻击令大地崩塌，而{$p2}几乎化为齑粉！";
			}	elseif (($d >= 200000) && ($d < 500000)) {
				$words = "鬼哭神嚎！风暴既逝，{$p1}仍然屹立在战场上，而受到了【{$d}】点伤害的{$p2}想必已化为宇宙的尘埃了！";
			} elseif ( $d >= 500000) {
				$words = "残虐的攻击已经无法用言语形容！将{$p2}击飞出【{$d}】点伤害的英雄——{$p1}！让我们记住他的名字吧！";
			} else {
				$words = '';
			}
			*/
			if (($d >= 100) && ($d < 150)) {
				$words = "{$p1}对{$p2}做出了{$d}点的攻击，一定是有练过。";
			} elseif (($d >= 150) && ($d < 200)) {
				$words = "{$p1}拿了什么神兵？{$p2}被打了{$d}滴血。";
			} elseif (($d >= 200) && ($d < 250)) {
				$words = "{$p1}简直不是人！{$p2}瞬间被打了{$d}点伤害。";
			} elseif (($d >= 250) && ($d < 300)) {
				$words = "{$p1}发出会心一击！{$p2}损失了{$d}点生命！";
			} elseif (($d >= 300) && ($d < 400)) {
				$words = "{$p1}使出浑身解数奋力一击！{$d}点伤害！{$p2}还安好吗？";
			} elseif (($d >= 400) && ($d < 500)) {
				$words = "{$p1}使出武器中内藏的力量！可怜的{$p2}受到了{$d}点的伤害！";
			} elseif (($d >= 500) && ($d < 600)) {
				$words = "{$p1}眼色一变使出绝招！{$p2}招架不住，生命减少了{$d}点！";
			} elseif (($d >= 600) && ($d < 750)) {
				$words = "{$p1}手中的武器闪耀出七彩光芒！{$p2}招架不住，生命减少{$d}点！";
			} elseif (($d >= 750) && ($d < 1000)) {
				$words = "{$p1}受到天神的加护，打出惊天动地的一击！{$p2}被打掉{$d}点生命值！";
			} elseif (($d >= 1000) && ($d < 5000)) {
				$words = "{$p1}燃烧自己的生命得到了不可思议的力量！【{$d}】点的伤害值，没天理啊……{$p2}的HP足够么？";
			} elseif (($d >= 5000) && ($d < 10000)) {
				$words = "{$p1}超越自己的极限爆发出了震天动地的力量！在【{$d}】点的伤害后，{$p2}化作了一颗流星！";
			} elseif (($d >= 10000) && ($d < 50000)) {
				$words = "{$p1}运转百万匹周天，吐气扬声，一道霸气的光束过后，在【{$d}】点的伤害下，{$p2}还活着么？";
			} elseif (($d >= 50000) && ($d < 200000)) {
				$words = "{$p1}已然超越了人类的极限！【{$d}】点的伤害——疾风怒涛般的攻击令大地崩塌，而{$p2}几乎化为齑粉！";
			}	elseif (($d >= 200000) && ($d < 500000)) {
				$words = "鬼哭神嚎！风暴既逝，{$p1}仍然屹立在战场上，而受到了【{$d}】点伤害的{$p2}想必已化为宇宙的尘埃了！";
			} elseif ( $d >= 500000) {
				$words = "残虐的攻击已经无法用言语形容！将{$p2}击飞出【{$d}】点伤害的英雄——{$p1}！让我们记住他的名字吧！";
			} else {
				$words = '';
			}
			if ($words) $newsinfo .= "<li>{$hour}时{$min}分{$sec}秒，<span class=\"clan\">{$words}</span><br>\n";
		} else {
			$newsinfo .= "<li>$time,$news,$a,$b,$c,$d<br>\n";
		}
	}

	$newsinfo .= '';
	return $newsinfo;
		
}

?>
