<?php
	function __autoload($class_name){
		if(file_exists("../class/".$class_name.".php")){
			require_once("../class/".$class_name.".php");
		}else if(file_exists("../../class_tunk/".$class_name.".php")){
			require_once("../../class_tunk/".$class_name.".php");
		}else{
			die($class_name." is Undefined!!!");
		}
	}
	include_once('pdate.php');
	include_once('jdf.php');
	include_once('inc.php');	
	//include_once 'simplejson.php';
        //require("../class/nusoap.php");
	/*
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph.php');
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph_line.php');
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph_bar.php');
	*/
	ini_set("session.gc_probability","100");
	ini_set("session.gc_maxlifetime","900");
	ini_set("auto_detect_line_endings", true);
	date_default_timezone_set("Asia/Tehran");
	$conf = new conf;
	$enArray = array('all'=>1,'writer'=>'-2','professional'=>-1,'publisher'=>0);
?>
