#include "controller.h"

namespace controller		//命令bot执行操作
{	
	string botname, botpass;
	LL cooldownover;
	vector< pair<string,string> > cmd;
	
	void cooldownwait()
	{
		while (getcurtime()<cooldownover) usleep(50000);	//每0.05秒检查一次当前时间
	}
	
	void cooldownupdate(LL t)
	{
		cooldownover=getcurtime()+t;
	}
	
	void cmdinit()
	{
		cmd.clear(); 
		cmd.push_back(make_pair("botname",botname));
		cmd.push_back(make_pair("botpass",botpass));
	}
	
	void selectclub(int which)	//选择称号
	{
		textcolor("red");
		cout<<"botcontrol: selecting club "<<which<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","selectclub"));
		cmd.push_back(make_pair("var1",toString(which)));
		wget::execute(cmd);
	}
	
	void search()	//探索
	{
		textcolor("red");
		cout<<"botcontrol: searching ( cur pls: "<<profile::pls<<" )"<<endl;
		textcolor("none");
		cooldownwait(); cooldownupdate(SearchCD);
		cmdinit();
		cmd.push_back(make_pair("command","search"));
		wget::execute(cmd);
	}
	
	void move(int pls)	//移动
	{
		textcolor("red");
		cout<<"botcontrol: moving to "<<pls<<endl;
		textcolor("none");
		cooldownwait(); cooldownupdate(MoveCD);
		cmdinit();
		cmd.push_back(make_pair("command","move"));
		cmd.push_back(make_pair("var1",toString(pls)));
		wget::execute(cmd);
	}
	
	void useitem(int which)		//使用物品
	{
		textcolor("red");
		cout<<"botcontrol: using item "<<which<<" ( "<<profile::itm[which].name<<" )"<<endl;
		textcolor("none");
		cooldownwait(); cooldownupdate(ItemCD);
		cmdinit();
		cmd.push_back(make_pair("command","itm"+toString(which)));
		wget::execute(cmd);
	}
	
	void combat(int flag)		//战斗
	{
		textcolor("red");
		if (flag) 
			cout<<"botcontrol: in combat, attack"<<endl; 
		else  cout<<"botcontrol: in combat, back"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","attack"));
		if (!flag) cmd.push_back(make_pair("var1","back"));
		wget::execute(cmd);
	}
	
	void corpse(string which)		//捡取尸体上的物品
	{
		cmdinit();
		cmd.push_back(make_pair("command","getcorpse"));
		cmd.push_back(make_pair("var1",which));
		wget::execute(cmd);
	}
	
	void itemget(int flag)		//拾取或丢弃发现的物品
	{
		textcolor("red");
		if (flag)
			cout<<"botcontrol: itemget ( "<<profile::itm[0].name<<" )"<<endl;
		else  cout<<"botcontrol: itemdrop ( "<<profile::itm[0].name<<" )"<<endl;
		textcolor("none");
		cmdinit();
		if (flag) 
			cmd.push_back(make_pair("command","itemget"));
		else  cmd.push_back(make_pair("command","dropitm0"));
		wget::execute(cmd);
	}
	
	void itemmerge(int t1, int t2)	//合并物品
	{
		profile::shopmode=0;
		textcolor("red");
		cout<<"botcontrol: merging item "<<t1<<" ( "<<profile::itm[t1].name<<" / "<<profile::itm[t1].kind;
		cout<<" ) with item "<<t2<<" ( "<<profile::itm[t2].name<<" / "<<profile::itm[t2].kind<<" )"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","itemmerge"));
		cmd.push_back(make_pair("merge1",toString(t1)));
		cmd.push_back(make_pair("merge2",toString(t2)));
		wget::execute(cmd);
	}
	
	void itemadd()
	{
		profile::shopmode=0;
		textcolor("red");
		cout<<"botcontrol: itemadd ( "<<profile::itm[0].name<<" )"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","itemadd"));
		wget::execute(cmd);
	}
	
	void itemswap(int t1)			//物品槽满时丢弃物品
	{
		profile::shopmode=0;
		textcolor("red");
		cout<<"botcontrol: dropping item "<<t1<<" ( "<<profile::itm[t1].name<<" )"<<endl;
		textcolor("none");
		cmdinit();
		if (t1>0)
			cmd.push_back(make_pair("command","swapitm"+toString(t1)));
		else  cmd.push_back(make_pair("command","dropitm0"));
		wget::execute(cmd);
	}
	
	void itembuy(string name, int num)			//购买物品
	{
		profile::shopmode=1;	//设定shop模式，保证购买的物品不会被丢掉
		textcolor("red");
		cout<<"botcontrol: buying item "<<name<<" ( num: "<<num<<" )"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","shopbuy"));
		cmd.push_back(make_pair("item",name));
		cmd.push_back(make_pair("bnum",toString(num)));
		wget::execute(cmd);
	}
	
	void itemoff(string which)
	{
		textcolor("red");
		cout<<"botcontrol: off item '"<<which<<"'"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","off"+which));
		wget::execute(cmd);
	}
	
	void itemmix(int mask)
	{
		textcolor("red");
		cout<<"botcontrol: mixing item ( mix mask: "<<mask<<" )"<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","itemmix"));
		cmd.push_back(make_pair("mask",toString(mask)));
		wget::execute(cmd);
	}
	
	void checkpvpinfo()
	{
		textcolor("red");
		cout<<"botcontrol: checking pvp info..."<<endl;
		textcolor("none");
		cmdinit();
		cmd.push_back(make_pair("command","pvpinfo"));
		wget::execute(cmd);
	}
	
	void verify()		//确认当前状态
	{
		cmdinit();
		cmd.push_back(make_pair("command","verify"));
		wget::execute(cmd);
	}
	
	void init()
	{
		npcinfoctl::init();
		pvpctl::init();
		mixitemctl::init();
		radarctl::init();
		wget::init();
		cooldownover=getcurtime();
		ifstream fin("bot.conf");
		fin>>botname>>botpass;
		fin.close();
		verify();
	}
}
 
