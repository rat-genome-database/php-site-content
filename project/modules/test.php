<?php

function test_dumpSession() {
  session_start();
  return "<h2>Your Diagnostics </h2><h3>Session ID:</h3> " . session_id().'<br/> <h3>Everything in your session:</h3> <br>'.dumpString($_SESSION) . 
      '<h3>Database User :</h3> ' . DBUSERNAME . '<h3>DataBase Host : </h3>' . DBHOSTNAME;
}

function test_overlib() {
  $toReturn = '';
  $toReturn .= makeLinkOverlib("'This is an ordinary popup.'", 'linkname').'<br/>'; 
  $toReturn .= makeLinkOverlib("'This is a sticky with a caption. And it is centered under the mouse!', STICKY, CAPTION, 'Sticky!', CENTER", 'nameoflink', 'someModule', 'someFunc', 'ID=5').'<br/>'; 
  return $toReturn;
}

?>