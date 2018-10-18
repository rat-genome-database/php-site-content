<?php

function getAspect($ont_codes) {
	$aspect = array();
	for( $k=0; $k<count($ont_codes); $k++ ) {
		$ont_code = $ont_codes[$k]; 
        if ($ont_code == 4) {
			$aspect[] = "'C','F','P'";
		} elseif( $ont_code == 7 ) {
			$aspect[] = "'B','D'";
		} elseif( $ont_code == 6 ) {
			$aspect[] = "'W'";
		} elseif( $ont_code == 5 ) {
			$aspect[] = "'N'";
		}
	}
	return $aspect;
}

function generateQuery($pipeline, $object, $species, $evidence_code = null, $dateArray, $ont_codes = null, $byannot = false,
$user = null) {

  $aspect = getAspect($ont_codes);
  	 
  $objCount = count($object);
  $specCount = count($species);
  $evCount = count($evidence_code);
  $aspectCount = count($aspect);
  $userkill=(!$user)?'--':'';  //if $user isn't passed in, userkill = '--',
  $distkill=(!$byannot)?'--':''; //etc, etc
  $aspectkill=(!$aspect)?'--':'';   //this comments out the relevant SQL
  $pipekill=(!$pipeline)?'--':'';
  $evidencekill=(!$evidence_code)?'--':'';
  $num=count($dateArray);
  $csObject = join(",", $object);  //set up comma separated strings for the SQL
    if (count($species) > 1){$species = join(",", $species);}
    
    $neoEvidence = join(",", $evidence_code);
    // $csEvidence = "'".str_replace(",", "','", $neoEvidence)."'";
    // not needed evidence array comes in a quoted strings
    $csAspect = join(",", $aspect);
    $toReturn=array();

      for ($i=0;$i<$num-1;$i++)
        {

        $sql = "
        select count (
        $distkill DISTINCT
        ANNOTATED_OBJECT_RGD_ID) AS RESULT from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key in ($csObject)
        and r.object_status = 'ACTIVE'
        $pipekill and f.evidence not in ('ISS', 'IEA', 'RCA')
        $evidencekill and f.evidence in ($neoEvidence)
        $aspectkill and f.aspect in ($csAspect)
        $userkill and f.created_by = $user
        and r.species_type_key in ($species)
        and f.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        
         if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        
        echo "<pre>".$sql."</pre><br><br>";
        //exit(0);
        $result = fetchRecord($sql);
        
        
       $toReturn[]=$result['RESULT'];
  
      }
      
  
 return $toReturn;   
}

function generateChart($dateArray, $plotArray, $kind="jp"){ //testing new GoogleCharts API for viability with RGD reporting
 include_once "jpgraph.php";
 //echo "hello world";
 //echo $kind;exit(0);
 if ($kind=="google"){
 $top = max($plotArray);
 $scaleArray = array();
 $scalar = (int)($top/10);
 $scaleArray[0] = 0;
for ($i=1;$i<10;++$i){
 $scaleArray[$i]= $scaleArray[$i-1] + $scalar;
} 
$scaleArray[10]=$top;
 $totalMonths = count($dateArray);
 //echo "is this line a problem?";
 $width = (int)((800/$totalMonths)/2)-2;
 //echo $width; 
//max chart size is 30k pixels, so i think 600x500 is probably our ideal bet here
//on second thought, to display x-axis labels with any significant number of bars,
//we'll need much more space.  800x375 it is.
 $toReturn='<img src="http://chart.apis.google.com/chart?chs=800x375&chd=t:';
 foreach ($plotArray as $key=>$value){
 if ($key != 0) $toReturn.=','; //add a comma after everything but the last item
 $toReturn .= $value;
 }
 $toReturn .= '&chds=0,'.$top.'&cht=bvs&chxt=x,y&chxl=0:|';
 foreach ($dateArray as $key=>$value){
  //if ($key != 0) $toReturn.='|';//add a pipe after everything but the last item  
  $toReturn.=date('M-j-y',$value).'|';
 }
 $toReturn.='1:';
 foreach ($scaleArray as $key=>$value){
  $toReturn.='|';//add a pipe after everything but the last item  
  $toReturn.=$value;
 }
 
 
$toReturn .= '&chbh=20,'.$width.','.$width;
$toReturn.= '">';  //remember anything enclosed in double quotes will not properly escape, appending a ton
                   //of junk to the image and formatting things wrong


 }
 else {
  //newgraph($dateArray, $plotArray, "Testing");
 // $toReturn = '<img src="http://localhost/rgdCuration/?module=reportGraph&func=drawGraph"></h2></br></br>';
//  setDirctOutput();
setSessionVar('Dates',$dateArray);
setSessionVar('Data',$plotArray);
$toReturn= '<img src="'.makeUrl('displayData','newGraph').'">'."</br></br>";
 //$toReturn = "candy and sunshine and happiness<br/>";
 //$toReturn .= getSessionVar('CreatedGraph');
  //$toReturn='<img src="http://localhost/rgdCuration/?module=reportGraph&func=drawGraph>';
  //$toReturn.=  makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMonthlyAnyAnnotations')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMonthlyAnyAnnotations'));
 }
  return $toReturn;
}

