<?php

/**
 * $Revision: 1.27 $
 * $Date: 2007/06/12 18:06:35 $
 * Created by George Kowalski 

 */
// dont you dare put any white space outside the php tags!... messes with the redirect!!
function curationPanel_contents() {
	$pmb = new PhpMyBorder(); // or new PhpMyBorder(true), read about stylesheet-support below

	$toReturn = '';
  $moduleName = getModuleName();


	if (!userLoggedIn()) {
		$authorize_url = 'https://github.com/login/oauth/authorize?'.http_build_query([
                    'client_id' => 'ee483d03b1806882b4b2',
                    'redirect_uri' => 'https://dev.rgd.mcw.edu/rgdCuration/',
                    'scope' => 'user',
                  ]);
		$toReturn .= $pmb->begin_round();
		$toReturn .= '<p><a href="'.$authorize_url.'">Log In</a></p>';
		$toReturn .= '<br/>';
		$toReturn .= $pmb->end_round();

	}
  
  // Only show this tool when in curation and certain sub-tools set by the Module we're in. 
  if ( $moduleName == null || $moduleName != "curation") { 
    return $toReturn;
  }
	// Curation Links and buttons here
	if (userLoggedIn() && ( userIsCurator() || userIsAdmin())) {
		$toReturn .= '<br>';
		$toReturn .= $pmb->begin_round();
		//Title
		$toReturn .= '<b>Curation Tool</b>';

		// Select Objects Button

		$toReturn .= "<table width=90% border=0 ><tr><td><fieldset ><legend>Core Objects</legend>";

		$geneObjectsArrayInSession = getBucketItems('GENE_OBJECT_BUCKET');
		$qtlObjectsArrayInSession = getBucketItems('QTL_OBJECT_BUCKET');
		$strainsObjectsArrayInSession = getBucketItems('STRAIN_OBJECT_BUCKET');
    $sslpObjectsArrayInSession = getBucketItems('SSLP_OBJECT_BUCKET');
		$termArrayInSession = getBucketItems('TERM_OBJECT_BUCKET');
		$referenceArrayInSession = getBucketItems('REFERENCE_OBJECT_BUCKET');

		// $toReturn .= count($geneObjectsArrayInSession) + count ( $qtlObjectsArrayInSession )  . " found<br>";
		// Session contains array of assoiciative arrays with RGD_ID => GeneName and objectType => 'G'
		if (count($geneObjectsArrayInSession) > 0 or $qtlObjectsArrayInSession > 0 or $strainsObjectsArrayInSession > 0) {

			// Process Genes first
			/////////////////////////////////
			if ($geneObjectsArrayInSession > 0) {
				foreach ($geneObjectsArrayInSession as $key => $value) {
					$geneName = NULL;
					$objectName = NULL;
					$objectType = NULL;
          $rgdID = NULL;
          $geneType = NULL;
          $species = NULL;
					foreach ($value as $subkey => $subvalue) {
						if ($subkey == $key) {
							$geneName = $subvalue;
						}
						if ($subkey == "objectType") {
							$objectType = $subvalue;
						}
            if ($subkey == "rgdID") {
              $rgdID = $subvalue;
            }
						if ($subkey == "geneType") {
              if ( $subvalue != 'gene' ) { 
							$geneType = ' (' . $subvalue . ')' ;
              }
						}
            if ($subkey == "name") {
              $objectName = $subvalue;
            }
						if ($subkey == "aliasesHtml") {
							$aliasesHtml = $subvalue;
						}
            if ($subkey == "species") {
              $species = $subvalue;
            }
					}
          $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object" >', "curation", "clearSessionObjects",  array("rgdId" => $rgdID)) ; 
          $toReturn .=  '&nbsp;<img src="icons/page_white_edit.png" border=0 alt="Edit" title="Edit Description" onClick="clearPopupPanel1('. $rgdID . ')"/>&nbsp;'; 
          $toReturn .= hrefOverlib(overLibArgs("<b>$species Gene Symbol: </b><div class=padleft> $geneName $geneType </div><br><b>Name:</b><div class=padleft>$objectName</div><br><b>Aliases:</b><div class=padleft> $aliasesHtml</div><br><b>RGDID:</b><div class=padleft>$rgdID</div>", "CENTER"), "G&nbsp;:&nbsp;" . $geneName, makeRgdQueryURL($rgdID));
           
					 $toReturn .= "<br>";
					
				}
			}

			//QTL's next to display
			////////////////////////////////

			if ($qtlObjectsArrayInSession > 0) {
				foreach ($qtlObjectsArrayInSession as $key => $value) {
					$qtlSymbol = NULL;
					$objectName = NULL;
					$objectType = NULL;
          $chromosome = NULL;
          $rgdID = NULL;
          $species = NULL;
					foreach ($value as $subkey => $subvalue) {
						if ($subkey == $key) {
							$qtlSymbol = $subvalue;
						}
            if ($subkey == "rgdID") {
              $rgdID = $subvalue;
            }
						if ($subkey == "objectType") {
							$objectType = $subvalue;
						}
						if ($subkey == "name") {
							$objectName = $subvalue;
						}
            if ($subkey == "chromosome") {
              $chromosome = $subvalue;
            }
            if ($subkey == "species") {
              $species = $subvalue;
            }
					}
					// $toReturn .= $objectType . " : " . makeGeneralSearchLink( $key ,  $qtlSymbol ) . "<br>";
          $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object">&nbsp;', "curation", "clearSessionObjects",  array("rgdId" => $rgdID)) ; 
					$toReturn .= hrefOverlib(overLibArgs("<b>$species QTL Symbol:</b><br><div class=padleft> $qtlSymbol </div><br><b>Chromosome:</b><div class=padleft>$chromosome</div><br><b>Name:</b><div class=padleft>$objectName </div><br><b>RGDID:</b><div class=padleft> $rgdID</div>", "CENTER") , "Q&nbsp;:&nbsp;" . $qtlSymbol, makeRgdQueryURL($rgdID));
           
					$toReturn .= "<br>";
				}
			}

			// Strains
			/////////////////////////////////////////
			if ($strainsObjectsArrayInSession > 0) {
				foreach ($strainsObjectsArrayInSession as $key => $value) {
					$strainSymbol = NULL;
					$objectName = NULL;
					$objectType = NULL;
          $aliasesHtml = NULL;
          $rgdID = NULL;
          $species = NULL;
					foreach ($value as $subkey => $subvalue) {
						if ($subkey == $key) {
							$strainSymbol = $subvalue;
						}
            if ($subkey == "rgdID") {
              $rgdID = $subvalue;
            }
						if ($subkey == "objectType") {
							$objectType = $subvalue;
						}
						if ($subkey == "name") {
							$objectName = $subvalue;
						}
            if ($subkey == "aliasesHtml") {
              $aliasesHtml = $subvalue;
            }
            if ($subkey == "species") {
              $species = $subvalue;
            }
					}
					// $toReturn .= $objectType . " : " . makeGeneralSearchLink( $key ,  truncateString( $strainSymbol, 18 )  ) . "<br>";
           $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object">&nbsp;', "curation", "clearSessionObjects",  array("rgdId" => $rgdID)) ; 
					$toReturn .= hrefOverlib(overLibArgs("<b>$species Strain:</b><div class=padleft>  $strainSymbol </div><br><b>Aliases:</b><div class=padleft> $aliasesHtml </div><br><b>RGDID:</b><div class=padleft> $rgdID</div>", "CENTER") , "S&nbsp;:&nbsp;" . truncateString($strainSymbol, 18), makeRgdQueryURL($rgdID)   );
          
					$toReturn .= "<br>";
				}
			}
      

	  
      // SSLPS
      /////////////////////////////////////////
      if ($sslpObjectsArrayInSession > 0) {
        foreach ($sslpObjectsArrayInSession as $key => $value) {
          $homologSymbol = NULL;
          $objectName = NULL;
          $objectType = NULL;
          $rgdID = NULL;
          $rgdID = NULL;
          $species = NULL; 
          foreach ($value as $subkey => $subvalue) {
            if ($subkey == $key) {
              $sslpSymbol = $subvalue;
            }
            if ($subkey == "rgdID") {
              $rgdID = $subvalue;
            }
            if ($subkey == "objectType") {
              $objectType = $subvalue;
            }
            if ($subkey == "species") {
              $species = $subvalue;
            }
            
          }
          // $toReturn .= $objectType . " : " . makeGeneralSearchLink( $key ,  truncateString( $strainSymbol, 18 )  ) . "<br>";
           $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object">&nbsp;', "curation", "clearSessionObjects",  array("rgdId" => $rgdID)) ; 
          $toReturn .= hrefOverlib(overLibArgs("<b>$species SSLP Symbol:</b><br><div class=padleft> $sslpSymbol </div><br><b>RGDID:</b><div class=padleft> $rgdID</div>", "CENTER") , "SS&nbsp;:&nbsp;" . $sslpSymbol, makeRgdQueryURL($rgdID) );
          
          $toReturn .= "<br>";
        }
      }
      

		$toReturn .= '<script type="text/javascript"> ' . "\n";
		$toReturn .= '<!-- ' . "\n";
		$toReturn .= 'function myPopupPanel1(rgdID) { ' . "\n";
		$toReturn .= 'window.open( "?module=curation&func=updateGeneDescriptionPopup&rgd_id=".concat(rgdID.toString()), "myWindow", ' . "\n";
		$toReturn .= '"status = 1,height=350,width=575,resizable=1,scrollbars=1,dependent=1" ) ' . "\n";
		$toReturn .= '} ' . "\n";
		$toReturn .= 'function clearPopupPanel1(rgd_id) { ' . "\n";
		$delayedURL = 'window.location="'. makeUrl("curation", "clearSessionObjects") ;
		//		$toReturn .= $delayedURL;
//		$toReturn .= 'setTimeout(\'' . $delayedURL . '\'.concat("&rgdId=").concat(rgd_id.toString()).concat(\'";\'), 500);';
		$toReturn .= 'myPopupPanel1(rgd_id);';
		//		$toReturn .= 'setTimeout(\'' . $delayedURL . '\'.concat("&rgdId=").concat(rgd_id.toString()) . \'";' . '\', 500);';
		//$toReturn .= 'myPopup2(rgd_id.toString());';
		$toReturn .= '} ' . "\n";
		$toReturn .= '//--> ' . "\n";
		$toReturn .= '</script>' . "\n";
			$toReturn .= "<center>" . makeLink('<img src="icons/basket_delete.png" border=0 alt="Clear All" title="Clear All">', "curation", "clearSessionObjects");
			$toReturn .= '&nbsp;<img src="icons/page_white_edit.png" border=0 alt="Edit All" title="Edit All" onClick="clearPopupPanel1(0)"></center>';
		} else {
			$toReturn .= "None Selected";
		}

		$toReturn .= "</fieldset></td></tr></table>";

		$toReturn .= makeLink('Select Objects', 'curation', 'selectObjects');
		$toReturn .= "<br/><br/>";

		// Select Terms Button
		$toReturn .= "<table width=90% border=0 ><tr><td><fieldset><legend>Ontology Terms</legend>";
		if ($termArrayInSession > 0) {
			foreach ($termArrayInSession as $termID => $value) {
				$termAcc = NULL;
				$termName = NULL;
				$termAspect = NULL;
				$aliasesHtml = NULL;
				
				foreach ($value as $subkey => $subvalue) {
					if ($subkey == "TERM_ACC") {
						$termAcc = $subvalue;
					}
					else if ($subkey == "TERM") {
						$termName = $subvalue;
					}
					else if ($subkey == "ASPECT") {
						$termAspect = $subvalue;
					}
					else if ($subkey == "aliasesHtml") {
						$aliasesHtml = $subvalue;
					}
				}
       $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object">&nbsp;', "curation", "clearSessionTerms",  array("termAcc" => $termAcc)) ; 
				$toReturn .= hrefOverlib(overLibArgs("<b>Term:</b><div class=padleft> $termName </div><br><b>Aspect:</b><div class=padleft> $termAspect </div><br><b>Term Acc:</b><div class=padleft> $termAcc </div><br><b>Synonyms:</b><div class=padleft> $aliasesHtml </div>", "CENTER"), truncateString('['.$termAspect.'] '.$termName, 24), makeRgdQueryURL($termAcc));
        
				$toReturn .= "<br>";
			}
			$toReturn .= "<center>" . makeLink('<img src="icons/basket_delete.png" border=0 alt="Clear All" title="Clear All">', "curation", "clearSessionTerms", "curation", "clearSessionTerms") . "</center>";
		} else {
			$toReturn .= "None Selected";
		}
		$toReturn .= "</fieldset></td></tr></table>";
		$toReturn .= makeLink('Select Terms', 'curation', 'selectTerms');
		$toReturn .= "<br/><br/>";

		// Select References Button
		$toReturn .= "<table width=90% border=0 ><tr><td><fieldset><legend>References</legend>";
		if ($referenceArrayInSession > 0) {
			foreach ($referenceArrayInSession as $key => $value) {

				$rgdID = NULL;
				$pubMedID = NULL;
				$title = NULL;
				$citation = NULL;
				foreach ($value as $subkey => $subvalue) {
					if ($subkey == "rgdID") {
						$rgdID = $subvalue;
					}
					if ($subkey == "pubMedID") {
						$pubMedID = $subvalue;
					}
					if ($subkey == "title") {
						$title = str_replace('"', '/', $subvalue);
					}
					if ($subkey == "citation") {
						$citation = str_replace('"', '/', $subvalue);
					}
				}
          $toReturn .=  makeLink('<img src="icons/basket_remove.png" border=0 alt="Remove" title="Remove Object">&nbsp;', "curation", "clearSessionReferences",  array("rgdId" => $rgdID)) ; 
        $toReturn .= hrefOverlib(overLibArgs( "<b>PubMed ID:</b> $pubMedID  <br><br><b>Reference:</b><div class=padleft> $title</div><br><b>Citation:</b><div class=padleft> $citation</div>", "CENTER", "WIDTH", "300" ), "RGD:" . $rgdID, makeRgdQueryURL( $rgdID )  );
       
				$toReturn .= "<br>";

			}
			$toReturn .= "<center>" . makeLink('<img src="icons/basket_delete.png" border=0 alt="Clear All" title="Clear All">', "curation", "clearSessionReferences") . "</center>";
		} else {
			$toReturn .= "None Selected";
		}
		$toReturn .= "</fieldset></td></tr></table>";
		$toReturn .= makeLink('Select References', 'curation', 'selectReferences');
		$toReturn .= "<br/><br/>";

		// Select Make Association Button
		$toReturn .= makeLink('Associations', 'curation', 'makeAss');

		// Make Celar All button
		$toReturn .= "<br/><br/>";
		$toReturn .= makeLink('Clear Everything', 'curation', 'clearAllCurationBuckets');

		$toReturn .= $pmb->end_round();
	}
  
	return $toReturn;
}
// dont you dare put any white space outside the php tags!... messes with the redirect!!
?>
