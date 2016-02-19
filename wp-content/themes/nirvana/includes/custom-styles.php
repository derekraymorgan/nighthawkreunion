<?php
////////// MASTER CUSTOM STYLE FUNCTION //////////

function nirvana_body_classes($classes) {
	$nirvanas= nirvana_get_theme_options();
	$classes[] = $nirvanas['nirvana_image_style'];
	$classes[] = $nirvanas['nirvana_caption'];

	if ($nirvanas['nirvana_magazinelayout'] == "Enable" || (is_front_page() && $nirvanas['nirvana_frontpage'] == "Enable" && $nirvanas['nirvana_frontpostsperrow'] == '2') ) { $classes[] = 'magazine-layout'; }
	if (is_front_page() && $nirvanas['nirvana_frontpage'] == "Enable") {$classes[] = 'presentation-page';$classes[]= 'coldisplay'.$nirvanas['nirvana_coldisplay']; }
	return $classes;
}
add_filter('body_class','nirvana_body_classes');

/* =GENERAL CUSTOM STYLES
-------------------------------------*/
function nirvana_custom_styles() {
	$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value; }
	$totalwidth= $nirvana_sidewidth+$nirvana_sidebar;
	$contentSize = $nirvana_sidewidth;
	$sidebarSize= $nirvana_sidebar;
	ob_start();

?>
<style type="text/css">
<?php
////////// LAYOUT DIMENSIONS. //////////

/* WIDE LAYOUT */ ?>
#header-container { width: <?php echo esc_html(($totalwidth)); ?>px;}
#header-container, #access >.menu, #forbottom, #colophon, #footer-widget-area, #topbar-inner, .ppbox, #pp-afterslider #container, #breadcrumbs-box { max-width: <?php echo esc_html(($totalwidth)); ?>px; }

<?php
/* BOXED LAYOUT */
if ($nirvana_duality == 'Boxed') { ?>
#header-full, #breadcrumbs, #main { max-width: <?php echo esc_html($totalwidth); ?>px; margin:0 auto; }
#access > .menu > ul { margin-left:0; } 
#pp-texttop, #pp-textmiddle, #pp-textbottom, #front-columns h2, .presentation-page #content {
		padding-left:20px !important; padding-right: 20px !important; } 
<?php };

/* RESPONSIVENESS DISABLED */
if ($nirvana_mobile == 'Disable') { ?>
#topbar, #header-full, #main, #forbottom, #access, #breadcrumbs{ min-width: <?php echo esc_html(($totalwidth)); ?>px; }
#access > .menu > ul {margin-left:0;}
#forbottom, #pp-texttop, #pp-textmiddle, #pp-textbottom, #front-columns h2, .presentation-page #content {
					padding-left: 20px !important; padding-right: 20px !important;box-sizing:border-box;-webkit-box-sizing:border-box; };
<?php } else { // Header widgets responsiveness if no header image is selected
if (get_header_image() == '') {?>@media (max-width: 800px) { #header-container {position: relative !important;} #branding {height: auto !important;}}<?php }
}


////////// COLUMNS //////////

$colPadding = 30;
$contentSize = $contentSize - 60;

?>
#container.one-column { }
#container.two-columns-right #secondary { width:<?php echo esc_html($sidebarSize); ?>px; float:right; }
#container.two-columns-right #content { width:<?php echo esc_html($contentSize-$colPadding); ?>px; float:left; } /*fallback*/
#container.two-columns-right #content { width:calc(100% - <?php echo esc_html($sidebarSize+$colPadding); ?>px); float:left; }
#container.two-columns-left #primary { width:<?php echo esc_html($sidebarSize); ?>px; float:left; }
#container.two-columns-left #content { width:<?php echo esc_html($contentSize-$colPadding); ?>px; float:right; } /*fallback*/
#container.two-columns-left #content { 	width:-moz-calc(100% - <?php echo esc_html($sidebarSize+$colPadding); ?>px); float:right;
										width:-webkit-calc(100% - <?php echo esc_html($sidebarSize+$colPadding); ?>px);
										width:calc(100% - <?php echo esc_html($sidebarSize+$colPadding); ?>px); }

#container.three-columns-right .sidey { width:<?php echo esc_html($sidebarSize/2); ?>px; float:left; }
#container.three-columns-right #primary { margin-left:<?php echo esc_html($colPadding); ?>px; margin-right:<?php echo esc_html($colPadding); ?>px; }
#container.three-columns-right #content { width:<?php echo esc_html($contentSize-$colPadding*2); ?>px; float:left; } /*fallback*/
#container.three-columns-right #content { 	width:-moz-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); float:left;
											width:-webkit-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px);
											width:calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px);}

#container.three-columns-left .sidey { width:<?php echo esc_html($sidebarSize/2); ?>px; float:left; }
#container.three-columns-left #secondary {margin-left:<?php echo esc_html($colPadding); ?>px; margin-right:<?php echo esc_html($colPadding); ?>px; }
#container.three-columns-left #content { width:<?php echo esc_html($contentSize-$colPadding*2); ?>px; float:right;} /*fallback*/
#container.three-columns-left #content { width:-moz-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); float:right;
										 width:-webkit-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px);
										 width:calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); }

