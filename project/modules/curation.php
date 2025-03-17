<?php


/**
 * Curation Functions 
 * $Revision: 1.55 $
 * $Date: 2007/09/12 20:24:31 $
 * Created by George Kowalski 
 */

/**
 *  The following is the "homepage" of the curation tool 
 */
function curation_contents() {
  return file_get_contents('staticHtml/curation.html', true);
} 

/**
 
 * Changed by WLiu 5/6/2010
 */
function curation_selectObjects() {
	setPageTitle("Select Core Objects");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$toReturn = '';
	$toReturn = 'You have the following options:<p>';
	$toReturn .= '<P><FIELDSET><LEGEND> Search for an object </legend>';
	$theForm = newForm('Search for Object', 'GET', 'curation', 'searchObjects');
	$theForm->addText('objectName', 'Enter an Object name or RGDID', 12, 80, true);
	$theForm->addSelect('matchType', 'Search where:', getSearchMatchType(), true);
	$theForm->addRadio('objectType', 'Object Type:', getObjectArraySearch(), true);
	$theForm->setDefault('objectType', 14); //Gene & homolog
	$theForm->setDefault('matchType', 'equals');
	$theForm->setInitialFocusField('objectName');
	$toReturn .= $theForm->quickRender();
	$toReturn .= '</LEGEND></FIELDSET>';

	$toReturn .= '<p><center>-- OR --</center><p>';
	$toReturn .= '<P><FIELDSET><LEGEND> Create a new object </legend>';
	$theForm2 = newForm('Create Object', 'GET', 'curation', 'createObjects');
	$theForm2->addText('objectName', 'Enter an Object name ', 12, 80, true);
	$theForm2->addRadio('objectType', 'Object Type:', getObjectArray(), true);
	$theForm2->setDefault('objectType', 1); //Gene
	$toReturn .= $theForm2->quickRender();
	$toReturn .= '</LEGEND></FIELDSET>';

	switch ($theForm->getState()) {
		case INITIAL_GET :
			return $toReturn;
		case SUBMIT_INVALID :
			redirectWithMessage(' Missing field. Please fill in the missing field and resubmit. ', makeUrl('curation', 'selectObjects'));
			break;
		case SUBMIT_VALID :
			// we should not do processing here as we have three forms on one page. They should all jump of to different
			// functions for processing . 
			redirectWithMessage(' Method call invalid. See a programmer as they screwed up.  ');
			break;
		default :
			return $toReturn;

	}
}

/**
 * Called from selectObjects() generated form. 
 */
