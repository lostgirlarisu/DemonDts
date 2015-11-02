#ifndef __WGET_H__
#define __WGET_H__

#include "common.h"
#include "npc.h"
#include "mixitem.h"
#include "profile.h"
#include "mapitem.h"
#include "slots.h"
#include "firecontrol.h"
#include "news.h"
#include "radar.h"

namespace wget		//与服务器交互
{ 
	const string bot_version="0.3-20140219-1";
	extern string server_name;
	
	void init();
	void updatestate();
	void execute(vector< pair<string,string> > cmd);
}

#endif
