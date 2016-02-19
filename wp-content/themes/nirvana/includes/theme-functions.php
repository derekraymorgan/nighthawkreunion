<?php
/**
 * Misc functions breadcrumbs / pagination / transient data /back to top button
 *
 * @package nirvana
 * @subpackage Functions
 */


 /**
 * Loads necessary scripts
 * Adds HTML5 tags for IE8
 * Used in header.php
*/
function nirvana_header_scripts() {
	$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
?>
<!--[if lt IE 9]>
<script>
document.createElement('header');
document.createElement('nav');
document.createElement('section');
document.createElement('article');
document.createElement('aside');
document.createElement('footer');
</script>
<![endif]-->
<script type="text/javascript">
function makeDoubleDelegate(function1, function2) {
    return function() { if (function1) function1(); if (function2) function2(); }
}
function nirvana_onload() {
<?php if ($nirvana_mobile=="Enable") { ?> jQuery(".entry-content").fitVids(); <?php } ?>
};
window.onload = makeDoubleDelegate(window.onload, nirvana_onload );
jQuery(document).ready(function(){
<?php if ($nirvana_mobile=="Enable") { ?> nirvana_mobilemenu_init(); <?php } ?>
});
</script>
<?php
} // nirvana_header_scripts()

add_action('wp_head','nirvana_header_scripts',100);


 /**
 * Adds title and description to heaer
 * Used in header.php
*/
function nirvana_header_image() {
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
	global $nirvana_totalSize;
	// Header styling and image loading
	// Check if this is a post or page, if it has a thumbnail, and if it's a big one
	global $post;

	if (get_header_image() != '') { $himgsrc = get_header_image(); }
	if ( is_singular() && has_post_thumbnail( $post->ID ) && $nirvana_fheader == "Enable" &&
		( ($nirvana_duality=='Boxed')? $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'header' ) : $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ) ) &&
		$image[1] >= $nirvana_totalSize ) : $himgsrc= $image[0];
	endif;

	if (isset($himgsrc) && ($himgsrc != '')) : echo '<img id="bg_image" alt="" title="" src="'.$himgsrc.'"  />';  endif;
}

add_action ('cryout_branding_hook','nirvana_header_image');

function nirvana_title_and_description() { 
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }

?><div id="header-container">
<?php

	switch ($nirvana_siteheader) {
		case 'Site Title and Description':
			echo '<div>';
			$heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';
			echo '<'.$heading_tag.' id="site-title">';
			echo '<span> <a href="'.esc_url( home_url( '/' ) ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">'.get_bloginfo( 'name' ).'</a> </span>';
			echo '</'.$heading_tag.'>';
			echo '<div id="site-description" >'.get_bloginfo( 'description' ).'</div></div>';
		break;

		case 'Clickable header image' :
			echo '<a href="'.esc_url( home_url( '/' ) ).'" id="linky"></a>' ;
		break;

		case 'Custom Logo' :
			if (isset($nirvana_logoupload) && ($nirvana_logoupload != '')) :
				echo '<div><a id="logo" href="'.esc_url( home_url( '/' ) ).'" ><img title="" alt="" src="'.$nirvana_logoupload.'" /></a></div>';
			endif;
		break;

		case 'Empty' :
		break;
	}
	echo '</div>';
} // nirvana_title_and_description()

add_action ('cryout_branding_hook','nirvana_title_and_description');


 /**
 * Add social icons in header / undermneu left / undermenu right / footer / left browser side / right browser side
 * Used in header.php and footer.php
*/
function nirvana_header_socials() {
	nirvana_set_social_icons('sheader');
}

function nirvana_smenul_socials() {
	nirvana_set_social_icons('smenul');
}

function nirvana_smenur_socials() {
	nirvana_set_social_icons('smenur');
}

function nirvana_footer_socials() {
	echo '<div id="sfooter-full">';
	nirvana_set_social_icons('sfooter');
	echo '</div>';
}

function nirvana_slefts_socials() {
	nirvana_set_social_icons('slefts');
}

function nirvana_srights_socials() {
	nirvana_set_social_icons('srights');
}

//Adding socials to the topbar
if($nirvana_socialsdisplay0) add_action('cryout_topbar_hook', 'nirvana_header_socials',13);
// Adding socials to the footer
if($nirvana_socialsdisplay3) add_action('cryout_footer_hook', 'nirvana_footer_socials',17);
// Adding socials to the left and right browser sides
if($nirvana_socialsdisplay4) add_action('cryout_wrapper_hook', 'nirvana_slefts_socials',13);
if($nirvana_socialsdisplay5) add_action('cryout_wrapper_hook', 'nirvana_srights_socials',13);


if ( ! function_exists( 'nirvana_set_social_icons' ) ) :
/**
 * Social icons function
 */
