<?php
	include_once("../kernel.php");
	function list_grp($inp)
	{
                $out = '';
		$mysql = new mysql_class;
		$mysql->ex_sql("select gr_name,group_id from `group_numbers` left join `group` on (`group`.`id`=group_id) where `numbers_id`='$inp'",$q);
		foreach($q as $r)
                    $out .=($out==''?'':',').'<span class="pointer" onclick="del_grp('.$r['group_id'].','.$inp.')" ><img style="width:15px;" src="../img/cancel.png" >'.$r['gr_name'].'</span>';
		return($out);
	}
	function addOzv($gname,$table,$fields,$column)
	{
		$out = FALSE;
		$conf = new conf;
		$mysql = new mysql_class;
		if(isset($_REQUEST['grp_id']))
			$group_id =(int) $_REQUEST['grp_id'];
		else
			$group_id = -1;
		$mobiles = trim($fields['mobiles']);
		$names =trim($fields['name']);
                $is_siah = trim($fields['is_siah']);
                $number_exist = numbers_class::number_exist($mobiles);
                if(count($number_exist)==0)
                {    
                    $ln = $mysql->ex_sqlx("INSERT INTO `numbers` (`id`, `mobiles`, `name`,is_siah) VALUES (NULL, '".$mobiles."', '".$names."', '".$is_siah."');",FALSE);
                    $ozv_id = $mysql->insert_id($ln);
                    $mysql->close($ln);
                    if($ozv_id>0 && $group_id >0)
                        $mysql->ex_sqlx("insert into group_numbers (`numbers_id`,`group_id`) values ('$ozv_id','$group_id')");
                    $out = ($ozv_id>0);
                }
                else {
                    $out ="false|شماره قبلا ثبت شده است";
                }
		return($out);
	}
        function add_grp($inp)
        {
           $tmp =  columnListLoader("group",array('id','gr_name'));
           $out = '<select onchange="add_grp('.$inp.',this);" >'.columnListToCombo($tmp).'</select>';
           $out .='<div id="khoon_'.$inp.'" ></div>';
           return($out);
        }
        if(isset($_REQUEST['add_group_id']))
        {
            $grp_id = (int)$_REQUEST['add_group_id'];
            $numbers_id  = (int)$_REQUEST['numbers_id'];
            $out = 'nok';
            if($grp_id>0 && $numbers_id>0)
                $out = group_numbers_class::add($grp_id,$numbers_id);
            die($out);
        }
        if(isset($_REQUEST['del_grp_id']) && isset($_REQUEST['del_number_id']))
        {
            $del_grp_id = (int)$_REQUEST['del_grp_id'];
            $del_number_id = (int)$_REQUEST['del_number_id'];
            $my = new mysql_class;
            die($my->ex_sqlx("delete from group_numbers where group_id=$del_grp_id and numbers_id= $del_number_id"));
        }
        $grp_id = -1;
        $werc='';
	if(isset($_REQUEST['grp_id']) && (int)$_REQUEST['grp_id']>0)
        {
                
		$grp_id = $_REQUEST['grp_id'];
                $my = new mysql_class;
                $my->ex_sql("select numbers_id from group_numbers where `group_id`=$grp_id", $q);
                $qu=-1;
                foreach($q as $r)
                    $qu.=','.$r['numbers_id'];
                $werc = " `id` in ($qu)";
        }
        if(isset($_REQUEST['sms_txt']))
        {
            $ou = 'nok';
            $sms_text = trim($_REQUEST['sms_txt']);
            $ids = $_REQUEST['ids'];
            $mobs=array();
            if($ids!='')
            {
                $my = new mysql_class;
                $my->ex_sql("select mobiles from numbers where id in ($ids) group by mobiles", $q);
                foreach($q as $r)
                    $mobs[]='0'.$r['mobiles'];
                $sms = new sms_class;
                $sms->sendSMS_webservice($mobs,$sms_text);
                $ou ="ok";
            }
            die($ou);
        } 
        
	$gname = "gname_user";
	$input =array($gname=>array('table'=>'numbers','div'=>'main_div_numbers'));
        $xgrid = new xgrid($input);
	$xgrid->eRequest[$gname] = array('grp_id'=>$grp_id);
	$xgrid->whereClause[$gname] = $werc;
	$xgrid->column[$gname][0]['name']='';
	$xgrid->column[$gname][1]['name']='نام';
	$xgrid->column[$gname][1]['search'] = 'text';
	$xgrid->column[$gname][2]['name']='شماره';
	$xgrid->column[$gname][2]['search'] = 'text';
	$xgrid->column[$gname][3]['name']='لیست سیاه';
        $xgrid->column[$gname][3]['clist'] = array(0=>'خیر',1=>'بله');
        $xgrid->column[$gname][3]['search']='list';
        $xgrid->column[$gname][3]['searchDetails'] = array(-1=>'همه',0=>'خیر',1=>'بله');
        $xgrid->column[$gname][4] = $xgrid->column[$gname][0];
        $xgrid->column[$gname][4]['name'] = 'گروه‌ها';
        $xgrid->column[$gname][4]['cfunction'] = array('list_grp');
        $xgrid->column[$gname][4]['access']='a';
        
        $xgrid->column[$gname][5] = $xgrid->column[$gname][0];
        $xgrid->column[$gname][5]['name'] = 'عضویت';
        $xgrid->column[$gname][5]['cfunction'] = array('add_grp');
        $xgrid->column[$gname][5]['access']='a';
        
	$xgrid->addFunction[$gname] = 'addOzv';
        if($grp_id>0)
            $xgrid->canAdd[$gname]= TRUE;
	$xgrid->canEdit[$gname]= TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
            die($out);
