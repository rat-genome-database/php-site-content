<?php
/*
 * All processing for the PORTAL display  in here. 
 * 5/2007 by George Kowalski
 * $Revision: 1.10 $
 * $Date: 2007/11/28 21:15:32 $
 * $Header: /var/lib/cvsroot/Development/RGD/rgdCuration/project/modules/portal.php,v 1.10 2007/11/28 21:15:32 gkowalski Exp $
 */
 require_once 'project/modules/BrowserChecker.php';
 
 setTemplate('portal_template');
 /**
  * Show the portal page , fillin in the two select statements at the top 
  */
 function portal_show() {
  $toReturn = '';
  $PAGE_CATEGORY_PAGE_DESC = '';
  $portalName = getRequestVarString('name','obesity');
  $portalKey = 0; 
  
  $sql = "select * from portal1 p 
    where  p.url_name = '". $portalName . "'";
  $entry = fetchRecord($sql);
  
  extract($entry ) ; 
  if ( $PAGE_CATEGORY_PAGE_DESC == null ) { 
    $PAGE_CATEGORY_PAGE_DESC = "Unknown";
    $PAGE_SUB_CATEGORY_DESC = "Unknown"; 
  }
  $portalKey = $PORTAL_KEY; 
  // Get top level list of terms for a give portal by name
  $sql = "select pc.portal_cat_id, pc.category_name from 
    portal_cat1 pc , portal_ver1 pv, portal1 p 
    where p.portal_key = pv.portal_key
    and pv.portal_ver_status = 'Active' 
    and pv.portal_ver_id = pc.portal_ver_id
    and p.url_name = '". $portalName . "'
    and pc.parent_cat_id = pc.portal_cat_id order by category_name";
   
   
    $sqlFirstCategorySql = "select pc.PORTAL_CAT_ID, pv.PORTAL_VER_ID from 
    portal_cat1 pc , portal_ver1 pv, portal1 p 
    where p.portal_key = pv.portal_key
    and pv.portal_ver_status = 'Active' 
    and pv.portal_ver_id = pc.portal_ver_id
    and p.url_name = '". $portalName . "'
    and pc.parent_cat_id is NULL ";
    
  $entry = fetchRecord($sqlFirstCategorySql);
  extract( $entry) ; 
  
  $topLevelCatID = $PORTAL_CAT_ID;
  
  $entries = fetchRecords($sql); 

  $values = array(); 
  $topLevelCatID *= -1; // to signify this is the top level term to to the second drop down list. other 
                        // methods will have to take the abs() of this .
                         
  $values += array($topLevelCatID => "All" );
  foreach ( $entries as $entry ) { 
    extract ( $entry ); 
    $values += array($PORTAL_CAT_ID => $CATEGORY_NAME ) ; 
  } 
  // $values = array('1'=>'Arrhythmia', '2'=>'Arterial Occlusive Diseases ', '3'=>'Cardiomegaly', ''=>'');
  $toReturn .= "<table CELLPADDING=\"10\"><tr><td>"; 
  $toReturn .= "<font color='#999999'><em>" . $PAGE_CATEGORY_PAGE_DESC . " :</em></font><br>"; 
 
  $firstAjaxArray   = array('uri' =>  makeUrl('portal','updateGviewer')   , 'div' => 'gviewer'); 
  $secondAjaxArray  = array('uri' =>  makeUrl('portal','categoryChosen')  , 'div' => 'selectDivArea2'); 
  $thirdAjaxArray   = array('uri' =>  makeUrl('portal','updateCategory')  , 'div' => 'category');
  $fourthAjaxArray  = array('uri' =>  makeUrl('portal','clearDivTag')     , 'div' => 'diseasename');
  $fifthAjaxArray   = array('uri' =>  makeUrl('portal','getSummaryTableHTML')     , 'div' => 'summaryTable');
  $sixthAjaxArray   = array('uri' =>  makeUrl('portal','getGenesInfoTableHTML')   , 'div' => 'geneInfo');
  $seventhAjaxArray = array('uri' =>  makeUrl('portal','getQTLInfoTableHTML')     , 'div' => 'qtlInfo');
  $eighthAjaxArray  = array('uri' =>  makeUrl('portal','getStrainInfoTableHTML')  , 'div' => 'strainInfo');
  $ninthAjaxArray   = array('uri' =>  makeUrl('portal','updateRatFlash')          , 'div' => 'rat-flash');
  $tenAjaxArray     = array('uri' =>  makeUrl('portal','updateHumanFlash')        , 'div' => 'human-flash');
  $elevenAjaxArray  = array('uri' =>  makeUrl('portal','updateMouseFlash')        , 'div' => 'mouse-flash');
  $twelthAjaxArray  = array('uri' =>  makeUrl('portal','getDiseaseChartCCFlash')  , 'div' => 'cc-pie');
  $thirteenAjaxArray = array('uri' =>  makeUrl('portal','getDiseaseChartBPFlash') , 'div' => 'bp-pie');
  $fourteenAjaxArray = array('uri' =>  makeUrl('portal','getDiseaseChartMPFlash') , 'div' => 'mp-pie');
  $fifteenAjaxArray  = array('uri' =>  makeUrl('portal','updateSynteny', array('URLName' => 'Rat Synteny', 'synteny' =>'rat'))  , 'div' => 'rat-Synteny');
  $sixteenAjaxArray  = array('uri' =>  makeUrl('portal','updateSynteny', array('URLName' => 'Human Synteny', 'synteny' =>'human'))  , 'div' => 'human-Synteny');
  $seventeenAjaxArray= array('uri' =>  makeUrl('portal','updateSynteny', array('URLName' => 'Mouse Synteny', 'synteny' =>'mouse'))  , 'div' => 'mouse-Synteny');
  
  $firstSelectArray = array ( $firstAjaxArray, $secondAjaxArray, $thirdAjaxArray , $fourthAjaxArray, $fifthAjaxArray, $sixthAjaxArray,$seventhAjaxArray, $eighthAjaxArray,$ninthAjaxArray, $tenAjaxArray , $elevenAjaxArray, $twelthAjaxArray, $thirteenAjaxArray , $fourteenAjaxArray, $fifteenAjaxArray,$sixteenAjaxArray ,$seventeenAjaxArray) ; 
  $toReturn .= makeMultipleAjaxSelect($firstSelectArray, $values, '', 'firstSelect');
   
  $toReturn .= '</td><td>'; 
  $toReturn .= "<font color='#999999'><em>" . $PAGE_SUB_CATEGORY_DESC . "<br><div id='selectDivArea2'><select><option>Please select a category first</option></select></div></td><td><font color='#999999'><em>OR:<br>" . makeLink( "Show all", 'portal','show', array('name' => $portalName)). "</td></tr></table>";
  
   
  $objectURL = ""; 
  return $toReturn . "$objectURL";
  
 }
 
 /**
 * 
 */