function curation_searchObjects() {

	setPageTitle("Search Core Objects");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$theForm = newForm('Search for Object', 'GET', 'curation', 'searchObjects');
	$theForm->addText('objectName', 'Enter an Object name or RGDID', 12, 80, true);
	$theForm->addSelect('matchType', 'Search where:', getSearchMatchType(), true);
	$theForm->addRadio('objectType', 'Object Type:', getObjectArraySearch(), true);
	// $theForm->setDefault('objectType', 1); //Gene
	// $theForm->setDefault('matchType', 'contains'); 

	$objectName = getRequestVarString('objectName');
	$objectType = getRequestVarNum('objectType');
	$matchType = getRequestVarString('matchType');

	// every result screen gets these 
	$toReturn = ' Process Term search : ' . $objectName;
	// Used in passing the search back to the original search page for display after selection

	$urlSearchArray = array (
		"objectName" => $objectName,
		"objectType" => $objectType,
		"matchType" => $matchType
	);

	switch ($theForm->getState()) {

		case SUBMIT_INVALID :
			$toReturn = $theForm->quickRender();
			redirectWithMessage(' Missing field. Please fill in the missing field and resubmit. ', makeUrl('curation', 'searchObjects'));
			break;
		case INITIAL_GET :
		case SUBMIT_VALID :
			if ($objectType == '1') { //gene search
				setPageTitle("Rat Genes Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (3));
			}
			elseif ($objectType == '4') { //gene search
                setPageTitle("Chinchilla Genes Found");
                $toReturn = $theForm->quickRender();
                $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (4));
            }
            elseif ($objectType == '9') { //gene search
                setPageTitle("Pig Genes Found");
                $toReturn = $theForm->quickRender();
                $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (9));
            }
            elseif ($objectType == '7') { //gene search
                setPageTitle("Squirrel Genes Found");
                $toReturn = $theForm->quickRender();
                $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (7));
            }
            elseif ($objectType == '16') { //gene search
                setPageTitle("Dog Genes Found");
                $toReturn = $theForm->quickRender();
                $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (6));
            }
            elseif ($objectType == '8') { //gene search
                setPageTitle("Bonobo Genes Found");
                $toReturn = $theForm->quickRender();
                $toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (5));
            }
			elseif ($objectType == '3') { //SSLP search
				setPageTitle("SSLP's Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforSSLPsByName($objectName, $urlSearchArray, $matchType);
			}
			elseif ($objectType == '5') { //strain search
				setPageTitle("Strain's Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforStrainsByName($objectName, $urlSearchArray, $matchType);
			}
			elseif ($objectType == '6') { //qtl search
				setPageTitle("QTL's Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforQTLsByName($objectName, $urlSearchArray, $matchType);

			}
			elseif ($objectType == '13') { //Human/Mouse search
				setPageTitle("Human / Mouse Genes Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforGenesByName($objectName, $urlSearchArray, $matchType, array (1,2)); 
			}
			elseif ($objectType == '14') { //Gene & homolog search
				setPageTitle("Rat / Human / Mouse Genes Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforGeneAndOrthologByName($objectName, $urlSearchArray, $matchType, 3, '1,2');
			}	
			elseif ($objectType == '15') { //Chinchilla & Orthologs search
				setPageTitle("Chinchilla / Human Genes Found");
				$toReturn = $theForm->quickRender();
				$toReturn .= doSearchforGeneAndOrthologByName($objectName, $urlSearchArray, $matchType, 4, '1');
			} else {
				$toReturn = ' Search not implemented for objectType = ' . $objectType;
				$toReturn = $theForm->quickRender();
			}
			return $toReturn;

			break;
		default :
			return $toReturn;
	}

}

/**
 * Add a Strain to the Bucket
 */
function curation_addStrainToBucket() {
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$objectName = getRequestVarString('objectName');
	$objectType = getRequestVarString('objectType');
	$matchType = getRequestVarString('matchType');
	$rgdID = getRequestVarString('RGD_ID');
	$result = fetchRecord("select s.strain_symbol, s.full_name, r.species_type_key from strains s , rgd_ids r   where s.rgd_id = " . $rgdID . ' and s.rgd_id = r.rgd_id ');
	extract($result);

	// Get Aliases
	$aliasesHtml = addSlashes(getAliasesInHtml($rgdID, 'strain'));
	$species = getSpeciesName($SPECIES_TYPE_KEY);
	$storedResult = array (
		$rgdID => $STRAIN_SYMBOL,
		'rgdID' => $rgdID,
		'name' => $FULL_NAME,
		'objectType' => 'S',
		'aliasesHtml' => $aliasesHtml,
		'species' => $species
	);
	// Add the objectName to the URL we go back so it will show the same search as before the user
	// selected the object to add.  
	$urlArray = array (
		"objectName" => $objectName,
		"objectType" => $objectType,
		"matchType" => $matchType
	);

	addItemToBucket('STRAIN_OBJECT_BUCKET', $rgdID, $storedResult);
	$howmany = count(getBucketItems('STRAIN_OBJECT_BUCKET'));

	redirectWithMessage(' Added Strain: ' . $STRAIN_SYMBOL, makeUrl('curation', 'searchObjects', $urlArray));

}

/**
 * Add a QTL to the Bucket
 */
function curation_addQTLToBucket() {
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$objectName = getRequestVarString('objectName');
	$objectType = getRequestVarString('objectType');
	$matchType = getRequestVarString('matchType');
	$rgdID = getRequestVarString('RGD_ID');
	$result = fetchRecord("select q.qtl_symbol, q.qtl_name , q.chromosome, r.species_type_key from qtls q, rgd_ids r where q.rgd_id = " . $rgdID . ' and q.rgd_id = r.rgd_id ');
	extract($result);

	$species = getSpeciesName($SPECIES_TYPE_KEY);
	$storedResult = array (
		$rgdID => $QTL_SYMBOL,
		'rgdID' => $rgdID,
		'name' => $QTL_NAME,
		'chromosome' => $CHROMOSOME,
		'objectType' => 'Q',
		'species' => $species
	);
	// Add the objectName to the URL we go back so it will show the same search as before the user
	// selected the obejct to add.  
	$urlArray = array (
		"objectName" => $objectName,
		"objectType" => $objectType,
		"matchType" => $matchType
	);

	addItemToBucket('QTL_OBJECT_BUCKET', $rgdID, $storedResult);
	$howmany = count(getBucketItems('QTL_OBJECT_BUCKET'));

	redirectWithMessage(' Added QTL: ' . $QTL_SYMBOL, makeUrl('curation', 'searchObjects', $urlArray));

}

/** 
 * Add selected Gene to the GENE Bucket
 */
function curation_addGeneToBucket() {
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$objectName = getRequestVarString('objectName');
	$objectType = getRequestVarString('objectType');
	$matchType = getRequestVarString('matchType');
	$rgdIDs = getRequestVarArray('RGD_ID');
	if (sizeof($rgdIDs) == 0) {
		$rgdIDs = array();
		$rgdIDs[] = getRequestVarString('RGD_ID');
	}
	$ids = "";
	
	foreach ($rgdIDs as $rgdID) {
		$result = fetchRecord("select g.gene_symbol, g.gene_desc, g.full_name, g.gene_type_lc, r.species_type_key from genes g, rgd_ids r where g.rgd_id = " . $rgdID . ' and g.rgd_id = r.rgd_id ');
		extract($result);
		
		// Get Aliases
		$aliasesHtml = getAliasesInHtml($rgdID, 'gene');
		$species = getSpeciesName($SPECIES_TYPE_KEY);
		$storedResult = array (
				$rgdID => $GENE_SYMBOL,
				'rgdID' => $rgdID,
				'name' => $FULL_NAME,
				'objectType' => 'G',
				'geneType' => $GENE_TYPE_LC,
				'geneDesc' => $GENE_DESC,
				'aliasesHtml' => $aliasesHtml,
				'species' => $species
		);
		
		// Add the objectName to the URL we go back so it will show the same search as before the user
		// selected the obejct to add.
		$urlArray = array (
				"objectName" => $objectName,
				"objectType" => $objectType,
				"matchType" => $matchType
		);
		
		
		addItemToBucket('GENE_OBJECT_BUCKET', $rgdID, $storedResult);
		$ids .= $rgdID . " ";
		$howmany = count(getBucketItems('GENE_OBJECT_BUCKET'));
	}

	redirectWithMessage(' Added Gene: ' . $ids, makeUrl('curation', 'searchObjects', $urlArray).'#'.$rgdID);

}

/**
 * Add a reference to it's own bucket. 
 */
function curation_addReferenceToBucket() {

	$rgdID = getRequestVarString('RGD_ID');

	_addReferenceToBucket($rgdID);

	redirectWithMessage(' Added Reference: ' . $rgdID, makeUrl('curation', 'selectReferences'));
}

function _addReferenceToBucket($refRgdId) {

	$result = fetchRecord("select r.ref_key, r.title, r.citation, r.rgd_id, x.acc_id   from references r left outer join rgd_acc_xdb x on r.rgd_id = x.rgd_id where r.rgd_id = " . $refRgdId);
	extract($result);
	$storedResult = array (
		'rgdID' => $refRgdId,
		'title' => $TITLE,
		'ref_key' => $REF_KEY,
		'pubMedID' => $ACC_ID,
		'citation' => $CITATION
	);
	// $refArray = array ( $rgdID, $storedResult ) ; 

	addItemToBucket('REFERENCE_OBJECT_BUCKET', $refRgdId, $storedResult);
}

/** 
 * Drop all Session Core Objects if called without RGDID = $rgdIDToDrop
 */
function curation_clearSessionObjects() {
	$rgdID = getRequestVarNum('rgdId');
	if ($rgdID == 0) {
		emptyBucketItems('GENE_OBJECT_BUCKET');
		emptyBucketItems('QTL_OBJECT_BUCKET');
		emptyBucketItems('STRAIN_OBJECT_BUCKET');
		emptyBucketItems('SSLP_OBJECT_BUCKET');
		redirectWithMessage('Dropped all Core Objects ', makeUrl('curation', 'selectObjects'));
	} else {
		removeItemFromBucket('GENE_OBJECT_BUCKET', $rgdID);
		removeItemFromBucket('QTL_OBJECT_BUCKET', $rgdID);
		removeItemFromBucket('STRAIN_OBJECT_BUCKET', $rgdID);
		removeItemFromBucket('SSLP_OBJECT_BUCKET', $rgdID);
		redirectWithMessage('Dropped Object ', makeUrl('curation', 'selectObjects'));
	}

}

/** 
 * Drop all Session Terms
 */
function curation_clearSessionTerms() {
	$termAcc = getRequestVarString('termAcc');
	if (strlen($termAcc)==0) {
		emptyBucketItems('TERM_OBJECT_BUCKET');
		redirectWithMessage('Dropped All Terms ', makeUrl('curation', 'selectTerms'));
	} else {
		removeItemFromBucket('TERM_OBJECT_BUCKET', $termAcc);
		redirectWithMessage('Dropped one Term ', makeUrl('curation', 'selectTerms'));
	}

}

/** 
 * Drop all Session References
 */
function curation_clearSessionReferences() {
	$rgdID = getRequestVarNum('rgdId');
	if ($rgdID == 0) {
		emptyBucketItems('REFERENCE_OBJECT_BUCKET');
		redirectWithMessage('Dropped All References ', makeUrl('curation', 'selectReferences'));
	} else {
		removeItemFromBucket('REFERENCE_OBJECT_BUCKET', $rgdID);
		redirectWithMessage('Dropped One Reference', makeUrl('curation', 'selectReferences'));
	}

}

function curation_clearAllCurationBuckets() {

	emptyBucketItems('REFERENCE_OBJECT_BUCKET');
	emptyBucketItems('TERM_OBJECT_BUCKET');
	emptyBucketItems('GENE_OBJECT_BUCKET');
	emptyBucketItems('QTL_OBJECT_BUCKET');
	emptyBucketItems('STRAIN_OBJECT_BUCKET');
	emptyBucketItems('SSLP_OBJECT_BUCKET');

	redirectWithMessage('Dropped all objects, terms and references in memory. Ready to start again. ', makeUrl('curation', 'makeAss'));
}

/** 
 * Drop all Session Terms
 */
function curation_addReferences() {
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	setPageTitle("Add Reference");
	$referanceResult = fetchRecords("select * from reference_types ");
	foreach ($referanceResult as $referenceRow) {
		extract($referenceRow);
		$referenceTypeArray[$REFERENCE_TYPE] = $REF_TYPE_DESC;
	}
	$publicationResult = fetchRecords("select distinct ( publication )as publication from references ");
	foreach ($publicationResult as $publicationRow) {
		extract($publicationRow);
		$publications[$PUBLICATION] = $PUBLICATION;
	}
	$theForm = newForm('Add Reference', 'GET', 'curation', 'addReferences');
	$theForm->addText('title', 'Title', 20, 20, true);
	$theForm->addText('author', 'Author List', 20, 20, true);
	$theForm->addTextArea('abstract', 'abstract', 7, 55, 1024, true);
	$theForm->addText('volumn', 'Volumn', 20, 20, true);
	$theForm->addSelect('publication', 'Publication', $publications, true);
	$theForm->addText('newpub', 'or Enter a new Publication', 40, 150, false);
	$theForm->addText('issue', 'Issue', 20, 20, true);
	$theForm->addText('pub_status', 'Status', 40, 200, true);
	$theForm->addText('pages', 'Pages', 20, 20, true);
	$theForm->addCoolDate('pub_data', 'Publication Date', true);
	$theForm->addText('citation', 'Citation', 20, 20, true);
	$theForm->addText('editors', 'Editors', 20, 20, true);
	$theForm->addText('publisher', 'Publisher', 20, 20, true);
	$theForm->addSelect('ref_type', 'Reference Type', $referenceTypeArray, true);
	$theForm->addText('pub_city', 'City of Publication', 20, 20, true);
	$theForm->addText('url_web_ref', 'URL Reference', 20, 20, false);
	$theForm->addTextArea('notes', 'Notes', 7, 55, 1024, true);

	switch ($theForm->getState()) {
		case INITIAL_GET :
			return "<h2> Create A New Reference</h2>" . $theForm->quickRender();
		case SUBMIT_INVALID :
			break;
		case SUBMIT_VALID :
			redirectWithMessage('Process Term search');
			break;
		default :
			return $theForm->quickRender();

			redirectWithMessage('Sorry, cannot do this at this time, give us two weeks ;-> . ', makeUrl('curation', 'selectReferences'));
	}
}

/** 
 * Perform Search for Ontology Term For New Ontology Tables
 */
function doSearchForTermsByName($searchTerm, $ontology) {
	$toReturn = '';
	
	// get rid of whitespace from beginning and end of search term
	$searchTerm = trim($searchTerm);
	
	$searchTermAll = cleanUpAndHandleMatching($searchTerm, 'contains');

	$sql = 'SELECT term,term_acc,is_obsolete,aspect FROM ont_terms v,ontologies o WHERE v.ont_id=o.ont_id AND v.is_obsolete = 0 and( upper (v.term) like ' . dbQuoteString(strtoupper($searchTermAll)) . ' OR term_acc like ' . dbQuoteString(strtoupper($searchTerm)) . ' ) ';
	// dump( $ontology ); 
	switch ($ontology) {
		case 'go' :
			$sql .= " AND v.ont_id IN('BP','MF','CC')";
			break;
		case 'rdo' : // RGD disease ontology
			$sql .= " AND v.ont_id='RDO'";
			break;
		case 'do' : // disease ontology
			$sql .= " AND v.ont_id='DO'";
			break;
		case 'nbo' :
			$sql .= " AND v.ont_id='NBO'";
			break;
		case 'po' : // mammalian phenotype
			$sql .= " AND v.ont_id='MP'";
			break;
		case 'hp' : // human phenotype
			$sql .= " AND v.ont_id='HP'";
			break;
		case 'pw' : // pathway
			$sql .= " AND v.ont_id='PW'";
			break;
		case 'vt' :
			$sql .= " AND v.ont_id='VT'";
			break;
		case 'rs' :
			$sql .= " AND v.ont_id='RS'";
			break;
		case 'cmo' :
			$sql .= " AND v.ont_id='CMO'";
			break;
		case 'mmo' :
			$sql .= " AND v.ont_id='MMO'";
			break;
		case 'xco' :
			$sql .= " AND v.ont_id='XCO'";
			break;
		case 'chebi' :
			$sql .= " AND v.ont_id='CHEBI'";
            break;
		default :
			// Don't add anything for Any saerch of "all"
	}
	$sql .= ' ORDER BY DECODE(LOWER(term), ' . dbQuoteString(strtolower($searchTerm)) . ', 0, 1), NVL(aspect,\'Z\'), LOWER(term)';
	// dump ( $sql ) ; 

	$terms = fetchRecords($sql);
	$table = newTable('Term ', 'Aspect', 'Term Acc', 'Synonyms', hrefOverlib("'Active Term (Green) <br>Obsolete Term ( Yellow) ', CENTER", 'Status'), 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;

	foreach ($terms as $term) {

		// htmlEscapeValues($gene);
		extract($term);

		$tmparray['searchTerm'] = $searchTerm;
		$tmparray['aspect'] = $ASPECT;
		$tmparray['termAcc'] = $TERM_ACC;
		$tmparray['ontology'] = $ontology;
		$table->addRow($TERM, $ASPECT, $TERM_ACC, getTermSynonymHtml($TERM_ACC), makeTermStatusLink($IS_OBSOLETE) , makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addTermToBucket', $tmparray));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;

}

/** 
 * Called from Term selection screen as produced in doSearchforTermsByName() function
 */
function curation_addTermToBucket() {

	$objectName = getRequestVarString('searchTerm');
	$ontology = getRequestVarString('ontology');
	$termAcc = getRequestVarString('termAcc');
	$aspect = getRequestVarString('aspect');
	$termSynonymHtml = getTermSynonymHtml($termAcc);
	$singleSql = "SELECT v.*,o.aspect FROM ont_terms v,ontologies o WHERE v.term_acc='$termAcc' and v.ont_id=o.ont_id";
	$results = fetchRecords($singleSql);
	$urlArray = array (
		"objectName" => $objectName,
		"ontology" => $ontology,
		"hiddenXYZ123" => ""
	);
	
	
	if (count($results) == 1) {

		$term_is_obsolete = $results[0]['IS_OBSOLETE'];
		if( $term_is_obsolete > 0 ) {
			redirectWithMessage('Term ' . $termAcc . ' is OBSOLETE!', makeUrl('curation', 'selectTerms', $urlArray));
			return;
		}
		
		$sqlNot4Curation = "select COUNT(0) CNT from ont_synonyms where synonym_name='Not4Curation' and term_acc='$termAcc'";
		$results2 = fetchRecords($sqlNot4Curation);
		$cntx = $results2[0]['CNT'];
		if ( $cntx > 0 ) {
			redirectWithMessage('Term ' . $termAcc . ' is Not4Curation!', makeUrl('curation', 'selectTerms', $urlArray));
			return;
		}
		
		// extract($results[0]);
		$termArray = $results[0];
		$termArray['aliasesHtml'] = $termSynonymHtml;
		//    $termArray = array ( 
		//      'term_acc' => $TERM_ACC,
		//      'aspect' => $ASPECT,
		//      'term_name' => $TERM,
		//      'aliasesHtml' => $termSynonymHtml  
		//    ) ; 
		addItemToBucket('TERM_OBJECT_BUCKET', $termArray['TERM_ACC'], $termArray);
		if ($objectName != null) {
			redirectWithMessage('Term ' . $termAcc . ' successfully Added', makeUrl('curation', 'selectTerms', $urlArray));
		} else
		{
			redirectWithMessage('Term ' . $termAcc . ' successfully Added', makeUrl('curation', 'selectTerms'));
		}
	} else {
		redirectWithMessage('Term ' . $termAcc . ' NOT successfully added', makeUrl('curation', 'selectTerms', $urlArray));
	}
}

function curation_addSSLPToBucket() {
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}

	$objectName = getRequestVarString('objectName');
	$objectType = getRequestVarString('objectType');
	$matchType = getRequestVarString('matchType');
	$rgdID = getRequestVarString('RGD_ID');
	$result = fetchRecord("select s.rgd_name, s.rgd_id, r.species_type_key  from sslps s, rgd_ids r where s.rgd_id = " . $rgdID . ' and s.rgd_id = r.rgd_id ');
	extract($result);

	$species = getSpeciesName($SPECIES_TYPE_KEY);
	$storedResult = array (
		$rgdID => $RGD_NAME,
		'rgdID' => $rgdID,
		'name' => $RGD_NAME,
		'objectType' => 'SS',
		'species' => $species
	);
	// Add the objectName to the URL we go back so it will show the same search as before the user
	// selected the obejct to add.  
	$urlArray = array (
		"objectName" => $objectName,
		"objectType" => $objectType,
		"matchType" => $matchType
	);

	addItemToBucket('SSLP_OBJECT_BUCKET', $rgdID, $storedResult);
	$howmany = count(getBucketItems('SSLP_OBJECT_BUCKET'));

	redirectWithMessage(' Added SSLP: ' . $RGD_NAME, makeUrl('curation', 'searchObjects', $urlArray));

}

function getGenesByName($objectName, $matchType, $speciesArray) {

	$maxresults = 500;
	$objectName = trim($objectName);
	$rgd_id_to_searchfor = $objectName;
	$objectName = cleanUpAndHandleMatching($objectName, $matchType);
	
	$sql = 'select  g.gene_key, g.gene_symbol, g.full_name, g.rgd_id, r.object_status , r.species_type_key from ';
	$sql .= 'genes g , rgd_ids r ';
	
	
	$sql .= 'where  g.rgd_id = r.rgd_id  and  r.object_status = \'ACTIVE\' and r.SPECIES_TYPE_KEY in ( ';
	$iterCount = 0;
	foreach ($speciesArray as $speciesID) {
		if ($iterCount++ >= 1) {
			$sql .= " , ";
		}
		$sql .= $speciesID;
	}
	
	$sql .= ' ) and rownum <= ' . $maxresults . ' and (( upper ( gene_symbol ) like \'' . strtoupper($objectName) . '\' ) ';
	$sql .= 'or ( upper ( full_name ) like \'' . strtoupper($objectName) . '\' )';
	// take care of searching for RGDID directly here
	if (is_numeric($rgd_id_to_searchfor)) {
		$sql .= ' or ( g.rgd_id = ' . $rgd_id_to_searchfor . ' ) ';
	}
	$sql .= 'or (g.rgd_id in ( ';
	// sub query  to get ID's of genes where alias has been created for gene name or symbol
	$sql .= 'select rgd_id from aliases where alias_value_lc like \'' . strtolower($objectName) . '\'  ';
	$sql .= 'and alias_type_name_lc in(\'old_gene_symbol\',\'old_gene_name\',\'alternate_name\',\'alternate_symbol\',\'alternate_id\') ';
	$sql .= ')))';
	//
	
	$sql .= ' order by decode(lower(g.gene_symbol), \'' . $rgd_id_to_searchfor . '\', 0, 1),lower(g.gene_symbol), abs(r.species_type_key - 3)';
	
	
	// dump ( $sql ) ;
	$genes = fetchRecords($sql);
	
	return $genes;
}


/**
 * Return HTML table of  genes ONLY selected by $objectName. $urlSearchArray is needed to construct links back to the 
 * original search screen with proper links to search. 
 
 * Changed by WLiu 5/6/2010
 */
function doSearchforGenesByName($objectName, $urlSearchArray, $matchType, $speciesArray) {

	$maxresults = 500;
	$toReturn = "";
	$genes = getGenesByName($objectName, $matchType, $speciesArray);
	$genecount = count($genes);
	$table = newTable('RGDID', 'Symbol', 'Name', 'Species', 'Aliases', hrefOverlib("'Active Gene (Green) <br>Withdrawn Gene ( Yellow)<br>Retired Gene (Red) ', CENTER", 'Status'), 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;
	// $urlSearchArray['matchType[]'] =  $matchType   ;  
	foreach ($genes as $gene) {

		// htmlEscapeValues($gene);
		extract($gene);

		$urlSearchArray['RGD_ID'] = $RGD_ID;
		// create a named anchor for every gene RGDID -- Sep'11 MT
		$table->addRow($RGD_ID."<a name='$RGD_ID'></a>", $GENE_SYMBOL, $FULL_NAME, makeSpeciesLink($SPECIES_TYPE_KEY), getAliasesInHtml($RGD_ID, 'gene'), makeObjectStatusLink($OBJECT_STATUS), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addGeneToBucket', $urlSearchArray));
	}

	if ($genecount == $maxresults) {
		$toReturn .= " <font color='red'>NOTE: You've exceeded your maximum search results of 500, only the first 500 are being displayed.  ";
		$toReturn .= "Please narrow your search.</font><p/>\n";
	} else {
		$toReturn .= "Result Count : " . $genecount;
	}
	$toReturn .= $table->toHtml();
	return $toReturn;

}

/**
 * Return HTML table of  genes ONLY selected by $objectName. $urlSearchArray is needed to construct links back to the 
 * original search screen with proper links to search. 
 
 * Changed by WLiu 5/6/2010
 */
function doSearchforGeneAndOrthologByName($objectName, $urlSearchArray, $matchType, $speciesTypeKey, $orthoSpecies) {

	$maxresults = 500;
	$toReturn = "";
	$genes = getGenesByName($objectName, $matchType, array($speciesTypeKey));
	$genecount = count($genes);
	
	if ($genecount == 0) return "<font color='red'>No records found!</font>";
	
	$rgd_id_to_searchfor = $objectName;
	
	$iterCount = 0;
	$rgdIds = "";
	foreach ($genes as $gene) {
		if ($iterCount++ >= 1) {
			$rgdIds .= ', ';
		}
		extract($gene);
		$rgdIds .= $RGD_ID;
	};
	
	$sql = 'SELECT DISTINCT * FROM (';
	$sql .= 'SELECT g.gene_key, g.gene_symbol, g.full_name, g.rgd_id, r.object_status, r.species_type_key, oth.SRC_RGD_ID as rat_rgd_id ';
	$sql .= 'FROM genes g, rgd_ids r, genetogene_rgd_id_rlt oth';
	$sql .= " where oth.SRC_RGD_ID in ($rgdIds) ";
	$sql .= " AND oth.dest_rgd_id=r.rgd_id AND r.object_status='ACTIVE' AND r.species_type_key IN ($orthoSpecies) AND r.rgd_id=g.rgd_id";
	$sql .= ' UNION ';
	$sql .= "select g.gene_key, g.gene_symbol, g.full_name, g.rgd_id, r.object_status , r.species_type_key, oth.DEST_RGD_ID ";
	$sql .= 'FROM genes g, rgd_ids r, genetogene_rgd_id_rlt oth';
	$sql .= " where oth.DEST_RGD_ID in ($rgdIds) ";
	$sql .= " AND oth.dest_rgd_id=r.rgd_id AND r.object_status='ACTIVE' AND r.species_type_key IN ($orthoSpecies) AND r.rgd_id=g.rgd_id";
	$sql .= ') a ';
	$sql .= "ORDER BY DECODE(lower(a.gene_symbol), '$rgd_id_to_searchfor', 0, 1),lower(a.gene_symbol), abs(a.species_type_key-$speciesTypeKey)";
//dump ( $sql ) ;
	$orthologs = fetchRecords($sql);
	
	$table = newTable('RGDID', 'Symbol', 'Name', 'Species', 'Aliases', hrefOverlib("'Active Gene (Green) <br>Withdrawn Gene ( Yellow)<br>Retired Gene (Red) ', CENTER", 'Status'), 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;
	foreach ($genes as $gene) {

		extract($gene);

		$urlSearchArray['RGD_ID'] = $RGD_ID;
		$rgdUrl = '&RGD_ID[]=' . $RGD_ID;
		$ratRgdId = $RGD_ID;
		$RGD_ID_tmp = $RGD_ID;
		$GENE_SYMBOL_tmp = $GENE_SYMBOL;
		$FULL_NAME_tmp = $FULL_NAME;
		$SPECIES_TYPE_KEY_tmp = $SPECIES_TYPE_KEY;
		$OBJECT_STATUS_tmp = $OBJECT_STATUS;
		
		// create a named anchor for every gene RGDID -- Sep'11 MT
		foreach ($orthologs as $ortholog) {
			
			extract($ortholog);
			if ($RAT_RGD_ID == $ratRgdId) {
				$rgdUrl .= '&RGD_ID[]=' . $RGD_ID;
			}
		}
		$table->addSpacer('<a href="/rgdCuration/?module=curation&func=addGeneToBucket&objectName='. $urlSearchArray['objectName'] .'&objectType='. $urlSearchArray['objectType'] .'&matchType='. $urlSearchArray['matchType'] . $rgdUrl . '"><img src="icons/arrow_down.png" border=0 alt="Add"/>Add the ortholog group to bucket</a>');
		$table->addRow($RGD_ID_tmp."<a name='$RGD_ID_tmp'></a>", $GENE_SYMBOL_tmp, $FULL_NAME_tmp, makeSpeciesLink($SPECIES_TYPE_KEY_tmp), getAliasesInHtml($RGD_ID_tmp, 'gene'), makeObjectStatusLink($OBJECT_STATUS_tmp), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addGeneToBucket', $urlSearchArray));
		

		foreach ($orthologs as $ortholog) {
			
			extract($ortholog);
			if ($RAT_RGD_ID == $ratRgdId) {
				$urlSearchArray['RGD_ID'] = $RGD_ID;
				$table->addRow($RGD_ID."<a name='$RGD_ID'></a>", $GENE_SYMBOL, $FULL_NAME, makeSpeciesLink($SPECIES_TYPE_KEY), getAliasesInHtml($RGD_ID, 'gene'), makeObjectStatusLink($OBJECT_STATUS), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addGeneToBucket', $urlSearchArray));
				$rgdUrl .= '&RGD_ID[]=' . $RGD_ID;
			}
		}
		$table->addSpacer('<a href="/rgdCuration/?module=curation&func=addGeneToBucket&objectName='. $urlSearchArray['objectName'] .'&objectType='. $urlSearchArray['objectType'] .'&matchType='. $urlSearchArray['matchType'] . $rgdUrl . '"><img src="icons/arrow_up.png" border=0 alt="Add"/>Add the ortholog group to bucket</a>');
		$table->addSpacer();
	}

	if ($genecount == $maxresults) {
		$toReturn .= " <font color='red'>NOTE: You've exceeded your maximum search results of 500, only the first 500 are being displayed.  ";
		$toReturn .= "Please narrow your search.</font><p/>\n";
	} else {
		$toReturn .= "Result Count : " . $genecount;
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}

/**
 * Return the html used to display the synonyms associated with a particula term in table form 
 * 
 */
function getTermSynonymHtml($termAcc) {
	$flip = -1;
	$returnString = '<table border=0>';
	$sql = "SELECT * FROM ont_synonyms WHERE term_acc='$termAcc' ORDER BY synonym_name";
	$synonymArray = fetchRecords($sql);
	foreach ($synonymArray as $synonymRow) {
		$returnString .= '<tr><td ';
		if ($flip == -1) {
			$returnString .= ' class=odd';
		} else {
			$returnString .= ' class=even';
		}
		$flip *= -1;
		$returnString .= '>' . str_replace('"', '', $synonymRow['SYNONYM_NAME']) . '</td></tr>';
	}
	return $returnString . '</table>';
}

/**
 * Return an HTML table with aliases for a given RGDID and aliasType: 'gene' or 'strain'
 * IT is critical that the generates table use NO line feeds or single / double quotes as it will 
 * be included in a javascript call in the panel through the $aliasesHtml variable that is stored in the session
 * for genes and strains. 
 */
function getAliasesInHtml($rgdID, $aliasType, $escapeQuotes = true) {

	$flip = -1;
	$returnString = '<table border=0>';
	$sql = 'select * from aliases where rgd_ID = ' . $rgdID;
	switch ($aliasType) {
		case 'gene' :
			$sql .= 'and alias_type_name_lc in( \'old_gene_symbol\', \'old_gene_name\', \'alternate_id\', \'alternate_symbol\', \'alternate_name\') ';
			break;
		case 'strain' :
			$sql .= 'and alias_type_name_lc in( \'old_strain_symbol\', \'old_strain_name\') ';
			break;
	}
	$sql .= ' order by  ALIAS_VALUE_LC ';

	$aliasArray = fetchRecords($sql);
	foreach ($aliasArray as $aliasRow) {
		$returnString .= '<tr><td ';
		if ($flip == -1) {
			$returnString .= ' class=odd';
		} else {
			$returnString .= ' class=even';
		}
		$flip *= -1;
		$returnString .= '>' . str_replace('"', '', $aliasRow['ALIAS_VALUE']) . '</td></tr>';
	}
	return $returnString . '</table>';
}

/**
 * Return HTML table of QTLs selected by $objectName. $urlSearchArray is neede to construct links back to the 
 * original search screen with proper links to search. 
 */
function doSearchforQTLsByName($objectName, $urlSearchArray, $matchType) {

	$toReturn = "";
	$objectName = trim($objectName);
	$rgd_id_to_searchfor = $objectName;
	$objectName = cleanUpAndHandleMatching($objectName, $matchType);
	$numericSearchString = '';

	$sql = 'select c.qtl_symbol, c.qtl_name , c.chromosome , c.rgd_id , r.object_status, r.species_type_key ';
	$sql .= ' from qtls c,rgd_ids r';
	$sql .= ' where';
	$sql .= ' c.rgd_id = r.rgd_id';
	// take care of searching for RGDID directly here
	if (is_numeric($rgd_id_to_searchfor)) {
		$sql .= ' and ( c.rgd_id = ' . $rgd_id_to_searchfor . ' ) or ';
	} else {
		$sql .= ' and ';
	}
	$sql .= '  ( upper ( c.qtl_name)  like \'' . strtoupper($objectName) . '\' or ';
	$sql .= ' upper ( c.qtl_symbol ) like \'' . strtoupper($objectName) . '\' )';
	$sql .= ' order by c.qtl_symbol';

	//dump( $sql ) ; 
	// $toReturn .= $sql . "<br>";
	$genes = fetchRecords($sql);
	$table = newTable('RGDID', 'Symbol', 'Name', ' Chromosome', hrefOverlib("'Active QTL (Green) <br>Withdrawn QTL ( Yellow)<br>Retired QTL (Red) ', CENTER", 'Status'), 'Species', 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;

	foreach ($genes as $gene) {

		// htmlEscapeValues($gene);
		extract($gene);
		$tmparray = $urlSearchArray;
		$tmparray['RGD_ID'] = $RGD_ID;
		$table->addRow($RGD_ID, $QTL_SYMBOL, $QTL_NAME, $CHROMOSOME, makeObjectStatusLink($OBJECT_STATUS), makeSpeciesLink($SPECIES_TYPE_KEY), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addQTLToBucket', $tmparray));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;

}

/**
 * Do search for SSLP's returning the HTML to display to the user of results. 
 */
function doSearchforSSLPsByName($objectName, $urlSearchArray, $matchType) {

	$toReturn = "";
	$objectName = trim($objectName);
	$rgd_id_to_searchfor = $objectName;
	$objectName = cleanUpAndHandleMatching($objectName, $matchType);
	$numericSearchString = '';

	$sql = 'select s.*, r.object_status from sslps s,
		    rgd_ids r
		    where 
		    s.rgd_id = r.rgd_id';
	// take care of searching for RGDID directly here
	if (is_numeric($rgd_id_to_searchfor)) {
		$sql .= ' and ( s.rgd_id = ' . $rgd_id_to_searchfor . ' ) or ';
	} else {
		$sql .= ' and ';
	}
	$sql .= ' upper ( s.rgd_name)  like \'' . strtoupper($objectName) . '\'  order by s.rgd_name';

	// dump( $sql ) ; 
	// $toReturn .= $sql . "<br>";
	$genes = fetchRecords($sql);
	$table = newTable('RGDID', 'SSLP Name', hrefOverlib("'Active Gene (Green) <br>Withdrawn Gene ( Yellow)<br>Retired Gene (Red) ', CENTER", 'Status'), 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;

	foreach ($genes as $gene) {

		// htmlEscapeValues($gene);
		extract($gene);
		$aliasesHtml = addSlashes(getAliasesInHtml($RGD_ID, 'strain'));
		$tmparray = $urlSearchArray;
		$tmparray['RGD_ID'] = $RGD_ID;
		$table->addRow($RGD_ID, $RGD_NAME, makeObjectStatusLink($OBJECT_STATUS), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addSSLPToBucket', $tmparray));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;

}

function doSearchforStrainsByName($objectName, $urlSearchArray, $matchType) {

	$toReturn = "";
	$objectName = trim($objectName);
	$rgd_id_to_searchfor = $objectName;
	$objectName = cleanUpAndHandleMatching($objectName, $matchType);
	$numericSearchString = '';

	$sql = 'SELECT DISTINCT s.strain_symbol, s.full_name, s.strain ,s.substrain,  s.rgd_id, r.object_status
		    FROM strains s
			JOIN rgd_ids r ON s.rgd_id=r.rgd_id
			LEFT JOIN aliases a ON s.rgd_id=a.rgd_id
		    WHERE object_status=\'ACTIVE\'';
	// take care of searching for RGDID directly here
	if (is_numeric($rgd_id_to_searchfor)) {
		$sql .= ' and ( s.rgd_id = ' . $rgd_id_to_searchfor . ' ) or ';
	} else {
		$sql .= ' and ';
	}
	$sql .= ' ( upper ( s.full_name)  like \'' . strtoupper($objectName) . '\' or 
		    upper ( s.strain_symbol ) like \'' . strtoupper($objectName) . '\' or
		    upper ( a.alias_value ) like \'' . strtoupper($objectName) . '\' )';

	$sql .= ' order by s.strain_symbol';

	// dump( $sql ) ; 
	// $toReturn .= $sql . "<br>";
	$genes = fetchRecords($sql);
	$table = newTable('RGDID', 'Strain', 'Aliases', hrefOverlib("'Active Gene (Green) <br>Withdrawn Gene ( Yellow)<br>Retired Gene (Red) ', CENTER", 'Status'), 'Select');
	$table->setAttributes('class="simple" width="100%"');
	$count = 0;

	foreach ($genes as $gene) {

		// htmlEscapeValues($gene);
		extract($gene);
		$aliasesHtml = addSlashes(getAliasesInHtml($RGD_ID, 'strain'));
		$tmparray = $urlSearchArray;
		$tmparray['RGD_ID'] = $RGD_ID;
		$table->addRow($RGD_ID, $STRAIN_SYMBOL, $aliasesHtml, makeObjectStatusLink($OBJECT_STATUS), makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addStrainToBucket', $tmparray));
	}
	$toReturn .= $table->toHtml();
	return $toReturn;

}

/**
 * Matches the values in the RGD_OBJECTS table, minus the objects we don't want to allow the users to create.
*/
function getObjectArray() {
	return array (
		'1' => 'Gene',
		'6' => 'QTL',
		'3' => 'SSLP',
		'5' => 'Strain'
	);
}

/**
 * Added 14 for search only .
 * WLiu 5/6/2010
*/
function getObjectArraySearch() {
	return array (
		'1' => 'Rat Gene',
		'4' => 'Chinchilla Gene',
		'9' => 'Pig Gene',
		'16' => 'Dog Gene',
		'8' => 'Bonobo Gene',
        '7' => 'Squirrel',
		'14' => 'Rat Gene & Orthologs',
		'13' => 'Human or Mouse Gene',
		'15' => 'Chinchilla & Orthologs',
		'6' => 'QTL',
		'3' => 'SSLP',
		'5' => 'Strain'
	);
}

/**
 * Returns an array of ONTOLOGY qualifiers name as key and value to use as a drop down list
 * Note: only the qualifiers applicable to types of to-be-annotated objects are returned
 */
function getOntQualifierArray($objArray) {
	// determine object keys for objects in all object array
	$objectKeys = array ();
	foreach ($objArray as $rgdId => $objText) {
		// $objText is like this: 'Rat Gene: A2m'
		if( strpos($objText, ' Gene: ') !== false ) {
			$objectKeys[] = 1;
		} else if( strpos($objText, ' QTL : ') !== false ) {
			$objectKeys[] = 6;
		} else if( strpos($objText, ' Strain: ') !== false ) {
			$objectKeys[] = 5;
		} else if( strpos($objText, ' SSLP: ') !== false ) {
			$objectKeys[] = 3;
		}
	}
	$ontArray = getOntTermsArrayFromSession ();
	$ontologies = '';
    $first = 0;

	foreach ($ontArray as $rgdId => $ontText) {
    	if ($first++ != 0) {
        	$ontologies .= ",";
        }
    	if( strpos($ontText, '[P]') !== false  || strpos($ontText, '[F]') !== false || strpos($ontText, '[C]') !== false) {
            $ontologies .= "'" . 'GO' . "'";
        }else if( strpos($ontText, '[E]') !== false ) {
            $ontologies .= "'" . 'CHEBI' . "'";
        } else if( strpos($ontText, '[D]') !== false ) {
            $ontologies .= "'" . 'RDO' . "'";
        } else if( strpos($ontText, '[N]') !== false || strpos($ontText, '[H]') !== false) {
            $ontologies .= "'" . 'MP' . "'";
        }else if( strpos($ontText, '[W]') !== false ) {
            $ontologies .= "'" . 'PW' . "'";
        }else if( strpos($ontText, '[V]') !== false ) {
            $ontologies .= "'" . 'VT' . "'";
        }
    }

	$objKeys = implode(',', array_unique($objectKeys, SORT_NUMERIC));
    if(empty($ontArray) && empty($objArray)){
        $sql = 'SELECT DISTINCT ont_qualifier_name,ont_qualifier_id FROM ontology_qualifier ORDER BY ont_qualifier_id';
	}else {
	    $sql = 'SELECT DISTINCT ont_qualifier_name,ont_qualifier_id FROM ontology_qualifier WHERE object_key IN('.$objKeys.') AND ont_id IN ('.$ontologies.') ORDER BY ont_qualifier_id';
	}
	$returnArray = array ();
	$results = fetchRecords($sql);
	if (count($results) > 0) {
		foreach ($results as $result) {
			extract($result);
			$returnArray[$ONT_QUALIFIER_NAME] = $ONT_QUALIFIER_NAME;
		}
	}
	return $returnArray;
}

/**
 * Returns an array of evidence codes from the database key = code and value = code
 */
function getEvidenceCodeArray() {
	$returnArray = array (
		"IAGP" => "IAGP",
		"IC" => "IC",
		"IDA" => "IDA",
		"IEP" => "IEP",
		"IGI" => "IGI",
		"IMP" => "IMP",
		"IPI" => "IPI",
		"ISO" => "ISO",
		"EXP" => "EXP",
		"HDA" => "HDA",
		"HEP" => "HEP",
		"HGI" => "HGI",
		"HMP" => "HMP",
		"HTP" => "HTP",
		"ND" => "ND",
	);
	return $returnArray;
}

function getTermInfoByTermAcc($termAcc) {

	$returnArray = array ();
	$sql = "select t.*,o.ASPECT from ONT_TERMS t,ONTOLOGIES o where TERM_ACC='$termAcc' and o.ont_id=t.ont_id";
	//dump ( $sql) ; 
	$resultType = fetchRecord($sql);
	$returnArray['TERM'] = $resultType['TERM'];
	$returnArray['TERM_ACC'] = $resultType['TERM_ACC'];
	$returnArray['ASPECT'] = $resultType['ASPECT'];

	$sql = "select count(*) as NOT4CURATION from ONT_SYNONYMS s where TERM_ACC='$termAcc' and SYNONYM_NAME='Not4Curation'";
	//dump ( $sql) ; 
	$resultType = fetchRecord($sql);
	$returnArray['NOT4CURATION'] = $resultType['NOT4CURATION']; // 0 or 1

	return $returnArray;
}

/**
 * 
 */
function getOntArray() {
	return array (
		'all' => 'All',
		'go' => 'Gene Ontology',
		'do' => 'Disease Ontology',
		'rdo' => 'RGD Disease Ontology',
		'nbo' => 'Neuro Behavioral Ontology',
		'po' => 'Phenotype Ontology',
		'hp' => 'Human Phenotype Ontology',
		'pw' => 'Pathway Ontology',
		'rs' => 'Rat Strain Ontology',
		'vt' => 'Vertebrate Trait Ontology',
		'cmo' => 'Clinical Measurement Ontology',
		'mmo' => 'Measurement Method Ontology',
		'xco' => 'Experimental Condition Ontology',
		'chebi' => 'Chebi Ontology',
	);
}

function getRefRetArray() {

	return array (
		'1' => 'Year',
		'2' => 'Citation',
		'3' => 'Title'
	);
}
/**
 * Edit an existing annotation
 */
function curation_EditAnnotation() {
  return "Don't you wish you actually could edit this annotation ? :-) "; 
}

/**
 * 
 */
function curation_selectTerms() {
	
	setPageTitle("Select Terms");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$theForm = newForm('Browse', 'GET', 'curation', 'selectTerms');
	$theForm->addText('objectName', 'Enter the term:', 80, 400, true);
	$theForm->setInitialFocusField('objectName');
	$objectNameSelected = getRequestVarString('objectName');
	if ($objectNameSelected) {
		preg_match("/[A-Z]+:[0-9]+/", $objectNameSelected, $acc_id);
		if ($acc_id) preg_match("/[A-Z]+/", $acc_id[0], $ont_id);
	}
	
	$toReturn = '    <script src="/rgdweb/js/jquery/jquery-1.12.4.min.js"></script>
    <script src="/rgdweb/js/jquery/jquery-ui-1.8.18.custom.min.js"></script>
    <script src="/rgdweb/js/jquery/jquery_combo_box.js"></script>
    <script type="text/javascript" src="/rgdweb/js/jquery/jquery-migrate-1.2.0.js"></script>
    <script type="text/javascript" src="/QueryBuilder/js/jquery.autocomplete.js"></script>

    <!--script type="text/javascript"  src="/solr/OntoSolr/admin/file?file=/velocity/jquery.autocomplete.curation.js&contentType=text/javascript"></script-->';
	$toReturn .= '<script type="text/javascript">$(document).ready(function(){$("#objectName").autocomplete("/solr/OntoSolr/select", {extraParams:{
                                             "fq": "cat:(BP CC MF MP HP NBO PW RDO RS VT CMO MMO XCO CHEBI)",
                                             "wt": "velocity",
                                              "bf": "term_len_l^.02",
                                             "v.template": "termmatch",
                                             "cacheLength": 0
                                           },
                                           scrollHeight: 240,
                                           max: 40
                                         });
                     $("#objectName").result(function(data, value){$("#form").submit();});$("input[name=submitBtn]").hide();
			         $("#objectName").focus();';
	$closeReturn = '});</script> Ontologies: <a href="/rgdCuration/?module=curation&func=selectTerms&objectName=biological_process+(GO%3A0008150)&ontology=&hiddenXYZ123=">BP</a> '
			.'<a href="/rgdCuration/?objectName=cellular_component+%28GO%3A0005575%29&hiddenXYZ123=&module=curation&func=selectTerms">CC</a> ' 
			.'<a href="/rgdCuration/?objectName=clinical+measurement+(CMO%3A0000000)&hiddenXYZ123=&module=curation&func=selectTerms">CMO</a> '
			.'<a href="/rgdCuration/?objectName=molecular_function+(GO%3A0003674)&hiddenXYZ123=&module=curation&func=selectTerms">MF</a> '
			.'<a href="/rgdCuration/?objectName=measurement+method+(MMO%3A0000000)&hiddenXYZ123=&module=curation&func=selectTerms">MMO</a> '
			.'<a href="/rgdCuration/?objectName=mammalian+phenotype+(MP%3A0000001)&hiddenXYZ123=&module=curation&func=selectTerms">MP</a> '
			.'<a href="/rgdCuration/?objectName=human+phenotype+(HP%3A0000001)&hiddenXYZ123=&module=curation&func=selectTerms">HP</a> '
			.'<a href="/rgdCuration/?objectName=NBO+ontology+(NBO%3A0000000)&hiddenXYZ123=&module=curation&func=selectTerms">NBO</a> '
			.'<a href="/rgdCuration/?objectName=pathway+(PW%3A0000001)&hiddenXYZ123=&module=curation&func=selectTerms">PW</a> '
			.'<a href="/rgdCuration/?objectName=Diseases+(DOID%3A4)&hiddenXYZ123=&module=curation&func=selectTerms">RDO</a> '
			.'<a href="/rgdCuration/?objectName=rat+strain+(RS%3A0000457)&hiddenXYZ123=&module=curation&func=selectTerms">RS</a> '
			.'<a href="/rgdCuration/?objectName=Trait+(VT%3A0000001)&hiddenXYZ123=&module=curation&func=selectTerms">VT</a> '
			.'<a href="/rgdCuration/?objectName=experimental+condition+(XCO%3A0000000)&hiddenXYZ123=&module=curation&func=selectTerms">XCO</a> '
			.'<a href="/rgdCuration/?objectName=chebi+ontology+(CHEBI%3A0)&hiddenXYZ123=&module=curation&func=selectTerms">CHEBI</a> ';
	
	switch ($theForm->getState()) {
		case INITIAL_GET :
			$toReturn .= $closeReturn;
			$toReturn .= $theForm->quickRender();
			return $toReturn;
		case SUBMIT_INVALID :
			break;
		case SUBMIT_VALID :
			// redirectWithMessage('Process Term search');
	
			// $theAddForm = newForm('Add Terms', 'GET', 'curation',  'addTermsToBucket');
			// Now we're on the search results page , add the additional fields to be filled in.
	
			if ($acc_id) $toReturn .= '$("#frame").attr("src", "/rgdweb/ontology/view.html?mode=iframe&ont='.$ont_id[0].'&sel_acc_id=selected_term&acc_id='.$acc_id[0].'");';
			$toReturn .= '$(window).on("message", accSelected);';
			$toReturn .= $closeReturn;
			$toReturn .= '<script type="text/javascript">function accSelected(event){oid=event.originalEvent.data.split("|")[0];term=event.originalEvent.data.split("|")[1];location.href="/rgdCuration/?module=curation&func=addTermToBucket&searchTerm="+term+" ("+oid+")&termAcc="+oid;}; </script>';
	
			$toReturn .= $theForm->quickRender();
			$toReturn .= ' <div id="mydiv"><a href="/solr/OntoSolr/browse?">OntoSolr ontology search tool</a><iframe id="frame" src="" width="100%" height="580">
   </iframe></div>';

			return $toReturn;
			break;
		default :
			return $theForm->quickRender();

	}

}

function curation_selectReferences() {

	setPageTitle("Select / Create References");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$toReturn = 'You have the following options:<p>';
	
	$theform = newForm('Search in OntoMate', 'GET', 'curation', 'searchRefInOntoMate', 'searchForm');
	$theform->confirmText = true;
	$objectArray = getObjectArrayFromSession();
	$ontTerms = getOntTermsArrayFromSession();
	
	$theform->addMultipleCheckBox('objectsFrom', '', $objectArray, false, "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
	$theform->addText('geneCondition', 'Additional Condition:', 120, 200, false);
	$theform->addCheckBox('looseMatch', 'Loose match', '');
	$theform->addMultipleCheckBox('ontterms', '', $ontTerms, false, "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
	$theform->addText('condition', 'Additional Condition:', 120, 200, false);
	
	$toReturn .=  generateSearchOntoPubForm($theform);
	
	$toReturn .= '<p><p>';
	
	$toReturn .= '<p><center>-- OR --</center><p>';

	// Form to add one PubMedID directly.
	$toReturn .= '<P><FIELDSET><LEGEND> Import a Reference Directly from NCBI</legend>';
	$pmidForm = newForm('Import Directly', 'GET', 'curation', 'addPubmedIDToBucket', 'importPMID');
	$pmidForm->addText('PMID', 'PMID:', 12, 80, false);
	$pmidForm->setInitialFocusField('PMID');
	$toReturn .= $pmidForm->quickRender();
	$toReturn .= '</LEGEND></FIELDSET>';
	$toReturn .= '<p><center>-- OR --</center><p>';
	
	$toReturn .= '<P><FIELDSET><LEGEND> Search By Existing Reference </legend>';
	$theForm = newForm('Find References', 'GET', 'curation', 'selectReferences');
	$theForm->addText('keywords', 'Keywords in Title, Citation, or RGDID:', 12, 80, false);
//	$theForm->setInitialFocusField('keywords');
	$theForm->addText('author', 'Author Surname', 12, 80, false);
	$theForm->addText('year', 'Year:', 12, 80, false);

	$theForm->addRadio('resultsOrder', ' Order Results By:', getRefRetArray(), false);
	$theForm->setDefault('resultsOrder', 1);

	$toReturn .= $theForm->quickRender();
	$toReturn .= '</LEGEND></FIELDSET>';
	$toReturn .= '<p><center>-- OR --</center><p>';

	$toReturn .= '<p><p>';
	
	// New update that does nto query remote screens
	$toReturn .= makeExternalLink('Create a new Reference', "/tools/curation/ref_edit.cgi?action=new");
	
	$toReturn .= "<p><p>";
	$toReturn .= '<script type="text/javascript"> ' . "\n";
	$toReturn .= "var wHandle = null;" . "\n";
	$toReturn .= 'function verify() { ' . "\n";
	$toReturn .= ' 
			var form = document.forms["searchForm"];
			var rs = "";
			var objIdx = 0;
			var termIdx = 0;
			for (var i = 0; i < form.elements.length; i++) {
				var element = form.elements[i];
			    if (element.checked) {
					if (element.name == "objectsFrom[]") {
						rs += "qRgdIds[" + objIdx + "].rgdId=" + element.value + "&";
			            objIdx ++;
			        } else if (element.name == "looseMatch") {
						rs += "qLooseMatch=true&";
			        } else {
						rs += "qOntoIds[" + termIdx + "].ontoId=" + element.value + "&";
			            termIdx ++;
					} 
			    } else if (element.name == "condition") {
			            rs += "qCond=" + element.value + "&";
				} else if (element.name == "geneCondition") {
			            rs += "qGeneCond=" + element.value + "&";
				} 
			};
			rs += "userId=' .getUserID(). '&userFullName='. getUserFullName() .'";
			rs += "&userKey=' .getSessionVar('userKey'). '&curHost=' .$_SERVER['HTTP_HOST'] .'" ;
			rs = "https://dev.rgd.mcw.edu/QueryBuilder/getResultForCuration?" + rs;
			console.log("RS:" +rs);
		    if (wHandle != null && !wHandle.closed) {
				wHandle.location.href=rs;
			} else {
				wHandle = window.open(rs, "_blank", "status = 1,height=750,width=1000,resizable=1,scrollbars=1,dependent=1,toolbar=1,location=1");
			};
			wHandle.focus();
					';
	$toReturn .= 'return false; ' . "\n";
	$toReturn .= '} ' . "\n";
	$toReturn .= '</script>' . "\n";
	
	
	$keywords = trim(getRequestVarString('keywords'));
	$author = trim(getRequestVarString('author'));
	$year = trim(getRequestVarString('year'));
	$resultsOrder = getRequestVarString('resultsOrder');

	$toReturn .= '<p><p>';
	
	
	switch ($theForm->getState()) {
		case INITIAL_GET :
			return $toReturn;
		case SUBMIT_INVALID :
			break;
		case SUBMIT_VALID :
			// redirectWithMessage('Process Term search');
			$toString = '<h2>References found</h2>';
			$count = 0;
			$firstActiveRefRgdId = 0;
			$toStringResults = doSearchforReferencesByAll($keywords, $author, $year, $resultsOrder, $count, $firstActiveRefRgdId);
			if( $count === 1 && $firstActiveRefRgdId != 0 ) {
				_addReferenceToBucket($firstActiveRefRgdId);
				$toString .= '<br>Added reference to bucket: RGD:'.$firstActiveRefRgdId;
			}
			$toString .= '<br>Your search returned ' . $count . ' records.</p>' . $toStringResults;
			return $toString;
			break;
		default :
			}
	return $toReturn;

}

/**
 * 
 */
function doSearchforReferencesByAll($keywords, $author, $year, $orderBy, & $count, & $firstActiveRefRgdId) {

	// clean up input fields
	$firstActiveRefRgdId = 0;
	$keywords = strtolower(trim($keywords));
	$author = strtolower(trim($author));
	$year = trim($year);

	$keywordsWild = '%' . $keywords . '%';
	$authorWild = '%' . $author . '%';
	$started_clause = false; // used to track where vs. and in clause. 
	$sql = 'SELECT distinct r.rgd_id, r.title, r.citation, rgd.OBJECT_STATUS ';
	switch ($orderBy) {
		case 1 :
			$orderby = ',r.pub_date ';
			break;
		case 2 :
			$orderby = ',r.citation ';
			break;
		case 3 :
			$orderby = ',r.title ';
			break;
		default :
			}
	$sql .= $orderby . 'FROM references r, rgd_ref_author k, rgd_ids rgd ';

	if ($author != "") {
		$sql .= ', authors a where k.author_key = a.author_key ';
		$started_clause = true;
		$sql .= 'and  r.ref_key = k.ref_key ';
	}

	if ($started_clause) {
		$sql .= ' AND ';
	} else {
		$sql .= ' WHERE ';
	}
	$sql .= "  r.ref_key = k.ref_key";
	$started_clause = true;

	if ($keywords != "") {
		if ($started_clause) {
			$sql .= ' AND ';
		} else {
			$sql .= ' WHERE ';
		}
		$sql .= '   (upper( r.title)  like \'' . strtoupper($keywordsWild) . '\' OR upper( r.citation)  like \'' . strtoupper($keywordsWild) . '\' ';

		if (is_numeric($keywords)) {
			$sql .= ' OR r.rgd_id = ' . $keywords;
		}
		$sql .= " ) ";

		$started_clause = true;
	}

	if ($author != "") {
		if ($started_clause) {
			$sql .= ' AND ';
		} else {
			$sql .= ' WHERE ';
		}
		$sql .= '    upper( a.author_lname)  like \'' . strtoupper($authorWild) . '\' ';
		$started_clause = true;
	}

	if ($year != "") {
		if ($started_clause) {
			$sql .= ' AND ';
		} else {
			$sql .= ' WHERE ';
		}
		$started_clause = true;
		$followingYear = $year +1;
		$sql .= '   r.pub_date between TO_DATE(\'01-JAN-' . $year . '\',\'DD-MON-YYYY\') and TO_DATE(\'01-JAN-' . $followingYear . '\',\'DD-MON-YYYY\') ';
	}

	if ($started_clause) {
		$sql .= ' AND ';
	} else {
		$sql .= ' WHERE ';
	}
	$sql .= ' r.rgd_id = rgd.rgd_id ';

	switch ($orderBy) {
		case 1 :
			$orderby = ' order by r.pub_date ';
			break;
		case 2 :
			$orderby = ' order by r.citation ';
			break;
		case 3 :
			$orderby = ' order by r.title ';
			break;
		default :
			}
	$sql .= $orderby;
	// dump($sql);
	$references = fetchRecords($sql);
	$toString = '<ol>';

	foreach ($references as $reference) {
		$count++;
		extract($reference);
		
		if( $firstActiveRefRgdId === 0  &&  $RGD_ID != 0  &&  $OBJECT_STATUS == 'ACTIVE' ) {
			$firstActiveRefRgdId = $RGD_ID;
		}
		
		$toString .= '<li><a href="' . makeReferenceURL($RGD_ID) . '">' . $TITLE . '</a>' . makeObjectStatusLink($OBJECT_STATUS) . '<br>' . $CITATION . "&nbsp;" . makeLink('<img src="icons/basket_put.png" border=0 alt="Add">', 'curation', 'addReferenceToBucket', "RGD_ID=" . $RGD_ID) . '</li><br>' . "\n";

	}
	$toString .= '</ol>';
	return $toString;
}

/** 
 * Make an Association
 */
function curation_makeAss() {

	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	setPageTitle("Make an Association");
	$toString = "What type of association to you wish to make ? <br><ul>";
	//	redirectWithMessage('Make Association');
	$toString .= "<li>" . makeLink('Make an Object / Term / Reference Annotation', 'curation', 'linkAnnotation') . "<p/>\n";
	$toString .= "<li>" . makeLink('Reference to Core Object', 'curation', 'linkCoreToRef') . "<p/>\n";
	$toString .= "<li>" . makeLink('Core Object to Core Object', 'curation', 'linkCoreToCore') . "<p/>\n";
	$toString .= "<li>" . makeLink('QTL to QTL with References', 'curation', 'linkQTLtoQTL') . "<p/>\n";
	$toString .= "</ul>";
	$toString .= "<p/>&nbsp;<p/>" . makeLink('Show / Delete My Annotations ', 'curationMaint', 'showMyAnnotation') . "\n";
	return $toString;

}

/**
 * Link core object to a Reference - This function generates a list for the user to approve first. 
 */
function curation_linkCoreToRef() {
	$toString = '';
	setPageTitle("Assocation Core Object to Reference");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}

	$theform = newForm('Generate List', 'GET', 'curation', 'linkCoreToRef');
	$objectArray = getObjectArrayFromSession();
	//  dump ( $objectArray) ; 
	$referenceArray = getReferenceArrayFromSession();
	$theform->addMultipleCheckBox('objectsFrom', '', $objectArray, true);
	$theform->addMultipleCheckBox('references', '', $referenceArray, true);

	switch ($theform->getState()) {
		case INITIAL_GET :
			if ($theform->getValue('command') == 'submit') {
				$theform->setDefault('command', NULL);

				//processing
				$numCreated = createAnnotations($theform, $returnMessage);
				$numCreatedRO = createReftoObject($theform, $returnMessageRO);

				$theform->addFormErrorMessage($numCreated . " Annotation(s) have been created in the database. <br>" . $returnMessage . '<br>' . $returnMessageRO);

			}
			// fall through and generateCoreToRefForm() also

		case SUBMIT_INVALID :
			return generateCoreToRefForm($theform, $objectArray, $referenceArray);
			break;

		case SUBMIT_VALID :

			$toString .= generateCoreToRefForm($theform, $objectArray, $referenceArray);
			$relationshipFullForm = generateCoreToFullRelationship($theform);

			$toString .= makeButtonLink("Cancel", 'curation', 'linkCoreToRef', $theform->getValues());
			$relCount = $relationshipFullForm->getValue('relCount');

			$toString .= $relationshipFullForm->formStart();
			$toString .= "<p/>\n";

			$table = newTable('From Object', 'From Object Type', 'Relationship', 'To Reference');
			$table->setAttributes('class="simple" width="100%"');
			for ($i = 0; $i < $relCount; $i++) {
				$table->addRow($relationshipFullForm->renderLabeledFields('fromRel' . $i), $relationshipFullForm->renderLabeledFields('fromRelType' . $i), $relationshipFullForm->renderLabeledFields('rel' . $i), $relationshipFullForm->renderLabeledFields('toRel' . $i));
			}

			$toString .= $table->toHtml();
			$toString .= "\n";
			$toString .= $relationshipFullForm->formEnd();

			return $toString;
			break;
		default :
			return $theform->quickRender();
	}
}

/**
 * Generate the relationship form between CORE to Relationship objects after the user has selected
 * Objects from the Core To Relationships Form
 */
function generateCoreToFullRelationship($theform) {

	$fromObjectsArray = (array) $theform->getValue('objectsFrom');
	$relationshipArray = $theform->getValue('references');

	$theRelform = newForm('Create Relationships', 'GET', 'curation', 'createCoretoRefRelationship');
	$relCnt = 0;
	// used if we get a skip a QTL to QTL match
	$warningMessage = '';
	foreach ($fromObjectsArray as $key => $fromObjectRGDID) {
		foreach ($relationshipArray as $relRGDID => $relValue) {
			$fromObj = getObjectInfoByRGID($fromObjectRGDID);
			$relObj = getObjectInfoByRGID($relValue);
			$theRelform->addReadOnlyLabel('fromRelType' . $relCnt, getObjectTypeByAbbr($fromObj['TYPE']));
			$theRelform->addReadOnlyLabel('fromRel' . $relCnt, $fromObj['SYMBOL']);
			$theRelform->addHidden('fromRelH' . $relCnt, $fromObj['ID']);
			$theRelform->addReadOnlyLabel('rel' . $relCnt, 'direct');
			$theRelform->addReadOnlyLabel('toRel' . $relCnt, $relObj['TITLE']);
			$theRelform->addHidden('toRelH' . $relCnt, $relValue);
			$relCnt++;
		}
	}
	$theRelform->addHidden('relCount', $relCnt);
	return $theRelform;
}

function generateCoreToRefForm($theform, $objectArray, $referenceArray) {

	//$theform->addMultipleCheckBox('objectsFrom', '', $objectArray, false);
	// $theform->addMultipleCheckBox('references', '', $referenceArray, false);
	$toString = $theform->formStart();
	$toString .= $theform->startGroup('Core Objects ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsFrom');
	$toString .= $theform->endGroup();
	$toString .= $theform->startGroup('References');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'references');
	$toString .= $theform->endGroup();
	$toString .= $theform->formEnd();

	return $toString;
}

/**
 * Link core object to reference to Ontology Terms
 */
function curation_linkAnnotation() {

	$toString = '<a name=\'title\'>&nbsp;</a>';
	
	setPageTitle("Make an Annotation");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}
	$theform = newForm('Generate List', 'GET', 'curation', 'linkAnnotation');

	$objectArray = getObjectArrayFromSession();
	$referenceArray = getReferenceArrayFromSession();
	$ontTerms = getOntTermsArrayFromSession();
	$evidenceCodesArray = getEvidenceCodeArray();
	$ontQualifierCodeArray = getOntQualifierArray($objectArray);

	$theform->addMultipleCheckBox('objectsFrom', '', $objectArray, true);
	$theform->addMultipleCheckBox('ontterms', '', $ontTerms, true);
	$theform->addMultipleCheckBox('references', '', $referenceArray, true);

	$theform->addSelect('qualifier', 'Qualifier', $ontQualifierCodeArray, false);
	$theform->addSelect('evidence', 'Evidence Code', $evidenceCodesArray, true);
	$theform->addText('with_info', 'With Info. ', 20, 200, false);
	$theform->addHidden('command', 'generate');

	$theform->addTextArea('notes', 'Notes', 10, 40, 1024, false);
	// set up objectRGDIDS to be passed into getAnnotationsHTMLTableByGenes() method later. 
	$objectRGDIDS = array ();
	foreach ($objectArray as $objkey => $objvalue) {
		$objectRGDIDS[] = $objkey;
	}

	switch ($theform->getState()) {
		case INITIAL_GET :

		case SUBMIT_INVALID :
			$toString .= generateLinkAnnotaionForm($theform, getBucketItems('GENE_OBJECT_BUCKET'), $referenceArray);
			// $objectArray = $theform->getValue('objectsFrom');
			// Attay html table of already existing annotations for all objects in bucket

			$toString .= getAnnotationsHTMLTableByGenes($objectRGDIDS, $ontTerms, $referenceArray);
			$toString .= getScrollStr("#title");
						
			return $toString;
			break;
		case SUBMIT_VALID :
			// Check QC on the form outside of required fields here. Set Error messages within here
			$commandstr = $theform->getValue('command');
			$theform->setDefault('command', 'generate');

			$passedQC = checkAnnotationQC($theform);

			// Now generate the ResultAnnotationForm from those selected annotations. This form will
			// show the annotation permutations to be created and allow the user to unselect various 
			// rows not to be created. 

			if (checkProperAnnotSelection($theform)) {
				$resultAnnotationForm = processAnnotationForm($theform);
			} else {
				$theform->addFormErrorMessage('You need to select at least one Object / Reference / Term combination.');
			}
			
			
			$toString .= generateLinkAnnotaionForm($theform, getBucketItems('GENE_OBJECT_BUCKET'), $referenceArray);
			
			if ( $commandstr == 'create'){
		        $theform->setDefault('command', 'generate' ) ; 
				$toString .= getScrollStr("#message");

 			} else {


 			////////////////////////////////////////////////////////
			// Associations to be created table generated next
			//
			$relCount = $resultAnnotationForm->getValue('relCount');
			$toString .= $resultAnnotationForm->formStart();
			
			// $toString .= dump ( $resultArray ) ;
			$table = newTable('ObjectName', 'Reference', '['.hrefOverlib("'Biological Process(P)<br>  Behavioral Process(B)<br>  Cellular Component(C)<br> Disease Ontology(D)<br> Mammalian Phenotype(N)<br> Molecular Function(F)<br> Pathway(W) <br> Chebi Ontology(E) ', CENTER", 'T').'] Term', 'Qualifier', 'Evidence', 'With Info', 'Species', 'Select');
			$table->setAttributes('class="simple" width="100%"');
			// foreach ( $resultArray as $objkey => $rowValue ) {
			//   extract($rowValue) ; 
			// $table->addRow( $objectname, 'RGD:' . $refvalue, $ontDesc, $qualifier, $evidence, $with_info);
			for ($i = 0; $i < $relCount; $i++) {
				$table->addRow($resultAnnotationForm->renderLabeledFields('objectnameL' . $i), 'RGD:' . $resultAnnotationForm->renderLabeledFields('refvalueL' . $i), $resultAnnotationForm->renderLabeledFields('ontDesc' . $i), $resultAnnotationForm->renderLabeledFields('qualifierL'), $resultAnnotationForm->renderLabeledFields('evidenceL' . $i), $resultAnnotationForm->renderLabeledFields('with_infoL' . $i), makeSpeciesLink($resultAnnotationForm->getValue('species' . $i)), $resultAnnotationForm->renderLabeledFields('select' . $i));
			}
			// }
    
			if ($passedQC) {
				// If QC check passed display table of annoations to be inserted into database.
			      if ( $commandstr == 'generate'){
					if (empty($theform->formErrorMessages)) {
						$toString .= getScrollStr("#result");
					} else {
						$toString .= getScrollStr("#title");
					}
			      	//					$toString .= "function _jumpToResult() {setTimeout(window.location='#result', 20000); setTimeout(window.scrollBy(0,-250), 20000);}";
					} 
					$toString .= '<h3>' . $relCount . ' Association(s) to be created:</h3>' ;
					$toString .= '<a href="#title">Click here to revise</a><br>';
					$toString .= $table->toHtml();
					$toString .= generateLinkAnnotaionFormHidden($theform, getBucketItems('GENE_OBJECT_BUCKET'));
					$toString .= $resultAnnotationForm->formEnd();
				}
			// submit command so we know to create reference in database when the form gets submitted with this
			// value in it. 
        	$toString .= makeButtonLink("Cancel", 'curation', 'linkAnnotation');
			// $toString .= makeButtonLink( "Create these Annotations in the Database", 'curation' , 'linkAnnotation', $theform->getValues()) ;
		      }
			$toString .= getAnnotationsHTMLTableByGenes($objectRGDIDS, $ontTerms, $referenceArray);
		        			      
			return $toString;
			break;
		default :
			return $theform->quickRender();
	}
}

function getScrollStr($position, $offset = 0) {
	$returnStr = "<script type=\"text/javascript\">";
	$returnStr .= "_loadSuper = window.onload;";
	$returnStr .= "window.onload = (typeof window.onload != 'function') ? _jumpToResult : function() { _loadSuper(); _jumpToResult(); }; ";
	if ($offset == 0) {
		$returnStr .= "function _jumpToResult() {setTimeout(window.location='" . $position . "', 20000);}";
	} else {
		$returnStr .= "function _jumpToResult() {setTimeout(window.location='" . $position . "', 20000); setTimeout(window.scrollBy(0,"	. $offset . "), 20000)}";
	}
	$returnStr .= "</script>";
	
	return $returnStr;
}

/**
 * Do all QC checks for the Annotation form return true if it passes else false
 */
function checkAnnotationQC(& $theForm) {
	// First check for
	$qc = checkWithInfoCode($theForm);
	if ($qc) {
		$qc = checkWithoutInfoCode($theForm);
		if ($qc) {
//			$qc = checkECwithOntTerms($theForm);
		}
	}

	// remove any spaces in with_info field
	$with_info = $theForm->getValue('with_info');
	$new_with_info = str_replace(' ', '', $with_info);
	$theForm->setDefault('with_info', $new_with_info);
	return $qc;
}

/**
 * Verify that the With Info field is filled in for certain evidence codes 
 * Returns: true is the with info field is ok, else false. 
 */
function checkWithInfoCode($theForm) {
	$with_info = $theForm->getValue('with_info');
	$evidence = $theForm->getValue('evidence');
	switch ($evidence) {
		case "ISO" :
		case "IPI" :
		case "IGI" :
		case "IC" :
			if (isReallySet($with_info)) {
				return true;
			} else {
				$theForm->addFormErrorMessage('The \'With Info\' field is required for this Evidence Code: ' . $evidence);
				return false;
			}
			break;

	}
	return true;
}

/* WLiu 5/26/2011*/
/**
 * Verify that the With Info field is not filled in for certain evidence codes 
 * Returns: true if the with info field is ok, else false. 
 */
function checkWithoutInfoCode($theForm) {
	$with_info = $theForm->getValue('with_info');
	$object_Array = getObjectArrayFromSession();;
	$checkStrain = 0;
	foreach ($object_Array as $objkey => $objvalue) {
	        if( strpos($objvalue, ' Strain: ') !== false ) {
            			$checkStrain = 5;
            }
    }
	$evidence = $theForm->getValue('evidence');
	switch ($evidence) {
		case "ISO" :
		case "IPI" :
		case "IGI" :
		case "IC" :
			return true;
			break;
        case "IAGP" :
        case "IMP" :
        case "IDA" :
            if (isReallySet($with_info) ) {
             if($checkStrain == '5') {
                return true;
             } else {
             $theForm->addFormErrorMessage('The \'With Info\' field should be empty for this Evidence Code: ' . $evidence);
             	return false;
             }
            } else {
                return true;
            }
            break;
		default:
			if (isReallySet($with_info)) {
				$theForm->addFormErrorMessage('The \'With Info\' field should be empty for this Evidence Code: ' . $evidence);
				return false;
			} else {
				return true;
			}
			break;
	}
}

/**
 * rule2: TAS must not be used with RDO,DO terms
 * rule4: ND is the only allowable evidence code for GO annotations to top-level GO terms:
 *        GO:0005575 'cellular component', GO:0008150 'biological process' and GO:0003674 'molecular function' 
 * Returns: true if the passed the rules, else false. 
 */
function checkECwithOntTermsPerAnnotation($theForm, $evidence, $ontvalue, $aspect, $termDesc) {

	switch ($evidence) {
        case 'IEP' :
			switch ($aspect) {
				case 'F' :
					$theForm->addFormErrorMessage('Evidence code \''.$evidence.'\' can\'t be combined with term: \''.$termDesc.'\'');
					return false;
				case 'C' :
					$theForm->addFormErrorMessage('Evidence code \''.$evidence.'\' can\'t be combined with term: \''.$termDesc.'\'');
					return false;
			}
			break;
		case 'TAS' :
			switch ($aspect) {
				case 'D' :
				case 'I' :
					$theForm->addFormErrorMessage('Evidence code \''.$evidence.'\' can\'t be combined with RDO terms: \''.$termDesc.'\'');
					return false;
			}
			break;
	}
	
	// ND is the only allowable evidence code for top-level terms 'biological process', 'molecular function', 'cellular component'
	if($ontvalue === 'GO:0008150' || $ontvalue === 'GO:0003674' || $ontvalue === 'GO:0005575') {
		if($evidence !== 'ND' ) {
			$theForm->addFormErrorMessage('Evidence code \'ND\' is the only allowable evidence code for \''.$termDesc.'\'');
			return false;
		}
	}




	return true;
}

/**
 * Check special annotations
 * Returns: true if the passed the rules, else false.
 */
function checkSpecialAnnotation($theForm, $rgdId, $ontvalue, $refRgdId) {
	switch ($refRgdId) {
		case '1598407':
			if ($ontvalue == 'GO:0008150' || $ontvalue == 'GO:0005575' || $ontvalue == 'GO:0003674') return true;
			$theForm->addFormErrorMessage('Reference RGD:1598407 can only be associated with "biological process" (GO:0008150),'.
					' "cellular component" (GO:0005575) or "molecular function" (GO:0003674), not ' . $ontvalue .'.');
			return false;
			break;
	}
	return true;
}

/** Check ontology annotations:
 * 1) HP annotations (aspect 'H') could be made only to human genes and qtls
 * 2) MP annotations (aspect 'N') could be made only to rat objects
 * 3) HP and MP annotations should go only with IAGP, IGI and IMP  evidence codes
 * 4) GO annotations (aspects 'B','F','P') are forbidden for human and mouse genes -- per RGDD-1413
 * Returns: true if the passed the rules, else false.
 */
function checkOntologyAnnotations($theForm, $oTermAspect, $species, $objType, $evidence) {
	static $allowedEvidenceCodes = array('IAGP','IGI','IMP');
	static $allowedMPEvidenceCodes = array('IAGP','IGI','IMP','IDA');
	
	switch($oTermAspect) {
		case 'N': // 'MP' terms
			if( $species == 1 || $species == 2 ) {
				$theForm->addFormErrorMessage('MP terms cannot be used for Mouse or Human ');
				return false;
			}
			else if( !in_array($evidence, $allowedMPEvidenceCodes) ) {
				$theForm->addFormErrorMessage('MP terms can only be used with the following evidence codes: '.join(', ', $allowedEvidenceCodes));
				return false;
			}
			break;
		case 'H': // 'HP' terms
			if( $species != 1 || ($objType != 'G' && $objType != 'Q') ) {
				$theForm->addFormErrorMessage('HP terms can only be used for human genes and qtls!');
				return false;
			}
			else if( !in_array($evidence, $allowedEvidenceCodes) ) {
				$theForm->addFormErrorMessage('HP terms can only be used with the following evidence codes: '.join(', ', $allowedEvidenceCodes));
				return false;
			}
			break;
		case 'B':
		case 'F':
		case 'P': // 'GO' annotations
			if( $species == 1 || $species == 2 ) {
				$theForm->addFormErrorMessage('GO annotations are forbidden for human and mouse genes');
				return false;
			}
			break;
		
	}
	return true;
}

/**
 * Returns the Annotations found for a given set of Object ID's passed in. Returns in HTML for
 * display to the user. Allows the user to click on an annotation and edit it. 
 */
function getAnnotationsHTMLTableByGenes($objectRGDIDArray, $ontTerms, $referenceArray) {
	if (!userLoggedIn()) {
		return NOACCESS_MSG;
	}
	// $userKey = getSessionVar('userKey') ;
	$token = getSessionVar('token');
	$toReturn = '';

	$toReturn .= "<p/>&nbsp;<p/>" . makeLink('Show / Delete My Annotations ', 'curationMaint', 'showMyAnnotation') . "\n";
	$toReturn .= '<p><h3>Annotations that already exist for Object(s) you\'ve selected:</h3></p>';
	if (sizeof($objectRGDIDArray) == 0) {
		return '';
	}
	
	$objIds = '';
	$first = 0;
	foreach ($objectRGDIDArray as $key => $rgdID) {
		if ($first++ != 0) {
			$objIds .= ",";
		}
		$objIds .= $rgdID;
	}
	$termAccs = '';
	$first = 0;
	foreach ($ontTerms as $termAcc => $term) {
		if ($first++ != 0) {
			$termAccs .= ",";
		}
		$termAccs .= "'" . $termAcc . "'";
	}
	$refIds = '';
	$first = 0;
	foreach ($referenceArray as $refId => $ref) {
		if ($first++ != 0) {
			$refIds .= ",";
		}
		$refIds .= $refId;
	}
	// generate the SQL to return all annotations for the objects that the user has in their bucket
	// except CHEBI and ClinVar pipeline annotations
	$sql = 'select 1 as score, a.FULL_ANNOT_KEY, a.TERM, a.ANNOTATED_OBJECT_RGD_ID, a.DATA_SRC, a.OBJECT_SYMBOL, a.REF_RGD_ID, a.EVIDENCE, a.WITH_INFO, a.ASPECT, a.OBJECT_NAME, a.QUALIFIER, a.RELATIVE_TO, a.CREATED_DATE, a.LAST_MODIFIED_DATE, a.TERM_ACC, a.CREATED_BY, a.LAST_MODIFIED_BY, a.XREF_SOURCE, DBMS_LOB.SUBSTR(a.notes, 3999) notes, r.species_type_key from full_annot a, rgd_ids r ';
	$sql .= '  WHERE a.data_src NOT IN(\'CTD\',\'ClinVar\') AND ';
	$sql .= '  ANNOTATED_OBJECT_RGD_ID in ( ' . $objIds;
	$sql .= ' ) and  a.ANNOTATED_OBJECT_RGD_ID = r.RGD_ID ';
//	$sql .= '  order by object_symbol, EVIDENCE, term ';

	if($refIds != '' && $termAcc != '') {
        $comboSql = 'select 5 as score, a.FULL_ANNOT_KEY, a.TERM, a.ANNOTATED_OBJECT_RGD_ID, a.DATA_SRC, a.OBJECT_SYMBOL, a.REF_RGD_ID, a.EVIDENCE, a.WITH_INFO, a.ASPECT, a.OBJECT_NAME, a.QUALIFIER, a.RELATIVE_TO, a.CREATED_DATE, a.LAST_MODIFIED_DATE, a.TERM_ACC, a.CREATED_BY, a.LAST_MODIFIED_BY, a.XREF_SOURCE, DBMS_LOB.SUBSTR(a.notes, 3999) notes, r.species_type_key from full_annot a, rgd_ids r ';
        $comboSql .= '  WHERE a.data_src NOT IN(\'CTD\',\'ClinVar\') AND ';
        $comboSql .= '  ANNOTATED_OBJECT_RGD_ID in ( ' . $objIds;
        $comboSql .= ' ) and a.ANNOTATED_OBJECT_RGD_ID = r.RGD_ID ';
        $comboSql .= ' and a.ref_rgd_id in (' . $refIds . ') and a.term_acc in (' . $termAccs . ')';
        $sql .= ' and a.ref_rgd_id not in (' . $refIds . ') and a.term_acc not in (' . $termAccs . ')';
        $sql = $comboSql . ' union ' . $sql;
    }
	if ($refIds != '') {
		$refSql = 'select 2 as score, a.FULL_ANNOT_KEY, a.TERM, a.ANNOTATED_OBJECT_RGD_ID, a.DATA_SRC, a.OBJECT_SYMBOL, a.REF_RGD_ID, a.EVIDENCE, a.WITH_INFO, a.ASPECT, a.OBJECT_NAME, a.QUALIFIER, a.RELATIVE_TO, a.CREATED_DATE, a.LAST_MODIFIED_DATE, a.TERM_ACC, a.CREATED_BY, a.LAST_MODIFIED_BY, a.XREF_SOURCE, DBMS_LOB.SUBSTR(a.notes, 3999) notes, r.species_type_key from full_annot a, rgd_ids r ';
		$refSql .= '  WHERE a.data_src NOT IN(\'CTD\',\'ClinVar\') AND ';
		$refSql .= '  ANNOTATED_OBJECT_RGD_ID in ( ' . $objIds;
		$refSql .= ' ) and a.ANNOTATED_OBJECT_RGD_ID = r.RGD_ID ';
		$refSql .= ' and a.ref_rgd_id in (' . $refIds . ')';
		if ($termAcc != '') {
		    $refSql .= ' and a.term_acc not in (' . $termAccs . ')';
		} else {
		    $sql .= ' and a.ref_rgd_id not in (' . $refIds . ')';
		}
		$sql = $refSql . ' union ' . $sql;
	} 
	if ($termAcc != '') {
		$termSql = 'select 4 as score, a.FULL_ANNOT_KEY, a.TERM, a.ANNOTATED_OBJECT_RGD_ID, a.DATA_SRC, a.OBJECT_SYMBOL, a.REF_RGD_ID, a.EVIDENCE, a.WITH_INFO, a.ASPECT, a.OBJECT_NAME, a.QUALIFIER, a.RELATIVE_TO, a.CREATED_DATE, a.LAST_MODIFIED_DATE, a.TERM_ACC, a.CREATED_BY, a.LAST_MODIFIED_BY, a.XREF_SOURCE, DBMS_LOB.SUBSTR(a.notes, 3999) notes,	 r.species_type_key from full_annot a, rgd_ids r ';
		$termSql .= '  WHERE a.data_src NOT IN(\'CTD\',\'ClinVar\') AND ';
		$termSql .= '  ANNOTATED_OBJECT_RGD_ID in ( ' . $objIds;
		$termSql .= ' ) and a.ANNOTATED_OBJECT_RGD_ID = r.RGD_ID ';
		$termSql .= ' and a.term_acc in (' . $termAccs . ')';
		if ($refIds != '') {
            $termSql .= ' and a.ref_rgd_id not in (' . $refIds . ')';
        } else {
		    $sql .= ' and a.term_acc not in (' . $termAccs . ')';
		}
		$sql = $termSql. ' union ' . $sql;
		$termSql = 'select 3 as score, a.FULL_ANNOT_KEY, a.TERM, a.ANNOTATED_OBJECT_RGD_ID, a.DATA_SRC, a.OBJECT_SYMBOL, a.REF_RGD_ID, a.EVIDENCE, a.WITH_INFO, a.ASPECT, a.OBJECT_NAME, a.QUALIFIER, a.RELATIVE_TO, a.CREATED_DATE, a.LAST_MODIFIED_DATE, a.TERM_ACC, a.CREATED_BY, a.LAST_MODIFIED_BY, a.XREF_SOURCE, DBMS_LOB.SUBSTR(a.notes, 3999) notes,	 r.species_type_key from full_annot a, rgd_ids r ';
		$termSql .= '  WHERE a.data_src NOT IN(\'CTD\',\'ClinVar\') AND ';
		$termSql .= '  ANNOTATED_OBJECT_RGD_ID in ( ' . $objIds;
		$termSql .= ' ) and a.ANNOTATED_OBJECT_RGD_ID = r.RGD_ID ';
		$termSql .= ' and a.term_acc in (select distinct od.CHILD_TERM_ACC from ONT_DAG od connect by prior od.CHILD_TERM_ACC = od.PARENT_TERM_ACC start with od.PARENT_TERM_ACC in (' . $termAccs . '))';
		$termSql .= ' and a.term_acc not in (' . $termAccs . ')';
		$sql .= ' and a.term_acc not in (select distinct od.CHILD_TERM_ACC from ONT_DAG od connect by prior od.CHILD_TERM_ACC = od.PARENT_TERM_ACC start with od.PARENT_TERM_ACC in (' . $termAccs . '))';
		$sql = $termSql. ' union ' . $sql;
	} 
	//dump ( $sql ) ;

	$finalSql = 'select * from (' . $sql . ') b order by score desc, object_symbol, EVIDENCE, term';
	$records = fetchRecords($finalSql);
	$table = newTable('Edit', 'Object name', 'Reference', 'Term', 'Qualifier', 'Evidence',  'With Info', hrefOverlib("'Biological Process(P)<br>  Behavioral Process(B)<br>  Cellular Component(C)<br> Disease Ontology(D)<br> Mammalian Phenotype(N)<br> Molecular Function(F)<br> Pathway(W) ', CENTER", 'T'), 'Species', 'Modified','Notes');
  
	$table->setAttributes('class="simple" width="100%"');
	foreach ($records as $record) {
		extract($record);
		//$table->addRow(makeLink('<img src="icons/page_white_edit.png" border=0 title="Edit" alt="Edit">', 'curation', 'editAnnotation', array (
	//		"fullAnnotKey",
	//		$FULL_ANNOT_KEY
	switch ($SCORE) {
		case 5:
    		$table->addRow(makeExternalLink("<img src='icons/page_white_edit.png' border=0 title='Edit' alt='Edit'>","/rgdweb/curation/edit/editAnnotation.html?rgdId=" . $FULL_ANNOT_KEY."&token=".$token), $OBJECT_SYMBOL, makeExternalLink('<font color="red">'.$REF_RGD_ID."</font>", makeReferenceURL($REF_RGD_ID)), '<font color="red">'.$TERM.'</font>', $QUALIFIER, $EVIDENCE, str_replace("|","| ",$WITH_INFO),  $ASPECT, makeSpeciesLink($SPECIES_TYPE_KEY), $LAST_MODIFIED_DATE, substr($NOTES,0,80));
    		break;
		case 4:
		    $table->addRow(makeExternalLink("<img src='icons/page_white_edit.png' border=0 title='Edit' alt='Edit'>","/rgdweb/curation/edit/editAnnotation.html?rgdId=" . $FULL_ANNOT_KEY."&token=".$token), $OBJECT_SYMBOL, makeExternalLink($REF_RGD_ID, makeReferenceURL($REF_RGD_ID)), '<font color="red">'.$TERM.'</font>', $QUALIFIER, $EVIDENCE, str_replace("|","| ",$WITH_INFO),  $ASPECT, makeSpeciesLink($SPECIES_TYPE_KEY), $LAST_MODIFIED_DATE, substr($NOTES,0,80));
			break;
		case 3:
		    $table->addRow(makeExternalLink("<img src='icons/page_white_edit.png' border=0 title='Edit' alt='Edit'>","/rgdweb/curation/edit/editAnnotation.html?rgdId=" . $FULL_ANNOT_KEY."&token=".$token), $OBJECT_SYMBOL, makeExternalLink($REF_RGD_ID, makeReferenceURL($REF_RGD_ID)), '<font color="orange">'.$TERM.'</font>', $QUALIFIER, $EVIDENCE, str_replace("|","| ",$WITH_INFO),  $ASPECT, makeSpeciesLink($SPECIES_TYPE_KEY), $LAST_MODIFIED_DATE, substr($NOTES,0,80));
			break;
		case 2:
		    $table->addRow(makeExternalLink("<img src='icons/page_white_edit.png' border=0 title='Edit' alt='Edit'>","/rgdweb/curation/edit/editAnnotation.html?rgdId=" . $FULL_ANNOT_KEY."&token=".$token), $OBJECT_SYMBOL, makeExternalLink('<font color="red">'.$REF_RGD_ID."</font>", makeReferenceURL($REF_RGD_ID)), $TERM, $QUALIFIER, $EVIDENCE, str_replace("|","| ",$WITH_INFO),  $ASPECT, makeSpeciesLink($SPECIES_TYPE_KEY), $LAST_MODIFIED_DATE, substr($NOTES,0,80));
			break;
		default:
		$table->addRow(makeExternalLink("<img src='icons/page_white_edit.png' border=0 title='Edit' alt='Edit'>","/rgdweb/curation/edit/editAnnotation.html?rgdId=" . $FULL_ANNOT_KEY."&token=".$token), $OBJECT_SYMBOL, makeExternalLink($REF_RGD_ID, makeReferenceURL($REF_RGD_ID)), $TERM, $QUALIFIER, $EVIDENCE, str_replace("|","| ",$WITH_INFO),  $ASPECT, makeSpeciesLink($SPECIES_TYPE_KEY), $LAST_MODIFIED_DATE, substr($NOTES,0,80));
	}
	}
	$toReturn .= $table->toHtml();
	return $toReturn;
}
/** 
 * Returns true if constraints will be ok , else returns false. 
 */
function verifyLinkConstraints($termAcc, $objRgdID, $refRGDID, $evidence, $with_info, $qualifier, &$full_annot_key) {

	$sql = "select FULL_ANNOT_KEY,NOTES from full_annot " .
	'where ' .
	' TERM_ACC = \'' . $termAcc . '\'' .
	' and ANNOTATED_OBJECT_RGD_ID = ' . $objRgdID .
	'  and DATA_SRC=\'RGD\' and EVIDENCE = \'' . $evidence . '\'' .
	' and REF_RGD_ID = ' . $refRGDID;
	if (isReallySet($with_info)) {
		$sql .= ' and WITH_INFO = ' . dbQuoteString($with_info) . ' ';
	} else {
		$sql .= ' and WITH_INFO is NULL ';
	}
	if (isReallySet($qualifier)) {
		$sql .= ' and QUALIFIER = ' . dbQuoteString($qualifier) . ' ';
	} else {
		$sql .= ' and QUALIFIER is NULL ';
	}

	dump ($sql ) ;
	// There is a constraint that the following foelds don't already exist. 
	//            @TERM_ACC 
	//            @ANNOTATED_OBJECT_RGD_ID
	//            @REF_RGD_ID 
	//            @EVIDENCE
	//            @WITH_INFO
	//            @QUALIFIER
	$full_annot_key = 0;
	$resultConstraint = fetchRecord($sql);
	if (count($resultConstraint) > 0) {
		$full_annot_key = $resultConstraint['FULL_ANNOT_KEY'];
		$notes = $resultConstraint['NOTES'];
		if( isReallySet($notes) && strlen($notes)>0 ) {
			$full_annot_key=0;
		}
		return false;
	} else {
		return true;
	}
}

function transferNotesToAnnotations($full_annot_key, $new_notes) {
	$conn = getNamedConnection(null);
	$r = $conn->UpdateBlob('FULL_ANNOT','NOTES',$new_notes,'NOTES IS NULL AND FULL_ANNOT_KEY='.$full_annot_key,'CLOB');
	return $r;
}

/** 
 * Create References to RGD_IDS table reference in RGD_REF_RGD_ID table
 */
function createReftoObject($refDBKey, $objRgdID, & $returnMessage) {

	//  $allReferencesArray = getBucketItems('REFERENCE_OBJECT_BUCKET'); 
	//  $objectArray = $theform->getValue('objectsFrom'); 
	//  $referenceArray = $theform->getValue('references'); 

	$numAlreadyExisting = 0;
	$numCreated = 0;
	// $refDBKey = $allReferencesArray[$refRGDID]['ref_key'];
	// dump ( $refDBKey ) ; 
	// dump ( $allReferencesArray[$refRGDID]['ref_key']) ; 

	// Verify that it does not exist. If so skip it. 
	$sql = "select * from  RGD_REF_RGD_ID where ref_key = " . $refDBKey . " and rgd_id = " . $objRgdID;
	$resultConstraint = fetchRecord($sql);
	if (count($resultConstraint) > 0) {
		$numAlreadyExisting++;
		if ($numAlreadyExisting > 0) {
			$returnMessage = "\n<!-- Skipped creation of RGD_REF_RGD_ID for ref_key = " . $refDBKey . ' and rgd_id = ' . $objRgdID . " -->\n";
		}
	} else {
		$sqlInsert = 'insert into RGD_REF_RGD_ID ( REF_KEY , RGD_ID ) values ( ' . $refDBKey . ',' . $objRgdID . ')';
		// dump ( $sqlInsert) ; 
		executeUpdate($sqlInsert);
    //$returnMessage = "\n<!-- Created ref of RGD_REF_RGD_ID for ref_key = " . $refDBKey . ' and rgd_id = ' . $objRgdID . " -->\n";
		$numCreated++;
	}

	return $numCreated;
}

/** 
 * Creates annotations in the database after the user has confirmed the assocations to make. 
 * Variable $transferredNotes will get number of annotations that had their notes transferred
 * Returns the number of assocations created. 
 */
function createAnnotations($evidence, $termAcc, $with_info, $notes, $refRGDID, $coreObjectRGDID, $useridKey, $qualifier, &$transferredNotes, &$not4curation) {
	// Core Object information 
	// echo " $evidence, $termAcc, $with_info, $notes, $refRGDID, $coreObjectRGDID  " ; 
	$objArray = getObjectInfoByRGID($coreObjectRGDID);
	$objectSymbol = $objArray['SYMBOL'];
	$objectName = $objArray['NAME'];
	// Need to trim as spaces in the full_annot. can screw up the later
	// export to GOA of these annotations. 
	if ($objectName != null) {
		trim($objectName);
	}
	$objectTypeKey = $objArray['OBJECT_KEY']; //Type of Object from the RGD_OBJECTS table

	// Other fields

	$numCreated = 0;
	$numAlreadyExisting = 0;
	$transferredNotes = 0;
	
	$termArray = getTermInfoByTermAcc($termAcc);
	$termName = $termArray['TERM'];
	$termAcc = $termArray['TERM_ACC'];
    $aspect = $termArray['ASPECT'];
	$not4curation = $termArray['NOT4CURATION']; // 0 or 1
	
	if( $not4curation>0 ) {
		// this term is tagged as 'Not4Curation'
		$not4curation = 1;
		return $numCreated;
	}

    if ($with_info=='') {
        $with_info=null;
        $with_info_str='null';
    }else {
        $with_info_str=dbQuoteString($with_info);
    }

	$sql = 'INSERT INTO full_annot ('.
		'full_annot_key, '.
		'term, '.
		"ANNOTATED_OBJECT_RGD_ID," .
		"RGD_OBJECT_KEY, " .
		"DATA_SRC," .
		"OBJECT_SYMBOL," .
		"REF_RGD_ID," .
		"EVIDENCE," .
		"WITH_INFO," .
		"ASPECT," .
		"OBJECT_NAME," .
		"NOTES," .
		"TERM_ACC," .
		"CREATED_BY," .
		"LAST_MODIFIED_BY," .
		"CREATED_DATE," .
		'last_modified_date,' .
		'curation_flag,' .
		'qualifier'.
	') VALUES ( '.
	"FULL_ANNOT_SEQ.NEXTVAL," .
	dbQuoteString($termName) . "," . // from ONT_TERMS.TERM
	$coreObjectRGDID . ", " . // Object RGD ID
	$objectTypeKey . "," . //RGD_OBJECT_KEY
	"'RGD'," . // Hardcoded ? 
	dbQuoteString($objectSymbol).','.
	$refRGDID . ','.
	dbQuoteString($evidence) . ','.
	$with_info_str . ','.
	dbQuoteString($aspect) . ','.
	dbQuoteString($objectName) . ','.
	dbQuoteString($notes) . ','.
	dbQuoteString($termAcc) . ','.
	$useridKey . ','.
	$useridKey . ','.
	"SYSDATE, SYSDATE, 'DO',".
	dbQuoteString($qualifier) . ")";

	 dump ( "SQL " . $sql ) ;
	// Check for constraint 
	$full_annot_key = 0;
	if (!verifyLinkConstraints($termAcc, $coreObjectRGDID, $refRGDID, $evidence, $with_info, $qualifier, $full_annot_key)) {

		// This record already exists -- upgrade notes in already existing annotations
		if( $full_annot_key>0 && isReallySet($notes) && strlen($notes)>0 ) {
			transferNotesToAnnotations($full_annot_key, $notes);
			$transferredNotes ++;
		}
		$numAlreadyExisting++;
	} else {
		$rowsUpdated = executeUpdate($sql);

        //if nothing updated the src check to see if source is different than RGD and update.
        	//            @TERM_ACC
        	//            @ANNOTATED_OBJECT_RGD_ID
        	//            @REF_RGD_ID
        	//            @EVIDENCE
        	//            @WITH_INFO
        	//            @QUALIFIER

        if ($rowsUpdated==0) {
            $sql='update full_annot set data_src=\'RGD\' where term_acc= ' . dbQuoteString($termAcc) .
                ' and ANNOTATED_OBJECT_RGD_ID=' . $coreObjectRGDID  .
                  ' and REF_RGD_ID=' . $refRGDID .
                   ' and EVIDENCE=' . dbQuoteString($evidence);

                   if ($with_info === null) {
                        $sql = $sql . ' and WITH_INFO is null ';
                    }else {
                        $sql = $sql . ' and WITH_INFO=' . dbQuoteString($with_info);
                    }
                    $sql = $sql . ' and QUALIFIER= ' . dbQuoteString($qualifier);

            dump($sql);
            $rowsUpdated = executeUpdate($sql);
        }


		$numCreated++;
	}

	// dump ( $resultArray ) ;
	return $numCreated;

}

/**
 *  Process the Object to Reference relationships in the form curation_linkCoreToRef() returns array  
 *  of objects to be displayed to the user for approval. 
 */
function processReference($theform) {

	$objectArray = $theform->getValue('objectsFrom');
	$referenceArray = $theform->getValue('references');
	$resultArray = array ();
	//dump($objectArray);
	// dump($referenceArray);

	foreach ($objectArray as $objkey => $objvalue) {
		$objectArray = getObjectInfoByRGID($objvalue);
		$objectname = $objectArray['SYMBOL'];
		foreach ($referenceArray as $refkey => $refvalue) {

			// dump ( $objvalue) ; dump (  $refvalue) ; dump (  $ontvalue) ; 
			$row = array (
				'objectname' => $objectname,
				"objvalue" => $objvalue,
				'refkey' => $refkey,
				'refvalue' => $refvalue
			);
			$resultArray[] = $row;
		}
	}
	//dump ( $resultArray ) ; 
	return $resultArray;

}

/**
 * Create the final process annotation form where the user sees the permutations of links being created. 
 * They also get the chance to unselect certain fields from being created. 
 * 
 */
function processAnnotationForm($theform) {

    print ('in process annotation form');
	$theRelform = newForm("Create Selected Annotations in DataBase", 'GET', 'curation', 'createAnnotationRelationship');
	
	$allObjectsArray = getObjectArrayFromSession();
	$formObjectArray = $theform->getValue('objectsFrom');
	$referenceArray = $theform->getValue('references');
	$onttermArray = $theform->getValue('ontterms');
	$ontTermsSessionArray = getOntTermsArrayFromSession();
	// Other fields to be added to the form once, they do not vary per row 
	$qualifier = $theform->getValue('qualifier');
	$primeEvidence = $theform->getValue('evidence');
	$with_info = $theform->getValue('with_info');
	$notes = $theform->getValue('notes');
	//  $resultArray = array(); 
	// dump ( $objectArray)  ;  dump (  $referenceArray) ; dump (  $onttermArray) ;
	$relCnt = 0; // keep tack of each row being added.  
	$relRejectedCnt = 0; // keep tack of each row being rejected.  
	
	foreach ($allObjectsArray as $objkey => $objvalue) {
		$autoAnn = true;
		$evidence = '';
		$objectArray = getObjectInfoByRGID($objkey);
		$objectname = $objectArray['SYMBOL'];
		$objType = $objectArray['TYPE'];
		$species = $objectArray['SPECIES'];
		$cur_with_info = $with_info;
		foreach ($formObjectArray as $formObjkey => $formObjvalue) {
			if ($formObjvalue == $objkey) {
				$evidence = $primeEvidence;
				$cur_with_info = $with_info;
				$autoAnn = false;
				break;
			}
			$formObject = getObjectInfoByRGID($formObjvalue);
			$formObjectname = $formObject['SYMBOL'];
			$formSpecies = $formObject['SPECIES'];
			if (isOrtholog($objkey, $formObjvalue)) {
				switch ($primeEvidence) {
					case 'IAGP':
					case 'IEP':
					case 'IMP':
					case 'IDA':
					case 'TAS':
					case 'IGI':
					case 'EXP':
					case 'HTP':
					case 'HEP':
					case 'HMP':
					case 'HDA':
					case 'HGI':
						$evidence = 'ISO';
						$cur_with_info = getIsoWithInfo($objkey, $formObjectArray); 
				}
			}
		};
		if ($evidence == '') continue;
		foreach ($referenceArray as $refkey => $refvalue) {
			foreach ($onttermArray as $ontkey => $ontvalue) {
				if ($autoAnn && substr($ontvalue, 0, 2) != 'DO'
						&& substr($ontvalue, 0, 2) != 'PW' && substr($ontvalue, 0, 5) != 'CHEBI') break;
				// restrict IGI orthologous ISO to disease terms
				if ($autoAnn && $primeEvidence === 'IGI' && $evidence === 'ISO' && substr($ontvalue, 0, 2) != 'DO' ) break;

				$oTermDescription = $ontTermsSessionArray[$ontvalue]; // f.e. '[C] clathrin complex'
				$oTermAspect = substr($oTermDescription, 1, 1); // term aspect, f.e. 'C'
				if (checkECwithOntTermsPerAnnotation($theform, $evidence, $ontvalue, $oTermAspect, $oTermDescription) && 
						checkSpecialAnnotation($theform, $objkey, $ontvalue, $refvalue) &&
						checkOntologyAnnotations($theform, $oTermAspect, $species, $objType, $evidence) ) {
					$theRelform->addReadOnlyLabel('objectnameL' . $relCnt, $objectname	);
					$theRelform->addHidden('objectname' . $relCnt, $objectname	);
					$theRelform->addHidden('species' . $relCnt, $species);
					$theRelform->addHidden('objvalue' . $relCnt, $objkey);
					$theRelform->addReadOnlyLabel('refvalueL' . $relCnt, $refvalue);
					$theRelform->addHidden('refvalue' . $relCnt, $refvalue);
					$theRelform->addHidden('ontvalue' . $relCnt, $ontvalue);
	                $theRelform->addReadOnlyLabel('evidenceL' . $relCnt, $evidence);
	                $theRelform->addHidden('evidence' . $relCnt, $evidence);
	                $theRelform->addReadOnlyLabel('ontDesc' . $relCnt, $oTermDescription);
	                $theRelform->addReadOnlyLabel('with_infoL'. $relCnt, $cur_with_info);
	                $theRelform->AddHidden('with_info' . $relCnt, $cur_with_info);
	                 
					$theRelform->addCheckbox('select' . $relCnt, ''); // include this row in database ?
					$theRelform->setDefault('select' . $relCnt, 'on'); // set all to checked on by default
					
					
					// over fields
					
					$relCnt++;
				} else $relRejectedCnt ++;
			}
		}
	}
	// fields that don't vary per row. 
	$theRelform->AddHidden('notes', $theform->getValue('notes'));
	$theRelform->addReadOnlyLabel('with_infoL', $theform->getValue('with_info'));
//	$theRelform->addReadOnlyLabel('evidenceL', $theform->getValue('evidence'));
	$theRelform->AddHidden('with_info', $theform->getValue('with_info'));
	$theRelform->AddHidden('evidence', $theform->getValue('evidence'));
	$theRelform->addReadOnlyLabel('qualifierL', $theform->getValue('qualifier'));
	$theRelform->AddHidden('qualifier', $theform->getValue('qualifier'));
	$theRelform->AddHidden('relCount', $relCnt);
	$theRelform->AddHidden('command', 'create');
	
	// dump ( $theRelform ) ;

	if ($relRejectedCnt > 0) $theform->addFormErrorMessage("Only " . $relCnt . " annotation(s) can be made. " . $relRejectedCnt . " annotation(s) have been rejected because of the reasons shown above.");
	return $theRelform;

}

function getIsoWithInfo($isoRgdId, $formObjectArray) {
	// compute with info for ISO annotations if there are multiple primary objects
	$iso_with_info = '';
	foreach ($formObjectArray as $formObjkey => $formObjvalue) {
		// skip non-orthologs
		if( isOrtholog($isoRgdId, $formObjvalue) ) {
			if( $iso_with_info == '' ) {
				$iso_with_info = 'RGD:' . $formObjvalue;
			} else {
				$iso_with_info .= '|RGD:' . $formObjvalue;
			}
		}
	}
	return $iso_with_info;
}

function generateCoreToCoreForm($theform) {

	$toString = $theform->formStart();
	$toString .= $theform->startGroup('From Core Objects ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsFrom');
	$toString .= $theform->endGroup();
	$toString .= "<p></p>";
	$toString .= $theform->startGroup('To Core Objects ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsTo');
	$toString .= $theform->endGroup();
	$toString .= $theform->formEnd();
	return $toString;
}

function generateQTLtoQTLForm($theform) {

	$toString = $theform->formStart();
	$toString .= $theform->startGroup('From QTL Objects ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsFrom');
	$toString .= $theform->endGroup();
	$toString .= "<p></p>";
	$toString .= $theform->startGroup('To QTL Objects ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsTo');
	$toString .= $theform->endGroup();
	$toString .= "<p></p>";
	$toString .= $theform->startGroup('References ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'references');
	$toString .= $theform->endGroup();
	$toString .= $theform->formEnd();
	return $toString;
}

function generateQTLtoQTLRelationshipForm($theform) {

	// get QTL types for drop down lists
	$qtlTypeArray = getQTLTypeArray();

	$fromObjectsArray = $theform->getValue('objectsFrom');
	//echo "from"; 
	// dump ( $fromObjectsArray ); 
	$toObjectsArray = $theform->getValue('objectsTo');
	$relationshipArray = $theform->getValue('references');
	//echo "to"; 
	// dump ( $toObjectsArray );
	// dump ( $relationshipArray );  
	$theRelform = newForm('Create Relationships', 'GET', 'curation', 'createQTLtoQTLRelationship');
	$relCnt = 0;
	// used if we get a skip a QTL to QTL match
	$warningMessage = '';
	foreach ($fromObjectsArray as $key => $fromObjectRGDID) {
		foreach ($toObjectsArray as $key => $toObjectRGDID) {
			foreach ($relationshipArray as $key => $relationshipRGDID) {
				$toObjArray = getObjectInfoByRGID($toObjectRGDID);
				$fromObjArray = getObjectInfoByRGID($fromObjectRGDID);
				$refObjArray = getObjectInfoByRGID($relationshipRGDID);

				$theRelform->addReadOnlyLabel('fromRel' . $relCnt, $fromObjArray['SYMBOL']);
				$theRelform->addHidden('fromRelH' . $relCnt, $fromObjArray['ID']);
				$theRelform->addSelect('rel' . $relCnt, ' ', $qtlTypeArray, true);
				$theRelform->addReadOnlyLabel('toRel' . $relCnt, $toObjArray['SYMBOL']);
				$theRelform->addHidden('toRelH' . $relCnt, $toObjArray['ID']);
				$theRelform->addReadOnlyLabel('refDesc' . $relCnt, $refObjArray['TITLE']);
				$theRelform->addHidden('refH' . $relCnt, $relationshipRGDID);
				$theRelform->setDefault('rel' . $relCnt, 3); // set default  
				$relCnt++;
			}
		}
	}
	$theRelform->addHidden('relCount', $relCnt);
	return $theRelform;
}

/**
 * Generate the relationship form between CORE to CORE objects after the user has selected
 * Objects from the Core To Core Form
 */
function generateCoreToCoreRelationshipForm($theform) {

	$fromObjectsArray = $theform->getValue('objectsFrom');
	//echo "from"; 
	//dump ( $fromObjectsArray ); 
	$toObjectsArray = $theform->getValue('objectsTo');
	//echo "to"; 
	//dump ( $toObjectsArray ); 
	$theRelform = newForm('Create Relationships', 'GET', 'curation', 'createCoretoCoreRelationship');
	$relCnt = 0;
	// used if we get a skip a QTL to QTL match
	$warningMessage = '';
	foreach ($fromObjectsArray as $key => $fromObjectRGDID) {
		foreach ($toObjectsArray as $key => $toObjectRGDID) {
			$toGeneType = '';
			$fromGeneType = '';
			$toObjArray = getObjectInfoByRGID($toObjectRGDID);
			$fromObjArray = getObjectInfoByRGID($fromObjectRGDID);

      // Skip QTL to QTL links , they are done in seperate tool
      if (($toObjArray['TYPE'] == 'Q') && ( $fromObjArray['TYPE'] == 'Q' )) {
        continue;
      }
			// Get GeneTypes to display  if we're dealing with genes
     
			if ($toObjArray['TYPE'] == 'G') {
				addItemToBucket('CCC', $toObjArray['GENE_TYPE']. ":" . $toObjectRGDID ) ;
				if ($toObjArray['GENE_TYPE'] != 'gene' && $toObjArray['GENE_TYPE'] != NULL ) {
					$toGeneType = "<br>(" . $toObjArray['GENE_TYPE'] . ")";
				}
			}
			if ($fromObjArray['TYPE'] == 'G') {
				if ($fromObjArray['GENE_TYPE'] != 'gene' && $fromObjArray['GENE_TYPE'] != NULL) {
					$fromGeneType = "<br>(" . $fromObjArray['GENE_TYPE'] . ")";
				}
			}

			$theRelform->addReadOnlyLabel('fromRelType' . $relCnt, getObjectTypeByAbbr($fromObjArray['TYPE']) . $fromGeneType);
			$theRelform->addReadOnlyLabel('toRelType' . $relCnt, getObjectTypeByAbbr($toObjArray['TYPE']) . $toGeneType);
			$theRelform->addReadOnlyLabel('fromRel' . $relCnt, $fromObjArray['SYMBOL']);
			$theRelform->addHidden('fromRelH' . $relCnt, $fromObjArray['ID']);
			$theRelform->addReadOnlyLabel('rel' . $relCnt, '12: direct ortholog - RGD manual');
			$theRelform->addReadOnlyLabel('toRel' . $relCnt, $toObjArray['SYMBOL']);
			$theRelform->addHidden('toRelH' . $relCnt, $toObjArray['ID']);
			$relCnt++;
		}
	}
	$theRelform->addHidden('relCount', $relCnt);
	return $theRelform;
}

/** 
 * Create QTL to QTL Relationships
 */

function curation_createQTLtoQTLRelationship() {

	// Build dynamic form based on number of fields 
	$theRelform = newForm('CreateRelationships', 'GET', 'curation', 'linkQTLtoQTL');
	$relCnt = getRequestVarNum('relCount');
	for ($i = 0; $i < $relCnt; $i++) {
		// $theRelform->addReadOnlyLabel('fromRelType'. $i  , '');  
		$theRelform->addReadOnlyLabel('fromRel' . $i, '');
		$theRelform->addReadOnlyLabel('toRel' . $i, '');
		$theRelform->addReadOnlyLabel('ftoRelH' . $i, '');
	}
	switch ($theRelform->getState()) {
		case INITIAL_GET :
			echo "INITIAL_GET<br>";
		case SUBMIT_INVALID :
			echo "INITIAL_GET<br>";
			$theRelform->addFormErrorMessage("ERROR again");
			// dump( $theRelform) ;  
			redirectWithMessage('Try again', makeUrl('curation', 'linkCoreToCore'));
			break;
		case SUBMIT_VALID :
			$theRelform->addFormErrorMessage("BUY again");
			//  dump( $theRelform) ;
			$returnMessage = '';
			for ($i = 0; $i < $relCnt; $i++) {
				$relFromRGDID = getRequestVarString('fromRelH' . $i);
				$toFromRGDID = getRequestVarString('toRelH' . $i);
				$refRGDID = getRequestVarString('refH' . $i);
				$toRel = getRequestVarString('rel' . $i);
				// $returnMessage .=  $relFromRGDID . "> ". $toRel . ">" . $toFromRGDID . "<br>";
				createQTLtoQTLLinks($relFromRGDID, $toFromRGDID, $refRGDID, $toRel, $returnMessage);
			}

			redirectWithMessage($returnMessage, makeUrl('curation', 'linkQTLtoQTL'), $theRelform->getValues());
	}
}

/**
 * From the linkCoretoCore page this function actually creates the relationships in the database
 * 
 */
function curation_createCoretoCoreRelationship() {

	// Build dynamic form based on number of fields 
	$theRelform = newForm('CreateRealtionships', 'GET', 'curation', 'linkCoreToCore');
	$relCnt = getRequestVarNum('relCount');
	for ($i = 0; $i < $relCnt; $i++) {
		$theRelform->addReadOnlyLabel('fromRelType' . $i, '');
		$theRelform->addReadOnlyLabel('fromRel' . $i, '');
		$theRelform->addReadOnlyLabel('toRel' . $i, '');
		$theRelform->addReadOnlyLabel('ftoRelH' . $i, '');
	}
	switch ($theRelform->getState()) {
		case INITIAL_GET :
			echo "INITIAL_GET<br>";
		case SUBMIT_INVALID :
			// echo "INITIAL_GET<br>";
			$theRelform->quickRender(); 
			// dump( $theRelform) ;  
			return; 
			break;
		case SUBMIT_VALID :
			$theRelform->addFormErrorMessage("BUY again");
			//  dump( $theRelform) ;
			$returnMessage = '';
			for ($i = 0; $i < $relCnt; $i++) {
				$relFromRGDID = getRequestVarString('fromRelH' . $i);
				$toFromRGDID = getRequestVarString('toRelH' . $i);
				// $returnMessage .=  $relFromRGDID . ">" . $toFromRGDID . "<br>";
				createOBJtoOBJLinks($relFromRGDID,  $toFromRGDID, $returnMessage);
			}

			redirectWithMessage($returnMessage, makeUrl('curation', 'linkCoreToCore'), $theRelform->getValues());
	}
}

/**
 * From the linkCoretoRef page this function actually creates the relationships in the database
 * 
 */
function curation_createCoretoRefRelationship() {

	// Build dynamic form based on number of fields 
	$theRelform = newForm('CreateRealtionships', 'GET', 'curation', 'linkCoreToRef');
	$relCnt = getRequestVarNum('relCount');
	for ($i = 0; $i < $relCnt; $i++) {
		$theRelform->addReadOnlyLabel('fromRelType' . $i, '');
		$theRelform->addReadOnlyLabel('toRelType' . $i, '');
		$theRelform->addReadOnlyLabel('fromRel' . $i, '');
		$theRelform->addReadOnlyLabel('ftoRelH' . $i, '');
	}
	switch ($theRelform->getState()) {
		case INITIAL_GET :
			echo "INITIAL_GET<br>";
		case SUBMIT_INVALID :
			echo "INITIAL_GET<br>";
			$theRelform->addFormErrorMessage("ERROR again");
			// dump( $theRelform) ;  
			redirectWithMessage('Try again', makeUrl('curation', 'linkCoreToCore'));
			break;
		case SUBMIT_VALID :
			$theRelform->addFormErrorMessage("BUY again");
			//  dump( $theRelform) ;
			$returnMessage = '';
			for ($i = 0; $i < $relCnt; $i++) {
				$relFromRGDID = getRequestVarString('fromRelH' . $i);
				$toFromRGDID = getRequestVarString('toRelH' . $i);

				// $returnMessage .=  $relFromRGDID . "> ". $toRel . ">" . $toFromRGDID . "<br>";
				createObjtoRefLinks($relFromRGDID, $toFromRGDID, $returnMessage);
			}

			redirectWithMessage($returnMessage, makeUrl('curation', 'linkCoreToRef'), $theRelform->getValues());
	}
}

/**
 * Create Object to Reference Link in the database
 */
function createObjtoRefLinks($fromObjID, $toRelID, & $returnMessage) {
	$returnMessage .= "<ul>\n";
	$relObject = getObjectInfoByRGID($toRelID);
	$fromObject = getObjectInfoByRGID($fromObjID);
	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from rgd_ref_rgd_id where rgd_id = ' . $fromObject['ID'] . " and ref_key = " . $relObject['REF_KEY'];
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $fromObject['SYMBOL'] . ' to "' . $relObject['TITLE'] . '" as it already existed</li></ul>';
		return;
	}

	// Set $relTypeID to String NULL if net picked by the user to represent no relationship. 
	if (!isReallySet($toRelID)) {
		$toRelID = 'NULL';
	}
	// Create new relationship
	$sql = "insert into rgd_ref_rgd_id ( rgd_id , ref_key ) values ( " . $fromObject['ID'] . ',' . $relObject['REF_KEY'] . ')';
	// $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {

		$returnMessage .= "<li>Created relationship for " . $fromObject['SYMBOL'] . ' to ' . $relObject['TITLE'] . '</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $fromObject['SYMBOL'] . ' to "' . $relObject['TITLE'] . '" as it already existed.</li>';
	}
	$returnMessage .= "</ul>\n";
}

/**
 * Create QTL to QTL Relationship
 */
function createQTLtoQTLLinks($fromID, $toID, $refRGDID, $refID, & $returnMessage) {

	$fromObject = getObjectInfoByRGID($fromID);
	$toObject = getObjectInfoByRGID($toID);
	$refObject = getObjectInfoByRGID($refRGDID);
	$returnMessage .= "<ul>";
	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from related_qtls where qtl_key1 = ' . $fromObject['QTL_KEY'] . " and qtl_key2 = " . $toObject['QTL_KEY'] . " and qtl_rel_type_key = " . $refID;
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $fromObject['SYMBOL'] . ' to ' . $toObject['SYMBOL'] . ' and reference " ' . $refObject['TITLE'] . '" as it already existed</li></ul>';
		$returnMessage .= $sqlcheck;
		return;
	}

	//  // Set $relTypeID to String NULL if net picked by the user to represent no relationship. 
	//  if ( ! isReallySet( $relTypeID )) { 
	//    echo "got it " ; 
	//    $relTypeID = 'NULL'; 
	//  } else { 
	//   dump ( $relTypeID ) ; 
	//  }
	// Create new relationship
	$sql = "insert into related_qtls ( qtl_key1 , qtl_key2, ref_key, qtl_rel_type_key ) values ( " . $fromObject['QTL_KEY'] . ',' . $toObject['QTL_KEY'] . ',' . $refObject['REF_KEY'] . ', ' . $refID . ')';
	// GK $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {

		$returnMessage .= "<li>Created relationship for " . $fromObject['SYMBOL'] . ' to ' . $toObject['SYMBOL'] . ' and reference " ' . $refObject['TITLE'] . ' "</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $fromObject['SYMBOL'] . ' to ' . $toObject['SYMBOL'] . ' and reference " ' . $refObject['TITLE'] . '" as it already existed</li>';
	}
	$returnMessage .= "</ul>\n";

}
/**
 * Create Object to Object links in the database given any type of Core Object
 */
function createOBJtoOBJLinks($fromID,  $toID, & $returnMessage) {

	$toObject = getObjectInfoByRGID($toID);
	$fromObject = getObjectInfoByRGID($fromID);
	$returnMessage .= "<ul>\n";
	if ($toObject['TYPE'] == 'P' || $fromObject['TYPE'] == 'P') {
		//Phenotypes relationships all go in one table
		$returnMessage .= "Phenotypes to Objects entry created<br>\n";
		relationshipCreateOtoP($fromObject, $toObject, $returnMessage);

	}
	elseif ($toObject['TYPE'] == 'S' || $fromObject['TYPE'] == 'S') {
		// Strain relationships
		if ($toObject['TYPE'] == 'Q' || $fromObject['TYPE'] == 'Q') {
			// $returnMessage .= "Strain to QTL entry created<br>\n";
			relationshipCreateSTtoQ($fromObject, $toObject, $returnMessage);
		} else {
			// All others
			// $returnMessage .= "Strain to non-QTL entry created<br>\n";
			relationshipCreateSTtoNonQ($fromObject, $toObject, $returnMessage);
		}

	}
	elseif ($toObject['TYPE'] == 'G' || $fromObject['TYPE'] == 'G') {

		if ($toObject['TYPE'] == 'Q' || $fromObject['TYPE'] == 'Q') {
			// $returnMessage .= "Gene to QTL entry created<br>\n";
			relationshipCreateQtoG($fromObject, $toObject, $returnMessage);
		}
		elseif ($toObject['TYPE'] == 'SS' || $fromObject['TYPE'] == 'SS') {
			// $returnMessage .= "Gene to SSLP entry created<br>\n";
			relationshipCreateSStoG($fromObject, $toObject, $returnMessage);
		} else {
			// gene to gene Relationship throught the GENE_VARIATIONS table. 
			relationshipCreateGtoG($fromObject, $toObject, $returnMessage);
			//$returnMessage .= "Sorry ,  " . $fromObject['TYPE'] . ":" . $fromObject['SYMBOL'] . " cannot be linked to " . 
			// $toObject['TYPE'] . ":" . $toObject['SYMBOL'] . " <br>\n";
		}
	}
	elseif ($toObject['TYPE'] == 'Q' && $fromObject['TYPE'] == 'Q') {
		$returnMessage .= "<li>QTL  " . $fromObject['SYMBOL'] . " cannot be linked to QTL " . ":" . $toObject['SYMBOL'] . ". Please use this link to make this type of " . makeLink('link', 'curation', 'linkQTLtoQTL') . "</li>\n";
	} else {
		$returnMessage .= "<li>Sorry ,  " . $fromObject['TYPE'] . ":" . $fromObject['SYMBOL'] . " cannot be linked to " .
		$toObject['TYPE'] . ":" . $toObject['SYMBOL'] . " </li>\n";
	}
	$returnMessage .= "</ul>\n";
}

/**
 * From the linkCoretoCore page this function actually creates the relationships in the database
 * 
 */
function curation_createAnnotationRelationship() {

	// Number of records actually created
	$numCreated = 0;
	$numSkipped = 0;
	$numAlreadyExisted = 0;
	$numTransferredNotes = 0;
	$numNot4Curation = 0;
	
	$relCnt = getRequestVarNum('relCount');
	$useridKey = getSessionVar('userKey');

	$qualifier = getRequestVarString('qualifier');
	$notes = getRequestVarString('notes');

	$returnMessage = '';
	for ($i = 0; $i < $relCnt; $i++) {
		$ontKeyRGDID = getRequestVarString('ontvalue' . $i);
		$refRGDID = getRequestVarString('refvalue' . $i);
		$coreObjectRGDID = getRequestVarString('objvalue' . $i);
		$evidence = getRequestVarString('evidence' . $i);
		$with_info = getRequestVarString('with_info' . $i);
		$with_info = trim($with_info);
		$selected = getRequestVarString('select' . $i);
		if ($selected == 'on') {
			$transferredNotes = 0;
			$not4curation = 0;
			$numCreated += createAnnotations($evidence, $ontKeyRGDID, $with_info, $notes, $refRGDID, $coreObjectRGDID, $useridKey, $qualifier, $transferredNotes, $not4curation);
			if( $transferredNotes>0 ) {
				$numTransferredNotes += $transferredNotes;
			}
			if( $not4curation>0 ) {
				$numNot4Curation += $not4curation;
			}
			// $returnMessage .= "$numCreated, $evidence, $ontKeyRGDID, $with_info, $notes, $refRGDID, $coreObjectRGDID , $useridKey<br> ";
			$ref = getObjectInfoByRGID($refRGDID);
			$refDBKey = $ref['REF_KEY'];
			$numCreatedRO = createReftoObject($refDBKey, $coreObjectRGDID, $returnMessage);
		} else {
			$numSkipped++;
		}
	}
	$numAlreadyExisted = $relCnt - $numCreated - $numSkipped - $numNot4Curation;
	$returnMessage .= $numCreated . " Annotation(s) have been created in the database. <br>\n";
	if ($numAlreadyExisted > 0) {
		$returnMessage .= $numAlreadyExisted . " Annotation(s) have been skipped as they already existed in the database. <br>\n";
	}
	if ($numNot4Curation > 0) {
		$returnMessage .= $numNot4Curation . " Annotation(s) have been skipped as the terms are Not4Curation. <br>\n";
	}
	if ($numSkipped > 0) {
		$returnMessage .= $numSkipped . " Annotation(s) have been skipped per your request. <br>\n";
	}
	if ($numTransferredNotes > 0) {
		$returnMessage .= "For ".$numTransferredNotes . " annotation(s) notes were transferred, per RGDD-727. <br>\n";
	}
	$path=$_SERVER['QUERY_STRING'];
 	$new_path = str_replace("&func=createAnnotationRelationship&", "&func=linkAnnotation&", $path);
    
    redirectWithMessage($returnMessage, "?" . $new_path);
}

function relationshipCreateGtoG($geneFromObject, $geneToObject, & $returnMessage) {

  if( $geneFromObject['ID'] == $geneToObject['ID'] ) {
    return;
  }
  
  // At this point we're creating an Ortholog relationship for the genes in question. 
  $userKey = getSessionVar('userKey'); // Userid for the user currently signed in. 
  
  // Verify that the relationship does not already exist. 
  $sqlcheck = 'select * from GENETOGENE_RGD_ID_RLT where SRC_RGD_ID=' . $geneFromObject['ID'] . ' and DEST_RGD_ID=' . $geneToObject['ID'];
  $resultArray = fetchRecords($sqlcheck);
  $gtog = "$geneFromObject[SYMBOL] ($geneFromObject[SPECIES]) to $geneToObject[SYMBOL] ($geneToObject[SPECIES])";
  if (count($resultArray) > 0) {
    $returnMessage .= "<li>Relationship already exists for $gtog.</li>";
  }
  else {  
    // Create new Ortholog relationship
    $sql = 'insert into GENETOGENE_RGD_ID_RLT (GENETOGENE_KEY, SRC_RGD_ID, DEST_RGD_ID, XREF_DATA_SRC, XREF_DATA_SET, ORTHOLOG_TYPE_KEY, CREATED_BY, LAST_MODIFIED_BY, CREATED_DATE, LAST_MODIFIED_DATE ) values (' . 
    'GENETOGENE_RGD_ID_RLT_SEQ.NEXTVAL,' . 
    $geneFromObject['ID'] . ',' . 
    $geneToObject['ID'] . ',' . 
    " 'RGD', 'RGD', 12, " . // ortholog_type=12: manual ortholog
    $userKey . ',' . $userKey . ',' . 
    ' sysdate, sysdate' . 
    ')';
  
    $rowsUpdated = executeUpdate($sql);
    if ($rowsUpdated == 1) {
      $returnMessage .= "<li>Created relationship for $gtog.</li>";
	}
  }
  
  // Try reverse relationship as both are needed

  // Verify that the reverse relationship does not already exist. 
  $sqlcheck = 'select * from GENETOGENE_RGD_ID_RLT where SRC_RGD_ID=' . $geneToObject['ID'] . ' and DEST_RGD_ID=' . $geneFromObject['ID'];
  $resultArray = fetchRecords($sqlcheck);
  $gtog = "$geneToObject[SYMBOL] ($geneToObject[SPECIES]) to $geneFromObject[SYMBOL] ($geneFromObject[SPECIES])";
 
  if (count($resultArray) > 0) {
    $returnMessage .= "<li>Relationship already exists for $gtog.</li>";
  }
  else {
    // Create new reverse ortholog relationship
    $sql = 'insert into GENETOGENE_RGD_ID_RLT (GENETOGENE_KEY, SRC_RGD_ID, DEST_RGD_ID, XREF_DATA_SRC, XREF_DATA_SET, ORTHOLOG_TYPE_KEY, CREATED_BY, LAST_MODIFIED_BY, CREATED_DATE, LAST_MODIFIED_DATE ) values (' . 
	  'GENETOGENE_RGD_ID_RLT_SEQ.NEXTVAL,' . 
      $geneToObject['ID'] . ',' . 
      $geneFromObject['ID'] . ',' . 
    " 'RGD', 'RGD', 12, " . // ortholog_type=12: manual ortholog
      $userKey . ',' . $userKey . ',' . 
      ' sysdate, sysdate' . 
      ')';
    $rowsUpdated = executeUpdate($sql);
    if ($rowsUpdated == 1) {
      $returnMessage .= "<li>Created relationship for $gtog.</li>";
	}
  }
}

/*
 * Create SSLP to gene Reference
 */
function relationshipCreateSStoG($fromObject, $toObject, & $returnMessage) {

	// Handle that eather object may be the SSLP and the other is a gene 
	if ($fromObject['TYPE'] == 'G') {
		$geneObject = $fromObject;
		$sslpObject = $toObject;
	} else {
		$geneObject = $toObject;
		$sslpObject = $fromObject;
	}

	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from rgd_gene_sslp where gene_key = ' . $geneObject['GENE_KEY'] . " and SSLP_KEY = " . $sslpObject['SSLP_KEY'];
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $geneObject['SYMBOL'] . ' to ' . $sslpObject['SYMBOL'] . ' as it already existed</li>';
		return;
	}

	// Create new relationship
	$sql = "insert into rgd_gene_sslp ( gene_key , sslp_key ) values ( " . $geneObject['GENE_KEY'] . ',' . $sslpObject['SSLP_KEY'] . ')';
	// GK $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {
		$returnMessage .= "<li>Created relationship for " . $geneObject['SYMBOL'] .
		' to ' . $sslpObject['SYMBOL'] . '</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $geneObject['SYMBOL'] .
		' to ' . $sslpObject['SYMBOL'] . ' as it already existed.</li>';
	}

}

function relationshipCreateQtoG($fromObject, $toObject, & $returnMessage) {

	// Handle that eather object may be the Homolog and the onther had the RGD_ID that we want to link to. 
	if ($fromObject['TYPE'] == 'G') {
		$geneObject = $fromObject;
		$qtlObject = $toObject;
	} else {
		$geneObject = $toObject;
		$qtlObject = $fromObject;
	}

	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from rgd_gene_qtl where gene_key = ' . $geneObject['GENE_KEY'] . " and QTL_KEY = " . $qtlObject['QTL_KEY'];
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $geneObject['SYMBOL'] . ' to ' . $qtlObject['SYMBOL'] . ' as it already existed</li>';
		return;
	}

	// Create new relationship
	$sql = "insert into rgd_gene_qtl ( gene_key , qtl_key ) values ( " . $geneObject['GENE_KEY'] . ',' . $qtlObject['QTL_KEY'] . ')';
	// GK $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {
		$returnMessage .= "<li>Created relationship for " . $geneObject['SYMBOL'] .
		' to ' . $qtlObject['SYMBOL'] . '</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $geneObject['SYMBOL'] .
		' to ' . $qtlObject['SYMBOL'] . ' as it already existed.</li>';
	}

}

/**
 * Write relationship to database of Strain to QTL, returned the html to the user of the attempt in 
 * the $returnMessage variable. The from and toObjects can be mixed as to their type.
 */
function relationshipCreateSTtoQ($fromObject, $toObject, & $returnMessage) {

	// Handle that eather object may be the Strain and the onther had the RGD_ID that we want to link to. 
	if ($fromObject['TYPE'] == 'S') {
		$strainObject = $fromObject;
		$qtlObject = $toObject;
	} else {
		$strainObject = $toObject;
		$qtlObject = $fromObject;
	}

	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from rgd_qtl_strain where qtl_key = ' . $qtlObject['QTL_KEY'] . " and strain_key = " . $strainObject['STRAIN_KEY'];
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $qtlObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . ' as it already existed</li>';
		return;
	}

	// Create new relationship
	$sql = "insert into rgd_qtl_strain ( qtl_key , strain_key ) values ( " . $qtlObject['QTL_KEY'] . ',' . $strainObject['STRAIN_KEY'] . ')';
	// GK $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {

		$returnMessage .= "<li>Created relationship for " . $qtlObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . '</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $qtlObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . ' as it already existed.</li>';
	}

}

/**
 * Write relationship to database of Strain to QTL, returned the html to the user of the attempt in 
 * the $returnMessage variable. The from and toObjects can be mixed as to their type.
 */
function relationshipCreateSTtoNonQ($fromObject, $toObject, & $returnMessage) {

	// Handle that eather object may be the Strain and the onther had the RGD_ID that we want to link to. 
	if ($fromObject['TYPE'] == 'S') {
		$strainObject = $fromObject;
		$otherObject = $toObject;
	} else {
		$strainObject = $toObject;
		$otherObject = $fromObject;
	}

	// Verify that the relationship does not already exist. 
	$sqlcheck = 'select * from rgd_strains_rgd where rgd_id = ' . $otherObject['ID'] . " and strain_key = " . $strainObject['STRAIN_KEY'];
	$resultArray = fetchRecords($sqlcheck);
	if (count($resultArray) > 0) {
		$returnMessage .= "<li>Could not create relationship for " . $otherObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . ' as it already existed</li>';
		return;
	}

	// Create new relationship
	$sql = "insert into rgd_strains_rgd ( rgd_id , strain_key ) values ( " . $otherObject['ID'] . ',' . $strainObject['STRAIN_KEY'] . ')';
	// GK $returnMessage .=   $sql . "<br>"; 

	$rowsUpdated = executeUpdate($sql);
	if ($rowsUpdated == 1) {

		$returnMessage .= "<li>Created relationship for " . $otherObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . '</li>';
	} else {
		$returnMessage .= "<li>Could not create relationship for " . $otherObject['SYMBOL'] . ' to ' . $strainObject['SYMBOL'] . ' as it already existed.</li>';
	}

}

function relationshipCreateOtoP($fromObject, $toObject, $returnMessage) {

	$returnMessage .= "<li>Phenotypes NOT implemented at this time</li>\n";

}

/**
 * Return the Object Type String given the single leter abbreviation for that
 * Core object type
 */
function getObjectTypeByAbbr($abbr) {

	switch ($abbr) {
		case "G" :
			return "Gene";
			break;
		case "S" :
			return "Strain";
			break;
		case "Q" :
			return "QTL";
			break;
		case "H" :
			return "Homolog";
			break;
		case "SS" :
			return "SSLP";
			break;
		default :
			return "Unknown Type";
	}
}

function generateLinkAnnotaionForm($theform, $geneArray, $refArray = null) {
	$toString = $theform->formStart();
	$toString .= $theform->startGroup('Core Objects');
	$toString .= '<table width=100%><tr><td align=left width=80%>';
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsFrom');
	$toString .= '</td><td width=20% align=right>';
	//$toString .= 'Update Gene Descriptions';
	$toString .= getGeneDescriptionLink($geneArray);
	$toString .= '</td></tr></table>';
	$toString .= $theform->endGroup();
	$toString .= "<p>\n";
	$toString .= $theform->startGroup('Ontology Terms');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'ontterms');
	$toString .= $theform->endGroup();
	$toString .= "<p>\n";
	$toString .= $theform->startGroup('References ');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'references');
//    if ($refArray != null)	$toString .= getReferenceOntoPubLink($refArray);
	$toString .= $theform->endGroup();
	$toString .= '<table width=100%><tr><td align=left width=40%>';

	$toString .= $theform->renderLabeledFieldsInColumns(1, 'qualifier', 'evidence', 'with_info', 'aspect');
	$toString .= '</td><td width=60% align=left valign=bottom>';
	$toString .= '<a name=\'result\'>&nbsp;</a>';
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'notes');
	$toString .= '</td></tr></table>';
	$toString .= $theform->formEnd();

	return $toString;
}


function generateLinkAnnotaionFormHidden($theform, $geneArray) {
//	$toString .= $theform->startGroup('Core Objects');

	$toString = '<table style="display:none" width=100%><tr><td align=left width=80%>';
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'objectsFrom');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'ontterms');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'references');
//	$toString .= $theform->renderLabeledFieldsInColumns(1, 'qualifier', 'evidence', 'with_info', 'aspect', 'notes');
	$toString .= '</td></tr></table>';
	
	return $toString;
}

