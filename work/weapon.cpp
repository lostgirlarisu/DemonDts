#include "weapon.h"

weapon::weapon() {}

weapon::weapon(item _itm)
{
	itm=_itm; 
	calcattr();
	calcstim();
}

double weapon::get_hitrate(int skill)
{
	string kind=itm.kind.substr(0,2);
	rep(i,0,7)
		if (kind=="W"+ba_wep_kind[i])
		{
			double z=basic_hitrate[i]+skill*skill_hitrate[i];
			z=min(z,max_hitrate[i]);
			return z;
		}
	return 0;
}

double weapon::check_def(string ch, string def_key)		//检查减半属性
{
	rep(i,0,13)
		if (ch==attr_counter[i][0] && def_key.find(attr_counter[i][1])!=string::npos)
			return 0.5;
	return 1;
}

void weapon::calcscore(int skill, double &base, double &growth, double baseatt, int single_dmg)	//武器评分
{
	string kind=itm.kind.substr(0,2);
	base=0; growth=0; 
	double rapidg=1+rapid_weaken, crp=rapid_weaken; 
	int rsk=200; while (skill>=rsk && rsk<=800) crp*=rapid_weaken, rapidg+=crp, rsk+=200;
	if (itm.skind.find("r")==string::npos) rapidg=1;
	double hitrate=get_hitrate(skill);
	double trate=hitrate, missrate=1-trate; rsk=200;
	while (skill>=rsk && rsk<=800) trate*=rapid_weaken, missrate*=(1-trate), rsk+=200;
	if (itm.skind.find("r")==string::npos) missrate=1-hitrate;
	
	if (single_dmg) missrate=0, rapidg=ceil(rapidg-1e-8), hitrate=1;	//如果只计算能打出的高伤，不考虑命中率问题
	
	if (kind=="WF" || kind=="WJ") base+=itm.e*rapidg*hitrate;
	rep(i,0,7)
		if (itm.skind.find(ex_attack[i])!=string::npos)
		{
			double multiple=1; 
			if (kind=="W"+ex_good_wep[i]) multiple=2;
			multiple*=ex_punish[i];
			double dmg=ex_base_dmg[i]+itm.e/ex_wep_dmg[i]+double(skill)/ex_skill_dmg[i];
			if (ex_max_dmg[i]>0) dmg=min(dmg,ex_max_dmg[i]);
			base+=dmg*multiple*(1-missrate);
		}
	rep(i,0,7)
		if (kind=="W"+ba_wep_kind[i])
		{
			double dmg=(itm.e+baseatt)*ba_skill_multiple[i];
			growth+=dmg*rapidg*hitrate;
		}
}

void weapon::calcattr()
{
	calcscore(0,base_noskill,growth_noskill);
	calcscore(250,base_skilled,growth_skilled);
}

void weapon::calcstim()	//估算耐久
{
	string kind=itm.kind.substr(0,2);
	if (kind=="WG" || kind=="WJ") 
	{
		if (itm.skind.find("o")==string::npos)	
			durable=INF;			//可以装填的枪械可以无限使用
		else  if (itm.s==-1)
				durable=0;
			else  if (itm.skind.find("r")!=string::npos)
					durable=itm.s/2;
				else  durable=itm.s;
		return;
	}
	if (itm.s==-1) 
	{
		durable=INF; return;
	}
	if (kind=="WC" || kind=="WF" || kind=="WD")
	{
		if (itm.skind.find("r")!=string::npos)
			durable=itm.s/2;
		else  durable=itm.s;
		return;
	}
	if (kind=="WK" || kind=="WP")
	{
		double rate;
		if (kind=="WK") rate=0.3*0.8; else rate=0.12*0.85;
		if (itm.skind.find("r")!=string::npos) rate*=2.4;
		durable=ceil(itm.s/rate);
		return;
	}
	durable=0;
}

double weapon::estimatedmg(int skill, int armor, double baseatt)
{
	double base,growth; calcscore(skill,base,growth,baseatt);
	if (itm.kind.substr(0,2)=="WG" && itm.s==-1) return 1.0;
	return max(base+growth*skill/armor,1.0);
}

double weapon::estimatedmg_high(int skill, int armor, double baseatt)
{
	double base,growth; calcscore(skill,base,growth,baseatt,1);
	if (itm.kind.substr(0,2)=="WG" && itm.s==-1) return 1.0;
	return max(base+growth*skill/armor,1.0);
}

