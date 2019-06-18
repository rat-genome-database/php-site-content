<?php
/**
 * Administrative functions for the application, users logging in and out, creating and deleting users, changing security roles for individual users...
 * $Revision: 1.17 $
 * $Date: 2007/06/12 18:08:08 $
 *
 */

function admin_login() {
	$theForm = getLoginForm();
	switch ($theForm->getState()) {
		case SUBMIT_VALID :
			$userName = $theForm->getValue('username');
			$password = $theForm->getValue('password');
			// $password = hashPassword($password);

			// check for locked acount
			$result = fetchRecord('select * from users where username = ' . dbQuoteString($userName) . '  and active_yn = \'N\'', 'LOGIN');
			if (count($result) != 0) {
				redirectWithMessage('Your Acount has been locked. Please see the administrator to unlock your account.');
				break;
			}
			// Now check for correct password
			$result = fetchRecord('select * from users where username = ' . dbQuoteString($userName) . ' and password = ' . dbQuoteString($password) . ' and active_yn = \'Y\'', 'LOGIN');
			if (count($result) != 0) {
				extract($result);
				setSessionVar('uid', $userName); 
        setSessionVar('userKey', $USER_KEY); 
        setSessionVar('userFullName', $FIRST_NAME . " " . $LAST_NAME);
        setSessionVar('userEmail', $EMAIL);
        setSessionVar('userGroup', $USER_GROUP);  
        setSessionVar('userKey', $USER_KEY); 
				setCookieVar('userloggedin', '1');
				redirectWithMessage('You have successfully logged in.');
			} else {
				redirectWithMessage('Invalid login, please try again.');
			}
			break;
		default :
			return $theForm->quickRender();
	}
}


/** 
 * 
 */
function admin_changePassword() {
  if (!userLoggedIn()) {
    return NOTLOGGEDIN_MSG;
  }
  $toReturn = '';
  setPageTitle('Change Password');

  $theForm = newForm('submit', 'POST', 'admin', 'changePassword');
  $theForm->addPassword('PASS1', 'New Password', 10, 20, true);
  $theForm->setInitialFocusField('PASS1');
  $theForm->addPassword('PASS2', 'Confirm New Password', 10, 20, true);
  switch ($theForm->getState()) {
    case INITIAL_GET :
    case SUBMIT_INVALID :
      $toReturn .= $theForm->quickRender();
      break;
    case SUBMIT_VALID :
      if ($theForm->getValue('PASS1') !== $theForm->getValue('PASS2')) {
        $theForm->addFormErrorMessage('Passwords do not match, please enter them again');
        $toReturn .= $theForm->quickRender();
      } else {
        // excryption not needed -> $password = hashPassword($theForm->getValue('PASS1'));
        $password = $theForm->getValue('PASS1');
        executeUpdate('update users set password = ' . dbQuoteString($password) . ' where username = ' . dbQuoteString(getSessionVar('uid')), 'LOGIN');
        redirectWithMessage('password successfully changed');
      }
      break;
  }
  return $toReturn;
}


/** 
 * Allow managers to reset passwords. 
 * 
 */
