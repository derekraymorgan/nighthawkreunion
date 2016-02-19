<?php /*
 * Main loop related functions
 *
 * @package nirvana
 * @subpackage Functions
 */


 /**
 * Sets the post excerpt length to the number of words set in the theme settings
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Nirvana 1.0
 * @return int
 */
function nirvana_excerpt_length_words( $length ) {
	global $nirvana_excerptlength;
	return $nirvana_excerptlength;
}

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * @since nirvana 0.5
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function nirvana_custom_excerpt_more( $output ) {
	if (! is_attachment()) {$output .= nirvana_continue_reading_link();}
	return $output;
}

add_filter( 'excerpt_length', 'nirvana_excerpt_length_words' );
add_filter( 'get_the_excerpt', 'nirvana_custom_excerpt_more',10 );

/**
 * Sets the post excerpt length to the number of characters set in the theme settings
 *
 * @since nirvana 0.5
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function nirvana_excerpt_length_chars( $output ) {
	global $nirvana_excerptlength;
	global $nirvana_excerptdots;
	if ($output && !is_attachment()) { 
		$output = substr($output, 0, $nirvana_excerptlength). $nirvana_excerptdots . nirvana_continue_reading_link();
	}
	return $output;
}

if ($nirvanas['nirvana_excerpttype']=='Characters') {
	remove_filter( 'excerpt_length', 'nirvana_excerpt_length_words' );
	remove_filter( 'get_the_excerpt', 'nirvana_custom_excerpt_more',10 );
	add_filter( 'get_the_excerpt', 'nirvana_excerpt_length_chars' );
}
 
/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since nirvana 0.5
 * @return string "Continue Reading" link
 */
function nirvana_continue_reading_link() {
	global $nirvana_excerptcont;
	return '<p> <a class="continue-reading-link" href="'. get_permalink() . '"><span>' . $nirvana_excerptcont . '</span><i class="icon-right-dir"></i></a></p>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and nirvana_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since nirvana 0.5
 * @return string An ellipsis
 */
function nirvana_auto_excerpt_more( $more ) {
	global $nirvana_excerptdots;
	return $nirvana_excerptdots;
}
add_filter( 'excerpt_more', 'nirvana_auto_excerpt_more' );


/**
 * Adds a "Continue Reading" link to post excerpts created using the <!--more--> tag.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the the_content_more_link filter hook.
 *
 * @since nirvana 0.5
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function nirvana_more_link($more_link, $more_link_text) {
	global $nirvana_excerptcont;
	$new_link_text = $nirvana_excerptcont;
	if (preg_match("/custom=(.*)/",$more_link_text,$m) ) {
		$new_link_text = $m[1];
	}
	$more_link = str_replace($more_link_text, $new_link_text, $more_link);
	$more_link = str_replace('more-link', 'continue-reading-link', $more_link);
	return $more_link;
}
add_filter('the_content_more_link', 'nirvana_more_link',10,2);


/**
 * Allows post excerpts to contain HTML tags
 * @since nirvana 1.8.7
 * @return string Excerpt with most HTML tags intact
 */

function nirvana_trim_excerpt($text) {

	global $nirvana_excerptlength;
	global $nirvana_excerptcont;
	global $nirvana_excerptdots;
	$raw_excerpt = $text;
	//Retrieve the post content.
	$text = get_the_content('');
	//Delete all shortcode tags from the content.
	//$text = strip_shortcodes( $text );
	//$text = apply_filters('the_content', $text);
	// $text = str_replace(']]>', ']]&gt;', $text);

	$allowed_tags = '<a>,<img>,<b>,<strong>,<ul>,<li>,<i>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<pre>,<code>,<em>,<u>,<br>,<p>';
	
	$text = strip_tags($text, $allowed_tags);

	$words = preg_split("/[\n\r\t ]+/", $text, $nirvana_excerptlength + 1, PREG_SPLIT_NO_EMPTY);
	
	if ( count($words) > $nirvana_excerptlength ) {
		array_pop($words);
		$text = implode(' ', $words);
         } else {
		$text = implode(' ', $words);
		}
		
	return $text;
}

if ($nirvana_excerpttags=='Enable') {
	remove_filter( 'get_the_excerpt', 'nirvana_custom_excerpt_more',10 );
	remove_filter( 'get_the_excerpt', 'nirvana_excerpt_length_chars' );
	add_filter('get_the_excerpt', 'nirvana_trim_excerpt');
	add_filter('get_the_excerpt', 'do_shortcode');
}


/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Nirvana's style.css.
 *
 * @since nirvana 0.5
 * @return string The gallery style filter, with the styles themselves removed.
 */
function nirvana_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'nirvana_remove_gallery_css' );