#container.three-columns-sided .sidey { width:<?php echo esc_html($sidebarSize/2); ?>px; float:left; }
#container.three-columns-sided #secondary { float:right; }
#container.three-columns-sided #content { width:<?php echo esc_html($contentSize-$colPadding*2); ?>px; float:right; /*fallback*/
										  width:-moz-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); float:right;
										  width:-webkit-calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); float:right;
										  width:calc(100% - <?php echo esc_html($sidebarSize+$colPadding*2); ?>px); float:right;
		                                  margin: 0 <?php echo esc_html(($sidebarSize/2)+$colPadding);?>px 0 <?php echo esc_html(-($contentSize+$sidebarSize)); ?>px; }

<?php
////////// FONTS //////////
$nirvana_googlefont = cryout_gfontclean( esc_html($nirvana_googlefont), 2 );
$nirvana_googlefonttitle = cryout_gfontclean( esc_html($nirvana_googlefonttitle), 2 );
$nirvana_googlefontside = cryout_gfontclean( esc_html($nirvana_googlefontside), 2 );
$nirvana_googlefontwidget = cryout_gfontclean( esc_html($nirvana_googlefontwidget), 2 );
$nirvana_headingsgooglefont = cryout_gfontclean( esc_html($nirvana_headingsgooglefont), 2 );
$nirvana_sitetitlegooglefont = cryout_gfontclean( esc_html($nirvana_sitetitlegooglefont), 2 );
$nirvana_menugooglefont = cryout_gfontclean( esc_html($nirvana_menugooglefont), 2 );
?>
body { font-family: <?php echo ((!$nirvana_googlefont)?$nirvana_fontfamily:"'$nirvana_googlefont'"); ?>; }
#content h1.entry-title a, #content h2.entry-title a, #content h1.entry-title , #content h2.entry-title {
		font-family: <?php echo ((!$nirvana_googlefonttitle)?(($nirvana_fonttitle == 'General Font')?'inherit':$nirvana_fonttitle):"'$nirvana_googlefonttitle'"); ?>; }
.widget-title, .widget-title a { line-height: normal;
		font-family: <?php echo ((!$nirvana_googlefontside)?(($nirvana_fontside == 'General Font')?'inherit':$nirvana_fontside):"'$nirvana_googlefontside'");  ?>;  }
.widget-container, .widget-container a {
		font-family: <?php echo ((!$nirvana_googlefontwidget)?(($nirvana_fontwidget == 'General Font')?'inherit':$nirvana_fontwidget):"'$nirvana_googlefontwidget'");  ?>;  }
.entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6, #comments #reply-title,
.nivo-caption h2, #front-text1 h1, #front-text2 h1, h3.column-header-image  {
		font-family: <?php echo ((!$nirvana_headingsgooglefont)?(($nirvana_headingsfont == 'General Font')?'inherit':$nirvana_headingsfont):"'$nirvana_headingsgooglefont'");  ?>; }
#site-title span a {
		font-family: <?php echo ((!$nirvana_sitetitlegooglefont)?(($nirvana_sitetitlefont == 'General Font')?'inherit':$nirvana_sitetitlefont):"'$nirvana_sitetitlegooglefont'");  ?>; }
#access ul li a, #access ul li a span {
		font-family: <?php echo ((!$nirvana_menugooglefont)?(($nirvana_menufont == 'General Font')?'inherit':$nirvana_menufont):"'$nirvana_menugooglefont'");  ?>; }

<?php
////////// COLORS //////////
?>
body { color: <?php echo esc_html($nirvana_contentcolortxt); ?>; background-color: <?php echo esc_html($nirvana_backcolormain); ?> }
a { color: <?php echo esc_html($nirvana_linkcolortext); ?>; }
a:hover,.entry-meta span a:hover, .comments-link a:hover, body.coldisplay2 #front-columns a:active { color: <?php echo esc_html($nirvana_linkcolorhover); ?>; }
a:active {background-color:<?php echo esc_html($nirvana_accentcolorb);?>; color:<?php echo esc_html($nirvana_contentcolorbg);?>; }
.entry-meta a:hover, .widget-container a:hover, .footer2 a:hover {
border-bottom-color: <?php echo esc_html($nirvana_accentcolord);?>;
}
.sticky h2.entry-title a {background-color:<?php echo esc_html($nirvana_accentcolora);?>; color:<?php echo esc_html($nirvana_contentcolorbg);?>;}
#header { background-color: <?php echo esc_html($nirvana_backcolorheader); ?>; }
#site-title span a { color:<?php echo esc_html($nirvana_titlecolor); ?>; }
#site-description { color:<?php echo esc_html($nirvana_descriptioncolor); ?>; <?php if(cryout_hex2rgb($nirvana_descriptionbg)): ?>background-color: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_descriptionbg)); ?>,0.3); padding-left: 6px; <?php endif; ?>}

