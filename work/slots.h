#ifndef __SLOTS_H__
#define __SLOTS_H__

#include "common.h"
#include "item.h"
#include "armor.h"
#include "weapon.h"
#include "profile.h"
#include "firecontrol.h"
#include "food.h"

namespace slotctl
{
	extern double value[20];
	int routine();
	void update();
}

#endif
