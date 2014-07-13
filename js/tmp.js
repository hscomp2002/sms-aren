var boxWidth = 165;
var boxPadd = 20;
var windowWidthBorder = 30;
var paren = 'tabs';
function getRowCount(bw)
{
	var boxW;
	if(typeof bw == 'undefined')
		boxW = boxWidth+boxPadd;
	else
		boxW = bw;
	//var windowW = ($(document).width()-(2*windowWidthBorder)>0)?($(document).width()-(2*windowWidthBorder)):$(document).width();
	var windowW = ($("#kalaDiv").width()-(2*windowWidthBorder)>0)?($("#kalaDiv").width()-(2*windowWidthBorder)):$("#kalaDiv").width();
	//var windowW = ($("#"+paren).width()-(2*windowWidthBorder)>0)?($("#"+paren).width()-(2*windowWidthBorder)):$("#"+paren).width();
	var boxCount = (windowW - (windowW % boxW))/boxW;
	//console.log('windowWidth = '+windowW+','+$(document).width()+',count'+boxCount);
	return(boxCount);
}
function drawTable(inp)
{
	if(typeof inp=='undefined')
		inp = kala;
	var rowCount = getRowCount();
	var out = '';//'<table><tr>';
	var rowIndex = 0;
	for(var i=0;i < inp.length;i++)
	{
		out += '<td>'+drawKala(inp[i])+'</td>';
		if(rowIndex >= rowCount-1)
		{
			rowIndex = 0;
			out += '</tr><tr>';
		}
		else
			rowIndex++;
	}
	out += '</tr></table>';
	out = '<table'+((rowIndex>1)?'':' width="100%"')+'><tr>'+out;
	return(out);
}
