#include "radar.h"

namespace radarctl
{
	int lastradar[50];
	
	void init()
	{
		rep(i,1,49) lastradar[i]=-1;
	}
	
	int routine()
	{
		int found=0, which, aval=0;
		rep(i,1,6)
			if (profile::itm[i].kind=="ER")
			{
				found=1; which=i; 
				if (profile::itm[i].e>1e-8) aval=1;
				break;
			}
			
		if (!found) return 0;
		if (found && !aval)
		{
			rep(i,1,6)
				if (profile::itm[i].kind=="BR")
				{
					controller::useitem(i);
					return 1;
				}
		
			textcolor("yellow");
			cout<<"radarcontrol: requesting to buy battery 探测器电池"<<endl;
			textcolor("none");
			if (shopitemctl::request("探测器电池")) return 1; else return 0;
		}
		
		if (lastradar[profile::pls]<profile::areanum)
		{
			textcolor("yellow");
			cout<<"radarcontrol: using radar "<<profile::itm[which].name<<" ( item "<<which<<" )"<<endl;
			textcolor("none");
			controller::useitem(which);
			return 1;
		}
		return 0;
	}
	
	void update()
	{
		if (response.count("radarresultnum"))
		{
			int all=toInteger(response["radarresultnum"]);
			rep(i,1,all)
			{
				int type=toInteger(response["radarresulttype"+toString(i)]);
				int sNo=toInteger(response["radarresultsNo"+toString(i)]);
				int pls=toInteger(response["radarresultpls"+toString(i)]);
				lastradar[pls]=profile::areanum;
				string name=response["radarresultname"+toString(i)];
				if (type) 
					npcinfoctl::meetnpc(pls,type,sNo,name);
				else  pvpctl::updatepcpls(sNo,pls,profile::nowtime);
			}
		}
	}
}