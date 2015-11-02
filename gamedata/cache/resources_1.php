<?php

/*Game resources*/

require config('chat',$gamecfg);

//■ 空手武器 ■
$nowep = '拳头';

//■ 无防具 ■
$noarb = '内衣';
//■ 无道具 ■
$noitm = '--';
//■ 无限耐久度 ■
$nosta = '∞';
//■ 无属性 ■
$nospk = '--';
//■ 多种类武器 ■
$mltwk = '泛用兵器';
//■ 多重属性 ■
//$mltspk = '多重属性';


//游戏状态描述
$gstate = Array(0 => '<font color="grey">已结束</font>',10 => '即将开始',20 => '开放激活',30 => '人数已满',40=> '<font color="yellow">连斗中</font>',50=>'<font color="red">死斗中</font>',60=>'<font color="red">紧急状态！</font>');
$gwin = Array(0 => '程序故障', 1 => '全部死亡',2 => '最后幸存',3 => '锁定解除',4 => '无人参加',5 => '核爆全灭',6 => 'GM中止',7=>'幻境解离',8=>'幻境重构',9=>'黄鸡风暴',10=>'幻境重启',11=>'L.O.O.P');
$week = Array('日','一','二','三','四','五','六');
$clubinfo = Array(
	0=>'无',
	1=>'街头霸王',
	2=>'见敌必斩',
	3=>'灌篮高手',
	4=>'狙击鹰眼',
	5=>'拆弹专家',
	6=>'宛如疾风',
	7=>'锡安成员',
	8=>'黑衣组织',
	9=>'超能力者',
	10=>'高速成长',
	11=>'富家子弟',
	12=>'全能骑士',
	13=>'根性兄贵',
	14=>'健美兄贵',
	15=>'<span class="L5">L5状态</span>',
	16=>'全能骑士',
	17=>'走路萌物',
	18=>'天赋异禀',
	19=>'踏雪无痕',
	20=>'宝石骑士',
	21=>'恐怖份子',
	22=>'钢铁意志',
	23=>'铁拳无敌',
	24=>'亡灵骑士',
	25=>'怪物猎人',
	26=>'谈笑风生',
	27=>'迷之称号',
	28=>'零之使魔',
	49=>'奥秘学者',
	50=>'下位之灵',
	51=>'中位之灵',
	52=>'上位之灵',
	53=>'伟大秘仪',
	70=>'偶像大师',
	97=>'铁血战士',
	98=>'换装迷宫',
	99=>'亡灵骑士'
	);
$wthinfo = Array('晴天','大晴','多云','小雨','暴雨','台风','雷雨','下雪','起雾','浓雾','<span class="yellow">瘴气</span>','<span class="red">龙卷风</span>','<span class="clan">暴风雪</span>','<span class="blue">冰雹</span>','<span class="linen">离子暴</span>','<span class="green">辐射尘</span>','<span class="purple">臭氧洞</span>');
$sexinfo = Array(0=> '未定', 'm' => '男生', 'f' => '女生');
$hpinfo = Array('并无大碍','伤痕累累','生命危险','已经死亡');
$spinfo = Array('精力充沛','略有疲惫','精疲力尽','已经死亡');
$rageinfo = Array('平静','愤怒','暴怒','已经死亡');
$wepeinfo = Array('不值一提','略有威胁','威力可观','无敌神器');
$poseinfo = Array('通常','作战姿态','强袭姿态','探物姿态','偷袭姿态','治疗姿态','神闪姿态');
$tacinfo = Array('通常','','重视防御','重视反击','重视躲避');
$typeinfo = Array(
	0=>'学生',
	1=>'组织头目',
	2=>'监察员',
	3=>'不思议',//原本打算活动用，暂时放置
	//3=>'',//已经没用的类别
	//4=>'拟似意识',
	//5=>'杏仁豆腐',
	//6=>'黑幕',
	//7=>'幻影执行官',
	//8=>'管理员',//已经没用的类别
	//9=>'？？？？',
	//10=>'',//特殊BOSS【未名存在】
	11=>'管束外人员',//杀戮巫女
	12=>'深渊之民',
	13=>'管束外人员',//亚莉丝
	14=>'具象投影',
	//15=>'',//已被静流占用，不可修改
	16=>'GHost9',
	17=>'深渊灾厄',
	//20=>'英雄',
	//21=>'武神',
	//22=>'天神',
	//24=>'游动萌物',
	25=>'佣兵',
	//30=>'代码聚合体',
	//31=>'代码聚合体',
	//32=>'代码聚合体',
	//33=>'管理员',//英雄模式的BOSS
	40=>'卷入事件的无辜人员',//诊所众
	41=>'巫女',//巫女众
	42=>'班主任',//任林田
	43=>'研究员',
	//44=>'非作战人员',
	//45=>'电波幽灵',
	//87=>'夜种',
	//88=>'■■',
	89=>'士兵',
	90=>'克隆士兵',//量产无聊
	91=>'野生动物'
	);//最后一项不需要带逗号
	
$xiconlist = Array(
	//特定玩家才可以选择的专属头像
	'玩家ID' => Array(
			0 => '你的頭像數字，注意男女問題',
	),
	'亚莉丝' => Array(
			0 => '255',
	),
);
	
$stateinfo = Array
(
	0 => '正常存活',
	1 => '睡眠状态',
	2 => '治疗状态',
	3 => '静养状态',
	5 => '最后幸存',
	6 => '解除禁区',
	10 => '莫名身亡',
	11 => '禁区停留',
	12 => '毒发身亡',
	13 => '意外死亡',
	14 => '入侵失败', 
	15 => '黑幕抹杀', 
	16 => '黑幕抹杀', 
	17 => '遭遇天灾',
	18 => '烧伤不治', 
	19 => '失血过多',
	20 => '玩家杀害', 
	21 => '玩家杀害', 
	22 => '玩家杀害', 
	23 => '玩家杀害', 
	24 => '玩家杀害', 
	25 => '玩家杀害', 
	26 => '误食毒物', 
	27 => '误触陷阱', 
	28 => '死亡笔记',
	29 => '玩家杀害', 
	30 => '误触机关', 
	31 => 'L5病发', 
	32 => '挂机受罚', 
	33 => '天降软妹，无福消受', 
	34 => '溶剂作用', 
	35 => '救济',
	36 => '惨遭腰斩', 
	37 => '身首异处', 
	38 => '业火灼烧', 
	39 => '武器反噬', 
	40 => '御柱坠落', 
	41 => '武器反噬', 
	42 => '自杀袭击', 
	43 => '自爆身亡', 
	44 => '改装失误', 
	45 => '核弹袭击',
	46 => '鱼弹袭击',
	47 => '诅咒反噬',
	48 => '脱离战场',
);