.socials a:hover .socials-hover  { background-color: <?php echo esc_html($nirvana_socialcolorbghover); ?>; }
.socials-hover { background-color: <?php echo esc_html($nirvana_socialcolorbg); ?>; }
/* Main menu top level */
#access a, #nav-toggle span { color: <?php echo esc_html($nirvana_menucolortxtdefault); ?>; }
#access, #nav-toggle, #access ul li {background-color: <?php echo esc_html($nirvana_menucolorbgdefault); ?>; }
#access > .menu > ul > li > a > span {  }
#access ul li:hover {background-color: <?php echo esc_html($nirvana_submenucolorbgdefault); ?>; color:<?php echo esc_html($nirvana_submenucolortxtdefault); ?>; }
#access ul > li.current_page_item , #access ul > li.current-menu-item ,
#access ul > li.current_page_ancestor , #access ul > li.current-menu-ancestor  {
       background-color: <?php echo esc_html(cryout_hexadder($nirvana_menucolorbgdefault,'13'));?>; }
/* Main menu Submenus */
#access ul ul li, #access ul ul {
	background-color:<?php echo esc_html($nirvana_submenucolorbgdefault); ?>;
}
#access ul ul li a {color:<?php echo esc_html($nirvana_submenucolortxtdefault); ?>}
#access ul ul li:hover {background:<?php echo esc_html(cryout_hexadder($nirvana_submenucolorbgdefault,'14'));?>}
#breadcrumbs {background:<?php echo esc_html(cryout_hexadder($nirvana_backcolormain,'-10'));?>}
#access ul ul li.current_page_item, #access ul ul li.current-menu-item,
#access ul ul li.current_page_ancestor , #access ul ul li.current-menu-ancestor  {
background-color:<?php echo esc_html(cryout_hexadder($nirvana_submenucolorbgdefault,'14'));?>; }
<?php if (cryout_hex2rgb($nirvana_submenucolorshadow)): ?>#access ul ul { box-shadow: 3px 3px 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_submenucolorshadow)); ?>,0.3)); }<?php endif; ?>

#topbar {
<?php if ($nirvana_topbar == 'Hide'){ ?> display:none; <?php }
else { ?>
	background-color:  <?php echo esc_html($nirvana_topbarcolorbg); ?>;border-bottom-color:<?php echo esc_html(cryout_hexadder($nirvana_topbarcolorbg,'-20'));?>;
	<?php if ($nirvana_topbar == 'Fixed'): ?>
		position:fixed;top:0;z-index:300;opacity:0.8;
	<?php endif;
}?>
}
.menu-header-search #searchform {background: <?php echo esc_html($nirvana_accentcolore); ?>;}
<?php if ($nirvana_topbar == 'Fixed') {?> #header-full {margin-top:38px;} <?php } ?>
<?php if ($nirvana_topbarwidth == 'Full width'){ ?> #topbar-inner {max-width:95%;} <?php } ?>
.topmenu ul li a, .search-icon:before { color: <?php echo esc_html($nirvana_topmenucolortxt); ?>; }
.topmenu ul li a:hover { color: <?php echo esc_html($nirvana_topmenucolortxthover); ?>; background-color: <?php echo esc_html($nirvana_accentcolora); ?>; }
.search-icon:hover:before  { color: <?php echo esc_html($nirvana_accentcolora); ?>; }

#main { background-color: <?php echo esc_html($nirvana_contentcolorbg); ?>; }
#author-info, #entry-author-info, .page-title { border-color: <?php echo esc_html($nirvana_accentcolord); ?>; }
.page-title-text {border-color: <?php echo esc_html($nirvana_accentcolorb); ?>; }
.page-title span {border-color: <?php echo esc_html($nirvana_accentcolora); ?>; }
#entry-author-info #author-avatar, #author-info #author-avatar { border-color: <?php echo esc_html($nirvana_accentcolorc); ?>; }
.avatar-container:before {background-color:<?php echo esc_html($nirvana_accentcolorb);?>;}

.sidey .widget-container { color: <?php echo esc_html($nirvana_sidetxt); ?>; background-color: <?php echo esc_html($nirvana_sidebg); ?>; }
.sidey .widget-title { color: <?php echo esc_html($nirvana_sidetitletxt); ?>; background-color: <?php echo esc_html($nirvana_sidetitlebg); ?>;border-color:<?php echo esc_html($nirvana_accentcolord);?>;}
.sidey .widget-container a {color:<?php echo esc_html($nirvana_linkcolorside);?>;}
.sidey .widget-container a:hover {color:<?php echo esc_html($nirvana_linkcolorsidehover);?>;}

.widget-title span {border-color:<?php echo esc_html($nirvana_accentcolorb);?>;}

.entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6 {
     color: <?php echo esc_html($nirvana_contentcolortxtheadings); ?>; }
.entry-title, .entry-title a { color: <?php echo esc_html($nirvana_contentcolortxttitle); ?>; }
.entry-title a:hover { color: <?php echo esc_html($nirvana_contentcolortxttitlehover); ?>; }
#content span.entry-format { color: <?php echo esc_html($nirvana_accentcolord); ?>; }

#footer { color: <?php echo esc_html($nirvana_footercolortxt); ?>; background-color: <?php echo esc_html($nirvana_backcolorfooterw); ?>; }
#footer2 { color: <?php echo esc_html($nirvana_footercolortxt); ?>; background-color: <?php echo esc_html($nirvana_backcolorfooter); ?>;  }
#sfooter-full { background-color: <?php echo esc_html(cryout_hexadder($nirvana_backcolorfooter,'-5'));?>;  }
.footermenu ul li { border-color: <?php echo esc_html(cryout_hexadder($nirvana_backcolorfooter,'15'));?>;  }
.footermenu ul li:hover { border-color: <?php echo esc_html(cryout_hexadder($nirvana_backcolorfooter,'35'));?>;  }
#footer a { color: <?php echo esc_html($nirvana_linkcolorwooter); ?>; }
#footer a:hover { color: <?php echo esc_html($nirvana_linkcolorwooterhover); ?>; }
#footer2 a, .footermenu ul li:after  { color: <?php echo esc_html($nirvana_linkcolorfooter); ?>; }
#footer2 a:hover { color: <?php echo esc_html($nirvana_linkcolorfooterhover); ?>; }
#footer .widget-container { color: <?php echo esc_html($nirvana_widgettxt); ?>; background-color: <?php echo esc_html($nirvana_widgetbg); ?>; }
#footer .widget-title { color: <?php echo esc_html($nirvana_widgettitletxt); ?>; background-color: <?php echo esc_html($nirvana_widgettitlebg); ?>;border-color:<?php echo esc_html($nirvana_accentcolord);?>;}

a.continue-reading-link { color:<?php echo esc_html($nirvana_linkcolortext); ?>; border-color:<?php echo esc_html($nirvana_linkcolortext); ?>; }
a.continue-reading-link:hover { background-color:<?php echo esc_html($nirvana_accentcolora); ?> !important; color:<?php echo esc_html($nirvana_backcolormain); ?> !important; }
#cryout_ajax_more_trigger {border:1px solid <?php echo esc_html($nirvana_accentcolord); ?>; }
#cryout_ajax_more_trigger:hover {background-color:<?php echo esc_html($nirvana_accentcolore); ?>;}
a.continue-reading-link i.icon-right-dir {color:<?php echo esc_html($nirvana_accentcolora); ?>}
a.continue-reading-link:hover i.icon-right-dir {color:<?php echo esc_html($nirvana_backcolormain); ?>}
.page-link a, .page-link > span > em {border-color:<?php echo esc_html($nirvana_accentcolord);?>}

.columnmore a {background:<?php echo esc_html($nirvana_accentcolorb);?>;color:<?php echo esc_html($nirvana_accentcolore); ?>}
.columnmore a:hover {background:<?php echo esc_html($nirvana_accentcolora);?>;}

.file, .button, #respond .form-submit input#submit, input[type=submit], input[type=reset] {
	background-color: <?php echo esc_html($nirvana_contentcolorbg); ?>;
	border-color: <?php echo esc_html($nirvana_accentcolord); ?>;
    box-shadow: 0 -10px 10px 0 <?php echo esc_html($nirvana_accentcolore); ?> inset; }
.file:hover, .button:hover, #respond .form-submit input#submit:hover {
	background-color: <?php echo esc_html($nirvana_accentcolore); ?>; }
