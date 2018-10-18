<?php
/*
 * All processing for the PORTAL Administration tables in here. 
 * 5/2007 by George Kowalski
 * $Revision: 1.7 $
 * $Date: 2008/01/23 20:56:57 $
 *
 * Updated in Dec 2011 by Marek Tutaj to use new ontology tables
 */


/*********************************************************************************
 * Display the REPORT_PROCESS_TYPES table
 *********************************************************************************/
function portalAdmin_processPortal() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';
    setPageTitle('Portals ' .createHelpLinkCW( "Portals"));
  $toReturn .= '<br> The list of all Portals is shown below.  Select the URL name to run that portal.<br/><br/>';
  $toReturn .= makeLink('Create a Portal', 'portalAdmin', 'updatePortal').'&nbsp&nbsp' .createHelpLinkCW( "Portals", "Create a Portal");
   $toReturn .= '<p></p>';

  $entries = fetchRecords('select * from PORTAL1 order by FULL_NAME  ');
  $table = newTable(' Edit ', 'URL link ', 'Full Name' ,  'Data Last Updated', 'Type', 'Ver', 'Terms', 'Links', 'Del');
  $table->setAttributes('class="simple" width="100%"');
  foreach ($entries as $entry) {
    extract($entry);
    $ACTIVEDATE = fetchField("select DATE_LAST_UPDATED from PORTAL_VER1 where portal_key = $PORTAL_KEY and PORTAL_VER_STATUS = 'Active' ");  

    $table->addRow(makeLink($PORTAL_KEY, 'portalAdmin', 'updatePortal', 'PORTAL_KEY=' . $PORTAL_KEY),
     makeLink($URL_NAME , 'portal', 'show' , array('name' => $URL_NAME)),
	 $FULL_NAME,  $ACTIVEDATE,  $PORTAL_TYPE,  $ACTIVEDATE, 
	 makeLink('<img src="icons/add.png" border=0 alt="Add">', 'portalAdmin', 'showTerms', 'PORTAL_KEY=' . $PORTAL_KEY),
	 makeLink('<img src="icons/add.png" border=0 alt="Add">', 'portalAdmin', 'showLinks', 'PORTAL_KEY=' . $PORTAL_KEY),
	 makeLink('<img src="icons/database_delete.png" border=0 alt="Delete">', 'portalAdmin', 'deletePortal', 'PORTAL_KEY=' . $PORTAL_KEY));
  }
  $toReturn .= $table->toHtml();
  return $toReturn;
}


/*********************************************************************************
 * Update the PORTAL table
 *********************************************************************************/
