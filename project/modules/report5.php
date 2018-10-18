<?php
include_once("reportfunctions.php");
include_once("jpgraph.php");

//main module for the new reporting software
//alysha crowe and arthur rumpf
//last updated aug 16 08
function report5_MainSearch(){
  
  $toReturn ='';

  setPageTitle('Main Search');
  $arraye=getEvidenceArrayForDropDown(false, NULL,"qtls"); // with arg2 NULL arg3 is irrelevant

   //builds the form 
   
  $theForm = newForm('Submit', 'POST', 'report5','MainSearch'); 
  $theForm->addSelect('species', 'Species:', array ( 1 => 'Human', 2=> 'Mouse', 3=> 'Rat', '1,2,3'=>'All' ), true);
  $theForm->addSelect('object', 'Object Type:', array ( 1 => 'Gene', 6=> 'QTL', 5=> 'Strains', '1,5,6'=>'All' ), true); 

  $theForm->addSelect('userId', 'USER ID', getActiveUser(), false);
  $theForm->addMultipleCheckbox('Annotations', 'Annotations:', array('4'=>'GO','7'=>'DO','6'=>'PW','5'=>'MP'),false,''); 
  $theForm->addRadio('querytype', 'Please choose:', array('1'=>'Objects Annotated', '2'=>'Annotations'), true);
  $theForm->addCheckbox('resulttype','Check here for cumulative results:'); 
  $theForm->addCoolMultipleSelect('Evidence', 'Evidence Codes:',$arraye, count($arraye), false);   
  $theForm->addCoolDate('fromdate','Start date:',1);
  $theForm->addCoolDate('todate', 'End date:', 1);
  $theForm->addSelect('graphtype', 'Graph displayed by:', array('jp'=>'JPGraph', 'google'=>'Google Charts'), true);
  
  //setting the deafults
  $theForm->setDefault('resulttype', false);
  $theForm->setDefault('querytype', '2');
  $theForm->setDefault('species', '3');
  $theForm->setDefault('object','1');
  $theForm->setDefault('ObjectOrAnnot','Objects');
  $theForm->setDefault('Annotations','4','7','6','5');
  $theForm->setDefault('fromdate',getLastYear()); 
  $theForm->setDefault('todate', date('m/d/Y'));
  $theForm->setDefault('graphtype', 'jp');
  
  
 
  // validate the form 
  switch ($theForm->getState()){
    case INITIAL_GET :
   
  
    
   case SUBMIT_INVALID : 
   setPageTitle('Main Search');
   $toReturn .=$theForm->quickRender(); //returns the form
   
   break; 
   
   case SUBMIT_VALID : 
   setPageTitle('Results');
    $species=getRequestVarString('species'); //nab all the form data to work with
    $regOrCum=getRequestVarString('resulttype');    
    setSessionVar('Species',$species);
    $species_name=getSpeciesNameAndAll($species);
    $object=getRequestVarString('object');
    $annotObject=getRequestVarString('Annot with Objects');
    setSessionVar('Annot_object',$annotObject);
    $objs=getRequestVarString('querytype');
    $graphtype=getRequestVarString('graphtype');
    if($objs == 1) { 
      $doObjects = true;
    }else{
      $doObjects = NULL;
    }
      
   
    $evid=getRequestVarArray('Evidence');
    setSessionVar('Evidence', $evid);
   
    $user=getRequestVarString('userId');
  
    $fromDate=getRequestVarString('fromdate');
    $todate=getRequestVarString('todate');
    $dateArray=getStartOfMonthsByDates($fromDate,$todate);
    // returns the form 
    $toReturn .=$theForm->quickRender();  
   
 if ($regOrCum==false){  //I think this is the only place the second function really matters.
   $plotArray = generateQuery(false,array($object),$species,$evid,$dateArray,$theForm->getValue('Annotations'),$doObjects, $user);
 }
 else {
  $plotArray =  generateCumQuery(false,array($object),$species,$evid,$dateArray,$theForm->getValue('Annotations'),$doObjects, $user);
 }  
   
   
   $object_to_name = array ( 1 => 'Gene', 6=> 'QTL', 5=> 'Strains', '1,5,6'=>'All' );
   $toReturn .= '<h2>'. $species_name.' '. $object_to_name[$object].'</h2>';
   $toReturn .='<table border ="8">';


   $lengthTable= count($dateArray);

   $annotType2=array();
   foreach($theForm->getValue('Annotations') as $ont_code) 
   { 

     if ($ont_code == 4) 
     { 
       $toReturn.='<th> GO </th>';         
       $annotType2["GO"]= generateQuery(false, array($object),$species, $evid, $dateArray, array($ont_code), $doObjects,   $user);
     }
     if ($ont_code==7) { 
       $toReturn.='<th> DO </th>';
       $annotType2["DO"]= generateQuery(false, array($object),$species, $evid, $dateArray, array($ont_code), $doObjects,  $user); 
     }
     if ($ont_code==6) { 
       $toReturn.='<th> PW </th>';
       $annotType2["PW"]= generateQuery(false, array($object),$species, $evid, $dateArray, array( $ont_code), $doObjects, $user);
     }
     if ($ont_code==5) { 
       $toReturn.='<th> MP </th>'; 
       $annotType2["MP"]= generateQuery(false, array($object),$species, $evid, $dateArray, array($ont_code), $doObjects,  $user);  
     }
     

   } //ends the foreach loop - generating headers and running queries per ontology
   $annotType2["Total"] = $plotArray;
   
   // returns the date header for the table 
   $toReturn.='<th> Total ' . $objs .  '</th>';
   $toReturn.='<th> Total Number of objects with annotatoins created at any point this is useless</th>';
   $toReturn.='<th> Date </th>';

   $cummulative_total = 0;
   for ($i=0; $i< count($dateArray)-1; $i++)
   {

   $toReturn .='<tr>';

   // generate table rows
     $row_total = 0;
     foreach($theForm->getValue('Annotations') as $ont_code)
     {
       
       if($ont_code==4)
       { 
         $row_total += $annotType2["GO"][$i];
         $toReturn .= '<td align="right">'.$annotType2["GO"][$i].'</td>';
       }
       if($ont_code==7)
       {
         $row_total += $annotType2["DO"][$i]; 
         $toReturn .= '<td align="right">'.$annotType2["DO"][$i].'</td>';
       }
       if($ont_code==6) 
       {
         $row_total += $annotType2["PW"][$i];
         $toReturn .= '<td align="right">'.$annotType2["PW"][$i].'</td>';
       }
       if($ont_code==5)
       {
         $row_total += $annotType2["MP"][$i];
         $toReturn .= '<td align="right">'.$annotType2["MP"][$i].'</td>';
       }
     }// ends the foreach loop putting individual ontologies into row
     $cummulative_total += $row_total;
     $toReturn .= '<td align="right">'.$row_total.'</td>';
     $toReturn .= '<td align="right">'.$cummulative_total.'</td>';
     $toReturn .= '<td align="right">'.date("M d Y",$dateArray[$i])."-".date("M d Y",$dateArray[$i+1]).'</td>';
    
   $toReturn .='</tr>';
  } // ends for loop generating table rows
 $toReturn .='</table>';
 
 setSessionVar('Dates',$dateArray);
setSessionVar('Data2',$annotType2);
setSessionVar('Annotations', $theForm->getValue('Annotations'));

 $toReturn .= "</br>".makeLink('Export as CSV','displayData','CSVPrep');
 $toReturn.="</br>";
 $toReturn .= generateChart($dateArray, $plotArray, $graphtype);
 
 break;
 }
   return $toReturn;
}

 
 
   
  
 
?>
