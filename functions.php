<?php
// 1. Navigation / Menu 
// Usage: wp_nav_menu( array( 'theme_location' => 'top-menu', 'container' => false ) );
add_action( 'init', 'register_my_menus' );
function register_my_menus()
{
	register_nav_menus(
	array(
		'top-menu' => __( 'Header Navigation' ),
		'bottom-menu' => __( 'Footer Navigation' )
	)
	);
}


// 2. Remove default UL markup from wp_nav_menu output
function remove_ul ( $menu )
{
   return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
}
add_filter( 'wp_nav_menu', 'remove_ul' );


// 3. Enable featured image with custom dimension
add_theme_support( 'post-thumbnails');
if (function_exists('add_image_size'))
{
	add_image_size( 'blog_featured', 600, 300,true );
	// Add hard crop feature for medium size image. 
	add_image_size('medium', get_option( 'medium_size_w' ), get_option( 'medium_size_h' ), true );
}


// 4. Image compression while uploading
add_filter('jpeg_quality', 'jpeg_quality_callback');
function jpeg_quality_callback($arg)
{
	return (int)80; // 80% quality
}


// 5. Remove width and height attributes from inserted images
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );

function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}


// 6. Enable sidebar widget
/* Usage:
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')) :  ?>
  <?php endif; ?>
*/
if ( function_exists('register_sidebar') )
{
	register_sidebar(array(
	'name'=>'Sidebar',
	'id' => 'sidebar',
	'description' => 'Sidebar Widget',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
	));
}
add_post_type_support('page', 'excerpt');


// 7. Shorten post / page title by word limit
/* Usage: <?php echo short_title('...', 8); ?> Shorten title by 8 words */
function short_title($after = '', $length)
{
	$mytitle = explode(' ', get_the_title(), $length);
	if (count($mytitle)>=$length)
	{
		array_pop($mytitle);
		$mytitle = implode(" ",$mytitle). $after;
	}
	else
	{
		$mytitle = implode(" ",$mytitle);
	}
	return $mytitle;
}


// 8. Shorten excerpt by word limit
/* Usage: <?php echo excerpt(25); ?> in loop for 25 words limit*/
function excerpt($limit)
{
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	if (count($excerpt)>=$limit)
	{
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...';
	}
	else
	{
		$excerpt = implode(" ",$excerpt);
	}
	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	return $excerpt;
}


// 9. Shorten content by word limit
/* Usage: <?php echo content(25); ?> in loop for 25 words limit*/
function content($limit)
{
	$content = explode(' ', get_the_content(), $limit);
	if (count($content)>=$limit)
	{
		array_pop($content);
		$content = implode(" ",$content).'...';
	}
	else
	{
		$content = implode(" ",$content);
	}
	$content = preg_replace('/(<)([img])(\w+)([^>]*>)/','', $content);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}


// 10. Strip images from content
/* Usage: <?php remove_filter( 'the_content', 'remove_images' ); ?>*/
function remove_images( $content )
{
   $postOutput = preg_replace('/<img[^>]+./','', $content);
   return $postOutput;
}


// 11. Display the content of a page by page ID
/* Usage: <?php echo get_page_content_id(15);?> 15 is the id of the page */
function get_page_content_id($page_id)
{
	$page_data = get_page($page_id);
	$content = apply_filters('the_content', $page_data->post_content);
	return $content;
}


// 12. Display the content of a page by page slug
/* Usage: <?php echo get_page_content_slug(get_page_id("about-me"));?> 'about-me' is the sluf of the page */
function get_page_content_slug($page_slug)
{
	$page = get_page_by_path($page_slug);
	$page_data = get_page($page->ID);
	$content = apply_filters('the_content', $page_data->post_content);
	return $content;
}


// 13. Anti spam email shorcode inside content editor
/*Usage: [email]you@you.com[/email]*/
function email_encode_function( $atts, $content )
{
	return '<a href="'.antispambot("mailto:".$content).'">'.antispambot($content).'</a>';
}
add_shortcode( 'email', 'email_encode_function' );


// 14. Change default sender email address for generated emails
 function wpb_sender_email( $original_email_address ) {
    return 'name@yourdomain.com';
}
add_filter( 'wp_mail_from', 'wpb_sender_email' );


// 15. Change default sender name for generated emails
function wpb_sender_name( $original_email_from ) {
    return 'Your Name';
}
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );


// 16. Remove default widgets
// Usage: Uncomment the add_action function below
 function unregister_default_widgets() {
     unregister_widget('WP_Widget_Pages');
     unregister_widget('WP_Widget_Calendar');
     unregister_widget('WP_Widget_Archives');
     unregister_widget('WP_Widget_Links');
     unregister_widget('WP_Widget_Meta');
     unregister_widget('WP_Widget_Search');
     unregister_widget('WP_Widget_Text');
     unregister_widget('WP_Widget_Categories');
     unregister_widget('WP_Widget_Recent_Posts');
     unregister_widget('WP_Widget_Recent_Comments');
     unregister_widget('WP_Widget_RSS');
     unregister_widget('WP_Widget_Tag_Cloud');
     unregister_widget('WP_Nav_Menu_Widget');
 }
 //add_action('widgets_init', 'unregister_default_widgets', 11);


// 17. Remove WordPress version from Head
function remove_version_from_head()
{
	return '';
}
add_filter('the_generator', 'remove_version_from_head');


// 18. Remove login error message on login page
//add_filter('login_errors',create_function('$a', "return null;"));


// 19. Enqueue script in footer
function wp_custom_scripts_load()
{
	wp_register_script( 'custom-js', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), null, true );

	wp_enqueue_script( 'custom-js' );

}
if (!is_admin())
add_action( 'wp_enqueue_scripts', 'wp_custom_scripts_load' );


// 20. Enqueue CSS in header
function wp_custom_css_load()
{

	wp_register_style( 'maincss', get_template_directory_uri() . '/style.css', array(), '01012019', 'all' );

	wp_enqueue_style( 'maincss' );
}
if (!is_admin())
add_action( 'wp_enqueue_scripts', 'wp_custom_css_load' );


// 21. Disable admin bar for all users but admins in the front end
  show_admin_bar(false);

// 22. Function to change default sender email address
function wp_sender_email( $original_email_address ) {
    return 'info@yourdomain.com';
} 
add_filter( 'wp_mail_from', 'wp_sender_email' );

// 23. Function to change default email sender name
function wp_sender_name( $original_email_from ) {
    return 'Your Company Name';
}
add_filter( 'wp_mail_from_name', 'wp_sender_name' );
?>
