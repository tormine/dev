

<?php
/*
Template Name: about page
*/
?>



<?php get_header(); ?>

<div class="container">
	
				
					<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
							

						<div class="post-content">
	<div id="page-about">		
	<div id="top-banner-0">
	

	</div>				
	<div id="top-banner-1">
	<p class="title">
	      You...
	       </p>

	</div>		
	<div id="top-banner-2">
	<p><br>
	ride
	</p>
	<p class="title"><br>
	      to fly. to feel.<br>
	      to soar.  to laugh. <br>
	      to love.  to overcome.<br>
	     
	     
	       </p>

	</div>
	
	<div id="top-banner-3">
	<p>
	you ride
	</p>
	<p class="title">
		to prove them wrong.<br>
	</p>
	
	</div>
	<div id="top-banner-4">
	<p>
	hors.ly
	</p>
	<p class="title">
		helps you share, collect <br> and inspire.<br>
	</p>
	
	</div>
	<div id="top-banner-3">
	<p>
	hors.ly
	</p>
	<p class="title">
		helps you prove them wrong.<br>
	</p>
	
	</div>
					
	<div id="top-banner-5">
	  
	
	    <p class="title"><br>
	      my life<br>
	      my show<br>
	      my hors.ly<br>
	    </p>
	    <p class="left">
	      <a href="/register" class="button-huge">Join Hors.ly</a> 
	     
	    </p>
	 
	 
	
	</div>
	</div>	
							<?php
							the_content();
							wp_link_pages( array( 'before' => '<p><strong>' . __('Pages:', 'ipin') . '</strong>', 'after' => '</p>' ) );
							edit_post_link(__('Edit Page', 'ipin'),'<p>[ ',' ]</p>');
							?>
						</div>
						
						
						
				
					<?php endwhile; ?>
			
	
		
		
	</div>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<?php get_footer(); ?>