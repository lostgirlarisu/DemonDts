#ifndef __FOOD_H__
#define __FOOD_H__
#include "common.h"
#include "profile.h"
#include "item.h"
#include "controller.h"

const int hplimallow[5]={60,40,20,15,10};

namespace foodctl
{
	pair<double,double> countfood();
	double calc_tminute();
	int routine();
}
#endif