<?php

/**
 * Onyx custom filter or action functions to alter the wordpress behavior
 * To register a hook, see config/hooks.php
 * Please, do not register any hook here
 *
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference
 *
 */

/**
 * Remove wordpress frontend jquery
 * and reallocate js from header to footer.
 * Registered at actions->add->wp_enqueue_scripts at config/hook.php
 * 
 * @return void
 */
function onyx_header_footer_scripts() {
	// remove wordpress frontend jquery
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', (""));
		wp_enqueue_script('jquery');
		wp_deregister_script('wp-embed');
	}

	// relocate javascripts to footer
	remove_action('wp_head', 'wp_print_scripts');
	remove_action('wp_head', 'wp_print_head_scripts', 9);
	remove_action('wp_head', 'wp_enqueue_scripts', 1);
	add_action('wp_footer', 'wp_print_scripts', 5);
	add_action('wp_footer', 'wp_enqueue_scripts', 5);
	add_action('wp_footer', 'wp_print_head_scripts', 5);
}

/**
 * Add custom internal and slug classes to body_class.
 * Registered at filters->add->body_class at config/hook.php
 * 
 * @return void
 */
function onyx_body_classes($classes) {
	global$post;
	if (!is_home()) {
		$classes[] = 'int';
	}
	if (isset($post) && is_page()) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}

/**
 * Remove empty paragraphs
 * Registered at filters->add->the_content at config/hook.php
 * 
 * @return mixed
 */
function onyx_remove_empty_p($content){
	if (!O::is_amp()) {
		$content = force_balance_tags($content);
		$content = preg_replace('#<p>(\s|&nbsp;)*+(<br\s*/*>)?(\s|&nbsp;)*</p>#i', '', $content);
	}
	return $content;
}

/**
 * Add single templates by categories.
 * single-catslug.php
 * Registered at filters->add->single_templateat config/hook.php
 *
 * @return string
 */
function onyx_single_cat_template($t) {
	foreach(get_the_category() as $cat):
		if (file_exists(TEMPLATEPATH . "/single-cat-{$cat->slug}.php")) {
			return TEMPLATEPATH . "/single-cat-{$cat->slug}.php";
		}
		if($cat->parent) {
			$cat = get_category($cat->parent);
			if (file_exists(TEMPLATEPATH . "/single-cat-{$cat->slug}.php")) {
				return TEMPLATEPATH . "/single-cat-{$cat->slug}.php";
			}
		}
	endforeach;
	return $t;
}

/**
 * Action to add Onyx Theme Styles.
 * Registered at actions->add->wp_head config/hook.php
 *
 * @return void
 */
function onyx_load_styles() {
	$assets = O::conf('assets')->css;
	foreach ($assets as $css) {
		O::css($css[0], $css[1]);
	}
}

/**
 * Action to add Onyx Theme Javascripts
 * Registered at actions->add->wp_head config/hook.php
 *
 * @return void
 */
function onyx_load_javascripts() {
	$assets = O::conf('assets')->js;
	foreach ($assets as $js) {
		$js[2] = (!isset($js[2])) ?: $js[2];
		O::js($js[0], $js[1], $js[2]);
	}
}

/**
 * Action to inject gulp-livereload server for development,
 * only works with `.local` domains.
 * Registered at actions->add->wp_head config/hook.php
 *
 * @return void
 */
function onyx_load_livereload() {
	O::livereload();
}

/**
 * Action to configure WordPress send an email via SMTP.
 * Registered at actions->add->wp_head phpmailer_init/hook.php.
 *
 * @see config/mail.php
 * @return void
 */
function onyx_smtp_config($phpmailer) {
	$mail = O::conf('mail');
	$phpmailer->isSMTP();
	$phpmailer->From        = $mail->from;
	$phpmailer->FromName    = $mail->name;
	$phpmailer->Host        = $mail->host;
	$phpmailer->Port        = $mail->port;
	$phpmailer->SMTPSecure  = $mail->secure;
	$phpmailer->SMTPAuth    = $mail->auth;
	$phpmailer->Username    = $mail->user;
	$phpmailer->Password    = $mail->pass;
}

/**
 * Remove Private or Protected prefix from title.
 * Registered at filters->add->private_title_format|protected_title_format at config/hook.php
 * 
 * @deprecated
 * @return string
 */
function onyx_remove_private_title($title) {
	return "%s";
}


/**
 * Show excerpt by default
 * Not needed for Gutenberg.
 * Registered at filters->add->default_hidden_meta_boxes at config/hook.php
 * 
 * @deprecated
 * @return bool
 */
function onyx_show_hidden_excerpt($hidden, $screen) {
	if ('post' == $screen->base) {
		foreach($hidden as $key=>$value) {
			if ('postexcerpt' == $value) {
				unset($hidden[$key]);
				break;
			}
		}
	}
	return $hidden;
}

/**
 * Adjust a better wp-caption without <p>
 * and removing the additional 10px.
 * Registered at filters->add->img_caption_shortcode at config/hook.php
 * 
 * @deprecated
 * @return mixed
 */
function onyx_better_img_caption($output, $attr, $content) {
	// skip caption if in feed
	if (is_feed())
		return $output;
	// default argument settings
	$defaults = array(
		'id'			=> '',
		'align'		=> 'alignnone',
		'width'		=> '',
		'caption'	=> ''
	);
	// combine arguments with user input
	$attr = shortcode_atts($defaults, $attr);
	// if the width is less than 1, there is no caption, return the image normally.
	if (1 > $attr['width'] || empty($attr['caption']))
		return $content;
	// set the caption div attributes
	$attributes = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '');
	$attributes .= ' class="wp-caption ' . esc_attr($attr['align']) . '"';
	$attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';
	// open a caption div
	$output = '<div' . $attributes . '>';
	// allow shortcodes
	$output .= do_shortcode($content);
	// add caption text
	$output .= '<span class="wp-caption-text">' . $attr['caption'] . '</span>';
	// close the caption div
	$output .= '</div>';
	// return caption formatted correctly
	return $output;
}

?>