.entry-content tr th, .entry-content thead th {
	color: <?php echo esc_html($nirvana_contentcolortxtheadings); ?>; }
 #content tr th { background-color: <?php echo esc_html($nirvana_accentcolora); ?>;color:<?php echo esc_html($nirvana_contentcolorbg);?>; }
 #content tr.even { background-color: <?php echo esc_html($nirvana_accentcolore); ?>; }
hr { background-color: <?php echo esc_html($nirvana_accentcolord); ?>; }
input[type="text"], input[type="password"], input[type="email"], input[type="file"], textarea, select,
input[type="color"],input[type="date"],input[type="datetime"],input[type="datetime-local"],input[type="month"],input[type="number"],input[type="range"],
input[type="search"],input[type="tel"],input[type="time"],input[type="url"],input[type="week"] {
	/*background-color: <?php echo esc_html($nirvana_accentcolore); ?>;*/
    border-color: <?php echo esc_html($nirvana_accentcolord); ?> <?php echo esc_html($nirvana_accentcolorc); ?> <?php echo esc_html($nirvana_accentcolorc); ?> <?php echo esc_html($nirvana_accentcolord); ?>;
	color: <?php echo esc_html($nirvana_contentcolortxt); ?>; }
input[type="submit"], input[type="reset"] {
	color: <?php echo esc_html($nirvana_contentcolortxt); ?>;
	background-color: <?php echo esc_html($nirvana_contentcolorbg); ?>;
	border-color: <?php echo esc_html($nirvana_accentcolord); ?>;
	box-shadow: 0 -10px 10px 0 <?php echo esc_html($nirvana_accentcolore); ?> inset; }
input[type="text"]:hover, input[type="password"]:hover, input[type="email"]:hover, textarea:hover,
input[type="color"]:hover, input[type="date"]:hover, input[type="datetime"]:hover, input[type="datetime-local"]:hover, input[type="month"]:hover, input[type="number"]:hover, input[type="range"]:hover,
input[type="search"]:hover, input[type="tel"]:hover, input[type="time"]:hover, input[type="url"]:hover, input[type="week"]:hover {
	<?php if(cryout_hex2rgb($nirvana_accentcolore)): ?>background-color: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_accentcolore)); ?>,0.4); <?php endif; ?> }
