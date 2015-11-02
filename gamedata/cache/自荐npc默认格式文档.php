//NPC数据表
//例如一个武器的属性是爆炸+连击+带毒，那么就写成
//'wepsk' => 'dpr',
//武器可以带防具的抗性类属性，但是防具不可以带武器的主动类属性。写上去了虽然会显示，但是是没有实际效果的。
//例如不要在防具上加爆炸之类的属性。
//请不要选用灼焰、冰华、直死等属性，那是大BOSS专用的


'name' => '',//NPC的名字
'icon' => 0,//头像，没有提供头像的情况下默认0，有的情况下联系亚莉丝修改
'gd' => 'r',//性别：男-m；女-f；随机-r
'club' => 0,//觉醒力量：无-0；请查阅觉醒力量编号表
'mhp' => 0,//最大HP
'msp' => 0,//最大SP
'att' => 0,//角色攻击力
'def' => 0,//角色防御力
'pls' => 22,//初登场地区，自荐NPC一般用22深渊之口；随机为99；其他请查阅地区编号表
'lvl' => 0,//角色等级
'money' => 0,//角色所携带金钱
'rage' => 0,//登场怒气值，默认0
'pose' => 0,//姿势：通常-0；作战-1；强袭-2；探物-3；偷袭-4；治疗-5
'tactic' => 0,//策略：通常-0；重视防御-2；重视反击-3；重视躲避-4；不要问为什么没有1
'skill' => 0,//熟练度
'wep' => '',//武器名字
'wepk' => '',//武器种类，见后文的属性表，没带武器则空出来
'wepe' => 0,//武器效果值
'weps' => 0,//武器耐久值
'wepsk' => '',//武器附加属性
'arb' => '',//身体部位防具名字
'arbk' => 'DB',//种类固定DB，如果没穿的话把DB去掉
'arbe' => 0,//类推
'arbs' => 0,//类推
'arbsk' => '',//类推
'arh' => '',//头部防具名字
'arhk' => 'DH',//类推
'arhe' => 0,//类推
'arhs' => 0,//类推
'arhsk' => '',//类推
'arf' => '',//腿部防具名字
'arfk' => 'DF',//类推
'arfe' => 0,//类推
'arfs' => 0,//类推
'arfsk' => '',//类推
'ara' => '',//手（腕）部防具名字
'arak' => 'DA',//类推
'arae' => 0,//类推
'aras' => 0,//类推
'arask' => '',//类推
'art' => '',//饰品名字
'artk' => 'A',//饰品种类一般为A，歌词卡则为ss
'arte' => 0,//类推，但是效果不要超过20
'arts' => 0,//类推，但是耐久不要超过20
'artsk' => '',//类推
'itm1' => '',//携带道具的名字
'itmk1' => '',//种类
'itme1' => 0,//效果
'itms1' => 0,//耐久
'itmsk1' => '',//附加属性
'itm2' => '',	'itmk2' => '',	'itme2' => 0,	'itms2' => 0,	'itmsk2' => '',
'itm3' => '',	'itmk3' => '',	'itme3' => 0,	'itms3' => 0,	'itmsk3' => '',
'itm4' => '',	'itmk4' => '',	'itme4' => 0,	'itms4' => 0,	'itmsk4' => '',
'itm5' => '',	'itmk5' => '',	'itme5' => 0,	'itms5' => 0,	'itmsk5' => '',
'itm6' => '',	'itmk6' => '',	'itme6' => 0,	'itms6' => 0,	'itmsk6' => '',

//台词表
//注意：使用这个详细台词表时是不会自带对话双引号的，请自觉修正补全双引号
//也可以通过详细描述表达更多的内容，例如 0=>' 〇〇以难以想象的速度闪现到你的眼前，说道“你是逃不出我的手掌心的！” ',

'NPC的名字' => Array(
	0 => '遭遇时的台词',
	1 => '主动攻击时的台词',
	2 => '主动攻击时的台词2',
	3 => '损血过半时主动攻击的台词',
	4 => '损血过半时主动攻击的台词2',
	5 => '被攻击时的台词',
	6 => '同上，台词2',
	7 => '同上，损血过半（略',
	8 => '同上，台词2',
	9 => '战死时的台词',
	10 => '受到攻击且无法成功反击时的台词',
	11 => '攻击距离不足时的台词',
	12 => '攻击时出现暴击的台词',
	13 => '杀死对手时的台词',
	'color' => 'linen'//这个别改
),