function portalAdmin_updatePortal() { 
 
   if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
 

 $toReturn = '';
 $portalKey = getRequestVarNum('PORTAL_KEY');
 $urlName = getRequestVarString('URL_NAME');
 
 $theForm = newForm('Submit', 'GET', 'portalAdmin', 'updatePortal');
 $helpURL = createHelpLinkCW( "Portals", "Create a Portal" ); 
 $theForm->addHidden('PORTAL_KEY');
 $theForm->addText('URL_NAME'   , 'Url name  ( Must be unique ) ' , 10, 10, true);
 $theForm->addText('FULL_NAME'  , 'Portal Full Name ', 30,30, true);
 $theForm->addText('PAGE_NAME'  , 'Page Name ', 50,300, true);
 $theForm->addText('PAGE_TITLE' , 'Page Title ', 50,300, true);
 $theForm->addText('PAGE_IMG_URL' , 'Page Image URL ', 50,50, false);
 $theForm->addText('PAGE_CATEGORY_PAGE_DESC' , 'Page Category Description ', 50,50, false);
 $theForm->addText('PAGE_SUB_CATEGORY_DESC' , 'Page Sub-Category Description ', 50,50, false);
 $theForm->addSelect('PORTAL_TYPE', 'Type ', getPortalTypes() , true); 
 //$theForm->addText('TERM_NAME' , 'Ontology Term name to start at ( Standard ) ', 50,255, false);
 
 $theForm->addTextArea('PAGE_SUMMARY_DESC' , 'Page Summary Description ', 10, 50 , 4000, false);
 
 switch ($theForm->getState()) {
    case INITIAL_GET:

      $breadbrumb =   makeLink("Portal Admin", 'portalAdmin', 'processPortal') . " > " ; 
      if (0 != $portalKey) {
        $entry = fetchRecord('select * from PORTAL1 where PORTAL_KEY = ' . $portalKey);
        $theForm->setDefaults($entry);
        $breadbrumb .= "Update Portal"; 
             setPageTitle('Update Portal&nbsp;' . $helpURL  );
       
      }
      else {
        // set the default subsystem name to the last one entered to make it easy on the admin. 
        //$entry = fetchRecord('select SUBSYSTEM_NAME from ( select * from REPORT_PROCESS_TYPES  order by RPT_PROCESS_TYPE_ID desc ) where  rownum =1 ');
        
        $breadbrumb .= " Create a New Portal ";
        setPageTitle('Create a New Portal&nbsp;' . $helpURL  ); 
        $theForm->setDefault("PAGE_IMG_URL","/common/dportal/images/neurological.gif"); 
        $theForm->setDefault("PAGE_CATEGORY_PAGE_DESC","1. Choose a disease category");
        $theForm->setDefault("PAGE_SUB_CATEGORY_DESC","2. Choose a disease");
        $theForm->setDefault("PORTAL_TYPE", "Standard");
        
      }
      $toReturn .= makeBreadCrumbLink( $breadbrumb ) ; 
    case SUBMIT_INVALID:
      $toReturn .= $theForm->quickRender();
      
      break;
    case SUBMIT_VALID:
    if (0 != $portalKey) {
        executeUpdate('update PORTAL1 set '.getFieldsForUpdate($theForm).' where PORTAL_KEY =  '.$portalKey);
        redirectWithMessage('Portal successfully changed', makeUrl('portalAdmin', 'processPortal'));
      }
      else {
        // Check if userID already Existed 

        $result = fetchRecord('select * from PORTAL1 where URL_NAME = ' . dbQuoteString($urlName)  );
        if (count($result) != 0) {
          // redirectWithMessage('UserName Already Exists, please try another UserName', makeUrl('admin', 'updateProcessTypes'));
          $theForm->addFormErrorMessage('This Portal URL already Exists... please try another. ' ) ; 
          $toReturn .= $theForm->quickRender();
          break;
        }
        
        $newKey = getNextDBKey('PORTAL1');
        // echo $newKey; 
        $theForm->setDefault('PORTAL_KEY', $newKey); 
        //$theForm->setDefault("PORTAL_STATUS", "new");
        
        executeUpdate('insert into PORTAL1 ' . getFieldsForInsert($theForm));
        redirectWithMessage('Portal successfully created', makeUrl('portalAdmin', 'processPortal'));
      }
      break;
 }
   return $toReturn;
}

 /*********************************************************************************
 * Delete row from PORTAL table
 *********************************************************************************/
function portalAdmin_deletePortal() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $PORTAL_KEY = getRequestVarNum('PORTAL_KEY');

  // First Delete Links
  executeUpdate('delete from PORTAL_LINKS1 where PORTAL_KEY= ' .  $PORTAL_KEY );
  
  // Delete  Active Categories entries
  // Deleting all of them can take some time ! Leave for cleanup script. 
  $sql = "select portal_key from portal_ver1 where portal_ver_status = 'Active' and portal_key = $PORTAL_KEY";
  $portalVerID = fetchField($sql); 
  if ( $portalVerID != null ) { 
    executeUpdate('delete from PORTAL_CAT1 where PORTAL_VER_ID= ' .  $portalVerID );
  }
  
  // Delete Versions
  executeUpdate('delete from PORTAL_TERMSET1 where PORTAL_KEY = ' . $PORTAL_KEY ) ; 
  
  // Delete Objects
  executeUpdate('delete from PORTAL_OBJECTS where PORTAL_KEY = ' . $PORTAL_KEY ) ; 

  // Delete Portal entry
  executeUpdate('delete from PORTAL1 where PORTAL_KEY = ' .  $PORTAL_KEY );

  redirectWithMessage('Portal successfully deleted', makeUrl('portalAdmin', 'processPortal'));

}

