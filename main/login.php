<?php
	include("../kernel.php");
	include("../simplejson.php");
	session_start();
	$loged = isset($_SESSION[$conf->app.'_user_id']);
	$befor = $loged;
	if($loged)
		unset($_SESSION[$conf->app.'_user_id']);
	function userAvailable($user)
	{
		$q = null;
		$my = new mysql_class;
		$my->ex_sql("select `id` from `user` where `user` = '$user'",$q);
		return(!isset($q[0]));
	}
	function mailAvailable($mail)
	{
		$my = new mysql_class;
		$my->ex_sql("select `id` from `profile` where `email` = '$mail'",$q);
		return(!isset($q[0]));
	}
	function loadCity()
	{
		$out = '<select id="city_id" class="regdata" style="font-family:tahoma;" >';
		$my = new mysql_class;
		$my->ex_sql("select * from `city` order by `name` ",$q);
		foreach($q as $r)
			$out.='<option value="'.$r['id'].'" >'.$r['name'].'</option>';
		$out .='</select>';
		return($out);
	}
	function loadCustomer_type($inp=-1)
	{
		$out = '<select id="customer_type_id" class="regdate" style="font-family:tahoma;" ><option value="-1" ></option>';
		$my = new mysql_class;
		$my->ex_sql("select * from `customer_type` order by `name` ",$q);
		foreach($q as $r)
			$out.='<option '.($inp==(int)$r['id']? 'selected="selected"':'').' value="'.$r['id'].'" >'.$r['name'].'</option>';
		$out .='</select>';
		return($out);
	}
	$my = new mysql_class;
	if(isset($_REQUEST['regCode']))
	{
		$out = 'noFound';
		$uid = audit_class::codeToId($_REQUEST['regCode']);
		$usr = new user_class((int)$uid);
		$poro = new profile_class((int)$uid);
		if(isset($usr->en) && $usr->en == 0)
		{
			$my->ex_sqlx("update `user` set `en` = 1 where `id` = $uid");
			$out = 'enabled';
			if(isset($poro->mob) && trim($poro->mob)!='')
			{
				$act_sms = (trim($conf->after_active_sms)!='')?trim($conf->after_active_sms):'عضویت شما فعال گردید.';
				sms_class::send($act_sms,$poro->mob);
			}
			$_SESSION[$conf->app.'_user_id'] = $usr->id;
		}
		else if(isset($usr->en))
			$out = 'alreadyEnabled';
		die($out);
	}
	if(isset($_REQUEST['user']))
	{
		$out = 'false';
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['pass'];
		$my->ex_sql("select `id`,`pass`,`company_id` from `user` where `user` = '$user' and `en`=1 and `isEnable`=1 ",$q);
		if(isset($q[0]))
		{
			$out = ($q[0]['pass'] == $pass && (int)$q[0]['company_id']==(int)$conf->company_id)?'true':'false';
			if($out == 'true')
				$_SESSION[$conf->app.'_user_id'] = $q[0]['id'];
		}
		die($out);
	}
	if(isset($_REQUEST['checkUser']))
		die(userAvailable($_REQUEST['checkUser'])?'true':'false');
	if(isset($_REQUEST['checkMail']))
		die(mailAvailable($_REQUEST['checkMail'])?'true':'false');
	if(isset($_REQUEST['reset']))
	{
		$fuser = $_REQUEST['reset'];
		die(user_class::resetPassword($fuser)?'true':'false');
	}
	if(isset($_REQUEST['fname']))
	{
		$out = 'false';
		if(userAvailable($_REQUEST['email']))
		{
			$pre_user = (int)$_REQUEST['pre_user'];
			$codeOzviat = (int)$_REQUEST['codeOzviat'];
			$pre_user_id = $pre_user==-4 ? $codeOzviat: $pre_user ;
			$fname = $_REQUEST['fname'];
			$lname = $_REQUEST['lname'];
			$user = $_REQUEST['user_reg'];
			$email = $_REQUEST['email'];
			$mob = $_REQUEST['mob'];
			$tel = $_REQUEST['tel'];
			$tarikh_tavalod = $_REQUEST['tarikh_tavalod']==''? '0000-00-00 00:00:00':hamed_pdateBack2($_REQUEST['tarikh_tavalod']);
			$city_id = $_REQUEST['city_id'];
			$addr = $_REQUEST['addr'];
			$pass = $_REQUEST['npass'];
			$codePosti = $_REQUEST['codePosti'];
			$jender = (int)$_REQUEST['jender'];
			$ln = $my->ex_sqlx("insert into `user` (`fname`,`lname`,`user`,`pass`,`company_id`) values ('$fname','$lname','$user','$pass',".((int)$conf->company_id).")",FALSE);
			$user_id = $my->insert_id($ln);
			$my->close($ln);
			$my->ex_sqlx("insert into `profile` (`mob`,`tel`, `pre_user_id`,`addr`,`user_id`,`codeposti`,`city_id`,`tarikh_tavalod`,`email`,`user_daste_id`,`jender`) values ('$mob','$tel','$pre_user_id','$addr',$user_id,'$codePosti',$city_id,'$tarikh_tavalod','$email',3,$jender)");
			//$_SESSION[$conf->app.'_user_id'] = $user_id;
			$act_code = audit_class::idToCode($user_id);
			$text =(trim($conf->active_email)!='')?$conf->active_email:'کاربر '.$fname.' '.$lname.' به سامانه ورود دارما خوش آمدید کد فعال سازی شما <br/><a href="http://darma.ir/sale/main/?regCode='.audit_class::idToCode($user_id).'&" target="_blank">'.audit_class::idToCode($user_id).'</a><br>است.<br/>';
			$text = str_replace("#act_code#",'<a href="http://darma.ir/sale/main/?regCode='.$act_code.'&" >'.$act_code.'</a>',$text);
			$text = str_replace("#user#",$user,$text);
			$text = str_replace("#fname#",$fname,$text);
			$text = str_replace("#lname#",$lname,$text);
			$text = str_replace("#jender#",((int)$jender==1)?'آقای':'خانم',$text);
			$act_sms = (trim($conf->active_sms)!='')?trim($conf->active_sms):'کد رهگیری شما '."\n#act_code#\n".'می باشد';
			$act_sms = str_replace("#act_code#",$act_code,$act_sms);
			$act_sms = str_replace("#user#",$user,$act_sms);
			$act_sms = str_replace("#fname#",$fname,$act_sms);
			$act_sms = str_replace("#lname#",$lname,$act_sms);
			$act_sms = str_replace("#jender#",((int)$jender==1)?'آقای':'خانم',$act_sms);
			sms_class::send($act_sms,$mob);
			$mail = new email_class($email,'سامانه فروش دارما',$text);
			$out = 'true';
		}
		die($out);
	}
	$q = null;
	$maghta = "<option value='-1'>هیچکدام</option>";
	$my = new mysql_class;
	$my->ex_sql("select `id`,`name` from `maghta` order by `name`",$q);
	foreach($q as $r)
		$maghta .= "<option value='".$r['id']."'>".$r['name']."</option>";
	$year_tmp = '';
	$month_tmp = '';
	$day_tmp = '';
	$today_year = perToEnNums(jdate("Y"));
	$today_month = perToEnNums(jdate("m"));
	$today_day = perToEnNums(jdate("d"));
	for($i = 1300;$i <= $today_year;$i++)
		$year_tmp .= '<option value="'.$i.'" >'.enToPerNums($i).'</option>';
	for($i = 1;$i <= 12;$i++)
		$month_tmp .= '<option value="'.$i.'" >'.enToPerNums($i).'</option>';
	for($i = 1;$i <= 31;$i++)
		$day_tmp .= '<option value="'.$i.'" >'.enToPerNums($i).'</option>';
