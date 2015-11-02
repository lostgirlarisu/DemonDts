#ifndef __WEAPON_H__
#define __WEAPON_H__

#include "common.h"
#include "item.h"

const string ex_attack[8]={"p","u","i","d","e","w","f","k"};
const string ex_good_wep[8]={"K","G","C","D","P","D",".","."};
const double ex_base_dmg[8]={15,25,10,1,15,20,5,5};
const double ex_wep_dmg[8]={10,5,12,2,10,12,4,5};
const double ex_skill_dmg[8]={15,20,20,500,20,15,40,30};
const double ex_punish[8]={1.35,1,0.95,1,1,1.15,1.35,1.35};
const double ex_max_dmg[8]={90,120,80,-1,100,100,-1,-1};
const string ba_wep_kind[8]={"N","P","K","G","C","D","F","J"};
const double ba_skill_multiple[8]={0.6,0.6,0.65,0.6,0.4,0.75,0.4,0.7};
const double rapid_weaken=0.8;
const double basic_hitrate[8]={0.8,0.8,0.75,0.7,0.7,0.6,0.85,0.1};
const double max_hitrate[8]={0.9,0.9,0.85,0.95,0.96,0.7,0.96,0.98};
const double skill_hitrate[8]={0.025,0.025,0.025,0.05,0.25,0.02,0.1,0.2};
const string attr_counter[14][2]={{"N","P"},{"P","P"},{"K","K"},{"G","G"},
						{"C","C"},{"D","D"},{"F","F"},{"J","G"},
						{"p","q"},{"u","U"},{"i","I"},{"d","D"},{"e","E"},{"w","W"}};

struct weapon		//武器评估
{
	item itm;
	double base_noskill, growth_noskill;			//无熟练时基础伤害与伤害成长
	double base_skilled, growth_skilled;			//有熟练时基础伤害与伤害成长（250熟）
	int durable;							//武器耐久期望的使用次数
	
	weapon();
	weapon(item _itm);
	double get_hitrate(int skill);
	void calcscore(int skill, double &base, double &growth, double baseatt=150, int single_dmg=0);
	void calcattr();
	void calcstim();
	double check_def(string ch, string def_key);
	double estimatedmg(int skill, int armor, double baseatt=150);		//估测平均伤害
	double estimatedmg_high(int skill, int armor, double baseatt=150);	//估测最高伤害
	double estimatedmg_real(int skill, int armor, double baseatt, string def_key);	//估测真实伤害（考虑防御属性）
	int checkfinwep();
	int checkfinwep_no_durable();
	void print();
};
	
#endif 