function generateSearchOntoPubForm($theform) {
	$toString = $theform->formStart();
	$toString .= $theform->startGroup('Core Objects');
	$toString .= $theform->renderLabeledFields('objectsFrom');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'geneCondition');
	$toString .= $theform->renderLabeledFields('looseMatch');
	$toString .= $theform->endGroup();
	$toString .= "<p>\n";
	$toString .= $theform->startGroup('Ontology Terms');
	$toString .= $theform->renderLabeledFields('ontterms');
	$toString .= $theform->renderLabeledFieldsInColumns(1, 'condition');
	$toString .= $theform->endGroup();
	$toString .= "<p>\n";
	$toString .= $theform->formEnd();
	
	return $toString;
}



/**
 * Returns the HTML needed to generate a Button that will pop-up a window allowing for the editing of the 
 * Gene descriptions for genes present in the form. Return blank "" if no genes to link to. 
 */
function getGeneDescriptionLink($geneArray) {
	$returnString = '';
	if ($geneArray != null && count($geneArray) > 0) {
		$returnString = "\n\n";
		$returnString = '<script type="text/javascript"> ' . "\n";
		$returnString .= '<!-- ' . "\n";
		$returnString .= 'function myPopup2(rgdID) { ' . "\n";
		$returnString .= 'window.open( "?module=curation&func=updateGeneDescriptionPopup&rgd_id=".concat(rgdID.toString()), "myWindow", ' . "\n";
		$returnString .= '"status = 1,height=350,width=575,resizable=1,scrollbars=1,dependent=1" ) ' . "\n";
		$returnString .= '} ' . "\n";
		$returnString .= '//--> ' . "\n";
		$returnString .= '</script>' . "\n";

		$returnString .= '<input type="button" onClick="myPopup2(0)" value="Change Gene Descriptions">';
	}
	return $returnString;
}

