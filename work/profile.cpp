#include "profile.h"

int gamemode;

namespace profile		//bot基本情况
{
	weapon wep;
	armory db,dh,da,df,art;
	item itm[15];
	int mhp,hp,msp,sp,rage,money,club,mss,ss,skillpoint,att,def,pls,lvl,pose,tactic,wp,wk,wc,wg,wd,wf,areanum,mysNo,nowtime,defall;
	string injury, log, def_key;
	int pakused, used[15], shopmode;

	void update()
	{
		if (response.count("dead")) return;
		itm[7]=item("arb",""); itm[8]=item("arh",""); itm[9]=item("ara",""); 
		itm[10]=item("arf",""); itm[11]=item("art",""); itm[12]=item("wep",""); 
		wep=weapon(itm[12]); db=armory(itm[7]); dh=armory(itm[8]); 
		da=armory(itm[9]); df=armory(itm[10]); art=armory(itm[11]); 
		def_key=db.itm.skind+dh.itm.skind+da.itm.skind+df.itm.skind+art.itm.skind;
		rep(i,0,6) itm[i]=item("itm",toString(i));
		mhp=toInteger(response["mhp"]); hp=toInteger(response["hp"]); 
		msp=toInteger(response["msp"]); sp=toInteger(response["sp"]);
		rage=toInteger(response["rage"]); money=toInteger(response["money"]); 
		club=toInteger(response["club"]); mss=toInteger(response["mss"]); 
		ss=toInteger(response["ss"]); skillpoint=toInteger(response["skillpoint"]);
		att=toInteger(response["att"]); def=toInteger(response["def"]); 
		defall=int(db.itm.e+dh.itm.e+da.itm.e+df.itm.e)+def+1;
		pls=toInteger(response["pls"]); lvl=toInteger(response["lvl"]);
		pose=toInteger(response["pose"]); tactic=toInteger(response["tactic"]); 
		wp=toInteger(response["wp"]); wk=toInteger(response["wk"]);
		wc=toInteger(response["wc"]); wg=toInteger(response["wg"]);
		wd=toInteger(response["wd"]); wf=toInteger(response["wf"]);
		mysNo=toInteger(response["sNo"]); nowtime=toInteger(response["now"]);
		injury=response["inf"]; starttime=toInteger(response["starttime"]); 
		areanum=toInteger(response["areanum"]); log=response["log"];
		pakused=0; rep(i,1,6) if (itm[i].s>0) pakused++;
		memset(used,0,sizeof used);
	}
	
	double estimatedmg(npc enemy)	//估算bot打npc的伤害
	{
		if (wep.durable==0) return 0;
		int skill=0;
		if (wep.itm.kind=="WN" || wep.itm.kind=="WP") skill=wp;
		if (wep.itm.kind=="WK") skill=wk;
		if (wep.itm.kind=="WC") skill=wc;
		if (wep.itm.kind=="WG" || wep.itm.kind=="WJ") skill=wg;
		if (wep.itm.kind=="WD") skill=wd;
		if (wep.itm.kind=="WF") skill=wf;
		return wep.estimatedmg(skill,enemy.def);
	}
	
	double checkdanger(npc enemy)	//估算npc打bot的伤害
	{
		return enemy.wep.estimatedmg_real(enemy.skill,db.itm.e+dh.itm.e+da.itm.e+df.itm.e+def,enemy.att,def_key);
	}
	
	int checkplssafety(int pls)	//根据现有信息，检查一个地点是否安全
	{
		vector<npc> lis=npcinfoctl::getnpclist(pls);
		rept(it,lis)
			if (checkdanger(*it)>mhp*0.7) return 0;
		return 1;
	}
	
	int checkexist(string name)	//检查某个物品是否已经获得
	{
		rep(i,0,12) if (itm[i].name==name) { used[i]=1; return 1; }
		return 0;
	}
	
	int checkinpak(string name)	//检查某个物品是否在背包中
	{
		rep(i,1,6) if (itm[i].name==name) return i;
		return 0;
	}
	
	armory getarmor(string kind)
	{
		if (kind=="DB") return db;
		if (kind=="DH") return dh;
		if (kind=="DA") return da;
		if (kind=="DF") return df;
		return art;
	}
	
	int getskill(string kind)
	{
		if (kind=="WN" || kind=="WP") return wp;
		if (kind=="WK") return wk;
		if (kind=="WG" || kind=="WJ") return wg;
		if (kind=="WC") return wc;
		if (kind=="WD") return wd;
		if (kind=="WF") return wf;
		return 0;
	}
	
	int itemexist(string name)
	{
		rep(i,0,12) if (itm[i].name==name) return 1;
		return 0;
	}
	
	int do_routine_works()
	{
		//各个子模块的日常工作
		if (slotctl::routine()) return 1;
		if (firectl::routine()) return 1;
		if (radarctl::routine()) return 1;
		if (pvpctl::routine()) return 1;
		if (foodctl::routine()) return 1;
		return 0; 
	}
} 
