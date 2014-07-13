<?php
	class access_class
	{
		public $id=-1;
		public $group_id=-1;
		public $page_name="";
		public function __construct($id=-1)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `access` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$this->id=$q['id'];
				$this->group_id=$q['group_id'];
				$this->page_name=$q['page_name'];
			}
		}
		public function loadByGroup($grp_id,$is_group = 1)
		{
			$out = array();
			$mysql = new mysql_class;
			$grp_id = (int)$grp_id;
			$mysql->ex_sql("select `id`,`page_name` from `access` where `is_group` = $is_group and `group_id` = $grp_id",$q);
			foreach($q as $r)
				$out[(int)$r['id']] = $r['page_name'];
			return($out);
		}
	}
?>
