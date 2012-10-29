<?php
/*
Plugin Name: PhotoMosaic
Plugin URI: http://codecanyon.net/item/photomosaic-for-wordpress/243422
Description: A image gallery plugin for WordPress. See the options page for examples and instructions.
Author: makfak
Author URI: http://www.codecanyon.net/user/makfak
Version: 1.3
*/


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Illegal Entry');  
}

//============================== PhotoMosaic options ========================//
class photomosaic_plugin_options {

	function PM_getOptions() {
        $defaults = array(
            'padding' => 2,
            'columns' => 3,
            'width' => 300,
            'height' => 0,
            'links' => true,
            'random' => false,
            'force_order' => false,
            'link_to_url' => false,
            'external_links' => false,
            'auto_columns' => false,
            'center' => true,
            'show_loading' => false,
            'loading_transition' => 'fade',
            'lightbox' => true,
            'custom_lightbox' => false,
            'custom_lightbox_name' => 'prettyPhoto',
            'custom_lightbox_params' => '{}'
        );

        $options = get_option('photomosaic_options');
		
		if (!is_array($options)) {
			$options = $defaults;
			update_option('photomosaic_options', $options);
		} else {
            $options = $options + $defaults;
        }
		return $options;
	}

	function update() {
		if(isset($_POST['pm_save'])) {
			$options = photomosaic_plugin_options::PM_getOptions();

            foreach ($options as $k => $v) {
                $options[$k] = trim(stripslashes($_POST[$k]));
            }

			update_option('photomosaic_options', $options);
		} else {
			photomosaic_plugin_options::PM_getOptions();
		}

		add_menu_page('PhotoMosaic', 'PhotoMosaic', 'edit_themes', basename(__FILE__), array('photomosaic_plugin_options', 'display'));
	}
	
// -------------------------
// --- OPTIONS PAGE --------
// -------------------------
	function display() {
		$options = photomosaic_plugin_options::PM_getOptions();
		?>
		
		<div class="wrap">
			<h2>PhotoMosaic</h2>
            <p>
                PhotoMosaic takes advantage of Wordpress' built-in gallery feature.  Simply add the <code>[photomosaic]</code> shortcode to your 
                post/page content and any images attached to that post/page will be displayed as a PhotoMosaic gallery.
            </p>
            
			<form method="post" action="#" enctype="multipart/form-data" id="photomosaic-options">				
                <h3 style="clear:both; padding-bottom:5px; margin-bottom:0; border-bottom:solid 1px #e6e6e6">Layout</h3>
                <div style="overflow:hidden;">
	                <div style="width:25%;float:left;">
	                    <p>Width <i>(in pixels)</i></p>
	                    <p><input type="text" name="width" value="<?php echo($options['width']); ?>" /></p>
                        <span style="font-size:11px; color:#666666;">set to <b>0</b> for auto-sizing</span>
	                </div>
	                <div style="width:25%;float:left;">
	                    <p>Height <i>(in pixels)</i></p>
	                    <p><input type="text" name="height" value="<?php echo($options['height']); ?>" /></p>
	                    <span style="font-size:11px; color:#666666;">set to <b>0</b> for auto-sizing</span>
	                </div>
                    <div style="width:25%;float:left;">
                        <p>
                            <label><input name="center" type="checkbox" value="1" <?php if($options['center']) echo "checked='checked'"; ?> /> Center Galleries</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            causes the Mosaic to center itself to its container
                        </span>
                    </div>
                </div>
                <div style="overflow:hidden; margin-top:20px;">
                    <div style="width:25%;float:left;">
                        <p>Padding <i>(in pixels)</i></p>
                        <p><input type="text" name="padding" value="<?php echo($options['padding']); ?>" /></p>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>Columns</p>
                        <p><input type="text" name="columns" value="<?php echo($options['columns']); ?>" /></p>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>
                            <label><input name="auto_columns" type="checkbox" value="1" <?php if($options['auto_columns']) echo "checked='checked'"; ?> /> Auto-Columns</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            causes PhotoMosaic to calculate the optimal number of columns given the number of images in the gallery and the size of its container
                            - ignores the <b>columns</b> setting
                                <br/>
                            - the <b>width</b> setting is used as max-width 
                        </span>
                    </div>
                </div>
                
                
                <h3 style="clear:both; padding-bottom:5px; margin-bottom:0; border-bottom:solid 1px #e6e6e6">Behavior</h3>
                <div style="overflow:hidden;">
	                <div style="width:20%;float:left;">
	                    <p>
                            <label><input name="links" type="checkbox" value="1" <?php if($options['links']) echo "checked='checked'"; ?> /> Image Links</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">wraps images in links that point to the unresized version</span>
	                </div>
                    <div style="width:20%;float:left;">
                        <p>
                            <label><input name="link_to_url" type="checkbox" value="1" <?php if($options['link_to_url']) echo "checked='checked'"; ?> /> Link to URL</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            causes image links to point to a URL instead of the unresized image
                                <br/><br/>
                            define the link URL in the image description
                                <br/><br/>
                            requires that <b>image links</b> be checked
                        </span>
                    </div>
                    <div style="width:20%;float:left;">
	                    <p>
                            <label><input name="external_links" type="checkbox" value="1" <?php if($options['external_links']) echo "checked='checked'"; ?> /> Open Links in New Window</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            caues image links that point to a URL to open in a new window/tab
                                <br/><br/>
                            requires that <b>image links</b> and <b>link to url</b> be checked
                        </span>
	                </div>
                    <div style="width:20%;float:left;">
                        <p>
                            <label><input name="random" type="checkbox" value="1" <?php if($options['random']) echo "checked='checked'"; ?> /> Randomize</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">shuffles the image order everytime the page loads</span>
                    </div>
                    <div style="width:20%;float:left;">
                        <p>
                            <label><input name="force_order" type="checkbox" value="1" <?php if($options['force_order']) echo "checked='checked'"; ?> /> Force Image Order</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">prevents PhotoMosaic from reordering the images</span>
                    </div>
	            </div>
                <div style="overflow:hidden; margin-top:20px;">
                    <div style="width:25%;float:left;">
                        <p>
                            <label><input name="show_loading" type="checkbox" value="1" <?php if($options['show_loading']) echo "checked='checked'"; ?> /> Show Loading Spinner</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">displays a "loading gallery..." spinner until the mosaic is ready</span>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>Image Loading Transition = <?php echo $options['loading_transition']; ?></p>
                        <p>
                            <select name="loading_transition">
                              <option value="none" <?php if($options['loading_transition'] == 'none') echo "selected"; ?> >None</option>
                              <option value="fade" <?php if($options['loading_transition'] == 'fade') echo "selected"; ?> > Fade</option>
                              <option value="scale-up" <?php if($options['loading_transition'] == 'scale-up') echo "selected"; ?> >Scale Up</option>
                              <option value="scale-down" <?php if($options['loading_transition'] == 'scale-down') echo "selected"; ?> >Scale Down</option>
                              <option value="slide-up" <?php if($options['loading_transition'] == 'slide-up') echo "selected"; ?> >Slide Up</option>
                              <option value="slide-down" <?php if($options['loading_transition'] == 'slide-down') echo "selected"; ?> >Slide Down</option>
                              <option value="slide-left" <?php if($options['loading_transition'] == 'slide-left') echo "selected"; ?> >Slide Left</option>
                              <option value="slide-right" <?php if($options['loading_transition'] == 'slide-right') echo "selected"; ?> >Slide Right</option>
                            </select>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">a subtle 'arrival' effect on an image after it has been loaded</span>
                    </div>
                </div>

                <h3 style="clear:both; padding-bottom:5px; margin-bottom:0; border-bottom:solid 1px #e6e6e6">Lightbox</h3>
                <div style="overflow:hidden;">
                    <div style="width:25%;float:left;">
                        <p>
                            <label><input name="lightbox" type="checkbox" value="1" <?php if($options['lightbox']) echo "checked='checked'"; ?> /> Use Default Lightbox</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            displays your photos in a prettyPhoto lightbox when clicked.
                                <br/><br/>
                            requires that <b>image links</b> be checked
                        </span>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>
                            <label><input name="custom_lightbox" type="checkbox" value="1" <?php if($options['custom_lightbox']) echo "checked='checked'"; ?> /> Use Custom Lightbox</label>
                        </p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">allows you to specify your own lightbox and params</span>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>Custom Lightbox Name</p>
                        <p><input type="text" name="custom_lightbox_name" value="<?php echo($options['custom_lightbox_name']); ?>" /></p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            this is the name of the JS function called to activate your lightbox <br><i>(ie: prettyPhoto, fancybox, fancyZoom, facebox)</i>
                                <br><br>
                            capitalization matters
                                <br><br>
                            if you aren't familiar with JavaScript and jQuery, you may need to consult your lightbox 
                            plugin's documentation to find this function name
                        </span>
                    </div>
                    <div style="width:25%;float:left;">
                        <p>Custom Lightbox Params</p>
                        <p><textarea name="custom_lightbox_params"><?php echo($options['custom_lightbox_params']); ?></textarea></p>
                        <span style="font-size:11px; color:#666666; padding:0 30px 0 3px; display:block;">
                            this is a JS object that gets passed into your lightbox function call <br><i>(eg: {theme:'darkness'})</i>
                                <br><br>
                            if you aren't familiar with JavaScript and jQuery but have the lightbox enabled elsewhere on your site, 
                            view your page's source and look for something similar to... 
                                <br>
                            <i>$().lightboxName({ 
                                <br>&nbsp;&nbsp;&nbsp;
                                option:value, 
                                <br>&nbsp;&nbsp;&nbsp;
                                option2:value2 
                                <br>
                                });
                            </i>
                        </span>
                    </div>
                </div>

				<p style="margin-top:30px;"><input class="button-primary" type="submit" name="pm_save" value="Save Changes" /></p>
                <ul id="photomosaic-error-list"></ul>
			</form>
            
            <div style="margin:50px 0 0 0;">
                <h3 style="clear:both; padding-bottom:5px; margin:0; border-bottom:solid 1px #e6e6e6">Inline Attributes</h3>
                <p>
                    The PhotoMosaic shortcode has full support for inline attributes (eg. <code>[photomosaic width="600" height="400" random="1"]</code>). 
                    Any inline attributes will override the default values set on this page.  Available attributes:
                </p>
                <ul style="list-style:disc; margin:0 0 0 20px;">
                    <li><b>id</b> : the post/page id for the desired gallery</li>
                    <li><b>nggid</b> : the ID for the desired NextGen gallery</li>
                    <li><b>padding</b> : any number <i>(in pixels)</i></li>
                    <li><b>columns</b> : any number</li>
                    <li><b>width</b> : any number <i>(in pixels)</i></li>
                    <li><b>height</b> : any number <i>(in pixels)</i></li>
                    <li><b>center</b> : 1 = yes, 0 = no</li>
                    <li><b>links</b> : 1 = yes, 0 = no</li>
                    <li><b>random</b> : 1 = yes, 0 = no</li>
                    <li><b>force_order</b> : 1 = yes, 0 = no</li>
                    <li><b>link_to_url</b> : 1 = yes, 0 = no</li>
                    <li><b>external_links</b> : 1 = yes, 0 = no</li>
                    <li><b>auto_columns</b> : 1 = yes, 0 = no</li>
                    <li><b>show_loading</b> : 1 = yes, 0 = no</li>
                    <li><b>lightbox</b> : 1 = yes, 0 = no</li>
                    <li><b>custom_lightbox</b> : 1 = yes, 0 = no</li>
                    <li><b>custom_lightbox_name</b> : js function name <i>(eg: prettyPhoto)</i></li>
                    <li><b>custom_lightbox_params</b> : js object passed to the above function <i>(eg: {theme:'darkness'})</i></li>
                </ul>
            </div>
            
            <div style="margin:50px 0 0 0;">
                <h3 style="clear:both; padding-bottom:5px; margin:0; border-bottom:solid 1px #e6e6e6">Template Tag</h3>
                <p>
                    PhotoMosaic also supports Wordpress Template Tags (<code>wp_photomosaic()</code>).  This can be added to your theme's
                    template files to automatically add a gallery to every page.
                </p>
                <p>The PhotoMosaic template tag accepts an array of the attributes listed above.  For Example:</p>
<pre><code style="display:block">   $atts = array(
        'id' => 1,
        'columns' => 3
    );
    wp_photomosaic($atts);</code></pre>
            </div>

            <div style="margin:50px 0 0 0;">
                <h3 style="clear:both; padding-bottom:5px; margin:0; border-bottom:solid 1px #e6e6e6">Widget</h3>
                <p>
                    To use PhotoMosaic in your Widget-enabled sidebar simply add a standard text widget and 
                    add a <code>[photomosaic]</code> shortcode to the widget's text (exactly as you would in a page or post).
                </p>
            </div>

            <div style="margin:50px 0 0 0;">
                <p>Thank you for purchasing PhotoMosaic for Wordpress</p>
            </div>
		</div>
		<?php
	} 
} 

