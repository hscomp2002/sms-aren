<?php
	class numbers_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `numbers` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->mobiles=$r['mobiles'];
					$this->names=$r['names'];
					$this->group_id=$r['group_id'];
				}
			}
		}
                public function number_exist($number)
                {
                    $out = array();
                    $my = new mysql_class;
                    $my->ex_sql("select id from `numbers` where `mobiles`='$number'", $q);
                    foreach($q as $r)
                        $out[]=$r['id'];
                    return($out);
                }
	}
?>