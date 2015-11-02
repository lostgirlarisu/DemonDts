<?php

define('CURSCRIPT', 'game');

require './include/common.inc.php';

if ($server_addr!=$cache_server_addr && !$is_cache_server)
{
        header("Location: {$server_addr}index.php");
        exit(); 
}

//$t_s=getmicrotime();
//require_once GAME_ROOT.'./include/JSON.php';
require GAME_ROOT.'./include/game.func.php';
require config('combatcfg',$gamecfg);

$pm_pagestartime=microtime();

$plock=fopen(GAME_ROOT.'./gamedata/process.lock','ab');	
flock($plock,LOCK_EX);

//判断是否进入游戏
if(!$cuser||!$cpass) { gexit($_ERROR['no_login'],__file__,__line__); } 

$result = $db->query("SELECT * FROM {$tablepre}players WHERE name = '$cuser' AND type = 0");

if(!$db->num_rows($result)) { header("Location: valid.php");exit(); }

$pdata = $db->fetch_array($result);

//判断是否密码错误
if($pdata['pass'] != $cpass) {
	$tr = $db->query("SELECT `password` FROM {$tablepre}users WHERE username='$cuser'");
	$tp = $db->fetch_array($tr);
	$password = $tp['password'];
	if($password == $cpass) {
		$db->query("UPDATE {$tablepre}players SET pass='$password' WHERE name='$cuser'");
	} else {
		gexit($_ERROR['wrong_pw'],__file__,__line__);
	}
}

//判断游戏状态和玩家状态，如果符合条件则忽略指令
if($gamestate == 0) {
	$gamedata['url'] = 'end.php';
	ob_clean();
	$jgamedata = compatible_json_encode($gamedata);
	echo $jgamedata;
	ob_end_flush();
	exit();
}

//初始化各变量
extract($pdata,EXTR_REFS);
$log = $cmd = $main = $bskill = $markpls = $markname = '';
$gamedata = array();
init_playerdata();

$result = $db->query("SELECT sktime FROM {$tablepre}users WHERE username = '$name'");
$sktime = $db->result($result, 0);
if (!$sktime) $sktime=0;

if ($club==25){
	$result = $db->query("SELECT pls FROM {$tablepre}players WHERE pid = '$sktime'");
	$mp = $db->result($result, 0);
	if (($mp>=0)&&($lvl>=11)&&($sktime>0)){
		$markpls=$plsinfo[$mp];
	}else{
		$markpls="暂无目标";
	}
}

if ($gametype==2 && !$teamPass && ($mode!="team" || ($command!="teamc" && $command!="teamautoc" && $command!="noteam")))
	exit();

if ($gametype==2 && !$teamPass && $mode=="team" && $command=="noteam")
{
	$teamPass="1"; 
	$gamedata['url'] = 'game.php';
	include_once GAME_ROOT.'./include/game/gametype.func.php';
	if (check_teamfight_groupattack_setting())
	{
		$mhp+=200; $hp+=200; $def+=200; $itms1 = 40; $itms2 = 40; 
		$skillpoint=5; $wp=40; $wk=40; $wg=40; $wc=40; $wd=40; $wf=40;     
	}
}

//读取玩家互动信息
$result = $db->query("SELECT lid,time,log FROM {$tablepre}log WHERE toid = '$pid' AND prcsd = 0 ORDER BY time,lid");
$llist = '';
while($logtemp = $db->fetch_array($result)){
	$log .= date("H:i:s",$logtemp['time']).'，'.$logtemp['log'].'<br />';
	$llist .= $logtemp['lid'].',';
}
if(!empty($llist)){
	$llist = '('.substr($llist,0,-1).')';
	$db->query("UPDATE {$tablepre}log SET prcsd=1 WHERE toid = '$pid' AND lid IN $llist");
}

if ($server_addr!=$cache_server_addr && $is_cache_server)	//在分高速低速服务器的情况下，禁区状况要自己处理
{
	$movehtm = GAME_ROOT.TPLDIR.'/move.htm';
	$movedata = '<option value="main">■ 移动 ■<br />';

	foreach($plsinfo as $key => $value) {
		if(array_search($key,$arealist) > $areanum || $hack){
		$movedata .= "<option value=\"$key\"><!--{if \$pls == $key}--><--现在位置--><!--{else}-->$value($xyinfo[$key])<!--{/if}--><br />";
		}
	} 
	
	$odata=readover($movehtm);
	if ($odata!=$movedata) writeover($movehtm,$movedata);
}

