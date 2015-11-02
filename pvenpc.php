<?php

define('CURSCRIPT', 'help');

require './include/common.inc.php';
include_once config('npc',$gamecfg);

if ($server_addr!=$cache_server_addr && $is_cache_server)
{
        header("Location: {$server_addr}pvenpc.php");
        exit(); 
}

for ($i=0; $i<=20; $i++) $p[$i]=$i;
$ty5[1]=22;
$ty6[1]=21;
$ty7[1]=25;
$ty8[1]=30;$ty8[2]=31; $ty8[3]=32;

include template('pvenpc');

