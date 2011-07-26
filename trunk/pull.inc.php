<?php

//--------------------------------------------------------------
// -------------------------------------------------------------

function pull($type, $name) {
  
  // LEAGUE
  if($type == 'league') {
    $infos['url'] = 'http://www.footbo.com/Competitions/' . $name;
    // check cache before do a request
    if ( false === ( $html = get_transient( $type . '_' . $name ) ) ) { 
      $html = my_curl($infos['url']);
      if(!empty($html) && $html != null && $html != false)
        // set cache   
        set_transient($type . '_' . $name, $html, 60 * (int) get_option('footbo-field-cache'));  
    }
    return fetch_league($html);
  }
  
  
  // ***************************************************************************
    
}
// -------------------------------------------------------------
// -------------------------------------------------------------


// fixture table - REGEX
function fetch_fixture($html) {
  // just what we need - table with fixtures
  if( preg_match_all('/<div class="FootboControl">.*?(<table.*?class="TeamGames">(.)*?<\/table>)/is', $html, $match) ) {
    // strip attributes from <tr>
    $raw = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $match[1][0]);
    return $raw;  
  }
  else {
    return false;
  }
}
// ***************************************************************************

// all fixture - REGEX
function fetch_fixture_all($html) {
  if( preg_match_all('/<h3\sclass="Header">.*?<\/h3>\s?<table\sclass="GamesView".*?>[.\s]*?<\/table>/is', $html, $match) ) {
    // remove last td
    // remove attributes
    foreach($match[0] as $m) {
      $last_td = preg_replace("/<td\s?class=\"GamesViewLeague\".*?<\/td>/is", '', $m);
      $striped[] = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $last_td);
    }
    return $striped;
  }
  else
    return false;
}
// ***************************************************************************

// league table - REGEX
function fetch_league($html, $ajax = false) {
  if( preg_match_all('/<td\sclass="StandingsBox">\s?(<table.*>[.\s]*?<\/table>)\s?<\/td>/is', $html, $match) ) {
    $match[1][0] = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $match[1][0]);
    return $match[1][0]; 
  }
  else{
    return false;
  }
}
// -------------------------------------------------------------
// -------------------------------------------------------------

// curl
function my_curl($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}
// -------------------------------------------------------------
// -------------------------------------------------------------