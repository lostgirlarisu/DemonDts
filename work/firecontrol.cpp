#include "firecontrol.h"

namespace firectl
{
	double dmglowbnd, dmglowbnd2;	//初期武器和中期武器当前的伤害最低要求
	int usefulcnt[50], usefulcnt2[50];
	
	vector<candwep> clis;
	
	candwep::candwep(weapon _wep, int _type, double _tcost, double _mcost, double _dmg)
	{
		wep=_wep; type=_type; tcost=_tcost; mcost=_mcost; dmg=_dmg;
		memset(v,0,sizeof v);
	}
	
	void getstartwep_map(int *c, int skill, double dmglimit)	//选择起手武器（地图途径）
	{
		int plsnum=toInteger(response["plsnum"])-1;
		rep(i,1,plsnum)
		{
			if (!issafe[i]) continue;
			rept(it,mapitemctl::itemlist[i])
				if (wepkindmatch(it->first.kind))
				{
					weapon wep(it->first);
					if (wep.durable>30 && (WEPKIND!="WG" || wep.itm.s>30) && wep.estimatedmg(skill,250)>=dmglimit)
					{
						c[i]+=it->second;
						textcolor("blue");
						cout<<"firecontrol: candidate weapon ( map, skill="<<skill<<" ): "<<wep.itm.name<<endl;
						textcolor("none");
					}
				}
		}
	}
	
	int cmp(const pair<double,int> &a, const pair<double,int> &b)
	{
		if (a.first!=b.first) return a.first>b.first; else return a.second<b.second;
	}
	
	void getstartwep_mix(int skill, double dmg_lb)	//选择起手武器（合成途径）
	{
		int moneylim=toInteger(response["money"]);
		static pair<double,int> t[1010];
		int tn=0; 
		rep(i,1,mixitemctl::all)
			if (mixitemctl::difficulty[i]<ST_WEP_LIM && mixitemctl::money[i]<=moneylim && wepkindmatch(mixitemctl::mix[i].mixresult.kind))
			{
				weapon wep(mixitemctl::mix[i].mixresult);
				if (wep.durable>30 && (WEPKIND!="WG" || wep.itm.s>30))	//必须耐久足够
				{
					tn++; t[tn]=make_pair(wep.estimatedmg(skill,250),i);
				}
			}
		
		if (tn==0) return;
		sort(t+1,t+tn+1,cmp);
		rep(i,1,tn)
		{
			if (t[i].first<dmg_lb) break;
			//cout<<t[i].first<<" "<<mix[t[i].second].mixresult.name<<endl;
			mixitemctl::marked[t[i].second]=1;
		}
	}
	
	void getstartwep_shop(int skill, int money, double dmg_lb)		//选择起手武器（商店途径）
	{
		rep(i,1,shopitemctl::all)
		{
			pair<item,int> t=shopitemctl::itmlist[i];
			if (wepkindmatch(t.first.kind) && t.second<=money && weapon(t.first).estimatedmg(skill,250)>=dmg_lb)
			{
				shopitemctl::marked[i]=1;
				textcolor("blue");
				cout<<"firecontrol: candidate weapon ( shop, skill="<<skill<<" ): "<<t.first.name<<endl;
				textcolor("none");
			}
		}
	}
	
	void getfinwep_mix()		//选择后期武器（合成途径）
	{
		rep(i,1,mixitemctl::all)
			if (wepkindmatch(mixitemctl::mix[i].mixresult.kind) && !profile::itemexist(mixitemctl::mix[i].mixresult.name))
				if (mixitemctl::difficulty[i]<200 && mixitemctl::money[i]<=15000)
					if (weapon(mixitemctl::mix[i].mixresult).checkfinwep())
						mixitemctl::marked[i]=1;
	}
	
	void getfinwep_shop()		//选择后期武器（商店途径）
	{
		rep(i,1,shopitemctl::all)
		{
			pair<item,int> t=shopitemctl::itmlist[i];
			if (wepkindmatch(t.first.kind) && !profile::itemexist(t.first.name) && t.second<=15000)
			{
				//cout<<t.first.name<<" "<<weapon(t.first).estimatedmg_high(300,1200)<<endl;
				if (weapon(t.first).checkfinwep())
				{
					shopitemctl::marked[i]=1;
					textcolor("blue");
					cout<<"firecontrol: candidate weapon ( shop, final ): "<<t.first.name<<endl;
					textcolor("none");
				}
			}
		}
	}
	
	int routine()
	{
		//判断购买商店武器
		rep(i,1,shopitemctl::all)
			if (shopitemctl::itmlist[i].second<=profile::money && shopitemctl::marked[i])
				if (weapon(shopitemctl::itmlist[i].first).estimatedmg(profile::getskill(shopitemctl::itmlist[i].first.kind),250)>=dmglowbnd)
				{
					textcolor("blue");
					cout<<"routine: requesting to buy shopitem "<<shopitemctl::itmlist[i].first.name<<" ( num: 1 )"<<endl;
					textcolor("none");
					if (shopitemctl::request(shopitemctl::itmlist[i].first.name,1)) return 1;
				}
				
		//判断合成
		if (mixitemctl::checkmix()) return 1;
		
		//防具有比当前好的就穿上
		rep(i,1,6)
			if (!profile::used[i] && profile::itm[i].checkitmkind()==1 && armory(profile::itm[i]).val>profile::getarmor(profile::itm[i].kind).val && (profile::itm[i].s>1 || profile::itm[i].kind[0]=='A'))
			{
				controller::useitem(i);
				return 1;
			}
			
		//武器有比当前好的就用
		int bestwepid=12; 
		double minval=weapon(profile::itm[bestwepid]).estimatedmg(profile::getskill(profile::itm[bestwepid].kind),250);
		if (profile::itm[bestwepid].s==1) minval*=1e-4;	//留下最后一发
		rep(i,1,6)
			if (profile::itm[i].checkitmkind()==0)
			{
				double val=weapon(profile::itm[i]).estimatedmg(profile::getskill(profile::itm[i].kind),250);
				if (profile::itm[i].s==1) val*=1e-4;
				if (val>minval && (!profile::used[i] || weapon(profile::itm[i]).durable>=2)) bestwepid=i;
			}
		
		if (bestwepid!=12)
		{
			controller::useitem(bestwepid);
			return 1;
		}
		
		return 0;
	}
	