.entry-content code {background-color:<?php echo esc_html($nirvana_accentcolore);?>; border-color: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_accentcolora)); ?>,0.1);}
.entry-content pre { border-color: <?php echo esc_html($nirvana_accentcolord); ?>;}
abbr, acronym { border-color: <?php echo esc_html($nirvana_contentcolortxt); ?>; }
.comment-meta a { color: <?php echo esc_html($nirvana_contentcolortxtlight); ?>; }
#respond .form-allowed-tags { color: <?php echo esc_html($nirvana_contentcolortxtlight); ?>; }
.reply a{ border-color: <?php echo esc_html($nirvana_accentcolorc) ?>; }
.reply a:hover {color: <?php echo esc_html($nirvana_linkcolortext); ?>; }

.entry-meta {border-color:<?php echo esc_html($nirvana_accentcolorc);?>;}
.entry-meta .icon-metas:before {color:<?php echo esc_html($nirvana_metacoloricons); ?>;}
.entry-meta span a, .comments-link a {color:<?php echo esc_html($nirvana_metacolorlinks); ?>;}
.entry-meta span a:hover, .comments-link a:hover {color:<?php echo esc_html($nirvana_metacolorlinkshover); ?>;}
.entry-meta span, .entry-utility span, .footer-tags {color:<?php echo esc_html($nirvana_contentcolortxtlight);?>;}

.nav-next a:hover, .nav-previous a:hover {background:<?php echo esc_html($nirvana_linkcolortext);?>;color:<?php echo esc_html($nirvana_contentcolorbg); ?>;}

.pagination { border-color:<?php echo esc_html(cryout_hexadder($nirvana_accentcolore,'-10'));?>;}
.pagination a:hover { background: <?php echo esc_html($nirvana_accentcolorb); ?>;color: <?php echo esc_html($nirvana_contentcolorbg); ?> ;}

h3#comments-title {border-color:<?php echo esc_html($nirvana_accentcolord); ?>}
h3#comments-title span {background: <?php echo esc_html($nirvana_accentcolora); ?>;color: <?php echo esc_html($nirvana_contentcolorbg); ?> ;}
.comment-details {border-color:<?php echo esc_html($nirvana_accentcolorc); ?>}

#searchform input[type="text"] {color:<?php echo esc_html($nirvana_contentcolortxtlight); ?>;}
#searchform:after {background-color:<?php echo esc_html($nirvana_accentcolorb);?>;}
#searchform:hover:after {background-color:<?php echo esc_html($nirvana_accentcolora);?>;}
#searchsubmit {color:<?php echo esc_html($nirvana_accentcolore);?>}
li.menu-main-search #searchform #s {background-color:<?php echo esc_html($nirvana_backcolormain);?>;}
li.menu-main-search #searchsubmit {color:<?php echo esc_html($nirvana_contentcolortxtlight); ?>;}
.caption-accented .wp-caption {<?php if(cryout_hex2rgb($nirvana_accentcolora)):?> background-color:rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_accentcolora));?>,0.8)); <?php endif; ?>
	color:<?php echo esc_html($nirvana_contentcolorbg);?>}

.nirvana-image-one .entry-content img[class*='align'],.nirvana-image-one .entry-summary img[class*='align'],
.nirvana-image-two .entry-content img[class*='align'],.nirvana-image-two .entry-summary img[class*='align'] {
	border-color:<?php echo esc_html($nirvana_accentcolora); ?>;}
<?php
////////// LAYOUT //////////
?>
#content p, #content ul, #content ol, #content, .ppbox { text-align:<?php echo esc_html($nirvana_textalign);  ?> ; }
#content p, #content ul, #content ol, .widget-container, .widget-container a, table, table td, .ppbox , .navigation, #content dt, #content dd, #content {
                                font-size:<?php echo esc_html($nirvana_fontsize); ?>;line-height:<?php echo esc_html($nirvana_lineheight); ?>;
								word-spacing:<?php echo esc_html($nirvana_wordspace); ?>; letter-spacing:<?php echo esc_html($nirvana_letterspace); ?>; }

<?php if ($nirvana_uppercasetext): ?> #site-title a, #site-description, #access a span, .topmenu ul li a, .footermenu a, .entry-meta span a, .entry-utility span a, #content h3.entry-format,
span.edit-link, h3#comments-title, h3#reply-title, .comment-author cite, .reply a, .widget-title, #site-info a, .nivo-caption h2, a.continue-reading-link,
.column-image h3, #front-columns h3.column-header-noimage, .tinynav , .entry-title, #breadcrumbs, .page-link{ text-transform: <?php echo ($nirvana_uppercasetext==1? 'uppercase' :'none');?>; }<?php endif; ?>
<?php if ($nirvana_hcenter): ?> #bg_image {display:block;margin:0 auto;} <?php endif; ?>
#content h1.entry-title, #content h2.entry-title { font-size:<?php echo esc_html($nirvana_headfontsize); ?> ;}
.widget-title, .widget-title a { font-size:<?php echo esc_html($nirvana_sidefontsize); ?> ;}
.widget-container, .widget-container a { font-size:<?php echo esc_html($nirvana_widgetfontsize); ?> ;}
<?php $font_root = 36;
for($i=1;$i<=6;$i++):
	echo "#content .entry-content h$i, #pp-afterslider h$i { font-size: ";
	echo esc_html(round(($font_root-(4*$i))*(preg_replace("/[^\d]/","",$nirvana_headingsfontsize)/100),0));
	echo "px;} ";