//var_dump($_POST);
if($hp > 0){
	//显示枪声信息
	if(($now <= $noisetime+$noiselimit)&&$noisemode&&($noiseid!=$pid)&&($noiseid2!=$pid)) {
		if(($now-$noisetime) < 60) {
			$noisesec = $now - $noisetime;
			$log .= "<span class=\"yellow b\">{$noisesec}秒前，{$plsinfo[$noisepls]}传来了{$noiseinfo[$noisemode]}。</span><br>";
		} else {
			$noisemin = floor(($now-$noisetime)/60);
			$log .= "<span class=\"yellow b\">{$noisemin}分钟前，{$plsinfo[$noisepls]}传来了{$noiseinfo[$noisemode]}。</span><br>";
		}
	}
	
	if ($club==0 && !isset($clubavl))
	{
		include_once GAME_ROOT.'./include/game/clubslct.func.php';
		getclub($name,$c1,$c2,$c3);
		$clubavl[0]=0; $clubavl[1]=$c1; $clubavl[2]=$c2; $clubavl[3]=$c3;
	}

	//判断冷却时间是否过去
	if($coldtimeon){
		$cdover = $cdsec*1000 + $cdmsec + $cdtime;
		$nowmtime = floor(getmicrotime()*1000);
		$rmcdtime = $nowmtime >= $cdover ? 0 : $cdover - $nowmtime;
	}
	
	//判断背包内道具是否超限
	if(strpos($arbsk,'^')!==false && $arbs && $arbe){
		global $itmnumlimit;
		$itmnumlimit = $arbe>=$arbs ? $arbs : $arbe;
		include_once GAME_ROOT.'./include/game/itembag.func.php';
		overnumlimit();
	}
	
	if($coldtimeon && $rmcdtime > 0 && (strpos($command,'teammoveto')===0 || strpos($command,'move')===0 || strpos($command,'search')===0 || (strpos($command,'itm')===0)&&($command != 'itemget') || strpos($sp_cmd,'sp_weapon')===0 || strpos($command,'song')===0)){
		$log .= '<span class="yellow">冷却时间尚未结束！</span><br>';
		//$log .= '<span class="yellow">你的躁动使你损失了30点体力！</span><br>';
		//$sp=$sp-30;
		//if ($sp<1) $sp=1;
		$mode = 'command';
	}else{

		//进入指令判断
		if($mode !== 'combat' && $mode !== 'corpse' && strpos($action,'pacorpse')===false && $mode !== 'senditem' && ($mode !=='ctrlhostage')&& ($mode !=='ctrlhasi')){
			$action = '';
		}
		if($command == 'menu') {
			$mode = 'command';
			$action = '';
		} elseif($mode == 'command') {
			if($command == 'move') {
				//光环
				if ($now<=$auraa) { $hp=min($mhp,$hp+20); $sp=min($msp,$sp+20); $log.="<span class=\"lime\">光环使你回复了一定体力与生命值。</span><br>"; }
				//宝石
				global $gemstate,$gemname;
				if (($gemname=='青金石')&&($gemstate==2)){
				include_once GAME_ROOT.'./include/game/gem.func.php';
				magic_gem('青金石');
				}elseif(($gemname=='猫眼石')&&($gemstate==2)){
				include_once GAME_ROOT.'./include/game/gem.func.php';
				magic_gem('猫眼石');
				}
				include_once GAME_ROOT.'./include/game/search.func.php';
				move($moveto);
				if($coldtimeon){$cmdcdtime=$movecoldtime;}
			} elseif($command == 'search') {
				if ($verifycode!=$vcode) 
				{ 
					$log.="大逃杀不需要按键盘（为禁绝键盘辅助脚本，在聊天框以外按键盘会导致本次探索无效）。<br>"; //$mode='command'; 
					//光环
					if ($now<=$auraa) { $hp=min($mhp,$hp+20); $sp=min($msp,$sp+20); $log.="<span class=\"lime\">光环使你回复了一定体力与生命值。</span><br>"; }
					//宝石
					global $gemstate,$gemname;
					if (($gemname=='青金石')&&($gemstate==2)){
					include_once GAME_ROOT.'./include/game/gem.func.php';
					magic_gem('青金石');
					}elseif(($gemname=='猫眼石')&&($gemstate==2)){
					include_once GAME_ROOT.'./include/game/gem.func.php';
					magic_gem('猫眼石');
					}
					include_once GAME_ROOT.'./include/game/search.func.php';
					search();
					if($coldtimeon){$cmdcdtime=$searchcoldtime;}
				} 
				else
				{
					//光环
					if ($now<=$auraa) { $hp=min($mhp,$hp+20); $sp=min($msp,$sp+20); $log.="<span class=\"lime\">光环使你回复了一定体力与生命值。</span><br>"; }
					//宝石
					global $gemstate,$gemname;
					if (($gemname=='青金石')&&($gemstate==2)){
					include_once GAME_ROOT.'./include/game/gem.func.php';
					magic_gem('青金石');
					}elseif(($gemname=='猫眼石')&&($gemstate==2)){
					include_once GAME_ROOT.'./include/game/gem.func.php';
					magic_gem('猫眼石');
					}	
					include_once GAME_ROOT.'./include/game/search.func.php';
					search();
					if($coldtimeon){$cmdcdtime=$searchcoldtime;}
				}
			} elseif(strpos($command,'itm') === 0) {
				include_once GAME_ROOT.'./include/game/item.func.php';
				$item = substr($command,3);
				itemuse($item);
				if($coldtimeon){$cmdcdtime=$itemusecoldtime;}
			} elseif(strpos($command,'rest') === 0) {
				if($command=='rest3' && !in_array($pls,$hospitals)){
					$log .= '<span class="yellow">你所在的位置并非医院，不能静养！</span><br>';
				}else{
					$state = substr($command,4,1);
					$mode = 'rest';
				}
			} elseif($command == 'itemmain') {
				$mode = $itemcmd;
			}elseif($command == 'trace') {
				if (($club==25)&&($lvl>=19)){
					$mode = 'trace';
				}else{
					$log .= '<span class="yellow">你不能这么做！</span><br>';
				}
			} elseif($command == 'song') {
				$sname=trim(trim($art,'【'),'】');
				include_once GAME_ROOT.'./include/game/song.inc.php';
				//$log.=$sname;
				sing($sname);
			}elseif($command == 'special') {
				if($sp_cmd == 'sp_word'){
					include_once GAME_ROOT.'./include/game/special.func.php';
					getword();
					$mode = $sp_cmd;
				}elseif($sp_cmd == 'sp_adtsk'){
					include_once GAME_ROOT.'./include/game/special.func.php';
					adtsk();
					$mode = 'command';
				}elseif($sp_cmd == 'wep_evo'){
					global $wep;
					include_once GAME_ROOT.'./include/game/evowep.func.php';
					$res=check_evo($name,$wep);
					if ($res==-1) $res='你的武器无法进化！';
					$log.=$res.'<br>';
					//$log=$res.'<br><span class="yellow">注意，若更换武器，则累计性的进化进度将全部丢失！</span><br>';
					$mode = 'command';
				}elseif($sp_cmd == 'sp_trapadtsk'){
					$position = 0;
					if ($club==7)
					{	
						foreach(Array(1,2,3,4,5,6) as $imn)
							if(strpos(${'itmk'.$imn},'B')===0 && ${'itme'.$imn} > 0 ){
								$position = $imn;
								break;
							}
						if (!$position) 
						{
							$log .= '<span class="red">你没有电池，无法改造陷阱！</span><br />';
							$mode = 'command';
						}
					}
					else  if ($club==8)
					{
						foreach(Array(1,2,3,4,5,6) as $imn)
							if(${'itm'.$imn} == '毒药' && ${'itmk'.$imn} == 'Y' && ${'itme'.$imn} > 0 ){
								$position = $imn;
								break;
							}
						if (!$position) 
						{
							$log .= '<span class="red">你没有毒药，无法改造陷阱！</span><br />';
							$mode = 'command';
						}
					}
					else  
					{
						$log .= '<span class="red">你不懂得如何改造陷阱！</span><br />';
						$mode = 'command';
					}
					if ($position)
					{
						$position = 0;
						foreach(Array(1,2,3,4,5,6) as $imn)
							if(strpos(${'itmk'.$imn},'T')===0 && ${'itme'.$imn} > 0 ){
								$position = $imn;
								break;
							}
						if (!$position)
						{
							$log .= '<span class="red">你的背包中没有陷阱，无法改造！</span><br />';
							$mode = 'command';
						}
						else  $mode = 'sp_trapadtsk';
					}
				}elseif($sp_cmd == 'sp_trapadtskselected'){
					if (!isset($choice) || $choice=='menu')
					{
						$mode='command';
					}
					else
					{
						$choice=(int)$choice;
						if ($choice<1 || $choice>6)
							$log.='<span class="red">无此物品。</span><br />';
						else
						{
							include_once GAME_ROOT.'./include/game/special.func.php';
							trap_adtsk($choice);
						}
						$mode='command';
					}
				}elseif($sp_cmd == 'sp_pbomb'){
					$mode = 'sp_pbomb';
				}elseif($sp_cmd == 'sp_weapon'){
					include_once GAME_ROOT.'./include/game/special.func.php';
					weaponswap();
					$mode = 'command';
					if($coldtimeon){$cmdcdtime=$weaponswapcoldtime;}
				}elseif($sp_cmd == 'oneonone'){
					$mode='oneonone';
				}elseif($sp_cmd == 'sp_overdrive'){
					if (($lvl<19)||($sktime<=0)||($club!=7)){
						$log .= '<span class="red">你不能使用这个技能。</span><br />';
						$mode = 'command';
					}else{
						$addhp=round($mhp/100*$rage*1.5);
						$rage=0;
						if ($hp<$mhp) $hp=$mhp;
						$hp+=$addhp;
						$sktime--;
						$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
						$log.="<span class=\"lime\">你利用电流强化了自己的身体，你的生命值临时增加了{$addhp}点！</span><br>";
						$mode = 'command';
					}
				}elseif($sp_cmd == 'sp_ragnarok'){
					if (($lvl<19)||($sktime<=0)||($club!=23)){
						$log .= '<span class="red">你不能使用这个技能。</span><br />';
						$mode = 'command';
					}else{
						$wp=$wp*2;
						$sktime--;
						$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
						$log.="<span id=\"HsUipfcGhU\"></span>";
						$mode = 'command';
					}
				}elseif($sp_cmd == 'sp_hasi'){
					if ($club!=26){
						$log .= '<span class="red">你不能使用这个技能。</span><br />';
						$mode = 'command';
					}else{
						if ($sktime==1){
							$sktime=0;
							$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
							$log.="<span class=\"lime\">你现在将不会遇到自己的粉丝。</span><br>";
							$mode = 'command';
						}else{
							$sktime=1;
							$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
							$log.="<span class=\"lime\">你将继续遇到自己的粉丝。</span><br>";
							$mode = 'command';
						}
					}
				}elseif($sp_cmd == 'sp_bstorm'){
					if ((strpos($wepk,'WK')!==false)||(strpos($wepk,'WN')!==false)||($hp<=150)||($mhp<=150)||($lvl<3)||($sktime<=0)||($money<1500)||($club!=2)){
						$log .= '<span class="red">你不能使用这个技能。</span><br />';
						$mode = 'command';
					}elseif(strpos($wepsk,'T')!==false){
						$log .= '<span class="red">臣妾做不到啊！你告诉我这么一大坨枪怎么铸成剑？！</span><br />';
						$mode = 'command';
					}else{
						/*addnews ( $now, 'rageskill', $name, '', '__bstorm' );
						$result = $db->query("SELECT name,hp,pid FROM {$tablepre}players WHERE pls='$pls' AND hp>0 AND pid<>'$pid'");
						while($tdata = $db->fetch_array($result)) {
							$dmg=round(rand(15,40)/100*$wepe);
							if ($dmg>800) $dmg=800;
							if (($tdata['type']<20)||(!$tdata['type']>22))$tdata['hp']-=$dmg;
							if (!$tdata['type']) {$w_log = "<span class=\"yellow\">{$name}在{$plsinfo[$pls]}发动了剑刃风暴，你受到了{$dmg}点伤害！</span><br>";}
							logsave ( $tdata['pid'], $now, $w_log ,'b');
							if ($tdata['hp']<=0) {
								if (!$tdata['type']) {
									$w_log = "<span class=\"red\">你被斩杀了！</span><br>";
									logsave ( $tdata['pid'], $now, $w_log ,'b');
									}
								$tdata['hp']=0;
								include_once GAME_ROOT . './include/state.func.php';
								$killmsg = kill ( 'K', $tdata['name'], 0, $tdata['pid'], $wep );
							}	
							$db->array_update("{$tablepre}players",$tdata,"pid={$tdata['pid']}");
						}*/
						$log.='<span class="lime">你将你的武器改造成了斩系。</span><br />';
						$money=$money-1500;
						$hp=$hp-150;$mhp=$mhp-150;
						$wepk = 'WK';
						$sktime--;
						$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
						$mode = 'command';
					}
				}elseif($sp_cmd == 'sp_ultrasong'){
					global $ss,$mss;//绝唱
					if($mss<=0){
						$log .= '<span class="red">你不能使用这个技能。</span><br />';
						$mode = 'command';
					}else{
						global $db,$tablepre,$pid,$name,$pls,$now,$plsinfo;
						$log.= '你赋予了嗓音灰暗压抑的力量，并将此以可怕的歌谣抒发了出来。<br>
							天地为之变色，风云为之恸哭。<br>
							你嘶哑的歌声将成为此刻所有人的梦魇……<br>';
						
						include_once GAME_ROOT.'./include/news.func.php';
						addnews ( 0, 'songattack', $name, $plsinfo[$pls]);
				
						$result = $db->query("SELECT name,hp,tactic,pid FROM {$tablepre}players WHERE pls='$pls' AND type=0 AND hp>0 AND pid<>'$pid'");
						while($tdata = $db->fetch_array($result)) 
						{
							$dmg=$mss*50;
							//if ($tdata['tactic']==2) $dmg=ceil($dmg*0.9);
							$tdata['hp']-=$dmg;
							$w_log = "<span class=\"yellow\">{$name}在{$plsinfo[$pls]}奏唱了足以腐蚀灵魂的死亡之歌，你受到了{$dmg}点伤害！</span><br>";
							logsave ( $tdata['pid'], $now, $w_log ,'b');
							if ($tdata['hp']<=0) 
							{
								$w_log = "<span class=\"yellow\">你耳膜迸裂，七窍流血，奄奄一息，但奇迹般地存活下来了。</span><br>";
								logsave ( $tdata['pid'], $now, $w_log ,'b');
								$tdata['hp']=1;
								/*
								$w_log = "<span class=\"red\">你被核弹杀死了！</span><br>";
								logsave ( $tdata['pid'], $now, $w_log ,'b');
								$tdata['hp']=0;
								include_once GAME_ROOT . './include/state.func.php';
								$killmsg = kill ( 'nuclbomb', $tdata['name'], 0, $tdata['pid'], $name );
								*/
							}	
							$db->array_update("{$tablepre}players",$tdata,"pid={$tdata['pid']}");
						}
						$mss = $ss = 0;
					}
				}elseif($sp_cmd == 'sp_callin'){
					if (($lvl<15)||($sktime<=0)||($club!=11)||($money<1200)){
						$log .= '<span class="red">金钱不足，需要1200元才能发动这个技能。</span><br />';
						$mode = 'command';
					}else{
						addnews ( $now, 'rageskill', $name, '', '__callin' );
						$money=$money-1200;
						include_once GAME_ROOT . './include/system.func.php';
						$dd=rand(0,10);
						if (rand(0,99)%2==0) $dd=rand(0,5);
						addnpc(25,$dd,1,$name);
						$sktime--;
						$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
						$log.='<span class="lime">你消耗了1200元，召唤了一名佣兵前来协助你。</span><br />';
						$mode = 'command';
					}
				}elseif($sp_cmd == 'sp_callhere'){
				global $plsinfo,$pls;
					$cart=$name.'的契约书';
					$result = $db->query("SELECT * FROM {$tablepre}players WHERE art = '$cart' AND arts = '1'");
					if($db->num_rows($result)) {
						$log.="<span class='lime'>你将你的佣兵召唤到了{$plsinfo[$pls]}！</span><br />";
						$db->query("UPDATE {$tablepre}players SET pls=$pls WHERE art='$cart' AND type='25' ");
					}else{
						$log.='<span class="red">你没有佣兵！</span><br />';
					}
				}elseif($sp_cmd == 'sp_longaotian'){
					if (($lvl<11)||($club!=10)||($rage<100)){
						$log .= '<span class="red">怒气不足。</span><br />';
						$mode = 'command';
					}else{
						addnews ( $now, 'rageskill', $name, '', '__lat' );
						$ldice=rand(1,7);
						if ($ldice==1){
							$log.='<span class="lime">你感觉强壮无比。</span><br />';
							$mhp=round($mhp*1.5);
						}elseif($ldice==2){
							$log.='<span class="red">你感觉 很难受。</span><br />';
							$mhp=floor($mhp/2)+1;
							if ($hp>$mhp) $hp=$mhp;
						}elseif($ldice==3){
							$log.='<span class="lime">你获得了一笔横财。</span><br />';
							$money=round($money*1.5)+1;
						}elseif($ldice==4){
							$log.='<span class="red">您真是两袖清风。</span><br />';
							$money=0;
						}elseif($ldice==5){
							$log.='<span class="red">你遗忘了一些东西。</span><br />';
							$wp=$wk=$wc=$wd=$wg=$wf=0;
						}elseif($ldice==6){
							$log.='<span class="lime">你学到了新的姿势。</span><br />';
							$wp=$wp+30;$wk=$wk+30;$wg=$wg+30;$wd=$wd+30;$wc=$wc+30;$wf=$wf+30;
						}elseif($ldice==7){
							$log.='<span class="yellow">你好像没有什么变化？</span><br />';
							$club=17;
						}
						$rage=0;
						$mode = 'command';
					}
				}elseif($sp_cmd == 'sp_skpts'){
					include_once GAME_ROOT.'./include/game/clubskills.func.php';
					calcskills($skarr);
					$p12[1]=1; $p12[2]=2;
					$mode='sp_skpts';
				}elseif($sp_cmd == 'sp_gemming'){
					$mode='sp_gemming';
				}elseif($sp_cmd == 'sp_trapcvt'){
					$mode='sp_trapcvt';
				}elseif($sp_cmd == 'sp_rcktcvt'){
					$mode='sp_rcktcvt';
				}elseif($sp_cmd == 'sp_cloak'){
					if (($lvl<19)||($sktime<=0)||($club!=19)||($rage<100))
					{
						$log .= '<span class="red">怒气不足。</span><br />';
						$mode = 'command';
					}
					else
					{
						$rage-=100;
						$dcloak=$now+45;
						$log.="<span class=\"yellow\">你进入了隐身状态，处于隐身状态时你不会被敌人发现。<br>这个状态将持续45秒或直到你作出一次攻击，这次攻击将必定命中并产生200%伤害。</span><br>";
						$sktime--;
						$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
						$mode='command';
					}
				}elseif($sp_cmd == 'sp_auraa'){
					if (($lvl<3)||($club!=16)||($rage<30))
					{
						$log .= '<span class="red">怒气不足，需要30怒气。</span><br />';
						$mode = 'command';
					}
					else
					{
						$rage-=30;
						if ($auraa<$now)
						{
							$auraa=$now+120;
							$log.="<span class=\"yellow\">光环成功开启。作用时间：120秒。</span>";
						}
						else
						{
							$auraa+=120;
							$log.="<span class=\"yellow\">光环有效时间已延长120秒。</span>";
						}
					}
				}elseif($sp_cmd == 'sp_aurab'){
					if (($lvl<7)||($club!=16)||($rage<30))
					{
						$log .= '<span class="red">怒气不足，需要30怒气。</span><br />';
						$mode = 'command';
					}
					else
					{
						$rage-=30;
						if ($aurab<$now)
						{
							$aurab=$now+180;
							$log.="<span class=\"yellow\">光环成功开启。作用时间：180秒。</span>";
						}
						else
						{
							$aurab+=180;
							$log.="<span class=\"yellow\">光环有效时间已延长180秒。</span>";
						}
					}
				}elseif($sp_cmd == 'sp_aurac'){
					if (($lvl<11)||($club!=16)||($rage<60))
					{
						$log .= '<span class="red">怒气不足，需要60怒气。</span><br />';
						$mode = 'command';
					}
					else
					{
						$rage-=60;
						if ($aurac<$now)
						{
							$aurac=$now+120;
							$log.="<span class=\"yellow\">光环成功开启。作用时间：120秒。</span>";
						}
						else
						{
							$aurac+=120;
							$log.="<span class=\"yellow\">光环有效时间已延长120秒。</span>";
						}
					}
				}elseif($sp_cmd == 'sp_aurad'){
					if (($lvl<15)||($club!=16)||($rage<30))
					{
						$log .= '<span class="red">怒气不足，需要30怒气。</span><br />';
						$mode = 'command';
					}
					else
					{
						$rage-=30;
						if ($aurad<$now)
						{
							$aurad=$now+240;
							$log.="<span class=\"yellow\">光环成功开启。作用时间：240秒。</span>";
						}
						else
						{
							$aurad+=240;
							$log.="<span class=\"yellow\">光环有效时间已延长240秒。</span>";
						}
					}
				}else{
					$mode = $sp_cmd;
				}
				
			} elseif($command == 'team') {
				if ($gametype==2 && $teamPass)
				{
					$log.="此命令在本游戏模式下不可用。<br>";
					$mode="command";
				}
				else
				{
					include_once GAME_ROOT.'./include/game/team.func.php';
					if($teamcmd == 'teamquit') {				
						teamquit();
					} else{
						teamcheck();
					}
				}
			}
		} elseif($mode == 'item') {
			include_once GAME_ROOT.'./include/game/item2.func.php';
			$item = substr($command,3);
			use_func_item($usemode,$item);
		} elseif($mode == 'itemmain') {
			include_once GAME_ROOT.'./include/game/itemmain.func.php';
			if($command == 'itemget') {
				itemget();
			} elseif($command == 'itemadd') {
				itemadd();
			} elseif($command == 'itemmerge') {
				if($merge2 == 'n'){itemadd();}
				else{itemmerge($merge1,$merge2);}
			} elseif($command == 'itemmove') {
				itemmove($from,$to);
			} elseif(strpos($command,'drop') === 0) {
				$drop_item = substr($command,4);
				itemdrop($drop_item);
			} elseif(strpos($command,'off') === 0) {
				$off_item = substr($command,3);
				itemoff($off_item);
				//itemadd();
			} elseif(strpos($command,'swap') === 0) {
				$swap_item = substr($command,4);
				itemdrop($swap_item);
				itemadd();
			} elseif($command == 'itemmix') {
				if (isset($itemselect) && $itemselect==999)
					$mode='command';
				else
				{
					$mixlist = array();
					if (!isset($mixmask))
					{
						for($i=1;$i<=6;$i++)
							if(isset(${'mitm'.$i}) && ${'mitm'.$i} == $i)
								$mixlist[] = $i;
					}
					else
					{
						for($i=1;$i<=6;$i++)
							if ($mixmask&(1<<($i-1)))
								$mixlist[] = $i;
					}
					if (isset($itemselect))
						itemmix($mixlist,$itemselect);
					else  itemmix($mixlist);
				}
			} elseif($command == 'itemencase') {
				if(strpos($arbsk,'^')!==false && $arbs && $arbe){
					$ilist = array();
					for($i=1;$i<=6;$i++){
						if(isset(${'mitm'.$i}) && ${'mitm'.$i} == $i){
							$ilist[] = $i;
						}
					}
					item_encase($ilist);
				}else{
					$log.="<span class='red'>你身上没有背包，或是没有将背包装备上！<br>";
				}
			} elseif($command == 'iteminfo') {
				if(strpos($arbsk,'^')!==false && $arbs && $arbe){
					item_info();
				}else{
					$log.="<span class='red'>你身上没有背包，或是没有将背包装备上！<br>";
				}
			} elseif(strpos($command,'usebagitm') !==false) {
				if(strpos($arbsk,'^')!==false && $arbs && $arbe){
					$itemid = substr($command,10);
					item_out($itemid);
				}else{
					$log.="<span class='red'>你身上没有背包，或是没有将背包装备上！<br>";
				}
			}
		} elseif($mode == 'special') {
			include_once GAME_ROOT.'./include/game/special.func.php';
			if(strpos($command,'pose') === 0) {
				$pose = substr($command,4,1);
				$log .= "基础姿态变为<span class=\"yellow\">$poseinfo[$pose]</span>。<br> ";
				$mode = 'command';
			} elseif(strpos($command,'tac') === 0) {
				$tactic = substr($command,3,1);
				$log .= "应战策略变为<span class=\"yellow\">$tacinfo[$tactic]</span>。<br> ";
				$mode = 'command';
			} elseif(strpos($command,'inf') === 0) {
				$infpos = substr($command,3,1);
				chginf($infpos);
			} elseif(strpos($command,'chkp') === 0) {
				$itmn = substr($command,4,1);
				chkpoison($itmn);
			} elseif(strpos($command,'shop') === 0) {
				$shop = substr($command,4,2);
				shoplist($shop);
			} elseif(strpos($command,'clubsel') === 0) {
				$clubchosen = substr($command,7,1);
				include_once GAME_ROOT.'./include/game/clubslct.func.php';
				$retval=selectclub($clubchosen);
				if ($retval==0)
					$log.="称号选择成功。<br>";
				else if ($retval==1)
					$log.="称号选择失败，称号一旦被选择便无法更改。<br>";
				else if ($retval==2)
					$log.="未选择称号。<br>";
				else  $log.="称号选择非法！<br>";
				$mode = 'command';
			}
		} elseif($mode == 'senditem') {
			include_once GAME_ROOT.'./include/game/battle.func.php';
			senditem();
		} elseif($mode == 'ctrlhostage') {
			include_once GAME_ROOT.'./include/game/battle.func.php';
			ctrlhostage();
		} elseif($mode == 'ctrlhasi') {
			include_once GAME_ROOT.'./include/game/battle.func.php';
			ctrlhasi();
		}elseif($mode == 'combat') {
		global $wepe,$w_mhp;
			include_once GAME_ROOT.'./include/game/combat.func.php';
			//checkskill 不新开文件了
			$sflag=true;
			if (strpos($bsk,'__') === 0) $sflag=false;
			if (($bsk=='teach')&&(($club!=26))) $sflag=false;
			if (($bsk=='blame')&&(($club!=26)||($lvl<7)||($rage<20))) $sflag=false;
			if (($bsk=='mark')&&(($club!=25)||($lvl<11)||($rage<100))) $sflag=false;
			if (($bsk=='focus')&&(($club!=25)||($rage<15)||($lvl<3))) $sflag=false;
			if (($bsk=='threat')&&(($club!=11)||($rage<30)||($lvl<7))) $sflag=false;
			if (($bsk=='punch')&&(($club!=23)||($rage<30)||($lvl<7))) $sflag=false;
			if (($bsk=='sting')&&(($club!=8)||($rage<40)||($lvl<11))) $sflag=false;
			if (($bsk=='assasinate')&&(($club!=8)||($sktime<1)||($lvl<19))) $sflag=false;
			if (($bsk=='fireball')&&(($hp<=400)||($mhp<=400)||($lvl<7)||($club==17))) $sflag=false;
			if (($bsk=='absorb')&&(($club!=18)||($sktime<1)||($lvl<15)||($rage<100))) $sflag=false;
			if (($bsk=='analysis')&&(($club!=18)||($rage<60)||($lvl<11))) $sflag=false;
			if (($bsk=='ego')&&(($club!=10)||($lvl<3)||($rage<30))) $sflag=false;
			if (($bsk=='dominate')&&(($club!=10)||($sktime<1)||($lvl<15)||($rage<100))) $sflag=false;
			if (($bsk=='boom')&&(($club!=5)||($lvl<3)||($rage<60)||(strpos($wepk,'WD')===false))) $sflag=false;
			if (($bsk=='inplosion')&&(($club!=5)||($sktime<1)||($lvl<19)||($rage<50)||(strpos($wepk,'WD')===false))) $sflag=false;
			if (($bsk=='crit')&&(($club!=9)||($lvl<3)||($rage<40))) $sflag=false;
			if (($bsk=='recharge')&&(($club!=9)||($sktime<1)||($lvl<11))) $sflag=false;
			if (($bsk=='innerfire')&&(($club!=9)||($lvl<19)||($rage<60))) $sflag=false;
			if (($bsk=='net')&&(($club!=7)||($lvl<7)||($rage<15))) $sflag=false;
			if (($bsk=='suppress')&&(($club!=14)||($lvl<7)||($rage<30)||($hp<=round($mhp*0.15)))) $sflag=false;
			if (($bsk=='aim')&&(($club!=4)||($lvl<3)||($rage<20)||(strpos($wepk,'WG')===false))) $sflag=false;
			if (($bsk=='roar')&&(($club!=4)||($lvl<15)||($rage<100)||(strpos($wepk,'WG')===false))) $sflag=false;
			if (($bsk=='burst')&&(($club!=97)||($rage<25)||(strpos($wepk,'WG')===false))) $sflag=false;
			if (($bsk=='slayer')&&(($club!=97)||($lvl<15)||($rage<70)||(strpos($wepk,'WG')===false))) $sflag=false;
			if (($bsk=='eagleeye')&&(($club!=3)||($lvl<11)||($rage<60)||(strpos($wepk,'WC')===false))) $sflag=false;
			if (($bsk=='enchant')&&(($club!=3)||($lvl<3)||($rage<10)||(strpos($wepk,'WC')===false))) $sflag=false;
			if (($bsk=='bash')&&(($club!=1)||($lvl<11)||($rage<70)||(strpos($wepk,'WP')===false))) $sflag=false;
			if (($bsk=='ambush')&&(($club!=1)||($lvl<3)||($rage<20)||(strpos($wepk,'WP')===false))) $sflag=false;
			if (($bsk=='storm')&&(($club!=2)||($lvl<11)||($rage<70)||(strpos($wepk,'WK')===false))) $sflag=false;
			if (($bsk=='steeldance')&&(($club!=2)||($lvl<15)||($rage<70)||(strpos($wepk,'WK')===false))) $sflag=false;
			if (($bsk=='ragestrike')&&(($club!=19)||($lvl<7)||($rage<50))) $sflag=false;
			if (($bsk=='entangle')&&(($club!=24 && $club!=99)||($lvl<7)||($souls<1))) $sflag=false;
			if (($bsk=='fear')&&(($club!=24 && $club!=99)||($lvl<11)||($souls<1))) $sflag=false;
			if (($bsk=='corrupt')&&(($club!=24 && $club!=99)||($lvl<15)||($souls<1))) $sflag=false;
			if (($bsk=='nightmare')&&(($club!=24 && $club!=99)||($lvl<19)||($souls<4))) $sflag=false;
			if (($bsk=='hijack')&&(($club!=21)||($wepe/2<$w_mhp))) $sflag=false;
			if ((strpos($bsk,'aurora') === 0) && $bsk!='aurora') $sflag=false;
			if ($bsk=='aurora'){
				if (($club!=6)||($lvl<7)||($rage<15)){
					$sflag=false;
				}else{
					global $weather;
					$bsk.=$weather;
				}
			}
			//[u150915]偶像大师技能
			if (($bsk=='battlesong')&&(($club!=70 && $club!=99)||($lvl<3)||($rage<5)||($ss<20)||(strpos($artk,'ss')===false))) $sflag=false;
			if ($sflag) {combat(1,$command,$bsk);}else {$log.="未满足使用技能的条件。<br>";$mode = 'command';}
			//
		} elseif($mode == 'rest') {
			include_once GAME_ROOT.'./include/state.func.php';
			rest($command);
//		} elseif($mode == 'chgpassword') {
//			include_once GAME_ROOT.'./include/game/special.func.php';
//			chgpassword($oldpswd,$newpswd,$newpswd2);
//		} elseif($mode == 'chgword') {
//			include_once GAME_ROOT.'./include/game/special.func.php';
//			chgword($newmotto,$newlastword,$newkillmsg);
		} elseif($mode == 'corpse') {
			include_once GAME_ROOT.'./include/game/itemmain.func.php';
			getcorpse($command);
		} elseif($mode == 'team') {
			if ($gametype==2 && $teamPass)
			{
				$log.="此命令在本游戏模式下不可用。<br>";
				$mode="command";
			}
			else
			{
				include_once GAME_ROOT.'./include/game/gametype.func.php';
				include_once GAME_ROOT.'./include/game/team.func.php';
				if (check_teamfight_always_randteam() && $command!="teamautoc")
				{
					$log.='管理员设定了不允许创建队伍。<br>';
				}
				else
				{
					if ($command=="teammake") teammake($nteamID,$nteamPass);
					if ($command=="teamjoin") teamjoin($nteamID,$nteamPass);
					if ($command=="teamquit") teamquit($nteamID,$nteamPass);
					if ($command=="teamc")
					{
						if (strpos($nteamID,"路人")===0)
						{
							$log.="不允许创建/加入以“路人”开头的队伍。<br>";
						}
						else
						{
							$result = $db->query("SELECT pid FROM {$tablepre}players WHERE teamID='$nteamID'");
							if ($db->num_rows($result)>=get_max_teammate_num())
								$log.="队伍人数达到上限，无法加入！<br>";
							else
							{
								$log.="正在试图创建队伍<span class=\"yellow\">$nteamID</span>.. ";
								teammake($nteamID,$nteamPass);
								if (!$teamID) 
								{
									$log.="正在试图加入已存在的队伍<span class=\"yellow\">$nteamID</span>.. ";
									teamjoin($nteamID,$nteamPass);
								}
								if ($teamPass) $gamedata['url'] = 'game.php';
							}
						}
					}
					if ($command=="teamautoc")
					{
						include_once GAME_ROOT.'./include/game/gametype.func.php';
						teamfight_auto_allocate_team();
						$log='';
						$gamedata['url'] = 'game.php';
					}
				}
			}
		} elseif($mode == 'shop') {
			if((in_array($pls,$shops))||(($club==11)&&($lvl>=3))){
				if($command == 'shop') {
					$mode = 'sp_shop';
				} else {
					include_once GAME_ROOT.'./include/game/itemmain.func.php';
					itembuy($command,$shoptype,$buynum);
				}
			}else{
				$log .= '<span class="yellow">你所在的地区没有商店。</span><br />';
				$mode = 'command';
			}
		} elseif($mode == 'deathnote') {
			if($dnname){
				include_once GAME_ROOT.'./include/game/item2.func.php';
				deathnote($item,$dnname,$dndeath,$dngender,$dnicon,$name);
			} else {
				$log .= '嗯，暂时还不想杀人。<br>你合上了■DeathNote■。<br>';
				$mode = 'command';
			}
		}elseif($mode == 'trace') {
			if($trname){
				if (($club==25)&&($lvl>=19)){
					if ($name==$trname){
						$log .= '<span class="red">不能指定自己为目标。</span><br>';
					}else{
						$result = $db->query("SELECT pid FROM {$tablepre}players WHERE name = '$trname' AND type = 0");
						if(!$db->num_rows($result)){
							$log .= '<span class="red">该ID不存在！</span><br>';
						}else{
							$tarid = $db->result($result, 0);
							$db->query("UPDATE {$tablepre}users SET sktime='$tarid' WHERE username='$name'");
							$log.="<span id=\"HsUipfcGhU\"></span>";
						}
					}
				}else{
					$log .= '<span class="yellow">你不能这么做！</span><br>';
				}
			}
			$mode = 'command';
		}elseif($mode == 'oneonone') {
			if($dnname){
						include_once GAME_ROOT.'./include/game/special.func.php';
						oneonone($dnname,$name);
					} else {
						$log .= '约战取消。<br>';
						$mode = 'command';
					}
		} elseif ($mode == 'sp_skpts') {
			include_once GAME_ROOT.'./include/game/clubskills.func.php';
			upgradeclubskills($command);
			calcskills($skarr);
			$p12[1]=1; $p12[2]=2;
		} elseif ($mode == 'sp_pbomb') {
			include_once GAME_ROOT.'./include/game/special.func.php';
			if ($command=="YES") press_bomb();
			$mode = 'command';
		} elseif ($mode == "gametype2special") {
			if ($gametype!=2) 
			{ 
				$log.="此命令在本游戏模式下不可用。<br>"; 
				$mode = 'command';
			}
			else
			{
				if ($command=="sendflare")
				{
					$log.="发出支援请求成功。<br>";
					$flare=1;
					$mode='command';
				}
				else  if ($command=="stopflare")
				{
					$log.="取消支援请求成功。<br>";
					$flare=0;
					$mode='command';
				}
				else  if (strpos($command,'teammoveto') === 0)
				{
					$where=substr($command,10);
					include_once GAME_ROOT.'./include/game/search.func.php';
					move($where);
					if($coldtimeon){$cmdcdtime=$movecoldtime;}
				}
				else  if (strpos($command,'findteam') === 0)
				{
					$which=(int)substr($command,8);
					$tresult = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$which'");
					if (!$db->num_rows($tresult))
					{
						$log.="队友不存在。<br>";
						$mode='command';
					}
					else
					{
						$edata = $db->fetch_array($tresult);
						if (!$teamID || $edata['teamID']!=$teamID)
						{
							$log.="对方不是你的队友！<br>";
							$mode='command';
						}
						else  if ($edata['hp']<=0)
						{
							$log.="对方已经死亡！<br>";
							$mode=='command';
						}
						else  if ($edata['pls']!=$pls)
						{
							$log.="对方与你不在同一个地图！<br>";
							$mode=='command';
						}
						else
						{
							$bid = $which;
							$action = 'team'.$which;
							include_once GAME_ROOT.'./include/game/battle.func.php';
							findteam($edata);
						}	
					}
				}
			}
		} elseif ($mode=='sp_gemming'){
			if ($command=='gemming' && $t1!='back' && $t2!='back')
			{
				include_once GAME_ROOT.'./include/game/special.func.php';
				gemming($t1,$t2);
			}
			$mode='command';
		} elseif ($mode=='sp_trapcvt'){
			if ($command=='trapcvt' && $trap!='back')
			{
				include_once GAME_ROOT.'./include/game/special.func.php';
				trapcvt($trap);
			}
			$mode='command';
		} elseif ($mode=='sp_rcktcvt'){
			if ($command=='rcktcvt' && $t1!='back' && $t2!='back')
			{
				include_once GAME_ROOT.'./include/game/special.func.php';
				rcktcvt($t1,$t2);
			}
			$mode='command';
		} elseif($mode == 'gem_moonstone') {
		global $gemname,$gempower,$gemstate;
			if(($command!=='back')&&($gempower>=250)){
			include_once GAME_ROOT.'./include/game/gem.func.php';
			magic_gem_moonstone('月长石',$sac_type,$sac_num);
			}elseif($gempower<250){
			$log.="gem不足，无法激活{$gemname}。<br>";
			}else{
			$log.="你放弃了献祭。<br>";
			}
			$mode='command';
		} elseif($mode == 'gem_expend') {
		global $gemname,$gempower,$gemstate;
			if($command!=='back'){
			include_once GAME_ROOT.'./include/game/gem.func.php';
			feed_magic_gem($blocks);
			}else{
			$log.="你身上没有可用来补充gem的方块或宝石！<br>";
			}
			$mode='command';
		} elseif($mode == 'gem_searchitem') {
		global $gemname,$gempower,$gemstate;
			if($command!=='back'){
			include_once GAME_ROOT.'./include/game/gem.func.php';
			magic_find_item($itmname);
			}else{
			$log.="还没想好找什么吗？<br>";
			}
			$mode='command';
		} elseif ($mode=='gem'){
			global $gemstate,$gemname,$gempower,$gemexp,$gemlvl;
			if($gemstate!=0){
				if ($command == 'gem_states') {
					//宝石名字
					$log .= "<span class='clan'>『{$gemname}』</span><br>";
					//GEM值
					if(($club==49)||($club==53)){$log .= "GEM值：<span class='yellow'>{$gempower}／3000</span><br>";}
					else{$log .= "GEM值：<span class='yellow'>{$gempower}／1000</span><br>";}
					//宝石经验
					if($gemlvl<3){$log .= "积累经验：<span class='yellow'>{$gemexp}／100</span><br>";}
					else{$log .= "积累经验：<span class='yellow'>Ｎ／Ａ</span><br>";}
					//宝石等级
					if($gemlvl<3){$log .= "宝石等级：<span class='red'>{$gemlvl}</span><br>";}
					else{$log .= "宝石等级：<span class='red'>Max</span><br>";}
					//宝石状态
					if($gemstate==1){
						if(($gemname=='月长石')||($gemname=='翠榴石')){$log .= "当前状态：<span class='yellow'>需主动激活</span><br>";}
						else{$log .= "当前状态：<span class='yellow'>未激活</span><br>";}
					}elseif($gemstate==3){
						$log.="当前状态：<span class='grey'>封印中</span><br>";
					}elseif($gemstate==4){
						$log.="当前状态：<span class='grey'>冷却中</span><br>";
					}else{
					$log .= "当前状态：<span class='red'>已激活</span><br>";
					}
				}elseif ($command == 'gem_on'){
					if($gemname=='翠榴石'){
					include_once GAME_ROOT.'./include/game/gem.func.php';
					magic_gem('翠榴石');		
					}elseif($gemstate==4){
					$log .= "<span class='red'>魔法宝石处于封印中，无法激活！</span><br>";					
					}else{
					$gemstate=2;
					$log .= "宝石魔法已经激活。<br>";
					addnews($now,'gem_magic',$name,$gemname,'激活');
					}
				}elseif ($command == 'gem_off'){
					$gemstate=1;
					$log .= "宝石魔法已经被关闭。<br>";
					addnews($now,'gem_magic',$name,$gemname,'关闭');
				}elseif($sp_cmd == 'sp_moonstone'){
					$mode='gem_moonstone';
				}elseif ($sp_cmd == 'sp_gemexpend'){
					$mode='gem_expend';
				}
			}else{
				$log .= "<span class=\"yellow\">你身上没有绑定宝石。</span><br>";
			}
			$mode = 'command';
		} else {
			$mode = 'command';
		}
		
		if(strpos($action,'pacorpse')===0 && $gamestate < 40){
//			if($state == 1 || $state == 2 || $state ==3){
//				$state = 0;
//			}
			$cid = str_replace('pacorpse','',$action);
			if($cid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$cid' AND hp=0");
				if($db->num_rows($result)>0){
					$edata = $db->fetch_array($result);
					include_once GAME_ROOT.'./include/game/battle.func.php';
					findcorpse($edata);					
				}	
			}	
		}
				
		//指令执行完毕，更新冷却时间
		if($coldtimeon && isset($cmdcdtime)){
			$nowmtime = floor(getmicrotime()*1000);
			$cdsec = floor($nowmtime/1000);
			$cdmsec = fmod($nowmtime , 1000);
			$cdtime = $cmdcdtime;
			//$psdata = Array('pid' => $pid, 'cdsec' => $cdsec, 'cdmsec' => $cdmsec, 'cdtime' => $cdtime, 'cmd' => $mode);
			//set_pstate($psdata);
			$rmcdtime = $cmdcdtime;
		}
		//读取背包内道具
		if(strpos($arbsk,'^')!==false && $arbs && $arbe){
			include_once GAME_ROOT.'./include/game/itembag.func.php';
			$itemlist = item_arr();
		}
		$endtime = $now;
		$cmdnum ++;
		
		//$db->query("UPDATE {$tablepre}players SET endtime='$now',cdsec='$cdsec',cdmsec='$cdmsec',cdtime='$cdtime',club='$club',hp='$hp',mhp='$mhp',sp='$sp',msp='$msp',att='$att',def='$def',pls='$pls',lvl='$lvl',exp='$exp',money='$money',rp='$rp',bid='$bid',inf='$inf',rage='$rage',pose='$pose',tactic='$tactic',state='$state',killnum='$killnum',wp='$wp',wk='$wk',wg='$wg',wc='$wc',wd='$wd',wf='$wf',teamID='$teamID',teamPass='$teamPass',wep='$wep',wepk='$wepk',wepe='$wepe',weps='$weps',wepsk='$wepsk',arb='$arb',arbk='$arbk',arbe='$arbe',arbs='$arbs',arbsk='$arbsk',arh='$arh',arhk='$arhk',arhe='$arhe',arhs='$arhs',arhsk='$arhsk',ara='$ara',arak='$arak',arae='$arae',aras='$aras',arask='$arask',arf='$arf',arfk='$arfk',arfe='$arfe',arfs='$arfs',arfsk='$arfsk',art='$art',artk='$artk',arte='$arte',arts='$arts',artsk='$artsk',itm0='$itm0',itmk0='$itmk0',itme0='$itme0',itms0='$itms0',itmsk0='$itmsk0',itm1='$itm1',itmk1='$itmk1',itme1='$itme1',itms1='$itms1',itmsk1='$itmsk1',itm2='$itm2',itmk2='$itmk2',itme2='$itme2',itms2='$itms2',itmsk2='$itmsk2',itm3='$itm3',itmk3='$itmk3',itme3='$itme3',itms3='$itms3',itmsk3='$itmsk3',itm4='$itm4',itmk4='$itmk4',itme4='$itme4',itms4='$itms4',itmsk4='$itmsk4',itm5='$itm5',itmk5='$itmk5',itme5='$itme5',itms5='$itms5',itmsk5='$itmsk5',itm6='$itm6',itmk6='$itmk6',itme6='$itme6',itms6='$itms6',itmsk6='$itmsk6' where pid='$pid'");
	}
	
	$lowercasealphabet='abcdefghijklmnopqrstuvwxyz';
	$vcode=$lowercasealphabet[rand(0,25)];
	$randstring=$lowercasealphabet[rand(0,4)];
	$trick['a']=$lowercasealphabet[rand(0,25)];
	$trick['b']=$lowercasealphabet[rand(0,25)];
	$trick['c']=$lowercasealphabet[rand(0,25)];
	$trick['d']=$lowercasealphabet[rand(0,25)];
	$trick['e']=$lowercasealphabet[rand(0,25)];
	$trick[$randstring]=$vcode;
	//var_dump($pdata['action']);
	player_save($pdata);
		
	//显示指令执行结果
	$gamedata['innerHTML']['notice'] = ob_get_contents();
	if($coldtimeon && $showcoldtimer && $rmcdtime){
		$gamedata['timer'] = $rmcdtime;
	}
	if($hp > 0 && $coldtimeon && $showcoldtimer && $rmcdtime){
		$log .= "行动冷却时间：<span id=\"timer\" class=\"yellow\">0.0</span>秒<br>";
	}
	
}
init_profile();

