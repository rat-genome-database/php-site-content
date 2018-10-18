<?php
// dont you dare put any white space outside the php tags!... messes with the redirect!!

function mainPage_contents() {
  return file_get_contents('staticHtml/home.html', true);
}

function mainPage_404Message() {
  return 'We\'re sorry, we cannot locate that document, we have logged this and will fix it soon.';
}

function mainPage_sessionExpiredMessage() {
  return 'Your session has expired, please log in again';
}

// dont you dare put any white space outside the php tags!... messes with the redirect!!
?>
