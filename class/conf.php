<?php
	class conf
	{
		public $host = "localhost";
		public $app = "sms";
		public $db = "sms";
		public $user = "root";
		public $pass = "3068145";
                public $date_off = "0:00";
		public $company_id = 2;
		public function  __get($key)
		{
			$mysql = new mysql_class;
			$out = '';
			if(property_exists(__CLASS__,$key))
				$out = $this->$key;
			else
			{
				$mysql->ex_sql("select `value` from `conf` where `key` = '$key'",$q);
				if(isset($q[0]))
					$out = $q[0]['value'];
				if($out == 'TRUE')
					$out = TRUE;
				else if($out == 'FALSE')
					$out = FALSE;
			}
			return($out);
		}
		public function __set($key,$value)
		{
			$mysql = new mysql_class;
			if($value===TRUE)
				$value = 'TRUE';
			if($value===FALSE)
                                $value = 'FALSE';
			if(property_exists(__CLASS__,$key))
				$this->key = $value;
			else
			{
				$mysql->ex_sql("select `value` from `conf` where `key` = '$key'",$q);
                                if(isset($q[0]))
					$mysql->ex_sqlx("update `conf` set `value` = '$value' where `key` = '$key'");
				else
					$mysql->ex_sqlx("insert into `conf` (`key`,`value`) values ('$key','$value')");
			}
		}
	}
?>