/*********************************************************************************
 * Return array of portal types for drop down list
 *********************************************************************************/
function getPortalTypes() {
  $retArray = array(); 
  
    $retArray['Standard'] = 'Standard Portal';  
    $retArray['AI'] = 'Advanced Technology Portal'; 
    $retArray['Ontology'] = 'GViewer Search'; 
   return $retArray; 
} 

/**
 * Allow staff of maintain Links for a given portal
 */
function portalAdmin_showLinks() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $toReturn = '';
  $portalKey = getRequestVarString('PORTAL_KEY');
  $breadbrumb =   makeLink("Portal Admin", 'portalAdmin', 'processPortal') . " > Maintain Links" ; 
  $portalName = fetchField("select full_name from PORTAL1 where PORTAL_KEY = $portalKey");
  $toReturn .= makeBreadCrumbLink( $breadbrumb ) ;
  setPageTitle('Portal Links for ' . $portalName ."&nbsp;". createHelpLinkCW( "Portals", "Portal Links") );
 
  $sql = "select * from portal_links1 where portal_key = $portalKey order by LINK_ORDER";
  $results = fetchRecords($sql); 
  $linktable = 
  $linktable = newTable('Link name  ', 'Link Value', 'Link Order', 'Edit', 'Delete');
  $linktable->setAttributes('class="simple" width="100%"');
  
  $maxLinkOrder = 0; 
  // generate table to show all links for this portal. 
  foreach ( $results as $result ) { 
      extract ( $result ) ; 
      $linktable->addRow($LINK_NAME, $LINK_VALUE, $LINK_ORDER, makeLink('<img src="icons/add.png" border=0 alt="Add">', 'portalAdmin', 'editLink', 'PORTAL_LINKS_ID=' . $PORTAL_LINKS_ID. "&PORTAL_KEY=" . $portalKey) , makeLink('<img src="icons/delete.png" border=0 alt="Delete">', 'portalAdmin', 'delLink', 'PORTAL_LINKS_ID=' . $PORTAL_LINKS_ID ."&PORTAL_KEY=" . $portalKey)); 
      $maxLinkOrder = $LINK_ORDER; 
  }
  $maxLinkOrder += 5;
  
  // generate the form to add new link to this portal , default to the bottom 
  $newLinkForm = newForm('Add New Link', 'GET', 'portalAdmin', 'showLinks');
  $newLinkForm->addText('link_Name', 'Enter the link Name', 20, 100, true);
  $newLinkForm->setInitialFocusField('linkName');
  $newLinkForm->addText('link_Value', 'Enter the link URL:', 70, 2000, true);
  $newLinkForm->addText('link_Order', 'Enter the link Order (1-9999) :', 3, 4, true);
  $newLinkForm->setDefault('link_Order', $maxLinkOrder);
  $newLinkForm->addHidden('PORTAL_KEY', $portalKey);
  
  switch ($newLinkForm->getState()) {
    case INITIAL_GET :
      // return $newLinkForm->quickRender();
      break;
    case SUBMIT_INVALID :
      break;
    case SUBMIT_VALID : 
      $newLinkForm->addHidden('PORTAL_LINKS_ID', getNextDBKey( 'PORTAL_LINKS1' ) ); 
      $sqlInsert = "insert into PORTAL_LINKS1  " . getFieldsForInsert($newLinkForm);
      // $theAddForm = newForm('Add Terms', 'GET', 'curation',  'addTermsToBucket');
      // Now we're on the search results page , add the additional fields to be filled in. 
      executeUpdate($sqlInsert);
      redirectWithMessage('Link Added Successfully', makeUrl('portalAdmin', 'showLinks', array ( "PORTAL_KEY" => $portalKey)));
      break;
    default :
      //return $theForm->quickRender();
  }
  
  // render this top level page. 
  return $toReturn . $newLinkForm->quickRender() . $linktable->toHtml();
  
}