if ($gametype==2 && $teamID)
{
	$teamchatdata = getteamchat(0,$teamID,4);
	$result = $db->query("SELECT pid,mhp,hp,pls,name,flare FROM {$tablepre}players WHERE teamID='$teamID' AND pid<>'$pid'");
	$teammate_num = $db->num_rows($result);
	$i=0; 
	if ($hp>0) 
	{
		$total_mhp=$mhp; $total_hp=$hp;
	}
	else
	{
		$total_mhp=0; $total_hp=0;
	}
	while($data = $db->fetch_array($result)) 
	{
		$i++; $teammateinfo[$i]=$data; $teammateinfo[$i]['dummy']=0;
		if ($teammateinfo[$i]['pls']==$pls && $teammateinfo[$i]['hp']>0)
		{
			$total_mhp+=$teammateinfo[$i]['mhp'];
			$total_hp+=$teammateinfo[$i]['hp'];
		}
	}
	for ($k=$i+1; $k<=5; $k++) { $teammateinfo[$k]['dummy']=1; $teammateinfo[$k]['pls']=-1; }
	
	include_once GAME_ROOT.'./include/game/gametype.func.php';
	if (!check_teamfight_groupattack_setting() || !$teamID) unset($total_mhp);	
}
else $teammate_num=0;