/**
 * Returns the HTML needed to generate a Button that will open a window to show all references in OntoPub  
 */
function getReferenceOntoPubLink($refArray) {
	$toReturn = '';
	$qs = '';
	$refIdx = 0;
	foreach ($refArray as $rgdId => $title) {
		$qs .= 'qRefRgdIds[' . $refIdx . '].refRgdId='. $rgdId . '&';
		$refIdx ++;
	}
	
	if ($refIdx == 0) return $toReturn;
	$toReturn .= '<script type="text/javascript"> ' . "\n";
	$toReturn .= "var wHandle = null;" . "\n";
	$toReturn .= 'function showRefsInOntoPub() { ' . "\n";
	$toReturn .= ' 
			var rs = "' . $qs . '";
			rs += "&curHost=' .$_SERVER['HTTP_HOST'] .'";
			rs = "https://dev.rgd.mcw.edu/QueryBuilder/getResultForCuration?" + rs;

		    if (wHandle != null && !wHandle.closed) {
				wHandle.location.href=rs;
			} else {
				wHandle = window.open(rs, "_blank", "status = 1,height=750,width=1000,resizable=1,scrollbars=1,dependent=1,toolbar=1,location=1");
			};
			wHandle.focus();
					';
	$toReturn .= 'return false; ' . "\n";
	$toReturn .= '} ' . "\n";
	$toReturn .= '</script>' . "\n";
		
	$toReturn .= '<input type="button" onClick="showRefsInOntoPub()" value="Show All References in OntoMate">';
	return $toReturn;
}