function portalAdmin_editLink() { 
   if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $portalKey = getRequestVarString('PORTAL_KEY');
  $portalLinksID = getRequestVarString('PORTAL_LINKS_ID');
  setPageTitle('Edit Link' ."&nbsp;". createHelpLinkCW( "Portals", "Portal Links"));
  $breadbrumb =   makeLink("Portal Admin", 'portalAdmin', 'processPortal') . " > Edit Link" ; 
  //$portalName = fetchField("select full_name from PORTAL1 where PORTAL_KEY = $portalKey");
  $toReturn = makeBreadCrumbLink( $breadbrumb ) ;
   //dump( $result ) ; 
   // generate the form to add new link to this portal , default to the bottom 
  $newLinkForm = newForm('Save Link', 'POST', 'portalAdmin', 'editLink');
  $newLinkForm->addText('LINK_NAME', 'Enter the link Name', 20, 100, true);
  $newLinkForm->setInitialFocusField('linkName');
  $newLinkForm->addText('LINK_VALUE', 'Enter the link URL:', 70, 2000, true);
  $newLinkForm->addText('LINK_ORDER', 'Enter the link Order (1-9999) :', 3, 4, true);
  $newLinkForm->addHidden('PORTAL_KEY', $portalKey);
  $newLinkForm->addHidden('PORTAL_LINKS_ID', $portalLinksID);
  
  
  switch ($newLinkForm->getState()) {
    case INITIAL_GET :
      $sql = "select * from portal_links1 where portal_links_id = $portalLinksID";
      $result = fetchRecord( $sql) ; 
      $newLinkForm->setDefaults($result);
      return $toReturn . $newLinkForm->quickRender();
      break;
    case SUBMIT_INVALID :
      break;
    case SUBMIT_VALID : 
 
      $sqlInsert = "update PORTAL_LINKS1 set " . getFieldsForUpdate($newLinkForm) . "where portal_links_id = $portalLinksID";
     //dump($sqlInsert);
      // $theAddForm = newForm('Add Terms', 'GET', 'curation',  'addTermsToBucket');
      // Now we're on the search results page , add the additional fields to be filled in. 
      executeUpdate($sqlInsert);
      
      redirectWithMessage('Link Updated Successfully', makeUrl('portalAdmin', 'showLinks', array ( 'PORTAL_KEY' => $portalKey)));
      return;
      break;
    default :
  }
  return $newLinkForm->quickRender();
}
/**
 * delete a link from the PORTAL_LINKS1 table. 
 */
function portalAdmin_delLink() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $portalLinksID = getRequestVarNum('PORTAL_LINKS_ID');
  $portalKey = getRequestVarNum('PORTAL_KEY');
  if ( $portalLinksID <> 0 ) { 
    $sql = "delete from PORTAL_LINKS1 where PORTAL_LINKS_ID = $portalLinksID ";
    executeUpdate($sql);
    redirectWithMessage("Link Deleted", makeUrl('portalAdmin', 'showLinks', array ( "PORTAL_KEY" => $portalKey))); 
    return;
   }
   redirectWithMessage('Problem deleteing Link', makeUrl('portalAdmin', 'showLinks', array ( "PORTAL_KEY" => $portalKey)));
}

/*********************************************************************************
 * Display list of terms associated with a portal
 *********************************************************************************/