function PM_getOption($option) {
    global $mytheme;
    return $mytheme->option[$option];
}

// register functions
add_filter('widget_text', 'do_shortcode'); // Widget
add_action('admin_menu', array('photomosaic_plugin_options', 'update'));

$options = get_option('photomosaic_options');


//============================== insert HTML header tag ========================//
wp_register_script( 'JQPM', plugins_url('/js/jquery-1.8.2.min.js', __FILE__ ));

wp_enqueue_script('JQPM');
if (!is_admin()) {
	if($options['lightbox']) {
		wp_enqueue_style( 'prettyphoto-styles', plugins_url('/includes/prettyPhoto/prettyPhoto.css', __FILE__ ));
		wp_enqueue_script( 'prettyphoto-script', plugins_url('/includes/prettyPhoto/jquery.prettyPhoto.js', __FILE__ ), array('JQPM'));
	}
	
	wp_enqueue_style( 'photomosaic-custom-styles', plugins_url('/css/photoMosaic.css', __FILE__ ));
	wp_enqueue_script( 'photomosaic-custom-script', plugins_url('/js/jquery.photoMosaic.js', __FILE__ ), array('JQPM'));

	add_shortcode( 'photoMosaic', 'photomosaic_shortcode' );
	add_shortcode( 'photomosaic', 'photomosaic_shortcode' );
} else if (isset($_GET['page'])) { 
    if ($_GET['page'] == "photoMosaic.php") {
        wp_enqueue_script( 'photomosaic-form-validation', plugins_url('/js/jquery.photoMosaic.wp.form.js', __FILE__ ), array('JQPM'));
    }
}


