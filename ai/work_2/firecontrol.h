#ifndef __FIRECONTROL_H__
#define __FIRECONTROL_H__

#include "common.h"
#include "item.h"
#include "armor.h"
#include "weapon.h"
#include "profile.h"
#include "mapitem.h"
#include "mixitem.h"
#include "shopitem.h"

namespace firectl
{
	struct candwep		//候选武器属性
	{
		weapon wep;
		int type;		//武器的获得途径。 1: 地图 2: 合成 3: 商店 4: NPC
		double tcost;	//为了获得这件武器，期望的探索步数
		double mcost;	//为了获得这件武器，期望的金钱消耗
		double dmg;		//期望的伤害值（目标的防御由目前所处阶段决定 起手/中期/后期）
		double v[50];	//对各个地点产生的吸引力，地图或合成为探索吸引力，NPC为攻击吸引力
		candwep() {}
		candwep(weapon _wep, int _type, double _tcost, double _mcost, double _dmg);
	};

	extern double dmglowbnd, dmglowbnd2;	//初期武器和中期武器当前的伤害最低要求
	extern int usefulcnt[50];
	extern vector<candwep> cweplist;
	const int GWEPTIME=120;				//开始寻找中后期武器的最低游戏时间
	
	void getstartwep_map(int *c, int skill, double dmglimit);
	int cmp(const pair<double,int> &a, const pair<double,int> &b);
	void getstartwep_mix(int skill, double dmg_lb);
	void getfinwep_mix();
	int routine();
	void update();
}
#endif
