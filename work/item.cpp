#include "item.h"

item::item() {}

item::item(string prefix, string suffix)
{
	if (!response.count(prefix+"s"+suffix) || response[prefix+"s"+suffix]=="0") 
	{ 
		name="none"; kind=""; skind=""; e=0; s=0; return; 
	}
	name=response[prefix+suffix];
	kind=response[prefix+"k"+suffix];
	e=toInteger(response[prefix+"e"+suffix]);
	if (response[prefix+"s"+suffix]==nosta) s=-1; else s=toInteger(response[prefix+"s"+suffix]);
	skind=response[prefix+"sk"+suffix];
	kind=kind.substr(0,2);	//直接无视带星数的武器、双系武器等搞笑情况
}
	
item::item(string _name, string _kind, string _e, string _s, string _skind)
{
	name=_name; kind=_kind; e=toInteger(_e); skind=_skind;
	if (_s==nosta) s=-1; else s=toInteger(_s); 
	kind=kind.substr(0,2);	//直接无视带星数的武器、双系武器等搞笑情况
}

int item::checkitmkind()
{
	//0: 武器 1:防具 2:补给 4: 强化药 3:其他
	string k=kind.substr(0,2);
	if (k=="WP" || k=="WK" || k=="WC" || k=="WG" || k=="WD" || k=="WF" || k=="WJ") return 0;
	if (k=="DB" || k=="DH" || k=="DA" || k=="DF" || k=="A") return 1;
	if (k=="HB" || k=="HH" || k=="HS") return 2;
	if (k.length()>0 && (k[0]=='M' || k[0]=='V')) return 4;
	return 3;
}
	
void item::print()
{
	cout<<name<<","<<kind<<","<<e<<","<<s<<","<<skind<<","<<endl;
}
