#ifndef __CONTROLLER_H__
#define __CONTROLLER_H__

#include "common.h"
#include "wget.h"
#include "profile.h"
#include "radar.h"
#include "npc.h"
#include "mixitem.h"
#include "pvp.h"

namespace controller
{
	const LL MoveCD=200, SearchCD=200, ItemCD=200;	//移动、探索、使用物品冷却时间，单位毫秒
	extern string botname, botpass;
	extern LL cooldownover;
	extern vector< pair<string,string> > cmd;
	
	void selectclub(int which);
	void search();
	void move(int pls);
	void useitem(int which);
	void combat(int flag);
	void corpse(string which);
	void itemget(int flag);
	void itemmerge(int t1, int t2);
	void itemadd();
	void itemswap(int t1);
	void itembuy(string name, int bnum);
	void itemmix(int mask);
	void itemoff(string which);
	void checkpvpinfo();
	void verify();
	void init();
}

#endif