function makeMultipleAjaxLink($ajaxArray, $linkName  , $label = null) {
  
  if (isset($label)) {
    static $counter;
    $counter++;
    $id = "$label-$counter";
    $toReturn =  '<a href="javascript:void(0);" name="'.$id.'" id="'.$id.'" onclick="';
    
    foreach ( $ajaxArray as $num => $uriDivArray) { 
      $toReturn .= 'ajaxCheckbox(\''.$uriDivArray['uri'].'\', \''.$uriDivArray['div'] .'\' ); ';
    }
    $toReturn .= '">' . $linkName ."</a>";
  }
//  else {
//    return '<a href="javascript:void(0);" onclick="ajaxCheckbox(\''.$url .'\', \''.$callbackDivName.'\')">' . $linkName ."</a>";
//  }
  return $toReturn;
}
 
 function portal_updateSynteny() {
  setDirectOutput();
  $firstValueSelected = getRequestVarNum('firstSelect');
  $firstValueSelected = abs ( $firstValueSelected ) ;
  $species = getRequestVarString('species');
  $synteny = getRequestVarString('synteny');
  $URLName= getRequestVarString('URLName');
  if ( ! isReallySet( $URLName )) {
    $URLName = "Not Set";
  }
  $secondValueSelected = getRequestVarString('secondSelect');
  if ( isReallySet($firstValueSelected ) && $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
       $valueSelected = "1"; 
    }
    echo makeAjaxLink($URLName, makeUrl("portal",'updateGviewer', array("firstSelect" => $valueSelected, "species"=> $species, "synteny" => $synteny)), 'gviewer');
  
 }
 
 function portal_updateRatFlash() {
 	setDirectOutput();
 	$firstValueSelected = getRequestVarString('firstSelect');
  $firstValueSelected = abs ( $firstValueSelected ) ;
 	$secondValueSelected = getRequestVarString('secondSelect');
 	if ( isReallySet($firstValueSelected ) && $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
       $valueSelected = "1"; 
    }
    $firstAjaxArray   = array('uri' =>  makeUrl('portal','updateGviewer', array('firstSelect' => $valueSelected, 'species' => 'rat')), 'div' => 'gviewer'); 
    $secondAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'rat', 'synteny' => 'rat', 'URLName' => 'Rat Synteny')), 'div' => 'rat-Synteny'); 
    $thirdAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'rat', 'synteny' => 'human', 'URLName' => 'Human Synteny')), 'div' => 'human-Synteny');
    $forthAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'rat', 'synteny' => 'mouse', 'URLName' => 'Mouse Synteny')), 'div' => 'mouse-Synteny');
    $linkArray = array ( $firstAjaxArray, $secondAjaxArray, $thirdAjaxArray, $forthAjaxArray) ;

    echo makeMultipleAjaxLink($linkArray, "Rat",  'firstMultiLink');
 	// echo makeAjaxLink("Rat", makeUrl("portal",'updateGviewer', array("firstSelect" => $valueSelected, "species"=> "rat")), 'gviewer');
 	
 }
 function portal_updateHumanFlash() {
 	setDirectOutput();
 	$firstValueSelected = getRequestVarString('firstSelect');
  $firstValueSelected = abs ( $firstValueSelected ) ;
 	$secondValueSelected = getRequestVarString('secondSelect');
 	if ( isReallySet($firstValueSelected ) && $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
       $valueSelected = "1"; 
    }
 	// echo makeAjaxLink("Human", makeUrl("portal",'updateGviewer', array("firstSelect" => $valueSelected, "species"=> "human")), 'gviewer');
   $firstAjaxArray   = array('uri' =>  makeUrl('portal','updateGviewer', array('firstSelect' => $valueSelected, 'species' => 'human')), 'div' => 'gviewer'); 
    $secondAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'human', 'synteny' => 'rat', 'URLName' => 'Rat Synteny')), 'div' => 'rat-Synteny'); 
    $thirdAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'human', 'synteny' => 'human', 'URLName' => 'Human Synteny')), 'div' => 'human-Synteny');
    $forthAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'human', 'synteny' => 'mouse', 'URLName' => 'Mouse Synteny')), 'div' => 'mouse-Synteny');
    $linkArray = array ( $firstAjaxArray, $secondAjaxArray, $thirdAjaxArray, $forthAjaxArray) ;

    echo makeMultipleAjaxLink($linkArray, "Human",  'firstMultiLink');
 	
 }
 function portal_updateMouseFlash() {
 	setDirectOutput();
 	$firstValueSelected = getRequestVarString('firstSelect');
 	$secondValueSelected = getRequestVarString('secondSelect');
 	if ( isReallySet($firstValueSelected ) && $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
       $valueSelected = "1"; 
    }
 	//echo makeAjaxLink("Mouse", makeUrl("portal",'updateGviewer', array("firstSelect" => $valueSelected, "species"=> "mouse")), 'gviewer');
  
   $firstAjaxArray   = array('uri' =>  makeUrl('portal','updateGviewer', array('firstSelect' => $valueSelected, 'species' => 'mouse')), 'div' => 'gviewer'); 
    $secondAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'mouse', 'synteny' => 'rat', 'URLName' => 'Rat Synteny')), 'div' => 'rat-Synteny'); 
    $thirdAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'mouse', 'synteny' => 'human', 'URLName' => 'Human Synteny')), 'div' => 'human-Synteny');
    $forthAjaxArray   = array('uri' =>  makeUrl('portal','updateSynteny', array('firstSelect' => $valueSelected, 'species' => 'mouse', 'synteny' => 'mouse', 'URLName' => 'Mouse Synteny')), 'div' => 'mouse-Synteny');
    $linkArray = array ( $firstAjaxArray, $secondAjaxArray, $thirdAjaxArray, $forthAjaxArray) ;
  echo makeMultipleAjaxLink($linkArray, "Mouse",  'firstMultiLink');
 	
 }
 /**
  * Returns the Disease Category to be displayed in the Portal page via an AJAX call. 
  * Passes in the "firstSelect" request parameter that coresponds to the PORTAL_CAT_ID
  * selected. 
  */
 function portal_updateCategory() { 
  setDirectOutput();
  $CATEGORY_NAME = '';
  $valueSelected = getRequestVarNum('firstSelect');
  $valueSelected = abs ( $valueSelected ) ;
  
  $values = array();
  $sql = "select category_name from 
    PORTAL_CAT1   
    where portal_cat_id = $valueSelected ";
  $entry = fetchRecord($sql); ; 
  extract ( $entry ); 
  echo $CATEGORY_NAME; 
 }
 
 /**
  * 
  */
  function portal_updateDiseaseName() { 
  setDirectOutput();
  $CATEGORY_NAME = '';
  $valueSelected = getRequestVarString('secondSelect');
  $values = array();
  $sql = "select cat.* from 
    portal_cat1 cat   
    where  portal_cat_id = $valueSelected ";
  $entry = fetchRecord($sql); ; 
  extract ( $entry ); 
  echo ' > ' . $CATEGORY_NAME; 
 }
 
 /** 
  * Used to clear out div tags from an ajax control
  */
 function portal_clearDivTag(){
  setDirectOutput();
  echo "&nbsp"; 
 }
 
 /**
  * Updates the DIV tag ( selectDivArea2 ) to generate the second selection box when the first "secondSelect" contains the value of the PORTAL_CAT_ID chosen by the user
  * Category selection is made. 
  * 
  */
 function portal_categoryChosen() {
  setDirectOutput();
  $valueSelected = getRequestVarNum('firstSelect'); // if this is 1 then we are sleecting "ALL"
  $portalKey = getRequestVarNum('portalKey');
  $values = array();
  if ( $valueSelected < 0  ) { 
    return "<select>
      <option>Please select a category first</option>
      </select>";
//     $sql = "select cat.PORTAL_CAT_ID, cat.CATEGORY_NAME from  
//      portal_cat1 cat 
//      where  cat.parent_cat_id = (" .
//          "select cat.portal_cat_id  from  
//      portal_cat1 cat ,
//      portal_ver1 ver
//      where  cat.parent_cat_id is null 
//      and ver.portal_key = " . $portalKey . " 
//      and ver.portal_ver_status = 'Active'
//      and cat.portal_ver_id = ver.portal_ver_id" .
//          ")  order by CATEGORY_NAME";
  } else { 
      $valueSelected = abs ( $valueSelected ) ; 
      $sql = "select cat.PORTAL_CAT_ID, cat.CATEGORY_NAME from  
      portal_cat1 cat 
      where  cat.parent_cat_id = $valueSelected order by CATEGORY_NAME";
  }
 
  $entries = fetchRecords($sql); 
  $values = array($valueSelected => 'All'); 
  foreach ( $entries as $entry ) { 
    extract ( $entry ); 
    if ( $PORTAL_CAT_ID != $valueSelected ) { 
       $values += array($PORTAL_CAT_ID => $CATEGORY_NAME ) ; 
    } 
  }
  if ( count($values) < 2 ) { 
    // No grandChild terms to display .. no second select box to show....
    echo "&nbsp;";
    return;
  } else { 
  $firstAjaxArray    = array('uri' =>  makeUrl('portal','updateDiseaseName')     , 'div' => 'diseasename');
  $secondAjaxArray   = array('uri' =>  makeUrl('portal','updateGviewer')         , 'div' => 'gviewer');
  $thirdAjaxArray    = array('uri' =>  makeUrl('portal','getSummaryTableHTML')    , 'div' => 'summaryTable');
  $forthAjaxArray    = array('uri' =>  makeUrl('portal','getGenesInfoTableHTML')  , 'div' => 'geneInfo');
  $fifthAjaxArray    = array('uri' =>  makeUrl('portal','getQTLInfoTableHTML')    , 'div' => 'qtlInfo');
  $sixAjaxArray      = array('uri' =>  makeUrl('portal','getStrainInfoTableHTML') , 'div' => 'strainInfo');
  $sevenAjaxArray    = array('uri' =>  makeUrl('portal','updateRatFlash')    , 'div' => 'rat-flash');
  $eightAjaxArray    = array('uri' =>  makeUrl('portal','updateHumanFlash')  , 'div' => 'human-flash');
  $nineAjaxArray     = array('uri' =>  makeUrl('portal','updateMouseFlash')  , 'div' => 'mouse-flash');
  $tenAjaxArray      = array('uri' =>  makeUrl('portal','updateRatFlash')  , 'div' => 'rat-Synteny');
  $elevenAjaxArray   = array('uri' =>  makeUrl('portal','updateHumanFlash')  , 'div' => 'human-Synteny');
  $twelveAjaxArray   = array('uri' =>  makeUrl('portal','updateMouseFlash')  , 'div' => 'mouse-Synteny');
  $thirteenAjaxArray = array('uri' =>  makeUrl('portal','getDiseaseChartCCFlash')  , 'div' => 'cc-pie');
  $fourteenAjaxArray = array('uri' =>  makeUrl('portal','getDiseaseChartBPFlash')  , 'div' => 'bp-pie');
  $fifthteenAjaxArray= array('uri' =>  makeUrl('portal','getDiseaseChartMPFlash')  , 'div' => 'mp-pie');
  $sixteenAjaxArray  = array('uri' =>  makeUrl('portal','updateSynteny', array('URLName' => 'Rat Synteny', 'synteny' =>'rat'))  , 'div' => 'rat-Synteny');
  $seventeenAjaxArray    = array('uri' =>  makeUrl('portal','updateSynteny',array('URLName' => 'Human Synteny', 'synteny' =>'human'))  , 'div' => 'human-Synteny');
  $eightteenAjaxArray  = array('uri' =>  makeUrl('portal','updateSynteny',array('URLName' => 'Mouse Synteny', 'synteny' =>'mouse'))  , 'div' => 'mouse-Synteny');
  $ajaxArray = array($firstAjaxArray, $secondAjaxArray, $thirdAjaxArray , $forthAjaxArray, $fifthAjaxArray, $sixAjaxArray, $sevenAjaxArray,$eightAjaxArray,$nineAjaxArray, $tenAjaxArray, $elevenAjaxArray, $twelveAjaxArray, $thirteenAjaxArray,$fourteenAjaxArray, $fifthteenAjaxArray , $sixteenAjaxArray, $seventeenAjaxArray, $eightteenAjaxArray) ; 
  echo makeMultipleAjaxSelect($ajaxArray, $values, '', 'secondSelect');
  }
   
  
}
/**
 * Called to update the gviewer div tag in portal page. 
 * 
 */
