<?php
if (! defined ( 'IN_GAME' )) {
	exit ( 'Access Denied' );
}

function getskills(&$arr)
{
	$arr=Array(
		//铁拳无敌称号技能
		1=>Array(
			//格档，花费[0]点技能，使武器效果按百分之[1]比率算入防御力
			"sk1"=>Array(	
				0=>Array(0,5),
				1=>Array(1,10),
				2=>Array(2,15),
				3=>Array(2,20),
				4=>Array(2,25),
				5=>Array(3,35),
				6=>Array(4,45)),
			//暴击，花费[0]点技能，增强百分之[1]的攻击力
			//有百分之[2]的几率在计算伤害时减少敌人百分之[3]的防御
			"sk2"=>Array(	
				0=>Array(0,1,2,2),
				1=>Array(2,2,4,6),
				2=>Array(3,4,6,10),
				3=>Array(3,6,8,15),
				4=>Array(4,9,10,20),
				5=>Array(4,12,20,20),
				6=>Array(5,15,25,25))),
		//见敌必斩称号技能
		2=>Array(
			//精准，花费[0]点技能，命中率和连击命中系数提升百分之[1]
			"sk1"=>Array(	
				0=>Array(0,1),
				1=>Array(2,2),
				2=>Array(3,4),
				3=>Array(4,6),
				4=>Array(5,8),
				5=>Array(6,10),
				6=>Array(6,12)),
			//保养，花费[0]点技能，武器损坏率降低百分之[1]
			"sk2"=>Array(
				0=>Array(0,2),
				1=>Array(1,6),
				2=>Array(2,13),
				3=>Array(2,20),
				4=>Array(3,30),
				5=>Array(3,40),
				6=>Array(3,50))),
		//灌篮高手称号技能
		3=>Array(
			//臂力，花费[0]点技能，反击率提升百分之[1]
			"sk1"=>Array(	
				0=>Array(0,10),
				1=>Array(1,20),
				2=>Array(2,40),
				3=>Array(2,60),
				4=>Array(2,80),
				5=>Array(2,100),
				6=>Array(3,120)),
			//潜能，花费[0]点技能，攻击力提高[1]，伤害浮动增加[2]
			"sk2"=>Array(	
				0=>Array(0,1,2),
				1=>Array(2,2,4),
				2=>Array(3,4,8),
				3=>Array(3,6,13),
				4=>Array(4,8,19),
				5=>Array(5,10,25),
				6=>Array(5,12,33))),
		//狙击鹰眼称号技能
		4=>Array(
			//静息，花费[0]点技能，命中率和连击命中系数提升百分之[1]
			"sk1"=>Array(
				0=>Array(0,1),
				1=>Array(2,2),
				2=>Array(3,4),
				3=>Array(4,6),
				4=>Array(5,8),
				5=>Array(6,10),
				6=>Array(7,12)),
			//重击，花费[0]点技能，防具损坏率提高[1]，
			//防具损坏效果变为[2]倍，有百分之[3]的几率造成额外百分之[4]的伤害
			"sk2"=>Array(
				0=>Array(0,10,0,2,5),
				1=>Array(2,20,1,5,10),
				2=>Array(3,40,1,10,15),
				3=>Array(4,60,2,15,20),
				4=>Array(5,80,2,20,20),
				5=>Array(5,100,2,35,25),
				6=>Array(7,125,3,50,30))),
		//拆弹专家称号技能
		5=>Array(
			//隐蔽，花费[0]点技能，隐蔽率提升百分之[1]，先攻率提升百分之[2]
			"sk1"=>Array(
				0=>Array(0,2,1),
				1=>Array(2,4,3),
				2=>Array(3,8,6),
				3=>Array(3,12,9),
				4=>Array(4,16,12),
				5=>Array(5,20,15),
				6=>Array(5,24,18)),
			//冷静，花费[0]点技能，陷阱回避率提升百分之[1]，陷阱再利用率提升百分之[2]
			"sk2"=>Array(
				0=>Array(0,2,2),
				1=>Array(1,7,5),
				2=>Array(2,14,10),
				3=>Array(2,21,15),
				4=>Array(3,28,20),
				5=>Array(3,35,25),
				6=>Array(4,44,30))),
		//宛如疾风称号技能
		6=>Array(
			//敏捷，花费[0]点技能，隐蔽率提升百分之[1]，先攻率提升百分之[2]，反击率提升百分之[3]
			"sk1"=>Array(
				0=>Array(0,1,0,1),
				1=>Array(2,3,1,5),
				2=>Array(2,6,2,10),
				3=>Array(3,9,3,15),
				4=>Array(3,12,5,20),
				5=>Array(4,16,8,25),
				6=>Array(5,20,13,30)),
			//冷静，花费[0]点技能，对方命中率下降[1]
			"sk2"=>Array(
				0=>Array(0,1),
				1=>Array(2,4),
				2=>Array(3,8),
				3=>Array(3,12),
				4=>Array(4,18),
				5=>Array(4,24),
				6=>Array(5,30))),
		//超能力者称号技能
		9=>Array(
			//灵力，花费[0]点技能，灵系体力消耗减少百分之[1]，敌人对灵系反击率降低[2]
			"sk1"=>Array(
				0=>Array(0,0,2),
				1=>Array(1,5,8),
				2=>Array(2,10,16),
				3=>Array(3,16,24),
				4=>Array(3,22,32),
				5=>Array(4,30,40),
				6=>Array(5,40,50))),
		//踏雪无痕称号技能
		19=>Array(
			//迅疾，花费[0]点技能，获得百分之[1]的二连击率
			"sk1"=>Array(
				0=>Array(0,5),
				1=>Array(1,6),
				2=>Array(2,7),
				3=>Array(2,8),
				4=>Array(2,9),
				5=>Array(2,10),
				6=>Array(3,12))),
		//宝石骑士称号技能
		20=>Array(
			//结晶，花费[0]点技能获得一颗宝石
			"sk1"=>Array(
				0=>Array(0),
				1=>Array(3),
				2=>Array(4),
				3=>Array(6),
				4=>Array(8),
				5=>Array(9),
				6=>Array(10))),
		28=>Array(
			//晶莹，花费[0]点技能，给敌人的最终伤害减少百分之[1]，自己受到的最终伤害减少百分之[2]，RP增长率下降百分之[3]
			"sk1"=>Array(
				0=>Array(0,0,0,0),
				1=>Array(1,50,10,10),
				2=>Array(2,75,20,20),
				3=>Array(3,88,30,30),
				4=>Array(4,94,40,40),
				5=>Array(5,97,50,50),
				6=>Array(6,98,60,60)),
			//剔透，花费[0]点技能，主动攻击时有[1]%概率对敌人造成额外的伤害
			"sk2"=>Array(
				0=>Array(0,0),
				1=>Array(1,2),
				2=>Array(2,5),
				3=>Array(3,8),
				4=>Array(4,12),
				5=>Array(5,16),
				6=>Array(6,20))),
	);
}