function portalAdmin_showTerms() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $maxResults = 20;
  $toReturn = '';
  $theForm = newForm('Submit', 'GET', 'portalAdmin', 'showTerms');
  $theForm->addText("TERM", "Search for term to add &nbsp;", 40 , 50, true); 
  $theForm->addHidden('PORTAL_KEY' ) ; 
  $PORTAL_KEY = getRequestVarString('PORTAL_KEY');
  $theForm->setDefault('PORTAL_KEY', $PORTAL_KEY);
  $breadbrumb =   makeLink("Portal Admin", 'portalAdmin', 'processPortal') . " > Top Level Portal Terms" ; 
  $portalName = fetchField("select full_name from PORTAL1 where PORTAL_KEY = $PORTAL_KEY");
  $toReturn .= makeBreadCrumbLink( $breadbrumb ) ; 
  setPageTitle('Top Level Portal Terms ' ."&nbsp;". createHelpLinkCW( "Portals", "Portal Terms"));
  $toReturn .= "Portal : <b>$portalName</b><br><br> The list of all terms associated with this portal is shown below. You can add a top level term for the first drop down menu item of a portal. Once you've done this you can add secondary items to these top level terms. <br/><br/>";
  //$toReturn .= $theForm->quickRender();
  $toReturn .= '<p></p>';

  // set up results table at the bottom of screen showing top level annotations. 
  $entries = fetchRecords("select * from PORTAL_TERMSET1 ts where PORTAL_KEY = $PORTAL_KEY and PARENT_TERMSET_ID is NULL order by ONT_TERM_NAME  ");
  $table1 = newTable('Ontology', ' Term ', 'Acc ID', 'Maintain Children', 'Delete Term' );
  $table1->setAttributes('class="simple" width="100%"');
  foreach ($entries as $entry) {
    extract($entry);
	
	#show number of defined child termsets
	$child_termsets_count = fetchField("select count(*) from PORTAL_TERMSET1 where PARENT_TERMSET_ID = $PORTAL_TERMSET_ID");
	
    $table1->addRow(getTermOntologyStrByID($TERM_ACC), $ONT_TERM_NAME, $TERM_ACC,
	    makeLink('<img src="icons/add.png" border=0 alt="Add">', 'portalAdmin', 'showChildTerms', 'PORTAL_KEY=' . $PORTAL_KEY. '&parentTermSetId='. $PORTAL_TERMSET_ID)
			." ($child_termsets_count)",
		makeLink('<img src="icons/delete.png" border=0 alt="Add">', 'portalAdmin', 'delTopTerm', 'PORTAL_KEY=' . $PORTAL_KEY . '&portalTermSetId='. $PORTAL_TERMSET_ID) );
  }
  
  
  switch ($theForm->getState()) {
     case INITIAL_GET:
       $toReturn .= $theForm->quickRender();
       
     break;
     
    case SUBMIT_INVALID:
      $toReturn .= $theForm->quickRender();
      
      break;
    case SUBMIT_VALID:
      // search on term done display results and allow user to select term. 
   
      $term = $theForm->getValue('TERM');
      $term = trim( $term); 
      $theForm->setDefault('TERM' , $term);
      extract($theForm->getValues());
      $currentTerms = fetchArrayForSelectField("select term_acc,1 from portal_termset1 where parent_termset_id is null and portal_key = $PORTAL_KEY");
      //dump( $currentTerms ) ; 
      $start = getRequestVarNum('start');
      $sql = "select * from ont_terms where upper(term) like upper(".dbQuoteString('%'.$term.'%').")";
    
      
      $results = fetchRecordsLimit($sql, $maxResults, $start);
      $formValues = $theForm->getAllValues();
      $toReturn .= $theForm->quickRender();
      
      if (count($results) == 0) {
        $suggestions = array();
        if (count($suggestions) > 0) {
          $toReturn .= "No results found ... Did you mean : ";
          foreach ($suggestions as $suggestion) {
            
            $formValues['TERM'] = str_replace($term, $suggestion, $theForm->getValue('TERM'));
            $toReturn .= makeLink($formValues['TERM'], 'portalAdmin', 'showTerms', $formValues).' ?';
          }
        }
        else {
          $toReturn .= "We're sorry, we didn't find any terms in the vocabulary matching your search term, please try again.";
        }
      }
      else {
        // now do the esummary against the webenv, which will give us all the results at once
        $table = newTable('Select', 'Ontology', 'Term');
        $table->setAttributes('class=simple');
        $index = $start;
        foreach ($results as $result) {
          //var_dump($currentTerms);
          extract($result);
          urlencode($TERM_ACC);
          ++$index;
          $addLink = makeAjaxCheckbox(makeUrl('portalAdmin', 'toggleTopTerm', "quiet=1&termName=$TERM&termAcc=$TERM_ACC&portalKey=$PORTAL_KEY"),
			array_key_exists($TERM_ACC, $currentTerms), 'notificationArea-'.$TERM_ACC.'-'.$index);
          $browseLink = makeLinkPop('Browse ancestor and descendant nodes for this term', $TERM, 'search', 'browseMeshTree', 'termAcc='.$TERM_ACC);
          $table->addRow($addLink.'<br/><span id="notificationArea-'.$TERM_ACC.'-'.$index.'" style="background-color:beige"></span>',
			getTermOntologyStrByID($TERM_ACC), $browseLink);
        } 
        $count = fetchField('select count(*) from ('.$sql.')');
  
 
        $toReturn .= "<br/><b>Your search returned $count results. ".
			'You may use any of these terms in your profile by checking the checkbox, or you may further refine or generalize '.
			'your term by clicking on the term name itself and browsing ancestor or descendant terms. </b><br/><br/>';
  
        $pagingLinks = '<div align="center">'.doPagingLinks($count, $maxResults, $start, "<-- View Previous", "View Next -->", $theForm, 'start').'</div>';
        //toReturn .= $pagingLinks.'<br/>';
        $toReturn .= $table->toHtml(); 
        $toReturn .= "<br/>$pagingLinks"; 
      
      }

  }
  // $toReturn .= $table->toHtml();
  $toReturn .="<p><h3>Current Top Level Portal Terms</h3>";
  $toReturn .= $table1->toHtml(); 
  return $toReturn;
}

