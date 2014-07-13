<?php
	include_once("../kernel.php");
	include_once("../simplejson.php");
        if(isset($_REQUEST['sms_text']))
        {
            $grp_ids=array();
            $sms_text='';
            foreach($_REQUEST as $key=>$val)
            {
                if($key!='sms_text'  && $key!='calc')
                    $grp_ids[]=$key;
                else if($key=='sms_text')
                    $sms_text=$val;
            }
            $sms = new sms_class;
            if(isset($_REQUEST['calc']))
            {
                die((string)$sms->sms_grp_send($grp_ids,$sms_text,TRUE));
            }
            else
                if($sms->sms_grp_send($grp_ids,$sms_text))
                    die('ok');
        }
        $grp='<table width="100%"  ><tr>';
        $my = new mysql_class;
        $my->ex_sql("select * from `group` order by gr_name", $q);
        for($i=0;$i<count($q);$i++)
        {
            if($i!=0 && ($i%5)==0)
                $grp.='</tr><tr>';
            $grp.='<td><div style="height:100%;padding:5px;" class="alert alert-success" title="'.$q[$i]['gr_name'].'" ><input class="grp_ch" type="checkbox"  name="'.$q[$i]['id'].'" value="1"> '.substrH($q[$i]['gr_name'],10).'</div></td>';
        }
        $grp.='</table>';
?>
<!doctype html>
<html lang="fa">
	<head>
		<meta charset="utf-8">
		<title><?php echo $conf->title;  ?></title>
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />
                <link rel="stylesheet" href="../css/bootstrap-checkbox.css" type="text/css" />
		<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" type="text/css" />

		<link rel="stylesheet" href="../css/colorpicker.css">
		<link rel="stylesheet" href="../css/xgrid.css">
                <link rel="stylesheet" type="text/css" media="all" href="../js/cal/skins/aqua/theme.css" title="Aqua" />
                <link rel="stylesheet" href="../css/jquery.fileupload.css">
                
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
                <script type="text/javascript" src="../js/bootstrap-checkbox.js"></script>
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
                        div{margin-top: 5px;}
		</style>
                <script>
                    $(document).ready(function(){
                        $('.grp_ch').checkbox({
                            buttonStyle: 'btn-info',
                            buttonStyleChecked: 'btn-warning',
                            checkedClass: 'icon-check',
                            uncheckedClass: 'icon-check-empty'
                        });
                        $("#myModal").hide();
                    });
                    function loadMenu(inp,dobj)
                    {
                        $("#all_body").load(inp);
                        $("li").removeClass("active");
                        $(dobj).addClass("active");
                    }
                    function countChar(val) {
                            var len = val.value.length;
                            var countSms = 1;
                            countSms = Math.ceil(len/70);
                            $('#smsCount').text(countSms);
                            $('#charCount').text(len);
                    }
                    function chek_un()
                    {
                        $(".grp_ch").click();
                    }
                    function send_sms()
                    {
                        if($.trim($("#sms_text").val())=='')
                        {
                            var tt ='<div class="alert alert-warning" >'+ 'متن پیامک خالی است'+'</div>';
                            tt+='<button class="btn btn-info" onclick=\'$("#myModal").modal("hide");\'  > قبول</button>';
                            $("#myModal").html(tt);
                            $("#myModal").modal('show');
                            return(false);
                        }
                        var req = $("#sms_frm").serialize();
                        $("#khoon").html("<img src='../img/status_fb.gif' >");
                        $.get("index.php?calc=1&"+req,function(result){
                            $("#khoon").html(''); 
                            var smsCount = parseInt($("#smsCount").html(),10);
                            var tt ='<div class="alert alert-info">'+ 'تعداد پیامک ارسالی:'+result+'</div>';
                            tt+='<div class="alert alert-danger" >مبلغ:'+(smsCount*result*107)+' ریال</div>';
                            tt+=result==0?'':'<button class="btn btn-info" onclick="doSms();" >موافقم</button>';
                            tt+='<button class="btn btn-info" onclick=\'$("#myModal").modal("hide");\'  > انصراف</button>';
                            $("#myModal").html(tt);
                            $("#myModal").modal('show');
                        });
                    }
                    function doSms()
                    {
                        $("#myModal").modal('hide');
                        var req = $("#sms_frm").serialize();
                        $("#khoon").html("<img src='../img/status_fb.gif' >");
                        $("#send_btn").hide('fade');
                        $.get("index.php?"+req,function(result){
                            $("#khoon").html("ارسال با موفقیت انجام شد");
                            $("#send_btn").show('fade');
                        });
                    }
		</script>
	</head>
	<body dir="rtl">
            <div class="row" >
                <div class="span3" style="padding-top:20px;" >
			<h4 class="alert alert-success">
			سامانه ارسال پیامک
			</h4>
                        <div class="alert alert-danger" >
                            اعتبار:
                            <?php echo(monize(sms_class::getCredit())); ?>
                            ریال
                        </div>
		</div>
		<div class="span9"  >
			<img src="../img/header.jpg" >
		</div>
            </div>
                <div id='menu' class="span3" style='background:#eeeeee;'  >
                    <ul class="nav nav-pills nav-stacked">
                        <li onclick="loadMenu('group.php',this);" >
                            <a  href="#" >                              
                                گروهها
                            </a>
                        </li>
                        <li id="menu_numbers" onclick="loadMenu('numbers.php',this);" >
                            <a href="#" >                              
                                شماره ها
                            </a>
                        </li>
                        <li class="active" >
                            <a href="index.php" >                              
                                ارسال پیامک
                            </a>
                        </li>
                        <li onclick="loadMenu('kala.php',this);" >
                            <a  href="#" >                              
                                لیست سیاه
                            </a>
                        </li>
                    </ul>
                </div>
 
                <div class="span9" id="all_body" >
                    <button onclick="chek_un();" class="btn btn-info" >
                               انتخاب/عدم انتخاب همه 
                    </button>
                    <form id="sms_frm" >
                        <div id="group_div" >
                            <?php echo $grp; ?>
                        </div>
                        <div>
                            متن پیامک                        
                        </div>
                        <textarea id="sms_text" name="sms_text" class="span9" rows="7" onkeyup="countChar(this)" ></textarea>
                        تعداد پیامک:
                        <span id="smsCount">1</span>
                   تعداد حروف:
                        <span id="charCount">0</span>
                    </form>
                    <button id="send_btn" class="btn btn-danger" onclick="send_sms();" >
                        ارسال پیامک
                    </button>
                    <span id="khoon"></span>
                    <div class="modal" id="myModal" >
                    </div>
		</div>
	</body>
</html>
