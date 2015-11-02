<?php

if(!defined('IN_GAME')) {
	exit('Access Denied');
}

function check_evo($who,$mono)
{
	global $db,$money,$tablepre,$wep,$wepk,$wepe,$weps,$wepsk,$itm1,$itmk1,$itme1,$itms1,$itmsk1,$itm2,$itmk2,$itme2,$itms2,$itmsk2,$itm3,$itmk3,$itme3,$itms3,$itmsk3,$itm4,$itmk4,$itme4,$itms4,$itmsk4,$itm5,$itmk5,$itme5,$itms5,$itmsk5,$itm6,$itmk6,$itme6,$itms6,$itmsk6,$wepexp;
	global $wf;
	$mention=array(
		'骨刀【狛牙】'=>'武器进化需要能产生火焰的某件物品。在商厦也许能找到？',
		'飞龙刀【双火】'=>'此次进化需要战胜一定数量的敌人。',
		'飞龙刀【双炎】'=>'持续不断的使用这武器攻击，也许能激发出其隐藏的力量。当然，钱也是能解决一部分问题的。',
		'飞龙刀【双红莲】'=>'用强大的刀剑或是消耗资金来加铸都能得到更强的武器。',
		'飞龙刀【银】'=>'银色的太阳不留情的烧尽其他参展者。',
		'飞龙刀【焰二重】'=>'全息幻象已不是你的对手。证明这一点吧。',
		'血蛭'=>'它正蠢蠢欲动，垂涎着那些徘徊在世不愿离去的灵魂。',
		'死灵之蛭'=>'它还需要时间，需要锤炼，以及最重要的，成长。',
		'灵魂收割者'=>'它可以经过淬炼后重生，也可以迅速靠吞食而膨胀，这取决于你的耐心，或是境遇？',
		'蚀骨的邪灵'=>'让你的孩子健康的成长需要什么……哈，当然是钱了……',
		'咒缚的邪灵'=>'暴食的代价渐渐显露了出来，这是危机还是契机，就取决于你的灵力了。',
		'缠绕的邪灵'=>'它正一步步走向完美，你需要做的只是让它继续成长……以及杀戮。',
		'gem1' =>'宝石上刻着一行小字：“取索敌时自伤之血，以磨砺意志。”',
		'gem2'=>'宝石上的小字发生了变化：“浸泡英灵之血，求索伟大意蕴。”',
		'gem3'=>'宝石上的小字又发生了变化：“汲取神性之魂，升格本我之阶。”',
		'gem4'=>'宝石上的小字不再变化，定格成了六个字：置死地而后生。”',
		'双枪-黄金猎犬'=>'似乎献祭给它的灵魂还不足够。',
		'太刀-月下美人'=>'似乎献祭给它的灵魂还不足够。',
	);
	if ($mono!=$wep) {
		return -1;
	}
	$flag=0;
	if ($mono=='骨刀【狛牙】'){
		for($j=1;$j<=6;$j++){
			if (${'itm'.$j}=='打火机'){
				$flag=$j;
				break;
			}
		}
		if ($flag==0) return $mention[$mono];
		$wep=$after='飞龙刀【双火】';$wepe='50';$weps='50';$wepsk='Ou';
		${'itm'.$flag} = ${'itmk'.$flag} = ${'itmsk'.$flag} = '';
		${'itme'.$flag} = ${'itms'.$flag} = 0;
		$wepexp=0;	
	}elseif($mono=='飞龙刀【双火】'){
		if ($wepexp<8) return $mention[$mono]. '当前武器经验值为：' . $wepexp;
		$flag=1;
		$wep=$after='飞龙刀【双炎】';$wepe='180';$weps='220';$wepsk='Ou';
		$wepexp=0;
	}elseif($mono=='飞龙刀【双炎】'){
		if ($wepexp<110) return $mention[$mono]. '当前武器经验值为：' . $wepexp;
		$m=(150-$wepexp)*80;
		if ($m<0) $m=0;
		if ($money<$m) return $mention[$mono];
		$flag=1;
		$money-=$m;
		$wep=$after='飞龙刀【双红莲】';$wepe='180';$weps='120';$wepsk='Oru';
		$wepexp=0;
	}elseif($mono=='飞龙刀【双红莲】'){
		for($j=1;$j<=6;$j++){
			if ((${'itmk'.$j}=='WK')&&(${'itme'.$j}>=450)){
				$flag=$j;
				break;
			}
		}
		if ($flag){
			$wep=$after='飞龙刀【焰二重】';$wepe='860';$weps='150';$wepsk='Ouf';
			${'itm'.$flag} = ${'itmk'.$flag} = ${'itmsk'.$flag} = '';
			${'itme'.$flag} = ${'itms'.$flag} = 0;
			$wepexp=0;
			$rr="<span class=\"yellow\">{$mono}</span>进化成了<span class=\"yellow\">{$after}</span>。";
			addnews ( $now, 'evowep',$who, $mono, $after );
			return $rr;
		}
		if ($money<7500) return $mention[$mono];
		$flag=7;
		$wep=$after='飞龙刀【银】';$wepe='630';$weps='20';$wepsk='Orf';
		$money-=7500;
		$wepexp=0;
	}elseif($mono=='飞龙刀【银】'){
		if ($wepexp<3) return $mention[$mono]. '当前武器经验值为：' . $wepexp;
		$flag=1;
		$wep=$after='飞龙刀【椿】';$wepe='7000';$weps='∞';$wepsk='Znruf';
		$wepexp=0;
	}elseif($mono=='飞龙刀【焰二重】'){
		if ($wepexp<5) return $mention[$mono] . '当前武器经验值为：' . $wepexp;
		if ($money<6500) return $mention[$mono];
		$flag=1;
		$money-=6500;
		$wep=$after='飞龙刀【八重樱】';$wepe='3000';$weps='3000';$wepsk='duifk';
		$wepexp=0;
	}
//吸血鬼武器
	if ($mono=='血蛭'){
		$gflag=false;$cflag=false;
		for($j=1;$j<=6;$j++){
			if (${'itm'.$j}=='幽灵'){
				$gflag=true;$g=$j;
			}
			if (${'itm'.$j}=='怨灵'){
				$cflag=true;$c=$j;
			}
			if(($gflag)&&($cflag)){
				$flag=1;
				break;
			}
		}
		if (($flag==0)&&($wepexp<1)) return $mention[$mono];
		if(($gflag)&&($cflag)){
			${'itm'.$g} = ${'itmk'.$g} = ${'itmsk'.$g} = '';
			${'itme'.$g} = ${'itms'.$g} = 0;
			${'itm'.$c} = ${'itmk'.$c} = ${'itmsk'.$c} = '';
			${'itme'.$c} = ${'itms'.$c} = 0;	
		}
		if($wepexp>=1){
			$flag=1;
		}
		$wep=$after='死灵之蛭';$wepe='104';$weps='∞';$wepsk='O=|';
		$wepexp=0;
	}elseif($mono=='死灵之蛭'){
		if ($wepe<190) return $mention[$mono];
		$flag=1;
		$wep=$after='灵魂收割者';$wepsk='O=ui|';
	}elseif($mono=='灵魂收割者'){
		$wflag=false;$sflag=false;
		for($j=1;$j<=6;$j++){
			if ((${'itmk'.$j}=='WF')&&(${'itme'.$j}<=$wepe)){
				$wflag=true;$w=$j;
				break;
			}elseif ((${'itmk'.$j}=='WF')&&(${'itme'.$j}>$wepe)){
				$sflag=true;$w=$j;
				break;
			}
		}
		if ($wflag){
			$wep=$after='蚀骨的邪灵';$wepe=round(${'itme'.$w}-30);$wepsk='O=dH|';
			${'itm'.$w} = ${'itmk'.$w} = ${'itmsk'.$w} = '';
			${'itme'.$w} = ${'itms'.$w} = 0;
			$flag=1;
		}elseif($sflag){
			$wep=$after='咒缚的邪灵';$wepe=${'itme'.$w};$wepsk='O=dH';
			${'itm'.$w} = ${'itmk'.$w} = ${'itmsk'.$w} = '';
			${'itme'.$w} = ${'itms'.$w} = 0;
			$flag=1;
		}else{
			return $mention[$mono];
		}
	}elseif($mono=='蚀骨的邪灵'){
		if ($money<8500) return $mention[$mono];
		$flag=1;
		$wep=$after='缠绕的邪灵';$wepsk='O=rH|';
		$money-=8500;
	}elseif($mono=='咒缚的邪灵'){
		if ($wf<=$wepe) return $mention[$mono];
		$flag=1;
		$d=round(($wf-$wepe)*2);
		$wep=$after='缠绕的邪灵';$wepe=$d;$wepsk='O=rH|';
	}elseif($mono=='缠绕的邪灵'){
		if ($wepe<970) return $mention[$mono];
		$flag=1;
		$wep=$after='永咎的邪灵';$wepe='450';$weps='∞';$wepsk='=|rdn';
	}
//杀戮巫女之器
	if($mono=='双枪-黄金猎犬'){
			$gflag=false;
			for($j=1;$j<=6;$j++){
				if (${'itm'.$j}=='祭神之镜'){
					$gflag=true;$g=$j;
				}
				if($gflag){
					$flag=1;
					break;
				}
			}
			if (($flag==0)&&($wepexp<12)) return $mention[$mono]. '目前献祭的灵魂总量为：' . $wepexp;
			if($gflag){
				${'itm'.$g} = ${'itmk'.$g} = ${'itmsk'.$g} = '';
				${'itme'.$g} = ${'itms'.$g} = 0;
			}
			if($wepexp>=12){
				$flag=1;
			}
			$d=round($wepe*4);
			$wep=$after='神仪【黄金猎犬】';$wepe=$d;$weps='1000';$wepsk='dfrN|nt';
			$wepexp=0;
	}
	if($mono=='太刀-月下美人'){
			$gflag=false;
			for($j=1;$j<=6;$j++){
				if (${'itm'.$j}=='祭神之镜'){
					$gflag=true;$g=$j;
				}
				if($gflag){
					$flag=1;
					break;
				}
			}
			if (($flag==0)&&($wepexp<12)) return $mention[$mono]. '目前献祭的灵魂总量为：' . $wepexp;
			if($gflag){
				${'itm'.$g} = ${'itmk'.$g} = ${'itmsk'.$g} = '';
				${'itme'.$g} = ${'itms'.$g} = 0;
			}
			if($wepexp>=12){
				$flag=1;
			}
			$d=round($wepe*4);
			$wep=$after='神仪【月下美人】';$wepe=$d;$weps='1000';$wepsk='Lk=N|nt';
			$wepexp=0;
	}
	
//神秘系武器进化路线	
	global $club;
	include_once GAME_ROOT . './include/state.func.php';
	if($mono=='☆青金石法杖☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★青金石黎扎路★';$wepk='WF';$wepe='496';$weps='∞';$wepsk='rkiO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★青金石黎扎路★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英灵之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□青金石拉帕萨□';$wepk='WF';$wepe='19214';$weps='∞';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□青金石拉帕萨□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■青金石费阿■';$wepk='WF';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■青金石费阿■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜上灵＞';$wepk='WF';$wepe='240000';$weps='∞';$wepsk='nrkBt';
		$wepexp=0;	
		$club=53;
	}
	
	if($mono=='☆翠榴石战刃☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★翠榴石笛孟德★';$wepk='WK';$wepe='1056';$weps='1215';$wepsk='rpNO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★翠榴石笛孟德★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英雄之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□翠榴石拓洛尔□';$wepk='WK';$wepe='59729';$weps='42410';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□翠榴石拓洛尔□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■翠榴石艾雅■';$wepk='WK';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■翠榴石艾雅■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜时刃＞';$wepk='WK';$wepe='480000';$weps='∞';$wepsk='nrdkt';
		$wepexp=0;	
		$club=53;
	}
	
	if($mono=='☆琥珀石重锤☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★琥珀石欧贝尔★';$wepk='WP';$wepe='1079';$weps='1801';$wepsk='reNO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★琥珀石欧贝尔★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英雄之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□琥珀石安泊雅□';$wepk='WP';$wepe='65821';$weps='48082';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□琥珀石安泊雅□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■琥珀石尼萨■';$wepk='WP';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■琥珀石尼萨■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜船桨＞';$wepk='WP';$wepe='480000';$weps='∞';$wepsk='nfkBt';
		$wepexp=0;	
		$club=53;
	}
	
	if($mono=='☆红宝石投枪☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★红宝石洛铂尼★';$wepk='WC';$wepe='1179';$weps='∞';$wepsk='rdO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★红宝石洛铂尼★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英雄之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□红宝石瑞拉安□';$wepk='WC';$wepe='88016';$weps='∞';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□红宝石瑞拉安□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■红宝石费洛■';$wepk='WC';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■红宝石费洛■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜棘枪＞';$wepk='WC';$wepe='620000';$weps='∞';$wepsk='nrdBt';
		$wepexp=0;	
		$club=53;
	}
	
	if($mono=='☆黑曜石灵弹☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★黑曜石沃裴德★';$wepk='WD';$wepe='1520';$weps='∞';$wepsk='ndO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★黑曜石沃裴德★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英雄之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□黑曜石欧第斯□';$wepk='WD';$wepe='102030';$weps='∞';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□黑曜石欧第斯□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■黑曜石拉泊■';$wepk='WD';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■黑曜石拉泊■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜厄环＞';$wepk='WD';$wepe='880000';$weps='∞';$wepsk='nrdBt';
		$wepexp=0;	
		$club=53;
	}
	
	if($mono=='☆猫眼石火铳☆'){
		if ($wepexp<8888) return $mention['gem1'] . '目前献祭的鲜血总量为：' . $wepexp;
		$flag=1;
		$wep=$after='★猫眼石凯艾尔★';$wepk='WJ';$wepe='750';$weps='∞';$wepsk='nNfO';
		$wepexp=0;
		$club=50;
	}elseif($mono=='★猫眼石凯艾尔★'){
		if ($wepexp<1) return $mention['gem2'] . '目前吸取的英雄之魂数量为：' . $wepexp;
		if ($club!=50) return $mention['gem2'] . '你并不是下位之灵，无法为武器灌注灵魂！';
		$flag=1;
		$wep=$after='□猫眼石卡托思□';$wepk='WJ';$wepe='9869';$weps='2000';$wepsk='nrdBO';
		$club=51;
		$wepexp=0;
	}elseif($mono=='□猫眼石卡托思□'){
		if ($wepexp<1) return $mention['gem3'] . '目前吸取的武神之魂数量为：' . $wepexp;
		if ($club!=51) return $mention['gem3'] . '你并不是中位之灵，无法为武器注入神性！';
		$flag=1;
		$wep=$after='■猫眼石索汀■';$wepk='WG';$wepe='1';$weps='1';$wepsk='OXV';
		$wepexp=0;	
		$club=52;
	}elseif($mono=='■猫眼石索汀■'){
		if ($wepexp<1) return $mention['gem4'] . '目前献祭的灵魂总量为：' . $wepexp;
		if ($club!=52) return $mention['gem4'] . '你并不是上位之灵，无法为武器献祭灵魂！';
		$flag=1;
		$wep=$after='＜夜母＞';$wepk='WJ';$wepe='120000';$weps='2000';$wepsk='nrfBt';
		$wepexp=0;	
		$club=53;
	}
	
	if ($flag>0){
		$rr="<span class=\"yellow\">{$mono}</span>进化成了<span class=\"yellow\">{$after}</span>。";
		addnews ( $now, 'evowep',$who, $mono, $after );
		return $rr;
	}
	return -1;
}

?>
