<?php
/*
 * The following functions are called from the site to generate links for search engines to get to all 
 * the Genes / QTL's and Strains pages of our site. Drop a link to searchEngine_showAll() to 
 * generate all links 
 * 1/2008 by George Kowalski
 * $Revision: 1.3 $
 * $Date: 2008/01/18 22:27:50 $
 * $Header: /var/lib/cvsroot/Development/RGD/rgdCuration/project/modules/searchEngine.php,v 1.3 2008/01/18 22:27:50 gkowalski Exp $
 */
 
 setTemplate('CMStemplate'); // Empty template so it does not contain any indexable material itself.
 
 /**
  * Generate a sitemap in XML to be saved to the root filesystem of RGD. See http://www.sitemaps.org
  * or https://www.google.com/webmasters/tools/docs/en/protocol.html for details on thix XML doc's 
  * format. An External program will have to call this URL , ans save the file to sitemap.xml 
  */
  
  function getSiteMapXMLStart() { 
    return '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  }
  
  function getSiteMapXMLEnd() { 
    return "\n</urlset>";
  }

  function searchEngine_showAll() {
    setPageTitle("Alphabetical Listing of RGD Genes,  QTLs, and Strains - Rat Genome Database");
    $ret = "<h1>Alphebetical Listing of  RGD Genes,  QTLs, and Strains</h1>";
    $ret .= "<h2>" . makeLink('All Rat Genes', 'searchEngine', 'ratGenes'). "</h2>";
    $ret .= "<br><h2>" . makeLink('All Human Genes', 'searchEngine', 'humanGenes');
    $ret .= "</h2><br><h2>" . makeLink('All Mouse Genes', 'searchEngine', 'mouseGenes');
    
    $ret .= "</h2><br><h2>" . makeLink('All Rat QTLS', 'searchEngine', 'ratQTLS');
    $ret .= "</h2><br><h2>" . makeLink('All Human QTLS', 'searchEngine', 'humanQTLS');
    $ret .= "</h2><br><h2>" . makeLink('All Mouse QTLS', 'searchEngine', 'mouseQTLS');
    $ret .= "</h2><br><h2>" . makeLink('ALL Rat Strains', 'searchEngine', 'ratStrains'). "</h2>";
    return "$ret";
   }
 
  /**
  * 
  */
  function searchEngine_ratGenes() {
      return generateTopGeneLinks( 3 , true);  
   }
  
  /**
   * 
   */ 
  function searchEngine_humanGenes() {
       return generateTopGeneLinks( 1, true ); 
  }
  
  /**
   * 
   */
  function searchEngine_mouseGenes() {
       return generateTopGeneLinks( 2, true ); 
  }
  
  /**
   * 
   */
  function generateTopGeneLinks($speciesID, $returnHeader = true) {
   $ret = "";
     if ( $returnHeader ) { 
        setPageTitle("Alphabetical Gene Listing - Rat Genome Database");
        $ret .= " <h2>All RGD Genes begininning with letter:</h2><br> ";
      } else {
        $ret .= " Other Alphabetic Gene Lists : ";
     }
      $ret .= " ( " ;
      $chars = preg_split('//', "abcdefghijklmnopqrstuvwxyz", -1, PREG_SPLIT_NO_EMPTY);
      foreach ( $chars as $char ) {
        $ret .= "  " . makeLink(strtoupper ($char ) , 'searchEngine', 'geneDetails' , array("fl" => $char, "s" => $speciesID)) . "," ;
      }
      return $ret . " ) " ;
      
      
   } 
   
  /**
   * Dump links to all gene pages by species and firstLetter passed in 
   */
  function searchEngine_geneDetails() {
 
   $firstLetter = getRequestVarString('fl');
   $speciesID = getRequestVarNum("s");
   setPageTitle("Genes starting with the letter ".  strtoupper ($firstLetter ) . " - Rat Genome Database");
   $ret = " <h2>RGD Genes starting with " .  strtoupper ($firstLetter ) . "</h2>" ; 
   $ret .= generateTopGeneLinks($speciesID, false ) . "<br><br>"; 
    
   $ret .= getGeneData( $firstLetter, $speciesID , 'HTML' ); 
   
   return $ret; 
  }
  
  /**
   * 
   */
   function getGeneData (  $firstLetter, $speciesID , $format = 'HTML' ) {
    
    $ret = "";
    $sql =  "SELECT 
      g.rgd_id, g.gene_symbol  
      FROM 
          genes g , 
          rgd_ids r 
      WHERE 
          g.rgd_id = r.rgd_id 
          and r.OBJECT_STATUS = 'ACTIVE'
      and r.SPECIES_TYPE_KEY = " . $speciesID . "
      and upper(gene_symbol) like upper('" . $firstLetter . "%')  ";
      
    $entries = fetchRecords($sql ) ;
    
     foreach ($entries as $entry) {
      extract($entry);
      if ( $format == 'HTML' ) { 
        $ret .= "<a href='/rgdweb/report/gene/main.html?id=" . $RGD_ID . "'>RGD Gene Report: " . $GENE_SYMBOL . " (RGDID: ". $RGD_ID . ")</a><br><br>";
      } else { 
        // XML format
        echo '<url><loc>/rgdweb/report/gene/main.html?id=' . $RGD_ID. '</loc>';
        echo '<changefreq>weekly</changefreq>
<priority>0.9</priority>
</url>\n';
      }
     } 
     return $ret; 
     
  }
  
  
  function searchEngine_ratQTLS($format = 'HTML') {
      return generateTopQTLLinks( 3 , $format); 
  }
  
  function searchEngine_humanQTLS($format = 'HTML') {
       return generateTopQTLLinks( 1, $format ); 
  }
  
  function searchEngine_mouseQTLS($format = 'HTML') {
      return generateTopQTLLinks( 2, $format ); 
  }
   
  /**
   * Generate top level links to QTL pages with or without header to be included in other pages. 
   */
  function generateTopQTLLinks($speciesID, $returnHeader = true ) {
    
    $ret = "";
    if ( $returnHeader  ) { 
        setPageTitle("Alphabetical QTL Listing - Rat Genome Database");
        $ret = " <h2>All RGD QTLs begininning with letter:</h2><br>";
    } else {
         $ret .= " Other Alphabetic QTL Lists : ";
    }
    
    $ret .= " ( " ;
    $chars = preg_split('//', "abcdefghijklmnopqrstuvwxyz", -1, PREG_SPLIT_NO_EMPTY);
    foreach ( $chars as $char ) {
        $ret .= "  " . makeLink(strtoupper ($char ) , 'searchEngine', 'qtlDetails' , array("fl" => $char, "s" => $speciesID)) . "," ;
    }
    return $ret . " ) " ;
    
   } 
    
   /**
   * Dump links to all gene pages by species and firstLetter passed in 
   */
  function searchEngine_qtlDetails() {
  
   $ret = "";
   $firstLetter = getRequestVarString('fl');
   $speciesID = getRequestVarNum("s");
   
    setPageTitle("RGD QTLs starting with the letter ".  strtoupper ($firstLetter ) . " - Rat Genome Database" );

    $ret .= " <h2>RGD QTL's starting with " .  strtoupper ($firstLetter ) . "</h2>" ; 
    $ret .= generateTopQTLLinks($speciesID, false ) . "<br><br>"; 
   
    $ret .= getQTLData( $firstLetter, $speciesID , 'HTML' ); 
    return $ret;
  }
  
  /**
   * 
   */
  function getQTLData ( $firstLetter, $speciesID , $format = 'HTML' ) { 
    
   $ret = "";
   $sql =  "SELECT 
      q.rgd_id , q.qtl_symbol
      FROM 
          qtls q , 
          rgd_ids r 
      WHERE 
          q.rgd_id = r.rgd_id 
          and r.OBJECT_STATUS = 'ACTIVE'
      and r.SPECIES_TYPE_KEY = ". $speciesID . "
      and upper(qtl_symbol) like upper('" . $firstLetter . "%')" .
      "order by q.qtl_symbol"; 
      
      
    $entries = fetchRecords($sql ) ;
    
     foreach ($entries as $entry) {
      extract($entry);
      if ( $format == 'HTML' ) { 
        $ret .= "<a href='/objectSearch/qtlReport.jsp?rgd_id=" . $RGD_ID . "'>RGD QTL Report: " . $QTL_SYMBOL . " (RGDID: ". $RGD_ID  . ")</a> <br><br>"; 
      } else { 
        // XML format
        $ret .= '<url>
<loc>/objectSearch/qtlReport.jsp?rgd_id=' . $RGD_ID. '</loc>
<changefreq>weekly</changefreq>
<priority>0.9</priority>
</url>' . "\n";
     
      } 
     } 
     return $ret; 
     
  }
  
  /**
   * Just display links to strains pages directly, only 1200 of them...
   */
  function searchEngine_ratStrains() {
    return generateRatLinks();
  }
  
  function generateRatLinks($format = 'HTML') { 
    
    $ret = "";
    if ( $format == 'HTML' ) { 
      setPageTitle("Alphabetical Strain Listing - Rat Genome Database" );
      $ret = " <h2>All RGD Registered Rat Strains</h2> "; 
    } 
    

     $sql =  "SELECT 
          s.rgd_id , s.strain_symbol, s.full_name
      FROM 
          strains s , 
          rgd_ids r 
      WHERE 
          s.rgd_id = r.rgd_id 
          and r.OBJECT_STATUS = 'ACTIVE'" .
     " order by s.strain_symbol"; 
      
      
    $entries = fetchRecords($sql ) ;
    
     foreach ($entries as $entry) {
      extract($entry);
      if ( $format == 'HTML' ) { 
        $ret .= "<a href='/rgdweb/report/strain/main.html?id=" . $RGD_ID . "'>" . $STRAIN_SYMBOL . "</a>";
        if ( isReallySet ( $FULL_NAME )) {
          $ret .= " - " . $FULL_NAME;
         }
        $ret .= "<br><br>"; 
     } else { 
        // XML format
        $ret .= '<url>
<loc>/rgdweb/report/strain/main.html?id=' . $RGD_ID. '</loc>
<changefreq>weekly</changefreq>
<priority>0.9</priority>
</url>' . "\n";
     
      } 
    }
    return $ret;
  }



 ?>