function curation_updateGeneDescriptionPopup() {
    $inputRgdID = getRequestVarString('rgd_id');
	
    setTemplate("pop_up");
	setPageTitle("Update Gene Descriptions");

	$geneArray = getBucketItems('GENE_OBJECT_BUCKET');
	$theForm = newForm('Update Gene Descriptions', 'POST', 'curation', 'updateGeneDescriptionPopup');
	$count = 1;
	foreach ($geneArray as $rgdID => $myArray) {
		//dump ( $myArray);  
		if ($inputRgdID == 0 || $inputRgdID == $rgdID) {
		$geneName = "<b>" . $myArray[$rgdID] . "</b><p>" . makeSpeciesLink($myArray['species']) . "</p>";
		$theForm->addHidden('rgdID' . $count, $rgdID);
		$theForm->AddTextArea('geneDesc' . $count, $geneName . "<br>", 7, 50, 2000, false);
		$theForm->setDefault('geneDesc' . $count, $myArray['geneDesc']);
		// function addTextarea($name, $label, $rows, $cols, $maxchars, $required) {
		$count++;
  	}		
	}
	switch ($theForm->getState()) {
		case INITIAL_GET :
		case SUBMIT_INVALID :
			break;
		case SUBMIT_VALID :
			$numUpdated = updateGeneDescriptions($theForm);
			redirectWithMessage($numUpdated . ' Descriptions Updated', makeURL('curation', 'updateGeneDescriptionPopup'));
	}

	return $theForm->quickRender();
}
/**
 * Update the gene descriptions given the form submitted on the pop-up window.  Returns the number of 
 * records updated. 
 */