function getskills2(&$arr)
{
	getskills($t);
	$arr=Array(
		0=>0,
		1=>$t[1]["sk1"],
		2=>$t[1]["sk2"],
		3=>$t[2]["sk1"],
		4=>$t[2]["sk2"],
		7=>$t[3]["sk1"],
		8=>$t[3]["sk2"],
		5=>$t[4]["sk1"],
		6=>$t[4]["sk2"],
		9=>$t[5]["sk1"],
		10=>$t[5]["sk2"],
		11=>$t[6]["sk1"],
		12=>$t[6]["sk2"],
		13=>$t[9]["sk1"],
		14=>$t[19]["sk1"],
		15=>$t[20]["sk1"],
		16=>$t[28]["sk1"],
		17=>$t[28]["sk2"],
	);
}

function get_research_cost(&$arr)
{
	$arr=Array(0,1,2,2,1,2,1,1,1,2,2,2,3,1,12);
}

function gskill(&$arr,$club,$kind,$sk1lv)
{
	getskills($clskl);
	$sk2lv=$sk1lv;
	if ($club==9 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curles']=$clskl[$club]['sk1'][$sk1lv][1];
		$arr['curcnt']=$clskl[$club]['sk1'][$sk1lv][2];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newles']=$clskl[$club]['sk1'][$sk1lv+1][1];
			$arr['newcnt']=$clskl[$club]['sk1'][$sk1lv+1][2];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==1 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curdef']=$clskl[$club]['sk1'][$sk1lv][1];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newdef']=$clskl[$club]['sk1'][$sk1lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==1 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curatt']=$clskl[$club]['sk2'][$sk2lv][1];
		$arr['curpro']=$clskl[$club]['sk2'][$sk2lv][2];
		$arr['curdec']=$clskl[$club]['sk2'][$sk2lv][3];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newatt']=$clskl[$club]['sk2'][$sk2lv+1][1];
			$arr['newpro']=$clskl[$club]['sk2'][$sk2lv+1][2];
			$arr['newdec']=$clskl[$club]['sk2'][$sk2lv+1][3];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==2 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curacc']=$clskl[$club]['sk1'][$sk1lv][1];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newacc']=$clskl[$club]['sk1'][$sk1lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==2 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curpro']=$clskl[$club]['sk2'][$sk2lv][1];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newpro']=$clskl[$club]['sk2'][$sk2lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==3 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curpro']=$clskl[$club]['sk1'][$sk1lv][1];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newpro']=$clskl[$club]['sk1'][$sk1lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==3 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curatt']=$clskl[$club]['sk2'][$sk2lv][1];
		$arr['curfluc']=$clskl[$club]['sk2'][$sk2lv][2];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newatt']=$clskl[$club]['sk2'][$sk2lv+1][1];
			$arr['newfluc']=$clskl[$club]['sk2'][$sk2lv+1][2];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==4 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curacc']=$clskl[$club]['sk1'][$sk1lv][1];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newacc']=$clskl[$club]['sk1'][$sk1lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==4 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curpro']=$clskl[$club]['sk2'][$sk2lv][1];
		$arr['cureff']=$clskl[$club]['sk2'][$sk2lv][2];
		$arr['curpro2']=$clskl[$club]['sk2'][$sk2lv][3];
		$arr['curdmg']=$clskl[$club]['sk2'][$sk2lv][4];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newpro']=$clskl[$club]['sk2'][$sk2lv+1][1];
			$arr['neweff']=$clskl[$club]['sk2'][$sk2lv+1][2];
			$arr['newpro2']=$clskl[$club]['sk2'][$sk2lv+1][3];
			$arr['newdmg']=$clskl[$club]['sk2'][$sk2lv+1][4];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==5 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curhid']=$clskl[$club]['sk1'][$sk1lv][1];
		$arr['curact']=$clskl[$club]['sk1'][$sk1lv][2];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newhid']=$clskl[$club]['sk1'][$sk1lv+1][1];
			$arr['newact']=$clskl[$club]['sk1'][$sk1lv+1][2];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==5 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curmis']=$clskl[$club]['sk2'][$sk2lv][1];
		$arr['curpic']=$clskl[$club]['sk2'][$sk2lv][2];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newmis']=$clskl[$club]['sk2'][$sk2lv+1][1];
			$arr['newpic']=$clskl[$club]['sk2'][$sk2lv+1][2];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==6 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curhid']=$clskl[$club]['sk1'][$sk1lv][1];
		$arr['curact']=$clskl[$club]['sk1'][$sk1lv][2];
		$arr['curcnt']=$clskl[$club]['sk1'][$sk1lv][3];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newhid']=$clskl[$club]['sk1'][$sk1lv+1][1];
			$arr['newact']=$clskl[$club]['sk1'][$sk1lv+1][2];
			$arr['newcnt']=$clskl[$club]['sk1'][$sk1lv+1][3];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==6 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['curmis']=$clskl[$club]['sk2'][$sk2lv][1];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newmis']=$clskl[$club]['sk2'][$sk2lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==19 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['curpro']=$clskl[$club]['sk1'][$sk1lv][1];
		if ($sk1lv<6)
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newpro']=$clskl[$club]['sk1'][$sk1lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==20 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		if ($sk1lv<6)
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==28 && $kind==1)
	{
		$arr['lv']=$sk1lv;
		$arr['wdmgdown']=$clskl[$club]['sk1'][$sk1lv][1];
		$arr['dmgdown']=$clskl[$club]['sk1'][$sk1lv][2];
		$arr['rpdec']=100-$clskl[$club]['sk1'][$sk1lv][3];
		if ($sk1lv<6) 
		{
			$arr['nextlv']=$sk1lv+1; 
			$arr['cost']=$clskl[$club]['sk1'][$sk1lv+1][0];
			$arr['newwdmgdown']=$clskl[$club]['sk1'][$sk1lv+1][1];
			$arr['newdmgdown']=$clskl[$club]['sk1'][$sk1lv+1][2];
			$arr['newrpdec']=100-$clskl[$club]['sk1'][$sk1lv+1][3];
		}
		else  $arr['nextlv']=-1;
	}
	else  if ($club==28 && $kind==2)
	{
		$arr['lv']=$sk2lv;
		$arr['rpdmgr']=$clskl[$club]['sk2'][$sk2lv][1];
		if ($sk2lv<6) 
		{
			$arr['nextlv']=$sk2lv+1; 
			$arr['cost']=$clskl[$club]['sk2'][$sk2lv+1][0];
			$arr['newrpdmgr']=$clskl[$club]['sk2'][$sk2lv+1][1];
		}
		else  $arr['nextlv']=-1;
	}
}

