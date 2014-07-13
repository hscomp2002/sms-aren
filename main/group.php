<?php
	include_once("../kernel.php");
	function list_aza($inp)
	{
		$mysql = new mysql_class;
		$mysql->ex_sql("select count(`numbers_id`) as `cid` from  group_numbers join numbers on (numbers_id=numbers.id)  where `group_id`='$inp' ",$q);
		$count_aza = (int)$q[0]['cid'];
		$div="<center><div class='alert alert-danger' onclick='loadMenu(\"numbers.php?grp_id=$inp\");' style='cursor:pointer;' >مشاهده اعضا (".$count_aza.")</div></center>";
		return($div);
	}
        
	$gname = "gname_user";
	$input =array($gname=>array('table'=>'group','div'=>'main_div_group'));
    $xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name']='';
	$xgrid->column[$gname][1]['name']='نام';
	$xgrid->column[$gname][1]['search'] = 'text';
	$xgrid->column[$gname][2]=$xgrid->column[$gname][0];
	$xgrid->column[$gname][2]['name']='اعضا';
	$xgrid->column[$gname][2]['cfunction'] = array('list_aza');
	$xgrid->column[$gname][2]['access'] = 'a';
      
	$xgrid->canAdd[$gname]= TRUE;
	$xgrid->canEdit[$gname]= TRUE;
	$xgrid->canDelete[$gname] = FALSE;
       	$out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
        $(document).ready(function(){
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
</script>
<div id="main_div_group"></div>
