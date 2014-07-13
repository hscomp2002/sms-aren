<?php
	include_once("../kernel.php");
	include_once("../simplejson.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	if(isset($_SESSION[$conf->app.'_user_id']))
                die('<script>window.location="index.php";</script>');//die($conf->access_deny);
	if(isset($_REQUEST['user']))
	{
		$out = -1;
		$my = new mysql_class;
		$my->ex_sql("select id from user where user = '".$_REQUEST['user']."'",$q);
		if(isset($q[0]))
			$out = -2;
		else
		{
			$ln = $my->ex_sqlx("insert into user (user,pass,fname,lname,group_id,en,isEnable,cell,address) values ('".$_REQUEST['user']."','".$_REQUEST['pass']."','".$_REQUEST['fname']."','".$_REQUEST['lname']."',27,1,1,'".$_REQUEST['cell']."','".$_REQUEST['address']."')",FALSE);
			$out = $my->insert_id($ln);
			$my->close($ln);
		}
		die("$out");
	}
?>
<!doctype html>
<html lang="fa">
	<head>
		<meta charset="utf-8">
		<title><?php echo $conf->title;  ?></title>
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<link rel="stylesheet" href="../css/style.css">
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
		<script src="../js/md5.js"></script>
		<script type="text/javascript">
			function test()
			{
				alert('alala');
			}
			function regUser()
			{
				var fname = $("#fname").val();
				var lname = $("#lname").val();
				var user = $("#user").val();
				var cell = $("#cell").val();
				var address = $("#address").val();
				var pass = hex_md5($("#pass").val());
				$.get("reg.php",{"fname":fname,"lname":lname,"user":user,"cell":cell,"address":address,"pass":pass},function(result){
					var id = parseInt(result,10);
					if(!isNaN(id) && id > 0)
					{
						alert('ثبت نام شما با موفقیت انجام گرفت لطفا در صفحه بعدی وارد شده و خرید نمایید.');
						window.location = "index.php";
					}
					else
						alert('شماره تماس شما قبلا ثبت گردیده. لطفا با ما تماس بگیرید تا راهنمایی کنیم');
				});
			}
		</script>
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
	</head>
	<body dir="rtl">
                <div>
			<table style="width:100%;" >
				<tr>
					<td style="width:80%;" >
                       				<img src="../img/header.jpg">
					</td>
					<td style="width:20%;" class="blue" >
						<div style="margin:10px;">
						</div>
					</td>
				</tr>
			</table>
                </div>
		<div align="center">
			<div style="width:1104px">
				<table width="100%">
					<tr>
						<td>
							<input id="fname"  placeholder="نام" />
						</td>
						<td>
							<input id="lname"  placeholder="نام خانوادگی" />
						</td>
						<td>
							<input id="user"  placeholder="تلفن ثابت" />
						</td>
						<td>
							<input id="cell"  placeholder="تلفن همراه" />
						</td>
						<td>
							<input id="pass"  placeholder="رمز عبور" />
						</td>
					</tr>
					<tr>
						<td colspan="5" style="text-align:center;">
							<textarea id="address"  cols="80" rows="10" placeholder="آدرس" ></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="text-align:center;">
							<button onclick="regUser();">ثبت</button>
							<button onclick="window.location='admin_login.php';">ورود</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>