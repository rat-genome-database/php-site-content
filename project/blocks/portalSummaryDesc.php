<?php
function portalSummaryDesc_contents() { 
  $name = getRequestVarString('name','obesity');
  $sqlPortal = "select PAGE_SUMMARY_DESC from PORTAL1 where URL_NAME = '" . $name . "'";
  // echo $sqlPortal;
  $myresult = fetchRecord($sqlPortal);
  extract ( $myresult ) ;
  if ( $myresult == NULL ) {
        return "All Data is shown below";
  } else { 
	      return $PAGE_SUMMARY_DESC; 
  }
}
?>
