<?php
	include_once('../kernel.php');
	$my = new mysql_class;
	echo "select `numbers_tmp`.`id`,`numbers`.`group_id` from `numbers` left join `numbers_tmp` on (`numbers`.`mobiles`=`numbers_tmp`.`mobiles`) order by `numbers`.`mobiles`,`numbers`.`group_id`";
/*
	$my->ex_sql("select `numbers_tmp`.`id`,`numbers`.`group_id` from `numbers` left join `numbers_tmp` on (`numbers`.`mobiles`=`numbers_tmp`.`mobiles`) order by `numbers`.`mobiles`,`numbers`.`group_id`",$q);
	foreach($q as $r)
	{
		$v .= (($v!='')?',':'').' ('.$r['id'].','.$r['group_id'].') ';
	}
	$my->ex_sqlx("insert into group_numbers (numbers_id,group_id) values $v");
*/
?>
