<?php
	include_once('../kernel.php');
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	$number1 = -1;
	$number2 = -1;
	if(isset($_SESSION[$conf->app.'_number1']) && !isset($_SESSION[$conf->app.'_user_id']))
	{
		$number1 = (int)$_SESSION[$conf->app.'_number1'];
		$number2 = (int)$_SESSION[$conf->app.'_number2'];
	}
	session_destroy();
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	if($conf->ipBlock === TRUE)
	{
		$ip = new iplist_class($_SERVER['REMOTE_ADDR']);
		if(!$ip->addIP())
			die($conf->restricted);
	}
	$_SESSION[$conf->app."_login"] = "1";
	$content = '';
	//if ((isset($_REQUEST["stat"])) && ($_REQUEST["stat"]=="wrong_user"))
		//echo("<script>alert('نام کاربری و یا رمز عبور اشتباه می باشد');</script>");
	if(isset($_REQUEST['user']) && isset($_REQUEST['pass']) && isset($_REQUEST['jam']))
	{
		$pass=$_REQUEST['pass'];
		$user=$_REQUEST['user'];
		$ref = (isset($_REQUEST['ref']) && trim($_REQUEST['ref'])!='')?trim($_REQUEST['ref']):'';
		$jam = (int)$_REQUEST['jam'];
		$jam_real = $number1+$number2;
		if($conf->ipBlock === TRUE)
			$ip = new iplist_class($_SERVER['REMOTE_ADDR']);
		$mysql = new mysql_class;
		$mysql->enableCache = FALSE;
		$mysql->oldSql = TRUE;
		$mysql->directQuery("select `id`,`pass` from `user` where `user` = '".$user."'",$q);
		if ($r = $mysql->fetch_array($q))
		{
			//echo $pass;
			if($pass == $r["pass"] && $jam==$jam_real)
			{
				//$se = security_class::auth((int)$r["id"]);
				$_SESSION[$conf->app.'_user_id'] = (int)$r["id"];
				//$_SESSION[$conf->app.'_group_id'] = (int)$r["group_id"];
				if($conf->ipBlock === TRUE)
					$ip->removeIP();
				//user_class::addLoginCount((int)$r["id"]);
				$out = ($ref=='')?"index.php":$ref;
				die("<script> window.location=\"$out\"; </script>");
			}
			else if($pass != $r["pass"])
			{
				if($conf->ipBlock === TRUE)
					$ip->addIP(TRUE);
				//die('select `id`,`pass`,`group_id` from `user` where `user` = '.$user.'<br/>'.$pass);
			}
			else
			{
				//var_dump($_SESSION);
				//die('number1 = '.$number1.'+number2='.$number2.'=='.$jam.'##'.$jam_real);
			}
		}
		else
		{
			if($conf->ipBlock === TRUE)
				$ip->addIP(TRUE);
		}
	}
