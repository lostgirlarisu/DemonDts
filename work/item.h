#ifndef __ITEM_H__
#define __ITEM_H__

#include "common.h"

struct item
{
	string name, kind, skind;
	double e; int s;
	
	item();
	item(string prefix, string suffix);
	item(string _name, string _kind, string _e, string _s, string _skind);
	int checkitmkind();
	void print();
}; 
#endif