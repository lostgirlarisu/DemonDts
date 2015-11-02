#ifndef __NPC_H__
#define __NPC_H__

#include "common.h"
#include "item.h"
#include "weapon.h"

struct npc
{
	string name;
	int type, mhp, skill, pls, att, def, money;
	vector<item> itm;
	weapon wep;
};

namespace npcinfoctl			//NPC相关，包括NPC威胁、NPC掉落等
{
	extern vector<npc> npclist[50], randnpclist[100];
	extern map< pair<int,int>,npc > appeared[50];
	extern map< string,vector<string> > npcitem;
	extern map< pair<int,string> ,npc> npcname;
	extern map< pair<int,int>,int> npcwhere;
	extern int isdeath[100][400], npcnumcnt[50], npcdeathnumcnt[50];
	
	void init();
	npc meetnpc(int pls, int type, int sNo, string name);
	vector<string> queryitem(string s);
	vector<npc> getnpclist(int pls);
	int querynpcplace(int type, int sNo);
	void npcsetdeath(int type, int sNo, int pls);
	double getnpcwepdmg(int skill);
}
#endif 
