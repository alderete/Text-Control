<?php
/*
Plugin Name: Text Control
Plugin URI: http://github.com/alderete/Text-Control
Description: Take total control of how your blog formats text: Textile 1+2, Markdown, AutoP, nl2br, SmartyPant, and Texturize. Blog wide, individual posts, and defaults for comments.
Author: Jeff Minard, Frank B&uuml;ltge, Michael A. Alderete
Author URI: http://thecodepro.com/, http://bueltge.de, http://aldosoft.com/
Version: 2.5
Date: 01.13.2010 00:45:00
*/ 

// Hey!
// Get outta here! All configuration is done in WordPress
// Check the "options > Text Control Config" in your admin section


// Pre-2.6 compatibility
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


function tc_textdomain() {

	if (function_exists('load_plugin_textdomain')) {
		if ( !defined('WP_PLUGIN_DIR') ) {
			load_plugin_textdomain('textcontrol', str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');
		} else {
			load_plugin_textdomain('textcontrol', false, dirname(plugin_basename(__FILE__)) . '/languages');
		}
	}
}


/**
 * Add action link(s) to plugins page
 * Thanks Dion Hulse -- http://dd32.id.au/wordpress-plugins/?configure-link
 */
function tc_filter_plugin_actions($links, $file){
	static $this_plugin;

	if ( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="options-general.php?page=text-control/text-control.php">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}

/**
 * Images/ Icons in base64-encoding
 * @use function tc_get_resource_url() for display
 */
if( isset($_GET['resource']) && !empty($_GET['resource'])) {
	# base64 encoding performed by base64img.php from http://php.holtsmark.no
	$resources = array(
		'text-control.gif' => 
			'R0lGODlhCwALAPcAAAAAAAEBAQICAgMDAwQEBAUFBQYGBgcHBw'.
			'gICAkJCQoKCgsLCwwMDA0NDQ4ODg8PDxAQEBERERISEhMTExQU'.
			'FBUVFRYWFhcXFxgYGBkZGRoaGhsbGxwcHB0dHR4eHh8fHyAgIC'.
			'EhISIiIiMjIyQkJCUlJSYmJicnJygoKCkpKSoqKisrKywsLC0t'.
			'LS4uLi8vLzAwMDExMTIyMjMzMzQ0NDU1NTY2Njc3Nzg4ODk5OT'.
			'o6Ojs7Ozw8PD09PT4+Pj8/P0BAQEFBQUJCQkNDQ0REREVFRUZG'.
			'RkdHR0hISElJSUpKSktLS0xMTE1NTU5OTk9PT1BQUFFRUVJSUl'.
			'NTU1RUVFVVVVZWVldXV1hYWFlZWVpaWltbW1xcXF1dXV5eXl9f'.
			'X2BgYGFhYWJiYmNjY2RkZGVlZWZmZmdnZ2hoaGlpaWpqamtra2'.
			'xsbG1tbW5ubm9vb3BwcHFxcXJycnNzc3R0dHV1dXZ2dnd3d3h4'.
			'eHl5eXp6ent7e3x8fH19fX5+fn9/f4CAgIGBgYKCgoODg4SEhI'.
			'WFhYaGhoeHh4iIiImJiYqKiouLi4yMjI2NjY6Ojo+Pj5CQkJGR'.
			'kZKSkpOTk5SUlJWVlZaWlpeXl5iYmJmZmZqampubm5ycnJ2dnZ'.
			'6enp+fn6CgoKGhoaKioqOjo6SkpKWlpaampqenp6ioqKmpqaqq'.
			'qqurq6ysrK2tra6urq+vr7CwsLGxsbKysrOzs7S0tLW1tba2tr'.
			'e3t7i4uLm5ubq6uru7u7y8vL29vb6+vr+/v8DAwMHBwcLCwsPD'.
			'w8TExMXFxcbGxsfHx8jIyMnJycrKysvLy8zMzM3Nzc7Ozs/Pz9'.
			'DQ0NHR0dLS0tPT09TU1NXV1dbW1tfX19jY2NnZ2dra2tvb29zc'.
			'3N3d3d7e3t/f3+Dg4OHh4eLi4uPj4+Tk5OXl5ebm5ufn5+jo6O'.
			'np6erq6uvr6+zs7O3t7e7u7u/v7/Dw8PHx8fLy8vPz8/T09PX1'.
			'9fb29vf39/j4+Pn5+fr6+vv7+/z8/P39/f7+/v///ywAAAAACw'.
			'ALAAAIhQD//UN3y5GhQ63ICfxHrtCZLl+8iLnz7J+6RF/csPFi'.
			'ho4YOOBmlfljTloaYe4cednUaMutf+UK0fvHqcqbQllm/TKD5t'.
			'sfLVrGHNoiqFSWLq/gbBmTJtUZLoICcVnzyMwXSeTsiOmyNUwY'.
			'L2C2/YsWZ0wYMWK+kPm1MFwoOGTUZPImMCAAOw=='.
			'');
	
	if(array_key_exists($_GET['resource'], $resources)) {

		$content = base64_decode($resources[ $_GET['resource'] ]);

		$lastMod = filemtime(__FILE__);
		$client = ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false );
		// Checking if the client is validating his cache and if it is current.
		if (isset($client) && (strtotime($client) == $lastMod)) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 304);
			exit;
		} else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 200);
			header('Content-Length: '.strlen($content));
			header('Content-Type: image/' . substr(strrchr($_GET['resource'], '.'), 1) );
			echo $content;
			exit;
		}
	}
}


