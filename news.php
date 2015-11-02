<?php

define('CURSCRIPT', 'news');

require './include/common.inc.php';

if ($server_addr!=$cache_server_addr && $is_cache_server)
{
        header("Location: {$server_addr}news.php");
        exit(); 
}

//$t_s=getmicrotime();
//require_once GAME_ROOT.'./include/JSON.php';
require_once GAME_ROOT.'./include/news.func.php';


$newsfile = GAME_ROOT.'./gamedata/newsinfo.php';
$newshtm = GAME_ROOT.TPLDIR.'/newsinfo.htm';
$lnewshtm = GAME_ROOT.TPLDIR.'/lastnews.htm';

/*
if(filemtime($newsfile) > filemtime($lnewshtm)) {
	$lnewsinfo = nparse_news(0,$newslimit);
	writeover($lnewshtm,$lnewsinfo);
}
*/
if(!isset($newsmode)){$newsmode = '';}

//if ($newsmode == '' || $newsmode == 'last') $newsmode = 'all';

$data = readover($newshtm);
$plist = openfile($newshtm);
$num=count($plist); 

if ($data=='') $num=0;
	
$result = $db->query("SELECT COUNT(*) FROM {$tablepre}newsinfo");
$cnt = $db->result($result,0);

	

$data=nparse_news(0,65535);
writeover($newshtm,$data);

	
if($newsmode == 'all') {
	
	echo "<ul>";
	include template('newsinfo');
	echo "</ul>";
	$newsdata['innerHTML']['newsinfo'] = ob_get_contents();
	if(isset($error)){$newsdata['innerHTML']['error'] = $error;}
	ob_clean();
	$jgamedata = compatible_json_encode($newsdata);
	//$json = new Services_JSON();
	//$jgamedata = $json->encode($newsdata);
	echo $jgamedata;
	ob_end_flush();	

} elseif($newsmode == 'chat') {
	$newsdata['innerHTML']['newsinfo'] = '';
	$chats = getchat(0,'',$chatinnews);
	$chatmsg = $chats['msg'];
	foreach($chatmsg as $val){
		$newsdata['innerHTML']['newsinfo'] .= $val;
	}	
	if(isset($error)){$newsdata['innerHTML']['error'] = $error;}
	ob_clean();
	$jgamedata = compatible_json_encode($newsdata);
//	$json = new Services_JSON();
//	$jgamedata = $json->encode($newsdata);
	echo $jgamedata;
	ob_end_flush();
} else {
	include template('news');
}
//$t_e=getmicrotime();
//putmicrotime($t_s,$t_e,'news_time');

?>	
