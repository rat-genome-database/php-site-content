<?php
function portalBodyOnLoad_contents() { 
  $PORTAL_CAT_ID  = 0; 
  $PORTAL_KEY  = 0;
  $PORTAL_VERSION = 0;
  $name = getRequestVarString('name','obesity');
//  $sqlPortal = "select PORTAL_KEY, PORTAL_VERSION from PORTAL1 where URL_NAME = '" . $name . "'";
//  // echo $sqlPortal;
//  $myresult = fetchRecord($sqlPortal);
//  extract ( $myresult ) ;
//  $sqlFirstCategorySql = "select PORTAL_CAT_ID from PORTAL_CAT1 where PORTAL_KEY = " . $PORTAL_KEY . " and  PORTAL_VERSION = " .  $PORTAL_VERSION . " and parent_cat_id is NULL ";
//  $sqlFirstCategorySql = "select PORTAL_CAT_ID from PORTAL_CAT1 where PORTAL_VER_ID = " . $PORTAL_KEY . " and  PORTAL_VERSION = " .  $PORTAL_VERSION . " and parent_cat_id is NULL ";
  
   $sqlFirstCategorySql = "select pc.PORTAL_CAT_ID, pv.PORTAL_VER_ID from 
    portal_cat1 pc , portal_ver1 pv, portal1 p 
    where p.portal_key = pv.portal_key
    and pv.portal_ver_status = 'Active' 
    and pv.portal_ver_id = pc.portal_ver_id
    and p.url_name = '". $name . "'
    and pc.parent_cat_id is NULL ";
    
     
  $myresult1 = fetchRecord($sqlFirstCategorySql);
  extract ( $myresult1 ) ;
  //return "$name"; 
  // These need to match the portal.portal_show() method
  $retString = "ajaxCheckbox('/rgdCuration/?module=portal&func=updateGviewer&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'gviewer' );" .
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=updateCategory&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'category' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=getSummaryTableHTML&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'summaryTable' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=getGenesInfoTableHTML&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'geneInfo' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=getQTLInfoTableHTML&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'qtlInfo' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=getStrainInfoTableHTML&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'strainInfo' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=updateRatFlash&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'rat-flash' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=updateHumanFlash&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'human-flash' );".
  		"ajaxCheckbox('/rgdCuration/?module=portal&func=updateMouseFlash&species=rat&firstSelect=". $PORTAL_CAT_ID . "', 'mouse-flash' );". 
        "ajaxCheckbox('/rgdCuration/?module=portal&func=updateSynteny&species=rat&URLName=Rat%20Synteny&firstSelect=". $PORTAL_CAT_ID . "', 'rat-Synteny' );".
      "ajaxCheckbox('/rgdCuration/?module=portal&func=updateSynteny&&synteny=human&species=rat&URLName=Human%20Synteny&firstSelect=". $PORTAL_CAT_ID . "', 'human-Synteny' );".
      "ajaxCheckbox('/rgdCuration/?module=portal&func=updateSynteny&synteny=mouse&species=rat&URLName=Mouse%20Synteny&firstSelect=". $PORTAL_CAT_ID . "', 'mouse-Synteny' );". 
      "ajaxCheckbox('/rgdCuration/?module=portal&func=getHomeChartCCFlash&portalVerID=". $PORTAL_VER_ID . "', 'cc-pie' );" . "ajaxCheckbox('/rgdCuration/?module=portal&func=getHomeChartBPFlash&portalVerID=". $PORTAL_VER_ID . "', 'bp-pie' );" . "ajaxCheckbox('/rgdCuration/?module=portal&func=getHomeChartMPFlash&portalVerID=". $PORTAL_VER_ID . "', 'mp-pie' );";
  	
  return $retString;
}
?>