/**
 * Display Images/ Icons in base64-encoding
 * @return $resourceID
 */
function tc_get_resource_url($resourceID) {
	
	return trailingslashit( get_bloginfo('url') ) . '?resource=' . $resourceID;
}


/**
 * settings in plugin-admin-page
 */
function tc_add_settings_page() {
	global $wp_version;
	
	if ( function_exists('add_options_page') && current_user_can('switch_themes') ) {
		$menutitle = '';
		if ( version_compare( $wp_version, '2.6.999', '>' ) ) {
			$menutitle = '<img src="' . tc_get_resource_url('text-control.gif') . '" alt="" />' . ' ';
		}
		$menutitle .= __('Text Control', 'textcontrol');
		
		add_options_page(__('Text Control Formatting Options', 'textcontrol'), $menutitle, 8, __FILE__, 'tc_post_option_page');
		add_filter('plugin_action_links', 'tc_filter_plugin_actions', 10, 2);
	}
}


/**
 * credit in wp-footer
 */
function tc_admin_footer() {
	if ( basename($_SERVER['REQUEST_URI']) == 'text-control.php') {
		$plugin_data = get_plugin_data( __FILE__ );
		printf('%1$s ' . __('plugin') . ' | ' . __('Version') . ' %2$s | ' . __('Author') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
	}
}


function tc_post($text) {
	global $id;
	
	/* Start with the default values */
	$do_text = get_option('tc_post_format');
	$do_char = get_option('tc_post_encoding');
	
	/* Now for the updated format that will take precedence */
	if ( get_post_meta($id, '_tc_post_format', true) ) {
		$do_text = get_post_meta($id, '_tc_post_format', true);
	}
	if( get_post_meta($id, '_tc_post_encoding', true) ) {
		$do_char =  get_post_meta($id, '_tc_post_encoding', true);
	}
	
	$text = tc_post_process($text, $do_text, $do_char);
	
	return $text;
}


function tc_comment($text) {
	
	$do_text = get_option('tc_comment_format');
	$do_char = get_option('tc_comment_encoding');
	
	$text = tc_post_process($text, $do_text, $do_char);
	
	return $text;
}


function tc_post_process($text, $do_text='', $do_char='') {
	
	if($do_text == 'textile2') {
		require_once('text-control/textile2.php');
		$t = new Textile();
		$text = $t->TextileThis($text);
		
	} else if($do_text == 'textile1') {
		require_once('text-control/textile1.php');
		$text = textile($text);
		
	} else if($do_text == 'markdown') {
		require_once('text-control/markdown.php');
		$text = Markdown($text);
		
	} else if($do_text == 'wpautop') {
		$text = wpautop($text);
		
	} else if($do_text == 'nl2br') {
		$text = nl2br($text);
		
	} else if($do_text == 'none') {
		$text = $text;
		
	} else {
		$text = wpautop($text);
	}
	
	if($do_char == 'smartypants') {
		require_once('text-control/smartypants.php');
		$text = SmartyPants($text);
		
	} else if($do_char == 'wptexturize') {
		$text = wptexturize($text);
		
	} else if($do_char == 'none') {
		$text = $text;
		
	} else {
		$text = wptexturize($text);
	}
	
	return $text;
}


/* confiug page in  options */
function tc_post_option_page() {
	global $wpdb, $wp_version;
?>

<div class="wrap">
	<h2><?php _e('Text Control', 'textcontrol'); ?></h2>
	
	<?php
	if( $_POST['tc_post_format'] && $_POST['tc_post_encoding'] ) {
		// set the post formatting options
		update_option('tc_post_format', $_POST['tc_post_format']);
		update_option('tc_post_encoding', $_POST['tc_post_encoding']);
		
		echo '<div class="updated"><p>' . __('Post/Excerpt formatting updated.', 'textcontrol') . '</p></div>';
	}
	
	if( $_POST['tc_comment_format'] && $_POST['tc_comment_encoding'] ) {
		// set the comment formatting options
		update_option('tc_comment_format', $_POST['tc_comment_format']);
		update_option('tc_comment_encoding', $_POST['tc_comment_encoding']);
		
		echo '<div class="updated"><p>' . __('Comment formatting updated.', 'textcontrol') . '</p></div>';
	}
	?>
	
	<br class="clear" />
	
		<div id="poststuff" class="ui-sortable">
			<div class="postbox" >
				<h3><?php _e('Text Control Settings', 'textcontrol'); ?></h3>
				<div class="inside">
					<p><?php _e('Set up the default settings for your blog text formatting.', 'textcontrol'); ?></p>
					
					<br class="clear" />
					
					<form method="post" action="">
						<fieldset class="options">
							<legend><strong><?php _e('Posts &amp; Excerpts', 'textcontrol'); ?></strong></legend>
							
							<p><?php _e('These will set up what the blog parses posts with by default unless over ridden on a per-post basis.', 'textcontrol'); ?></p>
							
							<?php
							$do_text = get_option('tc_post_format');
							$do_char = get_option('tc_post_encoding');
							?>
							<select name="tc_post_format">
								<option value="wpautop"<?php if($do_text == 'wpautop' OR $do_text == ''){ echo(' selected="selected"');}?>>Default (wpautop)</option>
								<option value="textile1"<?php if($do_text == 'textile1'){ echo(' selected="selected"');}?>>Textile 1</option>
								<option value="textile2"<?php if($do_text == 'textile2'){ echo(' selected="selected"');}?>>Textile 2</option>
								<option value="markdown"<?php if($do_text == 'markdown'){ echo(' selected="selected"');}?>>Markdown</option>
								<option value="nl2br"<?php if($do_text == 'nl2br'){ echo(' selected="selected"');}?>>nl2br</option>
								<option value="none"<?php if($do_text == 'none'){ echo(' selected="selected"');}?>><?php _e('No Formatting', 'textcontrol'); ?></option>
							</select>
							
							<select name="tc_post_encoding">
								<option value="wptexturize"<?php if($do_char == 'wptexturize'){ echo(' selected="selected"');}?>>Default (wptexturize)</option>
								<option value="smartypants"<?php if($do_char == 'smartypants'){ echo(' selected="selected"');}?>>Smarty Pants</option>
								<option value="none"<?php if($do_char == 'none'){ echo(' selected="selected"');}?>><?php _e('No Character Encoding', 'textcontrol'); ?></option>
							</select>
							
							<input class="button" type="submit" value="<?php _e('Change Default Post/Excerpt Formatting', 'textcontrol'); ?>" />
							
					</fieldset>
					</form>
					<br />
					<form method="post" action="">
						<fieldset class="options">
							<legend><strong><?php _e('Comments', 'textcontrol'); ?></strong></legend>
							<p><?php _e('All comments will be processed with these:', 'textcontrol'); ?></p>
							
							<?php
							$do_text = get_option('tc_comment_format');
							$do_char = get_option('tc_comment_encoding');
							?>
							<select name="tc_comment_format">
								<option value="wpautop"<?php if($do_text == 'wpautop' OR $do_text == ''){ echo(' selected="selected"');}?>>Default (wpautop)</option>
								<option value="textile1"<?php if($do_text == 'textile1'){ echo(' selected="selected"');}?>>Textile 1</option>
								<option value="textile2"<?php if($do_text == 'textile2'){ echo(' selected="selected"');}?>>Textile 2</option>
								<option value="markdown"<?php if($do_text == 'markdown'){ echo(' selected="selected"');}?>>Markdown</option>
								<option value="nl2br"<?php if($do_text == 'nl2br'){ echo(' selected="selected"');}?>>nl2br</option>
								<option value="none"<?php if($do_text == 'none'){ echo(' selected="selected"');}?>><?php _e('No Formatting', 'textcontrol'); ?></option>
							</select>
							
							<select name="tc_comment_encoding">
								<option value="wptexturize"<?php if($do_char == 'wptexturize'){ echo(' selected="selected"');}?>>Default (wptexturize)</option>
								<option value="smartypants"<?php if($do_char == 'smartypants'){ echo(' selected="selected"');}?>>Smarty Pants</option>
								<option value="none"<?php if($do_char == 'none'){ echo(' selected="selected"');}?>><?php _e('No Character Formatting', 'textcontrol'); ?></option>
							</select>
							
							<input class="button" type="submit" value="<?php _e('Change Default Comment Formatting', 'textcontrol'); ?>" />
							
						</fieldset>
					</form>	
				</div>
			</div>
		</div>

		<div id="poststuff" class="ui-sortable">
			<div class="postbox closed" >
				<h3><?php _e('Information on the plugin', 'feedstats') ?></h3>
				<div class="inside">
					<p><?php _e('For further information or to grab the latest version of this plugin, visit the <a href=\'http://dev.wp-plugins.org/wiki/TextControl\' title=\'to the plugin-website\' >plugin\'s homepage</a>.', 'textcontrol'); ?><br />&copy; Copyright 2006 - <?php echo date("Y"); ?> <a href="http://thecodepro.com/" title="<?php _e('to the Website', 'textcontrol'); ?>" >Jeff Minard</a> <?php _e('and', 'textcontrol'); ?> <a href="http://bueltge.de" title="<?php _e('to the Website', 'textcontrol'); ?>" >Frank B&uuml;ltge</a> | <?php _e('Want to say thank you? Visit my <a href=\'http://bueltge.de/wunschliste/\' title=\'to the wishlist\' >wishlist</a>.', 'textcontrol'); ?></p>
					<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
				</div>
			</div>
		</div>

		<script type="text/javascript">
		<!--
		<?php if ( version_compare( substr($wp_version, 0, 3), '2.7', '<' ) ) { ?>
		jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
		<?php } ?>
		jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); } );
		jQuery('.postbox.close-me').each(function(){
			jQuery(this).addClass("closed");
		});
		//-->
		</script>
		
	</div>