function portal_updateGviewer() {
  setDirectOutput();
  $valueSelected = "1";
  $objectURL = ''; 
  $species = getRequestVarString('species');
  $synteny = getRequestVarString('synteny');
  if ( (! isReallySet($species )) || $species == '' ) { 
      $species = 'rat';
  }
  if ( ! isReallySet($synteny)) {
    $synteny = $species;
  }
  // used to see if we should display gviewer even if the user has execced max allowed objects. 
  $proceed = getRequestVarString('proceed');
  $baseMapURL = '';
  $firstValueSelected = getRequestVarNum('firstSelect');
  $firstValueSelected = abs( $firstValueSelected ) ; 
  $secondValueSelected = getRequestVarNum('secondSelect');
  // echo " firstValueSelected:" .  $firstValueSelected .":<br>";
  //echo " secondValueSelected:" .  $secondValueSelected .":<br>";
  
  // Get Portal name for includion on gviewer Too Complicatedt to pass around the name to each function
  // for now. 
  $portalName = ""; 
//  $pName = getRequestVarString('name');
//  $sqlPortal = "select FULL_NAME from PORTAL1 where URL_NAME = '" . $pName . "'";
//  // echo $sqlPortal;
//  $myresult = fetchRecord($sqlPortal);
//  extract ( $myresult ) ;
//  if ( $myresult != NULL ) {
//        $portalName =  $FULL_NAME; 
//  }
  
 
  switch ( $species . $synteny){
  	case "ratrat":
  		$baseMapURL = '/GViewer/data/rgd_rat_ideo.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_RAT';
  	break;
    case "rathuman":
      $baseMapURL = '/GViewer/data/rat-human_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_RAT';
    break;
    case "ratmouse":
      $baseMapURL = '/GViewer/data/rat-mouse_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_RAT';
    break;
  	case "humanhuman":
  		$baseMapURL = '/GViewer/data/human_ideo.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_HUMAN';
  	break;
    case "humanrat":
      $baseMapURL = '/GViewer/data/human-rat_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_HUMAN';
    break;
    case "humanmouse":
      $baseMapURL = '/GViewer/data/human-mouse_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_HUMAN';
    break;
  	case "mousemouse":
  		$baseMapURL = '/GViewer/data/mouse_ideo.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_MOUSE';
    break;
    case "mouserat":
      $baseMapURL = '/GViewer/data/mouse-rat_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_MOUSE';
    break;
    case "mousehuman":
      $baseMapURL = '/GViewer/data/mouse-human_synteny.xml';
      $dbColumnToCheck = 'ANNOT_OBJ_CNT_W_CHILDREN_MOUSE';
    break;
  }
  echo "Species: " .  $species ." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Synteny: $synteny<br>";
  if ( isReallySet($firstValueSelected ) && $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
  }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
  }else { 
       $valueSelected = "1"; 
  }
  // echo " valueSelected:" .  $valueSelected .":<br>";
  // Test for Too much data and display empty GViewer with warning message if
  // to many results . 
  $sql = "select ". $dbColumnToCheck. " from 
    portal_cat1  
    where portal_cat_id = $valueSelected";

    $numResults  = fetchField($sql);
    $skipFlag = false;
    if ( $numResults > 7000 && $proceed != 'true' ) { 
      $skipFlag = true;
      $dataToReturn = '';
      //$urlarray = array('uri' =>  makeUrl('portal','updateGviewer', array('proceed'=> 'true')), 'div' => 'gviewer'); 
      $link = makeAjaxLink('Here', makeUrl('portal','updateGviewer', array('proceed'=> 'true',  'species' => $species, 'firstSelect' => $firstValueSelected )), 'gviewer');
      $objectURL = "<br>Too much data to display in Gviewer at this level... You may click ". $link . " to proceed, but be aware that the resulting download may slow down your browser.";
    } else { 
      $dataToReturn = urlencode( getGviewerXML($valueSelected, $species));
    }
    
  $annotationURL = "/rgdCuration/?module=portal&func=showAnnotations"; 
  $gviewerURL = "/GViewer/GViewer2.swf";
    $check = new BrowserChecker();
  if ($check->browser_detection( 'browser' ) == "ie") {
    
    $objectURL .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="400" id="GViewer2" align="middle">
           <param name="allowScriptAccess" value="sameDomain" />
           <param name="movie" value="/GViewer/GViewer2.swf" />
           <param name="quality" value="high" />
           <param name="bgcolor" value="#FFFFFF" />
           <param name="FlashVars" value="&lcId=1234567890&baseMapURL=' . $baseMapURL . '&annotationXML=' . $dataToReturn . ' &titleBarText='. $portalName. '&dimmedChromosomeAlpha=40&bandDisplayColor=0x0099FF&wedgeDisplayColor=0xCC0000&browserURL=/fgb2/gbrowse/rgd_904/?label=AQTLS%26label=ARGD_curated_genes%26name=Chr&" />      
  </object>'; 
  } else { 
    $objectURL .= '<embed src="' . $gviewerURL . '" quality="high" bgcolor="#FFFFFF" width="800" height="400" name="GViewer2" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" FlashVars="&bandDisplayColor=0x0099FF&lcId=1234567890&baseMapURL=' . $baseMapURL . '&annotationXML=' . $dataToReturn . '&titleBarText=' . $portalName . '&dimmedChromosomeAlpha=40&wedgeDisplayColor=0xCC0000&browserURL=/fgb2/gbrowse/rgd_904/?label=AQTLS%26label=ARGD_curated_genes%26name=Chr&"  pluginspage="http://www.macromedia.com/go/getflashplayer" /> '; 
  }
  
  
  echo $objectURL;
  
}

