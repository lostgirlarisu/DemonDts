#ifndef __MAPITEM_H__
#define __MAPITEM_H__

#include "common.h"
#include "item.h"
#include "weapon.h"

extern int ST_WEP_LIM;		//开局武器可接受的最大期望探索步数

namespace mapitemctl			//地图野生物品
{
	extern int mapitemcnt[60];
	extern int idall, areanum, allitemnum;
	extern map<string,int> itemid, mapitem[60], randitem;
	extern vector< pair<item,int> > itemlist[60], randitemlist;
	
	int getitemid(string name);
	void updatemap();
	pair<double,int> calcdifficulty(string itmname);
	double getstartwepdmg(int skill);
}
#endif
