<?php
	class pay_class
	{
		public function ps_pay($pardakht_id,$amount)
		{
			$conf = new conf;
			require_once("../class/RSAProcessor.class.php"); 
			include_once("../simplejson.php");
			$processor = new RSAProcessor("../class/certificate.xml",RSAKeyType::XMLFile);
			$pardakht = new pardakht_class((int)$pardakht_id);
			$merchantCode = $conf->ps_merchantCode;
			$terminalCode = $conf->ps_terminalCode;
			$redirectAddress = $conf->ps_redirectAddress;
			$invoiceNumber = $pardakht_id;
			$timeStamp = str_replace('-','/',$pardakht->tarikh);
			$invoiceDate = str_replace('-','/',$pardakht->tarikh);
			$action = "1003"; 	// 1003 : براي درخواست خريد 
			$data = "#". $merchantCode ."#". $terminalCode ."#". $invoiceNumber ."#". $invoiceDate ."#". $amount ."#". $redirectAddress ."#". $action ."#". $timeStamp ."#";
			$data = sha1($data,true);
			$data =  $processor->sign($data); // امضاي ديجيتال 
			$result =  base64_encode($data); // base64_encode
			$out['invoiceNumber'] = $invoiceNumber;
			$out['invoiceDate'] = $invoiceDate;
			$out['amount'] = $amount;
			$out['terminalCode'] = $terminalCode;
			$out['merchantCode'] = $merchantCode;
			$out['redirectAddress'] = $redirectAddress;
			$out['timeStamp'] = $timeStamp;
			$out['action'] = $action;
			$out['sign'] = $result;
			$outJson = toJSON($out);
			return($outJson);
		}
	}
?>
