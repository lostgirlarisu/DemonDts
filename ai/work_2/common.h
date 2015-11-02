#ifndef __COMMON_H__
#define __COMMON_H__

#include <cstdlib>
#include <cstdio>
#include <iostream>
#include <cmath>
#include <algorithm>
#include <vector>
#include <set>
#include <map>
#include <cstring>
#include <fstream>
#include <sys/time.h>
#include <ctime>
#include <unistd.h>

using namespace std;

typedef long long LL;
typedef unsigned long long ULL;

#define SIZE(x) (int((x).size()))
#define rep(i,l,r) for (int i=(l); i<=(r); i++)
#define repd(i,r,l) for (int i=(r); i>=(l); i--)
#define rept(i,c) for (typeof((c).begin()) i=(c).begin(); i!=(c).end(); i++)

#ifndef ONLINE_JUDGE
#define debug(x) { cerr<<#x<<" = "<<(x)<<endl; }
#else
#define debug(x) {}
#endif

extern map<string,string> response;	//服务器的反馈
extern int starttime;			//游戏开始时间
extern int plsnum;			//地点总数
extern string WEPKIND;			//本局选择的武器系别
extern int issafe[50];			//各个地点是否安全

const string nosta="∞";
const int INF=1000000;
const string itmxn[13]={"itm0","itm1","itm2","itm3","itm4","itm5","itm6","arb","arh","ara","arf","art","wep"};

int toInteger(string s);
string toString(int x);	
int explodecomma(const char *s, string *res, int num);
LL getcurtime();
void textcolor(string color);
int wepkindmatch(string t1);

#endif