/** 
 * This function Not used !
 */
function portal_showAnnotations() {
  setDirectOutput();
  $counter = 1; 
    echo '<?xml version=\'1.0\' standalone=\'yes\'?><genome>';
    for ( $i = 1 ; $i < 5 ; $i++ ) {
      $start =  rand(1, 100000000);  
      $chrom = rand( 1, 20);
      $end = $start + rand(10000, 70000) ;
      $type = rand ( 0,1 ) ; 
      if ( $type == 0 ) { 
       $typeStr = "gene"; 
      } else {
        $typeStr = "qtl"; 
      }
      echo '<feature>';
      echo '<chromosome>' . $chrom.  '</chromosome>';  
      echo '<start>' . $start.  '</start>';
      echo '<end>' . $end.  '</end>';
      echo '<type>' . $typeStr.  '</type>';
      echo '<color>0x79CC3D</color>';
      echo '<label>Adora' . $i.  '</label>';
      echo '<link>/generalSearch/RgdSearch.jsp?quickSearch=1&searchKeyword=2051</link>';
      echo '</feature>';
    }
   echo '</genome>'; 

  
}
/**
 * 
 */
function portal_testXML() { 
  setDirectOutput();
  echo getGviewerXML(1); 
}


/**
 * 
 */
function getGviewerXML( $portalID, $species) {
  // $returnString = getAnnotationsXML();
  switch ( $species){
  	case 'rat':
	  	$gviewDataColumn = 'GVIEWER_XML_RAT';
	  	break;
  	case 'human':
  		$gviewDataColumn = 'GVIEWER_XML_HUMAN';
  		break;
  	case 'mouse':
  		$gviewDataColumn = 'GVIEWER_XML_MOUSE';  	
  }
   $sql = "select ". $gviewDataColumn. " from 
    portal_cat1  
    where portal_cat_id = $portalID";

    $xmldata  = fetchField($sql);
  return  $xmldata ; 
}

