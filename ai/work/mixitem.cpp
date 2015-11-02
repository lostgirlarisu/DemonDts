#include "mixitem.h"

namespace mixitemctl
{
	int all, money[1010], neednpc[1010], marked[1010], gotpart[1010], covered[1010];
	double difficulty[1010];
	mixitem mix[1010];
	map<string,int> mixlist;
	int areanum=-1;
	map<string,int> itemneeded[1010];
	int involved[15][1010];
	
	int reducecnt[20];
	
	pair<double, pair<int,int> > calcdifficulty(int i)
	{
		int moneylim=toInteger(response["money"]);
		pair<double, pair<int,int> > ret=make_pair(0,make_pair(0,0));
		rept(it,mix[i].stuff) 
		{
			double z=mapitemctl::calcdifficulty(*it).first;
			int flag=0;
			rep(k,0,12)
				if (profile::itm[k].name==*it && reducecnt[k])
				{
					reducecnt[k]--; flag=1; break;
				}
			if (flag) continue;
			
			int t=shopitemctl::getprice(*it);
			vector<string> s=npcinfoctl::queryitem(*it);
			if (!s.empty()) 
			{
				ret.second.second=1; continue;
			}
			if (mixlist.count(*it) && t>moneylim) 
			{
				pair<double, pair<int,int> > ts=calcdifficulty(mixlist[*it]); 
				ret.first+=ts.first; ret.second.first+=ts.second.first; ret.second.second|=ts.second.second;
			}
			else  if (t<INF && (t<=moneylim || z>ST_WEP_LIM)) ret.second.first+=t; else ret.first+=z;
		}
		return ret;
	}
	
	void calcneeded(int i, map<string,int> &needed)	//启发式合成
	{
		int moneylim=6000;
		rept(it,mix[i].stuff) 
		{
			double z=mapitemctl::calcdifficulty(*it).first;
			int flag=0;
			rep(k,0,12)
				if (profile::itm[k].name==*it && reducecnt[k])
				{
					reducecnt[k]--; involved[k][i]=1; flag=1; break;
				}
			if (flag) continue;
			int t=shopitemctl::getprice(*it);
			vector<string> s=npcinfoctl::queryitem(*it);
			if (!s.empty()) continue;
			if (mixlist.count(*it) && t>moneylim) 
			{
				calcneeded(mixlist[*it],needed);
				continue;
			}
			if (t<=moneylim) continue;
			needed[*it]++;
		}
	}
	
	void initreduce()
	{
		rep(k,0,12) 
			if (profile::itm[k].checkitmkind()<=1) //武器或防具会在合成中一次耗尽
				reducecnt[k]=1; 
			else  if (profile::itm[k].s!=-1) 	//无限耐久物品也会在合成中一次耗尽
					reducecnt[k]=profile::itm[k].s; 
				else  reducecnt[k]=1;
	}
	
	void calcall()
	{
		memset(involved,0,sizeof involved);
		rep(i,1,all) itemneeded[i].clear();
		rep(i,1,all) 
		{
			initreduce();
			calcneeded(i,itemneeded[i]);
		}
		
		memset(neednpc,0,sizeof neednpc);
		rep(i,1,all) 
		{
			initreduce();				
			pair<double,pair<int,int> > ret=calcdifficulty(i);
			difficulty[i]=ret.first; money[i]=ret.second.first; neednpc[i]=ret.second.second;
			//cout<<mix[i].mixresult.name<<" "<<difficulty[i]<<" "<<money[i]<<endl;
		}
		
	}
	
	void init()
	{
		static char buf[100010];
		all=0; mixlist.clear();
		ifstream fin("mixitem.conf");
		while (1)
		{
			int num; fin>>num; 
			fin.getline(buf,99990);	//跳过行末
			if (num==-1) break;
			all++; 
			rep(i,1,num)
			{
				fin.getline(buf,99990);
				mix[all].stuff.push_back(buf);
			}
			fin.getline(buf,99990); string t[10];
			explodecomma(buf,t,5);
			mix[all].mixresult=item(t[1],t[2],t[3],t[4],t[5]);
			mixlist[t[1]]=all;
		}
		fin.close();
	}

	void update()
	{
		if (areanum<toInteger(response["areanum"]))
		{
			areanum=toInteger(response["areanum"]); 
			mapitemctl::updatemap();
			shopitemctl::updateshop();
		}
		calcall();
	}
	
	void markinit()
	{
		memset(marked,0,sizeof marked); 
		memset(gotpart,0,sizeof gotpart);
		memset(covered,0,sizeof covered);
	}
	
	void markwep(int *c, int which)
	{
		int moneylim=toInteger(response["money"]); covered[which]=1;
		rept(it,mix[which].stuff)
		{
			pair<double,int> z=mapitemctl::calcdifficulty(*it);
			if (profile::checkexist(*it)) 
			{
				gotpart[which]=1; continue;
			}
			int t=shopitemctl::getprice(*it);
			vector<string> s=npcinfoctl::queryitem(*it);
			if (!s.empty()) continue;
			if (mixlist.count(*it) && t>moneylim) 
			{
				markwep(c,mixlist[*it]); gotpart[which]|=gotpart[mixlist[*it]];
			}
			else  if (!(t<INF && (t<=moneylim || z.first>ST_WEP_LIM))) 
					if (z.second>=0)
						c[z.second]+=mapitemctl::mapitem[z.second][*it];
		}
	}

	double getstartwepdmg(int skill)
	{
		int moneylim=toInteger(response["money"]);
		double dmg=0;
		rep(i,1,all)
			if (difficulty[i]<ST_WEP_LIM && money[i]<=moneylim && wepkindmatch(mix[i].mixresult.kind))
			{
				weapon wep(mix[i].mixresult);
				if (wep.durable>30 && (WEPKIND!="WG" || mix[i].mixresult.s>30))	//必须耐久足够
					dmg=max(dmg,wep.estimatedmg(skill,250));
			}
		return dmg;
	}
	
	int checkmix()
	{
		double maxdmg=-1; int which=-1;
		rep(i,1,all)
			if (marked[i] && fabs(difficulty[i])<1e-8 && !neednpc[i] && money[i]<=profile::money)	
			{
				double z=weapon(mix[i].mixresult).estimatedmg(profile::getskill(mix[i].mixresult.kind),250);
				if (z>maxdmg) maxdmg=z, which=i;
			}
		if (which==-1) return 0;
		
		int i=which;
		if (money[i]==0)
		{
			int mask=0;
			rept(it,mix[i].stuff)
				if (!profile::checkinpak(*it))
				{
					rep(k,7,12)
						if (profile::itm[k].name==*it)
						{
							cout<<"itemoff "<<k<<" "<<itmxn[k]<<endl;
							controller::itemoff(itmxn[k]);
							return 1;
						}
					cout<<"something was wrong.."<<endl;
				}
				else  mask|=(1<<(profile::checkinpak(*it)-1));
						
			cout<<"itemmix "<<mask<<endl;
			controller::itemmix(mask);
			return 1;
		}
		else 
		{
			rept(it,mix[i].stuff)
				if (!profile::checkexist(*it))
				{
					int z=shopitemctl::getprice(*it);
					if (z>profile::money) 
						cout<<"something was wrong.."<<endl;
					else  
					{
						cout<<"itembuy "<<*it<<endl;
						if (shopitemctl::request(*it,1)) return 1; else return 0;
					}
				}
		}
		return 0;
	}		 
}