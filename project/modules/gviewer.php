<?php

//
// Function to take a parameter and return XML for gviewer
// $Header: /var/lib/cvsroot/Development/RGD/rgdCuration/project/modules/gviewer.php,v 1.5 2007/09/11 20:09:14 gkowalski Exp $
//
// Output XML directly to the browser given the parameters :
// 
// term_acc = the Ontology term to search for
// Generates anootation data per the spec found here : 
// http://blog.gmod.org/nondrupal/FlashGViewer_forWeb/index.html
// 
// TODO : Take in one termID in addition to a ontology Term
// TODO : Handle Multiple terms and ontologies , puting the results togeather.
// TODO : handle wildcards properly on terms, remove use of "%" character and make
//        sure we filter input before using it in SQL. 

function gviewer_getxml() { 
	// Marek Tutaj, Jan 2012
	echo "This service is obsolete. Please use /rgdweb/ontology/gviewerData.html instead.";
}

?>