/**
 * Returns XML data for Flash GViewer to display. 
 * Documentation at: http://www.gmod.org/flashgviewer
 */
function getAnnotationsXML() {
  $returnStr = ''; 
  $counter = 1; 
  $returnStr .= '<?xml version=\'1.0\'';
  $returnStr .= 'standalone=\'yes\'?>' . "\n" . '<genome>'. "\n";
    for ( $i = 1 ; $i < 1000 ; $i++ ) {
      $start =  rand(1, 100000000);  
      $chrom = rand( 1, 20);
      $end = $start + rand(10000, 70000) ;
      $type = rand ( 0,1 ) ; 
      if ( $type == 0 ) { 
       $typeStr = "gene"; 
      } else {
        $typeStr = "qtl"; 
      }
      $returnStr .= '<feature>' . "\n";
      $returnStr .= '<chromosome>' . $chrom.  '</chromosome>'. "\n";  
      $returnStr .= '<start>' . $start.  '</start>'. "\n";
      $returnStr .= '<end>' . $end.  '</end>'. "\n";
      $returnStr .= '<type>' . $typeStr.  '</type>'. "\n";
      $returnStr .= '<color>0x79CC3D</color>'. "\n";
      $returnStr .= '<label>Adora' . $i.  '</label>'. "\n";
      $returnStr .= '<link>/generalSearch/RgdSearch.jsp?quickSearch=1&searchKeyword=2051</link>';
      $returnStr .= '</feature>'. "\n";
    }
   $returnStr .= '</genome>'. "\n";
   
   //$newfile = fopen('/tmp/output.gk', "w+");
      
   //fputs($newfile, $returnStr);
   //fclose($newfile);

  return $returnStr; 
  
  
}