function getclubavd(&$arr,$club)
{
	$arr['learn1']=0; $arr['learn2']=0;
	if ($club==1)
	{
		$arr['learn1']=1; $arr['learn2']=2;
	}
	else  if ($club==2)
	{
		$arr['learn1']=3; $arr['learn2']=4;
	}
	else  if ($club==3)
	{
		$arr['learn1']=7; $arr['learn2']=8;
	}
	else  if ($club==4)
	{
		$arr['learn1']=5; $arr['learn2']=6;
	}
	else  if ($club==5)
	{
		$arr['learn1']=9; $arr['learn2']=10;
	}
	else  if ($club==6)
	{
		$arr['learn1']=11; $arr['learn2']=12;
	}
	else  if ($club==9) $arr['learn1']=13;
	else  if ($club==19) $arr['learn1']=14;
	else  if ($club==20) $arr['learn1']=15;
	else  if ($club==28)	{
		$arr['learn1']=16;$arr['learn2']=17;
	}
}

function getck($x,&$c,&$k)
{
	$c1=Array(0,1,1,2,2,4,4,3,3,5,5,6,6,9,19,20,28,28);
	$k1=Array(0,1,2,1,2,1,2,1,2,1,2,1,2,1, 1, 1, 1, 2);
	$c=$c1[$x]; $k=$k1[$x];
}

