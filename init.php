<?php 
/*
Plugin Name: Advanced Portfolio
Plugin URI: #
Author: Sujan
Author URI: #
Version: 1.0 
Description: Custom Made Portfolio
*/


register_activation_hook(__FILE__, 'flush_korbo');

function flush_korbo(){
	flush_rewrite_rules();
}


add_action('wp_enqueue_scripts', function(){
	wp_register_script('mixitup-portfolio', PLUGINS_URL('assets/js/jquery.mixitup.min.js', __FILE__), array('jquery'));
	wp_register_script('portfolio-custom', PLUGINS_URL('assets/js/portfolio.js', __FILE__), array('jquery', 'mixitup-portfolio'));
	wp_register_style('portfolio-style', PLUGINS_URL('assets/css/portfolio.css', __FILE__));

	wp_enqueue_script('mixitup-portfolio');
	wp_enqueue_script('portfolio-custom');
	wp_enqueue_style('portfolio-style');
});


add_action('init', 'advance_portfolio_functions');

function advance_portfolio_functions(){
	register_post_type('advance-portfolio', array(
		'labels' => array(
			'name' => 'Recent Works',
			'add_new_item' => 'Add New Portfolio',
			'add_new' => 'Add New Portfolio',
			'all_items' => 'All Portfolios'
		),
		'public' => true,
		'supports' => array('title', 'editor', 'thumbnail')
	));

	register_taxonomy('advance-portfolio-types', 'advance-portfolio', array(
		'labels' => array(
			'name' => 'Types',
			'add_new_item' => 'Add New Type',
			'add_new' => 'Add New Type',
			'all_items' => 'All Portfolio Types'
		),
		'public' => true,
		'hierarchical' => true
	));
}


add_shortcode('advance-portfolio', 'advance_portfolio_output');

function advance_portfolio_output(){
	ob_start(); ?>

		<div class="filter" data-filter="all">Show All</div>
		<?php 
			$portfolio_types = get_terms('advance-portfolio-types');
			foreach($portfolio_types as $type) :
		?>
			<div class="filter" data-filter=".<?php echo $type->slug; ?>"><?php echo $type->name; ?></div>

		<?php endforeach; ?>

		<div id="container">
			<?php 
				$portfolios = new WP_Query(array(
					'post_type' => 'advance-portfolio',
					'posts_per_page' => -1
				));
				while($portfolios->have_posts() ) : $portfolios->the_post();

				$types = get_the_terms(get_the_id(), 'advance-portfolio-types');
				if($types) :
				foreach($types as $type) :
					
			?>
				<a href="<?php the_permalink(); ?>" class="mix <?php echo $type->slug; ?>"><?php the_title(); ?></a>

				<?php endforeach; endif; ?>
			<?php endwhile; ?>
		</div>
	

	<?php return ob_get_clean();
}



