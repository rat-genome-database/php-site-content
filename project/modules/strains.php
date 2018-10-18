<?php

$strains = fetchRecords("select * from strains");
$totalCount=count($strains);
$maxCount=0;

echo '<table border=1>';

for ($i=0; $i < $totalCount; $i++) {

  echo ' <tr>';
  echo '   <td>'.$strains[$i]["STRAIN_SYMBOL"].'</td>';
  echo '</tr>';


//    $submitId = $strains[$i]["SUBMIT_ID"];
//    $strainRgdId = $strains[$i]["STRAIN_RGD_ID"];
//    $strainSymbol = $strains[$i]["STRAIN_SYMBOL"];
//    $fullName = $strains[$i]["FULL_NAME"];

//    $strain = $strains[$i]["STRAINL"];
//    $substrain = $strains[$i]["SUBSTRAIN"];
//    $strainTypeName = $strains[$i]["STRAIN_TYPE_NAME"];
//    $genetics = $strains[$i]["GENETICS"];
//    $inbreadGen = $strains[$i]["INBREAD_GEN"];
//    $origin = $strains[$i]["ORIGIN"];

//    $color = $strains[$i]["COLOR"];
//    $characteristics = $strains[$i]["CHARACTERISTICS"];
//    $reproduction = $strains[$i]["REPRODUCTION"];
//    $behavior = $strains[$i]["BEHAVIOR"];
//    $liveDisease = $strains[$i]["LIVE_DISEASE"];
//    $anatomy = $strains[$i]["ANATOMY"];

//    $infection = $strains[$i]["INFECTION"];
//    $immunology = $strains[$i]["IMMUNOLOGY"];
//    $physBiochem = $strains[$i]["PHYS_BIOCHEM"];
//    $drgsChems = $strains[$i]["DRGS_CHEMS"];
//    $source = $strains[$i]["SOURCE"];
//    $flankMarker1 = $strains[$i]["FLANK_MARKER_1"];
//    $flankMarker2 = $strains[$i]["FLANK_MARKER_2"];
//    $notes = $strains[$i]["NOTES"];
//   $notesType = $strains[$i]["NOTES_TYPE"];
//    $geneRgdId = $strains[$i]["GENE_RGD_ID"];
//    $geneSymbol = $strains[$i]["GENE_SYMBOL"];
//    $sslpRgdId = $strains[$i]["SSLP_RGD_ID"];

//    $sslpSymbol = $strains[$i]["SSLP_SYMBOL"];
//    $qtlRgdId = $strains[$i]["QTL_RGD_ID"];
//    $qtlSymbol = $strains[$i]["QTL_SYMBOL"];
//    $aliasValue = $strains[$i]["ALIAS_VALUE"];
//    $aliasTypes = $strains[$i]["ALIAS_TYPES"];
//    $noteRefId = $strains[$i]["NOTE_REF_ID"];
//    $refRgdId = $strains[$i]["REF_RGD_ID"];
//    $datasetRefRgdId = $strains[$i]["DATASET_REF_RGD_ID"];
//    $strainNotes = $strains[$i]["STRAIN_NOTES"];
//    $rn = $strains[$i]["DATASET_REF_RGD_ID"];
//    $datasetRefRgdId = $strains[$i]["DATASET_REF_RGD_ID"];
//    $datasetRefRgdId = $strains[$i]["DATASET_REF_RGD_ID"];
//    $datasetRefRgdId = $strains[$i]["DATASET_REF_RGD_ID"];
//    $datasetRefRgdId = $strains[$i]["DATASET_REF_RGD_ID"];


 //   echo "strain = " . $strain . "</br>";
}

echo '</table>';

echo "<pre>";
var_dump($_REQUEST);
echo "</pre>";

function dataSubmission_render() {
  setTemplate("dev");
}
?>

<script>
//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your Browser Sucks!\nIt's about time to upgrade don't you think?");
	}
}

//Our XmlHttpRequest object to get the auto suggest
var searchReq = getXmlHttpRequestObject();

//Called from keyup on the search textbox.
//Starts the AJAX request.
function searchSuggest() {
	if (searchReq.readyState == 4 || searchReq.readyState == 0) {
		var str = escape(document.getElementById('txtSearch').value);
		searchReq.open("GET", 'junk.php?' + str, true);
		searchReq.onreadystatechange = handleSearchSuggest;
		searchReq.send(null);
	}
}

