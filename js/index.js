var showDrop;
$(document).ready(function(){
	jQuery.fn.extend({
		slideRightShow: function(du,fn) {
			return this.each(function() {
				$(this).show('slide', {direction: 'right'}, du,function(){
					fn();
				});
			});
		},
		slideLeftHide: function(du,fn) {
			return this.each(function() {
				$(this).hide('slide', {direction: 'left'}, du,function(){
					fn();
				});
			});
		},
		slideRightHide: function(du,fn) {
			return this.each(function() {
				$(this).hide('slide', {direction: 'right'}, du,function(){
					fn();
				});
			});
		},
		slideLeftShow: function(du,fn) {
			return this.each(function() {
				$(this).show('slide', {direction: 'left'}, du,function(){
					fn();
				});
			});
		}
	});
	jQuery.fn.bottomLeft = function (topOff,leftOff) {
		if(this.length > 0)
		{
			topOff = (typeof topOff !== 'undefined')?parseInt(topOff,10):10;
			leftOff = (typeof leftOff !== 'undefined')?parseInt(leftOff,10):10;
			this.css("position","fixed");
			var wh = $(window).height();
			var oh = this.height();
			var tt = String(wh-oh-topOff)+"px";
			this.css("top",tt);
			this.css("left",String(leftOff)+"px");
		}
	}
	jQuery.fn.bottomRight = function (topOff,leftOff) {
		if(this.length > 0)
		{
			topOff = (typeof topOff !== 'undefined')?parseInt(topOff,10):10;
			leftOff = (typeof leftOff !== 'undefined')?parseInt(leftOff,10):10;
			this.css("position","fixed");
			var wh = $(window).height();
			var ww = $(window).width();
			var oh = this.height();
			var ow = this.width();
			var tt = String(wh-oh-topOff)+"px";
			var ll = String(ww-ow-leftOff)+"px";
			this.css("top",tt);
			this.css("left",ll);
		}
	}
	jQuery.fn.topRight = function (topOff,leftOff) {
		if(this.length > 0)
		{
			topOff = (typeof topOff !== 'undefined')?parseInt(topOff,10):10;
			leftOff = (typeof leftOff !== 'undefined')?parseInt(leftOff,10):10;
			this.css("position","fixed");
			var wh = $(window).height();
			var ww = $(window).width();
			var oh = this.height();
			var ow = this.width();
			var tt = String(topOff)+"px";
			var ll = String(ww-ow-leftOff)+"px";
			this.css("top",tt);
			this.css("left",ll);
		}
	}
	createTabs("#tabs");
	$('#dialog').dialog({
		autoOpen : false,
		show: "slide",
		/*hide: "drop",*/
		modal: true,
		resizable: false,
		minWidth :200,
		minHeight : 200,
		position : 'center',
		closeOnEscape: true,
		beforeClose: function(event, ui) {
			return(true);
		}
	});
/*
	var wh = $(document).height();
	var ww = $(document).width();
	var dh = $("#drawer table").height();
	var dw = $("#drawer table").width();
	var dt = (wh-dh) / 2;
	var dl = (ww-dw)-230;
	$("#drawer").css("position","absolute");
	$("#drawer").css("top","5px");
	$("#drawer").css("left",dl+"px");
*/
	$(".kalaAbarSelector").click(function(e){
		var obj = $(e.currentTarget);
		window.location = "index.php?daste="+obj.prop("id").split('_')[1]+"&";
	});
	if(!logedIn)
	{
		$("#maliTitle").hide();
		$("#factor_p").hide();
		$("#comment_user").hide();
		$("#pishFactor_p").hide();
	}
	$("#slideShow").cycle({ 
		fx:      'turnDown', 
		delay:   -4000 ,
		fit : 1,
		width : 1024,
		height : 300,
		pause : 1
	});
	$("#slideShow img").tooltip({
	    track : true,
	    content: function () {
		return $(this).prop('title');
	    }
	});
	$("#StoreImg").tooltip();
	$("#SabadEmpty").tooltip();
	//$(".kalaGroupPic").tooltip();
	$(window).resize(function(){
		createFloatDiv();
	});
	$(window).scroll(function () {
		$("#kalaMiniDiv").hide();
		if($(window).scrollTop()>157)
		{
			floatWindowTop = 10;
			createFloatDiv();
		}
		else
		{
			floatWindowTop = floatWindowTopDef;
			createFloatDiv();
		}
	});
	var fdaste;
	$("#kalaDiv").html("<img src='../img/status_fb.gif' />");
	if(daste == '' || daste == 'home')
		fdaste = 'home=home';
	else
		fdaste = 'kala_abarGroup_id='+daste;
	$("#kalaDiv").load("loadKala.php?"+fdaste+"&",function(){
		closeMini();
	});
	createFloatDiv();
	createShegeft();
	setupSearch();
	startTimer();
	$(".kalaGroupPic,#kalaGroupDiv").mouseover(function(event){
		viewTitles();
		clearTimeout(showDrop);
		showDrop = setTimeout(function(){		
	                hideTitles();
		},1000);
		event.stopPropagation();
	});
/*
	$("#kalaGroupDiv").mouseout(function(event){
		event.stopPropagation();
        });
*/
});
function loadKala(kala_group_id)
{
	$(".kalaSelector").removeClass("drawerTable_active");
	$("#kalaGroup_"+kala_group_id).addClass('drawerTable_active');
	var mobj = $("#kalaMiniDiv");
	var of = $("#kalaGroup_"+kala_group_id).offset();	
	var h = $("#kalaGroup_"+kala_group_id).height();
	var w = $("#kalaGroup_"+kala_group_id).width();
	var dw = mobj.width();
	var l = of.left+w-dw+25;
	mobj.css("position","absolute");
	mobj.css("top",String(of.top+h+10)+"px");
	mobj.css("left",l+"px");
	mobj.css("z-index","1007");
	mobj.slideDown();
	mobj.html("<img src='../img/status_fb.gif' />");
	$.getJSON("loadKala.php?kala_group_id="+kala_group_id+"&",function(result){
		var out = '<table width="100%" class="kalaMiniTable"><tr><td colspan="2" align="left"><img class="pointer" onclick="closeMini();" src="../img/cancel.png" width="15px" /></td></tr><tr>';
		for(i in result)
		{
	
			out += '<td class="kalaMiniItem pointer" id="kalaMini_'+result[i]['id']+'" onclick="openKala('+result[i]['id']+');">'+result[i]['name']+'</td>';
			if(i % 2 != 0)
				out += '</tr><tr>';
		}
		out += '</tr></table>';
		mobj.html(out);
	});
}
function openKala(kala_miniGroup_id)
{
	$("#kalaDiv").html("<img src='../img/status_fb.gif' />");
	$("#kalaDiv").load("loadKala.php?kala_miniGroup_id="+kala_miniGroup_id+"&",function(){
		closeMini();
	});
}
function closeMini()
{
	$("#kalaMiniDiv").html('');
	$("#kalaMiniDiv").slideUp();
}
function createFloatDiv()
{
	$("#leftFloatDiv").bottomLeft(20,30);
	$("#rightFloatDiv").bottomLeft(100,50);
	if($.trim($("#kalaGroupDiv").html())!='')
	{
		$("#kalaGroupDiv").show();
		$("#kalaGroupDiv").topRight(floatWindowTop,100);
	}
}
function createTabs(inp)
{
	$( inp ).tabs({
		beforeActivate: function( event, ui ) {
			$(ui.oldPanel[0]).html('');
		},
		beforeLoad: function( event, ui ) {
			ui.panel.html("<img src='../img/status_fb.gif' />");
		}
		
	});
}
function openDialog(addr,prop,fn)
{
	$("#dialog").html("<img src='../img/status_fb.gif' alt='Loading . . .'/>");
	if($("#dialog").dialog)
	{
		if($("#dialog").dialog("isOpen"))
			$("#dialog").dialog("close");
		for(i in prop)
			$("#dialog").dialog("option",i,prop[i]);
		$("#dialog").dialog("open");
		$("#dialog").load(addr,function(){
			if(typeof fn == "function")
				fn();
		});
	}
}
function emptySabad(force)
{
	var f = (typeof force != 'undefined' && force === true)?true:false;
	if(!f)
	{
		if(confirm('آیا سبد کالا خالی شود؟'))
			$.get("index.php?sabad=empty&",function(result){
				sabad = {};
				showSabad();
			});
	}
	else
		$.get("index.php?sabad=empty&",function(result){
			sabad = {};
			showSabad();
		});
}
function refreshSabad()
{
	$.getJSON("index.php?refreshSabad=true&",function(result){
		sabad = result;
		if(typeof sabad.kalas != 'undefined' && sabad.kalas.length == 0)
			emptySabad(true);
		showSabad();
	});
}
function sabadIsEmpty()
{
	return(typeof sabad.kalas != 'undefined' && sabad.kalas.length > 0);
}
function objLength(obj)
{
	var out = 0;
	for(i in obj)
		if(typeof obj[i] != 'function')
			out++;
	return(out);
}
function showSabad()
{
	if(typeof sabad.kalas != 'undefined')
	{
		$("#sabad_td").show();
		$("#sabad_td1").hide();
		$("#sabad_count").html(objLength(sabad.kalas));
	}
	else
	{
		$("#sabad_td").hide();
		$("#sabad_td1").show();
	}
}
function continSabad()
{
	//resetDialog({width:800,height:600});
	if(logedIn)
	{
		if(sabad.kalas.length > 0)
		{
			openDialog('sabadPreview.php',{title:'سبد خرید',width:800,height:600});
			openFactor = false;
		}
		else
		{
			sabad = {};
			showSabad();
			alert('سبد شما خالی است');
		}
	}
	else
	{
		openFactor=true;
		showLogin(document.getElementById('loginIc'));
	}
}
function showLogin(tobj)
{
	obj = $(tobj);
	loged = logedIn;//(tobj.src.split('/')[tobj.src.split('/').length-1]=='out.png');
	if(loged)
	{
		if(confirm('آیا مایل به خروج هستید؟'))
		{
			openDialog('login.php',{title:'ﻭﺭﻭﺩ',width:500,height:250});
			$("#loginTitle span").text("ورود کابران");
			$("#userName").hide();
			logedIn = false;
		}
	}
	else
		openDialog('login.php',{title:'ورود',width:500,height:270});
}
function showOzviat(tobj)
{
	obj = $(tobj);
	loged = logedIn;//(tobj.src.split('/')[tobj.src.split('/').length-1]=='Login.png');
	if(loged)
		openDialog('profile.php',{title:'پروفایل',width:850,height:550});
	else
		openDialog('login.php?ozv=ozv',{title:'عضویت',width:500,height:250});
}
function showLogin1(tobj)
{
	openDialog('login.php',{title:'ورود',width:500,height:250});
}
function loginIcon(inp)
{
	if(inp === true)
	{
		$("#loginTitle span").text("خروج");
		$("#ozviatTitle span").text("پروفایل");
		logedIn = true;
		$("#maliTitle").show();
		$("#factor_p").show();
		$("#comment_user").show();
		$("#pishFactor_p").show();
		$("#cards_manage").show();	
		$.get("index.php",{userName:1},function(result){
			$("#userName").html(result);
			$("#userName").show();
			//closeDialog();
			if(openFactor)
				continSabad();
		});
	}
	else
	{
		logedIn = false;
		$("#pishFactor_p").hide();
		$("#maliTitle").hide();
		$("#loginTitle span").text("ورود کاربران");
		$("#ozviatTitle span").text("عضویت");
		$("#userName").hide();
		$("#factor_p").hide();
		$("#comment_user").hide();
		$("#cards_manage").hide();
	}
}
function resetDialog(prop)
{
	for(i in prop)
		$("#dialog").dialog("option",i,prop[i]);
}
function closeDialog()
{
	$("#dialog").dialog("close");
}
function al()
{
	alert('ok');
}
function addTab(addr,title)
{
	removeTab();
	$("#tabs").tabs('destroy');
	var obj = $("#tabs ul");
	var txt = '<li id="sear"><span class="pointer" style="padding:3px;" onclick="removeTab();">X</span><a href="'+addr+'" ><span>'+title+'</span></a></li>';
	obj.prepend(txt);
	createTabs("#tabs");
}
function removeTab()
{
	if($("#sear").length == 1)
	{
		$("#tabs").tabs('destroy');
		var obj = $("#sear").remove();
		createTabs("#tabs");
	}
}
function setupSearch()
{
	$("#searchItem").keyup(function(e){ 
		var code = e.which;
		if(code==13)
		{
			e.preventDefault();
			if($("#searchItem").val()!='')
				searchKala($("#searchItem").val());
			else
				alert('لطفا اطلاعات مورد نظر را وارد کنید');
		}
	});
}
function searchKala(searchText)
{
	var t = encodeURIComponent(searchText);
	//addTab("loadKala.php?q="+t+"&",'نتایج جستجو');//,searchText);
	$("#kalaDiv").html("<img src='../img/status_fb.gif' />");
	$("#kalaDiv").load("loadKala.php?q="+t+"&",'نتایج جستجو');
}
function ps_postRefId(inp)
{
	var result = jQuery.parseJSON(inp);
	var form = document.createElement("form");
	form.setAttribute("method", "POST");
	form.setAttribute("action", ps_payPage);         
	var hiddenField;
	for(i in result)
	{
		hiddenField = document.createElement("input");              
		hiddenField.setAttribute("name", i);
		hiddenField.setAttribute("value", result[i]);
		form.appendChild(hiddenField);
		document.body.appendChild(form);
	}
	form.submit();
	document.body.removeChild(form);
}

