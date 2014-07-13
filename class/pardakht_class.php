<?php
	class pardakht_class
	{
		public function __construct($id=-1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `pardakht` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id=$r['id'];
				$this->user_id=$r['user_id'];
				$this->tarikh=$r['tarikh'];
				$this->mablagh=$r['mablagh'];
                                if($r['bank_out'] != '')
  	                              $this->bank_out=unserialize($r['bank_out']);
				$this->factor_id = (int)$r['factor_id'];
                        }
                }
                public function add($user_id,$tarikh,$mablagh,$factor_id=-1)
                {
			$mysql = new mysql_class;
                        $sql = "insert into `pardakht` (`user_id`,`tarikh`,`mablagh`,`factor_id`) values ('$user_id','$tarikh','$mablagh','$factor_id')";
			$conn = $mysql->ex_sqlx($sql,FALSE);
                        $out = $mysql->insert_id($conn);
			$mysql->close($conn);
                        return($out);
                }
		public function getBarcode($id = -1)
		{
			$id = (int)$id;
			if($id <= 0)
				$id = $this->id;
			$out = pow($id,2) + 10000;
			$out = dechex($out);
			return($out);
		}
		public function barcode($barcode)
		{
			$barcode = hexdec($barcode);
			$out = sqrt($barcode - 10000);
			if($out % 1 != 0)
				$out = 0;
			return($out);
		}
	}
?>