$infinfo = Array('b' => '<span class="red">胸</span>', 'h' => '<span class="red">头</span>', 'a' => '<span class="red">腕</span>', 'f' => '<span class="red">足</span>', 'p' => '<span class="purple">毒</span>', 'u' => '<span class="red">烧</span>', 'i' => '<span class="clan">冻</span>', 'e' => '<span class="yellow">麻</span>','w' => '<span class="grey">乱</span>','P'=>'<span class="purple">猛</span>','B'=>'<span class="L5">裂</span>','S'=>'<span class="sienna">石</span>');
$attinfo = Array('N' => '徒手殴打', 'P' => '殴打','K' => '斩刺', 'G' => '射击', 'C' => '投掷', 'D' => '设置引信伏击', 'F' => '释放灵力攻击', 'J' => '狙击');
$skillinfo = Array('N' => 'wp', 'P' => 'wp', 'K' => 'wk', 'G' => 'wg', 'C' => 'wc', 'D' => 'wd', 'F'=> 'wf', 'J'=> 'wg');
//$rangeinfo = Array('N' => 'S', 'P' => 'S', 'K' => 'S', 'G' => 'M', 'C' => 'M', 'D' => 'L', 'F'=> 'M'); #各种攻击方式的射程，移动到combatcfg.php
$restinfo = Array('通常','睡眠','治疗','静养');
$noiseinfo = Array(
	'G' => '枪声',
	'J'=> '枪声',
	'D' => '爆炸声',
	'F'=>'灵气',
	'缩写歌名'=>'这是一个很长很长的歌名，不过至少需要3个字符才能识别！',
	'abs'=>'这就是最短的歌名的一个例子',
	'Crow Song'=>'Crow Song',
	'Alicemagic'=>'Alicemagic',
	'恋歌'=>'恋歌',
	'鸡肉之歌'=>'鸡肉之歌',
	'里海之誓'=>'里海之誓',
	'奇迹再现'=>'奇迹再现',
	);
$exdmgname = Array('p' => '毒性攻击', 'u' => '火焰燃烧', 'i'=>'冻气缠绕', 'd'=>'爆炸','e'=>'电击','w'=>'音波攻击','f' => '<span class="yellow">炽热之焰</span>','k' => '<span class="clan">凝结之息</span>');
$exdmginf = Array('h' => '<span class="red">头部受伤</span>', 'b' => '<span class="red">胸部受伤</span>', 'a'=> '<span class="red">腕部受伤</span>', 'f'=> '<span class="red">足部受伤</span>', 'p'=> '<span class="purple">中毒</span>','P'=> '<span class="purple">中猛毒</span>', 'u'=> '<span class="red">烧伤</span>', 'i'=> '<span class="blue">冻结</span>', 'e'=> '<span class="yellow">身体麻痹</span>', 'w'=> '<span class="grey">混乱</span>','S'=>'<span class="sienna">身体石化</span>');
$infwords = Array('h' => '<span class="red">头部受伤</span>', 'b' => '<span class="red">胸部受伤</span>', 'a'=> '<span class="red">腕部受伤</span>', 'f'=> '<span class="red">足部受伤</span>', 'p'=> '<span class="purple">毒发</span>','P'=> '<span class="purple">猛毒发作</span>', 'B'=> '<span class="red">裂伤发作</span>','u'=> '<span class="red">烧伤发作</span>', 'i'=> '<span class="blue">冻结影响</span>', 'e'=> '<span class="yellow">身体麻痹</span>', 'w'=> '<span class="grey">混乱</span>','S'=>'<span class="sienna">身体石化</span>');
$chatinfo = Array(0 => '全员', 1 => '队伍', 2 => '剧情', 3 => '遗言', 4 => '公告', 5 => '系统');

$iteminfo = Array(//注意顺序，AB必须在A的前面，以此类推
	'Ag' => '同志饰物',
	'Al' => '热恋饰物',
	'A'  => '饰物',
	'B' => '电池',
	'C' => '药剂',
	'Ca' => '药剂',
	'Ce' => '药剂',
	'Ci' => '药剂',
	'Cp' => '药剂',
	'Cu' => '药剂',
	'Cw' => '药剂',
	'DN' => '内衣',#内衣
	'DB' => '身体装备',
	'DH' => '头部装备',
	'DA' => '手臂装备',
	'DF' => '腿部装备',
	'EE' => '电脑设备',
	'EW' => '天气控制',
	'ER' => '探测仪器',
	'HH' => '生命恢复',
	'HS' => '体力恢复',
	'HB' => '命体恢复',
	'HM' => '歌魂增加',
	'HT' => '歌魂恢复',
	'GBr' => '机枪弹药',
	'GBi' => '气体弹药',
	'GBh' => '重型弹药',
	'GBe' => '能源弹药',
	'GB' => '手枪弹药',	
	'GP' => '枪械附件',	
	'GSa' => '瞄具',	
	'GSb' => '枪体',	
	'GSe' => '枪管',	
	'GSm' => '弹匣',
	'GSt' => '扳机',	
	'GS' => '枪械组件',		
	'GT' => '精密仪器',	
	'GEM' => '魔法宝石',
	'gbox' => '魔石匣',
	'jew' => '宝石匣',
	'M'=> '强化药物',
	'm' => '金钱道具',
	'N' => '无',	
	'PM' => '歌魂增加',
	'PT' => '歌魂恢复',
	'PH' => '生命恢复',
	'PS' => '体力恢复',
	'PB' => '命体恢复',
	'p' => '礼物',
	'ygo' => '卡包',
	//'R' => '探测仪器',	
	'ss' => '歌词卡片',
	'T' => '陷阱',
	'V'=> '技能书籍',
	'WN' => '空手',#空手
	'WGK' => '枪刃',
	'WCF' => '灵弹',
	'WKF' => '烈刃',
	'WKP' => '暴斩',
	'WCP' => '巨力',
	'WDG' => '重炮',
	'WJ' => '重型枪械',
	'WP' => '钝器',
	'WG' => '远程兵器',
	'WK' => '锐器',
	'WC' => '投掷兵器',
	'WD' => '爆炸物',
	'WF' => '灵力兵器',	
	'WQ' =>'？？？？',
	'XX' =>'杀意已决',
	'XY' =>'杀意未决',
	'X'=> '合成专用',
	'Y' => '特殊',
	'Z' => '特殊',#不可合并
	);
