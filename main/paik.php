<?php
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
	$user_id=(int)($_SESSION[$conf->app.'_user_id']);
	$cl1=new user_class($user_id);
	$use_name=$cl1->user;
        if(!$se->can_view)
                die($conf->access_deny);
	$permission=array();
	$cl2=new mysql_class;
	function addUser($gname,$table,$fields,$column)
	{
		return(TRUE);
		
	}
	function editUser($table,$id,$field,$val,$fn,$gname)
	{
		return(TRUE);
	}
	function delUser($table,$ids,$gname)
	{
		$ids_arr = explode(',',$ids);
		$out = TRUE;
		foreach($ids_arr as $id)
		{
			$user_prof = new profile_class($id);
			if(isset($user_prof->etebar) &&(int)$user_prof->etebar==0)
			{
				$my = new mysql_class;
				$my->ex_sqlx("delete from profile where user_id=$id");
				$my->ex_sqlx("delete from `$table` where `id`=$id");
			}
			else if(isset($user_prof->etebar) && (int)$user_prof->etebar>0)
				$out='FALSE|این کاربر اعتبار مالی دارد';
		}
		return($out);
	}
        $gname = "gname_user";
	$input =array($gname=>array('table'=>'paik','div'=>'main_div_paik'));
        $xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name']='';
	$xgrid->column[$gname][1]['name']='نام';
	$xgrid->canAdd[$gname]= TRUE;
	$xgrid->canEdit[$gname]= TRUE;
	$xgrid->canDelete[$gname] = TRUE;
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
<div id="main_div_paik"></div>