double weapon::estimatedmg_real(int skill, int armor, double baseatt, string def_key)
{
	if (def_key.find("A")!=string::npos) def_key+="PKGCDF";
	if (def_key.find("a")!=string::npos) def_key+="IqUWE";	//展开全系/属性防御
	string kind=itm.kind.substr(0,2);
	double base=0, growth=0; 
	double rapidg=1+rapid_weaken, crp=rapid_weaken; 
	int rsk=200; while (skill>=rsk && rsk<=800) crp*=rapid_weaken, rapidg+=crp, rsk+=200;
	if (itm.skind.find("r")==string::npos) rapidg=1;
	double hitrate=get_hitrate(skill);
	double trate=hitrate, missrate=1-trate; rsk=200;
	while (skill>=rsk && rsk<=800) trate*=rapid_weaken, missrate*=(1-trate), rsk+=200;
	if (itm.skind.find("r")==string::npos) missrate=1-hitrate;
	missrate=0; rapidg=ceil(rapidg-1e-8); hitrate=1;
	if (kind=="WF" || kind=="WJ") base+=itm.e*rapidg*hitrate*check_def(kind.substr(1,1),def_key);
	rep(i,0,7)
		if (itm.skind.find(ex_attack[i])!=string::npos)
		{
			double multiple=1; 
			if (kind=="W"+ex_good_wep[i]) multiple=2;
			multiple*=ex_punish[i]*check_def(ex_attack[i],def_key);
			double dmg=ex_base_dmg[i]+itm.e/ex_wep_dmg[i]+double(skill)/ex_skill_dmg[i];
			if (ex_max_dmg[i]>0) dmg=min(dmg,ex_max_dmg[i]);
			base+=dmg*multiple*(1-missrate);
		}
	rep(i,0,7)
		if (kind=="W"+ba_wep_kind[i])
		{
			double dmg=(itm.e+baseatt)*ba_skill_multiple[i]*check_def(ba_wep_kind[i],def_key);
			growth+=dmg*rapidg*hitrate;
		}
	return max(base+growth*skill/armor,1.0);
}

int weapon::checkfinwep()		//检查本武器是否是合格的后期武器
{
	if (durable<80) return 0;	//需要有一定的耐久
	
	item it=itm;
	
	if (it.skind.find("Z")!=string::npos)	//菁英属性应当考虑+4后的情况
		it.e*=1.5*1.6*1.7*1.8;
	
	if (it.s==-1 || (it.kind=="WG" && it.skind.find("o")==string::npos && it.s<=100)) 
		it.e*=1.5;					//无限耐久的考虑用安雅改一次后的情况
	
	if (((it.kind.substr(0,2)=="WP" && it.name.find("棍棒")!=string::npos) || it.kind.substr(0,2)=="WK") && (it.skind.find("Z")==string::npos))
		if (it.skind.find("r")!=string::npos || it.skind.find("d")!=string::npos)
			return 1;		//可以强化的殴系或斩系武器，只要属性有连击或爆炸就可以作为后期
	
	if (weapon(it).estimatedmg_high(300,1200)>700) return 1;	//要能打得动有一定防御的人
	return 0;
}

int weapon::checkfinwep_no_durable()		//检查本武器是否是合格的后期武器，不考虑耐久
{
	if (durable<10) return 0;	//只需10点耐久即可……
	
	item it=itm;
	
	if (it.skind.find("Z")!=string::npos)	//菁英属性应当考虑+4后的情况
		it.e*=1.5*1.6*1.7*1.8;
	
	if (it.s==-1 || (it.kind=="WG" && it.skind.find("o")==string::npos && it.s<=100)) 
		it.e*=1.5;					//无限耐久的考虑用安雅改一次后的情况
	
	if (((it.kind.substr(0,2)=="WP" && it.name.find("棍棒")!=string::npos) || it.kind.substr(0,2)=="WK") && (it.skind.find("Z")==string::npos))
		if (it.skind.find("r")!=string::npos || it.skind.find("d")!=string::npos)
			return 1;		//可以强化的殴系或斩系武器，只要属性有连击或爆炸就可以作为后期
	
	if (weapon(it).estimatedmg_high(300,1200)>700) return 1;	//要能打得动有一定防御的人
	return 0;
}

void weapon::print()
{
	cout<<"weapon quality: "<<endl;
	cout<<"basedmg_noskill: "<<base_noskill<<" growth_noskill: "<<growth_noskill<<endl;
	cout<<"basedmg_skilled: "<<base_skilled<<" growth_skilled: "<<growth_skilled<<endl;
	cout<<"durable: "; if (durable>1e8) cout<<"INF"<<endl; else cout<<durable<<endl;
}

