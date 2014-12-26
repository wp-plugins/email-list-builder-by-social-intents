<?php
/*
Plugin Name: Email List Builder by Social Intents
Plugin URI: http://www.socialintents.com
Description: Add a customizable and targeted email subscription widget to any page.  Integrates with MailChimp and Constant Contact as well as CSV Exports (more coming soon).  Additional widgets such as Feedback, and Social Offers are also available! Free for 30 new email list subscribers a month.
Version: 1.0.16
Author: Social Intents
Author URI: http://www.socialintents.com/
*/

$elb_domain = plugins_url();
add_action('init', 'elb_init');
add_action('admin_notices', 'elb_notice');
add_filter('plugin_action_links', 'elb_plugin_actions', 10, 2);
add_action('wp_footer', 'elb_insert',4);

define('SI_SMALL_LOGO',plugin_dir_url( __FILE__ ).'si-small.png');
define('SI_DASHBOARD_URL', "https://www.socialintents.com/dashboard.do");


function elb_init() {
    if(function_exists('current_user_can') && current_user_can('manage_options')) {
        add_action('admin_menu', 'elb_add_settings_page');
    }
}

function si_dashboard() {

      echo '<div id="dashboarddiv"><iframe id="dashboardiframe" src="'.SI_DASHBOARD_URL.'" height=500 width=98% scrolling="yes"></iframe></div>      
	<a href="'.SI_DASHBOARD_URL.'" target="_newWindow" onClick="javascript:document.getElementById(\'dashboarddiv\').innerHTML=\'\'; ">Open Email List Builder by Social Intents in a new window</a>.
      ';
}

function elb_insert() {

    global $current_user;
    if(strlen(get_option('elb_widgetID')) == 32 && get_option('elb_tab_text')) {
        get_currentuserinfo();
	echo("\n\n<!-- Social Intents Customization -->\n");
        echo("<script type=\"text/javascript\">\n");
        echo("var socialintents_vars_email ={\n");
        echo("'widgetId':'".get_option('elb_widgetID')."',\n");
        echo("'tabLocation':'".get_option('elb_tab_placement')."',\n");
        echo("'tabText':'".get_option('elb_tab_text')."',\n");
echo("'popupHeight':'".get_option('elb_popup_height')."',\n");
echo("'popupWidth':'".get_option('elb_popup_width')."',\n");
echo("'roundedCorners':'".get_option('elb_rounded_corners')."',\n");
echo("'backgroundImg':'".get_option('elb_background_img')."',\n");
        echo("'type':'email',\n");
        echo("'tabColor':'".get_option('elb_tab_color')."',\n");
        echo("'tabWidth':'220px',\n");
        echo("'marginRight':'60px', \n");
	echo("'marginTop':'180px', \n");
        echo("'headerTitle':'".get_option('elb_header_text')."'\n");
        echo("};\n");
        echo("(function() {function socialintents(){\n");
        echo("    var siJsHost = ((\"https:\" === document.location.protocol) ? \"https://\" : \"http://\");\n");
        echo("    var s = document.createElement('script');s.type = 'text/javascript';s.async = true;s.src = siJsHost+'www.socialintents.com/api/email/socialintents.js';\n");
        echo("    var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);};\n");
        echo("if (window.attachEvent)window.attachEvent('onload', socialintents);else window.addEventListener('load', socialintents, false);})();\n");
        echo("</script>\n");
    }
}

function elb_notice() {
    if(!get_option('elb_widgetID')) echo('<div class="error"><p><strong>'.sprintf(__('Your Email List Builder Plugin is disabled. Please go to the <a href="%s">plugin settings</a> to enter a valid widget key.  Find your widget key by logging in at www.socialintents.com and selecting your Widget General Settings.  New to socialintents.com?  <a href="http://www.socialintents.com">Sign up for a Free Trial!</a>' ), admin_url('options-general.php?page=email-list-builder-by-socialintents')).'</strong></p></div>');
}

