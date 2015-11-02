#ifndef __ARMOR_H__
#define __ARMOR_H__

#include "common.h"
#include "item.h"

const string ex_armor[15]={"C","c","D","E","F","G","I","K","P","q","U","W","Z","M","m"};
const double ex_armval[15]={80,80,180,40,140,60,40,80,80,40,40,40,0,700,700};

//考虑到目前大逃杀防具比较单调…… 前期的属性防御和全系防御都是身体防具，后期标准装备职人装…… 所以这里写的比较简单 
struct armory		//防具评估
{
	item itm;
	double val,stimdbf;		//简单的估价
	
	armory();
	armory(item _itm);
	void calcval();
};
#endif 