$itemspkinfo = Array(
	'A' => '全系防御',
	'a' => '属性防御',
	'B' => '伤害抹消',
	'b' => '属性抹消',
	'C' => '防投',
	'c' => '重击辅助',
	'D' => '防爆',
	'd' => '爆炸',
	'E' => '绝缘',
	'e' => '电击',	
	'F' => '防符',
	'f' => '灼焰',
	'G' => '防弹',
	'g' => '同志',
	'H' => 'HP制御',
	'h' => '伤害制御',//废弃
	'I' => '防冻',
	'i' => '冻气',
	'J' => '超量素材', //中国玩家没素质
	'j' => '多重',
	'K' => '防斩',
	'k' => '冰华',
	'L' => '致残',
	'l' => '热恋',
	'M' => '陷阱探测',
	'm' => '陷阱迎击',
	'N' => '冲击',
	'n' => '贯穿',
	'O' => '进化',
	'o' => '一发',
	'P' => '防殴',
	'p' => '带毒',
	'q' => '防毒',
	'R' => '混沌伤害',
	'r' => '连击',
	'S' => '消音',
	's' => '调整',
	'T' => '兼容',
	't' => '复位',
	'U' => '防火',
	'u' => '火焰',
	'V' => '诅咒',
	'v' => '灵魂绑定',
	'W' => '隔音',
	'w' => '音波',
	'X' => '直死', //NPC专用
	'x' => '奇迹',
	'Z' => '菁英',
	'z' => '天然',
	'=' => '吸血',
	'|' => '成长',
	'-' => '精神抽取',
	'*' => '灵魂抽取',
	'+' => '技能抽取',
	'^' => '背包',
	);



$shops = Array(0,14,15);
$hospitals = Array(19,25);
$plsinfo = Array(
	0=>'分校',
	1=>'北海岸',
	2=>'北村住宅区',
	3=>'北村公所',
	4=>'邮电局',
	5=>'消防署',
	6=>'观音堂',
	7=>'清水池',
	8=>'白诘草神社',
	9=>'墓地',
	10=>'山丘地带',
	11=>'隧道',
	12=>'西村住宅区',
	13=>'废弃寺庙',
	14=>'废校',
	15=>'灵子研究中心',
	16=>'森林地带',
	17=>'剑塚湖',
	18=>'南村住宅区',
	19=>'诊所',
	20=>'灯塔',
	21=>'南海岸',
	22=>'深渊之口',
	23=>'战术核潜艇',
	24=>'广播塔',
	25=>'凉亭'
);//最后一项不需要带逗号
$xyinfo = Array(
	0=>'D-6',
	1=>'A-2',
	2=>'B-4',
	3=>'C-3',
	4=>'C-4',
	5=>'C-5',
	6=>'C-6',
	7=>'D-4',
	8=>'E-2',
	9=>'E-4',
	10=>'F-6',
	11=>'E-8',
	12=>'F-3',
	13=>'F-9',
	14=>'G-3',
	15=>'G-6',
	16=>'H-4',
	17=>'H-6',
	18=>'I-6',
	19=>'I-7',
	20=>'I-10',
	21=>'J-6',
	22=>'D-8',
	23=>'G-1',
	24=>'I-4',
	25=>'H-8',
);
$areainfo = Array
	(
	0=>"这里是禁区，如果不快点离开，项圈将会爆炸。<br>",
	1=>"向大海望去，只能看到四处巡逻的监视船只。<br>",
	2=>"以前有人在这里居住吧，如今已变成废墟。。<br>",
	3=>"这里是村子的中心吗？现在已经空无一人。<br>",
	4=>"空荡荡的邮局内没有任何东西。<br>",
	5=>"虽说这里是消防站，但是连消防车也没有。<br>",
	6=>"这里供奉着大大小小各种各样的佛像。一到晚上，令人毛骨悚然。<br>",
	7=>"池水十分清澈，也许可以直接饮用。<br>",
	8=>"到处都是人为种植的白诘草，非常茂盛的。<BR>在这里仰望天空的话，总觉得会被某种忧伤的思绪所感染。<br>",
	9=>"这里埋葬着很多被当做祭品杀死的怪兽。<br>不知道是怎样奇怪的召唤仪式呢……<br>",
	10=>"能够一览整个岛的高地。<BR>不过，如果站在这里，被其他人找到的可能性也很高。<br>",
	11=>"真黑暗。<BR>如果在这样的地方被袭击，相信会是非常危险。<br>",
	12=>"这里与其他的住宅街差不多，完全与废墟无异。<br>",
	13=>"完全荒废了的地方。佛像犹如断壁残垣。<br>",
	14=>"长长的坂道的尽头是一所学校。<BR>学校规模不小，但总给人寂寥无人之感。<br><span class=\"yellow\">从校内的自动售货机似乎能买到些什么。</span><br>",
	15=>"曝晒着的牌坊，感觉很凄惨。<br>这个临时建造的研究所，似乎是西边的那座神社的旧址。<br>",
	16=>"浓郁的树叶遮住了阳光，是容易被袭击的地方啊……<br>",
	17=>"这里与其说是湖不如说是沼泽。<br>仔细一看湖底竟然闪烁着斑斑驳驳的金属光辉，<BR>令人毛骨悚然的地方…<br>",
	18=>"与其他住宅区相比，这里的商店特别多。<BR>虽然如此，整个城市弥漫着一种莫名的悲伤的气氛……<br>",
	19=>"巨大的建筑物内，不知名的设备依然在运转。<br>根据残缺不全的说明，<span class=\"yellow\">也许这些机器可以为人治疗伤口和恢复精力？</span><br>",
	20=>"快要成为坚固的城寨了。<BR>地板上有大量的干的血痕。发生了什么？<br>",
	21=>"数艘轮船停靠着。<BR>是士兵和优胜者将要乘坐的船吗？<br>",
	22=>"喷涌着暴戾之气的深不见底的洞窟。<br>若是失足掉下去的话……<br>",
	23=>"有艘并不是本国标志的潜艇停靠在这个地方。<br>看起来并不是友善的存在，探索必须谨慎。<br>",
	24=>"全岛广播似乎由这座戒备森严的塔来支持。<br>那个巫女会在这里吗？<br>",
	25=>"飘来红茶的香气，宁静得让人舒心。<br>你通过竖在附近的告示牌得知这里是非管制地区，<br>允许你使用这里提供的简易医疗设备。<br>但是前提是不能惊扰那位管理员少女……<br>",
);



