<?php
	class group_numbers_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `group_numbers` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->numbers_id=$r['numbers_id'];
					$this->group_id=$r['group_id'];
				}
			}
		}
                public function add($grp_id,$number_id)
                {
                    $out = 'nok';
                    $my = new mysql_class;
                    $my->ex_sql("select id from group_numbers where numbers_id='$number_id' and group_id=$grp_id", $q);
                    if(count($q)>0)
                        $out = 'exist';
                    else
                    {
                        $my->ex_sqlx("insert into group_numbers (numbers_id,group_id) values ('$number_id',$grp_id)");
                        $out = "ok";
                    }
                    return($out);
                }
	}
?>