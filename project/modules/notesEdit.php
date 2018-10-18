<?php
/*
 * notesEdit.php
 *
 * $Revision: 1.1 $
 * $Date: 2007/06/12 18:08:33 $
 */
 
 function notesEdit_selectObjects() {
  setPageTitle("Notes Editor");
  if (!userLoggedIn()) {
    return NOTLOGGEDIN_MSG;
  }
  $toReturn = '';
  $toReturn = 'You have the following options:<p>';
  $toReturn .= '<P><FIELDSET><LEGEND> Search for an object </legend>';
  $theForm = newForm('Search for Object', 'GET', 'notesEdit', 'searchObjects');
  $theForm->addText('objectName', 'Enter an Object name or RGDID', 12, 80, true);
  $theForm->addSelect('matchType', 'Search where:', getSearchMatchType(), true);
  $theForm->addRadio('objectType', 'Object Type:', getObjectArrayForNotes(), true);
  $theForm->setDefault('objectType', 1); //Gene
  $theForm->setDefault('matchType', 'contains');
  $theForm->setInitialFocusField('objectName');
  $toReturn .= $theForm->quickRender();
  $toReturn .= '</LEGEND></FIELDSET>';

  switch ($theForm->getState()) {
    case INITIAL_GET :
      return $toReturn;
    case SUBMIT_INVALID :
      redirectWithMessage(' Missing field. Please fill in the missing field and resubmit. ', makeUrl('notesEdit', 'selectObjects'));
      break;
    case SUBMIT_VALID :
      // we should not do processing here as we have three forms on one page. They should all jump of to different
      // functions for processing . 
      redirectWithMessage(' Method call invalid. See a programmer as they screwed up.  ');
      break;
    default :
      return $toReturn;

  }
}
/**
 * Matches the values in the RGD_OBJECTS table, minus the objects we don' t want to allow the users * to create .
*/
function getObjectArrayForNotes() {
  return array (
    '1' => 'Gene',
    '6' => 'QTL',
    '5' => 'Strain'
  );
}

?>
