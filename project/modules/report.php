<?php
include_once ("jpgraph.php");
//include_once ("jpgraph_line.php");
/**
* Curation Reports
* $Revision: 1.44 $
* $Date: 2007/08/09 14:19:00 $
* Created by George Kowalski
*/

/**
* Home page for All reports generated here.
*/
function report_home() {
    
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Reports");
    $table = newTable('Report Categories');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Basic RGD Data Objects', 'report', 'basicReportHome'));
    $table->addRow( 'Data Added to Genes');
    $table->addRow( makeLink('All Annotations', 'report', 'allAnnotReportHome'));
    $table->addRow(makeLink('GO Annotations','report','GOHome'));
    $table->addRow( makeLink('Disease Annotations','report','DOHome'));
    $table->addRow( makeLink('PathWay Annotations','report','PWHome'));
    $table->addRow(makeLink('Mammalian Phenotype Annotations','report','MPHome'));
    $table->addRow(makeLink('Nomenclature','report','nomenHome'));
    $table->addRow(makeLink('References','report','RefHome'));
    $table->addRow(makeLink('Pipeline Processes', 'report', 'start') . ' (  See: ' . makeLink('Process Types', 'tableMaint', 'processTypes') . ' ) ' );
    $table->addRow(makeLink('Options','report','setColor'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}

/**
* Basic RGD Data Objects This is the home page for all Gene Reports
*/
function report_basicReportHome() {
    
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Basic RGD Data Objects");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Current Genes', 'report', 'basicGeneReport'));
    $table->addRow( makeLink('Gene Creation History for last 3 years', 'report', 'geneHistory')
    . ' ( ' .makeLink('Human', 'report', 'geneHistory', 'species=1').  ' ) '
    . ' ( ' .makeLink('Mouse', 'report', 'geneHistory', 'species=2').  ' ) '
    );
    $table->addRow(makeLink('Number of Genes with each of the XDB IDs','report3','XDBStart'));
    $table->addRow(makeLink('Number of Genes with the XDB ID Added Each Month within Query Range','report3','MonthlyXDBStart'));
    $table->addRow(makeLink('Total Number of Genes with the XDB ID Each Month','report3','CumMonthlyXDBStart'));
    $table->addRow(makeLink('Number of Genes Added Each Month within Query Range','report3','GenesStart'));
    $table->addRow(makeLink('Total Number of Genes Each Month','report3','CumGenesStart'));
    $table->addRow(makeLink('Number of Gene Variants Added Each Month within Query Range','report3','VariantsStart'));
    $table->addRow(makeLink('Total Number of Gene Variants Each Month','report3','CumVariantsStart'));
    $table->addRow(makeLink('Number of Pseudogenes Added Each Month within Query Range','report3','PseudogenesStart'));
    $table->addRow(makeLink('Total Number of Pseudogenes Each Month','report3','CumPseudogenesStart'));
    $table->addRow(makeLink('Number of Alleles Added Each Month within Query Range','report3','AllelesStart'));
    $table->addRow(makeLink('Total Number of Alleles Each Month','report3','CumAllelesStart'));
    $table->addRow(makeLink('Number of Known and Predicted Genes Added Each Month Within Query Range','report3','KPStart'));
    $table->addRow(makeLink('Total Number of Known and Predicted Genes Each Month','report3','CumKPStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}

/**
* Basic RGD Data Objects This is the home page for all Gene Reports
*/
function report_allAnnotReportHome() {
    
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Full Annotation Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Object - Term - Reference Annotation History  by Month', 'report', 'geneHistoryByMonth') . ' ( ' . makeLink('Only My', 'report', 'myGeneHistoryByMonth') . ' )');
    //$table->addRow(makeLink('Number of Genes with any Ontology Annotations','reportA4','AnyAnnotationsStart'));
    $table->addRow(makeLink('Number of Ontology Annotations for Genes Each Month Within Query Range','report3','MonthlyAnyAnnotationsStart'));
    $table->addRow(makeLink('Total Number of Ontology Annotations for Genes-Cumulative By Month','report3','CumMonthlyAnyAnnotationsStart'));
    $table->addRow(makeLink('Number of Genes receiving Any Ontology Annotations Each Month Within Query Range','report3','MonthlyGWAnyAnnotationsStart'));
    $table->addRow(makeLink('Total Number of Genes with Any Ontology Annotations-Cumulative By Month','report3','CumMonthlyGWAnyAnnotationsStart'));
    //$table->addRow(makeLink('Percent of Genes Annotated','report3','PercentAnnotatedStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* GO Annotations
*/
function report_GOHome() {
    setSessionVar('AnnotFrom','G%');
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Gene Ontology Annotation Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Number of GO Annotations for Genes Added Each Month Within Query Range', 'report3', 'GOStart'));
    $table->addRow( makeLink('Total Number of GO Annotations for Genes Each Month','report3','CumGOAStart'));
    $table->addRow(makeLink('Number of Genes Receiving GO Annotations Each Month Within Query Range','report3','GWGOStart'));
    $table->addRow(makeLink('Total Number of Genes with GO Annotations Each Month','report3','CumGWGOAStart'));
    /*$table->addRow(makeLink('Number of  Non-IEA GO Annotations Added for Genes Each Month Within Query Range','report3','NUMnIEAAStart'));
    $table->addRow(makeLink('Total Number of non-IEA GO Annotations for Genes Each Month','report3','CumNUMnIEAAStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-IEA GO Annotations Each Month Within Query Range','report3','nIEAAStart'));
    $table->addRow(makeLink('Total Number of Genes With Non-IEA GO Annotations Each Month','report3','CumnIEAAStart'));
    $table->addRow(makeLink('Number of Non-IEA, Non-ISS GO Annotations For Genes Added Each Month Within Query Range','report3','NUMnIEAnISSAStart'));
    $table->addRow(makeLink('Total Number of non-IEA, non-ISS GO Annotations for Genes Each Month','report3','CumNUMnIEAnISSAStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-IEA, Non-ISS GO Annotations Each Month Within Query Range','report3','nIEAnISSAStart'));
    $table->addRow(makeLink('Total Number of Genes with Non-IEA, Non-ISS GO Annotations Each Month','report3','CumnIEAnISSAStart'));*/
    //
    /*$table->addRow(makeLink('Number of Non-[Evidence] GO Annotations for Genes Added Each Month Within Query Range','report3','NotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Non-[Evidence] GO Annotations for Genes Each Month','report3','CumNotEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-[Evidence] GO Annotations Each Month Within Query Range','report3','GWNotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with Non-[Evidence] GO Annotations Each Month','report3','CumGWNotEvidenceStart'));*/
    $table->addRow(makeLink('Number of GO Annotations for Genes by Evidence Added Each Month Within Query Range','report3','EvidenceStart'));
    $table->addRow(makeLink('Total Number of GO Annotations for Genes by Evidence Each Month','report3','CumEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving GO Annotations by Evidence Each Month Within Query Range','report3','GWEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with GO Annotations by Evidence Each Month','report3','CumGWEvidenceStart'));
    //
    $table->addRow(makeLink('Number of MF, BP, and CC GO Annotations for Genes Added Each Month Within Query Range','report3','MBCAStart'));
    $table->addRow(makeLink('Total Number of MF, BP, and CC GO Annotations For Genes Each Month','report3','CumMBCAStart'));
    $table->addRow(makeLink('Number of Genes Receiving MF, BP, and CC GO Annotations each Month','report3','GWMBCAStart'));
    $table->addRow(makeLink('Total Number of Genes with MF, BP, and CC GO Annotations Each Month','report3','CumGWMBCAStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* DO Annotations
*/
function report_DOHome() {
    setSessionVar('AnnotFrom','D%');
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Disease Ontology Annotation Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Number of DO Annotations for Genes Added Each Month Within Query Range', 'report3', 'GOStart'));
    $table->addRow( makeLink('Total Number of DO Annotations for Genes Each Month','report3','CumGOAStart'));
    $table->addRow(makeLink('Number of Genes Receiving DO Annotations Each Month Within Query Range','report3','GWGOStart'));
    $table->addRow(makeLink('Total Number of Genes with DO Annotations Each Month','report3','CumGWGOAStart'));
    /*$table->addRow(makeLink('Number of Non-[Evidence] DO Annotations for Genes Added Each Month Within Query Range','report3','NotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Non-[Evidence] DO Annotations for Genes Each Month','report3','CumNotEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-[Evidence] DO Annotations Each Month Within Query Range','report3','GWNotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with Non-[Evidence] DO Annotations Each Month','report3','CumGWNotEvidenceStart'));*/
    $table->addRow(makeLink('Number of DO Annotations for Genes by Evidence Added Each Month Within Query Range','report3','EvidenceStart'));
    $table->addRow(makeLink('Total Number of DO Annotations for Genes by Evidence Each Month','report3','CumEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving DO Annotations by Evidence Each Month Within Query Range','report3','GWEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with DO Annotations by Evidence Each Month','report3','CumGWEvidenceStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* PW Annotations
*/
function report_PWHome() {
    setSessionVar('AnnotFrom','PW%');
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("PathWay Annotation Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Number of PW Annotations for Genes Added Each Month Within Query Range', 'report3', 'GOStart'));
    $table->addRow( makeLink('Total Number of PW Annotations for Genes Each Month','report3','CumGOAStart'));
    $table->addRow(makeLink('Number of Genes Receiving PW Annotations Each Month Within Query Range','report3','GWGOStart'));
    $table->addRow(makeLink('Total Number of Genes with PW Annotations Each Month','report3','CumGWGOAStart'));
        /*$table->addRow(makeLink('Number of Non-[Evidence] PW Annotations for Genes Added Each Month Within Query Range','report3','NotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Non-[Evidence] PW Annotations for Genes Each Month','report3','CumNotEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-[Evidence] PW Annotations Each Month Within Query Range','report3','GWNotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with Non-[Evidence] PW Annotations Each Month','report3','CumGWNotEvidenceStart'));*/
    $table->addRow(makeLink('Number of PW Annotations for Genes by Evidence Added Each Month Within Query Range','report3','EvidenceStart'));
    $table->addRow(makeLink('Total Number of PW Annotations for Genes by Evidence Each Month','report3','CumEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving PW Annotations by Evidence Each Month Within Query Range','report3','GWEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with PW Annotations by Evidence Each Month','report3','CumGWEvidenceStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* MP Annotations
*/
function report_MPHome() {
    setSessionVar('AnnotFrom','MP%');
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Mammalian Phenotype Annotation Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Number of MP Annotations for Genes Added Each Month Within Query Range', 'report3', 'GOStart'));
    $table->addRow( makeLink('Total Number of MP Annotations for Genes Each Month','report3','CumGOAStart'));
    $table->addRow(makeLink('Number of Genes Receiving MP Annotations Each Month Within Query Range','report3','GWGOStart'));
    $table->addRow(makeLink('Total Number of Genes with MP Annotations Each Month','report3','CumGWGOAStart'));
    /*$table->addRow(makeLink('Number of Non-[Evidence] MP Annotations for Genes Added Each Month Within Query Range','report3','NotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Non-[Evidence] MP Annotations for Genes Each Month','report3','CumNotEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving Non-[Evidence] MP Annotations Each Month Within Query Range','report3','GWNotEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with Non-[Evidence] MP Annotations Each Month','report3','CumGWNotEvidenceStart'));*/
    $table->addRow(makeLink('Number of MP Annotations for Genes by Evidence Added Each Month Within Query Range','report3','EvidenceStart'));
    $table->addRow(makeLink('Total Number of MP Annotations for Genes by Evidence Each Month','report3','CumEvidenceStart'));
    $table->addRow(makeLink('Number of Genes Receiving MP Annotations by Evidence Each Month Within Query Range','report3','GWEvidenceStart'));
    $table->addRow(makeLink('Total Number of Genes with MP Annotations by Evidence Each Month','report3','CumGWEvidenceStart'));
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* References
*/
function report_RefHome()
{
    setPageTitle("Reports");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Reference Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink('Number of References in RGD', 'report3', 'ReferencesStart'));
    $table->addRow( makeLink('Number of References Added Each Month Within Query Range','report3','MonthlyReferencesStart'));
    $table->addRow( makeLink('Total Number of References in RGD Each Month','report3','CumMonthlyReferencesStart'));
    
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* Nomenclature
*/
function report_nomenHome()
{
    setPageTitle("Nomenclature");
    if (!userLoggedIn()) {
        return NOTLOGGEDIN_MSG;
    }
    
    setPageTitle("Nomenclature Reports");
    $table = newTable('Reports');
    $table->setAttributes('class="simple" width="70%"');
    $table->addRow( makeLink("Number of Genes receiving Nomenclature Events Each Month Within Query Range",'report3','NEventStart'));
    $table->addRow( makeLink('Total Number of Genes with Nomenclature Events Each Month','report3','CumNEventStart'));
    //$table->addRow( makeLi'Total Number of References in RGD Each Month','report3','CumMonthlyReferencesStart'));
    
    $toReturn = '<center>' . $table->toHtml();
    return $toReturn;
}
/**
* Generates the HTML page of all gene counts only in HTML, no graphing done.
*/
function report_basicGeneReport() { 
  
  // check to see if we're being called via a link to export to excel. 
  $export = getRequestVarString('export');
   
  if ( $export == 'excel' )  {  
    setDirectOutput(); 
  } else { 
    setPageTitle("Current Gene Reports");
  }
  $totalGenes = fetchRecord("select count (*) as count from genes g ");
  
  // Basic gene Count 
  $referanceResult = fetchRecords("select count (*) as count , r.object_status  as status, r.species_type_key from genes g , rgd_ids r where g.rgd_id = r.rgd_id group by r.object_status,  r.species_type_key");
  $toReturn = '<h2>Current Gene Count for All Types</h2>';
  $table = newTable('Count', 'Gene Status', 'Species');
  $table->setAttributes('class="simple" width="70%"');
  foreach ($referanceResult as $referenceRow) {
    extract($referenceRow);
    $table->addRow( $COUNT, $STATUS, getSpeciesName( $SPECIES_TYPE_KEY) );
  }
  $toReturn .= $table->toHtml();
  $toReturn .= 'Total genes : ' . $totalGenes['COUNT']; 
  
  // Basic Gene Count by type 
  $referanceResult = fetchRecords("select count (*) as count , r.object_status as status , g.gene_type_lc as type from genes g , rgd_ids r where g.rgd_id = r.rgd_id and r.species_type_key = 3 group by r.object_status, g.gene_type_lc order by g.gene_type_lc");
  $toReturn .= '<h2>Current Gene Count By Types for Rattus';
    $toReturn .= '<font size=-2>( ' . makeLink('Export to Excel', 'report', 'basicGeneReport', array ( 'export' => 'excel' ) );
  $toReturn .= ')</h2>';
  $table = newTable('Count',  'Gene Type', 'Gene Status');
  $csv_output = "Count,Gene Type,Gene Status\n";
  $table->setAttributes('class="simple" width="70%"');
  foreach ($referanceResult as $referenceRow) {
    extract($referenceRow);
    $table->addRow( $COUNT,  $TYPE, $STATUS);
    $csv_output .= "$COUNT,$TYPE,$STATUS\n";
  }
  $toReturn .= $table->toHtml();

  generateFooter($toReturn); 
  
  if ( $export ==  'excel'  ) { 
     header("Content-type: text/csv");
     header("Content-disposition: filename=export_" . date("Y-m-d") . ".csv");
    echo $csv_output;
  } else { 
     return $toReturn; 
  }
}

/** 
 * Display the Genes Created for the last 3 years by Type
 * 
 * Queries the database, generates the data which is put into the 
 * session then generates image link back to report_geneCreatedReport() 
 * that displays the actual graph.  If option of species is passed in use that
 * otherwise assume rat. 
 */
function report_geneHistory() {
  $thisDateArray = getdate(); 
  $thisYear = $thisDateArray["year"];
  $species = getRequestVarString('species');
  if ( ! isReallySet( $species ) ) { 
    $species = 3; // Rat by default; 2 = mouse 1 = human from species_types
  }
  $speciesName = getSpeciesName($species ) ; 
  $fullResultArray = array(); 
  // Proces the last 3 years og genes
  for( $procYear = $thisYear; $procYear > ( $thisYear - 3 ) ; $procYear-- ) { 
    $rowArray = array(); 
    // dump ($procYear ) ; 
    $sql = 'select  count(*) as count , g.gene_type_lc   from rgd_ids r, genes g where '; 
    $sql .=' r.created_date >  to_date(\'12/31/'. ( $procYear - 1 ) . '\' , \'MM/DD/YYYY\' )';  
    $sql .=' and r.created_date < to_date(\'1/1/'. ( $procYear + 1 ) . '\' , \'MM/DD/YYYY\' )';
    $sql .=' and r.OBJECT_STATUS = \'ACTIVE\'';
    $sql .=' and r.SPECIES_TYPE_KEY = \''. $species . '\''; 
    $sql .=' and r.rgd_id = g.rgd_id ';
    $sql .=' group by g.gene_type_lc';
    // dump ( $sql ) ; 
    $results = fetchRecords($sql);
    foreach ( $results   as $resultRow ) { 
      extract( $resultRow ) ; 
      $rowArray[$GENE_TYPE_LC] = $COUNT;
      
    }
    $fullResultArray[$procYear] = $rowArray;
   }
   // dump ( $fullResultArray) ; 
   
   // clean up data
   // Get list of all gene types so we can map the ones that 
   // were not obtained that year to zero
   $sql = "select gene_type_lc from gene_types order by gene_type_lc " ; 
   $results = fetchrecords( $sql ) ;
    foreach ( $results as $row ) { 
      extract ( $row );  
      $geneArray[] = $GENE_TYPE_LC;
      $emptyArray[$GENE_TYPE_LC] = 0; 
    } 
    // dump ( $emptyArray ) ; 
  
  // fill in the blank array into our existing array so we have values for each type
   for( $procYear = $thisYear; $procYear > ( $thisYear - 3 ) ; $procYear-- ) { 
      $tmpArray =  $fullResultArray[$procYear] ;
      $yearArray[]= $procYear; 
      $finalArray = $tmpArray + $emptyArray; 
      $finalResultsRow[$procYear] = $finalArray;
      $finalResultArray[$procYear] = $finalResultsRow;  
   }
   //dump ( $finalResultArray ) ; 
   
  $data1 = array();
  $data2= array();
  $data3 = array(); 
  unset ( $tmpArray ) ;
  $fullResultArray = array(); 
  // Process the last 3 years of genes
  $thisYear = $thisDateArray["year"];
  for( $procYear = $thisYear, $count=0; $procYear > ( $thisYear - 3 ) ; $procYear-- , $count++) { 
    $tmpArray = $finalResultArray[$procYear][$procYear];
   //dump ( $tmpArray) ; 
    foreach ($geneArray as $geneType ) {
      switch( $count ) { 
        case 0: 
            $data1[] = $tmpArray[$geneType]; 
          break;
        case 1:
            $data2[] = $tmpArray[$geneType]; 
          break;
        case 2 :
            $data3[] = $tmpArray[$geneType]; 
      }
    }
    unset ( $tmpArray ) ; 
  }
   setSessionVar('data1', $data1);
   setSessionVar('data2', $data2);
   setSessionVar('data3', $data3);
   setSessionVar('datax', $geneArray);
   setSessionVar('yearArray',  $yearArray);
   $rand = rand(1, 10000); // Need to stick this in to get aroound image cacheing on browser. 
   return '<h2>' . $speciesName . ' Gene Types Created for each of the Last 3 Years:<br> <img src="' . makeUrl('report', 'geneCreatedReport' , "random=". $rand  ) . '" > ';    

}
/** 
 * Display the Genes Created for the last 3 years by Type
 * OUTPUTS the Graph only
 */
function report_geneCreatedReport() { 
  include_once ("jpgraph_bar.php");
  setDirectOutput();
  $data1 = getSessionVar('data1');
  $data2 = getSessionVar('data2');
  $data3 = getSessionVar('data3');
  $datax = getSessionVar('datax');
  $yearArray = getSessionVar('yearArray');
  
  // Create the graph. These two calls are always required
  $graph = new Graph(600,400,"auto"); 
  $graph->SetScale("textlin");
  $graph->xaxis->SetTickLabels($datax);
  $graph->xaxis->SetLabelAngle(90);
  $graph->yaxis->SetLabelAngle(90);
  $graph->SetMargin(40,20,30,150);
  $graph->SetShadow();
  
  // Create the bar plots
  $b1plot = new BarPlot($data1);
  $b1plot->SetFillColor("orange");
  $b1plot->SetValuePos('top');
  $b1plot->value->SetFormat('%d');$b1plot->value->Show();
  
  $b2plot = new BarPlot($data2);
  $b2plot->SetFillColor("blue");
  $b2plot->value->Show();
  
  $b3plot = new BarPlot($data3);
  $b3plot->SetFillColor("red");
  $b3plot->value->Show();
  // Set Legend
  $b1plot->SetLegend($yearArray[0]);
  $b2plot->SetLegend($yearArray[1]);
  $b3plot->SetLegend($yearArray[2]);
  
  // Create the grouped bar plot
  $gbplot = new GroupBarPlot (array($b3plot, $b2plot,$b1plot));
  
  // ...and add it to the graPH
  $graph->Add($gbplot);
  // $graph->SetShadow();
  //  $graph->title->Set("Rat Genes Created Per Year");
  //  $graph->xaxis->title->Set("Gene Types Created");
  $graph->yaxis->title->Set("Totals per year");
  $graph->SetMarginColor('white');
  $graph->SetFrame(false);
  $graph->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
  
  // Display the graph
   $graph->Stroke();
}


function report_geneHistoryByMonth() {
  
  if (!userLoggedIn()) {
    return NOACCESS_MSG;
  }

  
  $userKey = getSessionVar('userKey') ;
  $toReturn = '<p></p>';
 
  $theForm = newForm('Select date', 'POST', 'report', 'geneHistoryByMonth'); 
  $theForm->addSelect('month', 'Show Annotations for the following month:', getLastMonths(), 1, false);
  $theForm->setDefault('month', 'c');
  $theForm->getState();
    
   if ( $theForm->getValue('month') == null ) {
    $theForm->setDefault('month', 0);
   }
  $toReturn .= $theForm->quickRender();
  $startMonthDate = getMonthStartDate($theForm->getValue('month')); 
  $endMonthDate = getMonthEndDate($theForm->getValue('month'));
  $toReturn .= "Showing All Annotations from ". $startMonthDate ." to " . $endMonthDate . "<br>";
  $dateArray = getMonthArray($startMonthDate, $endMonthDate); 
  foreach ( $dateArray as $key => $processDate ) { 
    $sql = 'select count(*) as mycount from full_annot where created_date between  to_date( \''. $processDate. '\', \'MM-DD-YYYY\' ) and to_date( \''. $processDate. '\', \'MM-DD-YYYY\' ) + 1';
    $result = fetchRecord($sql);
    // $toReturn .= "Date ". $processDate . " : " . $result['MYCOUNT'] . '<BR>'; 

    $plotData[] = $result['MYCOUNT']; 
    $dateData[] = $processDate; 
  } 
  // $plotData  = array(11,3, 8,12,5 ,1,9, 13,5,7 ); 
  // dump ( $plotData ) ; 
  // dump ( $dateData ); 
  setSessionVar('data1', $plotData);
  setSessionVar('datax', $dateData);
   return $toReturn . '<h2>Annotations Created for the Selected Month<br> <img src="' . makeUrl('report', 'geneMonthlyCreatedReport' ). '" > ';  
    
}
function report_myGeneHistoryByMonth() {
  
  if (!userLoggedIn()) {
    return NOACCESS_MSG;
  }

  
  $userKey = getSessionVar('userKey') ;
  $userName = getSessionvar('userFullName');  
  $toReturn = '<p></p>';
 
  $theForm = newForm('Select date', 'POST', 'report', 'myGeneHistoryByMonth'); 
  $theForm->addSelect('month', 'Show Annotations for the following month:', getLastMonths(), 1, false);
  $theForm->setDefault('month', 'c');
  $theForm->getState();
    
   if ( $theForm->getValue('month') == null ) {
    $theForm->setDefault('month', 0);
   }
  $toReturn .= $theForm->quickRender();
  $startMonthDate = getMonthStartDate($theForm->getValue('month')); 
  $endMonthDate = getMonthEndDate($theForm->getValue('month'));
  $toReturn .= "Showing All Annotations from ". $startMonthDate ." to " . $endMonthDate . "<br>";
  $dateArray = getMonthArray($startMonthDate, $endMonthDate); 
  foreach ( $dateArray as $key => $processDate ) { 
    $sql = 'select count(*) as mycount from full_annot where last_modified_by = '.$userKey . ' and created_date between  to_date( \''. $processDate. '\', \'MM-DD-YYYY\' ) and to_date( \''. $processDate. '\', \'MM-DD-YYYY\' ) + 1';
    // echo $sql; 
    $result = fetchRecord($sql);
    // $toReturn .= "Date ". $processDate . " : " . $result['MYCOUNT'] . '<BR>'; 

    $plotData[] = $result['MYCOUNT']; 
    $dateData[] = $processDate; 
  } 
  // $plotData  = array(11,3, 8,12,5 ,1,9, 13,5,7 ); 
  // dump ( $plotData ) ; 
  // dump ( $dateData ); 
  setSessionVar('data1', $plotData);
  setSessionVar('datax', $dateData);
   return $toReturn . '<h2>Annotations of ' . $userName . ' Created in the Selected Month<br> <img src="' . makeUrl('report', 'geneMonthlyCreatedReport' ). '" > ';  
    
}
/**
 * 
 */
function report_geneMonthlyCreatedReport() { 
  include_once ("jpgraph_bar.php");
  setDirectOutput();
   $data1 = getSessionVar('data1');
   $datax = getSessionVar('datax');
  // $yearArray = getSessionVar('yearArray');

  
    
 
  // Create the graph. These two calls are always required
  $graph  = new Graph(600, 350,"auto");    
  $graph->SetScale( "textlin");
  $graph->xaxis->SetTickLabels($datax);
  $graph->xaxis->SetTextLabelInterval(3);
  $graph->xaxis->HideTicks();
  $graph->xaxis->SetLabelAngle(90);
  $graph->yaxis->SetLabelAngle(90);
  // Create the linear plot
  $b1plot = new BarPlot($data1);
  $b1plot->SetFillColor("blue");
  $b1plot->value->SetFormat('%d');$b1plot->value->Show();
  $b1plot->value->HideZero(); 
 
  // Add the plot to the graph
  $graph->Add( $b1plot);
  //$graph->title->Set("Total FULL_ANNOT Annotations Created");
  $graph->SetMarginColor('white');
  $graph->SetFrame(false);
  //  $graph->xaxis->title->Set("Gene Types Created");
  $graph->yaxis->title->Set("Totals per day");
  $graph->img->SetMargin(40,40,40,80);
  $graph->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

  // Display the graph
  $graph->Stroke(); 
 
}
//
// Common foorter for all reports goes here
//
function generateFooter(&$toReturn ) {
  $today = date("F j, Y, g:i a"); 
  $toReturn .= "<p>This Page Generated: $today</P>";
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
 
 // Return month array of dates to report on for example: 01-01-2005 to 01-31-2005
 function getMonthArray( $startDate, $endDate ) { 
     
    $returnArray = array( ) ;
    $processDateStr = $startDate; 
    $returnArray[] = $processDateStr; 
    $mcount = 0;
    while (  $processDateStr !== $endDate ) { 
       $sql = ' select TO_CHAR ( to_date (  \''. $processDateStr . "'  , 'MM-DD-YYYY' ) + 1 , 'MM-DD-YYYY')  as MYDATE from dual ";
        $result = fetchRecord($sql); 
        $returnArray[] = $result['MYDATE']; 
        $processDateStr = $result['MYDATE'];
        if ( $mcount > 40 ) break; 
        $mcount++;  
    }
    return $returnArray; 
  

 }

/**
 * creates a table with the first dropdown and second dropdown, populates the first
 */
function report_start() {
  $menuArray = array();
  if (!userLoggedIn()) {
    return NOACCESS_MSG;
  }
  $menuArray[] = "Choose below:";
  $reports = fetchRecords('select distinct SUBSYSTEM_NAME from REPORT_PROCESS_TYPES');
  foreach ( $reports   as $resultRow ) { 
  extract( $resultRow ) ; 
  $menuArray[$SUBSYSTEM_NAME] = $SUBSYSTEM_NAME;    
  }
  $toReturn = '';
  $table = newTable('SUBSYSTEM', 'PROCESS TYPE');
  $table->addRow(makeAjaxSelect(makeUrl('report','subsysNameChosen'), $menuArray, '', 'selectDivArea2', 'thevalue') , '<div id="selectDivArea2"><select><option>Please select subsystem</option></select></div>');
 $toReturn =  '<h2> Pipeline Processes: </h2> ';  
  return $toReturn . $table->toHtml().'</br></br><div id="selectDivArea3">AWAITING CHOICE</div>';
    }
/**
 * Second AJAX dropdown
 */
 function report_subsysNameChosen() {
  setDirectOutput();
  $menuArray2 = array();
  $valueSelected = getRequestVarString('thevalue');
  $reportQuery = 'select distinct RPT_PROCESS_TYPE_ID, RPT_PROCESS_TYPE_DESC from REPORT_PROCESS_TYPES where SUBSYSTEM_NAME = \'';
  $reportQuery .= $valueSelected;
  $reportQuery .= '\'';
  $reports = fetchRecords($reportQuery);
  $menuArray2[0] = "Choose a Process Type:";
  foreach ( $reports   as $resultRow ) { 
  extract( $resultRow ) ; 
  $menuArray2[$RPT_PROCESS_TYPE_ID] = $RPT_PROCESS_TYPE_DESC;}
  
  $values = array();
  
  //echo makeAjaxSelect(makeUrl('report','procTypeDescChosen'), $menuArray2, '', 'selectDivArea3', 'thevalue');
echo makeAjaxSelect(makeUrl('report','makeImageLink'), $menuArray2, '', 'selectDivArea3', 'thevalue');
}
/**
 * Makes the graph show up as an image - otherwise it just shows up as a big text string.  Also creates links to export things as CSVs.
 */
function report_makeImageLink(){
  setDirectOutput();
    $valueSelected = getRequestVarString('thevalue');
    if ( $valueSelected != 0 ){
    echo "<img src = ".(makeUrl('report2','procTypeDescChosen', array('theValue' => $valueSelected))) .">";
    echo "</br></br>".makeLink('Export ALL data as CSV', 'report', 'dataToCSV2', array('theValue' => $valueSelected, 'type'=>'all'));//, array('type' => 'all'));
    echo "</br></br>".makeLink('Export last 90 days as CSV', 'report', 'dataToCSV2', array('theValue' => $valueSelected, 'type'=>'90'));//, array('type' => '90'));
    }
}
/**
 * Actually does the exporting to CSVs.  Depending on the link clicked, will either export the full set of values or just the what the graph shows.
 */
function report_dataToCSV2() {
  setDirectOutput();
      $valueSelected = getRequestVarString('theValue');
      $typeSelected = getRequestVarString('type');
      if ( $typeSelected == 'all'){
      $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD\') as CDATE,  EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' order by CDATE ASC';}
      else {
        $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD HH24:MI:SS\') as CDATE, EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' and CREATED_DATE between (sysdate - 90) and sysdate order by CDATE ASC';
        
      }
      $results = fetchRecords($dataRequest);
    //$table = newTable('Count',  'Gene Type', 'Gene Status');
    $csv_output = "Extract Value,Date\n";
   // $table->setAttributes('class="simple" width="70%"');
    foreach ($results as $referenceRow) {
    extract($referenceRow);
   // $table->addRow( $RPT_PROCESS_TYPE_ID,  $EXTRACT_VALUE, $CDATE);
    $csv_output .= "$EXTRACT_VALUE,$CDATE\n";
    }
  //$toReturn .= $table->toHtml();

 //generateFooter($toReturn); 
  
     header("Content-type: text/csv");
     header("Content-disposition: filename=" . $valueSelected ."_export_" . date("Y-m-d") . ".csv");
     echo $csv_output;
}


/**
 * Takes the selected process type and spits out a graph of the data for the last 90 days.
 */
function report_procTypeDescChosen() {
 
  setDirectOutput();
  $graphArray = array();
  $dateArray = array();
  $yData = array();

  $valueSelected = getRequestVarString('theValue');

  $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD HH24:MI:SS\') as CDATE, EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' and CREATED_DATE between (sysdate - 90) and sysdate order by CDATE ASC'; //sql string, gets all selected data for the last 90 days in a PHP-friendly date format

  $reports = fetchRecords($dataRequest);

  $dateCheck = array();
  foreach ( $reports   as $resultRow ) { //get the data into a usable format
  extract( $resultRow ) ; 
  $graphArray[$CDATE] = $EXTRACT_VALUE;
  $temp = strtotime( $CDATE );
  $dateArray[] = $temp;
  $yData[] = $EXTRACT_VALUE;

  }

  
  if (count($yData) > 2){  //JPgraph crashes if there's only two values and it tries to autoscale, but autoscale is too useful to ditch altogether
  $graph  = new Graph(650, 460,"auto"); 
  $graph->SetMargin(50, 20, 30, 70);   

  $graph->SetScale( 'intlin');
  $graph->xaxis->SetLabelAngle(90);
// Create the linear plot
$graph->title->Set('Data for the Last 90 Days');
$lineplot =new LinePlot($yData, $dateArray);
$lineplot->mark->SetType(MARK_UTRIANGLE);
//$lineplot->value->Show();  //makes values show up above the points - useful, but it gets cluttered quickly
$lineplot ->SetColor("red");
$lineplot ->SetWeight(3);
$graph->SetMarginColor("cornflowerblue");
 $graph->xaxis->SetLabelFormatString('M-d-y', true);
// Add the plot to the graph
$graph->Add( $lineplot);

// Display the graph
$graph->Stroke(); 
 }  
 else {
   $graph  = new Graph(650, 460,"auto"); 
  $graph->SetMargin(50, 20, 30, 70);   
$graph->title->Set('Data for the Last 90 Days');
  $graph->SetScale( 'intlin',0,0,($dateArray[0] - 80000), ($dateArray[1] + 80000) ); //manually set the scale
  $graph->xaxis->SetLabelAngle(90);
// Create the linear plot
$lineplot =new LinePlot($yData, $dateArray);
$lineplot->mark->SetType(MARK_UTRIANGLE);
//$lineplot->value->Show();
$lineplot ->SetColor("red");
$lineplot ->SetWeight(3);
$graph->SetMarginColor("cornflowerblue");
 $graph->xaxis->SetLabelFormatString('M-d-y', true);
// Add the plot to the graph
$graph->Add( $lineplot);

// Display the graph
$graph->Stroke();   
 }
   


}

function report_setColor($form='')
{
    //echo 'hi';
    //$done=getSessionVarOKEmpty('done');
    $toReturn=$form;
    //echo 'hi';
    $colors=getColorArray();
    $req=0;
    if ($form=='')
    {
        $req=1;
    }
    $default=false;
    $defaulte='<h2><font color="';
  $theForm = newForm('Submit', 'POST', 'report', 'setColor'); 
  $theForm->addSelect('color','Color',$colors,1,false);
  $col=getSessionVarOKEmpty('Color');
  $gra=getSessionVarOKEmpty('GraphColor');
  //echo $col;
  if (isset($col))
  {
    $theForm->setDefault('color',$col);
    $defaulte.=$gra.'">';
  }
  else
  {
    $theForm->setDefault('color','5');
    $defaulte.='blue">';
  }
  $defaulte.='CURRENT</font></h2>';
  switch ($theForm->getState()) {
        case INITIAL_GET :          
                setPageTitle('Set Default Color');
                setSessionVar('done',0);
                //echo 'hi';
                $default=true;
        case SUBMIT_INVALID :
            if ($form=='')
            {
                $toReturn .= $theForm->quickRender();
            }
            //echo 'hi';
            break;
        case SUBMIT_VALID :
            //$
            //setPageTitle('Color Set: '
            //echo 'hi';
            //echo 'hi<br>';
            //$toReturn.=$theForm->quickRender();
                $color=getRequestVarString('color');
                //echo 'hi<br>';
                //echo 'hi';
            if ($form=='')
            {
                setSessionVar('Color',$color);
            }
            //$colors=getColorArray();
            //echo 'hi';
            if ($color!='9'&&$form=='')
            {
                //echo 'hi';
                //echo 'hi9<br>';
                setSessionVar('GraphColor',$colors[$color]);
                $toReturn.=$theForm->quickRender();
                //$toReturn.='</br></br>Color set to <font color="'.$colors[$color].'">'.$colors[$color].'</font>';
                $toReturn.='</br></br>Color set to '.$colors[$color];
                $toReturn.='</br></br><h1><font color="'.$colors[$color].'">PREVIEW</font></h1>';
            }
            elseif ($form=='')
            {
                /*echo 'hicall<br>';
                echo $color;
                echo $form.'3';
                //echo (boolean)(2=='9');
                if ($color=='9')
                {
                    echo "$color='9'";
                }
                else if ($form!*/
                //echo 'bye';
                //$daForm='r';
                //while ($daForm!='')
                //{
                    /*$daForm=*/
                    //while (true)
                    //{ 
                        $toReturn.=callFunc('report','setHexColore');
                    //}
               // }
            }
            //echo 'hi<br>';
            //echo 'hi';
            //echo $toReturn;
            /*if (!isset($done))
            {
                $toReturn.=$defaulte;
            }*/
            if ($default)
            {
                $toReturn.=$defaulte;
            }
            if ($form!='')
            {
                $toReturn.='</br></br><a href="http://www.asahi-net.or.jp/~FX6M-FJMY/java09e.html" target="_blank">HTML COLOR PICKER</a>';
            }
            return $toReturn;
            //make here 
            break;
    }
   // echo $toReturn;
   //echo 'hi';
   /*if (!isset($done))
   {
        $toReturn.=$defaulte;
   }*/
   if ($default)
   {
        $toReturn.=$defaulte;
   }
    return $toReturn;
  //$toReturn.='Nathan Rocks!';
  //required fields are not requiring
  //return $toReturn;

}
/*function report_setHexColore()
{
    //echo 'hi';
    return callFunc('report','setHexColor');
    //echo 'hi';
}*/
function report_setHexColore()
{
    setSessionVar('cont',1);
    return callFunc('report','setHexColor');
}
function report_setHexColor($text=null)
{
    //if ($tex
    //echo 'hi';
    $toReturn='';
    //$cole=getSessionVarOKEmpty('Color');
    $gcol=getSessionVarOKEmpty('GraphColor');
    if (isset($text))
    {
         $toReturn.="<h3>$text\n\n</h3>";
         
    }
    $theForm = newForm('Submit', 'POST', 'report', 'setHexColor'); 
    $theForm->addText('hexcolor','Color (hex)<font color="red">*</font>',6,6,0);
    if (isset($gcol)&&strlen($gcol)==7)
    {
        $theForm->setDefault('hexcolor',substr($gcol,1,6));
    }
    //$theForm->quickRender();
    $bob=$theForm->getState();
    $cont=getSessionVar('cont');
    $done=getSessionVar('done');
    if ($cont==1&&isset($text))
    {
        $bob=SUBMIT_INVALID;
    }
    elseif ($cont==1&&$done==0)
    {
        $bob=INITIAL_GET;
    }
    elseif ($cont==2)
    {
        setSessionVar('cont',1);
    }
    if ($cont==1)
    {
        setSessionVar('cont',2);
    }
    //setSession
    switch ($bob) {
        case INITIAL_GET :          
                setPageTitle('Set Default Color');
                setSessionVar('done',1);
                //echo 'hi';
        case SUBMIT_INVALID :
            $toReturn .= $theForm->quickRender();
            //echo '</br>hi';
            return report_setColor($toReturn);
            break;
        case SUBMIT_VALID :
            //echo 'hi';
            //setSessionVar('cont',2);
            $hex=getRequestVarString('hexcolor');
            $hex=strtoupper($hex);
            //echo $hex;
            if (strlen($hex)!=6)
            {
                //echo 'hi';
                setSessionVar('cont',1);
                return report_setHexColor('Color must be six hexagecimal digits');
                //echo 'hi';
            }
            else
            {
                $hexarray=getHexArray();
                for ($i=0;$i<6;$i++)
                {
                    $works=false;
                    foreach ($hexarray as $element)
                    {
                        if ($element==$hex[$i])
                        {
                            $works=true;
                            break;
                        }
                        /*if (!$works)
                        {
                            break;
                        }*/
                    }
                    if (!$works)
                    {
                        break;
                    }
                }
                //echo 'hi';
                if (!$works)
                {
                    //echo $hex[$i];
                    setSessionVar('cont',1);
                    return report_setHexColor('All characters must be valid hexagecimal digits');
                }
                else
                {
                    setSessionVar('GraphColor',"#$hex");
                }
                $toReturn.=$theForm->quickRender();
                //return $toReturn;
                return report_setColor($toReturn."</br></br>Color set to #$hex".'</br></br><h1><font color="#'.$hex.'">PREVIEW</font></h1>');
                //make here 
            }
            break;
    }
    //echo $toReturn;
    $cont=getSessionVar('cont');
    if ($cont==1)
    {
        return report_setColor();
    }
    else
    {
        return $toReturn.'</br></br><a href="http://www.asahi-net.or.jp/~FX6M-FJMY/java09e.html">HTML COLOR PICKER</a>';
    }
}
function getColorArray()
{
    return array('1'=>'red','2'=>'orange','3'=>'yellow','4'=>'green','5'=>'blue','6'=>'purple','7'=>'black','8'=>'white','9'=>'custom');
}
function getHexArray()
{
    return array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F');
}
?>