function photomosaic_shortcode( $atts ) {
	global $post;
	$post_id = intval($post->ID);
    $options = photomosaic_plugin_options::PM_getOptions();

	extract(shortcode_atts(array(
        'id'                       => $post_id,
        'padding'                  => $options['padding'],
        'columns'                  => $options['columns'],
        'width'                    => $options['width'],
        'height'                   => $options['height'],
        'links'                    => $options['links'],
        'random'                   => $options['random'],
        'force_order'              => $options['force_order'],
        'link_to_url'              => $options['link_to_url'],
        'external_links'           => $options['external_links'],
        'auto_columns'             => $options['auto_columns'],
        'center'               	   => $options['center'],
        'show_loading'             => $options['show_loading'],
        'loading_transition'       => $options['loading_transition'],
        'lightbox'                 => $options['lightbox'],
        'custom_lightbox'          => $options['custom_lightbox'],
        'custom_lightbox_name'     => $options['custom_lightbox_name'],
        'custom_lightbox_params'   => $options['custom_lightbox_params'],
        'include'                  => '',
        'exclude'                  => ''
	), $atts));

	$unique = time() + rand(21,40);
	
	$output_buffer = '
		<script type="text/javascript" data-photomosaic-gallery="true">
			var PMalbum'.$unique.' = [';

	if ( !empty($atts['nggid']) ) {
		$output_buffer .= PMBuildJsonFromNGG($atts['nggid'], $link_to_url);
	} else {
		$output_buffer .= PMBuildJsonFromPost($id, $link_to_url, $include, $exclude);
	}		
	
	$output_buffer .='];
		</script><script type="text/javascript" data-photomosaic-call="true">';
	
	if(intval($height) == 0){
		$opts_height = "'auto'";
	} else {
		$opts_height = intval($height);
	}
	
	if(intval($width) == 0){
		$opts_width = "'auto'";
	} else {
		$opts_width = intval($width);
	}

    if(intval($center)){
        $opts_center = "true";
    } else {
        $opts_center = "false";
    }

    if(intval($links)){
        $opts_links = "true";
    } else {
        $opts_links = "false";
    }

	if(intval($random)){
		$opts_random = "true";
	} else {
		$opts_random = "false";
	}

	if(intval($force_order)){
		$opts_force_order = "true";
	} else {
		$opts_force_order = "false";
	}

	if(intval($external_links)){
		$opts_external_links = "true";
	} else {
		$opts_external_links = "false";
	}

	if(intval($auto_columns)){
		$opts_auto_columns = "true";
	} else {
		$opts_auto_columns = "false";
	}
	
	if(intval($show_loading)){
		$opts_show_loading = "true";
	} else {
		$opts_show_loading = "false";
	}

	if(intval($lightbox)){
		$lightbox = "true";
	} else {
		$lightbox = "false";
	}
	
	if(intval($custom_lightbox)){
		$custom_lightbox = "true";
		// just in case
		$lightbox = "false";
	} else {
		$custom_lightbox = "false";
	}

	$output_buffer .='
            JQPM(document).ready(function($) {
                $("#photoMosaicTarget'.$unique.'").photoMosaic({
                    gallery: PMalbum'.$unique.',
                    padding: '. intval($padding) .',
                    columns: '. intval($columns) .',
                    width: '. $opts_width .',
                    height: '. $opts_height .',
                    center: '. $opts_center .',
                    links: '. $opts_links .',
                    external_links: '. $opts_external_links .',
                    auto_columns: '. $opts_auto_columns .',
                    show_loading: '. $opts_show_loading .',
                    loading_transition: "'. $loading_transition .'",
			';
			
			if($options['lightbox'] || $options['custom_lightbox']) {
				$output_buffer .='
					modal_name: "pmlightbox",
					modal_group: true,';
				
				if($options['lightbox']) {
					$output_buffer .='
						modal_ready_callback : function($photomosaic){
							$("a[rel^=\'pmlightbox\']", $photomosaic).prettyPhoto({
                    			overlay_gallery: false
                			});
						},
					';
				} elseif ($options['custom_lightbox']) {
					$output_buffer .='
						modal_ready_callback : function($photomosaic){
							jQuery("a[rel^=\'pmlightbox\']", $photomosaic).'.$options['custom_lightbox_name'].'('.$options['custom_lightbox_params'].');
						},
					';
				}
			}
			
			$output_buffer .='
					random: '. $opts_random .',
					force_order: '. $opts_force_order .'
				});
			});
		</script>
		<div id="photoMosaicTarget'.$unique.'"></div>';
	return preg_replace('/\s+/', ' ', $output_buffer);
}


function PMBuildJsonFromPost($id, $link_to_url, $include, $exclude){
	$output_buffer = '';
	
	if ( !empty($include) ) { 
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'asc', 'orderby' => 'menu_order') );
				
		$attachments = array();

		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'asc', 'orderby' => 'menu_order') );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'asc', 'orderby' => 'menu_order') );
	}
	
	if ( !empty($attachments) ) {
		$i = 0;
		$len = count($attachments);
		foreach ( $attachments as $aid => $attachment ) {

			$image_full = wp_get_attachment_image_src( $aid , 'full');
			$image_medium = wp_get_attachment_image_src( $aid , 'medium');
			$_post = & get_post($aid); 
			$image_title = attribute_escape($_post->post_title);
			$image_alttext = get_post_meta($aid, '_wp_attachment_image_alt', true);
			$image_caption = $_post->post_excerpt;
			$image_description = $_post->post_content;
			
			if( intval($link_to_url) ) { 
				$url_data = ',url: "' . $image_description . '"';
			} else {
				$url_data = '';
			}

            $output_buffer .='{
                src: "' . $image_full[0] . '",
                thumb: "' . $image_medium[0] . '",
                caption: "' . $image_caption . '",
                width: "' . $image_full[1] . '",
                height: "' . $image_full[2] . '"
                ' . $url_data . '
            }';

			if($i != $len - 1) {
				$output_buffer .=',';	    
			}

			$i++;
		}
	}	

	return $output_buffer;
}

