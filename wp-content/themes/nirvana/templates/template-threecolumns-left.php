<?php /*
 * Template Name: Three Columns, Sidebars on the Left
 *
 * @package Cryout Creations
 * @subpackage nirvana
 * @since nirvana 0.5
 */
get_header(); ?>

		<section id="container" class="three-columns-left">
	
			<div id="content" role="main">

				<?php get_template_part( 'content/content', 'page'); ?>

			</div><!-- #content -->
			<?php get_sidebar('left'); get_sidebar('right'); ?>
		</section><!-- #container -->


<?php get_footer(); ?>