function elb_plugin_actions($links, $file) {
    static $this_plugin;
    if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
    if($file == $this_plugin && function_exists('admin_url')) {
        $settings_link = '<a href="'.admin_url('options-general.php?page=email-list-builder-by-socialintents').'">'.__('Settings', $elb_domain).'</a>';
        array_unshift($links, $settings_link);
    }
    return($links);
}

function elb_add_settings_page() {
    function elb_settings_page() {
        global $elb_domain ?>
<div class="wrap">
        <?php screen_icon() ?>
    <h2><?php _e('Email List Builder by Social Intents', $elb_domain) ?></h2>
    <div class="metabox-holder meta-box-sortables ui-sortable pointer">
        <div class="postbox" style="float:left;width:30em;margin-right:10px">
            <h3 class="hndle"><span><?php _e('Email List Builder Settings', $elb_domain) ?></span></h3> 
            <div class="inside" style="padding: 0 10px">
                <form id="saveSettings" method="post" action="options.php">
                    <p style="text-align:center"><?php wp_nonce_field('update-options') ?>
			<a href="http://www.socialintents.com/" title="Email and Social Widgets that help grow your business">
			<?php echo '<img src="'.plugins_url( 'socialintents.png' , __FILE__ ).'" height="150" "/> ';?></a></p>

                    <p><label for="elb_widgetID"><?php printf(__('Enter your Widget Key below to activate the plugin.  If you don\'t have your key but have already signed up, you can <a href=\'http://www.socialintents.com\' target=\'_blank\'>login here</a> to grab your key under your widget --> your code snippet..<br>', $elb_domain), '<strong><a href="http://www.socialintents.com/" title="', '">', '</a></strong>') ?></label><br />
			<input type="text" name="elb_widgetID" id="elb_widgetID" placeholder="Your Widget Key" value="<?php echo(get_option('elb_widgetID')) ?>" style="width:100%" />
                    <p class="submit" style="padding:0"><input type="hidden" name="action" value="update" />
                        <input type="hidden" name="page_options" value="elb_widgetID" />
                        <input type="submit" name="elb_submit" id="elb_submit" value="<?php _e('Save Settings', $elb_domain) ?>" class="button-primary" /> 
			</p>
                 </form>
            </div>
        </div>
        <div class="postbox" style="float:left;width:38em">
            <h3 class="hndle"><span id="elb_noAccountSpan"><?php _e('No Account?  Sign up for a Free Social Intents Trial!', $elb_domain) ?></span></h3>
            <div id="elb_register" class="inside" style="padding: -30px 10px">			
		<p><?php printf(__('Social Intents is an email and social widgets platform that helps you grow your business with simple, effective plugins
			with targeted rules and dynamic reports.
			Please visit %1$sSocial Intents%2$ssocialintents.com%3$s to 
				learn more.', $elb_domain), '<a href="
http://www.socialintents.com/" title="', '">', '</a>') ?></p>
			<b>Sign Up For a Free Trial Now!</b> (or register directly on our site at <a href="http://www.socialintents.com" target="_blank">Social Intents</a>)<br>
			<input type="text" name="elb_email" id="elb_email" value="<?php echo(get_option('admin_email')) ?>" placeholder="Your Email" style="width:50%;margin:3px;" />
			<input type="text" name="elb_name" id="elb_name" value="<?php echo(get_option('user_nicename')) ?>" placeholder="Your Name" style="width:50%;margin:3px;" />
			<input type="password" name="elb_password" id="elb_password" value="" placeholder="Your Password" style="width:50%;margin:3px;" />
			<br><input type="button" name="elb_inputRegister" id="elb_inputRegister" value="Register" class="button-primary" style="margin:3px;" /> 
			
			
               
            </div>
	    <div id="elb_registerComplete" class="inside" style="padding: -20px 10px;display:none;">
		<p>View reports, customize CSS styles, and export email subscribers on our website at <a href='http://www.socialintents.com'>www.socialintents.com</a>
		</p><form id='saveDetailSettings' method="post" action="options.php">
		<?php wp_nonce_field('update-options') ?>
		<input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="elb_popup_height, elb_popup_width, elb_background_img,elb_rounded_corners,elb_tab_text,elb_tab_placement,elb_header_text,elb_detail_text,elb_time_on_page,elb_tab_color" />
		<table width="100%" >
		<tr><td width="25%">Tab Text: </td>
		<td >
		<?php
		if(get_option('elb_tab_text') ) {
     		?>
     			<input type="text" name="elb_tab_text" id="elb_tab_text" value="<?php echo(get_option('elb_tab_text')) ?>" style="margin:3px;width:100%;" />
		
    		<?php 
			} else {
   		?>
			<input type="text" name="elb_tab_text" id="elb_tab_text" value="Subscribe Now!" style="margin:3px;width:100%;" />
		<?php 
			}
   		?>
		</td>
		</tr>
		<tr><td width="25%">Tab Color: </td>
		<td >
		<?php
		if(get_option('elb_tab_color') && get_option('elb_tab_color') != '') {
     		?>
     			<input type="text" name="elb_tab_color" id="elb_tab_color" value="<?php echo(get_option('elb_tab_color')) ?>" style="margin:3px;width:100%;" />
		
    		<?php 
			} else {
   		?>
			<input type="text" name="elb_tab_color" id="elb_tab_color" value="#00AEEF" style="margin:3px;width:100%;" />
		<?php 
			}
   		?>
		</td>
		</tr>
		<tr><td>Tab Placement: </td><td>
		<?php 
		if(get_option('elb_tab_placement') && get_option('elb_tab_placement') == 'bottom') {
     		?>
     		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom" selected>Bottom</option>
			<option value="top">Top</option>
			<option value="right">Right</option>
			<option value="left">Left</option>
			<option value="hide">Hide</option>
		</select> 	
    		<?php 
			} else if(get_option('elb_tab_placement') == 'top') {
   		?>
		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom">Bottom</option>
			<option value="top" selected>Top</option>
			<option value="right">Right</option>
			<option value="left">Left</option>
			<option value="hide">Hide</option>
		</select> 
		<?php 
			} else if(get_option('elb_tab_placement') == 'right') {
   		?>
		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom">Bottom</option>
			<option value="top">Top</option>
			<option value="right" selected>Right</option>
			<option value="left">Left</option>
			<option value="hide">Hide</option>
		</select> 
		<?php 
			} else if(get_option('elb_tab_placement') == 'left') {
   		?>
		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom">Bottom</option>
			<option value="top" >Top</option>
			<option value="right" selected>Right</option>
			<option value="left">Left</option>
			<option value="hide">Hide</option>
		</select> 
		<?php 
			} else if(get_option('elb_tab_placement') == 'hide') {
   		?>
		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom">Bottom</option>
			<option value="top">Top</option>
			<option value="right">Right</option>
			<option value="left">Left</option>
			<option value="hide"  selected>Hide</option>
		</select> 
		<?php 
			} else {
   		?>
		<select id="elb_tab_placement" name="elb_tab_placement">
			<option value="bottom">Bottom</option>
			<option value="top">Top</option>
			<option value="hide">Hide</option>
		</select> 
		<?php 
			}
   		?>
		
		</td></tr>
		<tr><td>When To Show Popup: </td><td>
		<?php 
		if(get_option('elb_time_on_page') && get_option('elb_time_on_page') == '0') {
     		?>
     		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0" selected>Immediately</option>
			<option value="-1" >Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 	
    		<?php 
			} else if(get_option('elb_time_on_page') == '-1') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1" selected>Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 
		<?php 
			} else if(get_option('elb_time_on_page') == '10') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10"  selected>10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select>  
		<?php 
			} else if(get_option('elb_time_on_page') == '15') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15"  selected>15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 
		<?php 
			} else if(get_option('elb_time_on_page') == '20') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20"   selected>20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 
		<?php 
			} else if(get_option('elb_time_on_page') == '30') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20" >20 Seconds</option>
			<option value="30"  selected>30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 
		<?php 
			} else if(get_option('elb_time_on_page') == '45') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20" >20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45"  selected>45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select> 
		<?php 
			} else if(get_option('elb_time_on_page') == '60') {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60"  selected>60 Seconds</option>
		</select>  
		<?php 
			} else {
   		?>
		<select id="elb_time_on_page" name="elb_time_on_page">
			<option value="0">Immediately</option>
			<option value="-1">Ignore</option>
			<option value="10">10 Seconds</option>
			<option value="15">15 Seconds</option>
			<option value="20">20 Seconds</option>
			<option value="30">30 Seconds</option>
			<option value="45">45 Seconds</option>
			<option value="60">60 Seconds</option>
		</select>  
		<?php 
			}
   		?>
		
		</td></tr>
		<tr><td>Header Text: </td><td>
		<?php 
		if(get_option('elb_header_text') && get_option('elb_header_text') != '') {
     		?>
     		<input type="text" name="elb_header_text" id="elb_header_text" value="<?php echo(get_option('elb_header_text')) ?>" style="margin:3px;width:100%;" />
		
    		<?php 
			} else {
   		?>
		<input type="text" name="elb_header_text" id="elb_header_text" value="There you are - we have been waiting for you!" style="margin:3px;width:100%;" />
		<?php 
			}
   		?>
		</td></tr>
		<tr><td>Detail Text: </td>
		<td>
		<?php 
		if(get_option('elb_detail_text') && get_option('elb_detail_text') != '') {
     		?>
     		<textarea rows="4" name="elb_detail_text" id="elb_detail_text" style="margin:3px;width:100%;"><?php echo(get_option('elb_detail_text')) ?></textarea>
		<?php 
			} else {
   		?>
		<textarea rows="4" name="elb_detail_text" id="elb_detail_text" style="margin:3px;width:100%;">Sign up now for the latest new, info, and updates!</textarea>
		<?php 
			}
   		?>
		</td></tr>
		<tr><td>Popup Height: </td>
		<td>
		<?php 
		if(get_option('elb_popup_height') && get_option('elb_popup_height') != '') {
     		?>
     		<input type="text" name="elb_popup_height" id="elb_popup_height" value="<?php echo(get_option('elb_popup_height')) ?>" style="margin:3px;width:100%;" />
		<?php 
			} else {
   		?>
		<input type="text" name="elb_popup_height" id="elb_popup_height" value="160px" style="margin:3px;width:100%;" placeholder="Height in Pixels - 160px"/>
		<?php 
			}
   		?>
		</td></tr>
		<tr><td>Popup Width: </td>
		<td>
		<?php 
		if(get_option('elb_popup_width') && get_option('elb_popup_width') != '') {
     		?>
     		<input type="text" name="elb_popup_width" id="elb_popup_width" value="<?php echo(get_option('elb_popup_width')) ?>" style="margin:3px;width:100%;" />
		<?php 
			} else {
   		?>
		<input type="text" name="elb_popup_width" id="elb_popup_width" value="500px" style="margin:3px;width:100%;" placeholder="Width in Pixels - 560px"/>
		<?php 
			}
   		?>
		</td></tr>
		<tr><td>Background Image: </td>
		<td>
		<?php 
		if(get_option('elb_background_img') && get_option('elb_background_img') != '') {
     		?>
     		<input type="text" name="elb_background_img" id="elb_background_img" value="<?php echo(get_option('elb_background_img')) ?>" style="margin:3px;width:100%;" />
		<?php 
			} else {
   		?>
		<input type="text" name="elb_background_img" id="elb_background_img" value="" style="margin:3px;width:100%;" placeholder="Absolute URL:  https://www.yourdomain.com/bg.jpg"/>
		<?php 
			}
   		?>
		</td></tr>
		<tr><td>Rounded Corners: </td>
		<td>
		<?php 
		if(get_option('elb_rounded_corners') && get_option('elb_rounded_corners') == 'yes') {
     		?>
     		<select id="elb_rounded_corners" name="elb_rounded_corners">
			<option value="yes" selected>Yes</option>
			<option value="no">No</option>
		</select> 	
    		<?php 
			} else if(get_option('elb_rounded_corners') == 'no') {
   		?>
		<select id="elb_rounded_corners" name="elb_rounded_corners">
			<option value="yes">Yes</option>
			<option value="no" selected>No</option>
		</select>
		<?php 
			} else{
   		?>
		<select id="elb_rounded_corners" name="elb_rounded_corners">
			<option value="yes">Yes</option>
			<option value="no">No</option>
		</select>
		<?php 
			} 
   		?>
		<tr><td></td><td>
		<input id='elb_inputSaveSettings' type="button" value="<?php _e('Save Settings', $elb_domain) ?>" class="button-primary" /> 
		<br><small >If you don't see your latest settings reflected in your site, please refresh your browser cache
		or close and open the browser.  
		</small>	
		</td></tr>
		</table> 
			
		</form>
	    </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {

var elb_wid= $('#elb_widgetID').val();
if (elb_wid=='') 
{}
else
{
	$( "#elb_register" ).hide();
	$( "#elb_registerComplete" ).show();
	$( "#elb_noAccountSpan" ).html("Configure you Email List Builder Widget");

}
$(document).on("click", "#elb_inputSaveSettings", function () {

var elb_wid= $('#elb_widgetID').val();
var elb_tt= encodeURIComponent($('#elb_tab_text').val());
var elb_ht= encodeURIComponent($('#elb_header_text').val());
var elb_dt= encodeURIComponent($('#elb_detail_text').val());
var elb_tp= $('#elb_tab_placement').val();
var elb_top= $('#elb_time_on_page').val();
var elb_ww= $('#elb_popup_width').val();
var elb_wh= $('#elb_popup_height').val();
var elb_rc= $('#elb_rounded_corners').val();
var elb_bi= encodeURIComponent($('#elb_background_img').val());

var url = 'https://www.socialintents.com/json/jsonSaveEmailSettings.jsp?tt='+elb_tt+'&ht='+elb_ht+'&wid='+elb_wid+'&dt='+elb_dt+'&tp='+elb_tp+'&wh='+elb_wh+'&ww='+elb_ww+'&top='+elb_top+'&rc='+elb_rc+'&bi='+elb_bi+'&callback=?';sessionStorage.removeItem("settings");
$.ajax({
   type: 'GET',
    url: url,
    async: false,
    jsonpCallback: 'jsonCallBack',
    contentType: "application/json",
    dataType: 'jsonp',
    success: function(json) {
       $('#elb_widgetID').val(json.key);
	sessionStorage.removeItem("settings");
	sessionStorage.setItem("hasSeenPopup","false");
	$( "#saveDetailSettings" ).submit();
	
    },
    error: function(e) {
    }
});

  });

$(document).on("click", "#elb_inputRegister", function () {

var elb_email= $('#elb_email').val();
var elb_name= $('#elb_name').val();
var elb_password= $('#elb_password').val();
var url = 'https://www.socialintents.com/json/jsonSignup.jsp?name='+elb_name+'&email='+elb_email+'&pw='+elb_password+'&callback=?';
$.ajax({
   type: 'GET',
    url: url,
    async: false,
    jsonpCallback: 'jsonCallBack',
    contentType: "application/json",
    dataType: 'jsonp',
    success: function(json) {
	if (json.msg=='') {
         	$('#elb_widgetID').val(json.key);
		alert("Thanks for signing up!  Now customize your settings...");
		$( "#saveSettings" ).submit();
		
	}
	else {
		alert(json.msg);
	}
    },
    error: function(e) {
       
    }
});

});
});

</script>
    <?php }
    add_submenu_page('options-general.php', __('List Builder Settings', $elb_domain), __('List Builder Settings', $elb_domain), 'manage_options', 'email-list-builder-by-socialintents', 'elb_settings_page');
     add_menu_page('Account Configuration', 'List Builder', 'administrator', 'si_dashboard', 'si_dashboard', SI_SMALL_LOGO);
      add_submenu_page('si_dashboard', 'Dashboard', 'Dashboard', 'administrator', 'si_dashboard', 'si_dashboard');
   
}?>
