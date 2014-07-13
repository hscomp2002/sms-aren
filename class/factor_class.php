<?php
	class factor_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `factor` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->user_id=$r['user_id'];
					$this->shomare=$r['shomare'];
					$this->tarikh=$r['tarikh'];
					$this->paik_id=$r['paik_id'];
					$this->status=$r['status'];
					$this->pardakht=$r['pardakht'];
				}
			}
		}
		public function finalFactor($user_id)
		{
			$tarikh = date("Y-m-d H:i:s");
			$my = new mysql_class;
                        $ln = $my->ex_sqlx("insert into factor (user_id,tarikh) values ($user_id,'$tarikh')",FALSE);
			$factor_id = $my->insert_id($ln);
			$my->close($ln);
			if($factor_id>0)
				$my->ex_sqlx("update factor_det set factor_id = $factor_id where factor_id = -$user_id");
			return($factor_id);
		}
		public function loadDet($user_id = -1)
		{
			$out = array();
			$id = ((int)$user_id <= 0)?$this->id:-1*(int)$user_id;
			$my = new mysql_class;
			$my->ex_sql("select * from `factor_det` where `factor_id` = $id",$q);
			foreach($q as $r)
			{
				$tt = new factor_det_class;
				$tt->id = (int)$r['id'];
				$tt->factor_id= (int)$r['factor_id'];
				$tt->kala= new kala_class((int)$r['kala_id']);
				$tt->tedad= (int)$r['tedad'];
				$tt->ghimat= (int)$r['ghimat'];
				$out[] = $tt;
			}
			return($out);
		}
		public function getMablagh($factor_id)
		{
			$my = new mysql_class;
			$my->ex_sql("select sum(ghimat) jam from factor_det where factor_id=$factor_id",$q);
			$out = (int)$q[0]['jam'];
			return($out);
		}
	}
?>
