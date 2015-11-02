<?php
if(!defined('IN_ADMIN')) {
	exit('Access Denied');
}

$is_solo=0;
save_gameinfo();

include template('admin_menu');

?>