function updateGeneDescriptions($theForm) {
	$geneArray = getBucketItems('GENE_OBJECT_BUCKET');

	// Update Bucket Objects first
	$count = 1;

	$updateCount = 0;

	// Cycle over the form fields and see if they have matches in the session. 
	while ($theForm->getValue('rgdID' . $count) != null) {
		//dump ( $theForm->getValue('rgdID' . $count) ) ; 
		$formRgdID = $theForm->getValue('rgdID' . $count);
		$myArray = $geneArray[$formRgdID];
		if ($myArray != null) {
			$myArray['geneDesc'] = $theForm->getValue('geneDesc' . $count);
			//dump ( $myArray['geneDesc'] ) ; 
		}
		// dump ( $myArray); 
		addItemToBucket('GENE_OBJECT_BUCKET', $formRgdID, $myArray);

		$count++;
	}

	// Now update database with new descriptions if they've changed.
	$count = 1; // reset counter 
	while ($theForm->getValue('rgdID' . $count) != null) {
		$rgdIDToUpdate = $theForm->getValue('rgdID' . $count);
		$newDescription = $theForm->getValue('geneDesc' . $count);
		$sql = "select gene_desc from genes where rgd_id = " . $rgdIDToUpdate;
		$resultType = fetchRecord($sql);
		$currentDescription = $resultType['GENE_DESC'];
		if ($currentDescription != $newDescription) {
			// Update record in genes table with new description
			$sql = "update genes set gene_desc = " . dbQuoteString($newDescription) . ' where rgd_id = ' . $rgdIDToUpdate;
			$rowsUpdated = executeUpdate($sql);
			$updateCount += $rowsUpdated;

			// Now update rgd_ids table with last mod time.  
			$sql = "update rgd_ids set last_modified_date = sysdate where rgd_id = " . $rgdIDToUpdate;
			$rowsUpdated = executeUpdate($sql);

		}
		$count++;
	}
	return $updateCount;
}