//坑爹的discuz template会自动把图片html换行导致图片之间有空格，只好不用模板手动拼出img列表了
$auratext="";
if ($auraa>=$now) { $tleft=$auraa-$now; $auratext.='<img src="img/auraA.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurab>=$now) { $tleft=$aurab-$now; $auratext.='<img src="img/auraB.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurac>=$now) { $tleft=$aurac-$now; $auratext.='<img src="img/auraC.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($aurad>=$now) { $tleft=$aurad-$now; $auratext.='<img src="img/auraD.gif" title="将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($club==16 && $lvl>=19) $auratext.='<img src="img/auraE.gif" title="每个光环将提升基础伤害30%" width="24" height="24">';
if ($debuffa>=$now) { $tleft=$debuffa-$now; $auratext.='<img src="img/debuffA.png" title="恶灵缠绕，物理伤害输出降低20%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($debuffb>=$now) { $tleft=$debuffb-$now; $auratext.='<img src="img/debuffB.gif" title="恐惧状态，受到物理伤害增加20%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($debuffc>=$now) { $tleft=$debuffb-$now; $auratext.='<img src="img/debuffC.gif" title="灵魂腐蚀，基础攻防降低60%&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($dcloak>=$now) { $tleft=$dcloak-$now; $auratext.='<img src="img/cloak.gif" title="隐身状态&#13;将于'.$tleft.'秒后消失" width="24" height="24">'; }
if ($club==23 && $sktime<1) { $auratext.='<img src="img/duel.gif" title="决斗状态" width="24" height="24">'; }

if ($club==25){
	$result = $db->query("SELECT pls FROM {$tablepre}players WHERE pid = '$sktime'");
	$mp = $db->result($result, 0);
	if (($mp>=0)&&($lvl>=11)&&($sktime>0)){
		$markpls=$plsinfo[$mp];
	}else{
		$markpls="暂无目标";
	}
}

if($hp <= 0) {
	$dtime = date("Y年m月d日H时i分s秒",$endtime);
	$kname='';
	if($bid) {
		$result = $db->query("SELECT name FROM {$tablepre}players WHERE pid='$bid'");
		if($db->num_rows($result)) { $kname = $db->result($result,0); }
	}
	ob_clean();
	include template('death');
	$gamedata['innerHTML']['cmd'] = ob_get_contents();
	$mode = 'death';
} elseif($cmd){	
	$gamedata['innerHTML']['cmd'] = $cmd;
} elseif($itms0){
	ob_clean();
	include template('itemfind');
	$gamedata['innerHTML']['cmd'] = ob_get_contents();
} elseif($state == 1 || $state == 2 || $state ==3) {
	ob_clean();
	include template('rest');
	$gamedata['innerHTML']['cmd'] = ob_get_contents();
} elseif(!$cmd) {
	ob_clean();
	if($mode&&file_exists(GAME_ROOT.TPLDIR.'/'.$mode.'.htm')) {
		include template($mode);
	} else {
		include template('command');
	}
	$gamedata['innerHTML']['cmd'] = ob_get_contents();
	//$gamedata['cmd'] .= '<br><br><input type="button" id="submit" onClick="postCommand();return false;" value="提交">';
} else {
	$log .= '游戏流程故障，请联系管理员<br>';
	//$gamedata['innerHTML']['cmd'] = $cmd;
	//$gamedata['cmd'] .= '<br><br><input type="button" id="submit" onClick="postCommand();return false;" value="提交">';
}

//$pm_pageendtime = microtime();
//$pm_starttime = explode(" ",$pm_pagestartime);
//$pm_endtime = explode(" ",$pm_pageendtime);
//$pm_totaltime = $pm_endtime[0]-$pm_starttime[0]+$pm_endtime[1]-$pm_starttime[1];
//$pm_timecost = sprintf("%.2f",$pm_totaltime);
//$log.="<span class=\"grey\">页面执行时间： $pm_timecost ms</span><br>";


if(isset($url)){$gamedata['url'] = $url;}
$gamedata['innerHTML']['pls'] = $plsinfo[$pls];
$gamedata['innerHTML']['anum'] = $alivenum;

ob_clean();
$main ? include template($main) : include template('profile');
$gamedata['innerHTML']['main'] = ob_get_contents();
$gamedata['innerHTML']['log'] = $log;
if(isset($error)){$gamedata['innerHTML']['error'] = $error;}
$gamedata['value']['teamID'] = $teamID;
if($teamID){
	$gamedata['innerHTML']['chattype'] = "<select name=\"chattype\" value=\"2\"><option value=\"0\">$chatinfo[0]<option value=\"1\" selected>$chatinfo[1]</select>";
}else{
	$gamedata['innerHTML']['chattype'] = "<select name=\"chattype\" value=\"2\"><option value=\"0\" selected>$chatinfo[0]</select>";
}
//foreach($gamedata as $k => $v){
//	$w .= "{ $k } => { $v };\n\r";
//}
//writeover('a.txt',$w);
ob_clean();
$jgamedata = compatible_json_encode($gamedata);
//$json = new Services_JSON();
//$jgamedata = $json->encode($gamedata);
echo $jgamedata;

ob_end_flush();
//$t_e=getmicrotime();
//putmicrotime($t_s,$t_e,'cmd_time');

?>
