<?php
	class log_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `log` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->user_id=$r['user_id'];
					$this->regdate=$r['regdate'];
					$this->page=$r['page'];
					$this->toz=$r['toz'];
					$this->extra=$r['extra'];
				}
			}
		}
		public function add($us_id,$to,$ext)
		{
			$flag='false';
			$ms=new mysql_class;
			$page=security_class::thisPage();
			$today=date("Y/m/d H:i:s");
			$tmp=$ms->ex_sqlx("insert into log (`user_id`, `regdate`, `page`, `toz`, `extra`) 
			values('$us_id','$today','$page','$to','$ext')",FALSE);
			//die("insert into log(`user_id`, `regdate`, `page`, `toz`, `extra`) 
			//values('$user_id','$today','$page','$toz','$extra')");
			$max=$ms->insert_id($tmp);
			$ms->close($tmp);
			if($max>0)
				$flag='true';
			return($flag);
		}
	}
?>