function startBank(mablagh,factor_id)
{
	var pr = {};
	pr['mablagh'] = mablagh;
	if(typeof factor_id != 'undefined')
		pr['factor_id'] = factor_id;
	else
	{
		alert('شماره فاکتور جهت ارسال به بانک  داده نشده است');
		return(false);
	}
	$.getJSON("index.php",pr,function(result){
		if(result.status)
			ps_postRefId(result.pay_code);
		else
			alert('خطا در ارسال به بانک ، لطفا مجددا تلاش نمایید');
	});
}
function showMali()
{
	openDialog('mali.php',{title:'گردش مالی',width:900,height:700});
}
function showFactor()
{
	openDialog('factor.php',{title:'پیگیری فاکتور',width:950,height:700});
}
function showPishFactors()
{
	openDialog('pishFactor_all.php',{title:'پیش فاکتور',width:950,height:700});
}
function showComment()
{
	openDialog('comment_user.php',{title:'نظرات و پیشنهادات',width:400,height:300});
}
function showKala(kala_id)
{
	openDialog("kalaProfile.php?kala_id="+kala_id+"&",{width:600,height:400,title:"..."});
}
function showCards()
{
	openDialog("cards.php",{width:950,height:700,title:"مدیریت کارتها"});
}
function kalaOver(kala_id)
{
}
function nextShegeft()
{
	$(".timeLimitItem").hide();
	if($("#timeLimit_"+kalaSlideIndex).length == 1)
	{
		$("#timeLimit_"+kalaSlideIndex).slideLeftShow(3000,function(){
			$(".shegeftKalaKol").removeClass('shegeftKalaSelected');
			$("#shegetfKala_"+kalaSlideIndex).addClass('shegeftKalaSelected');
			if(typeof kalaSlide[kalaSlideIndex+1] == 'undefined')
				kalaSlideIndex = 0;
			else
				kalaSlideIndex++;
			showTimer();
			setTimeout(function(){
				nextShegeft()
			},4000);
		});
	}
	else
	{
		$(".shegeftKalaKol").removeClass('shegeftKalaSelected');
		$("#shegetfKala_"+kalaSlideIndex).addClass('shegeftKalaSelected');
		if(typeof kalaSlide[kalaSlideIndex+1] == 'undefined')
			kalaSlideIndex = 0;
		else
			kalaSlideIndex++;
		showTimer();
		setTimeout(function(){
			nextShegeft()
		},4000);
	}
}
function createShegeft()
{
	if(kalaSlide.length>0)
	{
		var selectors = '<table class="shegeftKalaTable pointer">';
		var out = '';
		for(i in kalaSlide)
		{
			out += "<div onclick='openDialog(\"kalaProfile.php?kala_id="+kalaSlide[i].id+"&\",{width:600,height:400,title:\"کالای شگفت انگیز\"});' class = 'pointer timeLimitItem' "+((i>0)?"style='display:none;'":'')+" id='timeLimit_"+i+"'>";
			out += '<table class="shegeftSlide" width="100%" >';
			out += '<tr>';
			out += '<td>';
			out += ((kalaSlide[i].pic!='')?"<img src='"+kalaSlide[i].pic+"' />":'');
			out += '</td>';
			out += '<td class="shegeft_timer" >';
			var sabad_txt = '<div><span class="shegeft_sabad" >اضافه به سبد خرید <img src="../img/sabad_icon.png" width="20" ></span></div>';
			out += '<img src="../img/shegeftangiz.png" alt="پیشنهاد شگفت انگیز" ><div class="timer"></div>'+sabad_txt;
			out += '</td>';
			out += '<td class="shegeft_profile" >';
			var pro ="<div>مزایای خرید از <span style='color:red' >پیشنهاد شگفت انگیز</span></div>";
			pro+='<div style="padding-top:30px;" ><table><tr><td valign="bottom" class="shegeft_ghimat" ><div style="padding-bottom:10px;" >'+kalaSlide[i].ghimat/10+'| <span class="line" >'+kalaSlide[i].ghimat_user/10+'</span></div></td></tr></table></div>';
			out += pro;
			out += '</td>';
			out += '</tr>';
			out += '</table>';
			out += "</div>";
			selectors += '<tr><td'+((i==0)?' class="shegeftKalaKol shegeftKalaSelected"':' class="shegeftKalaKol"')+' onclick="loadShegeft('+kalaSlide[i].id+');" id="shegetfKala_'+i+'">'+kalaSlide[i].name+'</td></tr>';
			startSec.push(kalaSlide[i].time_limit-currentSec);
		}
		selectors += '</table>';
		$("#shegeftSelectors").html(selectors);
		$("#shegeftContainer").html(out);
		setTimeout(function(){
			nextShegeft()
		},6000);
	}
	else
		$("#shegeftDiv").remove();
}
function loadShegeft(i)
{
/*
	kalaSlideIndex = (i>0)?i-1:kalaSlide.length;
	$(".timeLimitItem").hide();
	if($("#timeLimit_"+kalaSlideIndex).length == 1)
	{
		$("#timeLimit_"+kalaSlideIndex).slideLeftShow(3000,function(){
			$(".shegeftKalaKol").removeClass('shegeftKalaSelected');
			$("#shegetfKala_"+kalaSlideIndex).addClass('shegeftKalaSelected');
			if(typeof kalaSlide[kalaSlideIndex+1] == 'undefined')
				kalaSlideIndex = 0;
			else
				kalaSlideIndex++;
		});
	}
	else
	{
		$(".shegeftKalaKol").removeClass('shegeftKalaSelected');
		$("#shegetfKala_"+kalaSlideIndex).addClass('shegeftKalaSelected');
		if(typeof kalaSlide[kalaSlideIndex+1] == 'undefined')
			kalaSlideIndex = 0;
		else
			kalaSlideIndex++;
	}
	showTimer();
*/
	
}
function hideKalaSlide(i)
{
	$("#timeLimit_"+i).remove();
	delete startSec[i];
}
function startTimer()
{
	if(typeof startSec[kalaSlideIndex] != 'undefined')
	{
		setInterval(function(){
			for(i in startSec)
			{
				if(startSec[i] > 0)
					startSec[i]--;
				else
					hideKalaSlide(i);
			}
			showTimer();
		},1000);
	}
}
function secToTime(inp)
{
	var out = [0,0,0,0,0];
	out[4] = (inp-(inp % 604800))/604800;
	var w = inp % 604800;
	out[3] = (w-(w % 86400))/86400;
	var h = w % 86400;
	out[0] = (h-(h % 3600))/3600;
	var m = inp % 3600;
	out[1] = (m - (m % 60)) / 60;
	var s = m % 60;
	out[2] = s
	return(out);
}
function showTimer()
{
	if(typeof startSec[kalaSlideIndex] != 'undefined')
	{
		var t = secToTime(startSec[kalaSlideIndex]);
		$(".timer").html('<span class="timer_style" >'+((t[4]>0)?t[4]+' هفته || ':'')+((t[3]>0)?t[3]+' روز || ':'')+t[0]+' || '+t[1]+' || '+t[2]+'</span>');
	}
}
function viewTitles()
{
	$(".kalaGroupTitle").slideDown();
}
function hideTitles()
{
	$(".kalaGroupTitle").slideUp();
}
