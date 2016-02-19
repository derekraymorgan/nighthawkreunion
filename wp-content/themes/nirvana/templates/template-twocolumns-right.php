<?php /*
 * Template Name: Two Columns, Sidebar on the Right
 *
 * @package Cryout Creations
 * @subpackage nirvana
 * @since nirvana 0.5
 */
get_header(); ?>

		<section id="container" class="two-columns-right">
	
			<div id="content" role="main">

				<?php get_template_part( 'content/content', 'page'); ?>

			</div><!-- #content -->
			<?php get_sidebar('right'); ?>
		</section><!-- #container -->


<?php get_footer(); ?>