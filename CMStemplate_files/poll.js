
//set the poll to be displayed.  Someday this should come from the database
var pollId=181;

function initPoll() {
	if (getCookie("Voted" + pollId) != null && (getCookie("Voted" + pollId) == "Voted" || parseInt(getCookie("Voted" + pollId)) > 2)) {
		return;	
	}
	
	initPopUp("skip()");
	showPopWin('http://' + window.location.hostname + '/plf/plfRGD/index.php?module=poll&func=renderDialog&pollID=' + pollId, 480, 600, null);		
	//ajaxpage('http://' + window.location.hostname + '/plf/plfRGD/index.php?module=poll&func=renderDialog&pollID=' + pollId, 'poll-splash')
	document.getElementById("popupContainer").style.visibility="visible";

	if (getCookie("Voted" + pollId) == null) {
		var myDate=new Date()
		myDate.setDate(myDate.getDate()+365)			
		setCookie("Voted" +pollId, "1", myDate, "/") 		
	}else {
		setCookie("Voted" +pollId, parseInt(getCookie("Voted" + pollId)) + 1, myDate, "/");
	} 		
}

//called following poll submission
function pollSubmit(form) {
	hidePopWin(false);
	ajaxpage('http://' + window.location.hostname + '/plf/plfRGD/index.php?module=poll&func=render&pollID=' + pollId + '&' + getFormString(form), 'poll')
}

//called when the poll is skiped
function skip() {
	hidePopWin(false);
	ajaxpage('http://' + window.location.hostname + '/plf/plfRGD/index.php?module=poll&func=render&skip=1&pollID=' + pollId, 'poll')	
}

//returns the contents of a form in a url encoded get string
function getFormString(theform) {
var formStr = "";
var amp = "";

for(i=0; i<theform.elements.length; i++){	
	if (theform.elements[i].name != "module" && theform.elements[i].name != "func") {
		if(theform.elements[i].type == "text" || theform.elements[i].type == "textarea" || theform.elements[i].type == "hidden"){
			formStr += amp+theform.elements[i].name+"="+encodeURIComponent(theform.elements[i].value);
		} else if ((theform.elements[i].type == "checkbox" || theform.elements[i].type == "radio") && theform.elements[i].checked) {
			formStr += amp+theform.elements[i].name+"="+encodeURIComponent(theform.elements[i].value);
		} else if (theform.elements[i].type == "select-one") {
			formStr += amp+theform.elements[i].name+"="+theform.elements[i].options[theform.elements[i].selectedIndex].text;
		}
		amp = "&";
	}
}
return formStr;
}//end PostAform function 



/*
  name - name of the desired cookie
  return string containing value of specified cookie or null
  if cookie does not exist
*/

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

 /*
   name - name of the cookie
   value - value of the cookie
   [expires] - expiration date of the cookie
     (defaults to end of current session)
   [path] - path for which the cookie is valid
     (defaults to path of calling document)
   [domain] - domain for which the cookie is valid
     (defaults to domain of calling document)
   [secure] - Boolean value indicating if the cookie transmission requires
     a secure transmission
   * an argument defaults when it is assigned null as a placeholder
   * a null placeholder is not required for trailing omitted arguments
*/

function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}



/***********************************************
* Dynamic Ajax Content- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var bustcachevar=1 //bust potential caching of external pages after initial request? (1=yes, 0=no)
var loadedobjects=""
var rootdomain="http://"+window.location.hostname
var bustcacheparameter=""

function ajaxpage(url, containerid){
var page_request = false
if (window.XMLHttpRequest) // if Mozilla, Safari etc
page_request = new XMLHttpRequest()
else if (window.ActiveXObject){ // if IE
try {
page_request = new ActiveXObject("Msxml2.XMLHTTP")
}
catch (e){
try{
page_request = new ActiveXObject("Microsoft.XMLHTTP")
}
catch (e){}
}
}
else
return false
page_request.onreadystatechange=function(){
loadpage(page_request, containerid)
}
if (bustcachevar) //if bust caching of external page
bustcacheparameter=(url.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
page_request.open('GET', url+bustcacheparameter, true)
page_request.send(null)
}

function loadpage(page_request, containerid){
if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1))

if (page_request.responseText.indexOf("pollForm") != -1) {
    if (document.getElementById(containerid)) {	
        if (page_request.responseText.indexOf("resultBar") == -1) {
		document.getElementById(containerid).innerHTML=page_request.responseText;
	}else {
		document.getElementById(containerid).innerHTML="";		
	}
    }
}

}

function loadobjs(){
if (!document.getElementById)
return
for (i=0; i<arguments.length; i++){
var file=arguments[i]
var fileref=""
if (loadedobjects.indexOf(file)==-1){ //Check to see if this object has not already been added to page before proceeding
if (file.indexOf(".js")!=-1){ //If object is a js file
fileref=document.createElement('script')
fileref.setAttribute("type","text/javascript");
fileref.setAttribute("src", file);
}
else if (file.indexOf(".css")!=-1){ //If object is a css file
fileref=document.createElement("link")
fileref.setAttribute("rel", "stylesheet");
fileref.setAttribute("type", "text/css");
fileref.setAttribute("href", file);
}
}
if (fileref!=""){
document.getElementsByTagName("head").item(0).appendChild(fileref)
loadedobjects+=file+" " //Remember this object as being already added to page
}
}
}