$dinfo = Array(
	10 => '不知道什么原因，你死去了。<br>这应该是一个BUG，请通知管理员。<br>',
	11 => '“滴滴滴——”<br>这是……手机闹铃的提示音？<br>“糟糕！还没确认这次的禁区情况——”<br>还没等你有所反应，死神一般的空间裂缝已经把你吞没了。<br>等待你的只有死亡……<br>',
	12 => '“呜……到此为止了吗……”<br>毒素造成的痛苦让你无法再坚持下去了。<br>你吐出嘴里最后一点深黑的血液，仰面倒了下去。<br>',
	13 => '“不好！”<br>也许在平时的你看来，这只是小菜一碟……<br>但对于此刻遍体鳞伤的你来说，<br>眼前的突发状况无异于压垮骆驼的最后一根稻草。<br>你不甘心地倒下了，再也没有起来。<br>',
	14 => '“也许咱应该断定你上网成瘾？”<br>这是……林无月的声音！<br>从哪里传来的？<br>她怎么会知道我试图入侵虚拟世界的控制系统？<br>还没等你反应过来，你就两眼一黑，什么都不知道了。<br>',
	15 => '“我很抱歉，不过这是林无月的意思。”<br>面前突然出现的，是一个陌生男子。<br>这人说些什么怪话呢？<br>你正要上前问个究竟，只见男子手中白光一闪，你的意识就此烟消云散。<br>',
	16 => '“我很抱歉，不过这是林无月的意思。”<br>面前突然出现的，是一个陌生男子。<br>这人说些什么怪话呢？<br>你正要上前问个究竟，只见男子手中白光一闪，你的意识就此烟消云散。<br>',
	17 => '“呜……到此为止了吗……”<br>身体已被冰雹砸得千疮百孔，伤痛让你无法再坚持下去了。<br>你脚下一软，向前栽倒，失去了意识。<br>',
	18 => '“呜……到此为止了吗……”<br>烧伤导致的伤痛让你无法再坚持下去了。<br>你脚下一软，向前栽倒，失去了意识。<br>',
	19 => '“呜……到此为止了吗……”<br>随着大量的血液不断的流出，你脚下一软，向前栽倒，失去了意识。<br>',
	20 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个挥舞双拳的身影，在你失神的瞳孔中逐渐淡去……<br>',
	21 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个紧握钝器的身影，在你失神的瞳孔中逐渐淡去……<br>',
	22 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个腰佩刀剑的身影，在你失神的瞳孔中逐渐淡去……<br>',
	23 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个扛着枪械的身影，在你失神的瞳孔中逐渐淡去……<br>',
	24 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个手持投掷武器的身影，在你失神的瞳孔中逐渐淡去……<br>',
	25 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个手持爆炸物的身影，在你失神的瞳孔中逐渐淡去……<br>',
	26 => '“这味道……不对！”<br>饥渴难耐的你才咬了一口手中的补给品，就觉得不对劲。<br>然而，你发现得太晚了……<br>剧毒在几秒钟之内就夺去了你的生命。<br>',
	27 => '“什么！这里竟然……”<br>没能留意到陷阱的你，只能眼睁睁看着轰然启动的陷阱无情地撕碎你的身躯。<br>“啊啊……这是……哪个混蛋……”<br>你的双眼被鲜血永远地掩盖了。<br>',
	28 => '你被很奇怪的事情夺去了生命。<br>也许这跟一个名叫夜什么月的人有一星半点的关系。<br>具体情况请参见游戏状况。<br>',
	29 => '一切……都结束了吧……<br>你无力地倒在地上，<br>眼睁睁地看着血液从致命的伤口喷涌而出。<br>一个攥着符札的身影，在你失神的瞳孔中逐渐淡去……<br>',
	30 => '好奇心果然杀死猫啊……<br>你勉强支起破碎的身躯，<br>看着那个你刚才按下的带按钮的小盒子无奈地笑着。<br>这真是残酷的恶作剧啊。<br>你的意识逐渐模糊了……<br>',
	31 => '注射器里的药液才打进一半，你就觉得身体有异样。<br>“脖子……好痒……”<br>你疯狂地抠着脖子上的淋巴腺，<br>很快就倒在动脉破裂而流出的血泊中……<br>',
	32 => '“就躲在这里，让那些人自相残杀去吧。”<br>你正打着自己的小算盘，却被一声怒喝打断了。<br>“来人，这里有个挂机党！”<br>你惊愕地看着不知从哪里冒出来的玩家们把你团团围住。<br>“浪费时间，快去死吧！”<br>之后的事情，就太猎奇了……<br>',
	33 => '“对不起、对不起！能让我迫降一下吗？”<br>勉强躲过弹幕的你，忽然听到头上传来这样焦急的道歉声。<br>少女的迫降……？莫非是指……<br>少女娇柔的话音让你放松了警惕。<br>还没等你反应过来，少女——以及她乘坐的、几十吨重的机体——便把你的整个世界压得粉碎……<br>',
	34 => '将手中的溶剂一饮而尽之后，你感到全身就像燃烧起来一样。<br>“没错，我需要的就是这种力量！”<br>然而，当你看到自己像奶油般融化了的手掌在地上扑腾的时候，你才发现这场豪赌押错了边。<br>“那么，你就燃烧殆尽吧。”在意识崩解前，传来了一个女声的叹息。<br>',
	35 => '在你失去意识之前，你仿佛听到了一个冰冷的声音。<br>“像你这样的Homo-Speculator……”<br>“……真是最差劲的个体了”<br>然后，你看着你的身体和意识在一道圣光中溶解了。<br>',
	36 => '你徒劳地想挣脱丝带的束缚，<br>但是从丝带上传来的巨大压力，简简单单地将你一分两半。<br>真是杂鱼一样的死法……<br>',
	37 => '你徒劳地想躲避丝带，<br>但是说时迟那时快，你发现你的头正在骨碌碌地往山脚下滚去。<br>真是杂鱼一样的死法……<br>',
	38 => '你成功地躲避了丝带，<br>没想到从丝带中竟然喷出了岩浆！<br>你的意识在烈火中消失了。<br>',
	39 => '“去死吧！就算你是权限[哔]也挡不住我这一击的！”<br>你狂笑着使出你自认为决定胜负的一击，噗通倒下的却是你自己。<br>“不，这不科学！”被武器背叛的你遁入了无尽的黑暗中。<br>',
	40 => '“看起来好像没什么反应嘛……那是！！”<br>天空中突然降下的巨大柱状物瞬间将你的世界吞没。<br>你眼前一黑，便失去了意识。<br>',
	41 => '“去死吧！就算你是权限[哔]也挡不住我这一击的！”<br>你的队友挥舞着手中的武器，狂妄的喊着。<br><br>“诶？ 这武器上写的是…… 直死……？”<br>发现事情不对的你冲上前去，试图阻止队友的举动，但已经太迟了。<br>队友手中的武器仿佛变成了黑洞，只一瞬间就把你们都吞没了。<br>',
	42 => '你暴风骤雨般的攻击将对方打得毫无还手之力，正当你欺身向前，准备了结对方的性命时，对方忽然振臂高呼：<br><span class="clan">“安拉胡阿克巴！”</span><br>你这才发现对方腰间绑着一排明晃晃的炸药，但他已猛地扑了上来。剧烈的爆炸一瞬间就夺去了你的生命。',
	43 => '你被对方暴风骤雨般的攻击打得毫无还手之力。对方欺身向前，准备了结你的性命。你不甘就这么死去，振臂高呼到：<br><span class="clan">“安拉胡阿克巴！”</span><br>对方这才发现你腰间绑着一排明晃晃的炸药。<br>你用尽最后的力气拉响了引线，猛地扑了上去。剧烈的爆炸一瞬间就夺去了你的生命。',
	44 => '<span class="yellow">“嗑哒……”</span><br>突然，你听见了一个细微的金属碰撞声音。<br><span class="clan">“糟糕…… 难道……”</span><br>还没反应过来，陷阱就在你手中爆炸了。<br>果然还是技术不过硬啊……<br>你无奈地哀叹着，永远地闭上了眼睛。',
	45 => '天空中仿佛有什么东西正在发出眩目的红光，整个大地都被照成了红色。<br>你疑惑地望向天空，不知道发生了什么。<br>只见远处的天空中，一颗明亮的星星，渐渐显露出来。<br>定睛看去，那其实不是星星，是飞行器一般的东西，正高速划破天空，身后留下清晰可见的尾迹。<br>高速飞行的东西已经很近了，圆锥形的脑袋在阳光下反射着耀眼的金光。<br>就在那一瞬间，整个岛屿便被千万倍亮于太阳的刺眼光芒所淹没了……<br>',
	46 => '鱼形的生物向你吐出一枚炸弹，你躲闪不及，被炸了个正着，但却毫发无损。<br>正当你想露出不屑的笑容嘲讽对方时，你听到了像是秒表归零的声音。<br>猛烈的爆炸从你身体的内部发出，你被无尽的烈焰吞噬了。<br>',
	47 => '你正挥动手中被诅咒的武器肆意杀戮着，享受着失败者灵魂的悲鸣。<br>一阵剧烈的痛苦毫无征兆的从你的心口传来，你不自觉的发出了一声凄厉的尖叫。<br>黑暗蒙上了你的双目，你却清晰的看到无数双鲜血淋漓的手向你伸来，你的意识在无尽的痛苦之中就此消散了……<br>',
);