// Returns the Ontology Name given the Ontology ID
function getTermOntologyStrByID( $accId ) { 
  $retStr = strtok($accId, ":"); 
  return $retStr; 
}

/**
 *  called from AJAX to delete / add top level terms
 */
function portalAdmin_toggleTopTerm() {
 if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  setDirectOutput();
  $termName = getRequestVarString('termName');
  $quiet = getRequestVarNum('quiet');
  $portalKey = getRequestVarNum('portalKey');
  $termAcc = getRequestVarString('termAcc');
  
  $alreadyThereID = fetchField("select portal_termset_id  from portal_termset1 where term_acc = '". $termAcc . "' and portal_key = $portalKey ");
  $removed = false;
  if ($alreadyThereID > 0) {
    // delete children first
    executeUpdate("delete from portal_termset1 where PARENT_TERMSET_ID = $alreadyThereID");
    // parent term delete
    executeUpdate("delete from portal_termset1 where portal_termset_id = $alreadyThereID");
    $removed = true;
  }
  else {
    executeUpdate("insert into portal_termset1 (portal_termset_id, portal_key, term_acc, ont_term_name) values (portal_termset1_seq.nextval, $portalKey, '$termAcc', '$termName' )");
  }
  $message = $removed?"Removed":"Added $termAcc";
  
    echo $message;
 
}
function portalAdmin_toggleChildTerm() {
 if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  setDirectOutput();
  $termName = getRequestVarString('termName');
  $termName = dbQuoteString( $termName);
  $quiet = getRequestVarNum('quiet');
  $portalKey = getRequestVarNum('portalKey');
  $termAcc = getRequestVarString('termAcc');
  $parentTermSetId = getRequestVarString('parentTermSetId');
  
  $alreadyThereID = fetchField("select portal_termset_id from portal_termset1 where term_acc = '". $termAcc . "' and portal_key = $portalKey and parent_termset_id = $parentTermSetId");
  $removed = false;
  if ($alreadyThereID > 0) {
    executeUpdate("delete from portal_termset1 where portal_termset_id = $alreadyThereID");
    $removed = true;
  }
  else {
    executeUpdate("insert into portal_termset1 (portal_termset_id, portal_key, parent_TermSet_Id, term_acc, ont_term_name) values (portal_termset1_seq.nextval, $portalKey, $parentTermSetId, '$termAcc', $termName )");
  }
  $message = $removed?"Removed":"Added $termAcc";
  
    echo $message;
 
}
/**
 * Delete the top level term and all children. 
 */
function portalAdmin_delTopTerm() {
 if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $portal_termset_id = getRequestVarNum('portalTermSetId');
  $PORTAL_KEY = getRequestVarNum('PORTAL_KEY');
  // delete children first
  executeUpdate("delete from portal_termset1 where PARENT_TERMSET_ID = $portal_termset_id");
  // delete parent term
  executeUpdate("delete from portal_termset1 where portal_termset_id =  $portal_termset_id");
  
  redirectWithMessage(' Term removed ', makeUrl('portalAdmin', 'showTerms', "PORTAL_KEY=" . $PORTAL_KEY));
}

