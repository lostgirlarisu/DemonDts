#include "pvp.h"

namespace pvpctl
{
	player pc[1010];
	int all, lastcheck;
	
	void init()
	{
		all=0; lastcheck=0;
	}
	
	void playersetdeath(int which)
	{
		pc[which].death=1;
		textcolor("purple");
		cout<<"pvpctl: player "<<which<<" ( "<<pc[which].name<<" ) had died."<<endl;
		textcolor("none");
	}
	
	void playersetrevival(int which)
	{
		pc[which].death=0;
		textcolor("purple");
		cout<<"pvpctl: player "<<which<<" ( "<<pc[which].name<<" ) revivaled."<<endl;
		textcolor("none");
	}
	
	void updatepcpls(int which, int pls, int t)
	{
		if (t<pc[which].lastvtime) return;
		pc[which].pls=pls; pc[which].lastvtime=t;
		textcolor("purple");
		cout<<"pvpctl: player "<<which<<" ( "<<pc[which].name<<" ) located at pls "<<pls<<endl;
		textcolor("none");
	}
	
	void addnewplayer(int which, string name)
	{
		all=max(all,which);
		pc[which].death=0; pc[which].lastvtime=0; pc[which].pls=0; pc[which].mhp=400; 
		pc[which].skill=20; pc[which].defall=100; pc[which].name=name;
		pc[which].cankill=0; pc[which].danger=0;
		pc[which].wep=weapon(item("wep","WP","5","9999",""));
		textcolor("purple");
		cout<<"pvpctl: new player added, name '"<<name<<"', ID "<<which<<endl;
		textcolor("none");
	}
	
	int querypcpls(int sNo)
	{
		return pc[sNo].pls;
	}
	
	int routine()
	{
		if (toInteger(response["gametime"])-lastcheck>=30)
		{
			lastcheck=toInteger(response["gametime"]);
			controller::checkpvpinfo(); 
			return 1;
		}
		return 0;
	}
	
	int checkcankill(player p)
	{
		return profile::wep.estimatedmg_real(profile::getskill(profile::wep.itm.kind),p.defall,profile::att,"")>=p.mhp*0.7;
	}
	
	int checkcanbekilled(player p)
	{
		return p.wep.estimatedmg_real(p.skill,profile::defall,200,profile::def_key)>=profile::mhp*0.7;
	}
	
	void update()
	{
		if (response.count("pcalivenum"))
		{
			int tot=toInteger(response["pcalivenum"]);
			rep(i,1,tot)
			{
				int sNo=toInteger(response["pcalivesNo"+toString(i)]);
				string wepk=response["pcalivewepk"+toString(i)];
				string wepe=response["pcalivewepe"+toString(i)];
				string wepsk=response["pcalivewepsk"+toString(i)];
				int defall=toInteger(response["pcalivedefall"+toString(i)])+1;
				int mhp=toInteger(response["pcalivemhp"+toString(i)]);
				int skill=toInteger(response["pcaliveskill"+toString(i)]);
				int isteam=toInteger(response["pcaliveisteammate"+toString(i)]);
				pc[sNo].wep=weapon(item("wep",wepk,wepe,"9999",wepsk));
				pc[sNo].defall=defall;
				pc[sNo].mhp=mhp;
				pc[sNo].skill=skill;
				pc[sNo].isteam=isteam;
			}
			rep(i,1,all)
				if (i!=profile::mysNo)
				{
					cout<<"pvp: "<<i<<" "<<" "<<pc[i].pls<<" "<<profile::nowtime-pc[i].lastvtime<<" "<<profile::wep.estimatedmg_real(profile::getskill(profile::wep.itm.kind),pc[i].defall,profile::att,"")<<endl;
					pc[i].cankill=checkcankill(pc[i]); 
					pc[i].danger=checkcanbekilled(pc[i]); 
				}
		}
	}
	
	int gettarget()
	{
		int maxt=0, maxi=0;
		rep(i,1,all)
			if (i!=profile::mysNo && !pc[i].death && pc[i].cankill && !pc[i].danger && issafe[pc[i].pls] && !pc[i].isteam && pc[i].lastvtime>maxt)
			{
				maxt=pc[i].lastvtime; maxi=i;
			}
		//fixme: 需要更精确的估价系统
		if (profile::nowtime-maxt<=40) return maxi; else return -1;
	}
}

