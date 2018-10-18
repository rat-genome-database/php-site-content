<?php
/*
 * objectEdit.php
 * $Revision: 1.3 $
 * $Date: 2007/12/18 19:41:41 $
 */
 
 function objectEdit_selectObjects() {
  setPageTitle("Object Edit");
  if (!userLoggedIn()) {
    return NOTLOGGEDIN_MSG;
  }
  $toReturn = '';
  $toReturn = 'You have the following options:<br>  <font color=red>( Please note that Nomenclature events are NOT created when you edit an object )</font><p>';
  $toReturn .= '<P><FIELDSET><LEGEND> Enter an object name </legend>';
  $theForm = newForm('Search for Object', 'GET', 'objectEdit', 'selectObjects');
  $theForm->addText('objectName', 'Enter an object name to edit or it\'s RGDID', 12, 80, true);
  $theForm->addSelect('matchType', 'Search where:', getSearchMatchType(), true);
  $theForm->addRadio('objectType', 'Object Type:', getObjectArrayForNotes(), true);
  $theForm->setDefault('objectType', 6); //QTL
  $theForm->setDefault('matchType', 'contains');
  $theForm->setInitialFocusField('objectName');
  

  switch ($theForm->getState()) {
    case INITIAL_GET :
    $toReturn .= $theForm->quickRender();
    $toReturn .= '</LEGEND></FIELDSET>';
      return $toReturn;
    case SUBMIT_INVALID :
    $toReturn .= $theForm->quickRender();
  $toReturn .= '</LEGEND></FIELDSET>';
      redirectWithMessage(' Missing field. Please fill in the missing field and resubmit. ', makeUrl('objectEdit', 'selectObjects'));
      break;
    case SUBMIT_VALID :
    $toReturn .= $theForm->quickRender();
    $toReturn .= '</LEGEND></FIELDSET>';
      // we should not do processing here as we have three forms on one page. They should all jump of to different
      // functions for processing . 
      switch ( $theForm->getValue('objectType') ) {
       case 1:
       case 5:
        redirectWithMessage(' Cannot Edit this type of object yet...', makeUrl('objectEdit', 'selectObjects'));
       break;
       case 6:
        $toReturn .= "<p/>";
        $qtlHtml = getQTLsByName($theForm->getValue("objectName"), is_numeric($theForm->getValue('objectName')));
         $toReturn .= $qtlHtml; 
       break;
      
      }
      // redirectWithMessage(' Method call invalid. See a programmer as they screwed up.  ');
      break;
    default :
      return $toReturn;

  }
  return $toReturn;
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

function getQTLsByName( $qtlName , $is_rgdid) {
  if (!  isReallySet ( $qtlName )) { 
    return ''; 
  } 
  if ( $is_rgdid ) { 
          $sql = 'select * from QTLS where rgd_id = ' . $qtlName ;
        }
        else { 
           $sql = 'select * from QTLS where upper( qtl_symbol ) like upper( \'%' . $qtlName . '%\') order by qtl_symbol';
        }
    
  $entries = fetchRecords($sql);
  $table = newTable('QTL_KEY', 'QTL_SYMBOL', 'QTL_NAME');
  $table->setAttributes('class="simple" width="100%"');
  foreach ($entries as $entry) {
    extract($entry);
    $table->addRow(makeLink($QTL_KEY, 'objectEdit', 'updateQTLS', 'QTL_KEY=' . urlencode($QTL_KEY)), $QTL_SYMBOL, $QTL_NAME );
  }
  return $table->toHtml();
}

function objectEdit_updateQTLS() {
  
  $toReturn = '';
  $breadbrumb =   makeLink("Object Edit", 'objectEdit', 'selectObjects') . " > Update QTL Entry" ; 

  $toReturn .= makeBreadCrumbLink( $breadbrumb ) ;
  $qtlKey =   getRequestVarString('QTL_KEY');
  $qtlKey =   getRequestVarString('QTL_KEY');
  $theForm = newForm('Submit', 'GET', 'objectEdit', 'updateQTLS');
  $theForm->addHidden('QTL_KEY', $qtlKey ) ; 
  $theForm->addText('QTL_SYMBOL', 'QTL_SYMBOL   ', 20, 20, true);
  $theForm->addText('QTL_NAME', 'QTL_NAME   ', 60, 500, true);
  $theForm->addSelect('species','Species',  getSpeciesArrayForDropDown(),3, true);
  $theForm->addNumber('PEAK_OFFSET', 'PEAK_OFFSET   ', 0, 100, false);
  $theForm->addText('CHROMOSOME', 'CHROMOSOME ', 3, 10, false);
  $theForm->addNumber('LOD', 'LOD ', 0, 10000, false);
  $theForm->addNumber('P_VALUE', 'P_VALUE ', 0, 10000, false);
  $theForm->addNumber('VARIANCE', 'VARIANCE ', 0, 100, false);
  $theForm->addTextArea('NOTES', 'NOTES ', 3, 70, 1000, false);
  //$theForm->addNumber('FLANK_1_RGD_ID', 'FLANK_1_RGD_ID ', 60, 450, false);
  //$theForm->addNumber('FLANK_2_RGD_ID', 'FLANK_2_RGD_ID ', 60, 450, false);
  //$theForm->addNumber('PEAK_RGD_ID', 'PEAK_RGD_ID ', 60, 450, false);
  $theForm->addSelect('INHERITANCE_TYPE', 'INHERITANCE_TYPE ', getInheritanceTypes(),  false);
  $theForm->addText('LOD_IMAGE', 'LOD_IMAGE ', 60, 500, false);
  $theForm->addText('LINKAGE_IMAGE', 'LINKAGE_IMAGE ', 60, 500, false);
  $theForm->addText('SOURCE_URL', 'SOURCE_URL ', 60, 500, false);
 
  $theForm->setInitialFocusField('QTL_KEY');
  $sql = null;
  switch ($theForm->getState()) {
    case INITIAL_GET :
      if (isReallySet($qtlKey)) { 
        $sql = 'select q.*, r.species_type_key  from QTLS q , rgd_ids r where QTL_KEY  = '. $qtlKey .' and q.rgd_id = r.rgd_id ';
        $entry = fetchRecord($sql);
        $theForm->setDefaults($entry);
        $speciesID = $entry{'SPECIES_TYPE_KEY'};
        $theForm->setDefault('species', $speciesID);
        setPageTitle('Update QTL Entry for RGDID: '. $entry{'RGD_ID'});
      } else {
        setPageTitle('New QTL Entry');
      }
    case SUBMIT_INVALID :
      $toReturn .= $theForm->quickRender();

      break;
    case SUBMIT_VALID :
      $sql = 'select * from QTLS where QTL_KEY = ' . $qtlKey;
      $newSpeciesID = $theForm->getValue('species');
      $theForm->removeField('species');
      
      $result = fetchRecord($sql);
      if (count($result) != 0) {
        executeUpdate('update QTLS set ' . getFieldsForUpdate($theForm) . ' where QTL_KEY =  ' . dbQuoteString($qtlKey));
        executeUpdate('update rgd_ids set species_type_key  = ' . $newSpeciesID . ' where rgd_id =  ( select rgd_id from qtls where qtl_key =' . $qtlKey . ')' );
        redirectWithMessage('QTL  successfully changed', makeUrl('objectEdit', 'updateQTLS', array ( 'QTL_KEY' => $qtlKey)));
      } else {

        // echo $newKey; 
        $theForm->setDefault('GENE_TYPE_LC', $geneTypeLC);
        // executeUpdate('insert into GENE_TYPES ' . getFieldsForInsert($theForm));
        redirectWithMessage('QTL Cannot be created at this time ...'. $sql, makeUrl('objectEdit', 'updateQTLS'));
      }
      break;
  }
  return $toReturn;
}


/**
 * Returns array of inheritance types for QTL table
 */
function getInheritanceTypes(){
  $inhArray = array(); 
  $entries = fetchRecords('Select * from inheritance_types');
  
  foreach ($entries as $entry) {
    extract($entry);
    $inhArray[$INHERITANCE_KEY] = $DESCRIPTION;
  }
  return $inhArray;
}
?>