function nirvana_author_on() {
	global $post;
	if (is_single() && get_the_author_meta('user_url',$post->post_author)) {
		echo '<link rel="author" href="' . get_the_author_meta('user_url',$post->post_author) . '">';
	}
}

add_action ('wp_head','nirvana_author_on');


if ( ! function_exists( 'nirvana_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since nirvana 0.5
 */
function nirvana_posted_on() {
	global $nirvanas;
	foreach ($nirvanas as $key => $value) {
		${"$key"} = $value; 
	}	
	 
	// If single page take appropiate settings
	if (is_single()) {
		$nirvana_blog_show = $nirvana_single_show;
	}

	// Post Author
	$output="";
	if ($nirvana_blog_show['author']) {
		$output .= sprintf( '<span class="author vcard" ><i class="icon-author icon-metas" title="'.__( 'Author ','nirvana'). '"></i> 
				<a class="url fn n" href="%1$s" title="%2$s">%3$s</a> <span class="bl_sep">&#8226;</span></span>',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				sprintf( esc_attr( __( 'View all posts by %s', 'nirvana' ) ), get_the_author() ),
				get_the_author()
		);
	}

	// Post date/time 
	if ($nirvana_blog_show['date'] || $nirvana_blog_show['time'] ) { 
		$separator="";$date="";$time ="";
		if ( $nirvana_blog_show['date'] && $nirvana_blog_show['time'] ) { 
			$separator = " - "; 
		}
		if ($nirvana_blog_show['date']) {
			$date = get_the_date(); 
		}
		if ($nirvana_blog_show['time']) {
			$time = esc_attr( get_the_time() );
		}
		$output .= '<span class="onDate date updated"><i class="icon-time icon-metas" title="'.__("Date", "nirvana").'"></i>
					<a href="' . get_permalink() . '" rel="bookmark">'.$date.$separator.$time.'</a></span>';
	}
	
	// Post categories
	if ($nirvana_blog_show['category'] &&  get_the_category_list()) { 
		$output .= '<span class="bl_categ"><i class="icon-folder-open icon-metas" title="'.__("Categories", "nirvana").'"></i>'
		. get_the_category_list( ', ' ) . '</span> ' ;
	}
	
	echo $output;
	
}; // nirvana_posted_on()
endif;


if ( ! function_exists( 'nirvana_posted_after' ) ) :
/**
 * Prints HTML with tags information for the current post. ALso adds the edit button.
 * @since nirvana 0.9
 */
function nirvana_posted_after() { 
	global $nirvanas;
	foreach ($nirvanas as $key => $value) { 
		${"$key"} = $value; 
	}	

	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list && ($nirvana_blog_show['tag']) ) : ?>
		<span class="footer-tags">
			<i class="icon-tag icon-metas" title="<?php _e( 'Tags','nirvana'); echo '"> </i>'.$tag_list; ?> 
		</span>
	<?php endif;
	edit_post_link( __( 'Edit', 'nirvana' ), '<span class="edit-link icon-metas"><i class="icon-edit  icon-metas"></i> ', '</span>' );
	cryout_post_footer_hook(); 
	
}; // nirvana_posted_after()
endif;

function nirvana_meta_infos() {
	global $nirvanas;
    foreach ($nirvanas as $key => $value) {
		${"$key"} = $value;
	}	
	switch($nirvana_metapos):

		case "Bottom":
			add_action('cryout_post_after_content_hook','nirvana_posted_on',10);
			add_action('cryout_post_after_content_hook','nirvana_posted_after',11);
			add_action('cryout_post_after_content_hook','nirvana_comments_on',12);
		break;
	
		case "Top":
			if( !is_single()) { 
				add_action('cryout_post_meta_hook','nirvana_posted_on',10);
				add_action('cryout_post_meta_hook','nirvana_posted_after',11);
				add_action('cryout_post_meta_hook','nirvana_comments_on',12);
			}
		break;
	
		default:
		break;
	
	endswitch;
}
add_action('wp_head','nirvana_meta_infos');

// Remove category from rel in categry tags.
add_filter( 'the_category', 'nirvana_remove_category_tag' );
add_filter( 'get_the_category_list', 'nirvana_remove_category_tag' );


function nirvana_remove_category_tag( $text ) {
	$text = str_replace('rel="category tag"', 'rel="tag"', $text); 
	return $text;
}


if ( ! function_exists( 'nirvana_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 * @since nirvana 0.5
 */
function nirvana_posted_in() {
	global $nirvanas;
    foreach ($nirvanas as $key => $value) {
		${"$key"} = $value;
	}	
	
	if ($nirvana_single_show['tag'] || $nirvana_single_show['bookmark']) {
		// Retrieves tag list of current post, separated by commas.
		$posted_in="";
		$tag_list = get_the_tag_list( '', ', ' );
		if ( $tag_list && $nirvana_single_show['tag'] ) {
			$posted_in .=  '<span class="footer-tags"><i class="icon-tag icon-metas" title="' . __( 'Tagged','nirvana') . '"></i>&nbsp; %2$s.</span>';
		} 
		if ($nirvana_single_show['bookmark'] ) {
			$posted_in .= '<span class="bl_bookmark"><i class="icon-bookmark icon-metas" title="'.__(' Bookmark the permalink','nirvana').'"></i> <a href="%3$s" title="'.__('Permalink to','nirvana').' %4$s" rel="bookmark"> '.__('Bookmark','nirvana').'</a>.</span>';
		}

		// Prints the string, replacing the placeholders.
		printf(
			$posted_in,
			get_the_category_list( ', ' ),
			$tag_list,
			get_permalink(),
			the_title_attribute( 'echo=0' )
		);
	}
}; // nirvana_posted_in()
endif;

if ( ! function_exists( 'nirvana_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function nirvana_content_nav( $nav_id ) {
	global $wp_query;
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>" class="navigation">
			<div class="nav-previous">
				<?php next_posts_link( __( '<i class="meta-nav-prev"></i> <span>Older posts</span>', 'nirvana' ) ); ?>
			</div>
			<div class="nav-next">
				<?php previous_posts_link( __( '<span>Newer posts</span> <i class="meta-nav-next"></i>', 'nirvana' ) ); ?>
			</div>
		</nav><!-- #nav-above -->
	<?php endif;
}; // nirvana_content_nav()
endif; // nirvana_content_nav

// Custom image size for use with post thumbnails
if ($nirvana_fcrop) {
	add_image_size( 'custom', $nirvana_fwidth, $nirvana_fheight, true );
} else {
	add_image_size( 'custom', $nirvana_fwidth, $nirvana_fheight );
}

function cryout_echo_first_image ($postID) {
	$args = array(
		'numberposts' 	=> 1,
		'orderby'		=> 'none',
		'post_mime_type'=> 'image',
		'post_parent' 	=> $postID,
		'post_status'	=> 'any',
		'post_type'		=> 'any'
	);

	$attachments = get_children( $args );
	//print_r($attachments);

	if ($attachments) {
		foreach($attachments as $attachment) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'custom' )  ? 
								wp_get_attachment_image_src( $attachment->ID, 'custom' ) : 
								wp_get_attachment_image_src( $attachment->ID );
			return $image_attributes[0];
		}
	}
}; // echo_first_image()

if ( ! function_exists( 'nirvana_set_featured_thumb' ) ) :
/**
 * Adds a post thumbnail and if one doesn't exist the first image from the post is used.
 */
function nirvana_set_featured_thumb() {
	global $nirvanas;
	foreach ($nirvanas as $key => $value) {
		${"$key"} = $value;
	}
	global $post;

	$image_src = cryout_echo_first_image($post->ID);
	if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $nirvana_fpost=='Enable') {
		the_post_thumbnail( 'custom', array("class" => "align".strtolower($nirvana_falign)." post_thumbnail" ) );
	} else if ($nirvana_fpost=='Enable' && $nirvana_fauto=="Enable" && $image_src ) {
		echo '<a title="'.the_title_attribute('echo=0').'" href="'.get_permalink().'" >
			<img width="'.$nirvana_fwidth.'" title="" alt="" class="align'.strtolower($nirvana_falign) . ' post_thumbnail" src="' . $image_src.'"></a>' ;
	}
};
endif; // nirvana_set_featured_thumb

if ($nirvana_fpost=='Enable' && $nirvana_fpostlink) {
	add_filter( 'post_thumbnail_html', 'nirvana_thumbnail_link', 10, 3 );
}

/**
 * The thumbnail gets a link to the post's page
 */
function nirvana_thumbnail_link( $html, $post_id, $post_image_id ) {
     $html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_post_field( 'post_title', $post_id ) ) .
			'" title="' . esc_attr( get_post_field( 'post_title', $post_id ) ) . '">' . $html . '</a>';
     return $html;
}; // nirvana_thumbnail_link()

?>