/**
 * Returns the Genes HTML table to be rendered in the DIV tag of the "Genes Info" table below the 
 * flash gviewer
 */
function portal_getGenesInfoTableHTML() { 
    setDirectOutput(); 
    
    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) {
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No Genes Found.";
      return;
    }
      
    $sql = "select GENE_INFO_HTML from 
      portal_cat1  
      where portal_cat_id = $valueSelected";

    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Genes Found."; 
    }
    echo  $xmldata ;
//  $returnHTML  = '<table cellpadding="1">
//      <tbody>
//      <tr>' ; 
//  $returnHTML  .= '<td width="100">
//      <a href="/tools/genes/genes_view.cgi?id=2004" target="new">A2m</a>
//      </td>
//      <td width="100">
//      <a href="/tools/genes/genes_view.cgi?id=735738&species=1" target="new">A2M</a>
//      </td>
//      <td width="100">
//      <a href="/tools/genes/genes_view.cgi?id=10050&species=2" target="new">A2m</a>
//      </td>
//      </tr>';
//   $returnHTML  .= '</table>';
//      echo $returnHTML; 
}

/**
 * 
 */
function portal_getQTLInfoTableHTML() { 
    setDirectOutput(); 
    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No QTLS Found.";
      return;
    }
    
    $sql = "select QTL_INFO_HTML from 
      portal_cat1  
      where portal_cat_id = $valueSelected";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No QTLS Found."; 
    }
    echo $xmldata ; 