function getlearnt(&$arr,$club,$skills)
{
	if ($club!=18 && $club!=98)	//天赋异禀和换装迷宫称号（供NPC）可以任意学习技能
		getclubavd($arr,$club);
	else 
	{
		$learn1=(int)(((int)($skills/100))/32);
		$learn2=((int)($skills/100))%32;
		$arr['learn1']=$learn1; $arr['learn2']=$learn2;
	}
}

function calcskills(&$arr)
{
	getskills($clskl); get_research_cost($rcost);
	global $club,$skills;
	$sk1lv=((int)($skills/10))%10;
	$sk2lv=$skills%10;
	getlearnt($arr,$club,$skills);
		
	if ($arr['learn1']) 
	{
		getck($arr['learn1'],$c,$k);
		gskill($arr['sk1'],$c,$k,$sk1lv);
	}
	if ($arr['learn2']) 
	{
		getck($arr['learn2'],$c,$k);
		gskill($arr['sk2'],$c,$k,$sk2lv);
	}

	if ($club==18)
	{
		for ($i=1; $i<=14; $i++)
		{
			if ($i!=$arr['learn1'] && $i!=$arr['learn2'])
			{
				$arr['rs'.$i]=0;
				$arr['rs'.$i.'cost']=$rcost[$i];
			}
			else  $arr['rs'.$i]=1;
		}
		$arr['learn']=2;
		if ($arr['learn1']) $arr['learn']--;
		if ($arr['learn2']) $arr['learn']--;
	}
}