$syschatinfo = Array(
	'hack' => Array(
		Array('r0','米可','哼，真有两下子……等下次禁区自动恢复好了。'),
		//Array('r0','红暮','大胆狂徒，再敢这样做小心我直接改你的RP值。'),
		//Array('r0','红暮','满是雪花的屏幕还真让人心烦。'),
		Array('r0','米可','禁区失效了诶。是冲着米可酱而来的吗，好好奉陪才行。'),
		Array('b0','米可','禁区被解除了？……'),
		//Array('b0','蓝凝','似乎可以从显示屏上看出弹幕来……'),
		Array('b0','米可','咦？不是吧！练习灵力武器会干扰到机器信号吗……'),
	),
	'rdown' => Array(
		Array('b0','蓝凝','刚才那声音是……难道？'),
		Array('b0','蓝凝','...Engage, Rage mode-魔法特工 苍蓝☆乱舞！'),
	),
	'bdown' => Array(
		Array('r0','红暮','你们对我妹妹做了什么！？'),
		Array('r0','红暮','凝酱……是嘛，看来有碍事的家伙出现了。'),
	),
	'areawarn20' => Array(
		Array('r0','米可','咳咳，那么下面播报禁区警告——'),
		Array('r0','米可','如果不提示，会有多少人阴沟里翻船呢？不过米可酱可没那么坏心眼啦……'),
		Array('r0','米可','广播系统好像有点失灵？谁都好，快来修一下吧。'),
		//Array('b0','蓝凝','这里是……警报。'),
		//Array('b0','蓝凝','NOW THE ALARM.'),
	),
	'areawarn40' => Array(
		Array('r0','米可','白热化！连斗阶段真是美丽啊。'),
		Array('r0','米可','再过不久就要决出胜者了呢。'),
		Array('r0','米可','似乎还留着几个重要地点，真遗憾。要不要找几个巫女去围堵一下呢？'),
		//Array('r0','红暮','那边的职人，对就是你们几个，没死的话过来帮忙把这个陷阱挪走。'),
		Array('b0','米可','诶诶！……差点忘报了……'),
		Array('b0','米可','啊～真无聊，小幸完全不联络人家……呃，在、在播报啊，现在是警报时间——'),
		//Array('r1b0','蓝凝','那家伙撤退了，我得加油……'),
	),
	'areaadd20' => Array(
		Array('r0','米可','听啊，那丧钟为谁而鸣……噗哈哈哈，人家不想念这么中二的演讲稿啦！'),
		Array('r0','米可','现在增加禁区。'),
		Array('r0','米可','各位好，这里是人见人爱的米可酱，我们先来看看本期的禁区死亡名单——'),
		//Array('r0b0','红暮','就这几个人么……凝酱，你怎么看？'),
		Array('b0','米可','禁区时间到！'),
		Array('b0','米可','按下按钮就可以了吗？人家不是很懂这些机器诶……'),
		//Array('b0','蓝凝','GENOCIDE IS COMING.'),
	),
	'areaadd40' => Array(
		Array('r0','米可','现在死掉就没法投币了哦！大概是这样的游戏术语？'),
		//Array('r0','红暮','正在奋战的各位辛苦了，下面给你们派发便当。'),
		Array('r0','米可','各位好，现在战斗已经进入高潮，我们来看看这次的禁区死亡名单——'),
		//Array('r0b1','红暮','就用你们的血为凝酱报仇雪恨！'),
		//Array('b0','蓝凝','DEVILRY IS COMING.'),
		//Array('b0','蓝凝','按键无效啊，那么看我的720度回旋斜踢电器维修法！咣——'),
		//Array('r1b0','蓝凝','不会让那家伙失望的……'),
	),
	'end1' => Array(
		Array('r0b0','红暮','没有结果呢。凝酱，准备『下一轮』吧。'),
		Array('r0b0','红暮','预想之中。那么，凝酱，我们从这里撤离吧。'),
		Array('b0','蓝凝','这局真无聊啊。'),
		Array('r1b0','红暮','看来我死得毫无价值呢。凝酱，准备『下一轮』吧。'),
		Array('r1','红暮','看来我死得毫无价值呢。那么，你们就永远留在这里吧。'),
		Array('b1','蓝凝','……真遗憾呢。'),
	),
	'end2' => Array(
		Array('r0','红暮','决出胜负了。欢迎来到胜者的世界。'),
		Array('r0','红暮','竟然是你……不，并没有别的意思。祝贺你，获胜者。'),
		Array('b0','蓝凝','正统派的结局么……'),
		Array('r1','红暮','于是似乎碰到了不按牌理出牌的家伙呢。'),
		Array('b1','蓝凝','...'),
	),
	'end3' => Array(
		Array('r0','红暮','咦，我还活着？这不科学！'),
		Array('r1','红暮','哼……竟然遇到愚蠢到出手反抗的家伙了啊。'),
		Array('r1','红暮','你觉得你真的能逃脱么？'),
		Array('r1','红暮','那就让你达成所谓的HAPPY ENDING吧，呵呵呵……'),
		Array('r1b1','红暮','看来还真是心狠手辣的家伙，我会记住的。'),
		Array('r1b1','红暮','看来还真是心狠手辣的家伙，我会记住的。'),
		Array('b0','蓝凝','正统派的结局么……'),
		
		Array('b1','蓝凝','...'),
	),
);