?>

		<style>
			/*td,th{ margin:3px;padding:3px;}*/
			#reg
			{
				text-align:center;
				display:none;
				font-family:tahoma;
			}
			#login
			{
				text-align:center;font-family:tahoma;
			}
		</style>
		<script type="text/javascript" >
			var befor= <?php echo $befor ? "true" :"false"; ?>;
			var userOk = false;
			var ozv = <?php echo isset($_REQUEST['ozv'])?'true':'false'; ?>;
			$(document).ready(function(){
				if(ozv)
					startReg();
				$(".lform").keyup(function(e){ 
                                        var code = e.which;
                                        if(code==13)
                                        {
                                                e.preventDefault();
												userLogin();
                                        }
                                });
				$(".nform").keyup(function(e){ 
                                        var code = e.which;
                                        if(code==13)
                                        {
                                                e.preventDefault();
												$("#pass").focus();
                                        }
                                });
				$.each($(".dateValue"),function(id,field){
	                Calendar.setup({
        		        inputField     :    field.id,
                		button         :    field.id,
	                	ifFormat       :    "%Y/%m/%d",
	        	        dateType           :    'jalali',
        	        	weekNumbers    : false
	                });			
		});
			});
			function md5(inp)
			{
				return(hex_md5(inp));
			}
			function checkUser(objd)
			{
				var obj = $(objd);
				//var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
				if($("#user_reg").val()=='')
					alert('نام کاربری به درستی وارد نشده است')
				else
				{	
					obj.after("<img class='khoon' src='../img/status_fb.gif' >");
					$.get("login.php?checkUser="+$("#user_reg").val()+"&",function(result){
						$(".khoon").remove();
						result = $.trim(result);
						userOk = (result == 'true');
						if(result == 'false')
							alert('نام کاربری معتبر نمی باشد');
						$("#user_reg").css("color",userOk?'green':'red');
					});
				}
			}
			function checkMail(objd)
			{
				var obj = $(objd);
				var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
				if(!email_regex.test($("#email").val()))
				{
					alert('آدرس ایمیل به درستی وارد نشده است');
					$("#email").val('');
				}
				else
				{	
					obj.after("<img class='khoon' src='../img/status_fb.gif' >");
					$.get("login.php?checkMail="+$("#email").val()+"&",function(result){
						$(".khoon").remove();
						result = $.trim(result);
						userOk = (result == 'true');
						if(result == 'false')
							alert('آدرس ایمیل معتبر نمی باشد');
						$("#email").css("color",(result == 'false')?'red':'green');
					});
				}
			}
			function regUser()
			{
				if(userOk && $("#npass").val() != '' && $("#npass2").val()==$("#npass").val() && $("#tell").val()!='')
				{
					var pre_user=$("#pre_user").val();
					if(pre_user=="-4" && parseInt($("#codeOzviat").val()<0))
						alert('کد عضویت را وارد کنید');
					else
					{
						var d = {};
						$(".regdata").each(function(id,field){
							if(field.id == "npass")
								d[field.id] = md5($(field).val());
							else
								d[field.id] = $(field).val();
						});
						var p = $.param(d);
						$("#regB").after("<img src='../img/status_fb.gif' class='khoon' >");
						$.get("login.php?"+p+"&",function(result){
							$(".khoon").remove();
							if(result == 'true')
							{
								//$("#msg").slideDown();
								//$("#msg").html('ورود با موفقیت انجام گرفت');
								getCode();
								//$("#login").hide();
								//loginIcon(true);
								//setTimeout('closeDialog()',3000);
								//closeDialog();
							}
							else
								alert('خطا در عضویت');
						});
						
					}
				}
				else if(!userOk)
					alert('لطفا نام کاربری جدید وارد کنید');
				else if($("#npass").val() == '')
					alert('رمز عبور نمی بایست خالی باشد');
				else if($("#npass2").val()!=$("#npass").val())
					alert('رمز عبور و تکرار آن برابر نیستند');
				else if($("#tell").val()=='')
					alert('تلفن همراه را جهت دریافت کد فعال سازی درست وارد کنید');
				else
					alert('خطا در اطلاعات');
			}
			function userLogin()
			{
				$("#logB").after("<img src='../img/status_fb.gif' class='khoon' >");
				$.get("login.php?user="+$("#user").val()+"&pass="+md5($("#pass").val())+"&",function(result){
					$(".khoon").remove();
					if( result == 'true')
					{
						$("#msg").slideDown();
						$("#msg").html('ورود با موفقیت انجام گرفت');
						setTimeout(function(){
							closeDialog();
						},3000);
						loginIcon(true);
						//setTimeout('closeDialog()',2000);
					}
					else
						alert('خطا در ورود');
				});
			}
			function startReg()
			{
				$("#login").slideUp('slow',function(){
					$("#reg").slideDown('slow');
					resetDialog({width:800,height:300});
				});
			}
			function startLogin()
			{
				$("#reg").slideUp('slow',function(){
					$("#login").slideDown('slow');
					resetDialog({width:500,height:250});
				});
			}
			function checkCodeOzaviat(obj)
			{
				if($(obj).val()=="-4")
					$("#td_code").show('slow');
				else
					$("#td_code").hide('slow');
			}
			function checkMobile(obj)
			{
				if(!isMobile($(obj).val()))
				{
					alert('تلفن همراه را درست وارد کنید');
					$(obj).val('');
				}
			}
			function startEnableUser()
			{
				$("#sht_peygham").toggle();
				$("#enb").toggle();
				$("#login").toggle();
			}
			function enableUser()
			{
				var regCode = $("#regCode").val();
				if(regCode != '')
				{
					$("#enb").append("<img src='../img/status_fb.gif' class='khon' >");
					$.get("login.php",{regCode:regCode},function(result){
						console.log(result);
						$(".khon").remove();
						switch(result)
						{
							case 'enabled':
								alert('فعال سازی با موفقیت انجام گرفت');
								loginIcon(true);
								closeDialog();
								break;
							case 'alreadyEnabled':
								alert('کاربری شما قبلا فعال گردیده است');
								break;
							case 'noFound':
								alert('کد فعال سازی نا معتبر است');
								break;
						}
					});
				}
				else
					alert('لطفا کد فعال سازی را وارد کنید');
			}
			function getCode()
			{
				//startLogin();
				//startEnableUser();
				$("#sht_peygham").show();
				$("#enb").show();
				$("#login").hide();
				$("#reg").hide();
			}
			function forgetPassword()
			{
				var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
				if($("#forget_inp").val()!='')
				{
					$("#div_forget").show();
					$("#div_forget").html('<img src="../img/status_fb.gif" >');
					$.get("login.php?reset="+$("#forget_inp").val(),function(result){
						if(result=='true')
							$("#div_forget").html('گذرواژه جدید برای شما پست الکترونیکی گردید');
						else
							$("#div_forget").html('چنین کاربری در سامانه موجود نیست');
					});
				}
				else
					alert("نام کاربری معتبر نیست");
			}
			function tarkh(dobj)
			{
				var t = $("#tarikh_tavalod");
				t.val($("#year_tmp").val()+'/'+$("#month_tmp").val()+'/'+$("#day_tmp").val());
			}
		</script>
		<div style="display:none;" id="msg" class="msg" ></div>
		<script>
		if(befor)
		{
			loginIcon(false);
			$("#msg").show();
			$("#msg").html('خروج با موفقیت انجام شد');
			//setTimeout('closeDialog()',3000);
		}
		</script>
		<style>
			#log_tb button{font-family:tahoma;}
		</style>
		<?php 
			if($befor)
				die();
		?>
	<div id="content" >
		<div id="sht_peygham" style="display:none;" class="msg">
			<?php
				echo (trim($conf->enable_msg)!='')?$conf->enable_msg:'';
			?>
		</div>
		<div id="all_div" align="center">
			<div id="login" align="center">
				<table id="log_tb" cellspacing="0" style="margin-left:auto;margin-right:auto;font-family:tahoma;" >
					<tr>
						<th colspan="2">
						ورود به سامانه فروش دارما
						</th>
					</tr>
					<tr>
						<td>
							نام کاربری : 
						</td>
						<td>
							<input type="text" placeholder="نام کاربری را واردکنید" id="user" name="user" class="nform"/>
						</td>
					</tr>
					<tr>
						<td>
							رمز عبور:
						</td>
						<td>
							<input type="password" placeholder="گذرواژه خود را وارد کنید" id="pass" name="pass" class="lform"/>
						</td>
					</tr>
					<tr>
						<td>
							<button style="width:65px" id="logB" onclick="userLogin();">ورود</button>
                        </td>
						<td>
							
							<button style="width:65px" onclick="startReg();">عضویت</button>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right" >
							<span onclick="$('#forget_pass').toggle();" class="pointer" ><u>رمز خود را فراموش کرده ام</u></span>
						</td>
					</tr>
					<tr id="forget_pass" style="display:none;" >
						<td colspan="2" align="right">
							<input type="text" id="forget_inp"  placeholder="نشانی ایمیل خود را وارد کنید" /><button onclick="forgetPassword();">بازنشانی</button>
							<div id="div_forget" style="display:none;" >
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right">
							<span class="pointer" onclick="startEnableUser();"><u>فعال سازی</u></span> <span style="color:red;" >( به ایمیل و تلفن همراه شما ارسال شده است)</span>
						</td>
					</tr>
				</table>
			</div>
			<div id="enb" style="display:none;">
				<table  id="enable_tb" cellspacing="0" style="margin-left:auto;margin-right:auto;font-family:tahoma;" >
					<tr>
						<td colspan="2" align="right">
							<input type="text" id="regCode" name="regCode" placeholder="کد فعال سازی" /><button onclick="enableUser();">ثبت</button>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right">
							<span class="pointer notice" onclick="startEnableUser();"><u>بازگشت</u></td>
					</tr>
				</table>
			</div>
			<div id="reg" align="center">
				<table cellspacing="0">
					<tr>
						<th colspan="8">
					 ثبت نام در سامانه فروش دارما
						</th>
					</tr>
					<tr>
						<td>
							نام
						</td>
						<td>
							*<input placeholder="نام" class="regdata" type="text" id="fname" name="fname" />
						</td>
						<td>
							نام خانوادگی
						</td>
						<td>
							*<input placeholder="نام خانوادگی" class="regdata" type="text" id="lname" name="lname" />
						</td>
						<td>
							نام کاربری
						</td>
						<td>
							*<input placeholder="نام کاربری" class="regdata" type="text" id="user_reg" name="user_reg" onblur="checkUser(this);" />
						</td>
					</tr>
					<tr>
						<td>
							پست الکترونیک
						</td>
						<td>
							*<input placeholder="پست الکترونیک" class="regdata" type="text" id="email" name="email" onblur="checkMail(this);" />
						</td>
						<td>
							رمز عبور
						</td>
						<td>
								*<input placeholder="رمز عبور" class="regdata" type="password" id="npass" name="npass" />
						</td>
						<td>
                            تکرار رمز عبور
						</td>
						<td>
								*<input placeholder="تکرار رمز عبور" class="regdata" type="password" id="npass2" name="npass2" />
						</td>
					</tr>
					<tr>
						<td>
							شماره همراه
						</td>
						<td>
							*<input placeholder="شماره همراه" class="regdata" type="text" id="mob" name="mob" onblur="checkMobile(this);" />
						</td>
						<td>
							شماره ثابت
						</td>
						<td>
							<input placeholder="شماره ثابت" class="regdata" type="text" id="tel" name="tel"  />
						</td>
						<td>
							تاریخ تولد
						</td>
						<td>
							<select onchange="tarkh(this);" id="day_tmp"><?php echo $day_tmp; ?></select>
							<select onchange="tarkh(this);" id="month_tmp"><?php echo $month_tmp; ?></select>
							<select onchange="tarkh(this);" id="year_tmp"><?php echo $year_tmp; ?></select>
                            &nbsp;<input readonly="readonly"  placeholder="تاریخ تولد" class="regdata dateValue" type="text" id="tarikh_tavalod" name="tarikh_tavalod" style="display:none;" />
                        </td>
						
					</tr>
					<tr>
						<td>
                            شهر
						</td>
						<td>
								*<?php echo loadCity(); ?>
						</td>
						<td>
                            نشانی
                         </td>
						<td colspan="3" >
							*<input placeholder="نشانی" style="width:95%;" class="regdata" type="text" id="addr" name="addr" />
						</td>
					</tr>
					<tr>
						<td>
							   کدپستی
						</td>
						<td>
							<input placeholder="کدپستی" class="regdata" type="text" id="codePosti" name="codePosti" />
						</td>
						<td>
							   نوع کاربر
						</td>
						<td>
							<?php echo loadCustomer_type(); ?>
						</td>
						<td>
                                                           جنسیت
                                                </td>
                                                <td>
                                                        <select class="regdata" id="jender" name="jender">
								<option value="1">مرد</option>
								<option value="2">زن</option>
							</select>
                                                </td>
					</tr>
					<tr>
						<td>
                                                       کد عضویت(آشنایی)
                                                </td>
						<td>
							<select class="regdata" id="pre_user" onchange="checkCodeOzaviat(this);" >
								<option value="-1" >جستجو</option>
								<option value="-2" >روزنامه</option>
								<option value="-3" >شرکت دارما</option>
								<option value="-4" >دوستان</option>
								<option value="-5" >سایر</option>
							</select>
						</td>
						<td  colspan="2" id="td_code" style="display:none;color:red;" >
							کد عضویت:
							<input placeholder="کد عضویت را وارد کنید" type="text" class="regdata" id="codeOzviat" >
                                                </td>
<!--
						<td>
                                                        <button onclick="startLogin();">ورود اعضا</button>
                                                </td>
-->
						<td>
							<button id="regB" onclick="regUser();">عضویت</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