function generateTable($plotArray, $dateArray, $species)

{ 
  $table = newTable('Number of Annotation','Date','Species');
  $table->setAttributes('class="simple" border="3"');
  for($i=0;$i<count($plotArray); $i++) {
    $table->addRow($plotArray[$i], date('m-d-Y',$dateArray[$i]), $species);
  }
  
  return $table->toHtml();
}

 

//generates the table 

function generateTables($dateArray,$annotType,$species)

{  

  $toReturn = "";

  $toReturn .='<table border ="3">';

  

  $total=0;

  $lengthTable= count($dateArray);

  $toReturn.='<th>GO</th>';
  $toReturn.='<th>DO</th>';
  $toReturn.='<th>PW</th>';
  $toReturn.='<th>MP</th>'; 

  //$toReturn.='<th>Total Annotation </th>';

  //$toReturn.='<th>Cumulative Totals</th>'; 

  $toReturn.='<th> Date </th>';

 

  $annotType2=array();
//array_shift($annotType);
  foreach($annotType as $ont_name =>$ont_code) {

  

    $annotType2[$ont_name]= generateQuery(false, 6, 3, NULL, $dateArray, $ont_code, NULL);

  // annotTYpe2 is holding the values of annotType and is generating the query of plotArray based of the value in the array. 

  }

 //var_dump($annotType2); echo "<BR>";
//exit(0);
  for ($i=0; $i< count($dateArray)-1; $i++) 

  {

    $toReturn .='<tr>';
//var_dump($annotType); exit(0);
    foreach($annotType as $ont_name => $ont_code) //displays the elements for the table

    {

      $toReturn .='<td>'.$annotType2[$ont_name][$i].'</td>';

    }

    $toReturn .='<td>'.date("M d Y",$dateArray[$i]).'</td>';

    $toReturn .='</tr>';

  }

  $toReturn .='</table>';

  return $toReturn;

}


function generateCumQuery($pipeline, $object, $species, $evidence_code = null, $dateArray, $ont_codes = null, $byannot = false,
$user = null) {

  $aspect = getAspect($ont_codes);
  
  $objCount = count($object);
  $specCount = count($species);
  $evCount = count($evidence_code);
  $aspectCount = count($aspect);
  $userkill=(!$user)?'--':'';  //if $user isn't passed in, userkill = '--',
  $distkill=(!$byannot)?'--':''; //etc, etc
  $aspectkill=(!$aspect)?'--':'';   //this comments out the relevant SQL
  $pipekill=(!$pipeline)?'--':'';
  $evidencekill=(!$evidence_code)?'--':'';
  $num=count($dateArray);
  $csObject = join(",", $object);  //set up comma separated strings for the SQL
    if (count($species) > 1){$species = join(",", $species);}
    
    $neoEvidence = join(",", $evidence_code);
    // $csEvidence = "'".str_replace(",", "','", $neoEvidence)."'";
    // not needed evidence array comes in a quoted strings
    $csAspect = join(",", $aspect);
    $toReturn=array();



        $sql = "
        select count (
        $distkill DISTINCT
        ANNOTATED_OBJECT_RGD_ID) AS RESULT from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key in ($csObject)
        and r.object_status = 'ACTIVE'
        $pipekill and f.evidence not in ('ISS', 'IEA', 'RCA')
        $evidencekill and f.evidence in ($neoEvidence)
        $aspectkill and f.aspect in ($csAspect)
        $userkill and f.created_by = $user
        and r.species_type_key in ($species)
        and f.created_date <= to_date('".date('m-d-Y',$dateArray[0])."', 'MM-DD-YYYY')";
        $total = 0;
      

        

        $result = fetchRecord($sql);
        

       $total = $result['RESULT'];
       $toReturn[]=$result['RESULT'];
  
      for ($i=1;$i<$num-1;$i++){
      $sql = "
        select count (
        $distkill DISTINCT
        ANNOTATED_OBJECT_RGD_ID) AS RESULT from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key in ($csObject)
        and r.object_status = 'ACTIVE'
        $pipekill and f.evidence not in ('ISS', 'IEA', 'RCA')
        $evidencekill and f.evidence in ($neoEvidence)
        $aspectkill and f.aspect in ($csAspect)
        $userkill and created_by = $user
        and r.species_type_key in ($species)
        and f.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        
         if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        
     
        $result = fetchRecord($sql);
        $total+=$result['RESULT'];
        $toReturn[]=$total;
      }
      
  //var_dump($toReturn);
  //exit(0);
 return $toReturn;   
}

function exportToCSV(){
return "</h2></br></br>".makeLink('Export as CSV','report5','CSVPrep',array('tackon'=>'CumMBCA'));
}
 function getActiveUser()
{
 
  $sql ="
    select USER_KEY, FIRST_NAME from USERS 
    where ACTIVE_YN = 'Y'
    and PRIVILEGE = 'write'";
   
   $result= fetchRecords($sql,'LOGIN');
    foreach($result as $key =>$value)
   {
   $toReturn[$value['USER_KEY']]= $value['FIRST_NAME'];
   }
  //var_dump($result);
  //exit(0);
    return $toReturn;
}
?>
