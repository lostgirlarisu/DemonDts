<?php
if ($_GET['pw']!='dts_admin') die();
$output = shell_exec('./packup.sh');
$output = shell_exec('ls -l dts.tgz');
echo "<pre>$output</pre>";
?>
