<?php
	class htmlGenerator
	{
		public $notHtml = array();
		public function __construct($table,$id=-1)
		{
			$my = new mysql_class;
			$my->oldSql = TRUE;
			$my->directQuery("SHOW FULL COLUMNS FROM  `$table`",$q);
			$tmp=array();
			while($r = mysql_fetch_array($q))
			{
				if($r['Comment']!='')
				{
					$val = '';
					if($id>0)
					{
						$my->oldSql = FALSE;
						$my->ex_sql("select `".$r['Field']."` from `$table` where `id`=$id",$qp);
						if(isset($qp[0]))
							$val = $qp[0][$r['Field']];
					}
					$tmp[]=array('name'=>$r['Field'],'comment'=>$r['Comment'],'val'=>$val,'type'=>$r['Type']);
				}
			}
			$this->fields = $tmp;
		}
		public function getHtml($inp,$class='')
		{
			$out='<table width="100%" class="'.$class.'" ><tr>';
			$i=0;
			foreach($this->fields as $n)
			{
				if(!in_array($n['name'],$this->notHtml))
				{
					$i++;
					if($i%$inp==0)
						$out .='</tr><tr>';
					$class_inp='';
					if($n['type']=='datetime' || $n['type']=='timestamo')
					{
						if($n['val']!='0000-00-00 00:00:00')
							$n['val']=jdate("Y/m/d",strtotime($n['val']));
						else
							$n['val']='';
							$class_inp='dateValue';
					}
					$out .= '<td align="left" >'.$n['comment'].':</td><td align="right" ><input type="text" class="regdate '.$class_inp.'" value="'.$n['val'].'" id="'.$n['name'].'" placeholder="'.$n['comment'].'" ></td>';
				}
			}
			$out .= '</tr></table>';
			return($out);
		}
	}
?>
