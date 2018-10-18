<?php
/**
 * serve up static content from the staticHtml directory
 */

function content_fetch() {
  $page = $_REQUEST['page'];
  return file_get_contents('staticHtml/'.$page.'.html');
}
?>