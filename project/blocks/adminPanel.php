<?php
/*
 * adminPanel.php
 * Created on May 16, 2007
 * Administrative left van toolbar here. Referenced from default.html page
 * $Revision: 1.4 $
 * $Date: 2007/12/18 19:42:49 $
 * 
 */
 function adminPanel_contents() {
   $toReturn = ""; 
   $pmb = new PhpMyBorder(); // or new PhpMyBorder(true), read about stylesheet-support below
   if (userLoggedIn()) { 
    
    $toReturn .= '<br/>';
    $toReturn .= $pmb->begin_round();
    $toReturn .= '<b>Tools</b><br/><br>';
    $toReturn .=  makeLink('Curation Tool', 'curation', 'contents');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Object Edit', 'objectEdit', 'selectObjects');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Merge/Retire/Split Objects', 'objectNomen', 'selectObjects');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Notes Editor', 'notesEdit', 'selectObjects');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Reference Editor', 'reference', 'contents');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Portal Admin', 'portalAdmin', 'processPortal');
    $toReturn .= "<br/><br/>"; 
    $toReturn .= makeLink('User Admin', 'admin', 'users');
    $toReturn .= '<br><br/>' . makeLink('Lookup Tables', 'tableMaint', 'home');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Reports', 'report', 'home');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Obsolete Terms', 'ont', 'obsoleteTerms');
    $toReturn .= "<br/><br/>";
    $toReturn .=  makeLink('Diags', 'test', 'dumpSession') ;
    $toReturn .= "<br/><br/>";
    $toReturn .= '<a target="_blank" href="history.html">Development History</a>';
    $toReturn .= $pmb->end_round();
  }
  return $toReturn; 
}
?>