function get_clubskill_random_gem($lvl)
{
	global $log;
	$log.='很抱歉，这个技能已经被废除了。<br>';
	return;
	//宝石骑士技能： 获取一颗随机宝石
	$gem=Array(
		0 => Array('黑色方块','白色方块','水晶方块',),
		1 => Array('红色方块','黄色方块','蓝色方块','绿色方块','金色方块','银色方块',),
		2 => Array('红宝石方块','蓝宝石方块','绿宝石方块',),
		);

	if ($lvl==1)
		$rate=Array(70,30,0);
	else if ($lvl==2)
		$rate=Array(40,50,10);
	else if ($lvl==3)
		$rate=Array(25,50,25);
	else if ($lvl==4)
		$rate=Array(20,40,40);
	else if ($lvl==5)
		$rate=Array(10,40,50);
	else if ($lvl==6)
		$rate=Array(0,40,60);
	
	$dice=rand(1,100);
	for ($i=0; $i<=2; $i++)
		if ($dice<=$rate[$i])
		{
			$which=rand(0,sizeof($gem[$i])-1);
			$itm=$gem[$i][$which];
			$log.="你获得了一颗<span class=\"yellow\">{$itm}</span>。<br>";
			include_once GAME_ROOT.'./include/game/itemmain.func.php';
			global $itm0,$itmk0,$itme0,$itms0,$itmsk0;
			$itm0=$itm; $itmk0='X'; $itme0=1; $itms0=1; $itmsk0='';
			itemfind();
			break;
		}
		else  $dice-=$rate[$i];
}

