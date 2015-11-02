#include "slots.h"

//背包管理
namespace slotctl
{
	double value[20];
	double sup_hpcnt, sup_spcnt;
	
	int routine()
	{
 		//用掉无害强化物品
		rep(i,1,6)
			if (!profile::used[i] && (profile::itm[i].kind[0]=='V' || profile::itm[i].kind[0]=='M'))
			{
				controller::useitem(i);
				return 1;
			}
		if (profile::wep.itm.kind=="WK")
		{
			rep(i,1,6)
				if (profile::itm[i].kind=="Y" && profile::itm[i].name.find("磨刀石")!=string::npos)
				{
					controller::useitem(i);
					return 1;
				}
		}
		if (profile::wep.itm.kind=="WP" && profile::wep.itm.name.find("棍棒")!=string::npos)
		{
			rep(i,1,6)
				if (!profile::used[i] && profile::itm[i].kind=="Y" && profile::itm[i].name.find("钉")!=string::npos)
				{
					controller::useitem(i);
					return 1;
				}
		}
		return 0;
	}
	
	void update()
	{
		//更新选中的武器
		rep(i,0,12) 
			if (profile::itm[i].checkitmkind()==0)
			{
				weapon wep(profile::itm[i]);
				if (!wepkindmatch(wep.itm.kind)) continue;
				if (wep.checkfinwep()) profile::used[i]=1;
				if (wep.estimatedmg(30,250)>=firectl::dmglowbnd && wep.durable>30) profile::used[i]=1;
				if (wep.estimatedmg(250,250)>=firectl::dmglowbnd2 && wep.durable>30) profile::used[i]=1;
			}
		//计算背包中物品的价值
		memset(value,0,sizeof value);
		//计算补给品的价值
		double shp=0, ssp=0;
		int chp[10], csp[10];
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==2)
			{
				item itm=profile::itm[i];
				double z;
				if (itm.e>60) z=min(itm.e,400.0)*itm.s; else z=0;	//小于60效的补给就不要吃了吧……
				chp[i]=csp[i]=0;
				if (itm.kind[1]=='H' || itm.kind[1]=='B') shp+=z, chp[i]=z;
				if (itm.kind[1]=='S' || itm.kind[1]=='B') ssp+=z, csp[i]=z;
			}
		sup_hpcnt=shp; sup_spcnt=ssp;
		double thp=1000;
		if (shp<1000) thp*=(3-shp/500);	//补给稀缺时价值较高
		if (profile::money>600 && shp<20000) thp=100;	//有钱时价值较低 
		double tsp=1000;
		if (ssp<1000) tsp*=(3-ssp/500);
		if (profile::money>600 && ssp<20000) tsp=100;
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==2)
			{
				if (shp>1e-8 && (profile::money<=600 || profile::itm[i].e>98)) value[i]+=chp[i]/shp*thp;
				if (ssp>1e-8 && (profile::money<=600 || profile::itm[i].e>98)) value[i]+=csp[i]/ssp*tsp;
			}
			
		//计算防具的价值
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==1)
			{
				armory ca=profile::getarmor(profile::itm[i].kind), na=armory(profile::itm[i]);
				double naval=na.val*na.stimdbf, caval=ca.val*ca.stimdbf;
				if (naval>caval) 
				{
					double t; if (caval>1e-8) t=min(1.0,(naval-caval)/caval); else t=1;
					value[i]+=500.0*t;
				}
			}
		
		//计算武器的价值
		double bestdmg; weapon bestwepi; int bestid;
		if (profile::wep.itm.s!=0)
		{
			bestdmg=profile::wep.estimatedmg(profile::getskill(profile::wep.itm.kind),250);
			double t=profile::wep.durable;
			if (t<=5) bestdmg*=0.1;	 //耐久差的武器价值不高
			else if (t<=10) bestdmg*=0.3;
			if (wepkindmatch(profile::wep.itm.kind)) bestdmg*=1.5;	//本系武器有一定价值加成
			bestwepi=profile::wep, bestid=12; 
		}
		else  bestdmg=0;
		
		double tdmg[13];
		
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==0)
			{
				tdmg[i]=weapon(profile::itm[i]).estimatedmg(profile::getskill(profile::itm[i].kind),250);
				double t=weapon(profile::itm[i]).durable;
				if (t<=5) tdmg[i]*=0.1;	 //耐久差的武器价值不高
				else if (t<=10) tdmg[i]*=0.3;
				if (wepkindmatch(profile::itm[i].kind)) tdmg[i]*=1.5;	//本系武器有一定价值加成
				if (tdmg[i]>bestdmg+1e-8) bestdmg=tdmg[i], bestwepi=weapon(profile::itm[i]), bestid=i;
			}
			
		if (fabs(bestdmg)<1e-8) bestdmg=1e-8;
		
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==0)
			{
				double t=pow(tdmg[i]/bestdmg,3), val;
				if (i!=bestid && (bestwepi.durable>30 || weapon(profile::itm[i]).durable<bestwepi.durable))
					val=t*10;	//耐久比最优武器差，或最优武器耐久足够，则该武器几乎无价值
				else  val=t*1000;		
				value[i]+=val;
			}
		
		//强化类道具有一定价值
		rep(i,0,6)
			if (profile::itm[i].checkitmkind()==4)
				value[i]+=400;
			
		//生命探测器，第一个探测器有价值
		int found=0;
		rep(i,0,6)
			if (profile::itm[i].kind=="ER" && profile::itm[i].skind=="2")
			{
				value[i]+=20000;
				found=1; break;
			}
		
		if (!found)
			rep(i,0,6)
				if (profile::itm[i].kind=="ER")
				{
					value[i]+=20000;
					break;
				}
		
		//被选中的物品价值很高
		rep(i,0,6) if (profile::used[i]) value[i]=INF;
		
		//如果刚才购买了物品，那么新购买的物品价值很高，以防止被扔掉
		if (profile::shopmode) value[0]=INF*2;
	}
}