<?php
}


function tc_add_custom_box() {

	if ( function_exists('add_meta_box') ) { // for WordPress >=2.5
		add_meta_box('textcontroldiv', __('Text Control', 'textcontrol'),
					 'tc_inside_the_textcontrol_box', 'post', 'advanced');
		add_meta_box('textcontroldiv', __('Text Control', 'textcontrol'),
					 'tc_inside_the_textcontrol_box', 'page', 'advanced');

	} else {
		add_filter('dbx_post_advanced', 'tc_post_admin_footer');
		add_action('dbx_page_advanced', 'tc_post_admin_footer' );
	}
}


/* remove and add filter in wp */
if ( function_exists( 'add_action') ) {
	add_action('init', 'tc_textdomain');
	
	remove_filter('the_content', 'wpautop');
	remove_filter('the_content', 'wptexturize');
	add_filter('the_content', 'tc_post');
	
	remove_filter('the_excerpt', 'wpautop');
	remove_filter('the_excerpt', 'wptexturize');
	add_filter('the_excerpt', 'tc_post');
	
	remove_filter('comment_text', 'wpautop', 30);
	remove_filter('comment_text', 'wptexturize');
	add_filter('comment_text', 'tc_comment');
	
	if ( is_admin() ) {
		add_action('admin_menu', 'tc_add_settings_page');
		add_action('admin_menu', 'tc_add_custom_box');
		add_filter('edit_post', 'tc_post_edit_post');
		add_filter('publish_post', 'tc_post_edit_post');
		add_action('in_admin_footer', 'tc_admin_footer');
	}
}


