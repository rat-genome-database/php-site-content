<?php
function portalNavigation_contents() { 
  
  $name = getRequestVarString('name','obesity');
  $sqlPortal = "select PORTAL_KEY from PORTAL1 where URL_NAME = '" . $name . "'";
  // echo $sqlPortal;
  $myresult = fetchRecord($sqlPortal);
  extract ( $myresult ) ;
  if ( $myresult == NULL ) {
        return "Portal Not found";
  } 
  
  $sqlLinks = "select * from portal_links1 where portal_key = $PORTAL_KEY order by LINK_ORDER";
  //  dump ( $sqlLinks );
  $linkResults = fetchRecords($sqlLinks);
  //dump ( $linkResults );
  
  
   $str = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" bgcolor="#194B74"><img src="/common/images/shim.gif" width="1" height="1"></td>
  </tr>
  
  <tr align="left">
    <td background="/common/images/menu_bkgd.gif">
    <table width="800" border="0" cellspacing="0" cellpadding="1">
        <tr>
      <td width="30"><img src="/common/images/shim.gif" width="30" height="1"></td>

      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
          <td><p>&nbsp;<a href="/" class="atitle">RGD&nbsp;Home</a></p></td>';
   // Iterate over links adding them in
    foreach ( $linkResults as $link ) { 
      extract ( $link ); 
      $str .= '<td><img src="/common/images/dotline1.gif" width="1" height="20"></td>';
      $str .= '<td><p>&nbsp;<a href="' . $LINK_VALUE .'" class="atitle">'. $LINK_NAME .'</a>&nbsp;</p></td>'; 
    }
      $str .= '
      <td width="1"><img src="/common/images/shim.gif" width="1" height="1"></td>
      <td width="110"><img src="/common/images/shim.gif" width="110" height="1"></td>
        </tr>
      </table>
  </td>
    <td width="100%" background="/common/images/menu_bkgd.gif">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#194B74"><img src="/common/images/shim.gif" width="1" height="1"></td>
  </tr>
</table>'
;

//  $str = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
//  <tr>
//    <td colspan="2" bgcolor="#194B74"><img src="/common/images/shim.gif" width="1" height="1"></td>
//  </tr>
//  
//  <tr align="left">
//    <td background="/common/images/menu_bkgd.gif">
//    <table width="800" border="0" cellspacing="0" cellpadding="1">
//        <tr>
//      <td width="30"><img src="/common/images/shim.gif" width="30" height="1"></td>
//
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//          <td><p>&nbsp;<a href="/" class="atitle">RGD&nbsp;Home</a></p></td>
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//          <td><p>&nbsp;<a href="index.shtml" class="atitle" name="link3" id="link1" onMouseOver="MM_showMenu(window.mm_menu_0615115401_0,-9,21,null,"link3");" onMouseOut="MM_startTimeout();">Diseases</a>&nbsp;</p></td>
//          <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//      <td><p>&nbsp;<a href="phenotype.shtml" class="atitle">Phenotypes</a>&nbsp;</p></td>
//          <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//
//      <td><p>&nbsp;<a href="biological_processes.shtml" class="atitle">Biological&nbsp;Processes</a></p></td>
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//          <td><p>&nbsp;<a href="pathways.shtml" class="atitle">Pathways</a>&nbsp;</p></td>
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//      <td><p>&nbsp;<a href="tools.shtml" class="atitle">Tools</a>&nbsp;&nbsp;</p></td>
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//          <td><p>&nbsp;<a href="relatedlink.shtml" class="atitle">Related&nbsp;Links</a>&nbsp;</p></td>
//      <td><img src="/common/images/dotline1.gif" width="1" height="20"></td>
//      <td width="1"><img src="/common/images/shim.gif" width="1" height="1"></td>
//      <td width="110"><img src="/common/images/shim.gif" width="110" height="1"></td>
//        </tr>
//      </table>
//  </td>
//
//    <td width="100%" background="/common/images/menu_bkgd.gif">&nbsp;</td>
//  </tr>
//  
//  <tr>
//    <td colspan="2" bgcolor="#194B74"><img src="/common/images/shim.gif" width="1" height="1"></td>
//  </tr>
//</table>'
//;
return $str; 
}
?>