//  $returnHTML  = '<table cellpadding="1">
//      <tbody>
//      <tr>' ; 
//  $returnHTML  .= '<td width="100">' .
//      '<a href="/objectSearch/qtlReport.jsp?rgd_id=631217" target="new">Activ1</a>
//      </td>
//      <td width="100">
//      <a href="/objectSearch/qtlReport.jsp?rgd_id=1357360" target="new">COHEN1_H</a>
//      </td>
//      <td width="100">
//      </td>
//      </tr>';
//   $returnHTML  .= '</table>';
//      echo $returnHTML; 
}

/**
 * 
 */
function portal_getStrainInfoTableHTML() { 
    setDirectOutput(); 
    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) {
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No Strains Found.";
      return;
    }
    $sql = "select STRAIN_INFO_HTML from 
      portal_cat1  
      where portal_cat_id = $valueSelected";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Strains Found."; 
    }
   
    echo   $xmldata ;  
//  $returnHTML  = '<table cellpadding="1">
//      <tbody>
//      <tr>' ; 
//  $returnHTML  .= '<td width="100">' .
//      '<a href="/tools/strains/strains_view.cgi?id=737892" target="new">ACI/SegHsd</a>
//      </td>
//      <td width="100">
//      </td>
//      <td width="100">
//      </td>
//      </tr>';
//   $returnHTML  .= '</table>';
//      echo $returnHTML; 
}

/**
 * Returns the Summary Table HTML to be placed in the Portal page by an AJAX request. 
 * Called with firstSelect or SecondSelect parameters based on if called from first or
 * second option select. 
 */
function portal_getSummaryTableHTML() { 
  $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) {
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No Data Found.";
      return;
    }
    setDirectOutput();
    $sql = "select SUMMARY_TABLE_HTML from 
      portal_cat1  
      where portal_cat_id = $valueSelected";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }
    echo   $xmldata ; 
  
//  $rand = rand(100, 200) ; 
//  echo '<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
//          <tr bgcolor="#EEEEEE">
//            <td><p>&nbsp;<strong>Summary</strong><br>
//                <img src="../../common/images/shim.gif" width="160" height="1"></p></td>
//            <td align="right"><p>Rat&nbsp;&nbsp;<br>
//            <img src="../../common/images/shim.gif" width="60" height="1"></p></td>
//            <td align="right"><p>Human&nbsp;&nbsp;<br>
//            <img src="../../common/images/shim.gif" width="70" height="1"></p></td>
//            <td align="right"><p>Mouse&nbsp;&nbsp;<br>
//            <img src="../../common/images/shim.gif" width="70" height="1"></p></td>
//            <td width="100%"><p>&nbsp;<strong>&nbsp;</strong></p></td>
//          </tr>
//          <tr align="right">
//            <td bgcolor="#FFFFFF"><p>Genes&nbsp;</p></td>
//            <td bgcolor="#FFFFFF"><span id="genes-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="human-genes-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="mouse-genes-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td rowspan="3" bgcolor="#FFFFFF">   </td>
//          </tr>
//          <tr align="right">
//            <td bgcolor="#FFFFFF"><p>QTLs&nbsp;</p></td>
//            <td bgcolor="#FFFFFF"><span id="qtls-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="human-qtls-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="mouse-qtls-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//          </tr>
//          <tr align="right">
//            <td bgcolor="#FFFFFF"><p>Strains&nbsp;</p></td>
//            <td bgcolor="#FFFFFF"><span id="strains-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="human-strains-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//            <td bgcolor="#FFFFFF"><span id="mouse-strains-sum">' . $rand . '</span>&nbsp;&nbsp;</td>
//          </tr>
//          </table>';
  
}
function portal_getDiseaseChartBPFlash() {
    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
     $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) {
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No QTLS Found.";
      return;
    }
   setDirectOutput();
    $sql = "select CHART_XML_BP from 
      portal_cat1  
      where portal_cat_id  = $valueSelected";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }
   $xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 
}