?>
<script type="text/javascript" >
        var grid_name = '<?php echo $gname; ?>';
        var gr_id = <?php echo $grp_id; ?>;
        $(document).ready(function(){
                var args=<?php echo $xgrid->arg; ?>;
                //args[grid_name]['afterLoad']=check_box;
                intialGrid(args);
        });
        function send_sms_select(){
            if($.trim($("#sms_text").val())=='')
            {
                $("#khoon").html("متن وارد نشده است");
                return(false);
            }
            var ids=[];
             var ids = '';
            var rowNums = [];
            $.each($("."+gArgs[grid_name]['cssClass']+"_checkSelect"),function(id,field){
                    var tmp = field.id.split('-');
                    if(tmp[1] == grid_name && field.checked)
                    {
                            rowNums[rowNums.length] = tmp[2];
                            var realId = trim($("#"+grid_name+"-span-id-"+tmp[2]).html());
                            ids += ((ids != '')?',':'')+realId;
                    }
            });
            var obj={
                'sms_txt':$("#sms_text").val(),
                'ids':ids
            };
            $("#khoon").html("<img src='../img/status_fb.gif' >");
            $("#send_btn_tak").hide();
            $.get("numbers.php",obj,function(result){
                $("#khoon").html("");
                $("#send_btn_tak").show();
                if($.trim(result)=='ok')
                {
                    $("#khoon").html("ارسال با موفقیت انجام گرفت");
                }    
                else
                    $("#khoon").html('هیچ شخصی انتخاب نشده است');
            });
        }
        function del_grp(grp_id,number_id)
        {
            if(confirm("آیا حذف این شماره از گروه انجام شود؟"))
            {
                grp_id = parseInt(grp_id,10);
                number_id = parseInt(number_id,10);
                if(grp_id>0 && number_id>0)
                {
                    var ob = {
                        "del_grp_id":grp_id,
                        "del_number_id":number_id
                    };
                    $.get("numbers.php",ob,function(result){
                        if($.trim(result)=='ok')
                        {
                            grid[grid_name].init(gArgs[grid_name]);
                        }
                    });
                }
                else
                    alert("اشکال در شماره یا گروه");
            }
        }
        function countChar(val) {
                var len = val.value.length;
                var countSms = 1;
                countSms = Math.ceil(len/70);
                $('#smsCount').text(countSms);
                $('#charCount').text(len);
        }
        function add_grp(inp,obj)
        {
            if(confirm("آیا به گروه اضافه شود؟"))
            {
                $("#khoon_"+inp).html("<img src='../img/status_fb.gif' >");
                var ob = {
                    'add_group_id':$(obj).val(),
                    'numbers_id':inp
                };
                $.get('numbers.php',ob,function(result){
                    $("#khoon_"+inp).html("");
                    if(result=='ok')
                    {
                        alert('افزودن به گروه با موفقیت انجام گرفت');
                        loadMenu('numbers.php?grp_id='+gr_id);
                    }
                    else 
                        alert('عملیات با خطا مواجه گردید یا این شماره در گروه قبلا عضو بوده است');
                });
            }
        }
</script>
<div id="main_div_numbers"></div>
<div>
    
    <textarea id="sms_text" class="span9" rows="7" onkeyup="countChar(this)" ></textarea>
                            تعداد پیامک:
    <span id="smsCount">1</span>
تعداد حروف:
    <span id="charCount">0</span>
    <button id="send_btn_tak" class="btn btn-warning" onclick="send_sms_select();" >
        ارسال پیامک
    </button>
    <span class="alert alert-danger" id="khoon"></span>
</div>