endfor; ?>
#site-title span a { font-size:<?php echo esc_html($nirvana_sitetitlesize); ?> ;}
#access ul li a { font-size:<?php echo esc_html($nirvana_menufontsize); ?> ;}
#access ul ul ul a {font-size:<?php echo esc_html((absint($nirvana_menufontsize)-2)); ?>px;}
<?php /*if ($nirvana_postseparator == "Show") { ?> article.post, article.page { padding-bottom: 10px; border-bottom: 3px solid #EEE; } <?php }*/ ?>
<?php if ($nirvana_comtext == "Hide") { ?> #respond .form-allowed-tags { display:none;} <?php } ?>
<?php switch ($nirvana_comclosed) {
	case "Hide in posts": ?> .nocomments { display:none;} <?php break;
	case "Hide in pages": ?> .nocomments2 {display:none;} <?php break;
	case "Hide everywhere": ?> .nocomments, .nocomments2 {display:none;} <?php break;
};//switch ?>
<?php if ($nirvana_comoff == "Hide") { ?> .comments-link span { display:none;} <?php } ?>
<?php if ($nirvana_tables == "Enable") { ?>
		#content table, #content tr, #content tr th, #content thead th, #content tr td, #content tr.even {background:none;border:none;color:inherit;}
<?php } ?>
<?php if ($nirvana_headingsindent == "Enable") { ?>
		#content h1, #content h2, #content h3, #content h4, #content h5, #content h6 { margin-left:20px;}
		.sticky hgroup { padding-left: 15px;}
<?php } ?>
#header-container > div { margin:<?php echo esc_html($nirvana_headermargintop); ?>px 0 0 <?php echo esc_html($nirvana_headermarginleft); ?>px;}
<?php if ($nirvana_pagetitle == "Hide") { ?> .page h1.entry-title, .home .page h2.entry-title { display:none; } <?php } ?>
<?php if ($nirvana_categtitle == "Hide") { ?> header.page-header, .archive h1.page-title { display:none; }  <?php } ?>
#content p, #content ul, #content ol, #content dd, #content pre, #content hr { margin-bottom: <?php echo esc_html($nirvana_paragraphspace);?>; }
<?php if ($nirvana_parindent != "0px") { ?> #content p { text-indent:<?php echo esc_html($nirvana_parindent);?>;} <?php } ?>

<?php if ($nirvana_metapos == 'Top') { ?> article footer.entry-meta {display:none;} <?php } ?>
<?php if ($nirvana_metapos == 'Bottom') { ?> article .entry-header .entry-meta {display:none;} <?php } ?>

<?php switch ($nirvana_menualign):
		case "center": ?> #access > .menu { display: table; margin: 0 auto; float: none; } 
						  #access > .menu > ul { text-align: center; } 
						  #access > .menu > ul > li { display: inline-block; float: initial; }
						  #access > .menu > ul > * { text-align: initial; }	<?php
		break;
		case "right": ?> #access  ul.menu { float: right; }
						 #nav-toggle { text-align: right; }	<?php
		break;
		case "rightmulti": ?> #access ul li { float: right; }
						 #nav-toggle { text-align: right; }
		<?php break;
		default: ?>
				#nav-toggle { text-align: left; } <?php
		break;
	  endswitch; ?>

#toTop:hover .icon-back2top:before {color:<?php echo esc_html($nirvana_accentcolorb);?>;}

#main {margin-top:<?php echo esc_html($nirvana_contentmargintop);?>px; }
#forbottom {padding-left: <?php echo esc_html($nirvana_contentpadding);?>px; padding-right: <?php echo esc_html($nirvana_contentpadding);?>px;}
#header-widget-area { width: <?php echo esc_html($nirvana_headerwidgetwidth); ?>; }
<?php
////////// HEADER IMAGE //////////
?>
#branding { height:<?php echo $nirvana_hheight; ?>px; }
<?php if ($nirvana_hratio) { ?> @media (max-width: 1920px) {#branding, #bg_image { height:auto; max-width:100%; min-height:inherit !important; } }	<?php } ?>
</style>
<?php
	$nirvana_custom_styling = ob_get_contents();
	ob_end_clean();
	return $nirvana_custom_styling;
} // nirvana_custom_styles()



/* = PRESENTATION PAGE CUSTOM CSS
-----------------------------------------------*/

function nirvana_presentation_css() {
$nirvanas= nirvana_get_theme_options();
foreach ($nirvanas as $key => $value) { ${"$key"} = $value; }

	ob_start();
	echo '<style type="text/css">';
	if ($nirvana_fronthidetopbar) {?> #topbar {display: none;} <?php }
	if ($nirvana_fronthideheader) {?> #branding {display: none;} <?php }
	if ($nirvana_fronthidemenu) {?> #access {display: none;} <?php }
  	if ($nirvana_fronthidewidget) {?> #colophon {display: none;} <?php }
	if ($nirvana_fronthidefooter) {?> #footer2 {display: none;} <?php }

	if ($nirvana_fpslider_topmargin) { ?> .slider-wrapper {padding: <?php echo esc_html($nirvana_fpslider_topmargin); ?>px 0;} <?php }
?>

.slider-wrapper {
	max-height: <?php echo esc_html($nirvana_fpsliderheight); ?>px;
	background: <?php echo esc_html($nirvana_fpsliderbgcolor); ?>;
}
.nivo-caption h2 {font-size:<?php echo esc_html($nirvana_fpslidertitlesize); ?>;<?php if($nirvana_fpslider_titlecaps) { ?> text-transform: uppercase; <?php }?>  }
.slide-text {font-size:<?php echo esc_html($nirvana_fpslidertextsize); ?>; <?php if($nirvana_fpslider_textcaps) { ?> text-transform: uppercase; <?php }?><?php if($nirvana_fpslider_centertext) { ?> text-align: center !important; <?php }?>  }
#frontpage .theme-default .nivoSlider .readmore a { font-size:<?php echo esc_html( round( intval($nirvana_fpslidertextsize) * 0.8, 0) ); ?>px; } 
#pp-texttop {background: <?php echo esc_html($nirvana_fronttextbgcolortop); ?>;}
#front-columns-box {background: <?php echo esc_html($nirvana_frontcolumnsbgcolor); ?>;}
#pp-textmiddle {background: <?php echo esc_html($nirvana_fronttextbgcolormiddle); ?>;}
#pp-textbottom {background: <?php echo esc_html($nirvana_fronttextbgcolorbottom); ?>;}
#slider{
	max-width: <?php echo esc_html($nirvana_fpsliderwidth); ?>px;
	max-height: <?php echo esc_html($nirvana_fpsliderheight); ?>px;
<?php if ($nirvana_fpslider_bordersize): ?> border:<?php echo esc_html($nirvana_fpslider_bordersize) ;?>px solid <?php echo esc_html($nirvana_fpsliderbordercolor); ?>; <?php endif; ?> }
.theme-default .nivo-controlNav {bottom:<?php echo esc_html($nirvana_fpslider_bordersize+20); ?>px;}

#front-text1 h2, #front-text2 h2, #front-text5 h2, #front-columns h2 { 
	color: <?php echo esc_html($nirvana_fronttitlecolor); ?>; 
	font-size: <?php echo round(34.6*(preg_replace("/[^\d]/","",$nirvana_headingsfontsize)/100),0) ?>px;
    line-height: <?php echo round(42*(preg_replace("/[^\d]/","",$nirvana_headingsfontsize)/100),0) ?>px; }


#front-columns > div, #front-columns > li {
	<?php switch ($nirvana_nrcolumns) {
    case 0: break;
	case 1: echo esc_html("width:".  (100-$nirvana_colspace)."%;margin:".$nirvana_colspace."% auto 0!important;float:none;"); break;
    case 2: echo esc_html("width:". ((100-$nirvana_colspace)/2)."%; margin: 0 ".$nirvana_colspace."% ".$nirvana_colspace."% 0;"); break;
    case 3: echo esc_html("width:". ((100-2*$nirvana_colspace)/3)."%; margin: 0 ".$nirvana_colspace."% ".$nirvana_colspace."% 0;"); break;
    case 4: echo esc_html("width:". ((100-3*$nirvana_colspace)/4)."%; margin: 0 ".$nirvana_colspace."% ".$nirvana_colspace."% 0;"); break;
	} ?>}

#front-columns > div.column<?php echo esc_html($nirvana_nrcolumns); ?>, #front-columns > li:nth-child(<?php echo esc_html($nirvana_nrcolumns); ?>n+1) { margin-right: 0; }

.column-image {	max-width:<?php echo esc_html($nirvana_colimagewidth);?>px;margin:0 auto;}
.column-image img {	max-width:<?php echo esc_html($nirvana_colimagewidth);?>px;  max-height:<?php echo esc_html($nirvana_colimageheight);?>px;}

.coldisplay1 .column-image-inside { background: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.7); }
.coldisplay1 .column-image-inside:hover { background: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.9); }

.nivo-caption .inline-slide-text {
	background-color: rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3);
	-moz-box-shadow:10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3), -10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3);
	-webkit-box-shadow:10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3), -10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3);
	box-shadow:10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3), -10px 0 0 rgba(<?php echo esc_html(cryout_hex2rgb($nirvana_fpslidercaptionbg)); ?>,0.3);
	-webkit-box-decoration-break: clone;
	-moz-box-decoration-break: clone;
	box-decoration-break: clone;
}
.nivo-caption h2{
 	-moz-text-shadow:0 1px 0px <?php echo esc_html($nirvana_fpslidercaptionbg); ?>;
	-webkit-text-shadow:0 1px 0px <?php echo esc_html($nirvana_fpslidercaptionbg); ?>;
	text-shadow:0 1px 0px <?php echo esc_html($nirvana_fpslidercaptionbg); ?>;
}
.nivo-caption, .nivo-caption a { color: <?php echo esc_html($nirvana_fpslidercaptioncolor); ?>; }
.theme-default .nivo-directionNav a { background-color:<?php echo esc_html($nirvana_fpsliderbordercolor); ?>; }
.slider-bullets .nivo-controlNav a { background-color: <?php echo esc_html($nirvana_sidetitlebg); ?>;border:2px solid <?php echo esc_html($nirvana_fpsliderbordercolor); ?>; }
.slider-bullets .nivo-controlNav a:hover { background-color: <?php echo esc_html($nirvana_menucolorbgdefault); ?>; }
.slider-bullets .nivo-controlNav a.active {background-color: <?php echo esc_html($nirvana_accentcolora); ?>; }
.slider-numbers .nivo-controlNav a { background-color:<?php echo esc_html($nirvana_fpsliderbordercolor);?>;}
.slider-numbers .nivo-controlNav a:hover { color: <?php echo esc_html($nirvana_accentcolora); ?>; }
.slider-numbers .nivo-controlNav a.active { color:<?php echo esc_html($nirvana_accentcolora); ?>;}

