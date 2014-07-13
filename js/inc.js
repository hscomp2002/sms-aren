function wopen(url, name, w, h)
{
  w += 32;
  h += 96;
  wleft = (screen.width - w) / 2;
  wtop = (screen.height - h) / 2;
  if (wleft < 0) {
    w = screen.width;
    wleft = 0;
  }
  if (wtop < 0) {
    h = screen.height;
    wtop = 0;
  }
  var win = window.open(url,
    name,
    'width=' + w + ', height=' + h + ', ' +
    'left=' + wleft + ', top=' + wtop + ', ' +
    'location=no, menubar=no, ' +
    'status=no, toolbar=no, scrollbars=yes, resizable=yes');
  win.resizeTo(w, h);
  win.moveTo(wleft, wtop);
  win.focus();
}
function drawGall(kala_gall,mod)
{
	var out ="<table>";
	for(var i=0;i<kala_gall.length;i++)
	{
		if((i+1)%7==0)
			out+='<tr>';
		out+='<td style="padding:7px;border:dashed 1px #666666;" >'+"<a class='lbox' href='../img/kala/"+kala_gall[i].pic+"' title='' ><img width='60' src='../img/kala/thumb_"+kala_gall[i].pic+"' ></a>";
		if(typeof mod!='undefined')
			out+="<div><input type='checkbox' class='gallery'  id='galdel_"+kala_gall[i].id+"' ></div></td>";
		else
			out+="</td>";
		if((i+1)%7==0)
			out+='</tr>';
	}
	out +="</table>";
	return(out);
}
function isCopyPressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 99;
}
function isPastePressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 118;
}
function getEventKeyCode(e) {
    var key;
    if (window.event)
        key = event.keyCode;
    else
        key = e.which;

    return key;
}
function ignoreKeys(key) {
    if (key == 0) { //function keys and arrow keys
        return true;
    }
    if (key == 13) { //return
        return true;
    }
    if (key == 8) { //backspace
        return true;
    }
    if (key == 9) { // tab
        return true;
    }

    return false;
}
function numbericOnKeypress(e) {

    if (isCopyPressed(e) || isPastePressed(e))
        return;
    var key = getEventKeyCode(e);

    if (key == 45 || key == 44 || key == 46) return true;// ',' '.' '-'
    if (ignoreKeys(key)) return true;
    if (isNumericKeysPressed(key)) { // Numbers
        return true;
    }
    if (window.event) //IE
        window.event.returnValue = false;
    else              //other browser
        e.preventDefault();
}
function isNumericKeysPressed(key) {

    if (key >= 48 && key <= 57) { // Numbers
        return true;
    }

    return false;
}
function isMobile(inp)
{
	var ou = false;
	if(isNumber(inp.substring(1,inp.length)))
	{
		if(inp.length==13 && inp.substring(0,1)=="+")
			ou = true;
		else if(inp.length==11 && inp.substring(0,1)=="0" && inp.substring(1,2)=="9")
			ou = true;
	}
	return (ou);
}
function isNumber(inp){
	var out=false;
	var sht=inp.replace(/,/gi,'');
	sht=sht.replace(/\./gi,'');
	if(String(parseInt(sht,10))==String(sht)){
		out=true;
	}
	return(out);
}
function jToM(fi)
{
	fi = unFixNums(fi);
	var tmpTr = fi.split('/');
	var Y = parseInt(tmpTr[0],10);
	var D = parseInt(tmpTr[2],10);
	var m = parseInt(tmpTr[1],10);
	if(D>Y)
	{
		Y = parseInt(tmpTr[2]);
		D = parseInt(tmpTr[0]);
	}
	tmpTr = JTG(Y,m,D,'');
	tmpTr = tmpTr.replace(/\//g,'-');
	return tmpTr;
}