?>
<html>
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <title> 
			<?php 
				echo $conf->title; ?>
	        </title>
		<script src="../js/jquery.min.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<!--<script src="../js/jquery.corner.js"></script>-->
		<script src="../js/md5.js"></script>
	<!--	<link type="text/css" href="../css/style.css" rel="stylesheet" />	-->
		<style>
			body
			{background-color:#1c2e43;background-size:100% 900px;background-repeat:repeat-x;background-image:-moz-linear-gradient(#325376,#1c2e43 900px);background-image:-webkit-gradient(linear,0 0,0 100%,from(#325376),to(#1c2e43));background-image:-o-linear-gradient(#325376,#1c2e43 900px);background-image:-ms-linear-gradient(#325376,#1c2e43 900px);-cp-background-image:linear-gradient(#325376,#1c2e43 900px);background-image:linear-gradient(#325376,#1c2e43 900px);font-family:tahoma,arial,sans-serif;direction:rtl;}
			.textbox
			{width:254px;height:22px;margin:5px 0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-moz-box-shadow:inset 0 10px 10px#C1E5EF;-webkit-box-shadow:inset 0 10px 10px #C1E5EF;box-shadow:inset 0 10px 10px #C1E5EF;border:2px solid #024257;color:#000;font-family:tahoma;font-size:13px;height:30px;}
			.login_but
			{
				color:#fff;
				background: #02526f url("../img/button_bg.png");
				font-family:tahoma;
				width:100px;
				font-weight:bold;
				border: 1px solid #333;
				height: 25px;
				font-size:11px;
				cursor:pointer;
			}
			#login_tb
			{
				font-weight:bold;
				font-family:tahoma;
				font-size:12px;
				color:#000000;
			}
			#all_div
			{
				width:360px;
				height:330px;
				border: 0px solid #62bad7;
				float:right;
				margin-top:70px;
				margin-left:auto;
				margin-right:110px;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				-o-border-radius: 5px;
				-ms-border-radius: 5px;
				border-radius: 5px;
			}
			#logo_div
			{
				/*background:#cccccc;*/
				margin:3px;
				width:350px;
				height:75px;
				border: 0px solid #333;
			}
			#login_div
			{
				background:url("../img/light.png");
				margin:3px;
				width:350px;
				height:270px;
				border: 1px solid #999;
			}
			#login_tb
			{
				color:#fff;
				margin-top: 15px;
				height:200px;
			}
			#login_tb td
			{
				text-align:right;
			}
			.shadow
			{
			    -moz-box-shadow: 3px 3px 4px #999;
			    -webkit-box-shadow: 3px 3px 4px #999;
			    box-shadow: 3px 3px 4px #999; /* For IE 8 */
			    -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#999999')"; /* For IE 5.5 - 7 */
			    filter: progid:DXImageTransform.Microsoft.Shadow(Strength = 4, Direction = 135, Color = '#999999');
			}
			a:link {color:#f3ea59;}
			a:visited {color:#f3ea59;}
			.login-whisp{
				width:950px;
				height:600px;
				/*position:absolute;*/
				background-size:100% 100%;
				/*background:url("../img/back.png") no-repeat center;*/
			}
		</style>	
		<script language="javascript">
			$(document).ready(function(){
				$("#menu").empty();
				if($("div").length>2)
					$($("div")[0]).empty();
				jQuery.fn.center = function () {
				    this.css("position","absolute");
				    this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + 
								                $(window).scrollTop()) + "px");
				    this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + 
								                $(window).scrollLeft()) + "px");
				    return this;
				}
				
				//$("#login_div").corner();
				//$(".login_but").corner("round 3px").addClass('shadow');
				$("#login_div").center();
			});
			function onEnterpress(e)

				{
				    var KeyPress  ;
				    if(e && e.which)
				    {
					e = e;		     
					KeyPress = e.which ;
				    }

				    else
				    {
					e = event;
					KeyPress = e.keyCode;
				    }
				    if(KeyPress == 13)
				    {
					document.getElementById('frm1').submit();
					return false     
				    }
				    else
				    {
					return true
				    }

				}
			function guistLogin()
			{
				document.getElementById('frm1').action='index_ozviat.php';
				document.getElementById('frm1').submit();
			}
			function vorood()
			{
				document.getElementById('pass').value = hex_md5(document.getElementById('pass').value);
				if(document.getElementById('uname').value!='' && document.getElementById('pass').value!='' && $("#jam").val()!='')
				{
					document.getElementById('frm1').submit();
				}
				else
					alert('لطفاً نام کاربری و رمز عبور و مجموع اعداد را وارد کنید');
			}
			function reg()
			{
				window.location="reg.php";
			}
		</script>
	</head>
	<body id="bd1" align="center" >
		<div style="height:600px;">
		<form action="admin_login.php" id="frm1" method="post">
			<input type="hidden" id="ref" name="ref" value="<?php echo(isset($_REQUEST['ref'])?$_REQUEST['ref']:''); ?>" />
				<div id="login_div"  align="center">
					<table id="login_tb" >
						<tr>
							<td>
								 تلفن ثابت :
							</td>
						</tr>
						<tr>
							<td>
								<input name="user" id="uname" type="text" value="" class="textbox" onkeydown="onEnterpress(event);" placeholder="نام کاربری خود را وارد نمایید">
							</td>
						</tr>
						<tr>
							<td>
								رمز عبور:
							</td>
						</tr>
						<tr>
							<td>
								<input name="pass" id="pass" type="password" value="" class="textbox"  onkeydown="onEnterpress(event);" placeholder="رمز عبور خود را وارد نمایید">
							</td>
						</tr>
						<tr>
							<td>
								<table style="color:#ffffff;" >
									<tr>
										<td>
											<img style="margin:5px" src='number1.php' >
										</td>
										<td>
											+
										</td>
										<td>
											<img style="margin:5px" src='number2.php' >
										</td>
										<td>
											=
										</td>
										<td>
											<input type="text" id="jam" name="jam" class="textbox" style="width:70px;" placeholder="مجموع" autocomplete="off" >
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<table width="100%">
									<tr>
										<td align="center" valign="buttom" width="33%">
											<input type="button" class="login_but" onclick="vorood();" value="ورود" />			
										</td>
										<td>
								                        <input type="button" class="login_but" onclick="reg();" value="عضویت" />
							                        </td>
									</tr>							
								</table>
							</td>
						</tr>
						<tr>
                                                        
						</tr>
				
					</table>
				</div>
		</form>
		</div>
		<script language="javascript">
			document.getElementById("uname").focus();
		</script>
	</body>
</html>