h3.column-header-image {color: <?php echo esc_html($nirvana_linkcolortext); ?>;}

.columnmore { background-color: <?php echo esc_html($nirvana_backcolormain); ?>; }
#front-columns h3.column-header-noimage { background: <?php echo esc_html($nirvana_contentcolorbg);?>; }
<?php
echo esc_html($nirvana_column_frames);
if ($nirvana_column_frames) {
echo "#front-columns > div:nth-child(1n+2) {transform:rotate(". rand(-5,-2)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
echo "#front-columns > div:nth-child(2n+1) {transform:rotate(". rand(-2,2)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
echo "#front-columns > div:nth-child(3n+2) {transform:rotate(". rand(2,5)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
echo "#front-columns > div:nth-child(5n+3) {transform:rotate(". rand(-5,-2)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
echo "#front-columns > div:nth-child(7n+5) {transform:rotate(". rand(-2,2)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
echo "#front-columns > div:nth-child(11n+7) {transform:rotate(". rand(2,5)."deg);-webkit-transform:rotate(". rand(-7,7)."deg);} ";
?>
#front-columns > div {
	border: 8px solid #fff;
	padding: 0;
	-webkit-box-shadow: 0 0 2px #ccc;
	-moz-box-shadow: 0 0 2px #ccc;
	box-shadow: 0 0 2px #ccc;
	-webkit-transition: all .2s ease-in-out;
	-moz-transition: all .2s ease-in-out;
	-o-transition: all .2s ease-in-out;
	transition: all .2s ease-in-out;
	-webkit-backface-visibility: hidden;
}
#front-columns > div:hover {
	z-index: 252;
	-webkit-transform: rotate(0deg) !important;
	transform: rotate(0deg) !important;
}
@media (max-width: 640px) {
	.nivo-caption h2 {color:<?php echo esc_html($nirvana_accentcolora); ?>;}
	.coldisplay1 .column-image-inside { background: transparent; }
}
<?php }

	echo '</style>';
	$nirvana_presentation_page_styling = ob_get_contents();
	ob_end_clean();
	return $nirvana_presentation_page_styling;

} // nirvana_presentation_css()


