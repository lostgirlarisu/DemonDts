#include "shopitem.h"

namespace shopitemctl			//商店物品
{
	int areanum=-1, all;
	map<string,int> price;
	map<string,item> slist;
	pair<item,int> itmlist[1010];
	int marked[1010];
	
	void updateshop()
	{
		static char buf[100000];
		if (areanum>=toInteger(response["areanum"])) return;
		areanum=toInteger(response["areanum"]); all=0; price.clear(); slist.clear();
		ifstream fin("shopitem.conf");
		while (!fin.eof())
		{
			fin.getline(buf,99990);
			int len=strlen(buf);
			if (len<5) continue;
			string res[10];
			if (!explodecomma(buf,res,9)) continue;
			if (res[1]=="0") continue;
			if (toInteger(res[4])>areanum) continue; 
			all++; itmlist[all]=make_pair(item(res[5],res[6],res[7],res[8],res[9]),toInteger(res[3]));
			price[res[5]]=toInteger(res[3]); slist[res[5]]=itmlist[all].first;
		}
		fin.close();
	}
	
	int getprice(string name)
	{
		if (price.count(name)) return price[name];
		return INF;
	}
	
	void markinit()
	{
		memset(marked,0,sizeof marked);
	}
	
	int getshopwepdmg(int money, int skill)
	{
		double maxdmg=-1;
		rep(i,1,all)
		{
			pair<item,int> t=itmlist[i];
			if (wepkindmatch(t.first.kind) && t.second<=money)
				maxdmg=max(maxdmg,weapon(t.first).estimatedmg(skill,250));
		}
		return maxdmg;
	}
	
	int checkrequest(string s, int num)
	{
		if (!price.count(s) || price[s]*num>profile::money) return 0;
		if (slist[s].checkitmkind()==2) return 1;		//批准买补给
		double tminute=min(slotctl::sup_hpcnt/550,slotctl::sup_spcnt/600);
		if (profile::money-price[s]*num>=600 || tminute>=3) return 1;	//还有闲钱，或者补给还能坚持至少3分钟，批准
		return 0;
	}
	
	int request(string s, int num)
	{
		if (!checkrequest(s,num)) 
		{
			textcolor("blue");
			cout<<"shopcontrol: request of buying item "<<s<<" ( num: "<<num<<" ) rejected"<<endl;
			textcolor("none");
			return 0;
		}
		textcolor("blue");
		cout<<"shopcontrol: request of buying item "<<s<<" ( num: "<<num<<" ) accepted"<<endl;
		textcolor("none");
		controller::itembuy(s,num);
		return 1;
	}
} 
