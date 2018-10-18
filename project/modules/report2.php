<?php
function report2_procTypeDescChosen() {
  //include_once ("jpgraph_date.php");
  include_once ("jpgraph.php");
  //include_once ("jpgraph_line.php");
  include_once ("jpgraph_line.php");
  setDirectOutput();
  $graphArray = array();
  $dateArray = array();
  $yData = array();
  //echo "hello world </br>";

  $valueSelected = getRequestVarString('theValue');
 // echo $valueSelected;
  //return;
//  $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD HH24:MI:SS\') as CDATE, EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' order by CDATE ASC';

  $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD HH24:MI:SS\') as CDATE, EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' and CREATED_DATE between (sysdate - 91) and sysdate order by CDATE ASC';
  //echo "before fetch </br>";
  $reports = fetchRecords($dataRequest);
 // echo "fetched data</br>";
  //return;
  $dateCheck = array();
  foreach ( $reports   as $resultRow ) { 
  extract( $resultRow ) ; 
  $graphArray[$CDATE] = $EXTRACT_VALUE;
  $temp = strtotime( $CDATE );
  $dateCheck[] =strtotime($CDATE);
  $dateArray[] = $temp;
  $yData[] = $EXTRACT_VALUE;
  //echo "gettin' data ";
  }
 // var_dump($yData);
 // echo "</br></br>";
  //var_dump($dateArray);
  //return;
 //echo 'hello';
  //if (count($yData) > 2){  //JPgraph crashes if there's only two values and it tries to autoscale, but autoscale is too useful to ditch altogether
  $graph  = new Graph(650, 460,"auto"); 
  $graph->SetMargin(50, 20, 30, 70);   
  //$graph->title->Set('Data for the Last 90 Days');
    //$graph->setScale('linlin');
  $graph->SetScale( 'linlin',0,0,gettimeofday(true)-(91*86400),gettimeofday(true)+1);
      //$graph->xaxis->scale->auto_ticks=false;
      $tickpos=array();
      for ($i=0;$i<14;$i++)
      {
        $tickpos[]=gettimeofday(true)-(91*86400)+(7*86400*$i);
      }
      $graph->xaxis->scale->ticks->SetMajTickPositions($tickpos);
  $graph->xaxis->SetLabelAngle(90);
  /*$dateArray=array();
  $timestamp=gettimeofday(true);
  for ($i=0;$i<14;$i++)
  {
      $dateArray[$i]=$timestamp;
      $timestamp-=(86400*7);
  }
  $formattedDateArray=array();
  for ($i=0;$i<14;$i++)
  {
      $formattedDateArray[$i]=date('m-d-Y',$dateArray[$i]);
  }
  $graph->xaxis->SetTickLabels($formattedDateArray);*/
    $graph->xaxis->SetTextLabelInterval(1);
// Create the linear plot
$graph->title->Set('Data for the Last 90 Days');
$lineplot =new LinePlot($yData, $dateArray);
$lineplot->mark->SetType(MARK_UTRIANGLE);
//$lineplot->value->Show();  //makes values show up above the points - useful, but it gets cluttered quickly
$lineplot ->SetColor(setGraphDefaults());
$lineplot ->SetWeight(3);
$graph->SetMarginColor("cornflowerblue");
    //$graph->xaxis->HideTicks();
 $graph->xaxis->SetLabelFormatString('M-d-y', true);
// Add the plot to the graph
$graph->Add( $lineplot);
//echo 'hi';
// Display the graph
$graph->Stroke(); 
 //}  
 /*else {//try IPI--total loaded into
   $graph  = new Graph(650, 460,"auto"); 
  $graph->SetMargin(50, 20, 30, 70);   
$graph->title->Set('Data for the Last 90 Days');
  $graph->SetScale( 'linlin',0,0,($dateArray[0] - 80000), ($dateArray[1] + 80000) ); //manually set the scale
  $graph->xaxis->SetLabelAngle(90);
  /*$dateArray=array();
  $timestamp=gettimeofday(true);
  for ($i=0;$i<14;$i++)
  {
      $dateArray[$i]=$timestamp;
      $timestamp-=(86400*7);
  }
  $formattedDateArray=array();
  for ($i=0;$i<14;$i++)
  {
      $formattedDateArray[$i]=date('m-d-Y',$dateArray[$i]);
  }
  $graph->xaxis->SetTickLabels($formattedDateArray);*/
    /*$graph->xaxis->SetTextLabelInterval(1);
// Create the linear plot
$lineplot =new LinePlot($yData, $dateArray);
$lineplot->mark->SetType(MARK_UTRIANGLE);
//$lineplot->value->Show();
$lineplot ->SetColor("red");
$lineplot ->SetWeight(3);
$graph->SetMarginColor("cornflowerblue");
    //$graph->xaxis->HideTicks();
    $graph->xaxis->scale->ticks->Set(14);
 $graph->xaxis->SetLabelFormatString('M-d-y', true);
// Add the plot to the graph
$graph->Add( $lineplot);
//echo 'bye';
// Display the graph
$graph->Stroke();   
 }*/

}


?>
