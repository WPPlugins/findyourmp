<?php

/**
 * add mps settings meta box both for posts and pages.
 * @return void
 */
 
function mps_settings_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'mps_post_box', 'Mps Settings', 'mps_meta_box', 'post', 'advanced', 'high' );
    add_meta_box( 'mps_page_box', 'Mps Settings', 'mps_meta_box', 'page', 'advanced', 'high' );
  }
}


/**
 * generates the mps settings html code.
 * @return void
 */
 

function mps_meta_box() {
	global $post;
	$pid = $post->ID;

	global $wpdb;
	$tbl_settings = $wpdb->prefix . "mps_settings";
	$tbl_post_settings = $wpdb->prefix . "mps_post_settings";

	$serialsettings = $wpdb->get_var("SELECT settings from $tbl_post_settings WHERE pid = '$pid'");

	// if no post settings are stored use default values, otherwise use settings from mps_post_settings table
	/*
	if ($serialsettings == null) { 
		$width = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'width'");
		$height = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'height'");
		$feed = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'feed'");
		$message = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'message'")));
		$confirmmessage = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'confirmmessage'")));
		$recipients = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'recipients'");
		$requirefrom = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'requirefrom'");
		$validatefrom = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'validatefrom'");
		$defaultfrom = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'from'");
		$domain = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'domain'");
		$subject = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'subject'")));
	} else {
		$settings = unserialize($serialsettings);
		$width = $settings['width'];
		$height = $settings['height'];
		$feed = $settings['feed'];
		$message = stripslashes(stripslashes($settings['message']));
		$confirmmessage = stripslashes(stripslashes($settings['confirmmessage']));
		$recipients = $settings['recipients'];
		$requirefrom = $settings['requirefrom'];
		$validatefrom = $settings['validatefrom'];
		$defaultfrom = $settings['from'];
		$domain = $settings['domain'];
		$subject = stripslashes(stripslashes($settings['subject']));
	}
	*/
	
?>
<div class="wrap zpwrap">
	<h2>Mps Configuration</h2>
	<p>This plugin uses <a href="http://www.zetaprints.com" target="_blank" title="About ZetaPrints">ZetaPrints</a> templates. Templates are processed by <a href="http://www.zetaprints.com/help/dynamic-image-generation-api/" target="_blank" title="Image generation API">ZetaPrints back end</a>. You only need to copy and paste data feeds for <a href="http://zetaprints.com" target="_blank" title="Find card templates">templates you like</a> in the text box below. You can <a href="http://www.zetaprints.com/help/" target="_blank">create and upload</a> your own e-card templates to use with this plugin.</p>
	<p><b>Insert shotcode [zp-e-cards] into a post where you want the plugin to appear.</b></p>

	<fieldset class="options">
	<table>
        <tr>
            <td class="tdwh">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Width</strong>
                            </td>
                            <td>
                        	<input value="<?php echo $width;?>" size="8" id="width" name="width"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Height</strong>
                            </td>
                            <td>
                        	<input value="<?php echo $height;?>" size="8" id="height" name="height"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="border: 1px solid rgb(238, 238, 238);">
                <table>
                    <tbody>
                        <tr>
                            <td style="padding: 5px; text-align: right;">
                                <strong>Feed</strong>
                                <br/>
                                <a> </a>
                            </td>
                            <td style="padding: 5px; text-align: right;">
                        	<input value="<?php echo $feed;?>" size="60" id="feed" name="feed"/>
                                <br/>
                                <a href="javascript:findTemplate();">Find Templates</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <div id="menuSearch">
                                    <div id="search">
                                        <div>
                                            <span>
                                                <input type="text" value="" class="text" name="Search" id="Search" title="Find by keywords or reference"/>
                                                <input type="button" onclick="searchTemplate('Settings')" value="Search" class="submits"/>
                                            </span>
                                        </div>
                                        <div style="float:right;margin:10px">Sort by:
                                            <span>
                                                <input type="radio" checked="checked" value="0" name="SortBy" id="SortBy0"/>
                                                <label for="OrderByDate">date</label>
                                            </span>
                                            <span>
                                                <input type="radio" value="1" name="SortBy" id="SortBy1"/>
                                                <label for="OrderByRank">popularity</label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td id="templatespane" colspan="2">
		<div id="loading1" style="display: none;">Loading templates ...
                    <div id="loading">
                    </div>
                </div>
                <div id="contentTemplates">
                </div>
            </td>
        </tr>

		<tr>
			<td><p><strong>Message</strong><p><p><small>Optional. A text message to appear before the image in the email.</small></p></td>
			<td><textarea class="f11" name="message" id="message" rows="6" cols="78"><?php echo $message;?></textarea><em></em></p></td>
		</tr>
		<tr>
			<td><p><strong>Confirmation Message</strong><p><p><small>Optional. A text message to appear before the image in the confirmation email.</small></p></td>
			<td><textarea class="f11" name="confirmmessage" id="confirmmessage" rows="6" cols="78"><?php echo $confirmmessage;?></textarea><em></em></p></td>
		</tr>
		<tr>
			<td><p><strong>Recipients</strong><p><small>Enter predefined list of recipients, one per line as NAME, NAME@EXAMPLE.COM.<br />Only NAME will be visible to users. Leave blank for senders to enter any email address.</small></p></td>
			<td><textarea class="f11" name="recipients" id="recipients" rows="6" cols="78"><?php echo $recipients;?></textarea><em></em></p></td>
		</tr>
		<tr>
			<td><p><strong>Require From address:</strong></td>
<?php
	if ($requirefrom == "on") {
			echo '<td><input onChange="requireChange(this.checked)" checked name="requirefrom" type="checkbox" id="requirefrom" /> <em>&nbsp;&nbsp;&nbsp;&nbsp;a sender must enter a valid `From` address</em></p></td>';
	} else {
			echo '<td><input onChange="requireChange(this.checked)" name="requirefrom" type="checkbox" id="requirefrom" /> <em>&nbsp;&nbsp;&nbsp;&nbsp;a sender must enter a valid `From` address</em></p></td>';
	}
?>
		</tr>
		<tr>
			<td><p><strong>Validate From address:</strong></td>
<?php
	$validatefromcomment = '&nbsp;&nbsp;&nbsp;&nbsp;require a confirmation for user From address?</em></p></td>';
	if ($validatefrom == "on") {
			echo '<td><input checked name="validatefrom" type="checkbox" id="validatefrom" /> <em>' . $validatefromcomment . '</em></p></td>';
	} else {
			echo '<td><input name="validatefrom" type="checkbox" id="validatefrom" /> <em>' . $validatefromcomment . '</em></p></td>';
	}
?>
		</tr>
		<tr>
			<td><p><strong>Default From address:</strong></td>
			<td><input class="f11" name="from" type="text" id="from" value="<?php echo $defaultfrom;?>" size="25" /> <small>used if no email is required for senders and for confirmation emails</small></p></td>
		</tr>
		<tr>
			<td><p><strong>Email subject:</strong></td>
			<td><input class="f11" name="subject" type="text" id="subject" value="<?php echo $subject;?>" size="78" /> <em></em></p></td>
		</tr>
		<tr>
			<td><p><strong>Domain for images:</strong></td>
			<td><input class="f11" name="domain" type="text" id="domain" value="<?php echo $domain;?>" size="50" /> <small>use http://zetaprints.com if in doubt</small></p></td>
		</tr>
	</table>
	</fieldset>
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
requireChange(<?php echo $requirefrom; ?>) ;
//-->
</script>

<script type="text/javascript">
function mps_click( pid, width , height , feed , embedcode, message , recipients , requirefrom , validatefrom , from , domain , subject ) { 
siteurl = "<?php echo get_bloginfo('wpurl'); ?>";
jQuery.post(siteurl+"/wp-admin/admin-ajax.php", {action:"mps_ajax", 'cookie': encodeURIComponent(document.cookie), 'pid':pid, 'width':width, 'height':height, 'feed':feed, 'embedcode':embedcode, 'message':message, 'recipients':recipients, 'requirefrom':requirefrom, 'validatefrom':validatefrom, 'from':from, 'domain':domain, 'subject':subject },
 function(str)
{
      alert(str);
});
}

</script>


<?php
}


