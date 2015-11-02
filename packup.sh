#!/bin/bash
mysqldump -h localhost -uDTS -pDTS -d dts > ./gamedata/sql/all.sql
tar -cvzf dts.tgz --exclude=config.inc.php --exclude=JudgeOnline --exclude=dts.tgz --exclude=./.* --exclude=gamedata/bak/* .

