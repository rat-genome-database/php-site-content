<?php
// dont you dare put any white space outside the php tags!... messes with the redirect!!

function menu_contents() {
  $toReturn = '<div align="right">';
  if (userLoggedIn()) {
    // $pis = getPiArray();
   // $roles = getSecurityRoleArray();
    $toReturn .= 'UserID: '.getUserID();
  //  $toReturn .= ' ('.getUserFullName().')';
    // $toReturn .= ' (PI: '.$pis[getSessionVar('pi')].') '; // note: pi is stored in session to avoid a db hit
                                                          // so any change to pi will require a relogin
   // $toReturn .= ' (Sec level: '.getSecurityLevel().') ';
   // $toReturn .= '&nbsp;'.makeLink('Change Password', 'admin', 'changePassword').'&nbsp;'.makeLink('Logout', 'admin', 'logout').'&nbsp;';
  }
  else {
    $toReturn .= 'Not logged in';
  }
  $toReturn .= '</div>';
  return $toReturn;
}
// dont you dare put any white space outside the php tags!... messes with the redirect!!
?>
