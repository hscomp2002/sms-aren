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
	//die("user is:".$use_name);
        if(!$se->can_view)
                die($conf->access_deny);
	$permission=array();
	$cl2=new mysql_class;
	//die("id is".$user_id);
	$canWrite='never';
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
		if(in_array("log.php",$permission))
			$canWrite='limited';
		
	}
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
		$out = '<span class="notice pointer" onclick="loadProfile('.$inp.');" >پروفایل</span>';
		return($out);
	}
	function loadMali($id)
	{
		$main = isset($_REQUEST['main'])?'main=main&':'';
		$out = '<span class="msg pointer" onclick="loadCont(null,\'mali.php?user_id='.$id.'&'.$main.'\');" >گردش مالی</span>';
		return($out);
	}
	function addUser($gname,$table,$fields,$column)
	{
		$user = $fields['user'];
		$pass = $fields['pass'];
		$grop_id = (isset($fields['grop_id']))?$fields['grop_id']:$_REQUEST['grop_id'];
		$fname = $fields['fname'];
		$lname = $fields['lname'];
		$en = $fields['en'];
		$isEnable = $fields['isEnable'];
		$user_daste_id = $fields['user_daste_id'];
		$mysql = new mysql_class;
                $ln = $mysql->ex_sqlx("insert into `user` (`user`,`pass`,`grop_id`,`fname`,`lname`,`user_daste_id`,`en`,`isEnable`) values ('$user','".(md5($pass))."','$grop_id','$fname','$lname','$user_daste_id',$en,$isEnable)",FALSE);
		$user_id = $mysql->insert_id($ln);
		$mysql->close($ln);
		$mysql->ex_sqlx("insert into `profile` (`user_id`) values ($user_id)");
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
	function delUser($table,$id,$gname)
	{
		$user_prof = new profile_class($id);
		if(isset($user_prof->etebar) &&(int)$user_prof->etebar==0)
		{
			$my = new mysql_class;
			$my->ex_sqlx("delete from `$table` where `id`=$id");
			$out = TRUE;
		}
		else
			$out='FALSE|این کاربر اعتبار مالی دارد';
		return($out);
	}
	function loadAcc($inp)
	{
		$div="<div class='msg pointer' onclick='loadAcc($inp);' >دسترسی </div>";
		return($div);
	}
	function tarikhBack($inp)
	{
		return(audit_class::hamed_pdateBack($inp));
	}
	function tarikh($inp)
	{
		return($inp!='0000-00-00 00:00:00' ? jdate('H:i  Y/m/d',strtotime($inp)):'نامعلوم');
	}
	function  load_usname($inp)
	{
		if($inp>0)
		{
			$my=new user_class($inp);
			$name='کاربر با این مشخصات موجود نیست.';
	 		if(isset($my))
				$name=$my->fname.' ' .$my->lname;
			return($name);
		}
		else
			return('نامعلوم');
	}
        $gname = "gname_user";
	$isMain = isset($_REQUEST['main'])?TRUE:FALSE;
	$input =array($gname=>array('table'=>'log','div'=>'main_div_log'));
        $xgrid = new xgrid($input);
	if($isMain)
		$xgrid->eRequest[$gname] = array('main'=>'main');
	$xgrid->whereClause[$gname] = " 1=1 order by `regdate` DESC ";
	$xgrid->column[$gname][0]['name'] = '';
	$xgrid->column[$gname][1]['name'] = 'کاربر';
	$xgrid->column[$gname][1]['cfunction'] =array('load_usname');
	$xgrid->column[$gname][2]['name'] = 'تاریخ';
	$xgrid->column[$gname][2]['cfunction'] = array('tarikh','tarikhBack');
	$xgrid->column[$gname][2]['search'] = 'dateValue';
	$xgrid->column[$gname][3]['name'] = '';
	$xgrid->column[$gname][4]['name'] = 'توضیحات';
	$xgrid->column[$gname][5]['name'] = '';
	
	//if($canWrite=='all' || $canWrite=='limited')
       	//	$xgrid->canEdit[$gname] = TRUE;
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
<div id="main_div_log" dir="rtl"></div>
