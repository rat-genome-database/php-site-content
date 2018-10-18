
function verify(f)
{
    if(f.searchKeyword.value == null ||f.searchKeyword.value == "" || f.searchKeyword.value == " * for wildcard"){
        alert("The form was not submitted because you have to enter a keyword to search on;");
        return false;
    }else{
        return true;
    }
}

function getTabIndex() {
    var menuMap = new Array(new Array(0), new Array("/data-entry", "/genes", "/objectSearch","/GO", "/strains", "/maps",  "/sequences", "/references", "/plfRGD"), new Array("/tool-entry", "/VCMAP", "/ontoloty", "/gviewer",  "/sequenceresource", "/ACPHAPLOTYPER" ,"/METAGENE", "/GENOMESCANNER"), new Array("/tools/Diseases","/dportal","/portal","/diseases") , new Array("gbreport","GenomeErrorReport"), new Array("nomen","registration-entry","/community-entry"));

	var index=0;

	for (i=0; i< menuMap.length; i++) {
		for (j=0; j< menuMap[i].length; j++) {
		        //alert(document.location.href + " " + menuMap[i][j]);
                        //if (ddtabmenu.currentpageurl.indexOf(menuMap[i][j]) != -1) {
                        if (document.location.href.indexOf(menuMap[i][j]) != -1) {
 				return i;
			}
		}
	}
     return index; 
}