function portalAdmin_delChildTerm() {
 if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $portal_termset_id = getRequestVarNum('portalTermSetId');
  $PORTAL_KEY = getRequestVarNum('PORTAL_KEY');
  $parentTermSetId = getRequestVarNum('parentTermSetId');
  
  // delete parent term
  executeUpdate("delete from portal_termset1 where portal_termset_id =  $portal_termset_id");
  // TODO: Need to delete children
  // return  "&parentTermSetID=" . $parentTermSetId ;
  redirectWithMessage(' Term removed ', makeUrl('portalAdmin', 'showChildTerms', "PORTAL_KEY=" . $PORTAL_KEY. "&parentTermSetId=" . $parentTermSetId));
}

/*********************************************************************************
 * Display list of terms associated with a portal
 * 
 *********************************************************************************/
function portalAdmin_showChildTerms() {
  if (!userIsCurator()) {
    return NOACCESS_MSG;
  }
  $maxResults = 20;
  $toReturn = '';
  $theForm = newForm('Submit', 'GET', 'portalAdmin', 'showChildTerms');
  $theForm->addTextArea("TERM", "Search for term to add ( one per line )<br>", 20 , 60, 4000, true); 
  $theForm->addHidden('PORTAL_KEY' ) ; 
  $PORTAL_KEY = getRequestVarString('PORTAL_KEY');
  $theForm->setDefault('PORTAL_KEY', $PORTAL_KEY);
  
  $theForm->addHidden('parentTermSetId' ) ;
  $parentTermSetId = getRequestVarString('parentTermSetId');
  $theForm->setDefault('parentTermSetId', $parentTermSetId);
  
  // Parent termSetId we'll be annotatign these children to. 
  // $parentTermSetId = getRequestVarString('portalTermSetId');
  // Get parent Inforamtion
  $result = fetchRecord("select * from portal_termset1 where portal_termset_id = $parentTermSetId");
  $PARENT_TERMSET_ID = $parentTermSetId; 
  $result1 = fetchRecord("select term_acc,term from ont_terms where term_acc = '" .  $result['TERM_ACC'] . "'" ) ; 
  $TERM_ACC = $PARENT_ONT_TERM_ACC = $result1['TERM_ACC'];
  $TERM = $PARENT_ONT_TERM_NAME = $result1['TERM'];

  $breadbrumb =   makeLink('Portal Admin', 'portalAdmin', 'processPortal') . " > " . makeLink('Top Level Portal Terms', 'portalAdmin', 'showTerms' , 'PORTAL_KEY=' . $PORTAL_KEY) . " > " . "Child Portal Terms"; 

  $toReturn .= makeBreadCrumbLink( $breadbrumb ) ; 
  setPageTitle('Child Portal Terms'  ."&nbsp;". createHelpLinkCW( "Portals", "Portal Terms"));
  $toReturn .= "<br> Adding Child terms under : <b> $PARENT_ONT_TERM_NAME </b><br/><br/>";
  //$toReturn .= $theForm->quickRender();
  $toReturn .= '<p></p>';

  // set up results table at the bottom of screen showing top level annotations. 
  $entries = fetchRecords("select * from PORTAL_TERMSET1 where PORTAL_KEY = $PORTAL_KEY and PARENT_TERMSET_ID = $PARENT_TERMSET_ID order by ONT_TERM_NAME  ");
  $table1 = newTable('Ontology', ' Term ', 'Acc ID', 'Delete Term' );
  $table1->setAttributes('class="simple" width="100%"');
  foreach ($entries as $entry) {
    extract($entry);
    $table1->addRow(getTermOntologyStrByID($PARENT_ONT_TERM_ACC), $ONT_TERM_NAME, $TERM_ACC,
		makeLink('<img src="icons/delete.png" border=0 alt="Add">', 'portalAdmin', 'delChildTerm', 'PORTAL_KEY=' . $PORTAL_KEY . '&parentTermSetId='. $PARENT_TERMSET_ID . "&portalTermSetId=$PORTAL_TERMSET_ID") );
  }
  
  
  switch ($theForm->getState()) {
     case INITIAL_GET:
       $toReturn .= $theForm->quickRender();
       
     break;
     
    case SUBMIT_INVALID:
      $toReturn .= $theForm->quickRender();
      
      break;
    case SUBMIT_VALID:
    // search on term done display results and allow user to select term. 
   
      $term = $theForm->getValue('TERM');
      $term = trim( $term); 
      $theForm->setDefault('TERM' , $term);
      // setSessionVar('meshSearchTerm_'.$fieldId, $term);
      extract($theForm->getValues());
      $currentTerms = fetchArrayForSelectField("select term_acc,1 from portal_termset1 where parent_termset_id = $PARENT_TERMSET_ID and portal_key = $PORTAL_KEY");
      //dump( $currentTerms ) ; 
      $start = getRequestVarNum('start');
      $sql = "SELECT *
        FROM 
         ont_terms t
        WHERE
          t.term_acc IN ( 
            SELECT child_term_acc
			FROM ont_dag
            START WITH child_term_acc = '$PARENT_ONT_TERM_ACC'
			CONNECT BY PRIOR child_term_acc = parent_term_acc
        ) 
        AND ("; 
       
      $termArray = preg_split('/\n/', $term);
      $doComma = false;
      foreach ( $termArray as $singleTerm ) { 
        $singleTerm  = trim ( $singleTerm); 
        if ( $doComma ) {
          $sql .= " OR ";
        }
       $sql .=  "REGEXP_LIKE(t.term,'$singleTerm','i')";
       $doComma = true;
      }
      $sql .= ") ORDER BY t.term";
	  //dump($sql);
      $results = fetchRecordsLimit($sql, $maxResults, $start);
      $formValues = $theForm->getAllValues();
      $toReturn .= $theForm->quickRender();
      
      if (count($results) == 0) {
        $suggestions = array() ;
        if (count($suggestions) > 0) {
          $toReturn .= "No results found ... Did you mean : ";
          foreach ($suggestions as $suggestion) {
            
            $formValues['TERM'] = str_replace($term, $suggestion, $theForm->getValue('TERM'));
            $toReturn .= makeLink($formValues['TERM'], 'portalAdmin', 'showChildTerms', $formValues).' ?';
          }
        }
        else {
          $toReturn .= "We're sorry, we didn't find any terms in the vocabulary matching your search term, please try again.";
        }
      }
      else {
        // now do the esummary against the webenv, which will give us all the results at once
        $table = newTable('Select', 'Ontology', 'Term', 'Acc ID');
        $table->setAttributes('class=simple');
        $index = $start;
        foreach ($results as $result) {
          //var_dump($currentTerms);
          extract($result);
          ++$index;
          $addLink = makeAjaxCheckbox(makeUrl('portalAdmin', 'toggleChildTerm', 
			"quiet=1&termName=" . urlencode($TERM). "&portalKey=$PORTAL_KEY&parentTermSetId=$parentTermSetId&termAcc=$TERM_ACC"),
			array_key_exists($TERM_ACC, $currentTerms), 'notificationArea-'.$TERM_ACC.'-'.$index);
          $table->addRow($addLink.'<br/><span id="notificationArea-'.$TERM_ACC.'-'.$index.'" style="background-color:beige"></span>',
			getTermOntologyStrByID($TERM_ACC), $TERM, $TERM_ACC);
        } 
        $count = fetchField('select count(*) from ('.$sql.')');
  
 
        $toReturn .= "<br/><b>Your search returned $count results. ".
			'You may use any of these terms in your profile by checking the checkbox, or you may further refine or generalize your term by clicking on the term name itself and browsing ancestor or descendant terms. </b><br/><br/>';
  
        $pagingLinks = '<div align="center">'.doPagingLinks($count, $maxResults, $start, "<-- View Previous", "View Next -->", $theForm, 'start').'</div>';
        //toReturn .= $pagingLinks.'<br/>';
        $toReturn .= $table->toHtml(); 
        $toReturn .= "<br/>$pagingLinks"; 
      
      }

  }
  // $toReturn .= $table->toHtml();
  $toReturn .="<p><h3>Current Top Level Portal Terms</h3>";
  $toReturn .= $table1->toHtml(); 
  return $toReturn;
}

?>