function tc_post_edit_post($id) {
	global $wpdb, $id;

	if(!isset($id)) $id = $_REQUEST['post_ID'];
	
	if( $id && $_POST['tc_post_i_format'] && $_POST['tc_post_i_encoding'] ){

		$qry = "DELETE FROM {$wpdb->postmeta} WHERE Post_ID = {$id} AND meta_key = '_tc_post_format';";
		$wpdb->query($qry);
		
		$qry = "DELETE FROM {$wpdb->postmeta} WHERE Post_ID = {$id} AND meta_key = '_tc_post_encoding';";
		$wpdb->query($qry);
			
		$qry = "INSERT INTO {$wpdb->postmeta} (Post_ID, meta_key, meta_value) VALUES ({$id}, '_tc_post_format', '$_POST[tc_post_i_format]');";
		$wpdb->query($qry);
			
		$qry = "INSERT INTO {$wpdb->postmeta} (Post_ID, meta_key, meta_value) VALUES ({$id}, '_tc_post_encoding', '$_POST[tc_post_i_encoding]');";
		$wpdb->query($qry);
	}
	
}


function tc_inside_the_textcontrol_box($post) {
	global $id;

	if(!isset($id)) $id = $_REQUEST['post'];

	$do_text = get_option('tc_post_format');
	$do_char = get_option('tc_post_encoding');

	if ( get_post_meta($id, '_tc_post_format', true) ) {
		$do_text = get_post_meta($id, '_tc_post_format', true);
	}
	if( get_post_meta($id, '_tc_post_encoding', true) ) {
		$do_char =  get_post_meta($id, '_tc_post_encoding', true);
	}
?>
	<p><?php _e('Format this post with:', 'textcontrol'); ?>
		<select name="tc_post_i_format">
			<option value="wpautop"<?php if($do_text == 'wpautop' OR $do_text == ''){ echo(' selected="selected"');}?>>Default (wpautop)</option>
			<option value="textile1"<?php if($do_text == 'textile1'){ echo(' selected="selected"');}?>>Textile 1</option>
			<option value="textile2"<?php if($do_text == 'textile2'){ echo(' selected="selected"');}?>>Textile 2</option>
			<option value="markdown"<?php if($do_text == 'markdown'){ echo(' selected="selected"');}?>>Markdown</option>
			<option value="nl2br"<?php if($do_text == 'nl2br'){ echo(' selected="selected"');}?>>nl2br</option>
			<option value="none"<?php if($do_text == 'none'){ echo(' selected="selected"');}?>><?php _e('No Formatting', 'textcontrol'); ?></option>
		</select>
		
		<select name="tc_post_i_encoding">
			<option value="wptexturize"<?php if($do_char == 'wptexturize'){ echo(' selected="selected"');}?>>Default (wptexturize)</option>
			<option value="smartypants"<?php if($do_char == 'smartypants'){ echo(' selected="selected"');}?>>Smarty Pants</option>
			<option value="none"<?php if($do_char == 'none'){ echo(' selected="selected"');}?>><?php _e('No Character Formatting', 'textcontrol'); ?></option>
		</select>
	
		<!--<input type="submit" value="Change Post Formatting" />-->
	</p>
	
	<script language="JavaScript" type="text/javascript"><!--
	var placement = document.getElementById("titlediv");
	var substitution = document.getElementById("mtspp");
	var mozilla = document.getElementById&&!document.all;
	if(mozilla)
		 placement.parentNode.appendChild(substitution);
	else placement.parentElement.appendChild(substitution);
	//--></script>
<?php
}