function upgradeclubskills($cmd)
{
	getskills($clskl);
	global $hp,$mhp,$att,$def,$inf,$skillpoint,$log,$club,$skills;
	if ($cmd=="clubbasic1")	//生命
	{
		if ($club==17 || $club==21)
		{
			$log.="你不能使用本技能。<br>";
			return;
		}
		
		if ($skillpoint<1)
		{
			$log.="技能点不足。<br>";
			return;
		}
		else
		{
			$skillpoint--;
			if ($club==14) 
			{
				$log.="消耗了<span class='lime'>1</span>点技能点，你的生命上限增加了<span class='yellow'>6</span>点。<br>";
				$hp+=6; $mhp+=6;
			}
			else 
			{
				$log.="消耗了<span class='lime'>1</span>点技能点，你的生命上限增加了<span class='yellow'>3</span>点。<br>";
				$hp+=3; $mhp+=3;
			}
		}
	}
	else  if ($cmd=="clubbasic2")	//攻防
	{
		if ($club==17 || $club==21)
		{
			$log.="你不能使用本技能。<br>";
			return;
		}
		
		if ($skillpoint<1)
		{
			$log.="技能点不足。<br>";
			return;
		}
		else
		{
			$skillpoint--;
			if ($club==14) 
			{
				$log.="消耗了<span class='lime'>1</span>点技能点，你的基础攻击增加了<span class='yellow'>8</span>点，基础防御增加了<span class='yellow'>10</span>点。<br>";
				$att+=8; $def+=10;
			}
			else 
			{
				$log.="消耗了<span class='lime'>1</span>点技能点，你的基础攻击增加了<span class='yellow'>4</span>点，基础防御增加了<span class='yellow'>6</span>点。<br>";
				$att+=4; $def+=6;
			}
		}
	}
	else  if ($cmd=="clubbasic3")	//治疗
	{
		if ($skillpoint<1)
		{
			$log.="技能点不足。<br>";
			return;
		}
		else
		{
			$flag=false;
			$skillpoint--;
			$log.="消耗了<span class='lime'>1</span>点技能点，<br>";
			if($club==17){
				$morex_inf=Array('b','h','a');
			}else{
				$morex_inf=Array('b','h','a','f','p','u','i','e','w','P','B','S');
			}
			foreach ($morex_inf as $value) {
			global $exdmginf;
				if(strpos ( $inf, $value ) !== false){
					$inf = str_replace ( $value, '', $inf );
					$log .= "你的{$exdmginf[$value]}状态解除了。<br>";
					$flag=true;
				}
			}
			if(!$flag){
				$log .= '但是什么也没发生。<br>';
			}
		}
	}
	else  if ($cmd=="voice")	//歌喉
	{
		global $mss;
		if ($club==17)
		{
			$log.="你不能使用本技能。<br>";
			return;
		}
		
		if ($skillpoint<1)
		{
			$log.="技能点不足。<br>";
			return;
		}
		else
		{
			$skillpoint--;
			$log.="消耗了<span class='lime'>1</span>点技能点，你的歌魂上限增加了<span class='yellow'>10</span>点。<br>";
			$mss+=10;
		}
	}
	else  if (strpos($cmd,'clubskill') === 0)
	{
		getlearnt($ac,$club,$skills);
		$sk1lv=((int)($skills/10))%10;
		$sk2lv=$skills%10;
		
		$which=0;
		$which=intval(substr($cmd,9,1),10);
		if (strlen($cmd)>=11) $which=$which*10+intval(substr($cmd,10,1),10);
		
		if ($which<1 || $which>32)
		{
			$log.="技能不合法。<br>";
			return;
		}
		if ($which!=$ac['learn1'] && $which!=$ac['learn2'])
		{
			$log.="你不能升级此技能。{$which}<br>";
			return;
		}
		if ($which==$ac['learn1'])
		{
			if ($sk1lv==6)
			{
				$log.="你已经升到了最高级。<br>";
				return;
			}
			getck($ac['learn1'],$c,$k);
			if ($skillpoint<$clskl[$c]['sk'.$k][$sk1lv+1][0])
			{
				$log.="技能点不足。<br>";
				return;
			}
			$skillpoint-=$clskl[$c]['sk'.$k][$sk1lv+1][0];
			$skills+=10;
			if ($which==15) get_clubskill_random_gem($sk1lv+1); else $log.="升级成功。<br>";
		}
		else
		{
			if ($ac['learn2']==0) 
			{
				$log.="你不能使用此技能。<br>";
				return;
			}
			if ($sk2lv==6)
			{
				$log.="你已经升到了最高级。<br>";
				return;
			}
			getck($ac['learn2'],$c,$k);
			if ($skillpoint<$clskl[$c]['sk'.$k][$sk2lv+1][0])
			{
				$log.="技能点不足。<br>";
				return;
			}
			$skillpoint-=$clskl[$c]['sk'.$k][$sk2lv+1][0];
			$skills++;
			if ($which==15) get_clubskill_random_gem($sk2lv+1); else $log.="升级成功。<br>";
		}
	}
	else  
	{
		if ($club!=18)
		{
			$log.="你不能研发技能。<br>";
			return;
		}
		
		$sk1lv=((int)($skills/10))%10;
		$sk2lv=$skills%10;
		$learn1=(int)(((int)($skills/100))/32);
		$learn2=((int)($skills/100))%32;
		if ($learn1 && $learn2)
		{
			$log.="你不能研发更多的技能了。<br>";
			return;
		}
		
		$which=0;
		$which=intval(substr($cmd,7,1),10);
		if (strlen($cmd)>=9) $which=$which*10+intval(substr($cmd,8,1),10);
		if ($which<1 || $which>14)	//天赋不能研发结晶技能，这是设定
		{
			$log.="技能不合法。<br>";
			return;
		}
		if ($which==$learn1 || $which==$learn2)
		{
			$log.="你已经研发过本技能了。<br>";
			return;
		}
		get_research_cost($rcost);
		if ($skillpoint<$rcost[$which])
		{
			$log.="技能点不足。<br>";
			return;
		}
		
		include_once GAME_ROOT.'./include/game/gametype.func.php';
		if (check_teamfight_groupattack_setting() && $which==14)
		{
			$log.='<span class="yellow">很抱歉，同地图小队集体作战模式下，踏雪无痕称号技能不可用，因此无法研发。</span><br>';
			return;
		}
		$skillpoint-=$rcost[$which];
		if (!$learn1) $skills+=3200*$which; else $skills+=100*$which;
	}
}

function get_clubskill_bonus_p($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2,&$att,&$def)
{
	//攻击防御加成系数
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$att=1; $def=1;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==2 && ${$prefix1.'wepk'}=="WP")	//铁拳无敌称号
		{
			$att*=(1+$clskl[2][${'a'.$i}][1]/100);
			if (rand(0,99)<$clskl[2][${'a'.$i}][2]) $def*=(1-$clskl[2][${'a'.$i}][3]/100);
		}
		if ($alearn['learn'.$i]==8 && ${$prefix1.'wepk'}=="WC")	//灌篮高手称号
		{
			$att*=(1+$clskl[8][${'a'.$i}][1]/100);
		}
		if ($alearn['learn'.$i]==6 && (${$prefix1.'wepk'}=="WG" || ${$prefix1.'wepk'}=="WJ"))	//狙击鹰眼称号
		{
			if (rand(0,99)<$clskl[6][${'a'.$i}][3]) $att*=(1+$clskl[6][${'a'.$i}][4]/100);
		}
	}
}

