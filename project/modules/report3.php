<?php

//total number of genes

function report3_GenesStart()
{
    setSessionVar('Genesrept','Genes');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'GenesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes Added Each Month within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $species=getRequestVarString('species');
            $pipe=getRequestVarString('pipeline');
            //echo getRequestVarString('pipeline');
            setSessionVar('Species',$species);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getGenesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('Genesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GenesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Genes$manual Added Each Month within Query Range");
            setSessionVar('GenesTitle',"Number of {$species}Genes$manual Added Each Month within Query Range");
            //report3_graphPrep('Genes','getReport',false);
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'Genes')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'Genes'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_GenesTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('Genesdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getGenesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(UNIQUE( G.GENE_KEY)) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_VariantsStart()
{
    setSessionVar('Variantsrept','Variants');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'VariantsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    $theForm->setDefault('pipeline','on');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Gene Variants Added Each Month within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $species=getRequestVarString('species');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Species',$species);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getVariantsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('Variantsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('VariantsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Gene Variants$manual Added Each Month  within Query Range");
            setSessionVar('VariantsTitle',"Number of {$species}Gene Variants$manual Added Each Month  within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'Variants')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'Variants'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_VariantsTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('Variantsdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getVariantsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(UNIQUE(G.GENE_KEY)) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_PseudogenesStart()
{
    setSessionVar('Pseudogenesrept','Pseudogenes');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'PseudogenesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('pipeline','on');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Pseudogenes Added Each Month within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getPseudogenesByMonth($dateArray,$theForm->getValue('pipeline'));
            //var_dump($plotArray);
            //make here
            setSessionVar('Pseudogenesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('PseudogenesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Pseudogenes$manual Added Each Month within Query Range");
            setSessionVar('PseudogenesTitle',"Number of {$species}Pseudogenes$manual Added Each Month within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'Pseudogenes','funct'=>'getReport')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'Pseudogenes','funct'=>'CSV'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_PseudogenesTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('Pseudogenesdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getPseudogenesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(UNIQUE( G.GENE_KEY)) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_CumMonthlyGWAnyAnnotationsStart()
{
    setSessionVar('CumMonthlyGWAnyAnnotationsrept','CumMonthlyGWAnyAnnotations');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'CumMonthlyGWAnyAnnotationsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes with Any Ontology Annotations each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumMonthlyGWAnyAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumMonthlyGWAnyAnnotationsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumMonthlyGWAnyAnnotationsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of {$species}Genes with Any$manual Added Ontology Annotations-Cumulative by Month");
            setSessionVar('CumMonthlyGWAnyAnnotationsTitle',"Total number of {$species}Genes with Any$manual Added Ontology Annotations-Cumulative by Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMonthlyGWAnyAnnotations')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMonthlyGWAnyAnnotations'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumMonthlyGWAnyAnnotationsTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumMonthlyGWAnyAnnotationsdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumMonthlyGWAnyAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count ( unique (  g.gene_symbol ) )
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id";
    if (!$pipeline)
    {
        $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
    //echo $total.'</br>';
    $sql="select count ( unique (  g.gene_symbol ) )
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique (  g.gene_symbol ) )
        from genes g, full_annot f ,  rgd_ids r
        where g.rgd_id = f.annotated_object_rgd_id
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species)
        and g.rgd_id = r.rgd_id
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//Genes with Molecular, Cellular, Biological function annotations
function report3_GWMBCAStart()
{
    setSessionVar('GWMBCArept','GWMBCA');
    ////setSessionVar('rept','GWMBCA');
    $toReturn='';
    $array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'GWMBCAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes Receiving Molecular Function/Biological Process/Cellular Component GO Annotations each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getGWMBCAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('GWMBCAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GWMBCADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Genes Receiving$manual $function GO Annotations Each Month");
            setSessionVar('GWMBCATitle',"Number of {$species}Genes Receiving$manual $function GO Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'GWMBCA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'GWMBCA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGWMBCAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique ( g.gene_key) )    from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species)
        and f.aspect = '$function'
        and f.term_acc like 'G%'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')
        ";
        //echo "$sql\n\n";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(UNIQUE(G.GENE_KEY))'];
    }
    return $toReturn;
}
/*function report3_GWNotEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','GWNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    

    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Number of Genes Receiving Non-[Evidence] {$annotArray[$default]} Annotations Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(true,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'GWNotEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','GWNotEvidenceGWNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
            //$theForm->addMultipleCheckbox('evidence','GWNotEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Number of Genes Receiving Non-[Evidence] {$annotArray[$default]} Annotations Each Month Within Query Range");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_GWNotEvidenceStart()
{
    setSessionVar('GWNotEvidencerept','GWNotEvidence');
    //////setSessionVar('rept','GWNotEvidence');
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(true,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','GWNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'GWNotEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','GWNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
    //$theForm->addMultipleCheckbox('evidence','GWNotEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Number of Genes Receiving Non-[Evidence] {$annotArray[$default]} Annotations Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('GWNotEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getGWNotEvidencennotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('GWNotEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GWNotEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=substr($e,4).', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num!=0)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2);
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
                if ($num>1)
                {
                    $titleadd.='] ';
                    $titleadd='non-['.$titleadd;
                }
                else
                {
                    $titleadd='non-'.$titleadd;
                }
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Number of {$species}Genes Receiving$manual {$pagetitleadd}{$function}$annot Annotations Each Month Within Query Range");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('GWNotEvidenceTitle',"Num of {$species}Genes Receiving$manual {$titleadd}{$function}$annot Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'GWNotEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'GWNotEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGWNotEvidencennotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('GWNotEvidence');
    }
    else
    {
        $pipeline=false;
    }
    
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="
        select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        //$sql.=date('m-d-Y',$dateArray[$i+1]-5);
        $sql.="', 'MM-DD-YYYY')
        ";
        //echo "$sql<br><br>";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}*/
function report3_ReferencesStart()
{
    setSessionVar('Referencesrept','References');
    ////setSessionVar('rept','References');
    $toReturn='';
    $toReturn.= "<h2>Number of references in RGD:\n" . getReferences()."</h2>";
    //make here
    //break;
    // }
    //return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getReferences()
{
    //$species=getSessionVar('Species');
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    //$num=count($dateArray);
    $toReturn=0;
    //for ($i=0;$i<$num-1;$i++)
    //{
    $sql="
    select   count ( r.ref_key )
    from references  r , rgd_ids
    where
    r.rgd_id = rgd_ids .rgd_id
    and rgd_ids.object_status  = 'ACTIVE'";
    //echo "$sql<br><br>";
    $result = fetchRecord($sql);
    //dump($result);
    //echo('\n\n');
    $toReturn+=$result['COUNT(R.REF_KEY)'];
    //}
    return $toReturn;
}
//XDB IDs

function report3_XDBStart()
{
    setSessionVar('XDBrept','XDB');
    ////setSessionVar('rept','XDB');
    $toReturn='';
    $XDB=getXDBArrayForDropDown();
    //var_dump($XDB);
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'XDBStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('xdb', 'XDB ID:', getXDBArrayForDropDown(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    //$theForm->addCoolDate('fromdate','Start date:',1);
    //$theForm->addCoolDate('todate','End date:',1);
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes with Each of the XDB ID');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            //$fromDate=getRequestVarString('fromdate');
            //$toDate=getRequestVarString('todate');
            $xdb=getRequestVarString('xdb');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('XDB',$xdb);
            setSessionVar('Species',$species);
            $species=getSpeciesNameAndAll($species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $xdb=$XDB[$xdb];
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            //$dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            //$plotArray=getXDBAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            //setSessionVar('XDBdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            //setSessionVar('Dates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            //echo 'hi';
            //setPageTitle("Number of Rat Genes with $xdb: ".getXDB());
            $toReturn.="<h2>Number of $species Genes with $xdb: ".getXDB().'</h2>';
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getXDB()
{
    $XDB=getSessionVar('XDB');
    $species=getSessionVar('Species');
    
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    //$num=count($dateArray);
    $toReturn=0;
    //for ($i=0;$i<$num-1;$i++)
    //{
    $sql="
    select count(distinct a.rgd_id) from RGD_ACC_XDB a, RGD_IDS r where a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null";
    //echo "$sql<br><br>";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result = fetchRecord($sql);
    //dump($result);
    //echo('\n\n');
    $toReturn+=$result['COUNT(DISTINCTA.RGD_ID)'];
    //}
    return $toReturn;
}
function report3_CumMonthlyReferencesStart()
{
    setSessionVar('CumMonthlyReferencesrept','CumMonthlyReferences');
    //setSessionVar('rept','CumMonthlyReferences');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'CumMonthlyReferencesStart');
    //$theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    //$theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    $theForm->setDefault('pipeline','on');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of References in RGD Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            //$species=getRequestVarString('species');
            //setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            //$species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumMonthlyReferencesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumMonthlyReferencesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumMonthlyReferencesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual References in RGD Each Month");
            setSessionVar('CumMonthlyReferencesTitle',"Total number of$manual References in RGD Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMonthlyReferences')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMonthlyReferences'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumMonthlyReferencesTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumMonthlyReferencesdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumMonthlyReferencesByMonth($dateArray,$pipeline)
{
    //$species=getSessionVar('Species');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select   count ( r.ref_key )  as COUNT
        from full_annot f, references  r , rgd_ids
        where r.rgd_id = f.annotated_object_rgd_id and
        r.rgd_id = rgd_ids .rgd_id
        and rgd_ids.object_status  = 'ACTIVE'";
    }
    else
    {
        $sql="select   count ( r.ref_key )  as COUNT
        from references  r , rgd_ids
        where
        r.rgd_id = rgd_ids .rgd_id
        and rgd_ids.object_status  = 'ACTIVE'";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select   count ( r.ref_key )
        from full_annot f, references  r , rgd_ids
        where r.rgd_id = f.annotated_object_rgd_id and
        r.rgd_id = rgd_ids .rgd_id
        and rgd_ids.object_status  = 'ACTIVE'
        and rgd_ids.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select   count ( r.ref_key )
        from references  r , rgd_ids
        where
        r.rgd_id = rgd_ids .rgd_id
        and rgd_ids.object_status  = 'ACTIVE'
        and rgd_ids.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT(R.REF_KEY)'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select   count ( r.ref_key )
            from full_annot f, references  r , rgd_ids
            where r.rgd_id = f.annotated_object_rgd_id and
            r.rgd_id = rgd_ids .rgd_id
            and rgd_ids.object_status  = 'ACTIVE'
            and rgd_ids.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select   count ( r.ref_key )
            from references  r , rgd_ids
            where
            r.rgd_id = rgd_ids .rgd_id
            and rgd_ids.object_status  = 'ACTIVE'
            and rgd_ids.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT(R.REF_KEY)'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//cumulative Genes with MF, BP, CC GO Annotations
function report3_CumGWMBCAStart()
{
    setSessionVar('CumGWMBCArept','CumGWMBCA');
    ////setSessionVar('rept','CumGWMBCA');
    $toReturn='';
    $array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGWMBCAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes with Molecular Function/Biological Process/Cellular Component GO Annotations Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumGWMBCAByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGWMBCAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGWMBCADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of {$species}Genes with$manual $function GO Annotations Each Month");
            setSessionVar('CumGWMBCATitle',"Total number of {$species}Genes with$manual $function GO Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGWMBCA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGWMBCA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumGWMBCATemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumGWMBCAdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumGWMBCAByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
    where
    g.rgd_id = r.rgd_id
    and g.rgd_id = f.annotated_object_rgd_id
    and r.object_key = 1
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and f.aspect = '$function'
    and f.term_acc like 'G%'";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
    where
    g.rgd_id = r.rgd_id
    and g.rgd_id = f.annotated_object_rgd_id
    and r.object_key = 1
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and f.aspect = '$function'
    and f.term_acc like 'G%'
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species)
        and f.aspect = '$function'
        and f.term_acc like 'G%'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
/*function report3_CumGWNotEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumGWNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Total Number of Genes With Non-[Evidence] {$annotArray[$default]} Annotations Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(true,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'CumGWNotEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumGWNotEvidenceCumGWNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
            //$theForm->addMultipleCheckbox('evidence','CumGWNotEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Total Number of Genes With Non-[Evidence] {$annotArray[$default]} Annotations Each Month");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_CumGWNotEvidenceStart()
{
    setSessionVar('CumGWNotEvidencerept','CumGWNotEvidence');
    //setSessionVar('rept','CumGWNotEvidence');
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(true,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumGWNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGWNotEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumGWNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
    //$theForm->addMultipleCheckbox('evidence','CumGWNotEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Total Number of Genes With Non-[Evidence] {$annotArray[$default]} Annotations Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('CumGWNotEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumGWNotEvidenceByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGWNotEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGWNotEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=substr($e,4).', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num!=0)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2);
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
                if ($num>1)
                {
                    $titleadd.='] ';
                    $titleadd='non-['.$titleadd;
                }
                else
                {
                    $titleadd='non-'.$titleadd;
                }
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Total Number of {$species}Genes with$manual {$pagetitleadd}{$function}$annot Annotations Each Month ");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('CumGWNotEvidenceTitle',"Tot Num of {$species}Genes with$manual {$titleadd}{$function}$annot Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGWNotEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGWNotEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumGWNotEvidenceByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('CumGWNotEvidence');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //$function=getSessionVar('Function');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}*/

//cumulative Genes with non-IEA, non-ISS GO Annotations
function report3_CumGWEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumGWEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Total Number of Genes With {$annotArray[$default]} Annotations by Evidence Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(false,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'CumGWEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumGWEvidenceCumGWEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
            //$theForm->addMultipleCheckbox('evidence','CumGWEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Total Number of Genes With {$annotArray[$default]} Annotations by Evidence Each Month");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_CumGWEvidenceStart()
{
    setSessionVar('CumGWEvidencerept','CumGWEvidence');
    /*if (!is_null($run))
    {
        return ($run);
    }*/
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(false,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumGWEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGWEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumGWEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
    //$theForm->addMultipleCheckbox('evidence','CumGWEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Total Number of Genes With {$annotArray[$default]} Annotations by Evidence Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('CumGWEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumGWEvidenceByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGWEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGWEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=$e.', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num>2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i+2);
                $pagetitleadd.="or $tytleadd ";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-3);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                //echo $i.br().$pagetitleadd.br();
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i);
                $pagetitleadd.=" or $tytleadd";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==1)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2).' ';
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Total Number of {$species}Genes with$manual {$pagetitleadd}{$function}$annot Annotations Each Month ");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('CumGWEvidenceTitle',"Tot Num of {$species}Genes with$manual {$titleadd}{$function}$annot Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGWEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGWEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumGWEvidenceByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('CumGWEvidence');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //$function=getSessionVar('Function');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
function report3_CumGenesStart()
{
    setSessionVar('CumGenesrept','CumGenes');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGenesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumGenesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGenesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGenesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual {$species}Genes Each Month");
            setSessionVar('CumGenesTitle',"Total number of$manual {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGenes')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGenes'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumGenesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID ";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID ";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//cumulative Alleles
function report3_CumAllelesStart()
{
    setSessionVar('CumAllelesrept','CumAlleles');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumAllelesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Alleles Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumAllelesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumAllelesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumAllelesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual {$species}Alleles Each Month");
            setSessionVar('CumAllelesTitle',"Total number of$manual {$species}Alleles Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumAlleles')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumAlleles'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumAllelesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
function report3_MonthlyXDBStart()
{
    setSessionVar('MonthlyXDBrept','MonthlyXDB');
    $toReturn='';
    $XDB=getXDBArrayForDropDown();
    $theForm = newForm('Submit', 'POST', 'report3', 'MonthlyXDBStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('xdb','XDB ID:',$XDB,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes with the XDB ID Added Each Month within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $xdb=getRequestVarString('xdb');
            setSessionVar('XDB',$xdb);
            $xdb=$XDB[$xdb];
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getMonthlyXDBByMonth($dateArray,$theForm->getValue('pipeline'));
            //var_dump($plotArray);
            //make here
            setSessionVar('MonthlyXDBdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('MonthlyXDBDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Genes with $xdb Added$manual Each Month within Query Range");
            setSessionVar('MonthlyXDBTitle',"Number of {$species}Genes with $xdb Added$manual Each Month within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'MonthlyXDB')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'MonthlyXDB'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getMonthlyXDBByMonth($dateArray,$pipeline)
{
    $XDB=getSessionVar('XDB');
    $species=getSessionVar('Species');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(distinct a.rgd_id) as COUNT from full_annot f, RGD_ACC_XDB a, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(distinct a.rgd_id) as COUNT from RGD_ACC_XDB a, RGD_IDS r where a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
            
        }
        
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_CumMonthlyXDBStart()
{
    setSessionVar('CumMonthlyXDBrept','CumMonthlyXDB');
    $toReturn='';
    $XDB=getXDBArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumMonthlyXDBStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('xdb','XDB ID:',$XDB,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes with the XDB ID Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $xdb=getRequestVarString('xdb');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('XDB',$xdb);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $xdb=$XDB[$xdb];
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumMonthlyXDBByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumMonthlyXDBdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumMonthlyXDBDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual {$species}Genes with $xdb by Month");
            setSessionVar('CumMonthlyXDBTitle',"Total number of$manual {$species}Genes with $xdb by Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMonthlyXDB')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMonthlyXDB'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumMonthlyXDBByMonth($dateArray,$pipeline)
{
    $XDB=getSessionVar('XDB');
    $species=getSessionVar('Species');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(distinct a.rgd_id) as COUNT from full_annot f, RGD_ACC_XDB a, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null";
    }
    else
    {
        $sql="select count(distinct a.rgd_id) as COUNT from RGD_ACC_XDB a, RGD_IDS r where a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(distinct a.rgd_id) as COUNT from full_annot f, RGD_ACC_XDB a, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(distinct a.rgd_id) as COUNT from RGD_ACC_XDB a, RGD_IDS r where a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(distinct a.rgd_id) as COUNT from full_annot f, RGD_ACC_XDB a, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(distinct a.rgd_id) as COUNT from RGD_ACC_XDB a, RGD_IDS r where a.rgd_id=r.rgd_id and r.species_type_key in ($species) and r.OBJECT_STATUS='ACTIVE' and a.XDB_KEY=$XDB and a.ACC_ID is not null
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//cumulative Monthly Known/Predicted
function report3_CumKPStart()
{
    setSessionVar('CumKPrept','CumKP');
    $toReturn='';
    //$XDB=getXDBArrayForDropDown();
    $annotations=array("'scrna', 'miscrna', 'snorna', 'snrna',  'rrna', 'trna', 'gene', 'protein-coding'"=>'Known', "'predicted-high', 'predicted-moderate', 'predicted-low', 'predicted-no evidence'"=>'Predicted');
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumKPStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot','Gene Status:',$annotations,1,false);
    //$theForm->addSelect('xdb','XDB ID:',$XDB,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot',"'scrna', 'miscrna', 'snorna', 'snrna',  'rrna', 'trna', 'gene', 'protein-coding'");
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Known and Predicted Genes Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            //$xdb=getRequestVarString('xdb');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            $annot=$annotations[$annot];
            //setSessionVar('XDB',$xdb);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$xdb=$XDB[$xdb];
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumKPByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumKPdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumKPDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual $annot {$species}Genes Each Month");
            setSessionVar('CumKPTitle',"Total number of$manual $annot {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumKP')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumKP'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumKPTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumKPdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumKPByMonth($dateArray,$pipeline)
{
    //$XDB=getSessionVar('XDB');
    $species=getSessionVar('Species');
    
    $annot=getSessionVar('Annot');
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and
        r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where
        r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and
        r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where
        r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and
            r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where
            r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
/*function report3_NotEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','NotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Added Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(true,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'NotEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','NotEvidenceNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
            //$theForm->addMultipleCheckbox('evidence','NotEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Added Each Month Within Query Range");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_NotEvidenceStart()
{
    setSessionVar('NotEvidencerept','NotEvidence');
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(true,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','NotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'NotEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','NotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
    //$theForm->addMultipleCheckbox('evidence','NotEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Added Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('NotEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getNotEvidencennotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('NotEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('NotEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=substr($e,4).', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num!=0)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2);
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
                if ($num>1)
                {
                    $titleadd.='] ';
                    $titleadd='non-['.$titleadd;
                }
                else
                {
                    $titleadd='non-'.$titleadd;
                }
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Number of$manual {$pagetitleadd}{$function}$annot Annotations for {$species}Genes Each Month Within Query Range");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('NotEvidenceTitle',"Num of$manual {$titleadd}{$function}$annot Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'NotEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'NotEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getNotEvidencennotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('NotEvidence');
    }
    else
    {
        $pipeline=false;
    }
    
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="
        select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        //$sql.=date('m-d-Y',$dateArray[$i+1]-5);
        $sql.="', 'MM-DD-YYYY')
        ";
        //echo "$sql<br><br>";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}*/

function report3_GOStart()
{
    setSessionVar('GOrept','GO');
    $toReturn='';
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $annotations=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'GOStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot', 'Annotation Type:', $annotations, 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot',$default);
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            setPageTitle('Number of '.$annotations[$default].' Annotations for Genes Added Each Month Within Query Range');
        case SUBMIT_INVALID :
                $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $annot=$annotations[$annot];
            // $function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getGOAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('GOdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GODates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of $annot Annotations for {$species}Genes Added$manual Each Month Within Query Range");
            setSessionVar('GOTitle',"Number of $annot Annotations for {$species}Genes Added$manual Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'GO')).'">'."</br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'GO'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGOAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $annot=getSessionVar('Annot');
    //echo $annot.br().$pipeline.br().$species;
    $go=false;
    if ($annot=='G%'&&$pipeline&&$species==3)
    {
        //echo 'go';
        $go=true;
        $pipeline=false;
    }
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql ="-- total number of GO annotations for rat genes F1
        select count ( *)  from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and f.term_acc like '$annot' -- Term is GO
        and r.species_type_key in ($species) -- RAT
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')
        ";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        if ($go)
        {
            $part1=$result['COUNT(*)'];
            $sql="select e.extract_value,e.created_date from report_extracts e
            where
            e.rpt_process_type_id=6 --GOAnnotationRat
            and e.created_date between         to_date('".date('m-d-Y',$dateArray[$i])."','MM-DD-YYYY') and to_date('";
        
            if ($i<$num-2)
            {
                $sql.=date('m-d-Y',$dateArray[$i+1]);
            }
            else
            {
                $sql.=date('m-d-Y',$dateArray[$i+1]+5);
            }
            $sql.="', 'MM-DD-YYYY')
            ";
            $results=fetchRecords($sql);
            //var_dump($results);
            //echo br(2);
            $numb=count($results);
            $part2=0;
            $latedate=0;
            for ($ii=0;$ii<$numb;$ii++)
            {
                $date=$results[$ii]['CREATED_DATE'];
                //$date=substr($date,0,
                $timestamp=oraclestrtotime($date);
                //echo $timestamp.br();
                if ($timestamp>$latedate)
                {
                    $latedate=$timestamp;
                    $part2=$results[$ii]['EXTRACT_VALUE'];
                }
            }
            $toReturn[]=$part1+$part2;
        }
        else
        {
            $toReturn[]=$result['COUNT(*)'];
        }
        //dump($result);
        //echo('\n\n');
    }
    return $toReturn;
}
function report3_CumGOAStart()
{
    setSessionVar('CumGOArept','CumGOA');
    $toReturn='';
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $annotations=getAnnotationArrayForDropDown();
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGOAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot','Annotation type:',$annotations,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot',$default);
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of '.$annotations[$default].'  Annotations for Genes Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $annot=$annotations[$annot];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumGOAByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGOAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGOADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual $annot Annotations for {$species}Genes Each Month");
            setSessionVar('CumGOATitle',"Total number of$manual $annot Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGOA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGOA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumGOAByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $pippeline=null;
    $annot=getSessionVar('Annot');
    //foreach ($dateArray as $)
    $go=false;
    if ($annot=='G%'&&$pipeline&&$species==3)
    {
        //echo 'go';
        $go=true;
        $pippeline=true;
        //$pipeline=false;
        //$pipeline=false;
    }
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="-- total number of GO annotations for rat genes F1
    select count ( *) as COUNT from full_annot f, rgd_ids r
    where
    f.annotated_object_rgd_id = r.rgd_id
    and f.rgd_object_key = 1 -- GENE
    and r.object_status = 'ACTIVE'
    and f.term_acc like '$annot' -- Term is GO
    and r.species_type_key in ($species) -- RAT";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    elseif (!is_null($pippeline))
    {
        $sql.='
        and (f.created_by not in (69,70) or f.created_by is null)';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="-- total number of GO annotations for rat genes F1
    select count ( *) as COUNT from full_annot f, rgd_ids r
    where
    f.annotated_object_rgd_id = r.rgd_id
    and f.rgd_object_key = 1 -- GENE
    and r.object_status = 'ACTIVE'
    and f.term_acc like '$annot' -- Term is GO
    and r.species_type_key in ($species) -- RAT
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    elseif (!is_null($pippeline))
    {
        $sql.='
        and (f.created_by not in (69,70) or f.created_by is null)';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="-- total number of GO annotations for rat genes F1
        select count ( *) as COUNT from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and f.term_acc like '$annot' -- Term is GO
        and r.species_type_key in ($species) -- RAT
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        elseif (!is_null($pippeline))
        {
            $sql.='
            and (f.created_by not in (69,70) or f.created_by is null)';
        }
        $result = fetchRecord($sql);
        if ($go)
        {
            $part1=$total-$result['COUNT'];
            $date=$dateArray[$i];
            if ($i>=$num-2)
            {
                $date+=5;
            }
            $results=getLastRun($date);
            $numb=count($results);
            $part2=0;
            $latedate=0;
            for ($ii=0;$ii<$numb;$ii++)
            {
                $date=$results[$ii]['CREATED_DATE'];
                //$date=substr($date,0,
                $timestamp=oraclestrtotime($date);
                //echo $timestamp.br();
                if ($timestamp>$latedate)
                {
                    $latedate=$timestamp;
                    $part2=$results[$ii]['EXTRACT_VALUE'];
                }
            }
            $toReturn[]=$part1+$part2;
        }
        else
        {
            //echo "</br>$sql</br>";
            //dump($result);
            //echo('\n\n');
            //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
            $toReturn[]=$total-$result['COUNT'];
        }
    }
    if ($go)
    {
        $part1=$total;
        $date=$dateArray[$num-1]+5;
        $results=getLastRun($date);
        $numb=count($results);
        $part2=0;
        $latedate=0;
        for ($ii=0;$ii<$numb;$ii++)
        {
            $date=$results[$ii]['CREATED_DATE'];
            //$date=substr($date,0,
            $timestamp=oraclestrtotime($date);
            //echo $timestamp.br();
            if ($timestamp>$latedate)
            {
                $latedate=$timestamp;
                $part2=$results[$ii]['EXTRACT_VALUE'];
            }
        }
        $toReturn[]=$part1+$part2;
    }
    else
    {
        $toReturn[]=$total;
    }
    //var_dump($toReturn);
    return $toReturn;
}
function report3_CumMBCAStart()
{
    setSessionVar('CumMBCArept','CumMBCA');
    $toReturn='';
    $array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumMBCAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Molecular Function/Biological Process/Cellular Component GO Annotations for Genes Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumMBCAByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumMBCAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumMBCADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual $function GO Annotations for {$species}Genes Each Month");
            setSessionVar('CumMBCATitle',"Total number of$manual $function GO Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMBCA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMBCA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumMBCAByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count(*)  as COUNT from full_annot f, rgd_ids r
    where
    f.annotated_object_rgd_id = r.rgd_id
    and f.rgd_object_key = 1
    and r.object_status = 'ACTIVE'
    and f.term_acc like 'G%'
    and r.species_type_key in ($species)
    and f.aspect = '$function'";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count(*)  as COUNT from full_annot f, rgd_ids r
    where
    f.annotated_object_rgd_id = r.rgd_id
    and f.rgd_object_key = 1
    and r.object_status = 'ACTIVE'
    and f.term_acc like 'G%'
    and r.species_type_key in ($species)
    and f.aspect = '$function'
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count(*)  as COUNT from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and r.object_status = 'ACTIVE'
        and f.term_acc like 'G%'
        and r.species_type_key in ($species)
        and f.aspect = '$function'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//monthly references
function report3_MonthlyReferencesStart()
{
    setSessionVar('MonthlyReferencesrept','MonthlyReferences');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'MonthlyReferencesStart');
    //$theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('pipeline','on');
    //$theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of References Added Each Month Within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            //$species=getRequestVarString('species');
            //setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            //$species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getMonthlyReferencesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('MonthlyReferencesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('MonthlyReferencesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of References Added$manual Each Month in Query Range");
            setSessionVar('MonthlyReferencesTitle',"Number of References Added$manual Each Month in Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'MonthlyReferences')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'MonthlyReferences'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getMonthlyReferencesByMonth($dateArray,$pipeline)
{
    //$species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select   count ( r.ref_key )
            from full_annot f, references  r , rgd_ids
            where r.rgd_id = f.annotated_object_rgd_id and
            r.rgd_id = rgd_ids .rgd_id
            and rgd_ids.object_status  = 'ACTIVE'
            and rgd_ids.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select   count ( r.ref_key )
            from references  r , rgd_ids
            where
            r.rgd_id = rgd_ids .rgd_id
            and rgd_ids.object_status  = 'ACTIVE'
            and rgd_ids.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(R.REF_KEY)'];
    }
    return $toReturn;
}
/*function report3_CumNotEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Total Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(true,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'CumNotEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumNotEvidenceCumNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
            //$theForm->addMultipleCheckbox('evidence','CumNotEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Total Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Each Month");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_CumNotEvidenceStart()
{
    setSessionVar('CumNotEvidencerept','CumNotEvidence');
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(true,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumNotEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'CumNotEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumNotEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8, false);
    //$theForm->addMultipleCheckbox('evidence','CumNotEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Total Number of Non-[Evidence] {$annotArray[$default]} Annotations for Genes Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('CumNotEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getCumNotEvidenceByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumNotEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumNotEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=substr($e,4).', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num!=0)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2);
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
                if ($num>1)
                {
                    $titleadd.='] ';
                    $titleadd='non-['.$titleadd;
                }
                else
                {
                    $titleadd='non-'.$titleadd;
                }
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Total Number of$manual {$pagetitleadd}{$function}$annot Annotations for {$species}Genes Each Month ");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('CumNotEvidenceTitle',"Tot Num of$manual {$titleadd}{$function}$annot Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumNotEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumNotEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumNotEvidenceByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('CumNotEvidence');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //$function=getSessionVar('Function');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
    ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
    ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        ";
        foreach ($evid as $e)
        {
            $sql.="$e
            ";
        }
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}*/

function report3_CumNEventStart()
{
    setSessionVar('CumNEventrept','CumNEvent');
    $toReturn='';
    //$XDB=getXDBArrayForDropDown();
    //$annotations=array("'scrna', 'miscrna', 'snorna', 'snrna',  'rrna', 'trna', 'gene', 'protein-coding'"=>'Known', "'predicted-high', 'predicted-moderate', 'predicted-low', 'predicted-no evidence'"=>'Predicted');
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumNEventStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('annot','Gene Status:',$annotations,1,false);
    //$theForm->addSelect('xdb','XDB ID:',$XDB,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('annot',"'scrna', 'miscrna', 'snorna', 'snrna',  'rrna', 'trna', 'gene', 'protein-coding'");
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes with Nomenclature Events Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$annot=getRequestVarString('annot');
            //$xdb=getRequestVarString('xdb');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Annot',$annot);
            //$annot=$annotations[$annot];
            //setSessionVar('XDB',$xdb);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$xdb=$XDB[$xdb];
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumNEventByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumNEventdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumNEventDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of {$species}Genes with$manual Nomenclature Events Each Month");
            setSessionVar('CumNEventTitle',"Total number of {$species}Genes with$manual Nomenclature Events Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumNEvent')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumNEvent'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumNEventTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumNEventdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumNEventByMonth($dateArray,$pipeline)
{
    //$XDB=getSessionVar('XDB');
    $species=getSessionVar('Species');
    
    //$annot=getSessionVar('Annot');
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="--number of rat genes receiving nomenclature events in query range
        select count(distinct n.rgd_id) as COUNT from full_annot f, NOMEN_EVENTS n, rgd_ids r where r.rgd_id = f.annotated_object_rgd_id and  r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID";
    }
    else
    {
        $sql="--number of rat genes receiving nomenclature events in query range
        select count(distinct n.rgd_id) as COUNT from NOMEN_EVENTS n, rgd_ids r where r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="--number of rat genes receiving nomenclature events in query range
        select count(distinct n.rgd_id) as COUNT from full_annot f, NOMEN_EVENTS n, rgd_ids r where r.rgd_id = f.annotated_object_rgd_id and  r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID and n.EVENT_DATE between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="--number of rat genes receiving nomenclature events in query range
        select count(distinct n.rgd_id) as COUNT from NOMEN_EVENTS n, rgd_ids r where r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID and n.EVENT_DATE between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="--number of rat genes receiving nomenclature events in query range
            select count(distinct n.rgd_id) as COUNT from full_annot f, NOMEN_EVENTS n, rgd_ids r where r.rgd_id = f.annotated_object_rgd_id and  r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID and n.EVENT_DATE between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="--number of rat genes receiving nomenclature events in query range
            select count(distinct n.rgd_id) as COUNT from NOMEN_EVENTS n, rgd_ids r where r.SPECIES_TYPE_KEY in($species) and r.object_key=1 and r.RGD_ID=n.RGD_ID and n.EVENT_DATE between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
function report3_GWGOStart()
{
    setSessionVar('GWGOrept','GWGO');
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $annotations=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'GWGOStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot','Annotation type:',$annotations,1,false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot',$default);
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes Receiving '.$annotations[$default]. ' Annotations Each Month Within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $annot=$annotations[$annot];
            // $function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getGWGOAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('GWGOdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GWGODates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}genes Receiving$manual $annot Annotations Each Month Within Query Range");
            setSessionVar('GWGOTitle',"Number of {$species}genes Receiving$manual $annot Annotations Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'GWGO')).'">'."</br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'GWGO'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGWGOAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $annot=getSessionVar('Annot');
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        //     $sql="select count ( unique ( g.gene_key) )    from genes g, rgd_ids r , full_annot f
        //where
        //g.rgd_id = r.rgd_id
        //and g.rgd_id = f.annotated_object_rgd_id
        //and r.object_key = 1
        //and r.object_status = 'ACTIVE'
        //and r.species_type_key  in ($species)
        //--and f.aspect = '$function'
        //and f.term_acc like 'G%'
        //and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and //to_date('";
        $sql ="-- Total Number of rat genes with GO Annotations F5 ( 11605 )
        select count ( unique ( g.gene_key) )    from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species) -- RAT
        and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(UNIQUE(G.GENE_KEY))'];
    }
    return $toReturn;
}
function report3_CumPseudogenesStart()
{
    setSessionVar('CumPseudogenesrept','CumPseudogenes');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumPseudogenesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Pseudogenes Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumPseudogenesByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumPseudogenesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumPseudogenesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual {$species}Pseudogenes Each Month");
            setSessionVar('CumPseudogenesTitle',"Total number of$manual {$species}Pseudogenes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumPseudogenes')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumPseudogenes'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumPseudogenesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and (g.GENE_TYPE_LC='pseudo' or g.GENE_TYPE_LC='pseudogene') and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

//Number of alleles
function report3_AllelesStart()
{
    setSessionVar('Allelesrept','Alleles');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'AllelesStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('pipeline','on');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Alleles Added Each Month within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getAllelesByMonth($dateArray,$theForm->getValue('pipeline'));
            //var_dump($plotArray);
            //make here
            setSessionVar('Allelesdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('AllelesDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Alleles Added$manual Each Month within Query Range");
            setSessionVar('AllelesTitle',"Number of {$species}Alleles Added$manual Each Month within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'Alleles')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'Alleles'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getAllelesByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) from GENES g, RGD_IDS r where  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='allele' and r.RGD_ID=g.RGD_ID and r.CREATED_DATE  between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(*)'];
    }
    return $toReturn;
}
function report3_EvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','EvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Number of {$annotArray[$default]} Annotations for Genes by Evidence Added Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(false,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'EvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','EvidenceEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
            //$theForm->addMultipleCheckbox('evidence','Evidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Number of {$annotArray[$default]} Annotations for Genes by Evidence Added Each Month Within Query Range");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_EvidenceStart()
{
    setSessionVar('Evidencerept','Evidence');
    /*if (!is_null($run))
    {
        return setPageTitle($run);
    }*/
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(false,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','EvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'EvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','EvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
    //$theForm->addMultipleCheckbox('evidence','Evidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Number of {$annotArray[$default]} Annotations for Genes by Evidence Added Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('Evidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getEvidencennotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('Evidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('EvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=$e.', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num>2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i+2);
                $pagetitleadd.="or $tytleadd ";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-3);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                //echo $i.br().$pagetitleadd.br();
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i);
                $pagetitleadd.=" or $tytleadd";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==1)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2).' ';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Number of$manual {$pagetitleadd}{$function}$annot Annotations for {$species}Genes Each Month Within Query Range");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('EvidenceTitle',"Num of$manual {$titleadd}{$function}$annot Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'Evidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'Evidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getEvidencennotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('Evidence');
    //$pipeline=getSessionVar('Pipeline');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="
        select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        //$sql.=date('m-d-Y',$dateArray[$i+1]-5);
        $sql.="', 'MM-DD-YYYY')
        ";
        //echo "$sql<br><br>";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_MBCAStart()
{
    setSessionVar('MBCArept','MBCA');
    $toReturn='';
    $array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'MBCAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Molecular Function/Biological Process/Cellular Component GO Annotations for Genes Added Each Month Within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            $function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            $plotArray=getMBCAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('MBCAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('MBCADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of $function GO Annotations for {$species}Genes Added$manual Each Month Within Query Range");
            setSessionVar('MBCATitle',"Number of $function GO Annotations for {$species}Genes Added$manual Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'MBCA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'MBCA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getMBCAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count(*)   from full_annot f, rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and r.object_status = 'ACTIVE'
        and f.term_acc like 'G%'
        and r.species_type_key in ($species)
        and f.aspect = '$function'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')
        ";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(*)'];
    }
    return $toReturn;
}

//Genes with non-IEA, non-ISS GO Annotations
//genes with
function report3_GWEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','GWEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Number of Genes Receiving {$annotArray[$default]} Annotations by Evidence Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(false,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'GWEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','GWEvidenceGWEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
            //$theForm->addMultipleCheckbox('evidence','GWEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Number of Genes Receiving {$annotArray[$default]} Annotations by Evidence Each Month Within Query Range");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_GWEvidenceStart()
{
    setSessionVar('GWEvidencerept','GWEvidence');
    /*if (!is_null($run))
    {
        return setPageTitle($run);
    }*/
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(false,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','GWEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'GWEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','GWEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
    //$theForm->addMultipleCheckbox('evidence','GWEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Number of Genes Receiving {$annotArray[$default]} Annotations by Evidence Each Month Within Query Range");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('GWEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getGWEvidencennotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('GWEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('GWEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=$e.', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num>2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i+2);
                $pagetitleadd.="or $tytleadd ";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-3);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                //echo $i.br().$pagetitleadd.br();
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i);
                $pagetitleadd.=" or $tytleadd";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==1)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2).' ';
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Number of {$species}Genes Receiving$manual {$pagetitleadd}{$function}$annot Annotations Each Month Within Query Range");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('GWEvidenceTitle',"Num of {$species}Genes Receiving$manual {$titleadd}{$function}$annot Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'GWEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'GWEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGWEvidencennotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('GWEvidence');
    //$pipeline=getSessionVar('Pipeline');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="
        select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species) -- RAT
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        //$sql.=date('m-d-Y',$dateArray[$i+1]-5);
        $sql.="', 'MM-DD-YYYY')
        ";
        //echo "$sql<br><br>";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
/*function report3_GWAnyAnnotationsStart()
{
    setSessionVar('GWAnyAnnotationsrept','GWAnyAnnotations');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'GWAnyAnnotationsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    //$theForm->addCoolDate('fromdate','Start date:',1);
    //$theForm->addCoolDate('todate','End date:',1);
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
   /* switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes Receiving Any Manual Ontology Annotations');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            //$fromDate=getRequestVarString('fromdate');
            //$toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            //$dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            //$plotArray=getGWAnyAnnotationsAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            //setSessionVar('GWAnyAnnotationsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            //setSessionVar('Dates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            /*$toReturn.= "<h2>Number of $species genes Receiving any Manual ontology annotations:\n" . getGWAnyAnnotations()."</h2>";
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getGWAnyAnnotations()
{
    $species=getSessionVar('Species');
    
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    //$num=count($dateArray);
    $toReturn=0;
    //for ($i=0;$i<$num-1;$i++)
    //{
    $sql="
    select count ( unique (  g.gene_symbol ) )
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id";
    //echo "$sql<br><br>";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result = fetchRecord($sql);
    //dump($result);
    //echo('\n\n');
    $toReturn+=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
    //}
    return $toReturn;
}*/
//total number of genes with any annotations-monthly
function report3_MonthlyGWAnyAnnotationsStart()
{
    setSessionVar('MonthlyGWAnyAnnotationsrept','MonthlyGWAnyAnnotations');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'MonthlyGWAnyAnnotationsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Genes receiving Any Ontology Annotations Each Month Within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getMonthlyGWAnyAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('MonthlyGWAnyAnnotationsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('MonthlyGWAnyAnnotationsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of {$species}Genes Receiving Any$manual Ontology Annotations Each Month Within Query Range");
            setSessionVar('MonthlyGWAnyAnnotationsTitle',"Number of {$species}Genes Receiving Any$manual Ontology Annotations Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'MonthlyGWAnyAnnotations')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'MonthlyGWAnyAnnotations'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_MonthlyGWAnyAnnotationsTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('MonthlyGWAnyAnnotationsdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getMonthlyGWAnyAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count ( unique (  g.gene_symbol ) )
        from genes g, full_annot f ,  rgd_ids r
        where g.rgd_id = f.annotated_object_rgd_id
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species)
        and g.rgd_id = r.rgd_id
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')
        ";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
    }
    return $toReturn;
}

//cumulative non-IEA, non-ISS GO Annotations
function report3_CumEvidenceSetAnnot()
{
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    //$aForm->setDefault('annot',$default);
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($aForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            $default=getSessionVarOKEmpty('AnnotFrom');
            if (!isset($default))
            {
                $default='G%';
            }
            setPageTitle("Total Number of {$annotArray[$default]} Annotations for Genes by Evidence Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $default=getRequestVarString('annot');
            $arraye=getEvidenceArrayForDropDown(false,$default);
            $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
            //var_dump($array);
            setSessionVar('AnnotFrom',$default);
            $theForm = newForm('Submit', 'POST', 'report3', 'CumEvidenceStart');
            $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
            //$table=newTable();
            //$sArray=getSpaceArray();
            //$spaces=spaceGen($sArray[$default]);
            //$table->setAttributes('width="100%"');
            //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumEvidenceCumEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
            if ($default=='G%')
            {
                $theForm->addSelect('function','Type:',$funx,1,false);
            }
            $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
            //$theForm->addMultipleCheckbox('evidence','CumEvidence:',$array,0,' ');
            //$theForm->addSelect('function','Type:',$array,1,false);
            $theForm->addCoolDate('fromdate','Start date:',1);
            $theForm->addCoolDate('todate','End date:',1);
            $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
            $theForm->setDefault('function','5');
            $theForm->setDefault('species', '3');
            $theForm->setDefault('fromdate',getLastYear());
            $theForm->setDefault('todate',date('m/d/Y'));
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            setPageTitle("Total Number of {$annotArray[$default]} Annotations for Genes by Evidence Each Month");
            return $toReturn;
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function report3_CumEvidenceStart()
{
    setSessionVar('CumEvidencerept','CumEvidence');
    /*if (!is_null($run))
    {
        return setPageTitle($run);
    }*/
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $toReturn='';
    $funx=array('F'=>'Molecular Function ','P'=>'Biological Process ','C'=>'Cellular Component ','5'=>'Any');
    $morefunx=array('Molecular Function '=>'MF ','Biological Process '=>'BP ','Cellular Component '=>'CC ','Any'=>'');
    $arraye=getEvidenceArrayForDropDown(false,$default);
    $array=array(0=>0);
            $num=count($arraye);
            //$a=0;
           for ($a=0;$a<$num;$a++){
            $array[key($arraye)]=$arraye[key($arraye)];
            next($arraye);
            //$a++;
            }
    $annotArray=getAnnotationArrayForDropDown();
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $aForm=newForm('Update Page','POST','report3','CumEvidenceSetAnnot');
    $aForm->addSelect('annot','Annotation Type:',$annotArray,1,false);
    $aForm->setDefault('annot',$default);
    $theForm = newForm('Submit', 'POST', 'report3', 'CumEvidenceStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$table=newTable();
    //$sArray=getSpaceArray();
    //$spaces=spaceGen($sArray[$default]);
    //$table->setAttributes('width="100%"');
    //$table->addRow("Annotation Type:",makeAjaxSelect(makeUrl('report3','CumEvidenceSetAnnot'),$annotArray,$default,'formArea','annot'));
    if ($default=='G%')
    {
        $theForm->addSelect('function','Type:',$funx,1,false);
    }
    $theForm->addCoolMultipleSelect('evidence', 'Evidence:', $array, 8,true);
    //$theForm->addMultipleCheckbox('evidence','CumEvidence:',$array,0,' ');
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    $theForm->setDefault('function','5');
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
            setPageTitle("Total Number of {$annotArray[$default]} Annotations for Genes by Evidence Each Month");
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender().'</div>';
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            if ($default=='G%')
            {
                $function=getRequestVarString('function');
                setSessionVar('Function',$function);
                $function=$funx[$function];
            }
            $annot=$default;
            setSessionVar('Annot',$annot);
            $annot=$annotArray[$default];
            //setSessionVar('Function',$function);
            //$function=$funx[$function];
            $evid=getRequestVarArray('evidence');
            setSessionVar('CumEvidence',$evid);
            $num=count($evid);
            for ($e=0;$e<$num;$e++)
            {
                $evid[$e]=$array[$evid[$e]];
            }
            //var_dump($evid);
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            //$toReturn .= $table->toHtml().'<div id="formArea">'.$theForm->quickRender();
            $toReturn.=$aForm->quickRender().$theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumEvidenceByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumEvidencedata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumEvidenceDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $titleadd='';
            $pagetitleadd='';
            foreach ($evid as $e)
            {
                $titleadd.=$e.', ';
                $pagetitleadd.=$e.', ';
            }
            if ($num>2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i+2);
                $pagetitleadd.="or $tytleadd ";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-3);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==2)
            {
                for ($i=strlen($pagetitleadd)-3;$i>-1;$i--)
                {
                    if ($pagetitleadd[$i]==',')
                    {
                        break;
                    }
                }
                //echo $i.br().$pagetitleadd.br();
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $tytleadd=substr($pagetitleadd,$i+2);
                $pagetitleadd=substr($pagetitleadd,0,$i);
                $pagetitleadd.=" or $tytleadd";
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                //$pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2);
                $pagetitleadd.=' ';
            }
            else if ($num==1)
            {
                $titleadd=substr($titleadd,0,strlen($titleadd)-2).' ';
                $pagetitleadd=substr($pagetitleadd,0,strlen($pagetitleadd)-2).' ';
            }
            if (!isset($function))
            {
                $function='Any';
            }
            if ($function=='Any')
            {
                $function='';
            }
            setPageTitle("Total Number of$manual {$pagetitleadd}{$function}$annot Annotations for {$species}Genes Each Month ");
            if ($function=='')
            {
                $function='Any';
            }
            //eco($function,2);
            $function=$morefunx[$function];
            setSessionVar('CumEvidenceTitle',"Tot Num of$manual {$titleadd}{$function}$annot Annotations for {$species}Genes Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumEvidence')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumEvidence'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumEvidenceByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    $evid=getSessionVar('CumEvidence');
    $annot=getSessionVar('Annot');
    if ($annot=='G%')
    {
        $function=getSessionVar('Function');
    }
    //$function=getSessionVar('Function');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count (f.full_annot_key  ) as COUNT from full_annot f , rgd_ids r
        where
        f.annotated_object_rgd_id = r.rgd_id
        and f.rgd_object_key = 1
        and f.evidence in (";
        $start=true;
        foreach ($evid as $e)
        {
            if (!$start)
            {
                $sql.=',';
            }
            $sql.="$e
            ";
            $start=false;
        }
        $sql.=')
         ';
        if ($annot=='G%'&&$function!='5')
        {
            $sql.="and f.aspect = '$function'";
        }
        $sql.="and r.object_status = 'ACTIVE'
        and r.species_type_key in ($species)
        and f.term_acc like '$annot'
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
function report3_CumGWGOAStart()
{
    setSessionVar('CumGWGOArept','CumGWGOA');
    $toReturn='';
    $default=getSessionVarOKEmpty('AnnotFrom');
    if (!isset($default))
    {
        $default='G%';
    }
    $annotations=getAnnotationArrayForDropDown();
    $theForm = newForm('Submit', 'POST', 'report3', 'CumGWGOAStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot','Annotation type:',$annotations,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot',$default);
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Genes with '.$annotations[$default].'  Annotations Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $annot=$annotations[$annot];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumGWGOAByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumGWGOAdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumGWGOADates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of {$species}Genes with$manual $annot Annotations Each Month");
            setSessionVar('CumGWGOATitle',"Total number of {$species}Genes with$manual $annot Annotations Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumGWGOA')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumGWGOA'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumGWGOATemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumGWGOAdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumGWGOAByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $annot=getSessionVar('Annot');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="-- Total Number of rat genes with GO Annotations F5 ( 11605 )
    select count ( unique ( g.gene_key) ) as COUNT   from genes g, rgd_ids r , full_annot f
    where
    g.rgd_id = r.rgd_id
    and g.rgd_id = f.annotated_object_rgd_id
    and r.object_key = 1 -- GENE
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species) -- RAT
    and f.term_acc like '$annot' -- Term is GO";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="-- Total Number of rat genes with GO Annotations F5 ( 11605 )
    select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
    where
    g.rgd_id = r.rgd_id
    and g.rgd_id = f.annotated_object_rgd_id
    and r.object_key = 1 -- GENE
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species) -- RAT
    and f.term_acc like '$annot' -- Term is GO
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="-- Total Number of rat genes with GO Annotations F5 ( 11605 )
        select count ( unique ( g.gene_key) )  as COUNT  from genes g, rgd_ids r , full_annot f
        where
        g.rgd_id = r.rgd_id
        and g.rgd_id = f.annotated_object_rgd_id
        and r.object_key = 1 -- GENE
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species) -- RAT
        and f.term_acc like '$annot' -- Term is GO
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}

function report3_KPStart()
{
    setSessionVar('KPrept','KP');
    $toReturn='';
    $annotations=array("'scrna', 'miscrna', 'snorna', 'snrna',  'rrna', 'trna', 'gene', 'protein-coding'"=>'Known', "'predicted-high', 'predicted-moderate', 'predicted-low', 'predicted-no evidence'"=>'Predicted');
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'KPStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addSelect('annot','Gene Status:',$annotations,1,false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('annot','0');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            setPageTitle('Number of Known and Predicted Genes Added Each Month Within Query Range');
        case SUBMIT_INVALID :
                $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            $annot=getRequestVarString('annot');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            setSessionVar('Annot',$annot);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $annot=$annotations[$annot];
            // $function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getKPByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('KPdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('KPDates',$dateArray);
            //setSessionVar('List',$list);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of $annot {$species}Genes Added$manual Each Month Within Query Range");
            setSessionVar('KPTitle',"Number of $annot {$species}Genes Added$manual Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'KP')).'">'."</br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'KP'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getKPByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    $annot=getSessionVar('Annot');
    // $list = getSessionVarOKEmpty('List');
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(UNIQUE( G.GENE_KEY)) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and
            r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID and r.CREATED_DATE between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('
            ";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where
            r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEy in ($species) and g.GENE_TYPE_LC in ($annot) and r.RGD_ID=g.RGD_ID and r.CREATED_DATE between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('
            ";
        }
        //$sql ="-- Total Number of rat genes with GO Annotations F5 ( 11605 )
        //select count ( unique ( g.gene_key) )    from genes g, rgd_ids r , full_annot f
        //where
        //g.rgd_id = r.rgd_id
        //and g.rgd_id = f.annotated_object_rgd_id
        //and r.object_key = 1 -- GENE
        //and r.object_status = 'ACTIVE'
        //and r.species_type_key = '$species' -- RAT
        //and f.term_acc like '$annot' -- Term is GO
        //and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')";
        //and g.created_by not in (69, 70)";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT'];
    }
    return $toReturn;
}
function report3_CumVariantsStart()
{
    setSessionVar('CumVariantsrept','CumVariants');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'CumVariantsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Gene Variants Each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumVariantsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumVariantsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumVariantsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual {$species}Gene Variants Each Month");
            setSessionVar('CumVariantsTitle',"Total number of$manual {$species}Gene Variants Each Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumVariants')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumVariants'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getCumVariantsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    if (!$pipeline)
    {
        $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    else
    {
        $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID
        and r.created_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
        $sql.="', 'MM-DD-YYYY')";
    }
    
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        if (!$pipeline)
        {
            $sql="select count(*) as COUNT from full_annot f, GENES g, RGD_IDS r where r.rgd_id = f.annotated_object_rgd_id and  r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        else
        {
            $sql="select count(*) as COUNT from GENES g, RGD_IDS r where r.OBJECT_STATUS='ACTIVE' and r.SPECIES_TYPE_KEY in ($species) and g.GENE_TYPE_LC='splice' and r.RGD_ID=g.RGD_ID
            and r.created_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        }
        
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
function report3_PercentAnnotatedStart()
{
    setSessionVar('PercentAnnotatedrept','PercentAnnotated');
    $toReturn='';
    //$array=array('F'=>'Molecular Function','P'=>'Biological Process','C'=>'Cellular Componenent');
    $theForm = newForm('Submit', 'POST', 'report3', 'PercentAnnotatedStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    //$theForm->addSelect('function','Type:',$array,1,false);
    //$theForm->addCoolDate('fromdate','Start date:',1);
    //$theForm->addCoolDate('todate','End date:',1);
    $theForm->setDefault('species', '3');
    //$theForm->setDefault('function','1');
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Percent of Genes Annotations');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            //$fromDate=getRequestVarString('fromdate');
            //$toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $species=getRequestVarString('species');
            //$function=getRequestVarString('function');
            setSessionVar('Species',$species);
            //setSessionVar('Function',$function);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            //$function=$array[$function];
            //setSessionVar('FName',$function);
            $toReturn .= $theForm->quickRender();
            //$dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            //$plotArray=getPercentAnnotatedAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            //setSessionVar('PercentAnnotateddata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            //setSessionVar('Dates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            $toReturn.= "<h2>Percent of genes annotated for $species:\n" . getPercentAnnotated()."</h2>";
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
function getPercentAnnotated()
{
    $species=getSessionVar('Species');
    
    //echo "Hi from inside a function!";
    //$function=getSessionVar('Function');
    //foreach ($dateArray as $)
    //$num=count($dateArray);
    $toReturn=0;
    //for ($i=0;$i<$num-1;$i++)
    //{
    //annotated
    $sql="
    select count ( unique g.gene_key ) from genes g, rgd_ids r , full_annot f
    where
    g.rgd_id = r.rgd_id
    and g.rgd_id = f.annotated_object_rgd_id
    and r.object_key = 1
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)";
    //echo "$sql<br><br>";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result = fetchRecord($sql);
    //dump($result);
    //echo('\n\n');
    $toReturn+=$result['COUNT(UNIQUEG.GENE_KEY)'];
    //}
    //all
    $sql="
    select count ( *)  from genes g, rgd_ids r
    where
    g.rgd_id = r.rgd_id
    and r.object_key = 1
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species) ";
    //echo "$sql<br><br>";
    if (!$pipeline)
    {
        $sql.='
        and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result = fetchRecord($sql);
    //dump($result);
    //echo('\n\n');
    $toReturn/=$result['COUNT(*)'];
    $toReturn*=100;
    $toReturn.='%';
    return $toReturn;
}
function report3_MonthlyAnyAnnotationsStart()
{
    setSessionVar('MonthlyAnyAnnotationsrept','MonthlyAnyAnnotations');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'MonthlyAnyAnnotationsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Number of Ontology Annotations for Genes Each Month Within Query Range');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getMonthlyAnyAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('MonthlyAnyAnnotationsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('MonthlyAnyAnnotationsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Number of$manual Ontology Annotations for {$species}Genes Each Month Within Query Range");
            setSessionVar('MonthlyAnyAnnotationsTitle',"Number of$manual Ontology Annotations for {$species}Genes Each Month Within Query Range");
            $toReturn.='<img src="'.makeUrl('reportGraph','graphPrep',array('tackon'=>'MonthlyAnyAnnotations')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CSVPrep',array('tackon'=>'MonthlyAnyAnnotations'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_MonthlyAnyAnnotationsTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('MonthlyAnyAnnotationsdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getMonthlyAnyAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $toReturn=array();
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count (*)
        from genes g, full_annot f ,  rgd_ids r
        where g.rgd_id = f.annotated_object_rgd_id
        and r.object_status = 'ACTIVE'
        and r.species_type_key  in ($species)
        and g.rgd_id = r.rgd_id
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        if ($i<$num-2)
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]);
        }
        else
        {
            $sql.=date('m-d-Y',$dateArray[$i+1]+5);
        }
        $sql.="', 'MM-DD-YYYY')
        ";
        if (!$pipeline)
        {
            $sql.='
            and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //dump($result);
        //echo('\n\n');
        $toReturn[]=$result['COUNT(*)'];
    }
    return $toReturn;
}
function report3_CumMonthlyAnyAnnotationsStart()
{
    setSessionVar('CumMonthlyAnyAnnotationsrept','CumMonthlyAnyAnnotations');
    $toReturn='';
    $theForm = newForm('Submit', 'POST', 'report3', 'CumMonthlyAnyAnnotationsStart');
    $theForm->addSelect('species', 'Species:', getSpeciesArrayForDropDownAndAll(), 1, false);
    $theForm->addCoolDate('fromdate','Start date:',1);
    $theForm->addCoolDate('todate','End date:',1);
    $theForm->addCheckbox('pipeline', '<b>Check to include data from pipeline:</b>');
    
    $theForm->setDefault('species', '3');
    $theForm->setDefault('fromdate',getLastYear());
    $theForm->setDefault('todate',date('m/d/Y'));
    //$theForm->getState();
    
    /*if ( $theForm->getValue('month') == null ) {
        $theForm->setDefault('month', 0);
    }*/
    //$toReturn = $theForm->quickRender();
    switch ($theForm->getState()) {
        case INITIAL_GET :
            
        case SUBMIT_INVALID :
                setPageTitle('Total Number of Ontology Annotations for Genes each Month');
            $toReturn .= $theForm->quickRender();
            
            break;
        case SUBMIT_VALID :
            $fromDate=getRequestVarString('fromdate');
            $toDate=getRequestVarString('todate');
            $pipe=getRequestVarString('pipeline');
            setSessionVar('Pipeline',$pipe);
            $manual=getManualForTitle($pipe,true);
            $species=getRequestVarString('species');
            setSessionVar('Species',$species);
            //echo "from $fromDate to $toDate";
            $species=getSpeciesNameAndAll($species);
            $toReturn .= $theForm->quickRender();
            $dateArray=getStartOfMonthsByDates($fromDate,$toDate);
            //make in global
            /*$num=count($dateArray);
            for ($i=0;$i<$num;$i++)
            {
                echo date('m/d/Y',$dateArray[$i]);
                echo "\n";
            }*/
            $plotArray=getCumMonthlyAnyAnnotationsByMonth($dateArray,$theForm->getValue('pipeline'));
            //make here
            setSessionVar('CumMonthlyAnyAnnotationsdata', $plotArray);
            //setSessionVar('StartDate',$fromDate);
            //setSessionVar('EndDate',$toDate);
            setSessionVar('CumMonthlyAnyAnnotationsDates',$dateArray);
            //displayQueryResultsAsText($plotArray,$dateArray);
            //$toReturn.=;
            setPageTitle("Total number of$manual Added Ontology Annotations for {$species}Genes-Cumulative by Month");
            setSessionVar('CumMonthlyAnyAnnotationsTitle',"Total number of$manual Added Ontology Annotations for {$species}Genes-Cumulative by Month");
            $toReturn.='<img src="'.makeUrl('reportGraph','CumgraphPrep',array('tackon'=>'CumMonthlyAnyAnnotations')).'">'."</h2></br></br>".makeLink('Export as CSV','reportGraph','CumCSVPrep',array('tackon'=>'CumMonthlyAnyAnnotations'));
            //make here
            break;
    }
    return $toReturn;
    //$toReturn.='Nathan Rocks!';
    //required fields are not requiring
    //return $toReturn;
}
/*function report3_CumMonthlyAnyAnnotationsTemporary()
{
    
    return 'IT WORKS!';
}*/
/*function displayQueryResultsAsText($plotArray,$dateArray)
{
    //$plotArray=getSessionVar('CumMonthlyAnyAnnotationsdata');
    //$dateArray=getSessionVar('Dates');
    $num=count($plotArray);
    for ($i=0;$i<$num;$i++)
    {
        echo date('m/d/Y',$dateArray[$i]);
        echo " to ";
        echo date('m/d/Y',$dateArray[$i+1]);
        echo "-$plotArray[$i] results\n";
        //echo "\n";
    }
}*/
function getCumMonthlyAnyAnnotationsByMonth($dateArray,$pipeline)
{
    $species=getSessionVar('Species');
    
    //foreach ($dateArray as $)
    $num=count($dateArray);
    $dateArray[$num-1]+=5;
    $toReturn=array();
    $sql="select count (*) as COUNT
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id";
    if (!$pipeline)
    {
        $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total=$result['COUNT'];
    //echo $total.'</br>';
    $sql="select count (*) as COUNT
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id
    and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$num-1])."', 'MM-DD-YYYY') and to_date('".date('m-d-Y',gettimeofday(true)+86400);
    $sql.="', 'MM-DD-YYYY')";
    if (!$pipeline)
    {
        $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
    }
    $result=fetchRecord($sql);
    $total-=$result['COUNT'];
    //$toReturn[]=$total;
    //echo $total.'</br>';
    for ($i=0;$i<$num-1;$i++)
    {
        $sql="select count (*) as COUNT
    from genes g, full_annot f ,  rgd_ids r
    where g.rgd_id = f.annotated_object_rgd_id
    and r.object_status = 'ACTIVE'
    and r.species_type_key  in ($species)
    and g.rgd_id = r.rgd_id
        and f.last_modified_date between to_date('".date('m-d-Y',$dateArray[$i])."', 'MM-DD-YYYY') and to_date('";
        /*if ($i==$num-1)
        {
            $sql.=date('m-d-Y',$dateArray[$num-1]);
        }*/
        //else
        //{
        $sql.=date('m-d-Y',$dateArray[$num-1]);
        //}
        $sql.="', 'MM-DD-YYYY')";
        if (!$pipeline)
        {
            $sql.=' and ((f.created_by not in (69,70) or f.created_by is null) and f.evidence!=\'IEA\' and (f.xref_source like \'PMID%\' or f.evidence not in(\'ISS\')))';
        }
        $result = fetchRecord($sql);
        //echo "</br>$sql</br>";
        //dump($result);
        //echo('\n\n');
        //$total-=$result['COUNT(UNIQUE(G.GENE_SYMBOL))'];
        $toReturn[]=$total-$result['COUNT'];
    }
    $toReturn[]=$total;
    //var_dump($toReturn);
    return $toReturn;
}
?>