function PMBuildJsonFromNGG($galleryID, $link_to_url) {
	global $wpdb, $post;
	$output_buffer ='';

	//Set sort order value, if not used (upgrade issue)
    $ngg_options['galSort'] = ($ngg_options['galSort']) ? $ngg_options['galSort'] : 'pid';
    $ngg_options['galSortDir'] = ($ngg_options['galSortDir'] == 'DESC') ? 'DESC' : 'ASC';
    
    // get gallery values
    $picturelist = nggdb::get_gallery($galleryID, $ngg_options['galSort'], $ngg_options['galSortDir']);

    $i = 0;
    $len = count($picturelist);
    foreach ($picturelist as $key => $picture) {
    	if( intval($link_to_url) ) { 
			$str = $picture->description;
			$pattern = '#(www\.|https?:\/\/){1}[a-zA-Z0-9]{2,}\.[a-zA-Z0-9]{2,}(\S*)#i';
			if ( preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER) ) {
				$url_data = ',url: "' . $matches[0][0] . '"';
			} else {
				$url_data = '';
			}
		} else {
			$url_data = '';
		}

        $output_buffer .='{
            src: "' . $picture->imageURL . '",
            thumb: "' . $picture->thumbURL . '",
            caption: "' . $picture->description . '",
            width: "' . $picture->meta_data['width'] . '",
            height: "' . $picture->meta_data['height'] . '"
            ' . $url_data . '
        }';

		if($i != $len - 1) {
			$output_buffer .=',';	    
		}
		
    	$i++;
    }
	return $output_buffer;
}


// Template Tag
function wp_photomosaic( $atts ){
	if (!is_admin()) {
		echo photomosaic_shortcode( $atts );
	}
}