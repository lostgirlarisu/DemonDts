#include "npc.h"

namespace npcinfoctl			//NPC相关，包括NPC威胁、NPC掉落等
{
	vector<npc> npclist[50], randnpclist[100];
	map< pair<int,int>,npc > appeared[50];
	map< string,vector<string> > npcitem;
	map< pair<int,string> ,npc> npcname;
	map< pair<int,int>,int> npcwhere;
	int isdeath[100][400], npcnumcnt[50], npcdeathnumcnt[50];
	
	void init()		//0禁时根据NPC列表初始化NPC信息以及可以确定的NPC的位置
	{
		//fixme: 禁区后部分NPC位置会变，导致维护的信息错误…… 但暂时不是大问题
		static char buf[100000];
		ifstream fin("npcinfo.conf");
		while (!fin.eof())
		{
			npc t;
			fin.getline(buf,99990); 
			if (strlen(buf)==0) break;
			t.name=buf;
			fin>>t.type>>t.mhp>>t.skill>>t.pls>>t.att>>t.def>>t.money; 
			fin.getline(buf,99990);	//跳过行末
			int z=0;
			while (1)
			{
				z++;
				fin.getline(buf,99990);
				if (strcmp(buf,"end")==0) break;
				string res[10];
				if (!explodecomma(buf,res,5)) continue;
				t.itm.push_back(item(res[1],res[2],res[3],res[4],res[5]));
				if (z==1) t.wep=weapon(item(res[1],res[2],res[3],res[4],res[5]));
				npcitem[res[1]].push_back(t.name);
			}
			npcname[make_pair(t.type,t.name)]=t;
			if (t.pls!=99)
			{
				npclist[t.pls].push_back(t);
				//fixme: 对固定刷新NPC可能会有问题，但考虑到似乎刷新位置固定的NPC都是不可击杀的boss，先这么凑活了
				appeared[t.pls][make_pair(t.type,0)]=t;
				npcwhere[make_pair(t.type,0)]=t.pls;
			}
			else  randnpclist[t.type].push_back(t);
		}
		fin.close();
	}
	
	npc meetnpc(int pls, int type, int sNo, string name)	//根据遇见的NPC的类型和名字返回完整NPC信息，并记录在案
	{
		if (!appeared[pls].count(make_pair(type,sNo))) 
		{
			textcolor("purple");
			cout<<"npccontrol: NPC "<<name<<" ( type "<<type<<", sNo "<<sNo<<" ) was located at pls "<<pls<<endl;
			textcolor("none");
			appeared[pls][make_pair(type,sNo)]=npcname[make_pair(type,name)];
			npcwhere[make_pair(type,sNo)]=pls;
			npcnumcnt[pls]++;
		}
		return npcname[make_pair(type,name)];
	}
	
	vector<string> queryitem(string s)
	{
		if (npcitem.count(s)) return npcitem[s];
		return vector<string>();
	}
	
	vector<npc> getnpclist(int pls)	//获取某地点已知的NPC列表
	{
		vector<npc> res;
		rept(it,appeared[pls]) 
			if (!isdeath[it->first.first][it->first.second]) 
				res.push_back(it->second);
		return res;
	}
	
	int querynpcplace(int type, int sNo)
	{
		if (npcwhere.count(make_pair(type,sNo))) return npcwhere[make_pair(type,sNo)];
		return -1;
	}
	
	void npcsetdeath(int type, int sNo, int pls)
	{
		if (!isdeath[type][sNo])
		{
			isdeath[type][sNo]=1;
			npcdeathnumcnt[pls]++;
		}
	}
	
	double getnpcwepdmg(int skill)
	{
		//fixme: 未完成……
		return 0;
	}
}