/**
 * check that at least one object ise selected from each group before permitting processing
 */
function checkProperAnnotSelection($theform) {
	$objectArray = $theform->getValue('objectsFrom');
	$referencesArray = $theform->getValue('references');
	$onttermsArray = $theform->getValue('ontterms');
	if (($objectArray === NULL) or ($referencesArray === NULL) or ($onttermsArray === NULL)) {
		return false;
	} else {
		return true;
	}

}

/**
 * Return array of QTL RelationShip Types for use in drop down lists. 
 */
function getQTLTypeArray() {

	$returnArray = array ();
	$sql = 'select * from QTL_REL_TYPES order by QTL_REL_DESC';
	$resultArray = fetchRecords($sql);
	foreach ($resultArray as $record) {
		extract($record);
		$returnArray[$QTL_REL_TYPE_KEY] = $QTL_REL_DESC;
	}
	return $returnArray;

}

function isOrtholog($rgdIDSrc, $rgdIDDest) {

	$sql = 'select 1 from GENETOGENE_RGD_ID_RLT where SRC_RGD_ID=' . $rgdIDSrc . ' AND DEST_RGD_ID=' . $rgdIDDest;
	$resultArray = fetchRecords($sql);
	
	return !empty($resultArray);
}

/**
 * 
 */
