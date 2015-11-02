#include "wget.h"

namespace wget		//与服务器交互
{
	string server_name;
		
	void init()
	{
		ifstream fin("server.conf");
		fin>>server_name;
		fin.close();
	}
	
	void updatestate()
	{
		if (response["botservice_version"]!=bot_version) 
		{
			cout<<"ERROR: SERVER BOTSERVICE VERSION NOT MATCHED ( REQUIRE "<<bot_version<<" CURRENT "<<response["botservice_version"]<<" )"<<endl;
			exit(0);
		}
		plsnum=toInteger(response["plsnum"])-1;
		profile::update();
		newsctl::update();
		radarctl::update();
		if (response.count("mode") && response["mode"]=="enemy_spotted" && toInteger(response["w_type"])>0)
			npcinfoctl::meetnpc(profile::pls,toInteger(response["w_type"]),toInteger(response["w_sNo"]),response["w_name"]);
				
		rep(i,0,plsnum) issafe[i]=profile::checkplssafety(i);
		issafe[33]=0;	//特判，F4虽然没有高级NPC但依然不安全
		
		mixitemctl::update();
		firectl::update();
		slotctl::update();
		pvpctl::update();
	}
	
	void execute(vector< pair<string,string> > cmd)
	{
		static char buf[100000];
		string s="wget -q -O response.txt \""+server_name+"/botservice.php\" --post-data=\"";
		rept(it,cmd) 
		{
			if (it!=cmd.begin()) s+="&";
			s+=it->first+"="+it->second;
		}
		s+="\"";
		LL tnow=getcurtime();
		system(s.c_str());
		textcolor("red");
		cout<<"wget: response time = "<<getcurtime()-tnow<<" ms"<<endl;
		textcolor("none");
		ifstream fin("response.txt");
		response.clear();
		while (!fin.eof())
		{
			fin.getline(buf,99990);
			if (fin.eof()) break; s=buf;
			if (s.find('=')==string::npos) continue;
			if (response.count(s.substr(0,s.find('=')))) continue;
			response[s.substr(0,s.find('='))]=s.substr(s.find('=')+1);
		}
		fin.close();
		updatestate();
	}
} 