//Called when the AJAX response is returned.
function handleSearchSuggest() {
	if (searchReq.readyState == 4) {
		var ss = document.getElementById('search_suggest')
		ss.innerHTML = '';
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) {
			//Build our element string.  This is cleaner using the DOM, but
			//IE doesn't support dynamically added attributes.
			var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			suggest += 'onmouseout="javascript:suggestOut(this);" ';
			suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			suggest += 'class="suggest_link">' + str[i] + '</div>';
			ss.innerHTML += suggest;
		}
	}
}

//Mouse over function
function suggestOver(div_value) {
	div_value.className = 'suggest_link_over';
}
//Mouse out function
function suggestOut(div_value) {
	div_value.className = 'suggest_link';
}

//Click function
function setSearch(value) {
	document.getElementById('txtSearch').value = value;
	document.getElementById('search_suggest').innerHTML = '';
}


</script>


<style type="text/css" media="screen">
	body {
		font: 11px arial;
	}
	.suggest_link {
		background-color: #FFFFFF;
		padding: 2px 6px 2px 6px;
	}
	.suggest_link_over {
		background-color: #3366CC;
		padding: 2px 6px 2px 6px;
	}
	#search_suggest {
		position: absolute;
		background-color: #FFFFFF;
		text-align: left;
		border: 1px solid #000000;
	}
</style>

<form id="frmSearch" action="http://www.DynamicAJAX.com/search.php">
	<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria"
		onkeyup="searchSuggest();" autocomplete="off" />
	<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" />

  <div id="search_suggest">
	</div>
</form>



<script>

function addTableNode(tableId, nodeId, nextNodeId) {
  table = document.getElementById("strainTable").childNodes[1];
  node = document.getElementById(nodeId);
  nextNode = document.getElementById(nextNodeId);
  clone = node.cloneNode(true);
  clone.id = node.id + new Date();
  table.insertBefore(clone, nextNode);
}

</script>


<form name="strainForm">
<input type="hidden" name="module" value="datasubmission" />
<input type="hidden" name="func" value="render" />

<table border="1" id="strainTable">
<tr>
  <td width="100">Symbol</td>
  <td><input type="text" name="symbol" /></td>
  <td></td>
</tr>
<tr>
  <td>Full Name</td>
  <td><input type="text" name="fullName" /></td>
  <td></td>
</tr>
<tr id="aliasSymbol">
  <td>Alias Symbol</td>
  <td><input type="text" name="strainSymbol" /></td>
  <td><a href="javascript:void(0)" onClick="addTableNode('strainsTable', 'aliasSymbol', 'aliasName')" style="font-size: 10px;">Add Symbol</a></td>
</tr>
<tr id="aliasName">
  <td>Alias Name</td>
  <td><input type="text" name="aliasName" /></td>
  <td><a href="javascript:void(0)" onClick="addTableNode('strainsTable', 'aliasName', 'refId')" style="font-size: 10px;">Add Alias</a></td>
</tr>
<tr id="refId">
  <td>Reference RGD ID</td>
  <td><input type="text" name="reference" /></td>
  <td><a href="javascript:void(0)" onClick="addTableNode('strainsTable', 'refId', 'strain')" style="font-size: 10px;">Add Reference</a></td>
</tr>
<tr id="strain">
  <td>Strain</td>
  <td><input type="text" name="strain" /></td>
  <td></td>
</tr>
<tr>
  <td>Substrain</td>
  <td><input type="text" name="subStrain" /></td>
  <td></td>
</tr>
<tr>
  <td>Strain Source</td>
  <td><input type="text" name="strainSource" /></td>
  <td></td>
</tr>
<tr>
  <td>Strain Source URL</td>
  <td><input type="text" name="strainSourceUrl" /></td>
  <td></td>
</tr>
<tr>
  <td>Strain Type</td>
  <td>
    <SELECT name="strainType" SIZE='1'>

		<OPTION SELECTED>Select Type
		<OPTION value='coisogenic'>Coisogenic
		<OPTION value='conplastic'>Conplastic
		<OPTION value='congenic'>Congenic
		<OPTION value='consomic'>Consomic
		<OPTION value='inbred'>Inbred
		<OPTION value='mutant'>Mutant
		<OPTION value='outbred'>Outbred
		<OPTION value='related_inbred'>Related inbred
		<OPTION value='recombinant_inbred'>Recombinant inbred
		<OPTION value='segregating_inbred'>Segregating inbred
		<OPTION value='transgenic'>Transgenic
	</SELECT>
</td>
  <td></td>
