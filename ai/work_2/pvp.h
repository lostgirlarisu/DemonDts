#ifndef __PVP_H__
#define __PVP_H__

#include "common.h"
#include "controller.h"

namespace pvpctl
{
	struct player
	{
		string name;
		weapon wep;
		int death, pls, mhp, skill, defall, isteam, lastvtime;	//lastvtime为最后一次确定此人位置的时间
		int cankill, danger;					//我是否可以击杀他/他是否可以击杀我
	};
	
	extern player pc[1010];
	extern int all, lastcheck;
	
	void init();
	void playersetrevival(int which);
	void playersetdeath(int which);
	void updatepcpls(int which, int pls, int t);
	int querypcpls(int sNo);
	void addnewplayer(int which, string name);
	int routine();
	void update();
	int gettarget();
}

#endif