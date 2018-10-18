<?php
function portaltitle_contents() { 
  $name = getRequestVarString('name','obesity');
  $sqlPortal = "select FULL_NAME from PORTAL1 where URL_NAME = '" . $name . "'";
  $myresult = fetchRecord($sqlPortal);
  extract ( $myresult ) ;
  if ( $myresult == NULL ) {
        return "RGD Portal";
  } else { 
	      return $FULL_NAME . " - Rat Genes, QTLs, and Strains - Rat Genome Database"; 
  }
}
?>
