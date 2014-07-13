<?php
	class factor_det_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `factor_det` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->factor_id=$r['factor_id'];
					$this->kala_id=$r['kala_id'];
					$this->tedad=$r['tedad'];
					$this->ghimat=$r['ghimat'];
				}
			}
		}
		public function addFactorDet($factor_id,$kala_id,$tedad,$ghimat)
		{
			$my = new mysql_class;
			$factor_id = (int)$factor_id;
			$kala_id = (int)$kala_id;
			$tedad = (int)$tedad;
			$ghimat = (int)$ghimat;
			$my->ex_sql("select `id` from `factor_det` where `kala_id` = $kala_id and `factor_id` = $factor_id",$q);
			if(isset($q[0]))
			{
				$fdet_id = (int)$q[0]['id'];
				$my->ex_sqlx("update `factor_det` set `tedad` = `tedad` + $tedad , `ghimat` = `ghimat` + $ghimat where `id` = $fdet_id");
			}
			else
			{
				$ln = $my->ex_sqlx("insert into `factor_det` (`factor_id`,`kala_id`,`tedad`,`ghimat`) values ($factor_id,$kala_id,$tedad,$ghimat)",FALSE);
				$fdet_id = $my->insert_id($ln);
				$my->close($ln);
			}
			if($fdet_id > 0)
				$my->ex_sqlx("update `kala` set `mojoodi` = `mojoodi` - $tedad where id = $kala_id");
			return($fdet_id);
		}
		public function loadByUser($user_id)
		{
			$out = array();
			$user_id = (int)$user_id;
			$my = new mysql_class;
			$my->ex_sql("select * from `factor_det` where `shomare` = -$user_id",$q);
			foreach($q as $r)
			{
				$tt = new factor_det_class;
				$tt->id = (int)$r['id'];
				$tt->factor_id= (int)$r['factor_id'];
				$tt->kala_id= (int)$r['kala_id'];
				$tt->tedad= (int)$r['tedad'];
				$tt->ghimat= (int)$r['ghimat'];
				$out[] = $tt;
			}
			return($out);
		}
	}
?>