// Nirvana function for inserting the Custom CSS into the header
function nirvana_customcss() {
	$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value; }
	if ($nirvana_customcss != "") {
		echo '<style type="text/css">'.htmlspecialchars_decode($nirvana_customcss, ENT_QUOTES).'</style>';
	}
} // nirvana_customcss()


// Nirvana function for inseting the Custom JS into the header
function nirvana_customjs() {
	$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value; }
	echo '<script type="text/javascript">';
	echo 'var cryout_global_content_width = '.$nirvana_sidewidth.';';
	if ($nirvana_customjs != "") {
		echo PHP_EOL.htmlspecialchars_decode($nirvana_customjs, ENT_QUOTES);
	}
	echo '</script>';
} // nirvana_customjs()

// Nirvana function for inseting slider on the presentation page
function nirvana_pp_slider() {
	$nirvanas= nirvana_get_theme_options();
	foreach ($nirvanas as $key => $value) { ${"$key"} = $value; } ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#slider').nivoSlider({
			effect: '<?php  echo esc_html($nirvana_fpslideranim); ?>',
			animSpeed: <?php echo esc_html($nirvana_fpslidertime); ?>,
			<?php if($nirvana_fpsliderarrows=="Hidden"): ?>directionNav: false,<?php endif;
			if($nirvana_fpsliderarrows=="Always Visible"): ?>directionNavHide: false,<?php endif; ?>
			//controlNavThumbs: true,
			beforeChange: function(){
				jQuery('.nivo-caption h2').addClass('nivo-caption-mate');
				jQuery('.inline-slide-text').fadeOut(500);
				jQuery('.inline-slide-text').css({'opacity':'100','display':'inline'});
				jQuery('.readmore').fadeOut(500);
				jQuery('.readmore').css({'opacity':'100','display':'table'});
			},
			pauseTime: <?php echo esc_html($nirvana_fpsliderpause); ?>
		});
	});
	</script>
<?php
}

////////// FIN //////////
?>