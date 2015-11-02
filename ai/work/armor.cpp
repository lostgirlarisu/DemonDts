#include "armor.h"

armory::armory() {}

armory::armory(item _itm)
{
	itm=_itm;
	calcval();
}
	
void armory::calcval()
{
	val=itm.e;
	if (itm.kind=="A" || itm.kind=="Ag" || itm.kind=="Al") val=0;
	if (itm.skind.find("A")!=string::npos) val+=300;
	if (itm.skind.find("a")!=string::npos) val+=400;
	int cn; if (itm.kind[0]!='A') cn=12; else cn=14;	//只对饰物考虑陷阱探测和陷阱迎击属性
	rep(i,0,cn)
		if (itm.skind.find(ex_armor[i])!=string::npos)
			val+=ex_armval[i];
	
	//耐久差的防具要削弱一定价值
	if (itm.kind[0]!='A')
	{
		const double sdebuff[21]={0,0.4,0.5,0.6,0.7,0.8,0.82,0.84,0.86,0.88,0.9,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99,1};
		if (itm.s==-1) stimdbf=sdebuff[1]; else if (itm.s<=20) stimdbf=sdebuff[itm.s]; else stimdbf=1;
	}
	else  stimdbf=1;
}

