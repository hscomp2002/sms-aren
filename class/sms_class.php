<?php
	class sms_class
	{
		public function sendSms($mob_number,$txt_sms)
		{
			$conf = new conf();
			$ch = curl_init();
			$addr = $conf->sms_addr.'Username='.$conf->sms_username.'&Password='.$conf->sms_password.'&PortalCode='.$conf->sms_portalnum.'&Mobile='.$mob_number.'&Message='.$txt_sms.'&Flash='.'0';
                        echo $addr."\n";
			curl_setopt($ch, CURLOPT_URL, $addr);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$out = (int)curl_exec($ch);
			curl_close($ch);
			return ($out);
		}
                public function sendSMS_webservice($numbers,$text_sms)
                {
                    $conf = new conf;
                    $text_arr = array();
                    foreach($numbers as $num)
                        $text_arr[] = $text_sms;
                    $numbers[]='';
                    $text_arr[]='';
                    $param = array('PortalCode'=>$conf->sms_portalnum,'UserName' => $conf->sms_username,
                    'PassWord' => $conf->sms_password,
                    'Mobiles' => $numbers,
                    'Messages'=>$text_arr,
                    'FlashSMS'=>FALSE,
                    'ServerType'=>1
                    );
                    $cl = new SoapClient("http://messagingws.negins.com/SendSMS.asmx?WSDL");
                    return($cl->MultiSMSEngine($param));
                }
                public function sms_grp_send($inp,$text_sms,$calculate=FALSE)
                {
                    $out = FALSE;
                    $conf = new conf;
                    $grp_ids = implode(',',$inp);
                    if(count($grp_ids)>0 && isset($this))
                    {
                        $my = new mysql_class;
                        $som_days_ago = date(strtotime(date("Y-m-d H:i:s").'  -'.$conf->sms_delay_days.' day '));
                        $my->ex_sql("select id from sms_text where `text`='$text_sms' and tarikh>='$som_days_ago'",$qp);
                        $iid='-1';
                        foreach($qp as $rp)
                        {
                            $iid.=($iid==''?'':',').$rp['id'];
                        }
                        $my->ex_sql("SELECT numbers.mobiles FROM `numbers` left join group_numbers on (numbers.id=numbers_id) where is_siah=0 and `group_id` in ($grp_ids) and not numbers.mobiles in (select mobile from sms_send where sms_text_id in ($iid)) group by mobiles",$q);
                        if(!$calculate)
                        {
                            $mobs=array();
                            $mobs2 = array();
                            foreach ($q as $r)
                            {
                                $mobs[] = '0'.$r['mobiles'];
                                $mobs2[] = $r['mobiles'];
                            }
                            $this->sendSMS_webservice($mobs, $text_sms);
                            $out=TRUE;
                        }
                        else
                            $out =count($q);
                    }
                    if(!$calculate)
                        sms_send_class::add($mobs2,$text_sms);
                    return($out);
                }
                public function getCredit()
                {
                    $out = 0;
                    $conf = new conf;
                    $param = array('PortalCode'=>$conf->sms_portalnum,'UserName' => $conf->sms_username,
                    'PassWord' => $conf->sms_password
                    );
                    $cl = new SoapClient("http://messagingws.negins.com/SendSMS.asmx?WSDL");
                    $tt = $cl->GetSystemCredit($param);
                    return($tt->GetSystemCreditResult);
                    //return($out);
                }
	}
?>