function nirvana_set_social_icons($idd) {
	$cryout_special_keys = array('Mail', 'Skype');
	global $nirvanas;
	foreach ($nirvanas as $key => $value) {
		${"$key"} = $value ;
	}
	echo '<div class="socials" id="'.$idd.'">';
	for ($i=1; $i<=9; $i+=2) {
		$j=$i+1;
		if ( ${"nirvana_social$j"} ) {
			if (in_array(${"nirvana_social$i"},$cryout_special_keys)) :
				$cryout_current_social = esc_html( ${"nirvana_social$j"} );
			else :
				$cryout_current_social = esc_url( ${"nirvana_social$j"} );
			endif;	?>

			<a <?php if ($nirvanas['nirvana_social_target'.$i]) {echo ' target="_blank" ';} ?> rel="nofollow" href="<?php echo $cryout_current_social; ?>"
			class="socialicons social-<?php echo esc_attr(${"nirvana_social$i"}); ?>" title="<?php echo ${"nirvana_social_title$i"} !="" ? esc_attr(${"nirvana_social_title$i"}) : esc_attr(${"nirvana_social$i"}); ?>">
				<img alt="<?php echo esc_attr(${"nirvana_social$i"}); ?>" src="<?php echo get_template_directory_uri().'/images/socials/'.${"nirvana_social$i"}.'.png'; ?>" />
			</a><?php
		}
	}
	echo '</div>';
} // nirvana_set_social_icons()
endif;


/**
 * Nirvana back to top button
 * Creates div for js
*/
function nirvana_back_top() {
	echo '<div id="toTop"><i class="icon-back2top"></i> </div>';
} // nirvana_back_top()

if ($nirvana_backtop=="Enable") add_action ('cryout_main_hook','nirvana_back_top');


 /**
 * Creates breadcrumns with page sublevels and category sublevels.
 */
function nirvana_breadcrumbs() {

$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
  $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $separator = '<i class="icon-angle-right"></i>'; // separator between crumbs
  $home = '<a href="'.home_url().'"><i class="icon-homebread"></i></a>'; // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb
  
  global $post;
  $homeLink = home_url();
  if (is_front_page() && $nirvana_frontpage=="Enable") {return;}
  if (is_home() && $nirvana_frontpage!="Enable") {
  
    if ($showOnHome == 1) echo '<div id="breadcrumbs"><div id="breadcrumbs-box"><a href="' . $homeLink . '"><i class="icon-homebread"></i>' .  __('Home Page','nirvana') . '</a></div></div>';
  
  } else {
  
    echo '<div id="breadcrumbs"><div id="breadcrumbs-box">' . $home . $separator . ' ';
  
    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $separator . ' ');
      echo $before . __('Archive by category','nirvana').' "' . single_cat_title('', false) . '"' . $after;
  
    } elseif ( is_search() ) {
      echo $before . __('Search results for','nirvana').' "' . get_search_query() . '"' . $after;
  
    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $separator . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $separator . ' ';
      echo $before . get_the_time('d') . $after;
  
    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $separator . ' ';
      echo $before . get_the_time('F') . $after;
  
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
  
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
        if ($showCurrent == 1) echo ' ' . $separator . ' ' . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); if (isset($cat[0])) {$cat = $cat[0];} else {$cat = false;}
        if ($cat) {$cats = get_category_parents($cat, TRUE, ' ' . $separator . ' ');} else {$cats=false;}
        if ($showCurrent == 0 && $cats) $cats = preg_replace("#^(.+)\s$separator\s$#", "$1", $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }
  
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
  
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); if (isset($cat[0])) {$cat = $cat[0];} else {$cat=false;}
      if ($cat) echo get_category_parents($cat, TRUE, ' ' . $separator . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
      if ($showCurrent == 1) echo ' ' . $separator . ' ' . $before . get_the_title() . $after;
  
    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
  
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo ' ' . $separator . ' ';
      }
      if ($showCurrent == 1) echo ' ' . $separator . ' ' . $before . get_the_title() . $after;
  
    } elseif ( is_tag() ) {
      echo $before . __('Posts tagged','nirvana').' "' . single_tag_title('', false) . '"' . $after;
  
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . __('Articles posted by','nirvana'). ' ' . $userdata->display_name . $after;
  
    } elseif ( is_404() ) {
      echo $before . __('Error 404','nirvana') . $after;
    }
  
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page','nirvana') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
  
    echo '</div></div>';
  
  }

}// breadcrumbs

if($nirvana_breadcrumbs=="Enable")  add_action ('cryout_breadcrumbs_hook','nirvana_breadcrumbs');

if ( !empty($nirvana_searchbar['top']) ) add_filter('wp_nav_menu_items','cryout_search_topmenu', 10, 2);
if ( !empty($nirvana_searchbar['main']) ) add_filter('wp_nav_menu_items','cryout_search_primarymenu', 10, 2);
if ( !empty($nirvana_searchbar['footer']) ) add_filter('wp_nav_menu_items','cryout_search_footermenu', 10, 2);

