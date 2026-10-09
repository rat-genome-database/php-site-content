<?php
/**
 * Administrative functions for the application. Users sign in with GitHub (see userLoggedIn() in
 * globalFunctions.php); the old username/password login and user administration were removed.
 * $Revision: 1.17 $
 * $Date: 2007/06/12 18:08:08 $
 *
 */

function admin_logout() {
	delCookieVar('userloggedin');
  destroySession();
	redirectWithMessage('You are now logged out');
}

?>