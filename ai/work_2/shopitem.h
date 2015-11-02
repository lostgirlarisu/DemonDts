#ifndef __SHOPITEM_H__
#define __SHOPITEM_H__

#include "common.h"
#include "item.h"
#include "weapon.h"
#include "profile.h"
#include "slots.h"
#include "controller.h"

namespace shopitemctl			//商店物品
{
	extern int areanum, all;
	extern map<string,int> price;
	extern map<string,item> slist;
	extern pair<item,int> itmlist[1010];
	extern int marked[1010];
	
	void updateshop();
	int getprice(string name);
	void markinit();
	int getshopwepdmg(int money, int skill);
	int checkrequest(string s, int num);
	int request(string s, int num=1);
}
#endif