function cryout_search_topmenu( $items, $args ) { 
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }

	if( $args->theme_location == 'top') {
		$items = $items."<li class='menu-header-search'>
							<i class='search-icon'></i>
							<form action='".esc_url(home_url( '/' ))."' id='searchform' method='get'>
								<input type='text' name='s' id='s' placeholder='".__('Search','nirvana')."...'>
								<input type='submit' id='searchsubmit' value='&#xe816;' />
							</form>
						</li>";
	}	
   return $items;
}

function cryout_search_primarymenu( $items, $args ) { 
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }

	if( $args->theme_location == 'primary') {
		$items = $items."<li class='menu-main-search'>
							<form action='".esc_url(home_url( '/' ))."' id='searchform' method='get'>
								<input type='text' name='s' id='s' placeholder='".__('Search','nirvana')."...'>
								<input type='submit' id='searchsubmit' value='&#xe816;' />
							</form>
						</li>";
	}	
   return $items;
}

function cryout_search_footermenu( $items, $args ) { 
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }

	if( $args->theme_location == 'footer') {
		$items = $items."<li class='menu-footer-search'>
							<form action='".esc_url(home_url( '/' ))."' id='searchform' method='get'>
								<input type='text' name='s' id='s' placeholder='".__('Search','nirvana')."...'>
								<input type='submit' id='searchsubmit' value='&#xe816;' />
							</form>
						</li>";
	}	
   return $items;
}


if ( ! function_exists( 'nirvana_pagination' ) ) :
/**
 * Creates pagination for blog pages.
 */
function nirvana_pagination($pages = '', $range = 2, $prefix ='')
{
     $showitems = ($range * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }

     if(1 != $pages)
     {
		echo "<div class='pagination_container'><nav class='pagination'>";
         if ($prefix) {echo "<span id='paginationPrefix'>$prefix </span>";}
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</nav></div>\n";
     }
} // nirvana_pagination()
endif;

function nirvana_nextpage_links($defaults) {
$args = array(
'link_before'      => '<em>',
'link_after'       => '</em>',
);
$r = wp_parse_args($args, $defaults);
return $r;
}
add_filter('wp_link_pages_args','nirvana_nextpage_links');

/**
 * Site info
 */
function nirvana_site_info() {
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }	?>
	<em style="display:table;margin:0 auto;float:none;text-align:center;padding:7px 0;font-size:13px;">
	<?php _e('Powered by','nirvana')?> <a target="_blank" href="<?php echo 'http://www.cryoutcreations.eu';?>" title="<?php echo 'Nirvana Theme by '. 'Cryout Creations';?>"><?php echo 'Nirvana' ?></a> &amp;
	<a target="_blank" href="<?php echo esc_url('http://wordpress.org/' ); ?>" title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'nirvana'); ?>"> <?php printf(' %s.', 'WordPress' ); ?></a></em>
	<?php } // nirvana_site_info()
add_action('cryout_footer_hook','nirvana_site_info',15);


/**
 * Copyright text
 */
function nirvana_copyright() {
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
	echo '<div id="site-copyright">' . do_shortcode( $nirvana_copyright ) . '</div>';
} // nirvana_copyright()


if ($nirvana_copyright != '') add_action('cryout_footer_hook','nirvana_copyright',11);

add_action('wp_ajax_nopriv_do_ajax', 'nirvana_ajax_function');
add_action('wp_ajax_do_ajax', 'nirvana_ajax_function');

if ( ! function_exists( 'nirvana_ajax_function' ) ) :
function nirvana_ajax_function(){
	ob_clean();

   // the first part is a SWTICHBOARD that fires specific functions
   // according to the value of Query Var 'fn'

	switch($_REQUEST['fn']){
		case 'get_latest_posts':
			$output = nirvana_ajax_get_latest_posts($_REQUEST['count'],$_REQUEST['categName']);
		break;
		default:
			$output = 'No function specified, check your jQuery.ajax() call';
		break;
	}

	// at this point, $output contains some sort of valuable data!
	// Now, convert $output to JSON and echo it to the browser
	// That way, we can recapture it with jQuery and run our success function

	$output=json_encode($output);
	if(is_array($output)) { print_r($output); }
	                 else { echo $output; }
	die;
} // nirvana_ajax_function()
endif;

if ( ! function_exists( 'nirvana_ajax_get_latest_posts' ) ) :
function nirvana_ajax_get_latest_posts($count,$categName){
	$testVar='';
	// The Query
	$the_query = new WP_Query( 'category_name='.$categName);
	// The Loop
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$testVar .=the_title("<option>","</option>",0);
		endwhile;
	endif;
	return $testVar;
} // nirvana_ajax_get_latest_posts()
endif;

