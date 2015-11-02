<?php
if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function calc($modval, $baseval, $curgid, $curuid, $curpid, $sttime, $vatime)
{
	$s=((string)$baseval).((string)$curgid).((string)$baseval).((string)$curuid).((string)$curpid).((string)$sttime).((string)$baseval).((string)$vatime).((string)$baseval);
	$s=md5($s);
	$hashval=0;
	for ($i=0; $i<strlen($s); $i++) $hashval=($hashval*16+ord($s[$i]))%$modval;
	return $hashval;
}

function swap(&$a,&$b)
{
	$t=$a; $a=$b; $b=$t;
}

function getclub($who, &$c1, &$c2, &$c3)
{
	global $db,$tablepre,$starttime,$validtime;
	$result = $db->query("SELECT gid FROM {$tablepre}winners ORDER BY gid desc LIMIT 1");
	$t=$db->fetch_array($result); $curgid=$t['gid']+1;
	$result = $db->query("SELECT uid FROM {$tablepre}users WHERE username='$who'");
	$t=$db->fetch_array($result); $curuid=$t['uid']+2;
	$result = $db->query("SELECT pid FROM {$tablepre}players WHERE name='$who' AND type=0");
	$t=$db->fetch_array($result); $curpid=$result['pid']+3;
	
	$c1=calc(12347,10007,$curgid,$curuid,$curpid,$starttime,$validtime);
	$c1%=6; if ($c1==0) $c1=9;	//超能称号为9号
	
	$delt=0;
	while ($delt<=30)
	{
		$c2=calc(10009,7789+$delt,$curgid,$curuid,$curpid,$starttime,$validtime);
		$c2%=6; $c2++;	if ($c2==6) $c2=23;		//第二个称号不允许超能
		if ($c1!=$c2) break;
		$delt++;
	}
	if ($delt>30) if ($c1==1) $c2=2; else $c2=1;
	
	
	$delt=0;
	while ($delt<=40)
	{
		$c3=calc(11131,6397+$delt,$curgid,$curuid,$curpid,$starttime,$validtime);
		$clubid = array(7,8,10,11,14,18,23,6,16,19,99,20,25,26,28);
		//$clubid = array(7,8,10,11,14,18,23,6,16,19,99,20,25,26,28,70);//增加偶像大师
		$c3%=15; $c3=$clubid[$c3];
		if (($c3!=$c2)&&($c3!=$c1)) break;
		$delt++;
	}
	if ($delt>40){
		$dice=rand(1,3);
		$c3=7;
		if ($dice=1) $c3=11;
		if ($dice=3) $c3=14;
	}

	include_once GAME_ROOT.'./include/game/gametype.func.php';
	//if ($c1==$c3 || $c2==$c3 || (check_teamfight_groupattack_setting() && ($c3==19 || $c3==21))) $c3=99;	//团战不允许踏雪或恐怖份子称号
	
	if ($c1>$c2) swap($c1,$c2);
	if ($c1>$c3) swap($c1,$c3);
	if ($c2>$c3) swap($c2,$c3);
}

function updateskill()
{
	global $db,$tablepre,$name,$club, $wp, $wk, $wc, $wg, $wd, $wf, $money, $hp, $mhp, $sp, $msp, $att, $def,$wep,$wepe,$weps,$wepk,$wepsk,$nosta,$dcloak,$log;
	global $mss,$ss;//[u150929]
	$sktime=0;
	if ($club==1) {$wp+=30;}
	if ($club==2) {$wk+=30;$sktime=1;}
	if ($club==3) {$wc+=30;}
	if ($club==4) {$wg+=30;}
	if ($club==5) {$wd+=20;$sktime=2;}
	if ($club==6) {$sktime=1;}
	if ($club==7) {$sktime=1;}
	if ($club==8) {$sktime=2;}
	if ($club==9) {$wf+=20;$sktime=2;}
	if ($club==10) {$sktime=2;}
	if ($club==11) {$money+=380;$sktime=2;}
	if ($club==16) { $wp+=15; $wk+=15; $wc+=15; $wg+=15; $wd+=15; $wf+=15; }
	if ($club==13) { $mhp+=200; $hp+=200; }
	if ($club==14) { $att+=100; $def+=100; $mhp+=100; $hp+=100;}
	if ($club==18) {$sktime=1;}
	if ($club==19) {$sktime=1; $dcloak=0;}
	if ($club==23) {$wp+=50;$sktime=1;$wep="拳头";$wepsk = '';$wepk = 'WN';$wepe = 0;$weps = $nosta;}
	if ($club==25) { $att+=100; $def+=100;$sktime=-1;}
	if ($club==26) { $mhp+=100; $hp+=100;$sktime=1;}
	if ($club==70) {$mss+=50; $sktime=1; $art="练声谱";$artk='ss';$arte=2;$arts=1;}//[u150929]偶像大师
	$db->query("UPDATE {$tablepre}users SET sktime='$sktime' WHERE username='$name'");
}

function selectclub($id)
{
	global $name, $club;
	if ($club!=0) return 1;
	if ($id==0) return 2;
	getclub($name,$c1,$c2,$c3);
	if ($id==1) { $club=$c1; updateskill(); return 0; }
	if ($id==2) { $club=$c2; updateskill(); return 0; }
	if ($id==3) { $club=$c3; updateskill(); return 0; }
	return 3;
}

?>
