<?php
	if(!function_exists('hex2bin'))
	{
		function hex2bin($h)
		{
			if(!is_string($h))
				return null;
			$r='';
			for($a=0; $a<strlen($h); $a+=2)
				$r.=chr(hexdec($h{$a}.$h{($a+1)}));
			return($r);
		}
	}
	function mehrdad_isequal($var,$val)
	{
		$out = FALSE;
		if(isset($var) && $var == $val)
			$out = TRUE;
		return($out);
	}
	function monize($str)
	{
		$out=$str;
		$out=str_replace(',','',$out);
		$out=str_replace('.','',$out);
		$j=-1;
		$tmp='';
		//$strr=explode(' ',$str);
		for($i=strlen($str)-1;$i>=0;$i--){
				//alert(txt[i]);
			if($j<2){
				$j++;
				$tmp=substr($str,$i,1) . $tmp;
			}else{
				$j=0;
				$tmp=substr($str,$i,1) . ',' . $tmp;
			}
		}                
		$out=$tmp;
	//	$out=($str);
	//	$out=strlen($str);
	//	$out=substr[strlen(
		return enToPerNums($out);
	}
	function umonize($str){
		$str = perToEnNums($str);
		$out=$str;
		$out=str_replace(',','',$out);
		$out=str_replace('.','',$out);
		return($out);
	}
	function enToPerNums($inNum){
		$outp = $inNum;
		$outp = str_replace('0', '۰', $outp);
		$outp = str_replace('1', '۱', $outp);
		$outp = str_replace('2', '۲', $outp);
		$outp = str_replace('3', '۳', $outp);
		$outp = str_replace('4', '۴', $outp);
		$outp = str_replace('5', '۵', $outp);
		$outp = str_replace('6', '۶', $outp);
		$outp = str_replace('7', '۷', $outp);
		$outp = str_replace('8', '۸', $outp);
		$outp = str_replace('9', '۹', $outp);
		return($outp);
	}
	function perToEnNums($inNum){
		$outp = $inNum;
		$outp = str_replace('۰', '0', $outp);
		$outp = str_replace('۱', '1', $outp);
		$outp = str_replace('۲', '2', $outp);
		$outp = str_replace('۳', '3', $outp);
		$outp = str_replace('۴', '4', $outp);
		$outp = str_replace('۵', '5', $outp);
		$outp = str_replace('۶', '6', $outp);
		$outp = str_replace('۷', '7', $outp);
		$outp = str_replace('۸', '8', $outp);
		$outp = str_replace('۹', '9', $outp);
		return($outp);
	}
	function loadTime(){
		//Server Version
		$ttt = time() + 8*60*60+30*60;
		//Local Version
		//$ttt = time();
 		return(enToPerNums(date("h:i",$tt)));
 	}
	function hamed_pdateBack2($inp)
        {
		$inp = perToEnNums($inp);
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }
			return $out;
        }
	function loadClist($table,$col='name')
	{
		$out = array(-1=>'');
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id`,`$col` from `$table` order by `$col`",$q);
		foreach($q as $r)
			$out[(int)$r['id']] = $r[$col];
		return($out);
	}
	function columnListLoader($table,$feilds = array('id','name'))
	{
		$out = array(0=>'');
		$tmp = explode('|',$table);
		$tableName = $tmp[0];
		$wer = isset($tmp[1])?' where '.$tmp[1]:'';
		if(count($feilds) >= 2)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select `".$feilds[0]."`,`".$feilds[1]."`".((isset($feilds[2]))?",`".$feilds[2]."`":'')." from `$tableName` $wer order by `".$feilds[0]."`",$q);
			foreach($q as $r)
				$out[$r[$feilds[0]]] = substrH($r[$feilds[1]],50).((isset($feilds[2]))?" ".$r[$feilds[2]]:'');
		}
		return($out);
	}
	function columnListToCombo($clist)
	{
		$out = '';
		if(is_array($clist))
			foreach($clist as $value=>$text)
				$out .= "<option value=\"$value\">\n$text\n</option>\n";
		return($out);
	}
	function substrH($str,$t)
	{
		$ntmp = $str;
		$nltmp = $ntmp;
		$count = mb_strlen($ntmp,'UTF-8');
		if($count>$t)
			$nltmp =mb_substr($ntmp,0,-$count+$t,'UTF-8').'-';
		return $nltmp;
	}
	function checkMobile($shomare)
        {
                $out = FALSE;
                $len = strlen($shomare);
                $tell1 = substr($shomare,0,2);
                $t = -1;
                $j = 2;
                for ($i = 0 ;$i <=9 ; $i ++)
                {
                        if ((substr($shomare,$i+2,1)>0) || (substr($shomare,$i+2,1)<9))
                                $t = 1;
                }
                if (($tell1 == "09") && ($len == 11) && ($t == 1))
                        $out = TRUE;
                else
                        $out = FALSE;
                return($out);
        }
	function makeThumbnails($updir, $img)
	{
	    $thumbnail_width = 134;
	    $thumbnail_height = 189;
	    $thumb_beforeword = "thumb";
	    $arr_image_details = getimagesize($updir.$img); // pass id to thumb name
	    $original_width = $arr_image_details[0];
	    $original_height = $arr_image_details[1];
	    if ($original_width > $original_height) {
		$new_width = $thumbnail_width;
		$new_height = intval($original_height * $new_width / $original_width);
	    } else {
		$new_height = $thumbnail_height;
		$new_width = intval($original_width * $new_height / $original_height);
	    }
	    $dest_x = intval(($thumbnail_width - $new_width) / 2);
	    $dest_y = intval(($thumbnail_height - $new_height) / 2);
	    if ($arr_image_details[2] == 1) {
		$imgt = "ImageGIF";
		$imgcreatefrom = "ImageCreateFromGIF";
	    }
	    if ($arr_image_details[2] == 2) {
		$imgt = "ImageJPEG";
		$imgcreatefrom = "ImageCreateFromJPEG";
	    }
	    if ($arr_image_details[2] == 3) {
		$imgt = "ImagePNG";
		$imgcreatefrom = "ImageCreateFromPNG";
	    }
	    if ($imgt) {
		$old_image = $imgcreatefrom($updir.$img);
		$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
		$imgt($new_image, $updir.$thumb_beforeword.'_'.$img);
	    }
	}
?>
