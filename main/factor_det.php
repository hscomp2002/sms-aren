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
	function loadKala($id)
	{
		$kal = new kala_class($id);
		return(isset($kal->id)?$kal->name:'--');
	}
	$gname = "gname_factor_det";
	$factor_id = isset($_REQUEST['fac_id'])?(int)$_REQUEST['fac_id']:-1;
	$factor = new factor_class($factor_id);
	$input =array($gname=>array('table'=>'factor_det','div'=>'main_div_factor_det'));
        $xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] ="factor_id=$factor_id";
	$xgrid->eRequest[$gname]=array('fac_id'=>$factor_id);
	$xgrid->column[$gname][0]['name']='';
	$xgrid->column[$gname][1]['name']='';
	$xgrid->column[$gname][2]['name']='محصول';
	$xgrid->column[$gname][2]['clist']=columnListLoader('kala');
	$xgrid->column[$gname][3]['name']='تعداد';
	$xgrid->column[$gname][4]['name']='قیمت';
	if($se->detailAuth('all'))
	{
		$xgrid->canAdd[$gname]= TRUE;
		$xgrid->canEdit[$gname]= TRUE;
		$xgrid->canDelete[$gname] = TRUE;
	}
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
<script src="../js/jquery.printElement.min.js" >
</script>
<div style="font-family:tahoma;" >
	<?php echo isset($factor->id)?'اشتراک: '.$cl1->eshterak.'  شماره سیستم:'.$factor->id.' شماره دستی:'.$factor->shomare.' جمع مبلغ '.factor_class::getMablagh($factor->id) :'';  ?>
</div>
<div id="main_div_factor_det"></div>
<div id="print_div" align="right" style="display:none;" >
	<?php 
		echo isset($factor->id)?'اشتراک: '.$cl1->eshterak.'<br/>  شماره سیستم:'.$factor->id.'<br/> شماره دستی:'.$factor->shomare.' <br/> جمع مبلغ '.factor_class::getMablagh($factor->id) :'';
		$my = new mysql_class;
		$tmp='';
		$my->ex_sql("select * from factor_det where factor_id=$factor_id",$q);
		foreach($q as $r)
			$tmp.='<br/>'.loadKala($r['kala_id']);
		echo $tmp;
	?>
</div>
<div id="pr_div" class="pointer" onclick="$('#print_div').show();$('#print_div').printElement();$('#print_div').hide();" >
	چاپ
</div>
