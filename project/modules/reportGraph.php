<?php
function reportGraph_graphPrep()
{
    $tackon=getRequestVarString('tackon');
    //$funct=getRequestVarString('funct');
    //setDirectOutput();
    /*if ($funct=='CumgetReport'||$funct=='getReport')
    {
        $funct($tackon);
    }*/
    include_once ("jpgraph.php");
    include_once ("jpgraph_bar.php");
    setDirectOutput();
    //include('jpgraph_bar.php');
    $plotArray=getSessionVar("{$tackon}data");
    $datArray=getSessionVar("{$tackon}Dates");
    $color=setGraphDefaults();
    $num=count($datArray);
    $dateArray=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $dateArray[$i]=$datArray[$i];
    }
    //if (count($plotArray) > 2){
    $graph  = new Graph(650, 460,"auto");
    //echo "graph created\n";
    $graph->SetScale("textlin");
    //echo "scale set to text";
    $formattedDateArray=array();
    //86400 seconds in a day
    for ($i=0;$i<$num-1;$i++)
    {
        $formattedDateArray[$i]=date('M',$dateArray[$i]);
        $onlyOne=false;
        if ($formattedDateArray[$i]==='Jan'&&!($i==0&&date('j',$dateArray[0])!=1)&&!($i==$num-2&&date('j',$datArray[$i+1])!=31))
        {
            $formattedDateArray[$i].="\n".date('Y',$dateArray[$i]);
        }
        else if ($i==0&&date('j',$dateArray[0])==1&&date('n',$dateArray[0])!=1)
        {
            $formattedDateArray[$i].="\n".date('Y',$dateArray[$i]);
        }
        else if ($i==$num-2&&date('j',$datArray[$i+1]+86400)==1&&date('n',$dateArray[0])!=1&&$i!=0)
        {
            $formattedDateArray[$i].="\n".date('Y',$datArray[$i+1]);
        }
        if ($i==0&&date('j',$dateArray[0])!=1)
        {
            $formattedDateArray[0].=' '.date('Y',$dateArray[0])."\n  from\nthe ".date('jS',$dateArray[0]);
            $onlyOne=true;
        }
        if ($i==$num-2&&date('j',$datArray[$i+1]+86400)!=1)
        {
            if (!$onlyOne)
            {
                $formattedDateArray[$i].=' '.date('Y',$datArray[$i+1])."\n until\nthe ".date('jS',$datArray[$i+1]);
            }
            else
            {
                $formattedDateArray[$i].="\n until\nthe ".date('jS',$datArray[$i+1]);
            }
        }
    }
    $graph->xaxis->SetTickLabels($formattedDateArray);
    $graph->xaxis->SetTextLabelInterval(1);
    $graph->xaxis->HideTicks();
    //$graph->xaxis->SetLabelAngle(90);
    $graph->yaxis->SetLabelAngle(90);
    // Create the linear plot
    $b1plot = new BarPlot($plotArray);
    //$b1plot->SetFillColor("#CC0033");
    $b1plot->setFillColor($color);
    $b1plot->value->SetFormat('%d');$b1plot->value->Show();
    $b1plot->value->HideZero();
    
    // Add the plot to the graph
    $graph->Add( $b1plot);
    //$graph->title->Set("Total FULL_ANNOT Annotations Created");
    $graph->SetMarginColor('cornflowerblue');
    $graph->SetFrame(false);
    //  $graph->xaxis->title->Set("Gene Types Created");
    $graph->yaxis->title->Set("Totals per month");
    $graph->img->SetMargin(40,40,40,80);
    //$title=getSessionVar("{$tackon}Title");
    $graph->title->Set(getSessionVar("{$tackon}Title"));
    $graph->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
    // Create the linear plot
    /*$lineplot =new LinePlot($plotArray, $dateArray);
    $lineplot->mark->SetType(MARK_UTRIANGLE);
    //$lineplot->value->Show();
    $lineplot ->SetColor("red");
    $lineplot ->SetWeight(3);*/
    // Add the plot to the graph
    //$graph->Add( $lineplot);
    //$graph->xaxis->SetLabelFormatString('M-d-y', true);
    
    // Display the graph
    $graph->Stroke();
    //}
}
function reportGraph_CSVPrep()
{
    $tackon=getRequestVarString('tackon');
    //$funct=getRequestVarString('funct');
    //setDirectOutput();
    setDirectOutput();
    $reportGraph=getSessionVar("{$tackon}rept");
    $plotArray=getSessionVar("{$tackon}data");
    $dateArray=getSessionVar("{$tackon}Dates");
    $title=getSessionVar("{$tackon}Title");
    header("Content-type: text/csv");
    header("Content-disposition: filename={$reportGraph}_export_" . date("Y-m-d") . ".csv");
    /*$valueSelected = getRequestVarString('theValue');
    $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD\') as CDATE,  EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' order by CDATE ASC';
    $results = fetchRecords($dataRequest);*/
    //$table = newTable('Count',  'Gene Type', 'Gene Status');
    echo "$title\nNumber of Results,Date\n";
    // $table->setAttributes('class="simple" width="70%"');
    /*foreach ($results as $referenceRow) {
        extract($referenceRow);*/
    // $table->addRow( $RPT_PROCESS_TYPE_ID,  $EXTRACT_VALUE, $CDATE);
    /*$num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo "$plotArray[$i],".date('m/d/Y',$dateArray[$i]);
        echo "\n";
    }*/
    foreach ($plotArray as $i=>$plot)
    {
        echo "$plot,".date('m/d/Y',$dateArray[$i]);
        echo "\n";
    }
    echo "\nFinal Date=,".date('m/d/Y',$dateArray[count($plotArray)]);
    //}
    //$toReturn .= $table->toHtml();
    
    //generateFooter($toReturn);
    
    //echo $csv_output;
}
function reportGraph_CumgraphPrep()
{
    $tackon=getRequestVarString('tackon');
    //$funct=getRequestVarString('funct');
    //setDirectOutput();
    /*if ($funct=='CumgetReport'||$funct=='getReport')
    {
        $funct($tackon);
    }*/
    include_once("jpgraph.php");
    include_once ("jpgraph_bar.php");
    setDirectOutput();
    //include('jpgraph_bar.php');
    $plotArray=getSessionVar("{$tackon}data");
    $datArray=getSessionVar("{$tackon}Dates");
    $color=setGraphDefaults();
    $num=count($datArray);
    $dateArray=array();
    for ($i=0;$i<$num;$i++)
    {
        $dateArray[$i]=$datArray[$i];
    }
    //if (count($plotArray) > 2){
    $graph  = new Graph(650, 460,"auto");
    //echo "graph created\n";
    $graph->SetScale("textlin");
    //echo "scale set to text";
    $formattedDateArray=array();
    for ($i=0;$i<$num;$i++)
    {
        $formattedDateArray[$i]=date("M\njS",$dateArray[$i]);
        if ($i===0||$i===$num-1||date('n',$dateArray[$i])==='1')
        {
            $formattedDateArray[$i].="\n".date('Y',$dateArray[$i]);
        }
    }
    //86400 seconds in a day
    $graph->xaxis->SetTickLabels($formattedDateArray);
    $graph->xaxis->SetTextLabelInterval(1);
    $graph->xaxis->HideTicks();
    //$graph->xaxis->SetLabelAngle(90);
    $graph->yaxis->SetLabelAngle(90);
    // Create the linear plot
    $b1plot = new BarPlot($plotArray);
    //$b1plot->SetFillColor("#CC0033");
    $b1plot->setFillColor($color);
    $b1plot->value->SetFormat('%d');
    $b1plot->value->Show();
    $b1plot->value->HideZero();
    
    // Add the plot to the graph
    $graph->Add( $b1plot);
    //$graph->title->Set("Total FULL_ANNOT Ontology Annotations Created");
    $graph->SetMarginColor('cornflowerblue');
    $graph->SetFrame(false);
    //  $graph->xaxis->title->Set("Gene Types Created");
    $graph->yaxis->title->Set("Totals per month");
    $graph->img->SetMargin(40,40,40,80);
    //$title=getSessionVar("{$tackon}Title");
    $graph->title->Set(getSessionVar("{$tackon}Title"));
    $graph->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
    // Create the linear plot
    /*$lineplot =new LinePlot($plotArray, $dateArray);
    $lineplot->mark->SetType(MARK_UTRIANGLE);
    //$lineplot->value->Show();
    $lineplot ->SetColor("red");
    $lineplot ->SetWeight(3);*/
    // Add the plot to the graph
    //$graph->Add( $lineplot);
    //$graph->xaxis->SetLabelFormatString('M-d-y', true);
    
    // Display the graph
    $graph->Stroke();
    //}
}
function reportGraph_CumCSVPrep()
{
    $tackon=getRequestVarString('tackon');
    //$funct=getRequestVarString('funct');
    //setDirectOutput();
    setDirectOutput();
    $reportGraph=getSessionVar("{$tackon}rept");
    $plotArray=getSessionVar("{$tackon}data");
    $dateArray=getSessionVar("{$tackon}Dates");
    $title=getSessionVar("{$tackon}Title");
    header("Content-type: text/csv");
    header("Content-disposition: filename={$reportGraph}_export_" . date("Y-m-d") . ".csv");
    /*$valueSelected = getRequestVarString('theValue');
    $dataRequest = 'select to_char(CREATED_DATE, \'YYYY-MM-DD\') as CDATE,  EXTRACT_VALUE from REPORT_EXTRACTS where RPT_PROCESS_TYPE_ID = '.$valueSelected.' order by CDATE ASC';
    $results = fetchRecords($dataRequest);*/
    //$table = newTable('Count',  'Gene Type', 'Gene Status');
    echo "$title\nNumber of Results,Date\n";
    // $table->setAttributes('class="simple" width="70%"');
    /*foreach ($results as $referenceRow) {
        extract($referenceRow);*/
    // $table->addRow( $RPT_PROCESS_TYPE_ID,  $EXTRACT_VALUE, $CDATE);
    //$num=count($plotArray);
    /*for ($i=0;$i<$num;$i++)
    {
        $csv_output .= "$plotArray[$i],".date('m/d/Y',$dateArray[$i]);
        $csv_output.="\n";
    }*/
    foreach ($plotArray as $i=>$plot)
    {
        echo "$plot,".date('m/d/Y',$dateArray[$i]);
        echo "\n";
    }
    //$csv_output.="\nFinal Date=,".date('m/d/Y',$dateArray[$num]);
    //}
    //$toReturn .= $table->toHtml();
    
    //generateFooter($toReturn);
    
    //echo $csv_output;
}
?>
