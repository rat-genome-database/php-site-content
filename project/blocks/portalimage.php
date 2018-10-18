<?php
function portalimage_contents() { 
  $name = getRequestVarString('name','obesity');
  $sqlPortal = "select PAGE_IMG_URL from PORTAL1 where URL_NAME = '" . $name . "'";
  // echo $sqlPortal;
  $myresult = fetchRecord($sqlPortal);
  extract ( $myresult ) ;
  if ( $myresult == NULL ) {
        return "/common/dportal/images/neurological.gif";
  } else { 
	      return $PAGE_IMG_URL; 
  }
}
?>