function get_clubskill_bonus($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2,&$att,&$def)
{
	//攻击防御力加成
	getskills2($clskl);
	global ${$prefix2.'wepk'}, ${$prefix2.'wepe'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$att=0; $def=0;
	for ($i=1; $i<=2; $i++)
	{
		if ($blearn['learn'.$i]==1 && ${$prefix2.'wepk'}=="WP")	//铁拳无敌称号
		{
			$dup=$clskl[1][${'b'.$i}][1]/100*${$prefix2.'wepe'};
			if ($dup>2000) $dup=2000;
			$def+=$dup;
		}
	}
}

function get_clubskill_bonus_hitrate($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//命中率系数
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	if ($bclub==19) $r/=1.2;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==3 && ${$prefix1.'wepk'}=="WK")	//见敌必斩称号
		{
			$r*=(1+$clskl[3][${'a'.$i}][1]/100);
		}
		if ($alearn['learn'.$i]==5 && (${$prefix1.'wepk'}=="WG" || ${$prefix1.'wepk'}=="WJ"))	//狙击鹰眼称号
		{
			$r*=(1+$clskl[5][${'a'.$i}][1]/100);
		}
		if ($blearn['learn'.$i]==12)						//宛如疾风称号
		{
			$r*=(1-$clskl[12][${'b'.$i}][1]/100);
		}
	}
	return $r;
}

function get_clubskill_bonus_imprate($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//武器损坏率系数
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==4 && ${$prefix1.'wepk'}=="WK")	//见敌必斩称号
		{
			$r*=(1-$clskl[4][${'a'.$i}][1]/100);
		}
	}
	return $r;
}

function get_clubskill_bonus_imfrate($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//防具损坏率系数
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==6 && (${$prefix1.'wepk'}=="WG" || ${$prefix1.'wepk'}=="WJ"))	//狙击鹰眼称号
		{
			$r*=(1+$clskl[6][${'a'.$i}][1]/100);
		}
	}
	return $r;
}

function get_clubskill_bonus_imftime($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//防具损坏效果系数
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==6 && (${$prefix1.'wepk'}=="WG" || ${$prefix1.'wepk'}=="WJ"))	//狙击鹰眼称号
		{
			$r+=$clskl[6][${'a'.$i}][2];
		}
	}
	return $r;
}

function get_clubskill_bonus_fluc($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//伤害浮动值
	getskills2($clskl);
	global ${$prefix1.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=0;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==8 && ${$prefix1.'wepk'}=="WC")	//灌篮高手称号
		{
			$r+=$clskl[8][${'a'.$i}][2];
		}
	}
	return $r;
}

function get_clubskill_bonus_counter($aclub,$askl,$prefix1,$bclub,$bskl,$prefix2)
{
	//反击率加成
	getskills2($clskl);
	global ${$prefix1.'wepk'}, ${$prefix2.'wepk'};
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	if ($aclub==19) $r*=1.15;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==7 && ${$prefix1.'wepk'}=="WC")	//灌篮高手称号
		{
			$r*=(1+$clskl[7][${'a'.$i}][1]/100);
		}
		if ($alearn['learn'.$i]==11)						//宛如疾风称号
		{
			$r*=(1+$clskl[11][${'a'.$i}][3]/100);
		}
		if ($blearn['learn'.$i]==13 && ${$prefix2.'wepk'}=="WF")	//超能力者称号
		{
			$r*=(1-$clskl[13][${'b'.$i}][2]/100);
		}
	}
	return $r;
}	

function get_clubskill_bonus_hide($clb,$skl)
{
	//躲避率加成
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=1;
	if ($clb==19) $r*=1.15;	
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==9) $r*=(1+$clskl[9][${'a'.$i}][1]/100);	//拆蛋专家称号
		if ($alearn['learn'.$i]==11) $r*=(1+$clskl[11][${'a'.$i}][1]/100);	//宛如疾风称号
	}
	return $r;
}

