<?php

/**
 * admin menu page for plugin configuration.
 * uses ecards_settings table for storage
 */

global $wpdb;
$tbl_settings = $wpdb->prefix . "options";

/**
 * update ecards_settings table upon Save submit.
 */
if (isset($_POST['save_settings'])) {

    $apikey = $_POST['apikey'];	
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET option_value='" .  $apikey .  "' WHERE option_name = 'theyworkforyou_apikey'" );
    
	?>
<div class="updated"><p>MPS settings saved.</p></div> 
<?php
}

/**
* load stored values from table and fills the form with these values.
*/
 

$apikey = $wpdb->get_var("SELECT option_value from $tbl_settings WHERE option_name = 'theyworkforyou_apikey'");

if($apikey == null){
	$apikey = "Enter API key here";
	$sql = "insert into " . $tbl_settings . " (blog_id, option_name, option_value, autoload) values (0, 'theyworkforyou_apikey', '" .  $apikey .  "', 'no' )" ;
	//$apikey = $sql;
    $results = $wpdb->query( $sql);
}

?>

<div class="wrap zpwrap">
<h2>Find your MP Configuration</h2>

  <p>This plugin enables the retreival of information on United Kingdom Members of Parliament, based upon postcode. </p>
<p><b>Insert shortcode [find-your-mp] into a post where you want the plugin to appear.</b></p>

<p>Also ensure that you have a <b>php.ini</b> file in your root directory containing the line <b>allow_url_fopen = true</b>.</p>


  <div id="defaultSettings"> 
    <h2>Setting</h2>
<form name="manage" method="post">
<fieldset class="options">
<table>
<tr>
<td colspan="2">Get an <a href="http://www.theyworkforyou.com/api/" >API key</a> from  <a href="http://www.theyworkforyou.com/" >TheyWorkForYou</a></td>
</tr>
<tr>
<td colspan="2" height="10">&nbsp; </td>
</tr>
<tr>
<td><strong>API Key:</strong></td>
<td><input name="apikey" type="text" id="apikey" value="<?php echo $apikey;?>"  size="78" /> </td>
</tr>
</table>
</fieldset>
<p><div class="submit">
    <input type="submit" name="save_settings" value="<?php _e('Save Settings', 'save_cache_settings')?>" style="font-weight:bold;" /></div>
</p>
</form>
</div>
</div>
<script type="text/javascript">
    <!--
    /**
     * disable 'validatefrom' checkbox if 'requirefrom' is unchecked.
     * @param bool x
     * @return void
     */
    function requireChange(x) {
        //alert(x);
        if (x) {
            document.getElementById('validatefrom').disabled = false;
        } else {
            document.getElementById('validatefrom').checked = false;
            document.getElementById('validatefrom').disabled = true;
        }
    }
    requireChange('<?php echo $requirefrom; ?>') ;

    //-->
</script>