function portal_getDiseaseChartMPFlash() {
    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ;
    $secondValueSelected = getRequestVarString('secondSelect');
    if ( $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No QTLS Found.";
      return;
    }
   setDirectOutput();
    $sql = "select CHART_XML_MP from 
      portal_cat1  
      where portal_cat_id  = $valueSelected";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }
   $xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 

  }

/*
 * Called from first and second drop down lists within the portal, Not from the Body onload, which calls 
 * portal_getHomeChartCCFlash() because it only knows the PortalName / PortalVerID
 */
function portal_getDiseaseChartCCFlash() { 

    $valueSelected = ""; 
    $firstValueSelected = getRequestVarNum('firstSelect');
    $firstValueSelected = abs ( $firstValueSelected ) ; 
    $secondValueSelected = getRequestVarString('secondSelect');
    //echo ":" . $firstValueSelected . ":<br>";
    //echo ":" . $secondValueSelected . ":";
    if ( $firstValueSelected != 0 ) { 
      $valueSelected = $firstValueSelected; 
    }elseif ( isReallySet( $secondValueSelected )) { 
      $valueSelected = $secondValueSelected; 
    }else { 
      echo "No QTLS Found.";
      return;
    }
   setDirectOutput();
    $sql = "select CHART_XML_CC from 
      portal_cat1  
      where portal_cat_id  = $valueSelected";
    $xmldata  = fetchField($sql);

    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }

   $xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 
   
}
/**
 * Returns Chart Flash object for Cellular Component and data for Gene Ontology Overview from the Body Onload call . 
 * This is not called when diseases are choosen. 
 */
function portal_getHomeChartCCFlash() { 
   $portalVerID = getRequestVarString('portalVerID');
   
   setDirectOutput();
    $sql = "select CHART_XML_CC from 
      portal_ver1  
      where portal_ver_id = $portalVerID";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
      return;
    }
   $xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 
   
}
/**
 * Returns Chart Flash object for Biological Process and data for Gene Ontology Overview from the Body Onload call . 
 * This is not called when diseases are choosen. 
 */
function portal_getHomeChartBPFlash() { 
   $portalVerID = getRequestVarString('portalVerID');
   $species = getRequestVarString('species');
   $synteny = getRequestVarString('synteny');
   
  
   setDirectOutput();
    $sql = "select CHART_XML_BP from 
      portal_ver1  
      where portal_ver_id = $portalVerID";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }
   $xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 
   
}
/**
 * Returns Chart Flash object for Moecular Function and data for Gene Ontology Overview from the Body Onload call . 
 * This is not called when diseases are choosen. 
 */
function portal_getHomeChartMPFlash() { 
   $portalVerID = getRequestVarString('portalVerID');
   setDirectOutput();
    $sql = "select CHART_XML_MP from 
      portal_ver1  
      where portal_ver_id = $portalVerID";
    $xmldata  = fetchField($sql);
    if (  ! isReallySet( $xmldata  )) { 
      $xmldata = "No Data Found."; 
    }
//    $xmldata1 = "<graph caption='Georgey Phenotype' decimalPrecision='0' xAxisName='Ontology Term' yAxisName='Number of annotations' showNames='0' showValues = '0' pieRadius='70'> <set name='protein binding' value='108' color='CCFF99' /> <set name='receptor activity' value='20' color='FF3399' /> <set name='calcium ion binding' value='15' color='99CCCC' /> <set name='nucleotide binding' value='14' color='CCCC33' /> <set name='transcription factor activity' value='14' color='66CC00' /> <set name='DNA binding' value='8' color='66CCFF' /> <set name='hydrolase activity' value='8' color='CC6699' /> <set name='transferase activity' value='7' color='66FF99' /> <set name='protein kinase activity' value='6' color='CC33FF' /> </graph> " ; 
    
$xmldataRet = "<embed width='300' height='160' src=\"/rgdCuration/charts/FC_2_3_Column2D.swf?&chartHeight=160&chartWidth=280&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>
<embed width=\"300\" height=\"120\" src=\"/rgdCuration/charts/FC_2_3_SSGrid.swf?&chartHeight=120&chartWidth=270&alternateRowBgColor=CCCC00&alternateRowBgAlpha=10&numberItemsPerPage=5&dataXML=" .$xmldata . " \" type=\"application/x-shockwave-flash\"/>";
    
    echo   $xmldataRet ; 
   
}

?>
