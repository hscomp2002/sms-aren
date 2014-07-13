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
	function loadUsers($id)
	{
		$user = new user_class($id);
		return(isset($user->user)?$user->lname.' '.$user->fname.'('.$user->user.')':'---');
	}
	function tarikh($inp)
	{
		$out = ($inp=='0000-00-00 00:00:00' || $inp=='')? '':jdate("Y/m/d",strtotime($inp));
		return($out);
	}
	function utarikh($inp)
	{
		$out =hamed_pdateBack2($inp).' 23:59:59';
		return($out);
	}
        function loadFactor_det($inp)
	{
		$out ='<button class="pointer notice"  onclick="loadMenu(\'factor_det.php?fac_id='.$inp.'\');"  >جزئیات</button>';
		return($out);
	}
	$gname = "gname_factor";
	$input =array($gname=>array('table'=>'factor','div'=>'main_div_factor'));
	$user_id = (int)$_SESSION[$conf->app.'_user_id'];
        $xgrid = new xgrid($input);
        if($se->detailAuth('all'))
        {
		$xgrid->whereClause[$gname]='1=1 order by tarikh desc';
		$xgrid->column[$gname][1]['search']='list';
		$xgrid->column[$gname][1]['searchDetails']=columnListLoader('user',array('id','lname','fname'));
		$xgrid->column[$gname][4]['search']='list';
		$xgrid->column[$gname][4]['searchDetails']=columnListLoader('paik',array('id','name'));
		$xgrid->canAdd[$gname]= TRUE;
		$xgrid->canEdit[$gname]= TRUE;
		$xgrid->canDelete[$gname] = TRUE;
	}
	else
		$xgrid->whereClause[$gname]="user_id=$user_id order by tarikh desc";
	//$xgrid->echoQuery=TRUE;
	$xgrid->column[$gname][0]['name']='شماره سیستم';
	$xgrid->column[$gname][1]['name']='سفارش دهنده';
	$xgrid->column[$gname][1]['clist'] = columnListLoader('user',array('id','lname','fname'));

	$xgrid->column[$gname][2]['name']='شماره';
	$xgrid->column[$gname][2]['search']='text';
	$xgrid->column[$gname][3]['name']='تاریخ';
	$xgrid->column[$gname][3]['cfunction'] = array('tarikh','utarikh');
	$xgrid->column[$gname][4]['name']='پیک';
	$xgrid->column[$gname][4]['clist'] = columnListLoader('paik',array('id','name'));
	

	$xgrid->column[$gname][5]['name']='وضعیت';
	$xgrid->column[$gname][5]['clist']=array('0'=>'ارسال نشده','2'=>'در راه','3'=>'به مقصد رسیده');
	$xgrid->column[$gname][5]['search']='list';
	$xgrid->column[$gname][5]['searchDetails']=array(-1=>'همه','0'=>'ارسال نشده','2'=>'در راه','3'=>'به مقصد رسیده');

	$xgrid->column[$gname][6]['name']='پرداخت شده';
	$xgrid->column[$gname][6]['clist']=array('0'=>'خیر','1'=>'بلی');
	$xgrid->column[$gname][6]['search']='list';
	$xgrid->column[$gname][6]['searchDetails']=array(-1=>'همه','0'=>'خیر','1'=>'بلی');

	$xgrid->column[$gname][7] = $xgrid->column[$gname][0];
	$xgrid->column[$gname][7]['name']='جزئیات';
	$xgrid->column[$gname][7]['cfunction']=array('loadFactor_det');
	$xgrid->column[$gname][7]['access']='q';

	
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
<div id="main_div_factor"></div>