	void update()
	{
		dmglowbnd=max(mapitemctl::getstartwepdmg(30)*0.75,mixitemctl::getstartwepdmg(30)*0.75);	//选择初期武器
		dmglowbnd=max(dmglowbnd,shopitemctl::getshopwepdmg(profile::money,30)*0.9);
		rep(i,0,12)
			if (wepkindmatch(profile::itm[i].kind) && weapon(profile::itm[i]).durable>30)
				dmglowbnd=max(dmglowbnd,weapon(profile::itm[i]).estimatedmg(max(profile::getskill(profile::itm[i].kind),30),250)/0.75+1);	//只要当前武器还能有一定耐久，就不能比当前武器差
		
		dmglowbnd2=max(mapitemctl::getstartwepdmg(250)*0.75,mixitemctl::getstartwepdmg(250)*0.75);	//选择中期武器
		//dmglowbnd2=max(dmglowbnd2,npcinfoctl::getnpcwepdmg(250)*0.85);
		dmglowbnd2=max(dmglowbnd2,shopitemctl::getshopwepdmg(1500,250)*0.9);
		rep(i,0,12)
			if (wepkindmatch(profile::itm[i].kind) && weapon(profile::itm[i]).durable>30)
				dmglowbnd2=max(dmglowbnd2,weapon(profile::itm[i]).estimatedmg(250,250)/0.75+1);	//只要当前武器还能有一定耐久，就不能比当前武器差
		
		memset(usefulcnt,0,sizeof usefulcnt);
		memset(usefulcnt2,0,sizeof usefulcnt2);
		mixitemctl::markinit();
		shopitemctl::markinit();
		clis.clear();
		
		getstartwep_mix(250,dmglowbnd2);			//选择中期武器（合成途径）
		getstartwep_map(usefulcnt,250,dmglowbnd2);	//选择中期武器（地图途径）
		getstartwep_shop(250,1500,dmglowbnd2);		//选择中期武器（商店途径）
		getfinwep_mix();						//选择后期武器（合成途径）
		getfinwep_shop();						//选择后期武器（商店途径）
		//getwep_npc(250,1500,dmglowbnd2);			//选择中期/后期武器（NPC途径）
		
		if (toInteger(response["gametime"])<GWEPTIME) 
		{
			//开局若干时间后才开始考虑中后期武器的事情
			//虽然不考虑刻意摸中后期武器，但如果已经有了部件还是要留下来的
			rep(i,1,mixitemctl::all) if (mixitemctl::marked[i]) mixitemctl::markwep(usefulcnt2,i);
			mixitemctl::markinit();
			shopitemctl::markinit();
			memset(usefulcnt,0,sizeof usefulcnt);
			memset(usefulcnt2,0,sizeof usefulcnt2);
			getstartwep_mix(30,dmglowbnd);			//选择初期武器（合成途径）
			getstartwep_map(usefulcnt,30,dmglowbnd);		//选择初期武器（地图途径）
			getstartwep_shop(30,profile::money,dmglowbnd);	//选择初期武器（商店途径）
		}
		
		//usefulcnt为地图上直接可以使用的武器的出现次数
		//而usefulcnt2为地图上合成部件的出现次数
		rep(i,1,mixitemctl::all) 
			if (mixitemctl::marked[i]) 
			{
				mixitemctl::markwep(usefulcnt2,i);
 				textcolor("blue");
				cout<<"firecontrol: candidate weapon ( mix ): "<<mixitemctl::mix[i].mixresult.name<<endl;
				textcolor("none");
			}
		
		//如果有合成已经摸到了第一个部件，应该不再考虑其他合成
		int flag=0;
		rep(i,1,mixitemctl::all)
			if (mixitemctl::marked[i] && mixitemctl::gotpart[i])	//已经有一个合成摸到了第一个部件
			{
				flag=1; break;
			}
		
		if (flag)
		{
			memset(usefulcnt,0,sizeof usefulcnt);
			memset(mixitemctl::covered,0,sizeof mixitemctl::covered);
			rep(i,1,mixitemctl::all)
				if (mixitemctl::marked[i] && mixitemctl::gotpart[i])
				{
					mixitemctl::markwep(usefulcnt,i);
					textcolor("blue");
					cout<<"firecontrol: selected weapon: "<<mixitemctl::mix[i].mixresult.name<<" "<<mixitemctl::difficulty[i]<<" "<<mixitemctl::money[i]<<endl;
					textcolor("none");
				}
		}
		else
		{
			rep(i,0,plsnum) usefulcnt[i]+=usefulcnt2[i];
		}
	}
}
