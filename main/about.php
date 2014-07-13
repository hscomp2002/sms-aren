<?php 
	include('../kernel.php');
	$ou = str_replace("#start1#",$conf->start1,$conf->about);
	$ou = str_replace("#stop1#",$conf->stop1,$ou);
	$ou = str_replace("#start2#",$conf->start2,$ou);
	$ou = str_replace("#stop2#",$conf->stop2,$ou);
	echo $ou;
?>
