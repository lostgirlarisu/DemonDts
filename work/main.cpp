#include "common.h"
#include "item.h"
#include "weapon.h"
#include "armor.h"
#include "mapitem.h"
#include "shopitem.h"
#include "npc.h"
#include "pvp.h"
#include "mixitem.h"
#include "controller.h"
#include "profile.h"
#include "slots.h"
#include "firecontrol.h"

//总思路：
//选择一个系，选择一个容易获得的该系武器作为开局武器，并选择一个较容易合成的武器作为后期武器
//farm，同时根据摸到的物品更新武器的合成难度以及武器选择

//游戏策略（gamemode）： 
//0:开局摸起手武器阶段（探索） 
//1:使用起手武器farm阶段（隐藏） 
//2:在已经没有兵的地方摸后期武器（探索）
//3:使用中期武器farm阶段（隐藏）
//4:使用后期武器追杀玩家（隐藏）


void lemon(string wep_kind)
{
	WEPKIND=wep_kind;
	controller::init();
	if (response.count("not_in_game")) { cout<<"ERROR: NOT IN GAME"<<endl; return; }
	if (response.count("wrong_passwd")) { cout<<"ERROR: WRONG PASSWORD"<<endl; return; }
	if (wep_kind=="WP") controller::selectclub(1);
	if (wep_kind=="WK") controller::selectclub(2);
	if (wep_kind=="WC") controller::selectclub(3);
	if (wep_kind=="WG") controller::selectclub(4);
	if (wep_kind=="WD") controller::selectclub(5);
	if (wep_kind=="WF") controller::selectclub(9);
	gamemode=0;
	while (1)
	{
		if (response.count("dead")) break;
		if (response.count("mode"))
		{
			cout<<"mode="<<response["mode"]<<endl;
			if (response["mode"]=="itemfind")
			{
				item itm0=item("itm","0");
				cout<<"itemget"<<endl;
				itm0.print();
				cout<<"itemkept"<<endl;
				controller::itemget(1);
				continue;
			}
			if (response.count("mode") && response["mode"]=="enemy_spotted")
			{
				int w_type=toInteger(response["w_type"]), w_sNo=toInteger(response["w_sNo"]);
				string w_name=response["w_name"];
				if (w_type==0) 
					pvpctl::updatepcpls(w_sNo,profile::pls,profile::nowtime);
				else  npcinfoctl::meetnpc(profile::pls,w_type,w_sNo,w_name);
			}
			if (response["mode"]=="enemy_spotted")
			{
				cout<<"combat "<<response["w_type"]<<" "<<response["w_sNo"]<<" "<<response["w_name"]<<endl;
				cout<<"estimate damage taken: "<<profile::checkdanger(npcinfoctl::meetnpc(profile::pls,toInteger(response["w_type"]),toInteger(response["w_sNo"]),response["w_name"]))<<endl;
				if (!profile::checkplssafety(profile::pls)) 
				{
					cout<<"back"<<endl;
					controller::combat(0);
				}
				else  
				{
					cout<<"attack"<<endl;
					controller::combat(1);
				}
				continue;
			}
			if (response["mode"]=="corpse")
			{
				cout<<"corpse"<<endl;
				controller::corpse("money");
				continue;
			}
			if (response["mode"]=="itemmerge0")
			{
				int s=toInteger(response["itemmergechoicenum"]), merged=0;
				rep(i,0,s-1)
				{
					int t=toInteger(response["itemmergechoice"+toString(i)]);
					if ((profile::itm[t].kind[0]=='H')==(profile::itm[0].kind[0]=='H')) //两个都有毒或两个都没毒
					{
						cout<<"merge with item "<<t<<endl;
						controller::itemmerge(0,t); merged=1;
						break;
					}
				}
				if (merged) continue;
				cout<<"not merge"<<endl;
				controller::itemadd(); continue;
			}
			if (response["mode"]=="itemdrop0")
			{
				rep(i,0,6) printf("%.2lf ",slotctl::value[i]); printf("\n");
				double minval=1e100; int mini=-1;
				repd(i,6,0)
					if (slotctl::value[i]+1e-8<minval)
						minval=slotctl::value[i], mini=i;
				
				cout<<"dropped item "<<mini<<" ( "<<profile::itm[mini].name<<" )"<<endl;
				controller::itemswap(mini); continue;
			}
			cout<<"something wrong : unknown mode "<<response["mode"]<<endl;
		}
		
		if (profile::do_routine_works()) continue;	//执行日常工作

		if (gamemode==0)
		{
			double minv=1e100; int pl=-1;
			rep(i,1,plsnum)
				if (firectl::usefulcnt[i]>0)
				{
					double z=double(mapitemctl::mapitemcnt[i])/firectl::usefulcnt[i];
					if (z<minv) minv=z, pl=i;
				}
			if (pl!=-1 && pl!=profile::pls) 
			{
				cout<<"looking for weapon component, moving to "<<pl<<endl;
				controller::move(pl);
			}
		}
		
		if (!issafe[profile::pls])
		{
			int target;
			while (1)
			{
				target=rand()%plsnum+1;
				if (issafe[target]) break;
			}
			cout<<"current area danger, moving to "<<target<<endl;
			controller::move(target);
			continue;
		}
		
		rep(i,0,12)
			if (profile::itm[i].checkitmkind()==0)
				if (weapon(profile::itm[i]).checkfinwep())
					gamemode=1;
		
		if (gamemode==1)
		{
			int t=pvpctl::gettarget();
			if (t!=-1)
			{
				int pl=pvpctl::querypcpls(t);
				if (profile::pls!=pl)
				{
					cout<<"chasing player "<<t<<" ( "<<pvpctl::pc[t].name<<" ), moving to area "<<pl<<endl;
					controller::move(pl);
					continue;
				}
			}
			else if (npcinfoctl::appeared[profile::pls].size()-npcinfoctl::npcdeathnumcnt[profile::pls]<=5 && npcinfoctl::npcdeathnumcnt[profile::pls]>=3)
			{
				int target;
				while (1)
				{
					target=rand()%plsnum+1;
					if (issafe[target]) break;
				}
				cout<<"switching area to :"<<target<<endl;
				controller::move(target);
				continue;
			}
		}
		
		if (profile::wep.checkfinwep() && profile::money>=800)
		{
			if (profile::wep.itm.kind=="WP") 
			{
				cout<<"buying power up item"<<endl;
				if (shopitemctl::request("钉",1)) continue;
			}
			else  if (profile::wep.itm.kind=="WK")
			{
				cout<<"buying power up item"<<endl;
				if (shopitemctl::request("磨刀石",1)) continue;
			}
		}
		profile::shopmode=0;
		cout<<"search"<<endl;
		controller::search();
	}
}

int main(int argc,char *argv[])
{
	if (argc!=2)
	{
		printf("Usage: ./main <wep_kind>\n");
		return 0;
	}
	srand(time(0));
	ios::sync_with_stdio(true);
	#ifndef ONLINE_JUDGE
		//freopen("try.in","r",stdin);
	#endif
	string wep_kind=argv[1];
	lemon(wep_kind);
	return 0;
}
