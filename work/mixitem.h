#ifndef __MIXITEM_H__
#define __MIXITEM_H__

#include "common.h"
#include "item.h"
#include "weapon.h"
#include "mapitem.h"
#include "shopitem.h"
#include "controller.h"
#include "profile.h"

namespace mixitemctl
{
	struct mixitem
	{
		vector<string> stuff;
		item mixresult;
	};
	
	extern int all, money[1010], neednpc[1010], marked[1010], gotpart[1010], covered[1010];
	extern double difficulty[1010];
	extern mixitem mix[1010];
	extern map<string,int> mixlist;
	extern int areanum;
	extern map<string,int> itemneeded[1010];
	extern int involved[15][1010];
	
	void init();
	pair<double, pair<int,int> > calcdifficulty(int i);
	void calcall();
	void init();
	void markinit();
	void update();
	void markwep(int *c, int which);
	double getstartwepdmg(int skill);
	int checkmix();
}

#endif
