<?php
	class iplist_class
	{
		public function __construct($ip='')
		{
			if($ip != '')
			{
				$this->ip=$ip;
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `iplist` where `ip` = '$ip'",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->ip=$r['ip'];
					$this->lastActivity=$r['lastActivity'];
					$this->tryCount=$r['tryCount'];
				}
			}
		}
		public function addIP($add = FALSE)
		{
			$conf = new conf;
			$mysql = new mysql_class;
			$out = TRUE;
			if(isset($this->id))
			{
				$ip = $this->ip;
				$maxTry = ((int)$conf->maxTry > 0) ? (int)$conf->maxTry : 3;
				$blockTime = ((int)$conf->blockTime > 0) ? (int)$conf->blockTime : 20;
				if($add)
					$mysql->ex_sqlx("update `iplist` set `tryCount` = `tryCount` +1,`lastActivity` = '".(date("Y-m-d H:i:s"))."' where `ip` = '$ip'");
				if($this->tryCount >= $maxTry && strtotime($this->lastActivity." + $blockTime minute") >= strtotime(date("Y-m-d H:i:s")))
					$out = FALSE;
				else if($this->tryCount >= $maxTry && strtotime($this->lastActivity." + $blockTime minute") < strtotime(date("Y-m-d H:i:s")))
					$mysql->ex_sqlx("delete from `iplist` where `ip` = '$ip'");
			}
			else if($add)
				$mysql->ex_sqlx("insert into `iplist` (`ip`,`lastActivity`) values ('".$this->ip."','".(date("Y-m-d H:i:s"))."')");
			return($out);
		}
		public function removeIP()
		{
			$mysql = new mysql_class;
			$mysql->ex_sqlx("delete from `iplist` where `ip` = '".$this->ip."'");
		}
	}
?>
