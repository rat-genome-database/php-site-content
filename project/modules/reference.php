<?php
/*
 * reference.php
 * $Revision: 1.1 $
 * $Date: 2007/06/12 18:10:25 $
 */
 
 function reference_Contents() {
  setPageTitle("References");
  if (!userLoggedIn()) {
    return NOTLOGGEDIN_MSG;
  }
  $toReturn = '';
  $toReturn .= 'You have the following options  :<p>';
  $toReturn .= makeLink('Create a reference Within RGD', 'reference', 'createRef'). '<br/><br/>' ;
  $toReturn .= makeLink('Import a reference', 'reference', 'importRef') ;
  return $toReturn;

}

?>