function admin_resetPassword() {
  if (!userIsAdmin()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';
  $userKey = getRequestVarNum('userKey');
  $userName = getRequestVarString('userName');
  $clearPass = createRandomPassword(7);
  // $hashedPass = hashPassword($clearPass);
  
  executeUpdate('update users set password = '.dbQuoteString($clearPass).' where user_key='. $userKey, 'LOGIN');
  
  redirectWithMessage('Password successfully reset to: <b>'.$clearPass.'</b><br/><br/> <b>Please instruct the user with userName ( <font color=red>' . $userName . ' </font>  ) to change their password to something memorable.</b>');
  
}

function admin_logout() {
	delCookieVar('userloggedin');
  destroySession();
	redirectWithMessage('You are now logged out');
}

/** 
 * Allow managers to create new users. 
 * 
 */
function admin_users() {
  if (!userIsAdmin()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';
  $toReturn .= makeLink('Create a New User', 'admin', 'updateUser');
   $toReturn .= '<p></p>';
  setPageTitle('User Administration');
  $users = fetchRecords('select * from users order by ACTIVE_YN desc , user_group, last_name', 'LOGIN');
  $table = newTable('USERNAME', 'NAME', 'ROLE', 'EMAIL', 'Active?', 'Log');
  $table->setAttributes('class="simple" width="100%"');
  foreach ($users as $user) {
    extract($user);
    $table->addRow(makeLink($USERNAME, 'admin', 'updateUser', 'USER_KEY=' . $USER_KEY), $FIRST_NAME . ' ' . $LAST_NAME, $USER_GROUP, $EMAIL, makeUserStatusLink($ACTIVE_YN), 
    makeLink('<img src="icons/page_white_magnify.png" border=0 alt="Log">', 'admin', 'userLog', 'USER_KEY=' . $USER_KEY));
  }
  $toReturn .= $table->toHtml();
  return $toReturn;
}

/**
 * Used in a table to show is user is active or inactive
 * 
 */
function generateActivelink($status ) { 
 $returnString = '';
 if ( $status == 'Y' ) { 
  $returnString ="Y"; 
 } else { 
  $returnString = "N";
 }
 return $returnString; 
  
}
function admin_lostPassword() {
  return 'Please contact an Admin user who can reset your password or determine your username.';
}

/**
 * Managers use this to delete a user from the system
 * 
 */
function admin_deleteUser() {
  if (!userIsAdmin()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';
  $userKey = getRequestVarNum('userKey');

  executeUpdate('delete from users where user_Key=' . $userKey, 'LOGIN');

  redirectWithMessage('User successfully deleted', makeUrl('admin', 'users'));

}

/**
 * Create an HTML link to colored ball based on status of object 
 */
function makeUserStatusLink($status) {
  switch ( $status ) {
    case 'Y':
      return '<img src="icons/flag_green.png" title="ACTIVE" alt="ACTIVE">';
    break;
    case 'N':
      return '<img src="icons/flag_red.png" title="NOT-ACTIVE" alt="NOT-ACTIVE">';
      break;
    default:
      return $status;
  }
}

/**
 * Managers update and create new users in this function. 
 * We do prevent a new user from having hte same username as an existing user. 
 * 
 * TODO : Error in that the admin can edit a user and change their username to 
 * another existing username, correct.  
 */
function admin_updateUser() {
  if (!userIsAdmin()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';

  $userKey = getRequestVarNum('USER_KEY');
  $userName = getRequestVarString('USERNAME');
  
  $theForm = newForm('submit', 'POST', 'admin', 'updateUser');
  
  $theForm->addHidden('USER_KEY');
  $theForm->addText('USERNAME', 'Username', 20, 30, true);
  $theForm->setInitialFocusField('USERNAME');
  $theForm->addText('FIRST_NAME', 'First Name', 20, 30, true);
  $theForm->addText('LAST_NAME', 'Last Name', 20, 30, true);
  $theForm->addText('EMAIL', 'Email Address', 30, 50, false);
  $theForm->addText('PHONE', 'Phone', 30, 50, false);
  $theForm->addText('INSTITUTE', 'Institute', 30, 100, false); 
  $theForm->addText('ADDRESS1', 'Address', 30, 100, false); 
  $theForm->addText('ADDRESS2', 'Address 2', 30, 100, false);
  $theForm->addText('CITY', 'City', 30, 50, false);
  $theForm->addText('STATE', 'State', 30, 50, false);
  $theForm->addText('ZIP_CODE', 'Zip', 10, 10, false);
  $theForm->addText('COUNTRY', 'Country', 30, 50, false);
  $theForm->addSelect('ACTIVE_YN', 'Active', getActiveArray(), true);       
  $theForm->addSelect('USER_GROUP', 'Security Role', getSecurityRoleArray(), true);

 
  switch ($theForm->getState()) {
    case INITIAL_GET:
      if (0 != $userKey) {
        $user = fetchRecord('select * from users where user_key = '.$userKey, 'LOGIN');
        $theForm->setDefaults($user);
        setPageTitle('Update User');
      }
      else {
        setPageTitle('New User');
      }
    case SUBMIT_INVALID:
      $toReturn .= $theForm->quickRender();
      if (0 != $userKey) {
        $tmpArray = array ( 'userKey' => $userKey, 'userName' => $theForm->getValue('USERNAME') ) ; 
        $toReturn .= makeLinkConfirm('Are you sure you want to reset this user\'s password?', 'Reset this Users Password', 'admin', 'resetPassword', $tmpArray);
        $toReturn .= ' ';
        $toReturn .= makeLinkConfirm('Are you sure you want to delete this user?', 'Delete User', 'admin', 'deleteUser', $tmpArray);
      }
      
      break;
    case SUBMIT_VALID:
      if (0 != $userKey) {
        executeUpdate('update users set '.getFieldsForUpdate($theForm).' where user_key = '.$userKey, 'LOGIN');
        redirectWithMessage('record successfully changed', makeUrl('admin', 'users'));
      }
      else {
        // Check if userID already Existed 

        $result = fetchRecord('select * from users where username = ' . dbQuoteString($userName) , 'LOGIN');
        if (count($result) != 0) {
          // redirectWithMessage('UserName Already Exists, please try another UserName', makeUrl('admin', 'updateUser'));
          $theForm->addFormErrorMessage('UserName Already Exists, please try another UserName' ) ; 
          $toReturn .= $theForm->quickRender();
          return $toReturn;
        }
        
        $newKey = getNextDBKey('users', 'LOGIN');
        // echo $newKey; 
        $theForm->setDefault('USER_KEY', $newKey); 
        $theForm->addHidden('PASSWORD');
        $clearPass = createRandomPassword(7);
        $theForm->setDefault('PASSWORD', $clearPass); 
        executeUpdate('insert into users '.getFieldsForInsert($theForm), 'LOGIN');
        redirectWithMessage('user successfully created<br/><br>Username: <b>'.$theForm->getValue('USERNAME').'</b><br/>Initial password: <b>'.$clearPass.'</b><br/><br/> <b>Please instruct the new user to change their password to something memorable.</b>');
      }
  }
  
  return $toReturn;
}




?>