#include "common.h"

map<string,string> response;
int plsnum;
string WEPKIND;
int issafe[50];
int starttime=-1;

int toInteger(string s)
{
	int flag=1, beg=0; 
	if (s[0]=='-') flag=-1, beg=1;
	int val=0;
	rep(i,beg,int(s.size())-1)
	{
		if (s[i]<'0' || s[i]>'9') break;
		val=val*10+s[i]-48;
	}
	return val*flag;
}

string toString(int x)
{
	string ret=""; int flag=0;
	if (x==0) ret="0";
	if (x<0) flag=1, x=-x;
	while (x) ret=char(x%10+48)+ret, x/=10;
	if (flag) ret="-"+ret;
	return ret;
}
	
int explodecomma(const char *s, string *res, int num)
{
	int k=1, i=0, len=strlen(s);
	while (i<len && k<=num)
	{
		if (s[i]==',') k++; else res[k]+=s[i]; 
		i++;
	}
	return k>num;
}

LL getcurtime()    
{    
	timeval tv;    
	gettimeofday(&tv,NULL);    
	return LL(tv.tv_sec) * 1000 + tv.tv_usec / 1000;    
}

void textcolor(string color)
{
	string code="";
	if (color=="none") code="\e[0m";
	if (color=="red") code="\e[31m\e[1m";
	if (color=="green") code="\e[32m\e[1m";
	if (color=="yellow") code="\e[33m\e[1m";
	if (color=="blue") code="\e[34m\e[1m";
	if (color=="purple") code="\e[35m\e[1m";
	if (color=="white") code="\e[37m\e[1m";
	cout<<code;
	if (color!="none" && starttime!=-1) printf("[ %.2lf ] ",getcurtime()/1000.0-starttime+28800);	//坑爹的时差……
}

int wepkindmatch(string t1)
{
	if (WEPKIND=="WP") return (t1=="WP" || t1=="WN");
	if (WEPKIND=="WK") return (t1.substr(0,2)=="WK");
	if (WEPKIND=="WG") return (t1=="WG" || t1=="WJ");
	if (WEPKIND=="WC") return (t1.substr(0,2)=="WC");
	return t1==WEPKIND;
}
