<?php
	class menu_table_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `menu_table` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->html_id=$r['html_id'];
					$this->name=$r['name'];
					$this->img=$r['img'];
					$this->link_address=$r['link_address'];
					$this->se_key=$r['se_key'];
					$this->order=$r['order'];
					$this->help_address=$r['help_address'];
					$this->position=$r['position'];
					$this->father=$r['father'];
				}
			}
		}
		public function loadMainMenu($user_id)
		{
			$out ='';
			$mysql=new mysql_class;
			$mysql->ex_sql("select * from `menu_table` ",$q);
				foreach($q as $r)
				{
					$firstMenu = $r['link_address'];
					
					$name = $r['name'];
					$out .="<button onclick='loadCont(this,$firstMenu);'>$name</button>";

				}
			
			return $out;
		}
	}
?>
