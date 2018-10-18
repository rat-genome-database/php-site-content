<?php
/*
 * The following functions are called from the site to generate sitemaps for search engines to get to all 
 * the Genes / QTL's and Strains pages of our site. 
 * These MUST be in a seperate file as the setDirectOutput() is set outside all
 * methods to get the framwwork not to cache echo statements to the browser , thus running the machine
 * out of memory !
 * 
 * 1/2008 by George Kowalski
 * $Revision: 1.1 $
 * $Date: 2008/01/21 20:01:47 $
 * $Header: /var/lib/cvsroot/Development/RGD/rgdCuration/project/modules/siteMap.php,v 1.1 2008/01/21 20:01:47 gkowalski Exp $
 */
 
require_once 'project/modules/searchEngine.php';
setTemplate('CMStemplate'); // Empty template so it does not contain any indexable material itself.
setDirectOutput(); // MUST BE OUTSIDE ALL METHODS ! or OUT OF MEMORY ERRORS , this causes all methods to 
                   // stream output without framework intervention !

/**
 * Run from the shell : 
 * 
 * cd /rgd_home/3.0/WWW
 * wget http://rgd.mcw.edu/rgdCuration/?module=siteMap\&func=generateSiteMapRatGenes -O sitemapratgene.xml
 * gzip sitemapratgene.xml
 * 
 */
function siteMap_generateSiteMapRatGenes() { 

    echo getSiteMapXMLStart();
    $chars = preg_split('//', "abcdefghijklmnopqrstuvwxyz", -1, PREG_SPLIT_NO_EMPTY);
    foreach ( $chars as $char ) {
         getGeneData( $char, 3, 'XML');
         // getGeneData( $char, 1, 'XML');
         //getGeneData( $char, 2, 'XML');
    }
    echo getSiteMapXMLEnd();
  
}
/**
 * 
 */
function siteMap_generateSiteMapQTLS() { 

    //header("Content-type: text/html");
    $ret  = getSiteMapXMLStart();
    $chars = preg_split('//', "abcdefghijklmnopqrstuvwxyz", -1, PREG_SPLIT_NO_EMPTY);
    foreach ( $chars as $char ) {
        $ret .= getQTLData($char , 3, 'XML');
        $ret .= getQTLData($char , 2, 'XML');
        $ret .= getQTLData($char , 1, 'XML');
    }
    $ret .= getSiteMapXMLEnd();
    
    echo $ret; 
  
  }

/**
 * Run with the following: 
 * 
 * cd /rgd_home/3.0/WWW
 * wget http://hastings.rgd.mcw.edu/rgdCuration/?module=siteMap\&func=generateSiteMapStrains -O sitemapstrain.xml
 * gzip sitemapstrain.xml
 */  
function siteMap_generateSiteMapStrains() { 
   
    $ret  = getSiteMapXMLStart();
    $ret .= generateRatLinks('XML');
    $ret .= getSiteMapXMLEnd();
    
    echo $ret; 
  
}
  
  ?>