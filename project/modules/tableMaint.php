<?php

/**
 * Table Maintenance Screens
 * 
 * $Revision: 1.8 $
 * $Date: 2007/06/11 20:15:47 $
 * Created by George Kowalski 
 */

/**
 * Home page for All Table Maintenance Screens generated here. 
 */
function tableMaint_home() {

	setPageTitle("Table Maintenance");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}

	setPageTitle("Lookup Table Maintenance");
	$table = newTable('Lookup Tables');
	$table->setAttributes('class="simple" width="70%"');
	$table->addRow(makeLink('Report Process Types', 'tableMaint', 'processTypes') .'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Report Process Types Table"));
	$table->addRow(makeLink('Gene Types', 'tableMaint', 'geneTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Gene Types Table"));
	$table->addRow(makeLink('RGD_XDB Table', 'tableMaint', 'RGDxdb').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "RGD_XDB Table"));
	$table->addRow(makeLink('RGD_XDB_SPECIES_URL Table', 'tableMaint', 'RGDxdbSpecies').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "RGD_XDB_SPECIES_URL Table"));
	$table->addRow(makeLink('Gene Variation Types ( Not active ) ', 'tableMaint', 'geneVariationTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Gene Variation Types Table"));
	$table->addRow(makeLink('RGD Cross Reference Table ( Not active ) ', 'tableMaint', 'xdb').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "RGD Cross Reference Table"));
	$table->addRow(makeLink('Strain Types ( Not active ) ', 'tableMaint', 'strainTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Strain Types Table"));
	$table->addRow(makeLink('Sequence Types ( Not active )', 'tableMaint', 'sequenceTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Sequence Types Table"));
	$table->addRow(makeLink('Note Types  ( Not active )', 'tableMaint', 'noteTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Note Types Table"));
	$table->addRow(makeLink('Maps ( Not active )', 'tableMaint', 'maps').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Maps Table"));
	$table->addRow(makeLink('Alias Types (  Not active )', 'tableMaint', 'aliasTypes').'&nbsp&nbsp' .createHelpLinkCW( "LookUp Tables", "Alias Types Table"));
	$toReturn = '<center>' . $table->toHtml();
	return $toReturn;
}

/**
* Display the REPORT_PROCESS_TYPES table
*/
function tableMaint_processTypes() {
	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$toReturn .= makeLink('Create a Entry', 'tableMaint', 'updateProcessTypes');
	$toReturn .= '<p></p>';
	setPageTitle('Report Process Types');
	$entries = fetchRecords('select * from REPORT_PROCESS_TYPES order by subsystem_name  , data_extract ');
	$table = newTable('ID', 'SubSystem Name', 'Extract', 'Description', 'Del');
	$table->setAttributes('class="simple" width="100%"');
	foreach ($entries as $entry) {
		extract($entry);
		$table->addRow(makeLink($RPT_PROCESS_TYPE_ID, 'tableMaint', 'updateProcessTypes', 'RPT_PROCESS_TYPE_ID=' . $RPT_PROCESS_TYPE_ID), $SUBSYSTEM_NAME, $DATA_EXTRACT, $RPT_PROCESS_TYPE_DESC, makeLink('<img src="icons/database_delete.png" border=0 alt="Delete">', 'tableMaint', 'deleteProcessTypes', 'RPT_PROCESS_TYPE_ID=' . $RPT_PROCESS_TYPE_ID));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}

/**
*  Update the REPORT_PROCESS_TYPES table
*/
function tableMaint_updateProcessTypes() {

	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$rptProcessTypeID = getRequestVarNum('RPT_PROCESS_TYPE_ID');
	$subSystemName = getRequestVarString('SUBSYSTEM_NAME');
	$dataExtract = getRequestVarString('DATA_EXTRACT');

	$theForm = newForm('Submit', 'GET', 'tableMaint', 'updateProcessTypes');
	$theForm->addHidden('RPT_PROCESS_TYPE_ID');
	$theForm->addText('SUBSYSTEM_NAME', 'SubSystem Name ', 20, 20, true);
	$theForm->addText('DATA_EXTRACT', 'Particular Extract ', 30, 30, false);
	$theForm->addText('RPT_PROCESS_TYPE_DESC', 'Process Description', 50, 50, false);

	switch ($theForm->getState()) {
		case INITIAL_GET :
			if (0 != $rptProcessTypeID) {
				$entry = fetchRecord('select * from REPORT_PROCESS_TYPES where RPT_PROCESS_TYPE_ID = ' . $rptProcessTypeID);
				$theForm->setDefaults($entry);
				setPageTitle('Update Entry');
			} else {
				// set the default subsystem name to the last one entered to make it easy on the admin. 
				$entry = fetchRecord('select SUBSYSTEM_NAME from ( select * from REPORT_PROCESS_TYPES  order by RPT_PROCESS_TYPE_ID desc ) where  rownum =1 ');

				$theForm->setDefault('SUBSYSTEM_NAME', $entry['SUBSYSTEM_NAME']);

				setPageTitle('New Entry');
			}
		case SUBMIT_INVALID :
			$toReturn .= $theForm->quickRender();

			break;
		case SUBMIT_VALID :
			if (0 != $rptProcessTypeID) {
				executeUpdate('update REPORT_PROCESS_TYPES set ' . getFieldsForUpdate($theForm) . ' where RPT_PROCESS_TYPE_ID =  ' . $rptProcessTypeID);
				redirectWithMessage('Entry successfully changed', makeUrl('tableMaint', 'processTypes'));
			} else {
				// Check if userID already Existed 

				$result = fetchRecord('select * from REPORT_PROCESS_TYPES where SUBSYSTEM_NAME = ' . dbQuoteString($subSystemName) . 'and data_extract = ' . dbQuoteString($dataExtract));
				if (count($result) != 0) {
					// redirectWithMessage('UserName Already Exists, please try another UserName', makeUrl('admin', 'updateProcessTypes'));
					$theForm->addFormErrorMessage('Thsi Subsystem / Data Extract combo already Exists, please try another pair');
					$toReturn .= $theForm->quickRender();
					break;
				}

				$newKey = getNextDBKey('REPORT_PROCESS_TYPES');
				// echo $newKey; 
				$theForm->setDefault('RPT_PROCESS_TYPE_ID', $newKey);
				executeUpdate('insert into REPORT_PROCESS_TYPES ' . getFieldsForInsert($theForm));
				redirectWithMessage('Entry successfully created', makeUrl('tableMaint', 'processTypes'));
			}
			break;
	}
	return $toReturn;
}
/**
 * Delete row from REPORT_PROCESS_TYPES table
 */
function tableMaint_deleteProcessTypes() {
	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$RPT_PROCESS_TYPE_ID = getRequestVarString('RPT_PROCESS_TYPE_ID');

	executeUpdate('delete from REPORT_PROCESS_TYPES where RPT_PROCESS_TYPE_ID= ' . $RPT_PROCESS_TYPE_ID);

	redirectWithMessage('Report Type successfully deleted', makeUrl('tableMaint', 'processTypes'));

}

/**
* Display the GENE_TYPES table
*/
function tableMaint_geneTypes() {
	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$toReturn .= makeLink('Create a Gene Type', 'tableMaint', 'updateGeneTypes');
	$toReturn .= '<p></p>';
	setPageTitle('Gene Types');
	$entries = fetchRecords('select * from GENE_TYPES order by GENE_TYPE_LC');
	$table = newTable('Primary Key', 'Internal Tools Description', 'Public Web Site Description', 'DEL');
	$table->setAttributes('class="simple" width="100%"');
	foreach ($entries as $entry) {
		extract($entry);
		$table->addRow(makeLink($GENE_TYPE_LC, 'tableMaint', 'updateGeneTypes', 'GENE_TYPE_LC=' . urlencode($GENE_TYPE_LC)), $GENE_TYPE_DESC, $GENE_DESC_PUBLIC, makeLink('<img src="icons/database_delete.png" border=0 alt="Delete">', 'tableMaint', 'deleteGeneTypes', 'GENE_TYPE_LC=' . urlencode($GENE_TYPE_LC)));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}

/**
*  Update the GENE_TYPES table
*/
function tableMaint_updateGeneTypes() {

	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$geneTypeLC = getRequestVarString('GENE_TYPE_LC');
	$geneTypeLC = trim($geneTypeLC);

	$theForm = newForm('Submit', 'GET', 'tableMaint', 'updateGeneTypes');
	$theForm->addText('GENE_TYPE_LC', 'Gene Type ( NOTE: Change this and <br>you\'ll get a new gene type  )  ', 20, 200, true);
	$theForm->addText('GENE_TYPE_DESC', 'Gene Type Description - Internal ', 60, 450, false);
	$theForm->addText('GENE_DESC_PUBLIC', 'Gene Type Description - Public', 60, 450, false);

	switch ($theForm->getState()) {
		case INITIAL_GET :
			if (isReallySet($geneTypeLC)) {
				$entry = fetchRecord('select * from GENE_TYPES where GENE_TYPE_LC = ' . dbQuoteString($geneTypeLC));
				$theForm->setDefaults($entry);
				setPageTitle('Update Entry');
			} else {
				setPageTitle('New Entry');
			}
		case SUBMIT_INVALID :
			$toReturn .= $theForm->quickRender();

			break;
		case SUBMIT_VALID :
			$result = fetchRecord('select * from GENE_TYPES where GENE_TYPE_LC = ' . dbQuoteString($geneTypeLC));
			if (count($result) != 0) {
				executeUpdate('update GENE_TYPES set ' . getFieldsForUpdate($theForm) . ' where GENE_TYPE_LC =  ' . dbQuoteString($geneTypeLC));
				redirectWithMessage('Gene Type successfully changed', makeUrl('tableMaint', 'geneTypes'));
			} else {
				// Check if userID already Existed 

				// echo $newKey; 
				$theForm->setDefault('GENE_TYPE_LC', $geneTypeLC);
				executeUpdate('insert into GENE_TYPES ' . getFieldsForInsert($theForm));
				redirectWithMessage('Gene Type successfully created', makeUrl('tableMaint', 'geneTypes'));
			}
			break;
	}
	return $toReturn;
}

/**
 * Delete Gene Types
 */
function tableMaint_deleteGeneTypes() {
	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$GENE_TYPE_LC = getRequestVarString('GENE_TYPE_LC');
	// Verify that this gene Type is not being used by the GENES table before deleting
	$result = fetchRecord('select GENE_TYPE_LC from ( select GENE_TYPE_LC from GENES where GENE_TYPE_LC = ' . dbQuoteString($GENE_TYPE_LC) . ' ) where  rownum =1 ');
	if (count($result) != 0) {
		redirectWithMessage('Sorry , this type cannot be deleted as it is being used in the GENES.GENE_TYPE_LC column. Change or delete occurances of \'' . $GENE_TYPE_LC . '\'  from this column first. ', makeUrl('tableMaint', 'geneTypes'));

	} else {

		executeUpdate('delete from GENE_TYPES where GENE_TYPE_LC= ' . dbQuoteString($GENE_TYPE_LC));
		redirectWithMessage('Gene Type successfully deleted', makeUrl('tableMaint', 'geneTypes'));
	}
}

/*
 * RGD_XDB functions
 */

/**
* Display the RGD_XDB table
*/
function tableMaint_RGDxdb() {
	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$toReturn .= makeLink('Create a new  Entry', 'tableMaint', 'updateRGDxdb');
	$toReturn .= '<p></p>';
	setPageTitle('RGD XDB Table');
	$entries = fetchRecords('select * from RGD_XDB order by XDB_NAME');
	$table = newTable('Primary Key', 'Name', 'Notes', 'DEL');
	$table->setAttributes('class="simple" width="100%"');
	foreach ($entries as $entry) {
		extract($entry);
		$table->addRow(makeLink($XDB_KEY, 'tableMaint', 'updateRGDxdb', 'XDB_KEY=' . $XDB_KEY), $XDB_NAME, $NOTES, makeLink('<img src="icons/database_delete.png" border=0 alt="Delete">', 'tableMaint', 'deleteRGDxdb', 'XDB_KEY=' . $XDB_KEY));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}

/**
*  Update and add new entries to the RGD_XDB table
*/
function tableMaint_updateRGDxdb() {

	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$xdbKey = getRequestVarString('XDB_KEY');

	$theForm = newForm('Submit', 'GET', 'tableMaint', 'updateRGDxdb');
	$theForm->addText('XDB_NAME', 'Name', 20, 50, true);
	// $theForm->addSelect('SPECIES_TYPE_KEY', 'Species', getSpeciesArrayForDropDown(), true); 
	// $theForm->addText('XDB_URL'        , 'URL', 70 , 200, true);
	$theForm->addTextarea('NOTES', 'Notes ', 20, 70, 1000, false);
	$theForm->addHidden('XDB_KEY', $xdbKey);

	switch ($theForm->getState()) {
		case INITIAL_GET :
			$breadcrumb = makeLink('RGD XDB Table', 'tableMaint', 'RGDxdb') . ' > ';
			if (isReallySet($xdbKey)) {
				$entry = fetchRecord('select * from RGD_XDB where XDB_KEY = ' . $xdbKey);
				$theForm->setDefaults($entry);
				$toReturn .= makeBreadCrumbLink( $breadcrumb .  " Update Entry $xdbKey " ) ;
			} else {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . ' New Entry') ;
			}
			$toReturn .= $theForm->quickRender();
			break;
		case SUBMIT_INVALID :
			$breadcrumb = makeLink('RGD XDB Table', 'tableMaint', 'RGDxdb') . ' > ';
			if (isReallySet($xdbKey)) {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . " Update Entry $xdbKey" );
			} else {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . 'New Entry' ) ;
			}
			$toReturn .= $theForm->quickRender();

			break;
		case SUBMIT_VALID :

			if (isReallySet($xdbKey)) {
				executeUpdate('update RGD_XDB set ' . getFieldsForUpdate($theForm) . ' where XDB_KEY =  ' . $xdbKey);
				redirectWithMessage('XDB Entry successfully changed', makeUrl('tableMaint', 'RGDxdb'));
			} else {
				// get new key from sequence
				$xdbKey = getNextDBKey("rgd_xdb");

				// echo $newKey; 
				$theForm->setDefault('XDB_KEY', $xdbKey);
				executeUpdate('insert into RGD_XDB ' . getFieldsForInsert($theForm));
				redirectWithMessage('XDB Entry successfully created', makeUrl('tableMaint', 'RGDxdb'));
			}
			break;
	}
	return $toReturn;
}

/**
 * Delete from RGD_XDB table if no extries in the RGD_ACC_XDB table found
 */
function tableMaint_deleteRGDxdb() {
	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$xdbKey = getRequestVarNum('XDB_KEY');
  // check to see if this key is being used in the RGD_ACC_XDB table. 
	$sql = 'select XDB_KEY from RGD_ACC_XDB where XDB_KEY = ' . $xdbKey . ' and  rownum = 1';
	$result = fetchRecords($sql);
	if (count($result) != 0) {
		redirectWithMessage('Sorry , this type cannot be deleted as it is being used in the RGD_ACC_XDB.XDB_KEY column. Change or delete occurances of \'' . $xdbKey . '\'  from this column first. ', makeUrl('tableMaint', 'RGDxdb'));
		return;
	} else {
    
    // Check if this is referenced in the RGD_XDB_SPECIES_URL table. Don't allow deletion if it is. 
    $sql = 'select XDB_KEY from RGD_XDB_SPECIES_URL where XDB_KEY = ' . $xdbKey . ' and  rownum = 1';
    $result = fetchRecords($sql);
    if (count($result) != 0) {
      redirectWithMessage('Sorry , this type cannot be deleted as it is being used in the RGD_XDB_SPECIES_URL.XDB_KEY column. Change or delete occurances of \'' . $xdbKey . '\'  from this column first. ', makeUrl('tableMaint', 'RGDxdb'));
    return;
    } 
    // OK to delete. 
		executeUpdate('delete from RGD_XDB where XDB_KEY = ' . $xdbKey);

		redirectWithMessage('XDB Extry successfully deleted', makeUrl('tableMaint', 'RGDxdb'));
	}

}

/*
 * RGD_XDB_SPECIES_URL functions
 */

/***************************************************************************
* Display the RGD_XDB_SPECIES_URL table
***************************************************************************/
function tableMaint_RGDxdbSpecies() {
	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$toReturn .= makeLink('Create a new  Entry', 'tableMaint', 'updateRGDxdbSpecies', array('COMMAND' => 'new'));
	$toReturn .= '<p></p>';
	setPageTitle('RGD_XDB_SPECIES_URL  Table');
	$entries = fetchRecords('select * from RGD_XDB_SPECIES_URL order by XDB_KEY, SPECIES_TYPE_KEY');
	$table = newTable('Species', 'Name', 'Notes', 'URL', 'DEL');
	$table->setAttributes('class="simple" width="100%"');
	foreach ($entries as $entry) {
		extract($entry);
    $tmpArray['XDB_KEY'] = $XDB_KEY; 
    $tmpArray['SPECIES_TYPE_KEY'] = $SPECIES_TYPE_KEY;
		$table->addRow(makeLink(getSpeciesName($SPECIES_TYPE_KEY), 'tableMaint', 'updateRGDxdbSpecies', $tmpArray),
     getXDBNameByKey($XDB_KEY), 
     $NOTES, 
     substr($XDB_URL, 0, 20), 
     makeLink('<img src="icons/database_delete.png" border=0 alt="Delete">', 'tableMaint', 'deleteRGDxdbSpecies', 'XDB_KEY=' . $XDB_KEY . '&SPECIES_TYPE_KEY=' . $SPECIES_TYPE_KEY));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}

/***************************************************************************
*  Update and add new entries to the RGD_XDB_SPECIES_URL table
***************************************************************************/
function tableMaint_updateRGDxdbSpecies() {

	if (!userIsCurator()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';
	$xdbKey = getRequestVarString('XDB_KEY');
	$speciesTypeKey = getRequestVarString('SPECIES_TYPE_KEY');
  $command = getRequestVarString('COMMAND');
  
	$theForm = newForm('Submit', 'GET', 'tableMaint', 'updateRGDxdbSpecies');
  if ( $command == 'new' ) { 
    $theForm->addSelect('XDB_KEY', 'XDB Name', getXDBNameArrayForDropDown(), true);
    $theForm->addSelect('SPECIES_TYPE_KEY', 'Species', getSpeciesArrayForDropDown(), true);
  } else { 
    // We stick in these Read Onlyt fields just to display them to the user, the real values 
    // Are put in as hiddne fields below. 
	  $theForm->addReadOnlySelect('XDB_KEY_RO', 'XDB Name', getXDBNameArrayForDropDown(), true);
    $theForm->setDefault('XDB_KEY_RO', $xdbKey);
    $theForm->addReadOnlySelect('SPECIES_TYPE_KEY_RO', 'Species', getSpeciesArrayForDropDown(), true);
    $theForm->setDefault('SPECIES_TYPE_KEY_RO', $speciesTypeKey);
    $theForm->addHidden('XDB_KEY', $xdbKey) ; 
    $theForm->addHidden('SPECIES_TYPE_KEY', $speciesTypeKey); 
  }

	$theForm->addText('XDB_URL', 'URL', 70, 200, true);
	$theForm->addTextarea('NOTES', 'Notes ', 20, 70, 1000, false);
  $theForm->addHidden('COMMAND', $command); 


	switch ($theForm->getState()) {
		case INITIAL_GET :
			$breadcrumb = makeLink('RGD_XDB_SPECIES_URL Table', 'tableMaint', 'RGDxdbSpecies') . ' > ';
			if ($command != 'new' ) {
				$entry = fetchRecord('select * from RGD_XDB_SPECIES_URL where XDB_KEY = ' . $xdbKey . ' AND SPECIES_TYPE_KEY = ' . $speciesTypeKey);
				$theForm->setDefaults($entry);
 
				$toReturn .= makeBreadCrumbLink( $breadcrumb . "Update Entry $xdbKey" ) ;
        
			} else {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . 'New Entry' ) ;
        $toReturn .= "<p><b>NOTE: You will not be permitted to override an existing entry if you select a XDB Name and Species that already exist.</b></p>";
			}
			$toReturn .= $theForm->quickRender();
			break;
		case SUBMIT_INVALID :
			$breadcrumb = makeLink('RGD_XDB_SPECIES_URL Table', 'tableMaint', 'RGDxdbSpecies') . ' > ';
			if ($command != 'new' ) {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . " Update Entry $xdbKey" ) ;
        
			} else {
				$toReturn .= makeBreadCrumbLink( $breadcrumb . ' New Entry')  ;
        $toReturn .= "<p><b>NOTE: You will not be permitted to override an existing entry if you select a XDB Name and Species that already exist.</b></p>";
			}
			$toReturn .= $theForm->quickRender();

			break;
		case SUBMIT_VALID :

      // If entry does not exist then create, otherwise update existing 
      // Entry.
      $alreadyExists = rgdXdbSpeciesALreadyExist($xdbKey, $speciesTypeKey); 
      // Prevent new entries from overwriting existing. User needs to update. 
      if ( $command == 'new' && $alreadyExists ) { 
        redirectWithMessage('Error: RGD_XDB_SPECIES_URL Entry already exists. Please edit the existing entry.', makeUrl('tableMaint', 'RGDxdbSpecies'));
        return;
      } 
      
			if ($alreadyExists ) {
        $theForm->removeField('COMMAND');
				executeUpdate('update RGD_XDB_SPECIES_URL set ' . getFieldsForUpdate($theForm) . ' where XDB_KEY =  ' . $xdbKey . ' AND SPECIES_TYPE_KEY = ' . $speciesTypeKey);
				redirectWithMessage('RGD_XDB_SPECIES_URL Entry successfully updated', makeUrl('tableMaint', 'RGDxdbSpecies'));
			} else {
				$theForm->removeField('COMMAND');
				executeUpdate('insert into RGD_XDB_SPECIES_URL ' . getFieldsForInsert($theForm));
				redirectWithMessage('RGD_XDB_SPECIES_URL Entry successfully created', makeUrl('tableMaint', 'RGDxdbSpecies'));
			}
			break;
	}
	return $toReturn;
}

/***************************************************************************
 * Delete from RGD_XDB_SPECIES_URL table
 ***************************************************************************/
function tableMaint_deleteRGDxdbSpecies() {
	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$xdbKey = getRequestVarNum('XDB_KEY');
  $speciesTypeKey = getRequestVarNum('SPECIES_TYPE_KEY');
	executeUpdate('delete from RGD_XDB_SPECIES_URL where XDB_KEY = ' . $xdbKey . ' AND SPECIES_TYPE_KEY = ' . $speciesTypeKey);
	redirectWithMessage('RGD_XDB_SPECIES_URL Entry successfully deleted', makeUrl('tableMaint', 'RGDxdbSpecies'));


}
/***************************************************************************
 * Returns array of species ready for drop Down List
 ***************************************************************************/
function getXDBNameArrayForDropDown() {
	$returnArray = array ();
	$sql = "select XDB_KEY as KEY , XDB_NAME as NAME from RGD_XDB order by XDB_NAME";
	$entries = fetchRecords($sql);
	foreach ($entries as $entry) {
		extract($entry);
		$returnArray["$KEY"] = $NAME;
	}
	return ($returnArray);
}

/**
 * Returns array of species ready for drop Down List
 */
function getXDBNameByKey($xdbKey) {
  $toReturn = 'Unknown'; 
  $sql = "select XDB_NAME  from RGD_XDB where XDB_KEY = ". $xdbKey;
  $entries = fetchRecords($sql);
  foreach ($entries as $entry) {
    extract($entry);
    $toReturn =  $XDB_NAME;
  }
  return ($toReturn);
}

/** 
 * Check for existance of RGD_XDB_SPECIES_URL table entry given the xdbKey and speciesTypeKey
 * Returns true if it exists , else false
 * 
 */
 function rgdXdbSpeciesAlreadyExist($xdbKey, $speciesTypeKey) { 
  $result = fetchRecord('select *  from RGD_XDB_SPECIES_URL where XDB_KEY = ' . $xdbKey . ' and SPECIES_TYPE_KEY = ' . $speciesTypeKey . ' and  rownum =1 ');
  if (count($result) != 0) {
    return true; 
  } else { 
    return false; 
  }
 }
?>