</tr>
<tr>
  <td>Genetics</td>
  <td><SELECT name='genetics' SIZE='1'>
		<OPTION SELECTED>Select Genetics
		<OPTION>Hooded
		<OPTION>Irish-hooding
		<OPTION>Non-agouti
		<OPTION>Non-hooded
		<OPTION>Non-pink-eyed
		<OPTION>Pink-eyed-dilute
	</SELECT></td>
  <td></td>
</tr>
<tr>
  <td>Strain Color</td>
  <td><SELECT name='strainColor' SIZE='1'>
		<OPTION SELECTED>Select Color
		<OPTION>Agouti
		<OPTION>Albino
		<OPTION>Brown
		<OPTION>Dilute
		<OPTION>Fawn
		<OPTION>Hooded
		<OPTION>Microphthalmie-blanc
		<OPTION>Pink-eyed-yellow
		<OPTION>Red-eyed-yellow
		<OPTION>Sand
		<OPTION>Silver
		<OPTION>Spotted-lethal
		<OPTION>White
		<OPTION>White-belly
		<OPTION>Yellow
	</SELECT></td>
  <td></td>
</tr>
<tr>
  <td>Inbread Generations</td>
  <td><input type="text" name="inbreadGenerations" /></td>
  <td></td>
</tr>
<tr>
  <td>Chromosome Altered</td>
  <td><SELECT name='chromosome' SIZE='1'>
		<OPTION SELECTED>Select Chromosome
		<OPTION>1
		<OPTION>2
		<OPTION>3
		<OPTION>4
		<OPTION>5
		<OPTION>6
		<OPTION>7
		<OPTION>8
		<OPTION>9
		<OPTION>10
		<OPTION>11
		<OPTION>12
		<OPTION>13
		<OPTION>14
		<OPTION>15
		<OPTION>16
		<OPTION>17
		<OPTION>18
		<OPTION>19
		<OPTION>20
		<OPTION>X
		<OPTION>Y
	</SELECT>

</td>
  <td></td>
</tr>
<tr>
  <td>Flank Marker 1 Symbol</td>
  <td><input type="text" name="flankMarker1Symbol" /></td>
  <td></td>
</tr>
<tr>
  <td>Flank Marker 2 Symbol</td>
  <td><input type="text" name="flankMarker2Symbol" /></td>
  <td></td>
</tr>
<tr>
  <td>Strain Origin</td>
  <td colspan="2"><TEXTAREA name="strainOrigin" ROWS=4 COLS=57></TEXTAREA></td>
</tr>
<tr>
  <td>Gene Symbol</td>
  <td><input type="text" name="geneSymbol" /></td>
  <td></td>
</tr>
<tr>
  <td>SSLP Symbol</td>
  <td><input type="text" name="sslpSymbol" /></td>
  <td></td>
</tr>
<tr>
  <td>QTL Symbol</td>
  <td><input type="text" name="qtlSymbol" /></td>
  <td></td>
</tr>
<tr>
  <td>L</td>
  <td><input type="text" name="strainSourceUrl" /></td>
  <td></td>
</tr>
<tr>
  <td>Note References</td>
  <td><input type="text" name="strainSourceUrl" /></td>
  <td></td>
</tr>
<tr>
    <td>Notes Ref ID</td>
    <td colspan="2"><INPUT type='text' name='noteRefID1' size=30, maxlength=200>&nbsp;
        <SELECT name='noteType1' SIZE='1'>
				<OPTION SELECTED>Select Note Type
				<OPTION value=strain_anatomy>ANATOMY
				<OPTION value=strain_behavior>BEHAVIOR
				<OPTION value=strain_characteristics>CHARACTERISTICS
				<OPTION value=strain_curation_comments>CURATION_COMMENTS
				<OPTION value=strain_drgs_chems>DRGS_CHEMS
				<OPTION value=strain_infection>INFECTION
				<OPTION value=strain_immunology>IMMUNOLOGY
				<OPTION value=strain_life_disease>LIFE_DISEASE
				<OPTION value=strain_other>OTHER
				<OPTION value=strain_phys_biochem>PHYS_BIOCHEM
				<OPTION value=strain_reproduction>REPRODUCTION
		</SELECT>
	</td>
</tr>
<tr>
	    <td>Notes</td>
	    <td colspan="2"><TEXTAREA name='noteValue1' ROWS=4 COLS=55></TEXTAREA></td>
</tr>
<tr>
	    <td></td>
	    <td></td>
	    <td></td>
</tr>
<tr>
	    <td colspan='3'><input type="submit" value="Add" name="submit" /></td>
</tr>

</table>

</form>






