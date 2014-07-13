<?php
	class kala_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `kala` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->name=$r['name'];
					$this->ghimat=$r['ghimat'];
					$this->pic=$r['pic'];
					$this->thumb=$r['thumb'];
					$this->mojoodi=$r['mojoodi'];
				}
			}
		}
		public function loadKalas()
		{
			$out = array();
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from kala order by name",$q);
			foreach($q as $r)
				$out[] = array('id'=>(int)$r['id'],'name'=>$r['name'],'thumb'=>(trim($r['thumb'])==''?'../img/kala/fast-food-512.png.png':trim($r['thumb'])),'pic'=>(trim($r['pic'])==''?'../img/kala/fast-food-512.png.png':trim($r['pic'])),'ghimat'=>(int)$r['ghimat'],'mojoodi'=>(int)$r['mojoodi']);
			return($out);
		}
	}
?>