$gametypedescription = Array(
	0 => '常规局',
	1 => '<span class="yellow">SOLO局</span>',
	2 => '<span class="L5">团战</span>',
);

/*Infomations*/
$_INFO = Array(
	'reg_success' => '注册成功！请返回首页登陆游戏。',
	'pass_success' => '修改密码成功。',
	'pass_failure' => '未修改密码。',
	'data_success' => '接受对帐户资料的修改。',
	'data_failure' => '未修改帐户资料。',
	'credits_conflicts' => '转换冲突，请勿同时转换不同类型积分。',
	'credits_success' => '积分转换成功。',
	'credits_failure' => '积分转换失败，请检查输入。',
	'credits_failure2' => '积分转换失败，请勿输入过大的数字。',
	'credits_failure3' => '积分转换失败，转换战斗力时请输入100的倍数。',
);

/*Error settings*/
$_ERROR = Array(
	'db_failure' => '数据库读写异常，请重试或通知管理员',
	'name_not_set' => '用户名不能为空，请检查用户名输入',
	'name_too_long' => '用户名过长，请检查用户名输入',
	'name_invalid' => '用户名含有非法字符，请检查用户名输入',
	'name_banned' => '用户名含有违禁用语，请检查用户名输入',
	'name_exists' => '用户名已被注册，请更换用户名',
	'pass_not_set' => '密码不能为空，请检查密码输入',
	'pass_not_match' => '两次输入的密码不一致，请检查密码输入',
	'pass_too_short' => '密码过短，请检查密码输入',
	'pass_too_long' => '密码过长，请检查密码输入',
	'ip_banned' => '此IP已被封禁，请与管理员联系',
	'logged_in' => '用户已登录，请先退出登陆再注册',
	'user_not_exists' => '用户不存在，请检查用户名输入',
	
	'no_login' => '用户未登陆，请从首页登录后再试',
	'login_check' => '用户信息验证失败，请清空缓存后再试',
	'login_time' => '登录间隔时间过长，请重新登录后再试',
	'login_info' => '用户信息不正确，请清空缓存和Cookie后进入游戏',
	'player_limit' => '本局游戏参加人数已达上限，无法进入，请下局再来',
	'wrong_pw' => '用户名或密码错误，请检查输入',
	'player_exist' => '角色已经存在，请不要重复激活',
	'no_start' => '游戏尚未开始，请稍后再试',
	'valid_stop' => '本游戏已经停止激活，无法进入，请下局再来',
	'user_ban' => '此账号禁止进入游戏，请与管理员联系',
	'scripter_ban' => '如果你看到我，说明你的帐号已经被封禁了，请与管理员联系',
	'no_admin' => '你不是管理员，不能使用此功能',
	'ip_limit' => '本局此IP激活人数已满，请下局再来',
	'no_power' => '你的管理权限不够，不能进行此操作',
	'wrong_adcmd' => '指令错误，请重新输入',
	//'invalid_name' => '用户名含有非法字符，请重新输入',
	//'banned_name' => '用户名含有违禁用语，请更改用户名',
	//'banned_ip' => '此IP已被封禁，请与管理员联系',
	//'long_name' => '用户名过长，请重新输入用户名'
);
//魔法宝石
$gemstateinfo=Array(
	1 => '<span class="yellow">未激活</span>',
	2 => '<span class="red">已激活</span>',
	3 => '<span class="grey">封印中</span>',
	4 => '<span class="clan">冷却中</span>',
	5 => '<span class="yellow">需主动激活</span>',
);
$gemlvlinfo=Array(
	0 => '<span class="red">Lv.0</span>',
	1 => '<span class="red">Lv.1</span>',
	2 => '<span class="red">Lv.2</span>',
	3 => '<span class="L5">Lv.max</span>',
);
$gemweponinfo=Array('☆青金石法杖☆','☆翠榴石战刃☆','☆琥珀石重锤☆','☆红宝石投枪☆','☆黑曜石灵弹☆','☆猫眼石火铳☆');
$gemweptwinfo=Array('★青金石黎扎路★','★翠榴石笛孟德★','★琥珀石欧贝尔★','★红宝石洛铂尼★','★黑曜石沃裴德★','★猫眼石凯艾尔★');
$gemweptrinfo=Array('□青金石拉帕萨□','□翠榴石拓洛尔□','□琥珀石安泊雅□','□红宝石瑞拉安□','□黑曜石欧第斯□','□猫眼石卡托思□');
$gemwepfoinfo=Array('■青金石费阿■','■翠榴石艾雅■','■琥珀石尼萨■','■红宝石费洛■','■黑曜石拉泊■','■猫眼石索汀■');
$gemwepinfo=Array('＜上灵＞','＜时刃＞','＜船桨＞','＜棘枪＞','＜厄环＞','＜夜母＞');
//枪械组件
$normal_gun=Array('普通枪械枪体','短节枪管','机械瞄具');
$sniper_gun=Array('重型枪械枪体','长节枪管','全息瞄具');
$knife_gun=Array('扁长式枪体','带刃滑膛枪管','反射式瞄具');
$cannon_gun=Array('重型枪炮枪体','巨炮枪管','内红点瞄具');
$gun_k=Array(
	'普通枪械枪体' => 'GSb',
	'重型枪械枪体' => 'GSb',
	'扁长式枪体' => 'GSb',
	'重型枪炮枪体' => 'GSb',
	'短节枪管' => 'GSe',
	'长节枪管' => 'GSe',
	'带刃滑膛枪管' => 'GSe',
	'巨炮枪管' => 'GSe',
	'机械瞄具' => 'GSa',
	'全息瞄具' => 'GSa',
	'反射式瞄具' => 'GSa',
	'内红点瞄具' => 'GSa',
	'普通弹匣' => 'GSm',
	'重型弹匣' => 'GSm',
	'气体弹仓' => 'GSm',
	'能源电池' => 'GSm',
	'单发式扳机' => 'GSt',
	'连发式扳机' => 'GSt',
);
$gun_body=Array(
	'WG' => '普通枪械枪体',
	'WJ' => '重型枪械枪体',
	'WGK' => '扁长式枪体',	
	'WDG' => '重型枪炮枪体',	
);
$gun_barrel=Array(
	'WG' => '短节枪管',
	'WJ' => '长节枪管',
	'WGK' => '带刃滑膛枪管',
	'WDG' => '巨炮枪管',
);
$gun_aiming=Array(
	'WG' => '机械瞄具',
	'WJ' => '全息瞄具',
	'WGK' => '反射式瞄具',
	'WDG' => '内红点瞄具',
);
$gun_trigger=Array(
	0 => '单发式扳机',
	1 => '连发式扳机',
);
$gun_ammo=Array(
	0 => '普通弹匣',
	1 => '重型弹匣',
	2 => '气体弹仓',
	3 => '能源电池',
);
$gun_other=Array(
	'S' => '消音器',
	'R' => '弹道校准器',
	'c' => '高速推进器',
	'd' => '榴弹发射器',
	'u' => '燃烧曳光弹改造装置',
	'i' => '液态速凝弹改造装置',
	'w' => '高频音爆弹改造装置',
	'e' => '电磁脉冲弹改造装置',
	'N' => 'PSR子弹排列蓄能装置',
	'r' => '选择火力快速射击装置',
	'n' => 'FMJ金属被甲弹改造装置',
	'' => '枪械增幅设备',
);
$gunnameA=Array('拂晓的','惨痛的','恐惧的','强大的','致命的','卑微的','下流的','光芒的','血色的','猛烈的','撕裂的','黄金的','洁白的','放肆的');
$gunnameB=Array('妹妹之','魔术师之','太阳之','倒吊者之','愚者之','风之','旅人之','勇者之','平民之','不可说之','仆从之','隐者之','猫之','友情之');
$gunnameC=Array('恒星','蔷薇','微光','吞噬者','不屈魂','暗流','北极','逆潮','胖次','劲弩','凛风','林中人','灯笼裤','服务器');