function get_clubskill_bonus_active($aclub,$askl,$bclub,$bskl)
{
	//先攻率加成
	getskills2($clskl);
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$r=1;
	if ($aclub==19) $r*=1.15;	
	if ($bclub==19) $r/=1.15;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==9)	//拆蛋专家称号
		{
			$r*=(1+$clskl[9][${'a'.$i}][2]/100);
		}
		if ($alearn['learn'.$i]==11)	//宛如疾风称号
		{
			$r*=(1+$clskl[11][${'a'.$i}][2]/100);
		}
		if ($blearn['learn'.$i]==9)	//拆蛋专家称号
		{
			$r/=(1+$clskl[9][${'b'.$i}][2]/100);
		}
		if ($blearn['learn'.$i]==11)	//宛如疾风称号
		{
			$r/=(1+$clskl[11][${'b'.$i}][2]/100);
		}
	}
	return $r;
}	

function get_clubskill_bonus_escrate($clb,$skl)
{
	//陷阱回避率加成
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=1;
	if ($clb==19) $r*=1.15;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==10) $r*=(1+$clskl[10][${'a'.$i}][1]/100);	//拆蛋专家称号
	}
	return $r;
}

function get_clubskill_bonus_reuse($clb,$skl)
{
	//陷阱再利用率加成
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=1;
	if ($clb==19) $r*=1.15;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==10) $r*=(1+$clskl[10][${'a'.$i}][2]/100);	//拆蛋专家称号
	}
	return $r;
}

function get_clubskill_bonus_spd($clb,$skl)
{
	//体力消耗减少
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=1;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==13) $r*=(1-$clskl[13][${'a'.$i}][1]/100);	//超能力者称号
	}
	return $r;
}

function get_clubskill_bonus_dblhit($clb,$skl,$t2=0)
{
	//二连击
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=0;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==14) $r=$clskl[14][${'a'.$i}][1];	//踏雪无痕称号
	}
	if ($t2) $r*=2;
	if (rand(0,99)<$r) return 1; else return 0;
}

function get_clubskill_bonus_ironwill_reduction($nowhp,$maxhp)	//钢铁意志称号伤害折扣
{
	return round((0.2+1.0*($maxhp-$nowhp)/$maxhp*0.8)*100);
}

function get_clubskill_bonus_dmg_val($club,$skl,$rp,$w_rp)
{
	//最终伤害增加值
	getskills2($clskl);
	getlearnt($learn,$club,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$rate = 0;
	for ($i=1; $i<=2; $i++)
	{
		if ($learn['learn'.$i]==17)	//攻击方有剔透技能，得到概率
		{
			$rate=$clskl[17][${'a'.$i}][1];
		}
	}
	$rate -= round($rp/20);
	if($rate < 0){$rate = 0;}
	$rpdmg = $w_rp - $rp;
	if($rpdmg > 0 && rand(0,99) < $rate){
		return $rpdmg;
	}
	return 0;
}

function get_clubskill_bonus_dmg_rate($aclub,$askl,$bclub,$bskl)
{
	//最终伤害加成/减成，a为攻击方，b为防御方
	getskills2($clskl);
	getlearnt($alearn,$aclub,$askl);
	getlearnt($blearn,$bclub,$bskl);
	$a1=((int)($askl/10))%10; $a2=$askl%10;
	$b1=((int)($bskl/10))%10; $b2=$bskl%10;
	$ar=100;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==16)	//攻击方有晶莹技能，伤害大幅下降
		{
			$ar-=$clskl[16][${'a'.$i}][1];
		}
	}
	$br=100;
	for ($i=1; $i<=2; $i++)
	{
		if ($blearn['learn'.$i]==16)	//防御方有晶莹技能，伤害下降
		{
			$br-=$clskl[16][${'b'.$i}][2];
		}
	}
	$r = round($ar*$br)/10000;
	return $r;
}

function get_clubskill_rp_dec($clb,$skl)
{
	//RP增长率下降
	getskills2($clskl);
	getlearnt($alearn,$clb,$skl);
	$a1=((int)($skl/10))%10; $a2=$skl%10;
	$r=0;
	for ($i=1; $i<=2; $i++)
	{
		if ($alearn['learn'.$i]==16) $r=$clskl[16][${'a'.$i}][3];	//晶莹剔透1
	}
	//echo $r;
	return $r;
}

?>
