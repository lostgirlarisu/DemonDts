 <?php
 //require_once './include/common.inc.php';
 function sing($sn){
	global $log,$msg,$now,$pls,$name,$nick,$plsinfo,$ss,$mss,$noiseinfo,$arte;
	global $db,$tablepre;
	global $att,$def;
	global $wep,$wepk,$weps,$wepes,$wepsk;
	global $mhp,$hp,$art,$artk,$arte,$arts,$artsk;//深渊版新歌
	global $club,$lvl;//偶像大师的唱歌消耗减免
	//$log.=$sn.'_'.$now.'_'.$pls.'_'.$name."<br>";
	//$r=$arte;
	//偶像大师的唱歌消耗减免
	if ($club==70){
		$r=floor($arte/2);
	}else{
		$r=$arte;
	}
	include_once GAME_ROOT.'./include/game/combat.func.php';
	
		if ($ss>=$r){
		$ss-=$r;
		$log.="消耗<span class=\"yellow\">{$r}</span>点歌魂，歌唱了<span class=\"yellow\">{$noiseinfo[$sn]}</span>。<br>";
	}else{
		$log.="需要<span class=\"yellow\">{$r}</span>歌魂才能唱这首歌！<br>";
		return;
	}
	
	if ($sn=="Alicemagic"){
		$log.="♪你說過在哭泣之後應該可以破涕而笑♪<br>
					♪我們的旅行　我不會忘♪<br>
					♪施展魔法　為了不再失去　我不會說再見♪<br>
					♪再次踏出腳步之時　將在某一天到來♪<br>";
					
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪你說過在哭泣之後應該可以破涕而笑♪')");
		
		//$result = $db->query("select * from {$tablepre}players where `pls`={$pls} and hp>0 and type=0");
		$db->query ( "UPDATE {$tablepre}players SET def=def+30 WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
		$def+=30;
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);
		return;
		
	}elseif ($sn=="Crow Song"){
			$log.="♪从这里找一条路♪<br>
					♪找到逃离的生路♪<br>
					♪奏响激烈的摇滚♪<br>
					♪盯紧遥远的彼方♪<br>
					♪在这个连呼吸都难以为继的都市中♪<br>";
					
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪从这里找一条路♪')");
		
		//$result = $db->query("select * from {$tablepre}players where pls='$pls' and hp>0 and type=0");
		$db->query ("UPDATE {$tablepre}players SET att=att+30 WHERE `pls`={$pls} AND hp>0 AND type=0");
		$att+=30;
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);
		return;
	
	
	}elseif ($sn=="恋歌"){
			$log.="♪la la la la♪<br>
					♪la la la la♪<br>
					♪la la la♪<br>
					♪la la la la la♪<br>
					♪la la la ... ...♪<br>";
					
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪♪la la la la♪♪...')");
		
		//$result = $db->query("select * from {$tablepre}players where pls='$pls' and hp>0 and type=0");
		$ss+=200;
		$mss=$ss;
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);
		return;
	
	
	
	}elseif ($sn=="鸡肉之歌"){
			$log.="♪翼失いながらも優しくて♪<br>
					♪今は静かに眠るこの手の中で♪<br>
					♪ありがとう　感謝の言葉♪<br>
					♪あなたは教えてくれたよ　鶏肉♪<br>";
					
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪♪la la la la♪♪...')");
		
		//$result = $db->query("select * from {$tablepre}players where pls='$pls' and hp>0 and type=0");
		if(rand(1,10)>=9){
			$db->query ( "UPDATE {$tablepre}players SET wep='鸡肉' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepk='WC' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET weps='55',wepe='55' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepsk='z' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
		}elseif(rand(1,10)>=5){
			$db->query ( "UPDATE {$tablepre}players SET wep='腐烂的鸡肉' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepk='WC' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET weps='∞',wepe='1' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepsk='Vv' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
		}else{
			$db->query ( "UPDATE {$tablepre}players SET wep='狂暴的腐烂的鸡肉' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepk='WC' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET weps='∞',wepe='1' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET wepsk='Vv' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arb='狂暴的腐烂的鸡肉',arh='腐烂的鸡肉',ara='腐烂的鸡肉',arf='腐烂的鸡肉',art='腐烂的鸡肉' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arbk='DB' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arhk='DH' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arak='DA' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arfk='DF' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET artk='A' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arbs=8192,arhs=8192,aras=8192,arfs=8192,arts=8192,arbe=1,arhe=1,arae=1,arfe=1,arte=1 WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
			$db->query ( "UPDATE {$tablepre}players SET arbsk='Vv',arhsk='Vv',arask='Vv',arfsk='Vv',artsk='Vv' WHERE `pls` ={$pls} AND hp>0 AND type=0 ");
		}
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);
		global $art,$arte,$artk,$arts,$artsk,$hp;
		if(rand(1,100)>92){
			$log.="<br><span class='red'>你的歌词卡忽然爆炸了！</span><br>";
			$art=$artk=$artsk='';$arte=$arts=0;
			$hp=1;
		}
		return;
	}elseif ($sn=="里海之誓"){
		global $rp;
			$log.="<span class='orange'>我在黑暗中祷告，<br>
					祈望不曾到来的黎明。<br>
					在血与泪的背后，<br>
					怨恨与愁苦将成泡影。<br>
					我是线这端的苦行者，彼端的殉道者。<br>
					我是里海中的最后一位圣贤。<br>
					为此，<br>
					我以此纸为约，在此立誓，<br>
					我将恪守先古的箴言。<br>
					即使遭遇背叛与忤逆，承担痛苦与责难，<br>
					不会被人所理解，亦不曾被人所铭记。<br>
					不追名逐利，<br>
					不违背本心。<br>
					我将承载世人无心之过，<br>
					替他们赎罪，直至流干我最后一滴罪人的血。<br>
					在此之前，我是尘埃。<br>
					今时之后，我是泥土。</span><br>";
		$rp=0;
		global $gemname,$gemstate;
		global $art,$arte,$artk,$arts,$artsk;
		if(($gemname=='白欧泊石')&&($gemstate==3)){
			$log.='<br>在你颂唱完这段誓言之后，指骨间的白欧泊石忽然剧烈的震动起来！<br><span class="clan">白欧泊石被激活了！</span><br>写有誓言的羊皮纸忽然化作尘埃随风消散了。<br>';
			$gemstate=2;
			$art=$artk=$artsk='';$arte=$arts=0;
		}else{
			$log.="<br>在你颂唱完这段誓言之后，写有誓言的羊皮纸忽然化作尘埃随风消散了。<br>";
			$art=$artk=$artsk='';$arte=$arts=0;
		}
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);
		return;
	}elseif ($sn=="奇迹再现"){
			$log.="♪新的风暴已经出现 怎么能够停滞不前♪<br>
					♪穿越时空 竭尽全力♪<br>
					♪我会来到你身边♪<br>
					♪微笑面对危险 梦想成真并不遥远♪<br>
					♪鼓起勇气 坚定向前♪<br>
					♪奇迹一定会出现♪<br>";
					
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪新的风暴已经出现 怎么能够停滞不前♪')");
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪穿越时空 竭尽全力♪')");
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪我会来到你身边♪')");
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪微笑面对危险 梦想成真并不遥远♪')");
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪鼓起勇气 坚定向前♪')");
		$db->query("INSERT INTO {$tablepre}chat (type,`time`,send,recv,msg) VALUES ('0','$now','$name','$plsinfo','♪奇迹一定会出现♪')");
		
		//$up=round($mhp*0.15);//最初设定是涨15%
		//$mhp+=$up;
		$mhp+=100;
		$hp=$mhp;
		//歌词卡几率消失
		$songdrop=rand(1,10);
		if($songdrop<=3){
			$art=$artk=$artsk='';
			$arte=$arts=0;
			$log.="歌词卡化作光消失了……";
		}
		addnoise($sn,'__',$now,$pls,0,0,$sn);
		addnews($now,'song',$nick.' '.$name,$plsinfo[$pls],$noiseinfo[$sn]);		
		return;
	}
	
	
//	if ($ss>=$r){
//		$ss-=$r;
//		$log.="消耗<span class=\"yellow\">{$r}</span>点歌魂，歌唱了<span class=\"yellow\">{$noiseinfo[$sn]}</span>。<br>";
//	}else{
//		$log.="需要<span class=\"yellow\">{$r}</span>歌魂才能唱这首歌！<br>";
//		return;
//	}

	
//	addnoise($sn,'__',$now,$pls,0,0,$sn);
//	addnews($now,'song',$name,$plsinfo[$pls],$noiseinfo[$sn]);
	return;
 }
 ?>