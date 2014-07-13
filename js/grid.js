function jshowGrid(indata,gname,targetFile)
{
	this.gname = gname;
	this.eRequest;
	this.gdata = indata;
	this.gcolumn;
	this.grow;
	this.gcss;
	this.gtableProperty;
	this.gtypeDef ;// {"varchar":"<input class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField\" type=\"text\" ",'int':"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_intField\" ","timestamp":"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_dateField\" "};
	this.gajaxTarget = targetFile;
	this.alertGrid;
	this.callBackFn={};
	this.scrollDown = false;
	this.getGrid = function(gname)
	{
		var out = "<div class=\""+gcss+"\">\n";
		var tmpRow;
		var cellId;
		var tmpCell;
		var tmpCol;
		var visibleCols=0;
		out += "<table "+gtableProperty+" >\n";
		out += "<tr>\n";
		if(gArgs[gname]['canDelete'])
		{
			out += "<th>\n";
			out += "<input type='checkbox' id='select_all_checks-"+gname+"' />\n";
			out += "</th>\n";
		}
		out += "<th>\nردیف\n</th>\n";
		for(colId in gcolumn)
		{
			tmpCol = gcolumn[colId];
			if(tmpCol['name'] && tmpCol['name'] != '')
			{
				out += "<th class = \""+tmpCol['css']+"\" >\n";
				out += (tmpCol['sort'])?"<span id=\""+gname+"-"+tmpCol['fieldname']+"\" class=\""+((tmpCol['sort']!='done')?"sortHeader":"sortedHeader")+"\" >":"";
				out += tmpCol['name'];
				out += (tmpCol['sort'])?"</span>":"";
				out += "</th>\n";
				visibleCols ++;
			}
		}
		out += "</tr>\n";
		var radif = 1+(gArgs[gname]['pageNumber']-1)*gArgs[gname]['pageRows'];
		for(i in grow)
		{
			tmpRow = grow[i]['cell'];
			out += "<tr "+((String(grow[i]['css']).indexOf('#')>=0)?("style=\"background:"+grow[i]['css']+";\""):("class=\""+grow[i]['css']+"\""))+" id='row-"+gname+"-"+i+"'>\n";
			if(gArgs[gname]['canDelete'])
			{
				out += "<td style='width:40px' >\n";
				out += "<input type='checkbox' class='"+gArgs[gname]['cssClass']+"_checkSelect' id='check-"+gname+"-"+i+"' />\n";
				out += "</td>\n";
			}
			out += "<td style='width:40px' >\n"+radif+"\n</td>\n";
			radif++;
			for(cellId in tmpRow)
				if(gcolumn[cellId]['name'] != '' || gcolumn[cellId]['fieldname'] == 'id')
				{
					tmpColumnList = (gcolumn[cellId]['clist'])?gcolumn[cellId]['clist']:null;
					tmpCell = tmpRow[cellId];
					tmpCell['css'] = (tmpCell['css']!='')?tmpCell['css']:gcolumn[cellId]['css'];
					out += "<td class = \""+tmpCell['css']+"\" "+((gcolumn[cellId]['fieldname'] == 'id' && gcolumn[cellId]['name'] == '')?"style=\"display:none;\"":"")+">\n";
					if(tmpColumnList == null)
					{
						out += "<span class=\""+gArgs[gname]['cssClass']+"_spanValue\" id=\""+gname+"-span-"+gcolumn[cellId]['fieldname']+"-"+i+"\">\n";
		                        	out += tmpCell['value'];
						out += "</span>\n";
						if(gArgs[gname]['canEdit'])
							if(!(gcolumn[cellId]['access']))
							{
								if(tmpCell['typ'] && gtypeDef[tmpCell['typ']])
									out += gtypeDef[tmpCell['typ']] + " id=\""+gname+"-"+gcolumn[cellId]['fieldname']+"-"+i+"\" name=\""+gname+"-"+gcolumn[cellId]['fieldname']+"-"+i+"\" "+((tmpCell['typ']!='blob')?"value=\"":">")+tmpCell['value']+((tmpCell['typ']!='blob')?"\" >":"</textarea>\n");
								else
									out += "<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField textValue\" id=\""+gname+"-"+gcolumn[cellId]['fieldname']+"-"+i+"\" name=\""+gname+"-"+gcolumn[cellId]['fieldname']+"-"+i+"\" value=\""+tmpCell['value']+"\" />";
							}
					}
					else
					{
						selDis = ((!gArgs[gname]['canEdit'] || gcolumn[cellId]['access'])?'disabled = "disabled"':'');
						out += "<select "+selDis+" class=\""+gArgs[gname]['cssClass']+"_selectValue\" id=\""+gname+"-select-"+gcolumn[cellId]['fieldname']+"-"+i+"\">\n";
						if(typeof gcolumn[cellId]['clistisnew']=='undefined'? true:gcolumn[cellId]['clistisnew'])
								out += listToCombo(tmpColumnList,tmpCell['value'],true);
						else
								out += listToCombo(tmpColumnList,tmpCell['value']);
						out += "</select>\n";
						
					}
	        	                out += "</td>\n";
				}
			out += "</tr>\n";
		}
		if(gArgs[gname]['canDelete'] || parseInt(gArgs[gname]['pageCount']) > 1 || gArgs[gname]['canAdd'])
		{
			out += "<tr>\n";
			out += "<td colspan='"+String(visibleCols+2)+"'>\n";
			out += "<table class=\""+gArgs[gname]['cssClass']+"_bottomTable\">\n";
			out += "<tr>\n";
			out += "<td align=\"right\">\n";
			if(gArgs[gname]['canDelete'])
			{
				out += "<button id='deleteRow-"+gname+"'>\n";
				out += "حذف";
				out += "</button>\n";
			}
			else
                	        out += "&nbsp;\n";
			out += "</td>\n";
			var rowCount = (typeof grid[gname].data.rowCount != 'undefined')?grid[gname].data.rowCount:0;
			out += "<td align='center'>\n";
			out += "تعداد : "+rowCount;
			if(parseInt(gArgs[gname]['pageCount'],10) > 1)
			{
				out += "<button id='nextPage-"+gname+"'>\n";
        		        out += "بعدی";
                		out += "</button>\n";
				var pageNumber = gArgs[gname]['pageNumber'];
				out += "<select class='"+gArgs[gname]['cssClass']+"_pageNumberSelector' id='pageNumber-"+gname+"'>\n";
				for(var pindx = 0;pindx < gArgs[gname]['pageCount'];pindx++)
				{
					if(parseInt(pageNumber) == pindx+1)
						out += "<option selected='selected' value='"+String(pindx+1)+"'>\n"+String(pindx+1)+"\n</option>\n";
					else
						out += "<option value='"+String(pindx+1)+"'>\n"+String(pindx+1)+"\n</option>\n";
				}
				out += "</select>\n";
				out += "<button id='prePage-"+gname+"'>\n";
		                out += "قبلی";
        		        out += "</button>\n";
			}	
			else	
				out += "&nbsp;\n";
			out += "</td>\n";
			out += "<td align=\"left\">\n";
			if(gArgs[gname]['canAdd'])
			{
				out += "<button id='addRow-"+gname+"'>\n";
        		        out += "ثبت";
                		out += "</button>\n";
			}
			else
				out += "&nbsp;\n";
			out += "</td>\n";
			out += "</tr>\n";
			out += "</table>\n";
			out += "</td>\n";
			out += "</tr>\n";
		}
		out += "</table>\n";
		out += "</div>\n";
		return(out);
	}
	function getRowId(rowNum,gname)
	{
		return(trim($("#"+gname+"-span-id-"+rowNum).html()));
	}
	function listToCombo(tmpList,sval,isNew)
	{
			var out = '';
			for(val in tmpList)
			{
					txt = tmpList[val];
					out += "<option value=\""+(isNew?txt.id:val)+"\" "+((sval == (isNew?txt.id:val))?"selected='selected'":'')+" >"+(isNew?txt.val:txt)+"</option>\n";
			}
			return(out);
	}
	function sendRequest(command,field,id,value,ajaxTarget,fromObj,gname)
	{
		canSave = false;
		if(gArgs[gname]['eRequest'])
                        ereq = $.param(gArgs[gname]['eRequest']);
		ur = ajaxTarget+"?command="+command+"&field="+field+"&value="+encodeURIComponent(value)+"&id="+id+"&table="+gname+"&"+ereq+"&";
		if(grid[gname].alertGrid)
			consol(ur);
		if(gArgs[gname]['beforeEdit'] && gArgs[gname]['beforeEdit']!='')
		{
			beforeEditFn = gArgs[gname]['beforeEdit'];
			beforeEditFn(gname,ur);
		}
		$("#"+fromObj).after("<img class=\""+gname+"-status\" src=\"../img/status_fb.gif\" alt=\"Loading . . .\"/>");
		$.get(ur,function(result){
			$("."+gname+"-status").remove();
			var res = trim(String(result));
			result = trim(String(result));
			tmp = fromObj.split('-');
			if(result.split(',').length == 3)
			{
				gname = result.split(',')[2];
				gArgs[gname]['pageCount'] = result.split(',')[1];
				result = result.split(',')[0];
			}
			if(gArgs[gname]['afterEdit'] && gArgs[gname]['afterEdit']!='')
			{
				afterEditFn = gArgs[gname]['afterEdit'];
				result = afterEditFn(gname,result);
			}
			var msg = result.split('|');
			result = msg[0];
			if(result == 'true')
				if(tmp.length == 3)
					$("#"+tmp[0]+"-span-"+tmp[1]+"-"+tmp[2]).html(value);
			else if(result != 'true' && tmp.length == 3)	
				$("#"+fromObj).html($("#span-"+fromObj).html());
			else
				$("#"+fromObj).html($("#span-"+fromObj).html());
			if(tmp.length == 3)
			{
				$("#"+fromObj).hide('slow');
				$("."+gArgs[gname]['cssClass']+"_spanValue").show('slow');
			}
			if(result == 'true')
				gridAlert('اصلاح با موفقیت انجام شد.','',gname);
			else
				gridAlert('اصلاح با موفقیت انجام نشد.'+((msg.length>1 && msg[1]!='')?":<br/>"+msg[1].split(',')[0]:''),'',gname);
		})
	}
	function deleteRequest(ids,ajaxTarget,rowNums,gname)
	{
		if(gArgs[gname]['eRequest'])
                        ereq = $.param(gArgs[gname]['eRequest']);
		if(grid[gname].alertGrid)
			consol(ajaxTarget+"?command=delete&ids="+ids+"&table="+gname+"&");
		if(gArgs[gname]['beforeDelete'] && gArgs[gname]['beforeDelete']!='')
		{
			beforeDeleteFn = gArgs[gname]['beforeDelete'];
			beforeDeleteFn(gname,ajaxTarget+"?command=delete&ids="+ids+"&table="+gname+"&");
		}
		$("#deleteRow-"+gname).after("<img class=\""+gname+"-status\" src=\"../img/status_fb.gif\" alt=\"Loading . . .\"/>");
		$.get(ajaxTarget+"?command=delete&ids="+ids+"&table="+gname+"&"+ereq+"&",function(result){
			$("."+gname+"-status").remove();
			result = trim(String(result));
                        if(result.split(',').length == 3)
                        {
				gname = result.split(',')[2];
                                gArgs[gname]['pageCount'] = result.split(',')[1];
                                result = result.split(',')[0];
                        }
			if(gArgs[gname]['afterDelete'] && gArgs[gname]['afterDelete']!='')
			{
				afterDeleteFn = gArgs[gname]['afterDelete'];
				result= afterDeleteFn(gname,result);
			}
			if(result.split('|')[0] == 'true')
			{
				for(rowid in rowNums)
					$("#row-"+gname+'-'+rowNums[rowid]).hide('slow');
				gridAlert('حذف با موفقیت انجام شد.','',gname);
			}
			else
				gridAlert('خطا در حذف'+((result.split('|').length > 1 && result.split('|')[1]!='')?":<br/>"+result.split('|')[1].split(',')[0]:''),'',gname);
		});
	}
	this.createAddBox = function (inp,gname,columnCount)
	{
		inp = grid[gname].column;
		var out = '<div class="darkDiv"></div>';
		out += '<div class="'+gArgs[gname]['cssClass']+'_addDiv" id="add-'+gname+'" ><table>';
		var j=1;
		for(i in inp)
		{
			if(inp[i]['name']!='')
			{
				tmp = j%columnCount;
				if(i==0)
					out +='<tr>';
				tmpColumnList = (inp[i]['clist'])?inp[i]['clist']:null;
				if(tmpColumnList == null && !(inp[i]['access']))
                                {
					var inpClass = '';
					if(inp[i]['typ']=='timestamp' || inp[i]['typ']=='datetime')
						inpClass = 'dateValue';
					else if(inp[i]['typ']=='int')
						inpClass = 'intValue';
					//inpClass = inp[i]['typ'];
					out +='<td align="left" >'+inp[i]['name']+':</td><td><input type="text" class="save_input-'+gname+' '+inpClass+' " id="'+gname+'-'+inp[i]['fieldname']+'" name="'+gname+'-'+inp[i]['fieldname']+'" placeholder="'+inp[i]['name']+' را وارد نمایید" ></td>';
					j++;
				}
				else if(tmpColumnList != null && !(inp[i]['access']))
				{
					out += '<td align="left" >'+inp[i]['name']+':</td><td>'+"<select class=\"save_input-"+gname+"\" id=\""+gname+'-'+inp[i]['fieldname']+"\" name=\""+gname+'-'+inp[i]['fieldname']+"\">\n";
					out += listToCombo(tmpColumnList,'',typeof inp[i]['clistisnew']=='undefined'? true:inp[i]['clistisnew']);
					out += "</select></td>\n";
					j++
				}
				if(tmp==0)
					out +='</tr><tr>';
			}
		}
		out +='</tr>';
		out +='<tr><td align="left" colspan="'+String(columnCount*2)+'" ><button id="save-'+gname+'" >ذخیره</button><button id="cancelSave-'+gname+'" >انصراف</button></td></tr>';
		out +='</table></div>';
		out +='<script>$(document).ready(function(){';
		out +='$.each($(".dateValue"),function(id,field){';
		out +='if(field.id)';
		out +='Calendar.setup({';
		out += 'inputField     :    field.id,';
		out += 'button:    field.id,';
		out +='ifFormat       :    "%Y/%m/%d",';
		out +='dateType           :    "jalali",';
		out += 'weekNumbers    : false';
		out += '});}); });';
		out +='</script>';
		return out;
	}
	this.saveData = function (gname,ajax_target,contentDiv,addColCount)
	{
                if(gArgs[gname]['eRequest'])
                        ereq = $.param(gArgs[gname]['eRequest']);
		var output = ajax_target+'?command=insert&table='+gname+"&"+ereq+"&";
		$.each($('.save_input-'+gname),function(id,field){
			output +=field.name+'='+encodeURIComponent(field.value)+'&';
		});
		init = this.init;
		if(grid[gname].alertGrid)
			consol(output);
		if(gArgs[gname]['beforeAdd'] && gArgs[gname]['beforeAdd']!='')
		{
			beforeAddFn = gArgs[gname]['beforeAdd'];
			beforeAddFn(gname,output);
		}
		$("#addRow-"+gname).after("<img class=\""+gname+"-status\" src=\"../img/status_fb.gif\" alt=\"Loading . . .\"/>");
		$.get(output,function(result){
			$("."+gname+"-status").remove();
                	if(result.split(',').length == 2)
	                {
		        	gArgs['pageCount'] = result.split(',')[1];
        			result = result.split(',')[0];
	                }
			if(gArgs[gname]['afterAdd'] && gArgs[gname]['afterAdd']!='')
			{
				afterAddFn = gArgs[gname]['afterAdd'];
				result = afterAddFn(gname,result);
			}
			var msg = result.split('|');
			result = msg[0];
			init(gArgs[gname]);
			if(parseInt(result,10)>0)
				gMsg[gname] = 'اطلاعات با موفقیت اضافه شد.';
			else
				gMsg[gname] ='خطا در ثبت.'+((msg.length > 1 && msg[1] != "")?":<br/>"+msg[1].split(',')[0]:''); 
		})
	}
	function gridAlert(msg,typ,gname)
	{
                        var gr = grid[gname];
			var addBox = '<div class="darkDiv"></div>';
			var tmp = msg.split('~');
			msg = tmp[0];
	                addBox += '<div class="'+((String(typ)!='')?String(typ):gArgs[gname]['cssClass'])+'_addDiv" id="add-'+gname+'" >';
                        addBox += msg;
			addBox += '</div>';
                        $("#add-"+gname).remove();
                        $("#"+contentDiv).prepend(addBox);
			setTimeout("killAlert('"+gname+"')",((tmp.length >1 && tmp[1]!='' && !isNaN(parseInt(tmp[1],10)) && typeof parseInt(tmp[1],10)!='undefined')?parseInt(tmp[1],10):4000));
                        $('#ok-'+gname).click(function(){
                                $("#add-"+gname).fadeOut('slow',function(){
                                        $(".darkDiv").next().remove();
                                        $(".darkDiv").remove();
                                });
                        });
                        $("#add-"+gname).center();
                        $(".darkDiv").center();
                        $("#add-"+gname).hide();
                        $("#add-"+gname).fadeIn('slow',function(){
				canSave = true;
                                $(window).scroll(function(){
                                        $("#add-"+gname).center();
                                        $(".darkDiv").center();
                                });
                                $(".darkDiv").click(function(){
                                        $("#add-"+gname).fadeOut('slow',function(){
                                                $(".darkDiv").next().remove();
                                                $(".darkDiv").remove();
                                        });
                                });
                        });
		
	}
	this.searchGrid = function (ser)
	{
		ggname = this.gname;
                var werc ='';
		//if(!whereObj[ggname])
			whereObj[ggname] = {};
                $.each($('.'+ser),function(id,field){
                        if(trim($(field).val()) != '' && $(field).is('input'))
			{
                                werc += ((werc=='')?' where ':' and ')+" (`"+$(field).prop("id")+"` like '|"+trim($(field).val())+"|') ";
				whereObj[ggname][$(field).prop("id")] = trim($(field).val());
			}
                        else if($(field).is('select')){
				var selectedText = $(field).prop('options')[$(field).prop('selectedIndex')].text;
				if((trim($(field).val()) != '-1' || trim(selectedText) != '') && (trim($(field).val()) != '0' || trim(selectedText) != '') && (trim($(field).val()) != '-1'|| trim(selectedText) != '&nbsp;') && (trim($(field).val()) != '0' || trim(selectedText) != '&nbsp;') && (trim($(field).val()) != '-1' || trim(selectedText) != 'همه') && (trim($(field).val()) != '0' || trim(selectedText) != 'همه'))
				{
					werc += ((werc=='')?' where ':' and ')+" (`"+$(field).prop("id")+"` = '"+trim($(field).val())+"') ";
					whereObj[ggname][$(field).prop("id")] = trim($(field).val());
				}
			}
                });
		whereClause[ggname] = '';
                whereClause[ggname] = encodeURIComponent(werc);
                grid[ggname].init(gArgs[ggname]);
	}
	this.createSearchBox = function ()
	{
		gname = this.gname;
		var cols = this.column;
		var out = '<table ><tr>';
		var colCount = 3;
		var tagname,tagend;
		var hasSer = true;
		var coli = 1;
		for(var i = 1;i <= cols.length;i++)
		{
			if(cols[i-1].search)
			{
				tagname = '<input type="text" class="grid_search_'+gname+'" autocomplete="off"  id="';
				tagend = '" />';
				if(cols[i-1].search == 'dateValue')
					tagname = '<input type="text" class="grid_search_'+gname+' dateValue" id="';
				else if(cols[i-1].search == 'list')
				{
					tagname = '<select class="grid_search_'+gname+'" id="';
					tagend = '">';
					var columns = cols[i-1].searchDetails;
					var isArr = typeof columns=='array' ? true:false;
					for(j in columns)
							tagend += '<option value="'+(isArr?j:columns[j].id)+'" >'+(isArr?columns[j]:columns[j].val)+'</option>';
					tagend += '</select>';
				}
				out += '<td>'+cols[i-1].name+'</td>';
				out += '<td>'+tagname+cols[i-1].fieldname+tagend+'</td>';
				if(coli % colCount == 0 && coli > 1)
					out += '</tr><tr>';
				coli++;
			}
		}
		hasSer = (out != '<table ><tr>');
		out += '</tr>';
		out += '<tr><td colspan="'+(colCount+1)+'">';
		out += '<button onclick="grid[\''+gname+'\'].searchGrid(\'grid_search_'+gname+'\');">جستجو</button>';
		out += '</td></tr>';
		if(!hasSer)
			out = '';
		return(out);
	}

	this.ready = function(ajaxTarget,gname,contentDiv,addColCount)
	{
		canSave = true;
		jQuery.fn.center = function () {
		    var myHeight = this.height();
		    var parentHeight = (this.parent().parent().parent().prop('id')=='dialog')?this.parent().parent().parent().height():$(window).height();//this.parent().height();
		    var myWidth = this.width();
		    var parentWidth = (this.parent().parent().parent().prop('id')=='dialog')?this.parent().parent().parent().width():$(window).width();//this.parent().width();
		    this.css("position","absolute");
		    //this.css("top", $(window).scrollTop()+Math.max(0, (parentHeight - myHeight) / 2) + "px");
		    //this.css("left", Math.max(0, (parentWidth - myWidth) / 2) + "px");
		    return this;
		}
		$.each($(".dateValue"),function(id,field){
	                Calendar.setup({
        		        inputField     :    field.id,
                		button         :    field.id,
	                	ifFormat       :    "%Y/%m/%d",
	        	        dateType           :    'jalali',
        	        	weekNumbers    : false
	                });			
		});
		$(".sortHeader").click(function(){
			tgname = this.id.split('-')[0];
			tfieldName = this.id.split('-')[1];
			if(!gArgs[tgname]['sort'])
				gArgs[tgname]['sort'] = {};
			gArgs[tgname]['sort']["sort-"+tfieldName] = tfieldName;
			grid[tgname].init(gArgs[tgname]);
		});
		$(".sortedHeader").click(function(){
			tgname = this.id.split('-')[0];
                        tfieldName = this.id.split('-')[1];
			if(gArgs[tgname]['sort']["sort-"+tfieldName])
				delete gArgs[tgname]['sort']["sort-"+tfieldName];
			grid[tgname].init(gArgs[tgname]);
                });
		if(gArgs[gname]['disableRowColor'])
		{
			$("#"+contentDiv+" table tr").mouseover(function(){
				$(this).css("background-image","url(../img/rang.png)");
			});
		}
		else
		{
			$("#"+contentDiv+" table tr:even").css('background','#eeeeee');
			//$("#"+contentDiv+" table tr:even").css('background','#e7bff3');
			//$("#"+contentDiv+" table tr:odd").css('background','#f4e1b5');
			$("#"+contentDiv+" table tr").mouseover(function(){
                                //$(this).css("background-image","url(../img/even.png)");
				$(this).css("background-color","#f8b924");
				//$(this).css("border","solid 2px #666666");
                        });
				
		}
		$("#"+contentDiv+" table tr:even").mouseout(function(){
       	                $(this).css('background-color','#eeeeee');
			//$(this).css("border","solid 1px #999999");
               	});
		$("#"+contentDiv+" table tr:odd").mouseout(function(){
       	                $(this).css('background-color','');
			//$(this).css("border","solid 1px #999999");
               	});
		$("."+gArgs[gname]['cssClass']+"_forField").hide();
		$("."+gArgs[gname]['cssClass']+"_forField").blur(function(){
			$("."+gArgs[gname]['cssClass']+"_forField").fadeOut('slow');
			$("."+gArgs[gname]['cssClass']+"_spanValue").fadeIn('slow');
		});
		$(".intValue").bind('keypress',function(e){
			var e=window.event || e
			var keyunicode=e.charCode || e.keyCode
			return ((keyunicode>=48 && keyunicode<=57) || keyunicode==45)? true : false
		});
		if(gArgs[gname]['canEdit'])
		{
			$.each($("."+gArgs[gname]['cssClass']+"_spanValue"),function(spanId,spanField)
			{
				var finame = spanField.id.split('-')[2];
				var findex = -1;
				for(fin in grid[gname]['column'])
					if(grid[gname]['column'][fin]['fieldname'] == finame)
						findex = fin;
				var acc = true;
				if(grid[gname]['column'][findex])
					if(grid[gname]['column'][findex]['access'])
						acc = false;
				if(acc && trim(spanField.innerHTML) != '')
				{
					$("#"+spanField.id).css("cursor","pointer");
					$("#"+spanField.id).click(function(){
                                                var tt = $("#"+spanField.id).parent().parent().prop('id').split('-');
                                                gname = tt[1];
                                                var sid = spanField.id
                                                $("#"+sid).hide('slow');
                                                var tmp = sid.split('-');
                                                $("#"+tmp[0]+"-"+tmp[2]+"-"+tmp[3]).show('slow',function(){
                                                        tmp = this.id.split('-');
                                                        gname = tmp[0];
                                                        fieldId = getRowId(tmp[2],gname);
                                                        $("#"+tmp[0]+"-"+tmp[1]+"-"+tmp[2]).focus();
                                                        $("#"+tmp[0]+"-"+tmp[1]+"-"+tmp[2]).bind('keypress', function(e) {
                                                                var code = (e.keyCode ? e.keyCode : e.which);
                                                                tmp = this.id.split('-');
                                                                if(code == 13 && canSave)
                                                                        sendRequest('update',tmp[1],fieldId,this.value,ajaxTarget,tmp[0]+"-"+tmp[1]+"-"+tmp[2],tmp[0]);
                                                        });
                                                });
                                        });
				}
				else if(trim(spanField.innerHTML) == '' && acc)
				{
					$("#"+spanField.id).parent().css("cursor","pointer");
					$("#"+spanField.id).parent().click(function(){
                	                        var tt = $("#"+spanField.id).parent().parent().prop('id').split('-');
                        	                gname = tt[1];
						var sid = spanField.id
                                        	$("#"+sid).hide('slow');
	                                        var tmp = sid.split('-');
        	                                $("#"+tmp[0]+"-"+tmp[2]+"-"+tmp[3]).show('slow',function(){
                	                                tmp = this.id.split('-');
                        	                        gname = tmp[0];
                                	                fieldId = getRowId(tmp[2],gname);
                                        	        $("#"+tmp[0]+"-"+tmp[1]+"-"+tmp[2]).focus();
                                                	$("#"+tmp[0]+"-"+tmp[1]+"-"+tmp[2]).bind('keypress', function(e) {
                                                        	var code = (e.keyCode ? e.keyCode : e.which);
	                                                        tmp = this.id.split('-');
        	                                                if(code == 13 && canSave)
                	                                                sendRequest('update',tmp[1],fieldId,this.value,ajaxTarget,tmp[0]+"-"+tmp[1]+"-"+tmp[2],tmp[0]);
                        	                        });
                                	        });
	                                });
				}
			});
			$("."+gArgs[gname]['cssClass']+"_selectValue").change(function(){
				var tmp = this.id.split('-');
				var val = this.value;
				var idd = this.id;
				if(confirm('آیا تغییر کند؟'))
				{
					var tt = this.id.split('-');//$("."+gArgs[gname]['cssClass']+"_selectValue").prop('id').split('-');
					gname = tt[0];
					fieldId = getRowId(tmp[3],gname);
                                    	sendRequest('update',tmp[2],fieldId,val,ajaxTarget,idd,gname);
				}
			});
		}
		co = gcolumn;
		saveData = this.saveData;
		$("#addRow-"+gname).click(function(){
			args = gArgs[gname];
			var gr = grid[gname];
			var addBox = gr.createAddBox(co,gname,addColCount);
			$("#add-"+gname).remove();
			$("#"+contentDiv).prepend(addBox);
			if(args['afterCreateAdd'] && args['afterCreateAdd']!='')
                        {
                                afterCreateAddFn = args['afterCreateAdd'];
                                afterCreateAddFn();
                        }
			$(".intValue").bind('keypress',function(e){
				var e=window.event || e
				var keyunicode=e.charCode || e.keyCode
				return (keyunicode>=48 && keyunicode<=57)? true : false
			});
			$("#save-"+gname).click(function(){
				grid[gname].saveData(gname,ajaxTarget,contentDiv,addColCount);
			});
			$('#cancelSave-'+gname).click(function(){
				$("#add-"+gname).fadeOut('slow',function(){
					$(".darkDiv").next().remove();
					$(".darkDiv").remove();
				});
			});
			$("#add-"+gname).center();
			$(".darkDiv").center();
			$("#add-"+gname).hide();
			$("#add-"+gname).fadeIn('slow',function(){
				$(window).scroll(function(){
					$("#add-"+gname).center();
					$(".darkDiv").center();
				});
				$(".darkDiv").click(function(){
					$("#add-"+gname).fadeOut('slow',function(){
						$(".darkDiv").next().remove();
						$(".darkDiv").remove();
					});		
				});
			});
		});
		$("#deleteRow-"+gname).click(function(){
			if(confirm('آیا حذف شود؟'))
			{
				var ids = '';
				var rowNums = [];
				$.each($("."+gArgs[gname]['cssClass']+"_checkSelect"),function(id,field){
					var tmp = field.id.split('-');
					if(tmp[1] == gname && field.checked)
					{
						rowNums[rowNums.length] = tmp[2];
						var realId = getRowId(tmp[2],gname);
						ids += ((ids != '')?',':'')+realId;
					}
				});
				if(ids != '')
					deleteRequest(ids,ajaxTarget,rowNums,gname);
				else
					gridAlert('گزینه‌ای برای حذف وجود ندارد.');
			}
		});
		$("#pageNumber-"+gname).change(function(){
			gArgs[gname]['pageNumber'] = this.value;
			grid[gname].init(gArgs[gname]);
		});
		$("#nextPage-"+gname).click(function(){
			if(parseInt(gArgs[gname]['pageNumber'])+1 <= parseInt(gArgs[gname]['pageCount']))
				gArgs[gname]['pageNumber']++;
			else
				gArgs[gname]['pageNumber'] = 1;
                        grid[gname].init(gArgs[gname]);
		});
		$("#prePage-"+gname).click(function(){
                        if(parseInt(gArgs[gname]['pageNumber'])-1 >= 1)
                                gArgs[gname]['pageNumber']--;
                        else
                                gArgs[gname]['pageNumber'] = parseInt(gArgs[gname]['pageCount']);
                        grid[gname].init(gArgs[gname]);
                });
		$("#select_all_checks-"+gname).click(function(){
			$.each($("."+gArgs[gname]['cssClass']+"_checkSelect"),function(id,field){
                                var tmp = field.id.split('-');
                                if(tmp[1] == gname)
					field.checked = ($("#select_all_checks-"+gname).attr('checked'))?true:false;
                	});
		});
	}
	this.setResult = function(result)
	{
		gdata = result;
		if(result['column'])
		{
			hcols = hiddenColumns[gname];
			for(i in hcols)
				if(result['column'][hcols[i]])
					result['column'][hcols[i]]['name'] = '';
		}
	        gcolumn = (result['column'])?result['column']:[];
        	grow = (result['rows'])?result['rows']:[];
	        gcss = (result['css'])?result['css']:[];
		eRequest = (result['eRequest'])?result['eRequest']:[];
        	gtableProperty = (result['tableProperty'])?result['tableProperty']:[];
		gtypeDef = {"varchar":"<input class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField\" type=\"text\" ",
			'int':"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField intValue\" ",
			"timestamp":"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField dateValue\" ",
			"datetime":"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField dateValue\" ",
			"blob":"<textarea class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField blobValue\" cols=\"10\" rows=\"10\" "};
		alertGrid = (result['alert'])?result['alert']:false;
		this.data = result;
		this.scrollDown = result['scrollDown'];
		this.column = (result['column'])?result['column']:[];
		this.row = (result['rows'])?result['rows']:[];
		this.css = (result['css'])?result['css']:[];
		this.eRequest = (result['eRequest'])?result['eRequest']:[];
		this.tableProperty = (result['tableProperty'])?result['tableProperty']:[];
		this.typeDef = {"varchar":"<input class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField\" type=\"text\" ",'int':"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField intValue\" ","timestamp":"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField dateValue\" ","datetime":"<input type=\"text\" class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField dateValue\" ","blob":"<textarea class=\""+gArgs[gname]['cssClass']+"_forField "+gArgs[gname]['cssClass']+"_textField blobValue\" cols=\"10\" rows=\"10\" "};
		this.alertGrid = (result['alert'])?result['alert']:false;
	}
	this.init = function(args)
	{
		targetFile = args['targetFile'];
		gname = args['gname'];
		pageNumber = ((args['pageNumber'])?args['pageNumber']:1);
		setResult = this.setResult;
		getGrid = this.getGrid;
		gready = this.ready;
		var ereq = '';
		if(args['eRequest'] && args['eRequest']!='')
			ereq = "&"+$.param(args['eRequest'])+"&";
		var sort = '';
		if(args['sort'] && args['sort']!='')
			sort = "&"+$.param(args['sort'])+"&";
		if(args['alert'] && args['alert']===true)
			consol(targetFile+"?pageNumber="+pageNumber+"&table="+gname+"&werc="+whereClause[gname]+ereq+sort);
		contentDiv = args['contentDiv'];
		$("#"+contentDiv).html("<img src='../img/status_fb.gif' />");
		if(args['beforeLoad'] && args['beforeLoad']!='')
		{
			beforeLoadFn = args['beforeLoad'];
			beforeLoadFn(gname,gArgs[gname]);
		}
		$.getJSON(targetFile+"?pageNumber="+pageNumber+"&table="+gname+"&werc="+whereClause[gname]+ereq+sort,function(result){
			gArgs[gname]['pageRows'] = result.pageRows;
			args = gArgs[gname];
			contentDiv = args['contentDiv'];
			targetFile = args['targetFile'];
			var ht;
			setResult(result);
			grid[gname].setResult(result);
			gArgs[gname]['pageCount'] = ((result['pageCount'])?result['pageCount']:gArgs[gname]['pageCount']);
			grid[gname].ajaxTarget = targetFile;
			ht = getGrid(gname);
			$("#"+contentDiv).html(ht);
			$("."+gArgs[gname]['cssClass']+"_forField").hide();
			$("#"+contentDiv).hide();
			$("#"+contentDiv).fadeIn('slow',function(){
				if(grid[gname].scrollDown)
					$("html, body").animate({ scrollTop: $(document).height() }, 1000);
			});
			grid[gname].ready(targetFile,gname,contentDiv,addColCount);
			$("#searchDiv_"+gname).remove();
			var searchBlock = grid[gname].createSearchBox();
			if(searchBlock != '')
				$("#"+contentDiv).before("<div id=\"searchDiv_"+gname+"\">"+searchBlock+"</div>");
			var whereO = whereObj[gname];
			for(i in whereO)
				$("#"+i).val(whereO[i]);
			$.each($(".dateValue"),function(id,field){
		                Calendar.setup({
        			        inputField     :    field.id,
                			button         :    field.id,
	                		ifFormat       :    "%Y/%m/%d",
		        	        dateType           :    'jalali',
        		        	weekNumbers    : false
	        	        });			
			});
			if(gMsg[gname] && gMsg[gname] != '')
			{
				gridAlert(gMsg[gname] , '' ,gname);
				gMsg[gname] = '';
			}
			if(args['afterLoad'] && args['afterLoad']!='')
			{
				afterLoadFn = args['afterLoad'];
				afterLoadFn(gname,result);
			}
		});
	}
}
function trim(str)
{
	out=str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        return out;
}
function killAlert(gname)
{
        var gr = grid[gname];
	$("#add-"+gname).fadeOut('slow',function(){
		$(".darkDiv").next().remove();
		$(".darkDiv").remove();
	});
}
function consol(inp)
{
	console.log(inp)
}
//	Add Item Block
//	End of Add item
var gArgs = {};
var grid = {};
var gMsg = {};
var whereClause = {};
var whereObj={};
var hiddenColumns = {};
var canSend = true;
function intialGrid(args)
{
	for(i in args)
	{
		gArgs[i] = args[i];
		contentDiv = args[i]['contentDiv'];
                targetFile = args[i]['targetFile'];
       	        gname = args[i]['gname'];
               	addColCount = args[i]['addColCount'];
                pageNumber = args[i]['pageNumber'];
       	        pageCount = args[i]['pageCount'];
		var result;
		grid[i] = new jshowGrid(result,gname,targetFile);
		whereClause[gname] = ((args[i]['werc'])?args[i]['werc']:'');
		if(args[i]['start'])
			grid[i].init(args[i]);
	}
}
