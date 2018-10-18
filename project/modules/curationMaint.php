<?php


/**
 * Curation Maintain / Edit Functions 
 * $Header: /var/lib/cvsroot/Development/RGD/rgdCuration/project/modules/curationMaint.php,v 1.8 2007/09/07 15:16:09 gkowalski Exp $
 *
 */
 
 
/**
 * Display a historical list of annotations for the user that is logged in with a link to delete an annotation
 * they made.  
 */
function curationMaint_showMyAnnotation() { 
  if (!userLoggedIn()) {
    return NOACCESS_MSG;
  }
  $userKey = getSessionVar('userKey') ; 
  $toReturn = '<p></p>';
  $theForm = newForm('Select date', 'POST', 'curationMaint', 'showMyAnnotation'); 
  $theForm->addCoolDate('fromdate','Start date:',1);
  $theForm->addCoolDate('todate','End date:',1);
  $theForm->setDefault('fromdate',getLastMonth());
  $theForm->setDefault('todate',date('m/d/Y'));

  switch ($theForm->getState()) {
    case INITIAL_GET:
    case SUBMIT_INVALID:
    
      break;
    case SUBMIT_VALID:
      break; 
  }

  $toReturn .= $theForm->quickRender();
  $fromDate = $theForm->getValue("fromdate");
  $toDate = $theForm->getValue("todate");
  $toReturn .= "Showing Annotations from ". $fromDate ." to " . $toDate . "<br>";
 
  setPageTitle('Your Annotations');
  
  // filter out annotations with 'I' aspect for DO ontology because they are auto generated by DoAnnotator pipeline
  $sql = 'SELECT a.* FROM full_annot a'; 
  $sql .= ' WHERE aspect<>\'I\' AND last_modified_by='. $userKey;
  $sql .= '  AND a.LAST_MODIFIED_DATE between TO_DATE(\''.$fromDate.'\',\'MM-DD-YYYY\') and TO_DATE(\''.$toDate.'\',\'MM-DD-YYYY\') +1 ';
  $sql .= ' ORDER BY last_modified_date  DESC';
  
  //dump ( $sql) ; 
  $records = fetchRecords($sql ) ; 
  $table = newTable( hrefOverlib("'Delete this Annotation and <b> ALSO DELETE</b> the Object to Reference link ', CENTER", 'Delete'), 'Object name', 'Reference', 'Term', 'Qualifier', 'Evidence', 'With Info', 'Modified',hrefOverlib("'Delete this Annotation <b>WITHOUT</b> deleting the Object to Reference link ', CENTER", 'Delete'));
  $table->setAttributes('class="simple" width="100%"');
  foreach ($records as $record) {
    extract($record);
     $table->addRow(  makeLinkConfirm('Are you positive you want to delete this annotation  ( Object to Reference link WILL be deleted ) ?', 'Annotation with Mapping', 'curationMaint', 'deleteAnnotation', 'FULL_ANNOT_KEY=' . $FULL_ANNOT_KEY . "&INCLUDE_MAP=true"), $OBJECT_SYMBOL, makeExternalLink($REF_RGD_ID, makeReferenceURL( $REF_RGD_ID)), $TERM_ACC, $QUALIFIER, $EVIDENCE, $WITH_INFO, $LAST_MODIFIED_DATE,
     makeLinkConfirm('Are you positive you want to delete this annotation ( Object to Reference link will NOT be deleted ) ?', 'Annotation', 'curationMaint', 'deleteAnnotation', 'FULL_ANNOT_KEY=' . $FULL_ANNOT_KEY)
     );
  }
  
  $toReturn .= $table->toHtml();
  return $toReturn;
} 
 
 /**  
  * Delete an annotation after user selects it from the showMyAnnotation function
  */
 function curationMaint_deleteAnnotation() { 
  if (!userLoggedIn()) {
    return NOACCESS_MSG;
  }
  $refDBKey = 0 ; 
  $objRgdID = 0; 
  $fullAnnotKey = getRequestVarString('FULL_ANNOT_KEY');
  $includeMap = getRequestVarString('INCLUDE_MAP');
  $toReturn = ''; 

  // Need to get the Reference RGDID and objects RGD ID first before deleting to delete from 
  // the RGD_RED_RGD_ID table
  $sqlSelectFullAnnot = "select * from full_annot where full_annot_key = " . $fullAnnotKey ;
  // $toReturn .= $sqlSelectFullAnnot . "<br>"; 
  $result = fetchRecord($sqlSelectFullAnnot);
  if (count($result) > 0) {
    extract($result);
    $ref = getObjectInfoByRGID($REF_RGD_ID);
    $refDBKey = $ref['REF_KEY']; // reference key , not RGDID
    $objRgdID = $ANNOTATED_OBJECT_RGD_ID;
  } else { 
    redirectWithMessage('Annotation could not be removed for fullAnnotKey :  ' . $fullAnnotKey . "<br>Please report this !", makeUrl('curationMaint', 'showMyAnnotation')); 
    return;  
  }
   
  // next delete from RGD_REF_RGD_ID table  if requested 
  if ( $includeMap == 'true' ) { 
    $sqlDeleteReferance = "delete from RGD_REF_RGD_ID where ref_key = " . $refDBKey . " and rgd_id = " . $objRgdID;
    // $toReturn .= $sqlDeleteReferance . "<br>"; 
    executeUpdate($sqlDeleteReferance);
      $toReturn =  'Annotation and the associated Object to Reference link have been deleted.<br> '; 
  } else { 
    $toReturn =  'Annotation has been deleted.<br> '; 
  }
  // finally delete from full_annot table
  $sqlDeleteFullAnnot = "delete from full_annot where full_annot_key = " . $fullAnnotKey ;
  //$toReturn .= $sqlDeleteFullAnnot . "<br>"; 
  executeUpdate($sqlDeleteFullAnnot);
  

  
  redirectWithMessage($toReturn , makeUrl('curationMaint', 'showMyAnnotation')); 
  
 }
 
 function getLastMonths() { 
   return array( "c" => "Current", 
  "-1" => "Previous Month", 
  "-2" => "Two Months ago", 
  "-3" => "Three Months ago", 
  "-4" => "Four Months ago", 
  "-5" => "Five Months ago", 
  "-6" => "Six Months ago" 
  ) ; 
 }
 
 /**
  * Returns the End of the month date given the processing month "c" for current , or -# for # months ago
  */
 function getMonthEndDate($processMonthStr ) { 
  if ( $processMonthStr == 'c' ) { $processMonthStr = 0; } 
  // $processMonthStr = $processMonthStr -1 ; 
  $sql = ' select TO_CHAR (  ADD_MONTHS (last_day ( sysdate ) , '. $processMonthStr . ")  , 'MM-DD-YYYY' ) as MYDATE from dual ";
  $row = fetchRecord ( $sql ) ; 
  return $row['MYDATE'];
  
 } 
 
 function getMonthStartDate($processMonthStr ) { 
  if ( $processMonthStr == 'c' ) { $processMonthStr = 0; } 
  $processMonthStr = $processMonthStr -1 ; 
  $sql = ' select TO_CHAR (  ADD_MONTHS (last_day ( sysdate ) , '. $processMonthStr . ") + 1  , 'MM-DD-YYYY' ) as MYDATE from dual ";
  $row = fetchRecord ( $sql ) ; 
  return $row['MYDATE'];
 } 
 
 
 
 
 ?>