<?php
/*
 * objectNomen.php
 * $Revision: 1.1 $
 * $Date: 2007/06/12 18:10:40 $
 */
 
 function objectNomen_selectObjects() {
  setPageTitle("Merge / Edit / Split an Object");
  if (!userLoggedIn()) {
    return NOTLOGGEDIN_MSG;
  }
  $toReturn = '';
  $toReturn .= '<P><FIELDSET><LEGEND> Merge/Retire/Split an Object </legend>';
  $theForm = newForm('Search for Object', 'POST', 'objectNomen', 'selectObjects');
  $theForm->addText('objectName', 'Enter an object name to start with', 12, 80, true);
  $theForm->addSelect('matchType', 'Search where:', getSearchMatchType(), true);
  $theForm->addSelect('operationType', 'Operation:', getOperationType(), true);
  $theForm->addRadio('objectType', 'Object Type:', getObjectArrayForNotes(), true);
  $theForm->setDefault('objectType', 1); //Gene
  $theForm->setDefault('matchType', 'contains');
  $theForm->setInitialFocusField('objectName');
  // $toReturn .= $theForm->quickRender();
  $objectName = getRequestVarString('objectName');
  $objectType = getRequestVarNum('objectType');
  $matchType = getRequestVarString('matchType');
  $urlSearchArray = array (
    "objectName" => $objectName,
    "objectType" => $objectType,
    "matchType" => $matchType
  );
  switch ($theForm->getState()) {
    case INITIAL_GET :
    
    case SUBMIT_INVALID :
      $toReturn .= $theForm->quickRender();
       $toReturn .= '</LEGEND></FIELDSET>';
       $toReturn .='<br/>' . makeLink('View Nomenclature Events', 'objectNomen', 'viewHistory'); 
      return $toReturn; 
      break;
    case SUBMIT_VALID :
      if ($objectType == '1') { //gene search
        setPageTitle("Rat Genes Found");
        $toReturn = $theForm->quickRender();
        $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (3));
      }
      return $toReturn;
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

function getOperationType() {
  return array (
    '1' => 'Merge',
    '2' => 'Retire',
    '3' => 'Split'
  );
}
/*
 *  Origianlly taken from curation.php
 */

function doSearchforGenesByName($objectName, $urlSearchArray, $matchType, $speciesArray) {

  $maxresults = 500;
  $toReturn = "";
  $objectName = trim($objectName);
  $rgd_id_to_searchfor = $objectName;
  $objectName = cleanUpAndHandleMatching($objectName, $matchType);

  $sql = 'select  g.gene_key, g.gene_symbol, g.full_name, g.rgd_id, r.object_status , r.species_type_key from ';
  $sql .= 'genes g , rgd_ids r ';
  $sql .= 'where  g.rgd_id = r.rgd_id  and  r.SPECIES_TYPE_KEY in ( ';
  $iterCount = 0;
  foreach ($speciesArray as $speciesID) {
    if ($iterCount++ == 1) {
      $sql .= " , ";
    }
    $sql .= $speciesID;
  }

  $sql .= ' ) and rownum <= ' . $maxresults . ' and (( upper ( gene_symbol ) like \'' . strtoupper($objectName) . '\' ) ';
  $sql .= 'or ( upper ( full_name ) like \'' . strtoupper($objectName) . '\' )';
  // take care of searching for RGDID directly here
  if (is_numeric($rgd_id_to_searchfor)) {
    $sql .= ' or ( g.rgd_id = ' . $rgd_id_to_searchfor . ' ) ';
  }
  $sql .= 'or (g.rgd_id in ( ';
  // sub query  to get ID's of genes where alias has been created for gene name or symbol
  $sql .= 'select rgd_id from aliases where alias_value_lc like \'' . strtolower($objectName) . '\'  ';
  $sql .= 'and ( alias_type_name_lc = \'old_gene_symbol\'  or alias_type_name_lc = \'old_gene_name\' )';
  $sql .= ')))';

  // dump ( $sql ) ;
  $genes = fetchRecords($sql);
  $genecount = count($genes);
  $table = newTable('RGDID', 'Symbol', 'Name', 'Species', 'Aliases', hrefOverlib("'Active Gene (Green) <br>Withdrawn Gene ( Yellow)<br>Retired Gene (Red) ', CENTER", 'Status'), 'Select');
  $table->setAttributes('class="simple" width="100%"');
  // $table->setAttributes('class="gene" width="100%"');
  $count = 0;
  // $urlSearchArray['matchType[]'] =  $matchType   ;  
  foreach ($genes as $gene) {

    // htmlEscapeValues($gene);
    extract($gene);

    $urlSearchArray['RGD_ID'] = $RGD_ID;
    $table->addRow($RGD_ID, $GENE_SYMBOL, $FULL_NAME, makeSpeciesLink($SPECIES_TYPE_KEY), getAliasesInHtml($RGD_ID, 'gene'), makeObjectStatusLink($OBJECT_STATUS), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addGeneToBucket', $urlSearchArray));
  }

  if ($genecount == $maxresults) {
    $toReturn .= " <font color='red'>NOTE: You've exceeded your maximum search results of 500, only the first 500 are being displayed.  ";
    $toReturn .= "Please narrow your search.</font><p/>\n";
  } else {
    $toReturn .= "Result Count : " . $genecount;
  }
  $toReturn .= $table->toHtml();
  return $toReturn;

}


?>
