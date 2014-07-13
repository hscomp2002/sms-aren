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
	//die("user  file is running:".$use_name);
	//die("all is:".$se->detailAuth('all') ."  dowrite is:". $se->detailAuth('doWrite'));
        if(!$se->can_view)
                die($conf->access_deny);
	
	//var_dump($se);
	//die();
	$permission=array();
	$cl2=new mysql_class;
	//die("id is".$user_id);
	/*$canWrite='never';
	if($user_id==1)
		$canWrite='all';
	else
	{
		$cl2->ex_sql("select * from access_det where frase='doWrite'",$res2);
		if(count($res2)>0)
		{
		
			for($i=0;$i<count($res2);$i++)
			{
		
				$acc_id=$res2[$i]['acc_id'];
				$cl2->ex_sql("select  `page_name` from access where id='$acc_id' and group_id='$user_id'",$res3);
				if(count($res3)>0)
				{
					$p_name=$res3[0]['page_name'];
					$permission[]=$p_name;

				}

			}
		}
		if(in_array("user.php",$permission))
			$canWrite='limited';
		$cl2->ex_sql("select * from access_det where frase='perm' and acc_id='$user_id'",$res2);
		if(count($res2)>0)
		{
			$canWrite='all';
		}
		
	}*/
	//die("can is:".$canWrite);
	function showPass($pass)
	{
		return('&nbsp;');
	}
	function changePass($pass)
	{
		return(md5($pass));
	}
	function loadProfile($inp)
	{
		global $canWrite;
		if($canWrite=='never')
			$out = '<span class="notice pointer" >پروفایل</span>';
		else
			$out = '<span class="notice pointer" onclick="loadProfile('.$inp.');" >پروفایل</span>';
		return($out);
	}
	function loadMali($id)
	{
		global $canWrite;
		$main = isset($_REQUEST['main'])?'main=main&':'';
		if($canWrite=='never')
			$out = '<span class="msg pointer" >گردش مالی</span>';
		else
			$out = '<span class="msg pointer" onclick="loadCont(null,\'mali.php?user_id='.$id.'&'.$main.'\');" >گردش مالی</span>';
		return($out);
	}
	function addUser($gname,$table,$fields,$column)
	{
		$conf = new conf;
		$user = $fields['user'];
		$pass = $fields['pass'];
		$grop_id = (isset($fields['group_id']))?$fields['group_id']:$_REQUEST['group_id'];
		$fname = $fields['fname'];
		$lname = $fields['lname'];
		$isEnable = $fields['isEnable'];
		$mysql = new mysql_class;
                $ln = $mysql->ex_sqlx("insert into `user` (`user`,`pass`,`fname`,`lname`,`group_id`,`en`,`isEnable`) values ('$user','".(md5($pass))."','$fname','$lname','$grop_id',1,$isEnable)",FALSE);
		$user_id = $mysql->insert_id($ln);
		$mysql->close($ln);
		return(TRUE);
		
	}
	function editUser($table,$id,$field,$val,$fn,$gname)
	{
		$id = (int)$id;
		if($field == 'pass')
			$val = md5($val);
		$mysql = new mysql_class;
		$mysql->ex_sqlx("update `$table` set `$field` = '$val' where `id` = $id");
		return(TRUE);
	}
	function delUser($table,$ids,$gname)
	{
		$ids_arr = explode(',',$ids);
		$out = TRUE;
		foreach($ids_arr as $id)
		{
			$user_prof = new profile_class($id);
			$my = new mysql_class;
			$my->ex_sqlx("update from `$table` set en=0 where `id`=$id");
		}
		return($out);
	}
	function loadAcc($inp)
	{
		
		$div="<div class='msg pointer' onclick='loadAcc($inp);' >دسترسی </div>";
		return($div);
	}
        $gname = "gname_user";
	$isMain = isset($_REQUEST['main'])?TRUE:FALSE;
	$input =array($gname=>array('table'=>'user','div'=>'main_div_user'));
        $xgrid = new xgrid($input);
	if($isMain)
		$xgrid->eRequest[$gname] = array('main'=>'main');
	
	if($se->detailAuth('all') || $se->detailAuth('doWrite'))
	{
		$wer = "`user` <> 'mehrdad' order by id desc";
		$xgrid->canAdd[$gname] = TRUE;
		$xgrid->canDelete[$gname] = TRUE;
		$xgrid->canEdit[$gname] = TRUE;
		$xgrid->column[$gname][2]['name'] = 'گذرواژه';
		$xgrid->column[$gname][2]['cfunction'] = array('showPass','changePass');
	//	$xgrid->column[$gname][5]['name'] = 'نوع';
	//	$xgrid->column[$gname][5]['clist'] = columnListLoader('grop');
		$xgrid->column[$gname][6]['name'] = '';
		$xgrid->column[$gname][7]['name'] = 'وضعیت';
		$xgrid->column[$gname][7]['clist'] = array(0=>'عدم ورود',1=>'قابل ورود');
	}
	else
	{
		$wer = "id=$user_id" ;
		$xgrid->column[$gname][2]['name']='';
		$xgrid->column[$gname][5]['name'] = '';
		$xgrid->column[$gname][6]['name'] = '';
		$xgrid->column[$gname][7]['name'] = '';
	}
	//$xgrid->echoQuery=TRUE;
	$xgrid->whereClause[$gname] = $wer;
	$xgrid->column[$gname][0]['name'] = '';
	$xgrid->column[$gname][1]['name'] = 'کلمه کاربری';
	$xgrid->column[$gname][1]['search'] = 'text';
	$xgrid->column[$gname][3]['name'] = 'نام';
	$xgrid->column[$gname][3]['search'] = 'text';
	$xgrid->column[$gname][4]['name'] = 'نام خانوادگی';
	$xgrid->column[$gname][4]['search'] = 'text';
	$xgrid->column[$gname][5]['name'] = 'گروه کاربری';
	$xgrid->column[$gname][5]['search'] = 'list';
	$xgrid->column[$gname][5]['searchDetails'] = columnListLoader('grop');
	$xgrid->column[$gname][5]['clist'] = columnListLoader('grop');
	$xgrid->column[$gname][8]['name'] = 'تلفن همراه';
	$xgrid->column[$gname][9]['name'] = 'آدرس';
	$xgrid->column[$gname][10]['name'] = 'کد اشتراک';
	$xgrid->addFunction[$gname] = 'addUser';
	$xgrid->editFunction[$gname] = 'editUser';
	$xgrid->deleteFunction[$gname] = 'delUser';
       	$out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
        $(document).ready(function(){
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
	function loadProfile(user_id)
	{
		$("#body").html("<img src='../img/status_fb.gif' >");
                $("#body").load('profile.php?user_id='+user_id+'&');
	}
	function loadAcc(id)
	{
		$("#body").load('permission.php?user_id='+id);
	}
</script>
<div id="main_div_user"></div>