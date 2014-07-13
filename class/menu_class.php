<?php
	class menu_class
	{
		public $menu = array();
		public function __construct($userName,$se)
		{
			$se_key = '';
			foreach($se->allDetails as $frase)
				if(strpos($frase,'se_key')===0)
				{
					$tmp = explode('=',$frase);
					$se_key = $tmp[1];
				}
			$out = array(array("name"=>'صفحه اصلی','url'=>'index.php'));
			if(trim($se_key)!='')
			{
				$my = new mysql_class;
				$my->ex_sql("select name,link_address from menu_table where se_key = '$se_key' order by `order`",$q);
				foreach($q as $r)
					$out[] = array('name'=>$r['name'],'url'=>$r['link_address']);
			}
			$out[] = array("name"=>'خروج','url'=>'admin_login.php');
			$this->menu = $out;
		}
	}
?>
