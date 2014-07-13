<?php
class audit_class{
	public function isAdmin($typ){
		$out = FALSE;
		if($typ == 0){
			$out = TRUE;
		}
		return $out;
	}
        public function isReallyAdmin(){
		$typ = -1;
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
                $out = FALSE;
                if($isAdmin){
                        $out = TRUE;
                }
                return $out;
        }
	public function sub_array($meghdar,&$arr)
	{
		$out = FALSE;
		if(is_array($arr))
			foreach($arr as $i => $val)
				if($val == $meghdar)
				{
					$out = TRUE;
					unset($arr[$i]);
				}
		return($out);
	}
	public function hamed_pdateBack($inp)
        {
		$inp = audit_class::perToEn($inp);
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

                return $out." 14:00:00";
        }
        public function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }

	public function enToPer($inNum){
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
	public function perToEn($inNum){
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
	public function hamed_jalalitomiladi($str)
	{
		$s=explode('/',$str);
		$out = "";
		if(count($s)==3){
			$miladi=jalali_to_jgregorian($s[0],$s[1],$s[2]);
			if((int)$miladi[1]<10)
				$miladi[1] = "0".$miladi[1];
			if((int)$miladi[2]<10)
		                $miladi[2] = "0".$miladi[2];
			$out=$miladi[0]."-".$miladi[1]."-".$miladi[2];
		}
		return $out;
		//jalali_to_gregorian()
	}
	public function idToCode($id)
	{
		return(dechex($id+10000));
	}
	public function codeToId($id)
	{
		$id=hexdec($id)-10000;
		return($id);
	}
	function dec2hex($number)
	{
		$hexvalues = array('0','1','2','3','4','5','6','7',
		       '8','9','A','B','C','D','E','F');
		$hexval = '';
		while($number != '0')
		{
			$hexval = $hexvalues[bcmod($number,'16')].$hexval;
			$number = bcdiv($number,'16',0);
		}
		return $hexval;
	}
	function hex2dec($number)
	{
		$decvalues = array('0' => '0', '1' => '1', '2' => '2',
		       '3' => '3', '4' => '4', '5' => '5',
		       '6' => '6', '7' => '7', '8' => '8',
		       '9' => '9', 'A' => '10', 'B' => '11',
		       'C' => '12', 'D' => '13', 'E' => '14',
		       'F' => '15');
		$decval = '0';
		$number = strrev($number);
		for($i = 0; $i < strlen($number); $i++)
		{
			$decval = bcadd(bcmul(bcpow('16',$i,0),$decvalues[$number{$i}]), $decval);
		}
		return $decval;
	}
	function newIdToCode($i,$dgits=16)
	{
		$suger = 26478000;	
		$inp = $suger+$i;
		$salt =number_format(pow(2,$dgits*4),0, '', '');
		$out = audit_class::dec2hex(bcsub($salt,$inp));
		return($out);
	}
	function newCodeToId($o,$dgits=16)
	{
		$suger = 26478000;	
		$intV = audit_class::hex2dec($o);
		$salt =number_format(pow(2,$dgits*4),0, '', '');
		$inp = bcsub($salt,$intV);
		$out = (float)$inp-$suger;
		return($out);
	}
}
?>
