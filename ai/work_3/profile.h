#ifndef __PROFILE_H__
#define __PROFILE_H__

#include "common.h"
#include "item.h"
#include "armor.h"
#include "weapon.h"
#include "controller.h"
#include "shopitem.h"
#include "radar.h"
#include "food.h"

extern int gamemode;

namespace profile		//bot基本情况
{
	extern weapon wep;
	extern armory db,dh,da,df,art;
	extern item itm[15];
	extern int mhp,hp,msp,sp,rage,money,club,mss,ss,skillpoint,att,def,pls,lvl,pose,tactic,wp,wk,wc,wg,wd,wf,areanum,mysNo,nowtime,defall;
	extern string injury, log, def_key;
	extern int pakused, used[15], shopmode;
	
	void update();
	double estimatedmg(npc enemy);
	double checkdanger(npc enemy);
	int checkplssafety(int pls);
	int checkexist(string name);
	int checkinpak(string name);
	armory getarmor(string kind);
	int getskill(string kind);
	int itemexist(string name);
	int do_routine_works();
}
#endif