$ach_text = Array(
	0 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 10, 2=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'mixitem',
		'title' => Array(
			0=>'永恒世界的住人',
			1=>'幻想世界的往人',
			2=>'永恒的覆唱'
		),
		'prc' => '合成次数 %s 次',
		'need' => Array(
			0=>'合成物品【KEY系催泪弹】1次',
			1=>'合成物品【KEY系催泪弹】5次',
			2=>'合成物品【KEY系催泪弹】30次'
		),
		'reward' => Array(
			0=>'切糕10',
			1=>'积分200 切糕300 <span class="evergreen">称号 幻想</span>',
			2=>'积分700 切糕1000 <span class="evergreen">称号 流星</span>'
		)
	),
	1 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'other',
		'title' => Array(
			0=>'清水池之王'
		),
		'prc' => '最快速度： %s 秒',
		'need' => Array(
			0=>'在开局5分钟内合成物品【KEY系催泪弹】'
		),
		'reward' => Array(
			0=>'积分30 切糕16 <span class="evergreen">称号 KEY男</span>'
		)
	),
	2 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 10, 2=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'battle',
		'title' => Array(
			0=>'Run With Wolves',
			1=>'Day Game',
			2=>'Thousand Enemies'
		),
		'prc' => '合成次数： %s 次',
		'need' => Array(
			0=>'在自己的行动中击杀10名玩家',
			1=>'在自己的行动中击杀100名玩家',
			2=>'在自己的行动中击杀1000名玩家'
		),
		'reward' => Array(
			0=>'积分10',
			1=>'积分500 切糕200 <span class="evergreen">称号 二度打</span>',
			2=>'切糕2000 <span class="evergreen">称号 G.D.M</span>'
		)
	),
	3 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 10, 2=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'battle',
		'title' => Array(
			0=>'脚本小子',
			1=>'黑客',
			2=>'幻境解离者？'
		),
		'prc' => '击杀总数： %s 名',
		'need' => Array(
			0=>'击杀100名NPC',
			1=>'击杀1000名NPC',
			2=>'击杀10000名NPC'
		),
		'reward' => Array(
			0=>'切糕5',
			1=>'积分200 <span class="evergreen">称号 黑客</span>',
			2=>'积分500 切糕1500 <span class="evergreen">称号 最后一步</span>'
		)
	),
	4 => Array(
		'img'=> Array(0 => 'ach/N.gif', 1=> 'ach/D.gif', 999=>'ach/4_1.gif'),
		'state' => Array (0 => 0, 1=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'battle',
		'title' => Array(
			0=>'冒烟突火',
			1=>'红杀将军'
		),
		'prc' => '推倒次数： %s 次',
		'need' => Array(
			0=>'推倒红暮1次',
			1=>'推倒红暮9次'
		),
		'reward' => Array(
			0=>'积分50 切糕75',
			1=>'<span class="evergreen">称号 越红者</span>'
		)
	),
	5 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'自作孽不可活'
		),
		'prc' => '死亡次数： %s 次',
		'need' => Array(
			0=>'因触发了自己设置的陷阱而死亡1次'
		),
		'reward' => Array(
			0=>'积分10 切糕5'
		)
	),
	6 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'野生君的邂逅'
		),
		'prc' => '死亡次数： %s 次',
		'need' => Array(
			0=>'因触发了陷阱 ★全地图唯一的野生高伤阔剑地雷★ 而死亡1次'
		),
		'reward' => Array(
			0=>'积分10 切糕15'
		)
	),
	7 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'野生君的暗恋'
		),
		'prc' => '遭遇次数： %s 次',
		'need' => Array(
			0=>'遭遇陷阱 ★全地图唯一的野生高伤阔剑地雷★ 8次'
		),
		'reward' => Array(
			0=>'积分50 切糕120'
		)
	),
	8 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'这么死也值了！'
		),
		'prc' => '死亡次数： %s 次',
		'need' => Array(
			0=>'因触发陷阱 ★一发逆转神话★ 而死亡1次'
		),
		'reward' => Array(
			0=>'积分10 切糕10'
		)
	),
	9 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'对下雷者的“大打击”'
		),
		'prc' => '完成次数： %s 次',
		'need' => Array(
			0=>'在最大血量小于850时，中了陷阱 ☆★☆大打击☆★☆ 却未死亡1次'
		),
		'reward' => Array(
			0=>'积分30 切糕15'
		)
	),
	10 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'救命的迎击'
		),
		'prc' => '迎击成功次数： %s 次',
		'need' => Array(
			0=>'利用 陷阱迎击 属性成功迎击高伤陷阱（大于1000效）1次'
		),
		'reward' => Array(
			0=>'积分15 切糕15'
		)
	),
	11 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'真·地雷磁铁'
		),
		'prc' => '遭遇次数： %s 次',
		'need' => Array(
			0=>'遭遇400次陷阱'
		),
		'reward' => Array(
			0=>'积分100 切糕100'
		)
	),
	12 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 20),
		'type' => 'undefined',
		'title' => Array(
			0=>'DeathNoter'
		),
		'prc' => '完成次数： %s 次',
		'need' => Array(
			0=>'使用野生的 ■DeathNote■ 杀死一名玩家'
		),
		'reward' => Array(
			0=>'积分30 切糕30'
		)
	),
	13 => Array(
		'img'=> Array(0 => 'ach/N.gif', 1=> 'ach/D.gif', 999=>'ach/13_1.gif'),
		'state' => Array (0 => 0, 1=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'battle',
		'title' => Array(
			0=>'深度冻结',
			1=>'跨过彩虹'
		),
		'prc' => '推倒次数： %s 次',
		'need' => Array(
			0=>'推倒蓝凝1次',
			1=>'推倒蓝凝3次'
		),
		'reward' => Array(
			0=>'积分150 切糕250',
			1=>'<span class="evergreen">称号 跨过彩虹</span>'
		)
	),
	14 => Array(
		'img'=> Array(0 => 'achievement_not_done.gif', 1=> 'achievement_0.gif'),
		'state' => Array (0 => 0, 1=> 10, 2=> 10, 999=>20),  //0 未完成   10 进行中   20 完成
		'type' => 'itemmix',
		'title' => Array(
			0=>'篝火的引导',
			1=>'世界的树形图',
			2=>'地=月'
		),
		'prc' => '合成次数： %s 次',
		'need' => Array(
			0=>'合成物品【KEY系燃烧弹】1次',
			1=>'合成物品【KEY系燃烧弹】5次',
			2=>'合成物品【KEY系燃烧弹】30次'
		),
		'reward' => Array(
			0=>'切糕10',
			1=>'积分200 切糕300 <span class="evergreen">称号 树形图</span>',
			2=>'积分700 切糕1000 <span class="evergreen">称号 TERRA</span>'
		)
	),
);
?>