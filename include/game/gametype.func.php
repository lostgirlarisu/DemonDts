<?php

function writeover_array($file,$arr)
{
	$s=''; $in=sizeof($arr);
	for ($i=0; $i<$in; $i++) if (strlen($arr[$i])>5) $s.=$arr[$i]."\n";
	writeover($file,$s);
}

function get_groupid()
{
	global $cuser,$cpass,$db,$tablepre,$gamefounder;
	if ($cuser=='') return 0;
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
	if(!$db->num_rows($result)) return 0;
	$udata = $db->fetch_array($result);
	if($udata['password'] != $cpass) return 0;
	if($cuser===$gamefounder) $mygroup=10; else $mygroup = $udata['groupid'];
	return $mygroup;
}

function check_can_solo(&$oid)
{
	global $cuser,$cpass,$db,$tablepre,$gamefounder;
	if ($cuser=='') return 0;
	$result = $db->query("SELECT * FROM {$tablepre}users WHERE username='$cuser'");
	if(!$db->num_rows($result)) return 0;
	$udata = $db->fetch_array($result);
	if($udata['password'] != $cpass) return 0;
	$oid=$udata['oid'];
	return 2;	//暂时不启用solo认证吧，等真遇到了恶意注册的再说
	return (int)$udata['can_solo']+1;
}

function check_solo_participater()
{
	global $cuser,$gameotherinfo;
	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
	return ($cuser==$a || $cuser==$b);
}

function clear_solo_list()
{
	global $gamecfg;
	$file = config('sololist',$gamecfg);
	writeover_array($file,'');
}

function get_solo_game(&$t1,&$t2)
{
	global $gamecfg, $gamenum;
	$file = config('sololist',$gamecfg);
	$sololist=openfile($file);
	$in=sizeof($sololist);
	for ($i=0; $i<$in; $i++)
	{
		list($gtype,$a,$b,$c,$d,$e,$f,$g) = explode(',',$sololist[$i]);
		if ($gtype==0) continue;
		//$g为特殊标记，表示是否已经执行过了 0:未执行 1:已执行 2:正在执行
		if ($g==2)
		{
			$g=1;
			$sololist[$i]=(string)$gtype.','.(string)$a.','.(string)$b.','.(string)$c.','.(string)$d.','.(string)$e.','.(string)$f.','.(string)$g.',';
		}
		if ($g) continue;
		$t1=$gtype; $t2=(string)$a.','.(string)$b.','.(string)$c.','.(string)$d.','.(string)$e.','.(string)$f.',';
		$g=2; $e=$gamenum;
		$sololist[$i]=(string)$gtype.','.(string)$a.','.(string)$b.','.(string)$c.','.(string)$d.','.(string)$e.','.(string)$f.','.(string)$g.',';
		writeover_array($file,$sololist);
		return 1;
	}
	writeover_array($file,$sololist);
	return 0;
}

function check_can_delete($sologame)
{
	global $cuser;
	list($gtype,$a,$b,$c,$d,$e,$f,$g) = explode(',',$sologame);		
	//只有(solo还未进行时参战双方)或5权限以上管理员有权删除solo
	if ($gtype==1) return (($g==0 && ($cuser==$a || $cuser==$b)) || (get_groupid()>=5));
	//只有5权限以上管理员有权删除团战
	if ($gtype==2) return (get_groupid()>=5);
	return 0;
}

function get_solo_game_html()
{
	$ret='<table border=1><tr align=center><td width=80px height=20px>局号</td><td width=80px height=20px>状态</td><td width=40px>类型</td><td width=250px>参战者</td><td width=100px >特殊条件</td><td width=40px>管理</td></tr>';
	global $gamecfg;
	$file = config('sololist',$gamecfg);
	$sololist=openfile($file);
	$in=sizeof($sololist);
	for ($i=0; $i<$in; $i++)
	{
		list($gtype,$a,$b,$c,$d,$e,$f,$g) = explode(',',$sololist[$i]);
		if ($gtype==0) continue;
		$ret.='<tr align=center>';
		if ($e==0) $ret.='<td>-</td>'; else $ret.="<td>$e</td>";
		if ($g==1) $ret.='<td height=20px><span class="grey">已结束</span></td>';
		else if ($g==0) $ret.='<td height=20px><span class="lime">等待中</span></td>';
		else if ($g==2) $ret.='<td height=20px><span class="red">战斗中</span></td>'; 
		else $ret.="<td height=20px>未知状态$g</td>";
		
		global $cuser;
		if ($gtype==1) 
		{
			$ret.='<td><span class="evergreen">SOLO</span></td>';
			$ret.='<td><span title="查看资料">';
			$ret.="<a href=\"user_profile.php?playerID=$a\">$a</a>";
			$ret.='</span> vs <span title="查看资料">';
			$ret.="<a href=\"user_profile.php?playerID=$b\">$b</a>";
			$ret.='</span></td>';
			$ret.='<td>-</td>';
		}
		else if ($gtype==2)
		{
			$ret.='<td><span class="L5">团战</span></td>';
			$ret.='<td>(任何人均可参加)</td>';
			$ret.="<td>队伍人数上限$c<br>";
			if ($d) $ret.='<span class="yellow">强制随机组队</span>';
			$ret.="</td>";
		}
		else  $ret.="<td>未知模式$gtype</td><td>-</td><td>-</td>";
		
		if (check_can_delete($sololist[$i]))	
			$ret.='<td><a href="solo.php?action=remove&sid='.$f.'">删除</a></td>';
		else  $ret.='<td>-</td>';
		$ret.="</tr>";
	}
	$ret.="</table>";
	return $ret;
}

