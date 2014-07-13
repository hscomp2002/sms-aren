<?php
	class sms_send_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `sms_send` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->mobile=$r['mobile'];
					$this->sms_text_id=$r['sms_text_id'];
					$this->tarikh=$r['tarikh'];
				}
			}
		}
                public function add($mobs,$text_sms)
                {
                    $out = -1;
                    $now = date("Y-m-d H:i:s");
                    $my = new mysql_class;
                    $ln = $my->ex_sqlx("insert into sms_text (`text`,`tarikh`) values ('$text_sms','$now')",FALSE);
                    $sms_text_id = $my->insert_id($ln);
                    $my->close($ln);
                    if($sms_text_id>0)
                    {
                        $qu_vals = '';
                        foreach($mobs as $numb)
                        {
                            $qu_vals.=($qu_vals==''?'':',')."('$numb',$sms_text_id,'$now')";
                        }
                        $qu = "insert into sms_send (`mobile`,`sms_text_id`,tarikh) values $qu_vals";
                        $out = 0;
                        if($qu_vals!='')
                        {
                            $my->ex_sqlx($qu);
                            $out = 1;
                        }
                    }
                    return($out); 
                }
	}
?>