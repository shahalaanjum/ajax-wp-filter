<?php 
/**
 * Plugin Name:       Ajax CPT and Taxonomy Filter  
 * Plugin URI:        #
 * Description:       Handle Ajax Search and Filter For CPT and Taxonomy
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shahala Anjum
 * Author URI:        https://profiles.wordpress.org/shahala/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ajax-cpt-taxonomy-filter
 */
 //enque scripts
function acpt_ajax_filter_enqueues() {
   
    wp_enqueue_script( 'filter', plugins_url( 'filter.js', __FILE__ ));
    wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ));
    wp_enqueue_style( 'bootstrap', plugins_url( 'assets/vendor/bootstrap/css/bootstrap.min.css', __FILE__ ));
    wp_localize_script( 'ajax-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'acpt_ajax_filter_enqueues' );
$atts = shortcode_atts(
	array(
		'postype' => 'post',
		'taxtype' => 'category',
	), $atts, 'ajax_posts_filter' );
function acpt_ajax_shortcode($atts){
		 ?>
<section class="section site-portfolio">
    <div class="container tax-data">
		<div class="taxonomy-list"> 
			<?php 
			if(!empty($atts['taxtype']) ){
				//passing more than one value with comma seperated
				$no_whitespaces = preg_replace( '/\s*,\s*/', ',', filter_var( $atts['taxtype'], FILTER_SANITIZE_STRING ) ); 
				$tax_array = explode( ',', $no_whitespaces );
				$onetax = $tax_array[0];
				//foreach($tax_array as $onetax){
					
					$categories = get_terms( $onetax, 'hide_empty=0'); 
					// echo '<pre>';
					// print_r($categories);
					echo '
						<input type="hidden" id="filters-'.$onetax.'" />' ;  ?>
						<div class="row mb-5 align-items-center">
							<div class="col-md-12 col-lg-6 text-start text-lg-end" data-aos="fade-up" data-aos-delay="100">
								<div id="filters" class="filters <?php echo esc_html( $onetax ) ; ?>-list cat-list">
									<a href="javascript:;" class="filter-link cat-list_item" data-slug="" data-type="<?php echo esc_html( $onetax ); ?>" data-id="">
										All
									</a>
									<?php foreach($categories as $category) : ?>
										<a href="javascript:;" class="filter-link cat-list_item" data-slug="<?php echo esc_html($category->slug); ?>" data-type="<?php echo esc_html( $onetax ); ?>" data-id="<?php echo esc_html($category->term_id); ?>">
											<?php echo esc_html($category->name); ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
				<?php //} 
			} ?>
		</div>
			<?php 
			$projects = new WP_Query([
				'post_type' => $atts['postype'],
				'posts_per_page' => 6,
				'order_by' => 'name',
			]);
			?>
			<?php if($projects->have_posts()): ?>
				<div id="portfolio-grid" class="row no-gutter project-tiles-portfolio project-tiles projects-grid" post-type="<?php echo esc_html( $atts['postype'] ); ?>" data-aos="fade-up" data-aos-delay="200"> 
					<?php
					while($projects->have_posts()) : $projects->the_post();
						$link = get_the_permalink();
						$img = get_the_post_thumbnail_url();
						$title = get_the_title();
							echo'
							<div class="item web col-sm-6 col-md-4 col-lg-4 mb-4">
								<a href="'.$link.'" class="item-wrap fancybox"> 
									
									<div class="work-info">
										<h3>'.$title.'</h3>
									</div>
									<img src="'.$img.'" width="auto" height="250">
									
								</a>
							</div>';
					endwhile;
					?>
				</div>
				<?php wp_reset_postdata(); ?>
				
				
			<?php 
			else :
				echo 'No posts found';
			endif; ?>
		<div id="more_blog_posts">Load More</div>
	</div>
</section>
<?php 
}
add_shortcode( 'ajax_posts_filter', 'acpt_ajax_shortcode' );
 //The PHP WordPress Filter,
function acpt_filter_blogs() {
	$catIdsu = sanitize_text_field($_POST['catIds']);
	$catIds = isset($catIdsu) ? esc_html($catIdsu) : '' ;
	$postypeu = sanitize_text_field($_POST['postype']);
	$postype = isset($postypeu) ? esc_html($postypeu) : '' ;
	$taxTypeu = sanitize_text_field($_POST['taxType']);
	$taxType = isset($taxTypeu) ? esc_html($taxTypeu) : '' ;
	
	
	//$tagIds = $_POST['tagIds'];
	$pageu = sanitize_text_field($_POST['pageNumber']);
	$page = isset($pageu) ? esc_html($pageu) : 0 ;

	$args = [
		'post_type' => ($postype) ? $postype : 'post',
		'posts_per_page' => 6,
		'post_status'  => 'publish',
		'orderby'        => 'publish_date',
		'order'     => ASC ,
		'paged'    => $page,
	];
	// project Category
	if (!empty($catIds)) {
		$args['tax_query'][] = [
			'taxonomy'      => $taxType,
			'field'		=> 'term_id',
			'terms'         => $catIds,
			'operator'      => 'IN'
		];
	}
	$output = '';
	$ajaxposts = new WP_Query($args);
	if ( $ajaxposts->have_posts() ) {
		while ( $ajaxposts->have_posts() ) : $ajaxposts->the_post();
			$link = get_the_permalink();
			$img = get_the_post_thumbnail_url();
			$title = get_the_title();
			$output .= '
				<div class="item web col-sm-6 col-md-4 col-lg-4 mb-4">
					<a href="'.$link.'" class="item-wrap fancybox"> 
						
						<div class="work-info">
							<h3>'.$title.'</h3>
						</div>
						<img src="'.$img.'"  width="auto" height="250">
						
					</a>
				</div>';
		
            //get_template_part('');
		endwhile;	
		$counter = $ajaxposts->max_num_pages;
		$result = [
			'total' => $counter,
			'html' => $output,
		];	
	} else {
        $result = [
			'total' => 1,
			'html' => 'No projects',
		];	
	}
	echo json_encode($result);
	wp_reset_postdata();
	die;
}
add_action('wp_ajax_filter_blogs', 'acpt_filter_blogs');
add_action('wp_ajax_nopriv_filter_blogs', 'acpt_filter_blogs');
 
