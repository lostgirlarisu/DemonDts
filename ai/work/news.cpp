#include "news.h"

namespace newsctl
{
	int visited[200000];
	
	void update()	//更新各类新闻消息，如NPC和玩家死亡情况，玩家的高伤害新闻等
	{
		int num;
		num=toInteger(response["npcdeathnum"]);
		rep(i,1,num)
		{
			int tim=toInteger(response["deathnpctime"+toString(i)]);
			int type=toInteger(response["deathnpctype"+toString(i)]);
			int sNo=toInteger(response["deathnpcsNo"+toString(i)]);
			int pls=toInteger(response["deathnpcpls"+toString(i)]);
			string name=response["deathnpcname"+toString(i)];
			int ksNo=toInteger(response["deathnpckillersNo"+toString(i)]);
			string kname=response["deathnpckillername"+toString(i)];
			npcinfoctl::meetnpc(pls,type,sNo,name);
			if (!npcinfoctl::isdeath[type][sNo])
			{
				textcolor("yellow");
				cout<<"news: NPC "<<name<<" ( type "<<type<<", sNo "<<sNo<<" ) was killed at pls "<<pls;
				if (ksNo!=-1) cout<<" ( murderer: "<<kname<<", sNo "<<ksNo<<" )";
				cout<<endl;
				textcolor("none");
				npcinfoctl::npcsetdeath(type,sNo,pls);
				if (ksNo!=-1) pvpctl::updatepcpls(ksNo,pls,tim);
			}
		}
		num=toInteger(response["pcdeathnum"]);
		rep(i,1,num)
		{
			int tim=toInteger(response["deathpctime"+toString(i)]);
			int sNo=toInteger(response["deathpcsNo"+toString(i)]);
			int pls=toInteger(response["deathpcpls"+toString(i)]);
			string name=response["deathpcname"+toString(i)];
			int state=toInteger(response["deathpcstate"+toString(i)]);
			int ktype=toInteger(response["deathpckillertype"+toString(i)]);
			int ksNo=toInteger(response["deathpckillersNo"+toString(i)]);
			string kname=response["deathpckillername"+toString(i)];
			if (!pvpctl::pc[sNo].death)
			{
				textcolor("yellow");
				cout<<"news: Player "<<name<<" ( sNo "<<sNo<<" ) was killed at pls "<<pls;
				if (ktype!=-1) cout<<" ( murderer: "<<kname<<", type "<<ktype<<", sNo "<<ksNo<<" )"; 
				cout<<endl;
				textcolor("none");
				pvpctl::playersetdeath(sNo);
			}
			if (ktype>0) npcinfoctl::meetnpc(pls,ktype,ksNo,kname); 
			if (ktype==0 && ksNo!=-1) pvpctl::updatepcpls(ksNo,pls,tim);
		}
		num=toInteger(response["newsnum"]);
		rep(i,1,num)
		{
			int nid=toInteger(response["newsnid"+toString(i)]);
			if (visited[nid]) continue;
			visited[nid]=1;
			int ntime=toInteger(response["newstime"+toString(i)]);
			string type=response["newstype"+toString(i)];
			string ia=response["newsinfoa"+toString(i)];
			string ib=response["newsinfob"+toString(i)];
			string ic=response["newsinfoc"+toString(i)];
			string id=response["newsinfod"+toString(i)];
			string ie=response["newsinfoe"+toString(i)];
			//未完工……
			cout<<"news at "<<ntime<<" : "<<type<<"/"<<ia<<"/"<<ib<<"/"<<ic<<"/"<<id<<"/"<<ie<<endl;
			if (type=="newpc" || type=="newgm")
			{
				pvpctl::addnewplayer(toInteger(id),ie);
			}
			if (type=="damagenew")
			{
				int type1=toInteger(id)/1000, sNo1=toInteger(id)%1000;
				int type2=toInteger(ie)/1000, sNo2=toInteger(ie)%1000;
				if (type1==0 && type2==0)	//玩家打玩家，用较新时间更新较旧时间
					if (pvpctl::pc[sNo1].lastvtime>pvpctl::pc[sNo2].lastvtime)
						pvpctl::updatepcpls(sNo2,pvpctl::pc[sNo1].pls,pvpctl::pc[sNo1].lastvtime);
					else  pvpctl::updatepcpls(sNo1,pvpctl::pc[sNo2].pls,pvpctl::pc[sNo2].lastvtime);
				else					//玩家打NPC，用NPC位置更新玩家位置
				{
					if (type1) swap(type1,type2), swap(sNo1,sNo2);
					int t=npcinfoctl::querynpcplace(type2,sNo2);
					//fixme: 禁区后部分NPC位置会变导致玩家位置更新错误，但看起来不是啥大问题
					if (t!=-1) pvpctl::updatepcpls(sNo1,t,ntime);
				}
			}
		}
		
		if (response.count("passive_battle"))
		{
			int type=toInteger(response["passive_w_type"]);
			int sNo=toInteger(response["passive_w_sNo"]);
			string name=response["passive_w_name"];
			if (type>0) 
				npcinfoctl::meetnpc(profile::pls,type,sNo,name);
			else  pvpctl::updatepcpls(sNo,profile::pls,profile::nowtime);
		}
	}
}