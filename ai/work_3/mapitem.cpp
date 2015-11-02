#include "mapitem.h"

int ST_WEP_LIM=40;		//开局武器可接受的最大期望探索步数

namespace mapitemctl			//地图野生物品
{
	int mapitemcnt[60];
	int idall=0, areanum=-1, allitemnum=0;
	map<string,int> itemid, mapitem[60], randitem;
	vector< pair<item,int> > itemlist[60], randitemlist;
	
	int getitemid(string name)
	{
		if (itemid.count(name)) return itemid[name];
		idall++; itemid[name]=idall; return idall;
	}
	
	void updatemap()
	{
		static char buf[100000];
		if (areanum>=toInteger(response["areanum"])) return;
		int newareanum=toInteger(response["areanum"]);
		ifstream fin("mapitem.conf");
		int randcnt=0;
		while (!fin.eof())
		{
			fin.getline(buf,99990);
			int len=strlen(buf);
			if (len<5) continue;
			string res[9];
			if (!explodecomma(buf,res,8)) continue;
			int area=toInteger(res[1]), pls=toInteger(res[2]), num=toInteger(res[3]);
			string name=res[4], kind=res[5], itme=res[6], itms=res[7], itmsk=res[8];
			allitemnum+=num;
			item itm(name,kind,itme,itms,itmsk);
			if ((areanum<area && area<=newareanum) || area==99) 
				if (pls==99) 
				{
					randcnt+=num; 
					randitem[name]+=num;
					randitemlist.push_back(make_pair(itm,num));
				}
				else 
				{
					mapitemcnt[pls]+=num;
					mapitem[pls][name]+=num;
					itemlist[pls].push_back(make_pair(itm,num));
				}
		}	
		int plsnum=toInteger(response["plsnum"])-1;	//英灵殿无物品
		rep(i,1,plsnum-1) mapitemcnt[i]+=randcnt/plsnum;
		areanum=newareanum;
		fin.close();
	}
	
	pair<double,int> calcdifficulty(string itmname)
	{
		int plsnum=toInteger(response["plsnum"])-1;
		double final=1e100; int fi=-1;
		rep(i,0,plsnum)
		{
			if (!issafe[i]) continue;
			map<string,int>::iterator it=mapitem[i].find(itmname);
			if (it==mapitem[i].end()) continue;
			//cout<<i<<" "<<mapitemcnt[i]<<" "<<it->second<<endl;
			if (mapitemcnt[i]/double(it->second)<final)
			{
				final=mapitemcnt[i]/double(it->second);
				fi=i;
			}
		}
		map<string,int>::iterator it=randitem.find(itmname);
		if (it!=randitem.end()) 
			if (double(allitemnum)/it->second<final)
			{
				final=double(allitemnum)/it->second;
				fi=-1;
			}
		return make_pair(final,fi);
	}
	
	double getstartwepdmg(int skill)
	{
		int plsnum=toInteger(response["plsnum"])-1; double dmg=0;
		rep(i,1,plsnum)
		{
			if (!issafe[i]) continue;
			int all=0; static pair<double,int> t[3010];
			rept(it,itemlist[i])
				if (wepkindmatch(it->first.kind))
				{
					weapon wep(it->first);
					if (wep.durable>30 && (WEPKIND!="WG" || wep.itm.s>30))	//必须耐久足够
					{
						all++; t[all]=make_pair(wep.estimatedmg(skill,250),it->second);
					} 
				}
			sort(t+1,t+all+1);
			int lim=mapitemcnt[i]/ST_WEP_LIM;
			repd(k,all,1)
			{
				lim-=t[k].second;
				if (lim<0) { dmg=max(dmg,t[k].first); break; }
			}
		}
		return dmg;
	}
}
