<?php
/*
Template Name: _popular
*/
?>

<?php get_header(); ?>

<div class="container-fluid">
	<?php
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	function filter_where( $where = '' ) {
		$duration = '-' . of_get_option('popularity_duration') . ' days';
		$where .= " AND post_date > '" . date('Y-m-d', strtotime($duration)) . "'";
		return $where;
	}
	
	if ('likes' == $popularity = of_get_option('popularity')) {
		add_filter('posts_where', 'filter_where');
	
		$args = array(
			'meta_key' => '_Likes Count',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'paged' => $paged
		);
	} else if ($popularity == 'repins') {
		add_filter('posts_where', 'filter_where');
	
		$args = array(
			'meta_key' => '_Repin Count',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'paged' => $paged
		);
	} else if ($popularity == 'comments') {
		add_filter('posts_where', 'filter_where');

		$args = array(
			'orderby' => 'comment_count',
			'paged' => $paged
		);
	} else {
		$args = array(
			'paged' => $paged
		);
	}
	
	query_posts($args);

	get_template_part('index', 'masonry');
	get_footer();
?>