function tc_post_admin_footer($content) {

	// Are we on the right page?
	if ( (preg_match('|post.php|i', $_SERVER['SCRIPT_NAME']) && $_REQUEST['action'] == 'edit' ) || (preg_match('|post-new.php|i', $_SERVER['SCRIPT_NAME'])) || (preg_match('|page.php|i', $_SERVER['SCRIPT_NAME']) && $_REQUEST['action'] == 'edit' ) || (preg_match('|page-new.php|i', $_SERVER['SCRIPT_NAME'])) ) {

			global $id;

			if(!isset($id)) $id = $_REQUEST['post'];
	
			$do_text = get_option('tc_post_format');
			$do_char = get_option('tc_post_encoding');
		
			if ( get_post_meta($id, '_tc_post_format', true) ) {
				$do_text = get_post_meta($id, '_tc_post_format', true);
			}
			if( get_post_meta($id, '_tc_post_encoding', true) ) {
				$do_char =  get_post_meta($id, '_tc_post_encoding', true);
			} ?>
		
			<div class="dbx-b-ox-wrapper">
				<fieldset id="textcontrol" class="dbx-box">
				<div class="dbx-h-andle-wrapper">
					<h3 class="dbx-handle"><?php _e('Text Control', 'textcontrol') ?></h3>
				</div>
					<div class="dbx-c-ontent-wrapper">
						<div id="postcustomstuff" class="dbx-content">
							<p><?php _e('Format this post with:', 'textcontrol'); ?>
								<select name="tc_post_i_format">
									<option value="wpautop"<?php if($do_text == 'wpautop' OR $do_text == ''){ echo(' selected="selected"');}?>>Default (wpautop)</option>
									<option value="textile1"<?php if($do_text == 'textile1'){ echo(' selected="selected"');}?>>Textile 1</option>
									<option value="textile2"<?php if($do_text == 'textile2'){ echo(' selected="selected"');}?>>Textile 2</option>
									<option value="markdown"<?php if($do_text == 'markdown'){ echo(' selected="selected"');}?>>Markdown</option>
									<option value="nl2br"<?php if($do_text == 'nl2br'){ echo(' selected="selected"');}?>>nl2br</option>
									<option value="none"<?php if($do_text == 'none'){ echo(' selected="selected"');}?>><?php _e('No Formatting', 'textcontrol'); ?></option>
								</select>
								
								<select name="tc_post_i_encoding">
									<option value="wptexturize"<?php if($do_char == 'wptexturize'){ echo(' selected="selected"');}?>>Default (wptexturize)</option>
									<option value="smartypants"<?php if($do_char == 'smartypants'){ echo(' selected="selected"');}?>>Smarty Pants</option>
									<option value="none"<?php if($do_char == 'none'){ echo(' selected="selected"');}?>><?php _e('No Character Formatting', 'textcontrol'); ?></option>
								</select>
					
								<!--<input type="submit" value="Change Post Formatting" />-->
							</p>
							
							<script language="JavaScript" type="text/javascript"><!--
							var placement = document.getElementById("titlediv");
							var substitution = document.getElementById("mtspp");
							var mozilla = document.getElementById&&!document.all;
							if(mozilla)
								 placement.parentNode.appendChild(substitution);
							else placement.parentElement.appendChild(substitution);
							//--></script>
				
						</div>
					</div>
				</fieldset>
			</div>
			<?php
			
	}
}
?>