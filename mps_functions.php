<?php

/**
 * generates the flash embed code using parameters retrieved from database.
 * @param int $pid
 * @return string
 */

function mps_default_render($pid, $content) {
	$code = get_form_code();
	
    $content = str_replace( '[find-your-mp]', $code, $content);

    return $content;
}

function get_form_code(){
	$code = '<div>'
	.'<form action="" name="sendPostcode" id="sendEmailForm" method="post">'
	.'Enter postcode: <input type="text" name="postcode" size="10" id="postcode" >'
	.'<input type="submit" id="sendemail" name="sendemail" value="Find" />'
	.'</div>';
	
	return $code;
}
 
function mps_get_details($postcode){

	global $wpdb;
	$tbl_settings = $wpdb->prefix . "options";

	$apikey = $wpdb->get_var("SELECT option_value from $tbl_settings WHERE option_name = 'theyworkforyou_apikey'");
	$url='http://www.theyworkforyou.com/api/getMP?key='.$apikey.'&output=xml&postcode='. str_replace(" ", "%20",  $postcode);
	$content = get_mp_code($url).	$code = get_form_code();

	return $content;
}


function get_mp_code($file){

	$xml=simplexml_load_file($file);

	if(isset($xml->error)){
		$code = $xml->error.', please try again.';
	}else{
		$domain = "http://www.theyworkforyou.com";
		$code =  '<img src='.'"'.$domain.$xml->image.'"  style="float:left;padding:5px;" title="'.$xml->full_name.'" alt="'.$xml->full_name.'"/>' . "<br/>"
			.'Name: <b>'.$xml->full_name . "</b><br/>"
			.'Party: ' .$xml->party . "<br/>"
			.'Constituency:  '. $xml->constituency . "<br/>"
			.'Entered Parliament:  '. $xml->entered_house . "<br/>"
			.'<a href="'.$domain.$xml->url. '"title="'.$xml->full_name.'" >More details</a> provided by <a href="'.$domain.'" title="TheyWorkForYou.com" >TheyWorkForYou.com</a><br/><br/>' ;
	}
	
	return $code;
}

?>