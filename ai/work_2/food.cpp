#include "food.h"

namespace foodctl
{
	int routine()
	{
		//先判HB，如果既要补血又要补体吃HB
		rep(i,1,6)
			if (profile::itm[i].kind.find("HB")!=string::npos && profile::itm[i].e>60 && !profile::used[i])
			{
				int up=min(profile::msp-profile::sp,int(profile::itm[i].e));
				if (profile::mhp-profile::hp>=hplimallow[gamemode] && up>min(double(profile::msp-60),profile::itm[i].e*0.5)) 
				{
					controller::useitem(i);
					return 1;
				}
			}
		
		//然后判HH和HS，只要补一项的吃
		rep(i,1,6)
			if (profile::itm[i].kind.find("HH")!=string::npos && profile::itm[i].e>60 && !profile::used[i])
			{
				if (profile::mhp-profile::hp>hplimallow[gamemode])
				{
					controller::useitem(i);
					return 1;
				}
			}
			else  if (profile::itm[i].kind.find("HS")!=string::npos && !profile::used[i])
			{
				int up=min(profile::msp-profile::sp,int(profile::itm[i].e));
				if (up>=min(double(profile::msp-60),profile::itm[i].e*0.95))
				{
					controller::useitem(i);
					return 1;
				}
			}
		
		//最后再判HB，如果身上只有HB补给，即使补一项也只能用HB补了
		rep(i,1,6)
			if (profile::itm[i].kind.find("HB")!=string::npos && profile::itm[i].e>60 && !profile::used[i])
				if (profile::mhp-profile::hp>hplimallow[gamemode] || profile::sp<60)
				{
					controller::useitem(i);
					return 1;
				}
		
		//没补给了就去商店买
		int chp=0, csp=0;
		rep(i,1,6)
		{
			if (profile::itm[i].kind=="HH" && profile::itm[i].e>60 && !profile::used[i]) chp=1;
			if (profile::itm[i].kind=="HS" && profile::itm[i].e>60 && !profile::used[i]) csp=1;
			if (profile::itm[i].kind=="HB" && profile::itm[i].e>60 && !profile::used[i]) chp=1, csp=1;
		}
		if (!chp)
		{
			double maxe=0; string name=""; int price=0;
			rep(i,1,shopitemctl::all)
			{
				pair<item,int> it=shopitemctl::itmlist[i];
				if (it.first.kind=="HH" && it.first.e>maxe)
				{
					maxe=it.first.e; name=it.first.name; price=it.second;
				}
			}
			int bnum=min(profile::money/price,3);
			if (bnum>0) 
			{ 
				textcolor("blue");
				cout<<"routine: requesting to buy shopitem "<<name<<" ( num: "<<bnum<<" )"<<endl;
				textcolor("none");
				if (shopitemctl::request(name,bnum)) return 1; 
			}
		}
		if (!csp)
		{
			double maxe=0; string name=""; int price=0;
			rep(i,1,shopitemctl::all)
			{
				pair<item,int> it=shopitemctl::itmlist[i];
				if (it.first.kind=="HS" && it.first.e>maxe)
				{
					maxe=it.first.e; name=it.first.name; price=it.second;
				}
			}
			int bnum=min(profile::money/price,3);
			if (bnum>0) 
			{ 
				textcolor("blue");
				cout<<"routine: requesting to buy shopitem "<<name<<" ( num: "<<bnum<<" )"<<endl;
				textcolor("none");
				if (shopitemctl::request(name,bnum)) return 1; 
			}
		}
		return 0;
	}
}