function add_solo_game($p1,$p2,$mask)
{
	global $gamecfg;
	$file = config('sololist',$gamecfg);
	$sololist=openfile($file);
	$in=sizeof($sololist);
	$sid=(string)rand(0,9999).(string)rand(0,9999).(string)rand(0,9999);
	$sololist[$in]='1,'.(string)$p1.','.(string)$p2.','.(string)$mask.','.''.','.''.','.$sid.','.'0'.',';
	writeover_array($file,$sololist);
}

function add_teamfight_game($p1,$p2)
{
	global $gamecfg;
	$file = config('sololist',$gamecfg);
	$sololist=openfile($file);
	$in=sizeof($sololist);
	$sid=(string)rand(0,9999).(string)rand(0,9999).(string)rand(0,9999);
	$sololist[$in]='2,'.'1'.','.'1'.','.(string)$p1.','.(string)$p2.','.''.','.$sid.','.'0'.',';
	writeover_array($file,$sololist);
}

function delete_solo_game($sid)
{
	global $gamecfg, $cuser;
	$file = config('sololist',$gamecfg);
	$sololist=openfile($file);
	$in=sizeof($sololist);
	for ($i=0; $i<$in; $i++)
	{
		list($gtype,$a,$b,$c,$d,$e,$f,$g) = explode(',',$sololist[$i]);		
		if ($f==$sid && check_can_delete($sololist[$i]))
		{
			array_splice($sololist,$i,1);
			writeover_array($file,$sololist);
			return;
		}
	}
}

function teamfight_auto_allocate_team()
{
	global $gameotherinfo;
	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
	$a=(int)$a; $b=(int)$b; $c=(int)$c;
	if ($b==1)
	{
		include_once GAME_ROOT.'./include/game/team.func.php';
		teammake('路人'.$a.'队','1');
		teamjoin('路人'.$a.'队','1');
		$b++; if ($b>$c) { $a++; $b=1; }
	}
	else
	{
		include_once GAME_ROOT.'./include/game/team.func.php';
		teamjoin('路人'.$a.'队','1');
		$b++; if ($b>$c) { $a++; $b=1; }
	}
	$gameotherinfo=(string)$a.','.(string)$b.','.(string)$c.','.(string)$d.','.(string)$e.','.(string)$f.','.(string)$g.',';
	save_gameinfo();
}

function check_teamfight_groupattack_setting()
{
//	global $gametype,$gameotherinfo,$teamID;
//	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
//	if ($gametype!=2) return 0;
//	//return $d;
	return 0;	//这个功能已经从代码层面移除了
}

function check_teamfight_always_randteam()
{
	global $gametype,$gameotherinfo,$teamID;
	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
	if ($gametype!=2) return 0;
	return $d;
}

function get_teamfight_npchp_multipler()
{
	if (!check_teamfight_groupattack_setting()) return 1;
	global $gameotherinfo;
	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
	if ($c==1) return 1;
	if ($c==2) return 1.5;
	if ($c==3) return 2.25;
	if ($c==4) return 3;
	if ($c==5) return 4;
	return 1;
}

function get_max_teammate_num()
{
	global $gametype,$gameotherinfo;
	if ($gametype!=2) return 100;
	list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
	return $c;
}

	
function send_gametypenews()
{
	global $gamenum,$gamestate,$lastupdate,$starttime,$winmode,$winner,$arealist,$areanum,$areatime,$areawarn,$validnum,$alivenum,$deathnum,$afktime,$optime,$weather,$hack,$combonum,$gamevars,$gametype,$gameotherinfo,$is_solo;
	if ($gametype==1)
	{
		list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
		addnews($starttime,'solostart',$a,$b,$c);
	}
	else  if ($gametype==2)
	{
		list($a,$b,$c,$d,$e,$f,$g) = explode(',',$gameotherinfo);
		addnews($starttime,'teamfightstart',$d);
	}
}

function update_gametype()
{
	global $now,$db,$tablepre;
	global $gamenum,$gamestate,$lastupdate,$starttime,$winmode,$winner,$arealist,$areanum,$areatime,$areawarn,$validnum,$alivenum,$deathnum,$afktime,$optime,$weather,$hack,$combonum,$gamevars,$gametype,$gameotherinfo,$is_solo;
	
	global $solo_wday;
	
	list($sec,$min,$hour,$day,$month,$year,$wday,$yday,$isdst) = localtime($starttime);
	
	if ($is_solo==1 && get_solo_game($t1,$t2))
	{
		$gametype=$t1;
		$gameotherinfo=$t2;
	}
	else  
	{
		$gametype=0;
		$gameotherinfo='';
		//clear_solo_list();	//清空未实际执行的solo局
	}
}

?>