function curation_linkCoreToCore() {
	$toString = '';
	setPageTitle("Assocation Core Object to Core Object");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}

	$theform = newForm('Generate List', 'GET', 'curation', 'linkCoreToCore');
	$objectArray = getObjectArrayFromSession();
	$theform->addMultipleCheckBox('objectsFrom', '', $objectArray, true);
	$theform->addMultipleCheckBox('objectsTo', '', $objectArray, true);
	$theform->addHidden('command', '');

	switch ($theform->getState()) {
		case INITIAL_GET :
			if ($theform->getValue('command') == 'submit') {
				$theform->setDefault('command', NULL);

				$theform->addFormErrorMessage($numCreated . " Annotation(s) have been created in the database. <br>" . $returnMessage . '<br>' . $returnMessageRO);
			}

		case SUBMIT_INVALID :
			return generateCoreToCoreForm($theform);
			break;

		case SUBMIT_VALID :

			$toString .= generateCoreToCoreForm($theform);
			$relationshipForm = generateCoreToCoreRelationshipForm($theform);

			$toString .= makeButtonLink("Cancel", 'curation', 'linkCoreToCore', $theform->getValues());

			$relCount = $relationshipForm->getValue('relCount');
			$toString .= $relationshipForm->formStart();

			$toString .= "<p/>\n";
			if ( $relCount > 0 ) { 
				$table = newTable('From Object', 'From Object Type', 'Relationship', 'To Object', 'To Object Type');
				$table->setAttributes('class="simple" width="100%"');
				for ($i = 0; $i < $relCount; $i++) {
					$table->addRow($relationshipForm->renderLabeledFields('fromRel' . $i), $relationshipForm->renderLabeledFields('fromRelType' . $i), $relationshipForm->renderLabeledFields('rel' . $i), $relationshipForm->renderLabeledFields('toRel' . $i), $relationshipForm->renderLabeledFields('toRelType' . $i));
				}

				$toString .= $table->toHtml();  
				$toString .= "\n";
				$toString .= $relationshipForm->formEnd();
			}
			return $toString;
			break;
		default :
			return generateCoreToCoreForm($theform);

	}
}

/**
 * 
 */
function curation_linkQTLtoQTL() {
	$toString = '';
	setPageTitle("Assocation QTL to QTL");
	if (!userLoggedIn()) {
		return NOTLOGGEDIN_MSG;
	}

	$theform = newForm('Generate List', 'GET', 'curation', 'linkQTLtoQTL');
	$qtlFullArray = getQTLsfromSession();
	$refArray = getReferenceArrayFromSession();
	$theform->addMultipleCheckBox('objectsFrom', '', $qtlFullArray, true);
	$theform->addMultipleCheckBox('objectsTo', '', $qtlFullArray, true);
	$theform->addMultipleCheckBox('references', '', $refArray, true);

	switch ($theform->getState()) {
		case INITIAL_GET :
			if ($theform->getValue('command') == 'submit') {
				$theform->setDefault('command', NULL);

				//processing

				$theform->addFormErrorMessage($numCreated . " Annotation(s) have been created in the database. <br>" . $returnMessage . '<br>' . $returnMessageRO);

				// redirectWithMessage('Added records !', makeUrl('curation', 'linkAnnotation'));
			}

		case SUBMIT_INVALID :
			return generateQTLtoQTLForm($theform);
			break;

		case SUBMIT_VALID :

			$toString .= generateQTLtoQTLForm($theform);

			$relationshipForm = generateQTLtoQTLRelationshipForm($theform);

			$toString .= makeButtonLink("Cancel", 'curation', 'linkQTLtoQTL', $theform->getValues());

			$relCount = $relationshipForm->getValue('relCount');
			$toString .= $relationshipForm->formStart();

			$toString .= "<p/>\n";
			$table = newTable('From QTL', 'Relationship', 'To QTL', 'Reference');
			$table->setAttributes('class="simple" width="100%"');
			for ($i = 0; $i < $relCount; $i++) {
				$table->addRow($relationshipForm->renderLabeledFields('fromRel' . $i), $relationshipForm->renderLabeledFields('rel' . $i), $relationshipForm->renderLabeledFields('toRel' . $i), $relationshipForm->renderLabeledFields('refDesc' . $i));
			}
			$toString .= $table->toHtml();
			$toString .= "\n";
			$toString .= $relationshipForm->formEnd();

			return $toString;
			break;
		default :
			return generateCoreToCoreForm($theform);

	}

}

/**
 * Return array of Ontology Terms from the array key = RGDID and value = 
 */
function getOntTermsArrayFromSession() {

	$termArrayInSession = getBucketItems('TERM_OBJECT_BUCKET');
	$returnArray = array ();
	// Select Terms Button
	if ($termArrayInSession > 0) {
		foreach ($termArrayInSession as $key => $value) {

			$termAcc = NULL;
			$termName = NULL;
			$termAspect = NULL;
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
			}
			$returnArray[$key] = '['.$termAspect.'] '.$termName;
		}
	}

	return $returnArray;
}

/**
 * Returns array(rgdID => title ) from objects selected in session
 */
function getReferenceArrayFromSession() {

	$referenceArrayInSession = getBucketItems('REFERENCE_OBJECT_BUCKET');
	$returnArray = array ();
	if ($referenceArrayInSession > 0) {
		foreach ($referenceArrayInSession as $key => $value) {

			$refTerm = NULL;
			$refName = NULL;
			foreach ($value as $subkey => $subvalue) {
				if ($subkey == "title") {
					$title = $subvalue;
				}
				if ($subkey == "rgdID") {
					$rgdID = $subvalue;
				}
			}
			$returnArray[$rgdID] = $title . " (RGD:" . $rgdID . ")";
		}
	}

	return $returnArray;
}

function getQTLsfromSession() {

	$qtlObjectsArrayInSession = getBucketItems('QTL_OBJECT_BUCKET');
	$objectArray = array ();

	//QTL's next to display
	if ($qtlObjectsArrayInSession > 0) {
		foreach ($qtlObjectsArrayInSession as $key => $value) {

			foreach ($value as $subkey => $subvalue) {
				$objectType = '';
				$species = '';
				if ($subkey == $key) {
					$qtlSymbol = $subvalue;
				}
				if ($subkey == "objectType") {
					$objectType = $subvalue;
				}
				if ($subkey == "species") {
					$species = $subvalue;
				}
			}
			$objectArray[$key] = $species . " QTL: <b>" . $qtlSymbol . '</b>';
		}
	}
	return $objectArray;
}
/** 
 * 
 */
function getObjectArrayFromSession() {

	$objectArray = array ();
	$geneObjectsArrayInSession = getBucketItems('GENE_OBJECT_BUCKET');
	$qtlObjectsArrayInSession = getBucketItems('QTL_OBJECT_BUCKET');
	$strainsObjectsArrayInSession = getBucketItems('STRAIN_OBJECT_BUCKET');
	$sslpObjectsArrayInSession = getBucketItems('SSLP_OBJECT_BUCKET');

	// $toReturn .= count($geneObjectsArrayInSession) + count ( $qtlObjectsArrayInSession )  . " found<br>";
	// Session contains array of assoiciative arrays with RGD_ID => GeneName and objectType => 'G'

	// Process Genes first
	if ($geneObjectsArrayInSession > 0) {
		foreach ($geneObjectsArrayInSession as $key => $value) {
			foreach ($value as $subkey => $subvalue) {
				$objectType = '';
				$species = '';
				if ($subkey == $key) {
					$geneName = $subvalue;
				}
				if ($subkey == "objectType") {
					$objectType = $subvalue;
				}
				if ($subkey == "species") {
					$species = $subvalue;
				}
			}
			$objectArray[$key] = $species . " Gene: <b>" . $geneName . '</b>';
		}

	}

	//QTL's next to display

	if ($qtlObjectsArrayInSession > 0) {
		foreach ($qtlObjectsArrayInSession as $key => $value) {

			foreach ($value as $subkey => $subvalue) {
				$objectType = '';
				$species = '';
				if ($subkey == $key) {
					$qtlSymbol = $subvalue;
				}
				if ($subkey == "objectType") {
					$objectType = $subvalue;
				}
				if ($subkey == "species") {
					$species = $subvalue;
				}
			}

			$objectArray[$key] = $species . " QTL : <b> " . $qtlSymbol . " </b>";
		}
	}

	// Strains
	if ($strainsObjectsArrayInSession > 0) {
		foreach ($strainsObjectsArrayInSession as $key => $value) {

			foreach ($value as $subkey => $subvalue) {
				$objectType = '';
				$species = '';
				if ($subkey == $key) {
					$strainSymbol = $subvalue;
				}
				if ($subkey == "objectType") {
					$objectType = $subvalue;
				}
				if ($subkey == "species") {
					$species = $subvalue;
				}
			}
			$objectArray[$key] = $species . " Strain: <b> " . $strainSymbol . '</b>';
		}
	}

	// SSLP
	if ($sslpObjectsArrayInSession > 0) {
		foreach ($sslpObjectsArrayInSession as $key => $value) {

			foreach ($value as $subkey => $subvalue) {
				$objectType = '';
				$species = '';
				if ($subkey == $key) {
					$sslpSymbol = $subvalue;
				}
				if ($subkey == "objectType") {
					$objectType = $subvalue;
				}
				if ($subkey == "species") {
					$species = $subvalue;
				}
			}
			$objectArray[$key] = $species . " SSLP: <b>" . $sslpSymbol . '</b>';
		}
	}

	return $objectArray;
}

function admin_login() {
	$theForm = getLoginForm();
	switch ($theForm->getState()) {
		case SUBMIT_VALID :
			$userName = $theForm->getValue('username');
			$password = $theForm->getValue('password');
			// $password = hashPassword($password);

			// check for locked acount
			$result = fetchRecord('select * from users where username = ' . dbQuoteString($userName) . '  and active_yn = \'N\'', 'LOGIN');
			if (count($result) != 0) {
				redirectWithMessage('Your Account has been locked. Please see the administrator to unlock your account.');
				break;
			}
			// Now check for correct password
			$result = fetchRecord('select * from users where username = ' . dbQuoteString($userName) . ' and password = \'' . $password . '\' and active_yn = \'Y\'', 'LOGIN');
			if (count($result) != 0) {
				extract($result);
				setSessionVar('uid', $userName);
				setCookieVar('userloggedin', '1');
				redirectWithMessage('You have successfully logged in.');
			} else {
				redirectWithMessage('Invalid login, please try again.');
			}
			break;
		default :
			return $theForm->quickRender();
	}
}

function admin_logout() {
	//$sql = 'update fmriuserlog set logout_time = sysdate where fmriuser_id = ' . getSessionVar('uid') . ' and browserSessionid = ' . dbQuoteString(session_id()) . ' and login_time = (select max(login_time) from fmriuserlog where fmriuser_id = ' . getSessionVar('uid') . ' and browserSessionid = ' . dbQuoteString(session_id()) . ')';
	//$rowsUpdated = executeUpdate($sql);
	delCookieVar('userloggedin');
	session_destroy();
	redirectWithMessage('You are now logged out');
}

function admin_userLog() {
	if (!userIsAdmin()) {
		return NOACCESS_MSG;
	}
	$toReturn = '';

	$limitNumber = 20;

	$toReturn .= center('Logout time is only recorded if the user clicks "logout".  If their session times out, the system will not know this, and cannot update the logout time.  This is why some logout times will be blank.<br/><br/>');

	$fmriuserId = getRequestVarNum('FMRIUSER_ID');
	$user = fetchRecord("select * from fmriuser where fmriuser_id = $fmriuserId");
	extract($user);

	$query = "select to_char(LOGIN_TIME, 'MM/DD/YYYY HH:MI:SS') as login_time, LOGIN_IP, to_char(LOGOUT_TIME, 'MM/DD/YYYY HH:MI:SS') as LOGOUT_TIME from fmriuserlog where fmriuser_id = $fmriuserId order by login_time desc ";
	if (getRequestVarNum('showAll') != 1) {
		$rs = selectLimit($query, $limitNumber);
		setPageTitle("First $limitNumber");
		$toReturn .= makeLink('Show All', 'admin', 'userLog', "FMRIUSER_ID=$fmriuserId&showAll=1");
	} else {
		$rs = executeQuery($query);
		setPageTitle("All");
		$toReturn .= makeLink("First $limitNumber", 'admin', 'userLog', "FMRIUSER_ID=$fmriuserId");
	}
	appendPageTitle(" User Log entries ");

	$table = newTable('LOGIN TIME', 'IP', 'LOGOUT TIME');
	$table->setAttributes('class="simple" width="100%"');
	while (!$rs->EOF) {
		extract($rs->fields);
		$table->addRow($LOGIN_TIME, $LOGIN_IP, $LOGOUT_TIME);
		$rs->moveNext();
	}
	$toReturn .= $table->toHtml();

	return $toReturn;
}

function curation_addPubmedIDToBucket() {
	
	$pubmedID = trim(getRequestVarString('PMID'));
	$path = makeUrl("/rgdweb/pubmed/importReferences.html?action=bucket&pmid_list=" . $pubmedID);
 	$new_path = str_replace("/rgdCuration/?module=", "", $path);
	
	redirect($new_path);
}
function curation_referencesNotFound() {
	redirectWithMessage("PMID not found in NCBI!",  makeUrl('curation', 'selectReferences'));
}
?>
