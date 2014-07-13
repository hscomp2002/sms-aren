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
	$gname = "gname_factor_det_all";
	$input =array($gname=>array('table'=>'factor_det','div'=>'main_div_factor_det_all'));
        $xgrid = new xgrid($input);
	$xgrid->whereClause[$gname]='factor_id>0 order by factor_id desc';
	$xgrid->column[$gname][0]['name']='';
	$xgrid->column[$gname][1]['name']='شماره فاکتور سیستم';
	$xgrid->column[$gname][1]['search']='text';
	$xgrid->column[$gname][2]['name']='محصول';
	$kala_list = columnListLoader('kala');
	$xgrid->column[$gname][2]['clist']=$kala_list;
	$xgrid->column[$gname][2]['search']='list';
	$xgrid->column[$gname][2]['searchDetails']=$kala_list;
	$xgrid->column[$gname][3]['name']='تعداد';
	$xgrid->column[$gname][4]['name']='قیمت';
	$xgrid->canAdd[$gname]= TRUE;
	$xgrid->canEdit[$gname]= TRUE;
	$xgrid->canDelete[$gname] = TRUE;
       	$out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<!doctype html>
<html lang="fa">
	<head>
		<meta charset="utf-8">
		<META HTTP-EQUIV="REFRESH" CONTENT="60">
		<title><?php echo $conf->title;  ?></title>
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />
		<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" type="text/css" />
		<link rel="stylesheet" href="../css/colorpicker.css">
		<link rel="stylesheet" href="../css/xgrid.css">
                <link rel="stylesheet" type="text/css" media="all" href="../js/cal/skins/aqua/theme.css" title="Aqua" />
		<script src="../js/jquery.min.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<script src="../js/grid.js"></script>
		<script src="../js/date.js" ></script>
		<script src="../js/inc.js"></script>
		<script src="../js/colorpicker.js"></script>
		<script src="../js/md5.js"></script>
                <script type="text/javascript" src="../js/cal/jalali.js"></script>
                <script type="text/javascript" src="../js/cal/calendar.js"></script>
                <script type="text/javascript" src="../js/cal/calendar-setup.js"></script>
                <script type="text/javascript" src="../js/cal/lang/calendar-fa.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<style>
			button
			{
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				border:1px solid black;
				cursor : pointer;
			}
			button:hover
			{
				background-color:#fefefe;
			}
			.pointer
			{
				cursor:pointer;
			}
			.menu_item:hover,.menu_item_selected
			{
				background:yellow;
			}
		</style>
		<script type="text/javascript" >
			$(document).ready(function(){
				var args=<?php echo $xgrid->arg; ?>;
				intialGrid(args);
			});
			function reloader()
			{
				loadMenu('factor_det_all.php',null);
				setTimeout();
			}
		</script>
	</head>
	<body dir="rtl">
		<div style="font-family:tahoma;" >
			<?php echo isset($factor->id)?'شماره سیستم:'.$factor->id.' شماره دستی:'.$factor->shomare.' جمع مبلغ '.factor_class::getMablagh($factor->id) :'';  ?>
		</div>
		<div id="main_div_factor_det_all"></div>
	</body>
</html>
