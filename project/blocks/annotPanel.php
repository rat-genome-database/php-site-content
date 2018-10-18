<?php
// dont you dare put any white space outside the php tags!... messes with the redirect!!
function annotPanel_contents() {
	$pmb = new PhpMyBorder(); // or new PhpMyBorder(true), read about stylesheet-support below

	$toReturn = '';

	if (userLoggedIn() && ( userIsCurator() || userIsAdmin()) && getModuleName() == "ont") {
    $toReturn .= "<br>";
    $toReturn .= $pmb->begin_round();
    $toReturn .= makeLink('Obsolete Terms', 'ont', 'obsoleteTerms') . '<br/>';
    $buckets = array();
    $buckets = getBucketItems('annotBuckets');
    foreach ($buckets as $bucket) {
      $annots = getBucketItems(ONT_BUCKET_PREFIX.$bucket);
      $toReturn .= '<br/>'.makeLink("Bucket $bucket", 'ont', 'bucket', "name=$bucket") . '('.count($annots).')<br/>';
      $toReturn .= makeLink('empty', 'ont', 'emptyBucket', "name=$bucket").' '.makeLink('del', 'ont', 'deleteBucket', "name=$bucket").'<br/>';
      if (count($annots) > 0) {
        foreach ($annots as $annot) {
          extract($annot);
          //$toReturn .= makeLinkOverlib("'<b>Term (term acc):</b><div class=padleft>  $TERM ($TERM_ACC)</div><br><b>Object (symbol):</b><div class=padleft>$OBJECT_NAME ($OBJECT_SYMBOL) </div><br><b>Reference (type):</b><div class=padleft>$TITLE ($REFERENCE_TYPE)</div>', CENTER", "obj: $OBJECT_SYMBOL").'<br/>';
          $toReturn .= makeLinkOverlib(overLibArgs("<b>Term (term acc):</b><div class=padleft>  $TERM ($TERM_ACC)</div><br><b>Object (symbol):</b><div class=padleft>$OBJECT_NAME ($OBJECT_SYMBOL) </div><br><b>Reference (type):</b><div class=padleft>$TITLE ($REFERENCE_TYPE)</div>","CENTER"), "obj: $OBJECT_SYMBOL").'<br/>';
        }
      }
    }
      
    
    $toReturn .= $pmb->end_round();

	}

	return $toReturn;
}
// dont you dare put any white space outside the php tags!... messes with the redirect!!
?>