if ( ! function_exists( 'nirvana_get_sidebar' ) ) :
function nirvana_get_sidebar() {
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
	switch($nirvana_side) {

		case '2cSl':
			get_sidebar('left');
		break;

		case '2cSr':
			get_sidebar('right');
		break;

		case '3cSl' : case '3cSr' : case '3cSs' :
			get_sidebar('left');
			get_sidebar('right');
		break;

		default:
		break;
	}
} // nirvana_get_sidebar()
endif;

if ( ! function_exists( 'nirvana_get_layout_class' ) ) :
function nirvana_get_layout_class() {
	$nirvanas = nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value ; }
	switch($nirvana_side) {
		case '2cSl': return "two-columns-left"; break;
		case '2cSr': return "two-columns-right"; break;
		case '3cSl': return "three-columns-left"; break;
		case '3cSr' : return "three-columns-right"; break;
		case '3cSs' : return "three-columns-sided"; break;
		case '1c':
		default: return "one-column"; break;
	}
} // nirvana_get_layout_class()
endif;


/** 
* Retrieves the IDs for images in a gallery. 
* @since nirvana 0.9 
* @return array List of image IDs from the post gallery. 
*/ 
function nirvana_get_gallery_images() { 
       $images = array(); 

       if ( function_exists( 'get_post_galleries' ) ) { 
               $galleries = get_post_galleries( get_the_ID(), false ); 
               if ( isset( $galleries[0]['ids'] ) ) 
                       $images = explode( ',', $galleries[0]['ids'] ); 
       } else { 
               $pattern = get_shortcode_regex(); 
               preg_match( "/$pattern/s", get_the_content(), $match ); 
               $atts = shortcode_parse_atts( $match[3] ); 
               if ( isset( $atts['ids'] ) ) 
                       $images = explode( ',', $atts['ids'] ); 
       } 

       if ( ! $images ) { 
               $images = get_posts( array( 
                       'fields'         => 'ids', 
                       'numberposts'    => 999, 
                       'order'          => 'ASC', 
                       'orderby'        => 'none', 
                       'post_mime_type' => 'image', 
                       'post_parent'    => get_the_ID(), 
                       'post_type'      => 'attachment', 
               ) ); 
       } 

       return $images; 
} // nirvana_get_gallery_images()


/** 
* Checks the browser agent string for mobile ids and adds "mobile" class to body if true 
* @return array list of classes. 
*/ 
function nirvana_mobile_body_class($classes){ 
	$nirvanas = nirvana_get_theme_options(); 
	if ($nirvanas['nirvana_mobile']=="Enable"): 
		$browser = (isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:''); 
		$keys = 'mobile|android|mobi|tablet|ipad|opera mini|series 60|s60|blackberry'; 
		if (preg_match("/($keys)/i",$browser)): $classes[] = 'mobile'; endif; // mobile browser detected 
	endif; 
	return $classes; 
} 
 
add_filter('body_class', 'nirvana_mobile_body_class');

////////// HELPER FUNCTIONS //////////

function cryout_optset($var,$val1,$val2='',$val3='',$val4=''){
	$vals = array($val1,$val2,$val3,$val4);
	if (in_array($var,$vals)): return false; else: return true; endif;
} // cryout_optset()

function cryout_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);
   if (preg_match("/^([a-f0-9]{3}|[a-f0-9]{6})$/i",$hex)):
        if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        return implode(",", $rgb); // returns the rgb values separated by commas
   else: return "";  // input string is not a valid hex color code
   endif;
} // cryout_hex2rgb()


function cryout_hexadder($hex,$inc) {
   $hex = str_replace("#", "", $hex);
   if (preg_match("/^([a-f0-9]{3}|[a-f0-9]{6})$/i",$hex)):
        if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
        }
		
		$rgb_array = array($r,$g,$b);
		$newhex="#";
		foreach ($rgb_array as $el) {
			$el+=$inc;
			if ($el<=0) { $el='00'; } 
			elseif ($el>=255) {$el='ff';} 
			else {$el=dechex($el);}
			if(strlen($el)==1)  {$el='0'.$el;}
			$newhex.=$el;
		}
		return $newhex;
   else: return "";  // input string is not a valid hex color code
   endif;
} // cryout_hexadder()


function cryout_gfontclean( $gfont, $mode = 1 ) {
	switch ($mode) {
		case 2: // for custom styling
			return esc_attr(str_replace('+',' ',preg_replace('/[:&].*/','',$gfont)));
		break;
		case 1: // for font enqueuing
		default: 
			return esc_attr(preg_replace( '/\s+/', '+',$gfont)); 
		break;
	} // switch
} // cryout_gfontcleanup()

?>